<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/assets/favicon.png') }}">
        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        <style>
            .cursor-pointer{
                cursor: pointer;
            }

            .secondary {
                color: #004702;
            }
            .primary {
                color: #327119;
            }
            .bg-s {
                background-color: #004702;
            }
            .bg-p {
                background-color: #327119;
            }
        .ss3-600 {
            font-weight: 600;
            font-style: normal;
        }
        .ss3-900 {
            font-weight: 900;
            font-style: normal;
        }
        .source-sans-3 {
            font-family: "Source Sans 3", sans-serif;
            font-optical-sizing: auto;
        }
        </style>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">
        
        <!-- Scripts -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
        @routes
        @vite(['resources/js/app.js'])
        @inertiaHead
    </head>
    <body>
        @inertia
    </body>
</html>
