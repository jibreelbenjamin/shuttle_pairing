<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ShuttlePairing') - ShuttlePairing</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm border-b border-gray-200 flex-shrink-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('tournament.index') }}" class="flex items-center space-x-2">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <span class="text-xl font-bold text-gray-900">ShuttlePairing</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('tournament.index') }}" class="text-gray-600 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        Turnamen
                    </a>
                    <a href="{{ route('tournament.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 transition-colors">
                        + Buat Turnamen
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 flex-shrink-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} ShuttlePairing - Aplikasi Bracket Turnamen Badminton
            </p>
        </div>
    </footer>

    <!-- Toast Notifications -->
    @if(session('success'))
        <div id="toast-success" class="fixed bottom-6 right-6 z-50 animate-slide-up">
            <div class="bg-green-600 text-white px-5 py-3 rounded-xl shadow-xl flex items-center space-x-3 min-w-[280px] max-w-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm font-medium flex-1">{{ session('success') }}</p>
                <button onclick="this.closest('#toast-success').remove()" class="flex-shrink-0 text-white/80 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div id="toast-error" class="fixed bottom-6 right-6 z-50 animate-slide-up">
            <div class="bg-red-600 text-white px-5 py-3 rounded-xl shadow-xl flex items-start space-x-3 min-w-[280px] max-w-sm">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    @foreach($errors->all() as $error)
                        <p class="text-sm font-medium">{{ $error }}</p>
                    @endforeach
                </div>
                <button onclick="this.closest('#toast-error').remove()" class="flex-shrink-0 text-white/80 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <style>
        @keyframes slide-up {
            from {
                opacity: 0;
                transform: translateY(1rem);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-slide-up {
            animation: slide-up 0.3s ease-out;
        }
    </style>

    <script>
        // Auto-dismiss toast after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            ['toast-success', 'toast-error'].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) {
                    setTimeout(function() {
                        if (el.parentNode) {
                            el.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                            el.style.opacity = '0';
                            el.style.transform = 'translateY(1rem)';
                            setTimeout(function() { el.remove(); }, 300);
                        }
                    }, 5000);
                }
            });
        });
    </script>
</body>
</html>
