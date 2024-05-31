<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->

</head>

<body class="font-sans antialiased dark:bg-black dark:text-white/50">
    <div
        class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-center pt-8 sm:justify-start sm:pt-0">
                <span class="hidden border-green-500 border-yellow-500 border-orange-500 border-red-500">
                    <div class="w-10 rounded-full h-10 flex justify-center items-center border-4 {{ $borderColor }}"
                        style="border-width: {{ $borderWidth }}">
                        <span> {{ $progress }}%</span>
                    </div>
                </span>
                <h1 class="text-6xl font-bold text-center">مرحبا بك في لارافيل</h1>
            </div>
        </div>
    </div>
</body>

</html>
