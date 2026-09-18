<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="pilgrim-print-html">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Print')</title>
    <link href="{{ asset('css/pilgrim-registration.css') }}?v=16" rel="stylesheet">
    <style>
        html.pilgrim-print-html,
        html.pilgrim-print-html body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            background: #fff;
        }

        @media print {
            html.pilgrim-print-html,
            html.pilgrim-print-html body,
            html.pilgrim-print-html * {
                overflow: visible !important;
                max-height: none !important;
            }

            html.pilgrim-print-html ::-webkit-scrollbar {
                display: none !important;
                width: 0 !important;
                height: 0 !important;
            }
        }
    </style>
</head>
<body class="pilgrim-print-page">
    @yield('content')
    @stack('scripts')
</body>
</html>
