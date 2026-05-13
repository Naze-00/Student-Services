<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Services</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-sm border-b px-6 py-3 flex justify-between items-center">
        <div class="flex items-center gap-6">
            <span class="font-bold text-gray-800 text-lg">Student Services</span>
            <a href="{{ route('students.index') }}"
               class="text-sm text-gray-600 hover:text-blue-600 {{ request()->routeIs('students.*') ? 'text-blue-600 font-medium' : '' }}">
                Students
            </a>
            <a href="{{ route('service-requests.index') }}"
               class="text-sm text-gray-600 hover:text-blue-600 {{ request()->routeIs('service-requests.*') ? 'text-blue-600 font-medium' : '' }}">
                Service Requests
            </a>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('imports.index') }}"
               class="text-sm text-gray-600 hover:text-blue-600 {{ request()->routeIs('imports.*') ? 'text-blue-600 font-medium' : '' }}">
                Import
            </a>
            @endif
        </div>
        <div class="flex items-center gap-3 text-sm">
            <span class="text-gray-500">{{ auth()->user()->name }}</span>
            <span class="px-2 py-0.5 rounded text-xs font-medium
                {{ auth()->user()->isAdmin() ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                {{ ucfirst(auth()->user()->role) }}
            </span>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button class="text-gray-400 hover:text-red-500">Logout</button>
            </form>
        </div>
    </nav>

    <main class="p-6 max-w-7xl mx-auto">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>