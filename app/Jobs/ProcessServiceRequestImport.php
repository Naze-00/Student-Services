<?php

namespace App\Jobs;

use App\Models\ImportLog;
use App\Models\ServiceRequest;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ProcessServiceRequestImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const SERVICE_TYPE_MAP = [
        'goodmoral'               => 'Good Moral Certificate',
        'good moral'              => 'Good Moral Certificate',
        'good moral cert'         => 'Good Moral Certificate',
        'good moral certificate'  => 'Good Moral Certificate',
        'id'                      => 'ID Replacement',
        'id repl'                 => 'ID Replacement',
        'id replace'              => 'ID Replacement',
        'id replacement'          => 'ID Replacement',
        'form 137'                => 'Form 137',
        'form137'                 => 'Form 137',
    ];

    public function __construct(
        private readonly string $filePath,
        private readonly int    $importLogId,
        private readonly int    $userId
    ) {}

    private function cellAsString(mixed $value): string
    {
        if (is_null($value) || $value === '') {
            return '';
        }

        if (is_float($value) && floor($value) === $value) {
            return trim((string)(int)$value);
        }

        return trim((string)$value);
    }

    private function parseDate(mixed $value): string
    {
        if (empty($value)) {
            return now()->toDateString();
        }

        if (is_numeric($value)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$value);
                return $date->format('Y-m-d');
            } catch (\Throwable) {}
        }

        $ts = strtotime((string)$value);
        return $ts !== false ? date('Y-m-d', $ts) : now()->toDateString();
    }

    public function handle(): void
    {
        $log = ImportLog::findOrFail($this->importLogId);

        $summary = [
            'total_rows'           => 0,
            'successful_requests'  => 0,
            'new_students_created' => 0,
            'skipped_rows'         => [],
        ];

        try {
            $fullPath = storage_path("app/public/{$this->filePath}");


            $allRows = Excel::toArray([], $fullPath)[0];

            if (empty($allRows)) {
                $log->update([
                    'status'       => 'Failed',
                    'summary_json' => array_merge($summary, ['error' => 'The uploaded file appears to be empty.']),
                ]);
                return;
            }

            $firstCell = strtolower(trim((string)($allRows[0][0] ?? '')));
            $hasHeader = in_array($firstCell, [
                'student_number', 'studentnumber', 'student number', 'student_no',
            ]);

            $dataRows = $hasHeader ? array_slice($allRows, 1) : $allRows;

            foreach ($dataRows as $index => $row) {
                $rowNum = $index + ($hasHeader ? 2 : 1);

                $studentNumber  = $this->cellAsString($row[0] ?? null);
                $serviceTypeRaw = strtolower($this->cellAsString($row[1] ?? null));
                $dateRequested  = $this->parseDate($row[2] ?? null);

                if ($studentNumber === '' && $serviceTypeRaw === '') {
                    continue;
                }

                $summary['total_rows']++;

                if ($studentNumber === '') {
                    $summary['skipped_rows'][] = [
                        'row'    => $rowNum,
                        'reason' => 'Missing student number',
                    ];
                    continue;
                }

                $serviceType = self::SERVICE_TYPE_MAP[$serviceTypeRaw] ?? null;
                if (!$serviceType) {
                    $summary['skipped_rows'][] = [
                        'row'    => $rowNum,
                        'reason' => "Unknown service type: \"{$serviceTypeRaw}\"",
                    ];
                    continue;
                }

                $student = Student::where('student_number', $studentNumber)->first();

                if (!$student) {
                    $student = Student::create([
                        'student_number' => $studentNumber,
                        'first_name'     => 'Unknown',
                        'last_name'      => 'Unknown',
                        'grade_level'    => 'N/A',
                        'email'          => "imported_{$studentNumber}@school.com",
                        'status'         => 'Active',
                        'is_imported'    => true,
                    ]);
                    $summary['new_students_created']++;
                } elseif ($student->status === 'Inactive') {
                    $summary['skipped_rows'][] = [
                        'row'    => $rowNum,
                        'reason' => "Student {$studentNumber} is Inactive",
                    ];
                    continue;
                }

                $exists = ServiceRequest::where('student_id', $student->id)
                    ->where('service_type', $serviceType)
                    ->whereDate('date_requested', $dateRequested)
                    ->exists();

                if ($exists) {
                    $summary['skipped_rows'][] = [
                        'row'    => $rowNum,
                        'reason' => 'Duplicate request',
                    ];
                    continue;
                }

                ServiceRequest::create([
                    'student_id'     => $student->id,
                    'service_type'   => $serviceType,
                    'date_requested' => $dateRequested,
                    'status'         => 'Pending',
                ]);

                $summary['successful_requests']++;
            }

            $log->update(['status' => 'Completed', 'summary_json' => $summary]);

        } catch (\Throwable $e) {
            $log->update([
                'status'       => 'Failed',
                'summary_json' => array_merge($summary, ['error' => $e->getMessage()]),
            ]);
        }
    }
}