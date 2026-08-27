<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>{{ filled($title ?? null) ? $title.' - '.config('app.name') : config('app.name') }}</title>

<link rel="icon" href="{{ asset(config('branding.logo')) }}" type="image/png">
<link rel="apple-touch-icon" href="{{ asset(config('branding.logo')) }}">

<link href="{{ asset('vendor/owl-carousel/owl.carousel.css') }}" rel="stylesheet">
<link href="{{ asset('css/style.css') }}" rel="stylesheet">
<link href="{{ asset('css/deyar-brand.css') }}?v=2" rel="stylesheet">
<link href="{{ asset('css/admin-forms.css') }}?v=29" rel="stylesheet">
<link href="{{ asset('vendor/tom-select/css/tom-select.default.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">

@stack('styles')