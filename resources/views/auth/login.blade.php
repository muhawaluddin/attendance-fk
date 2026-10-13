<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Absensi - {{ $appSetting->institution_name ?? 'Universitas Hasanuddin' }}</title>
    <link rel="icon" href="{{ $appSetting->institution_logo ? asset($appSetting->institution_logo) : asset('assets/img/favicon.ico') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @php
        $theme = $appSetting->theme_config ?? [
            'primary' => '#b91c1c',
            'primary_hover' => '#991b1b',
            'primary_light' => '#fef2f2',
            'primary_border' => '#fecaca',
            'primary_text' => '#7f1d1d',
        ];
        $primaryColor = $theme['primary'];
        $primaryHover = $theme['primary_hover'];
    @endphp

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '{{ $theme['primary_light'] }}',
                            100: '{{ $theme['primary_border'] }}',
                            500: '{{ $primaryColor }}',
                            600: '{{ $primaryColor }}',
                            700: '{{ $primaryColor }}',
                            800: '{{ $primaryHover }}',
                            900: '{{ $primaryHover }}',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --brand-primary: {{ $primaryColor }};
            --brand-hover: {{ $primaryHover }};
        }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 font-sans relative overflow-hidden">
    
    <!-- Background Dynamic Glow Blobs -->
    <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full blur-3xl pointer-events-none opacity-25" style="background-color: {{ $primaryColor }};"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 rounded-full blur-3xl pointer-events-none opacity-20" style="background-color: {{ $primaryHover }};"></div>

    <div class="w-full max-w-md relative z-10">
        
        <!-- Card Container -->
        <div class="bg-white/95 backdrop-blur-xl rounded-xl shadow-2xl border border-white/20 p-8 sm:p-10">
            
            <!-- Branding Header -->
            <div class="text-center mb-8">
                <div class="inline-flex p-3 rounded-lg bg-brand-50 border border-brand-100 shadow-sm mb-4">
                    <img src="{{ $appSetting->institution_logo ? asset($appSetting->institution_logo) : asset('assets/img/unhas.png') }}" 
                         alt="Logo Institusi" 
                         class="h-16 w-16 object-contain" 
                         onerror="this.src='https://upload.wikimedia.org/wikipedia/id/8/8a/Logo-unhas.png'">
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $appSetting->app_name ?? 'SISTEM ABSENSI' }}</h1>
                <p class="text-sm font-bold text-brand-700 mt-0.5">{{ $appSetting->faculty_name ?: $appSetting->institution_name }}</p>
                @if($appSetting->faculty_name && $appSetting->institution_name)
                    <p class="text-xs text-slate-600 font-medium">{{ $appSetting->institution_name }}</p>
                @endif
                <p class="text-xs text-slate-400 mt-2">Silakan masuk menggunakan kredensial akun Anda</p>
            </div>

            <!-- Error Alerts -->
            @if($errors->has('login'))
                <div class="mb-5 p-3.5 rounded-lg bg-red-50 border border-red-200 text-red-700 text-xs font-semibold flex items-center gap-2.5">
                    <i class="fa-solid fa-triangle-exclamation text-base"></i>
                    <span>{{ $errors->first('login') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-5 p-3.5 rounded-lg bg-blue-50 border border-blue-200 text-blue-700 text-xs font-medium flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-info text-base"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            <!-- Form Login -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Username -->
                <div>
                    <label for="username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Username / NIP / NIM</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-regular fa-user text-sm"></i>
                        </div>
                        <input type="text" name="username" id="username" value="{{ old('username', 'admin') }}" required autofocus
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-600 focus:bg-white transition-all text-slate-800 placeholder-slate-400"
                               placeholder="Masukkan username Anda">
                    </div>
                    @error('username')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kata Sandi</label>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </div>
                        <input type="password" name="password" id="password" value="password" required
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-600 focus:bg-white transition-all text-slate-800 placeholder-slate-400"
                               placeholder="••••••••">
                    </div>
                    @error('password')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Options -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-brand-700 focus:ring-brand-600 border-slate-300">
                        <span class="text-xs text-slate-600 select-none">Ingat saya</span>
                    </label>
                    <a href="{{ route('monitoring') }}" target="_blank" class="text-xs text-brand-700 hover:text-brand-800 font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-tv"></i>
                        <span>Layar TV</span>
                    </a>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-2.5 px-4 rounded-lg text-white font-bold text-sm shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2"
                        style="background-color: {{ $primaryColor }};">
                    <i class="fa-solid fa-right-to-bracket text-base"></i>
                    <span>Masuk ke Sistem</span>
                </button>
            </form>

            <!-- Quick Demo Credentials Hint -->
            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500 font-medium">Akun Demo Bawaan:</p>
                <div class="mt-2 flex items-center justify-center gap-2 text-[11px]">
                    <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-700 font-mono">user: <strong>admin</strong></span>
                    <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-700 font-mono">pass: <strong>password</strong></span>
                </div>
            </div>

        </div>

        <!-- Footnote -->
        <p class="text-center text-xs text-slate-400 mt-6 font-medium">
            &copy; {{ date('Y') }} {{ $appSetting->institution_name ?? 'Universitas' }}. Powered by Laravel 13.
        </p>

    </div>

</body>
</html>
