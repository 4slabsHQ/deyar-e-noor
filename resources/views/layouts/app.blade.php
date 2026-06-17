<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.head')
</head>
<body>

    <div id="preloader">
        <div class="lds-ripple"><div></div><div></div></div>
    </div>

    <div id="main-wrapper">

        @include('partials.header')
        @include('partials.sidebar')

        <div class="content-body">
            <div class="container-fluid">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')

            </div>
        </div>

        @include('partials.footer')

    </div>

    @include('partials.scripts')
    @stack('scripts')

</body>
</html>