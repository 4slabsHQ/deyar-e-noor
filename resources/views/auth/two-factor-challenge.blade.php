<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.head')
</head>
<body class="bg-primary">
<div class="container d-flex flex-column justify-content-center" style="min-height:100vh;">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg">
                <div class="card-body p-5">
                    <h4 class="text-center mb-4">Two-Factor Authentication</h4>
                    <p class="text-center text-muted mb-4">Enter the authentication code from your app, or a recovery code.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('two-factor.login') }}">
                        @csrf
                        <div class="form-group mb-3" id="code-group">
                            <label class="mb-2">Authentication Code</label>
                            <input type="text" inputmode="numeric" class="form-control" name="code" autofocus>
                        </div>
                        <div class="form-group mb-3 d-none" id="recovery-group">
                            <label class="mb-2">Recovery Code</label>
                            <input type="text" class="form-control" name="recovery_code">
                        </div>
                        <div class="text-center mb-3">
                            <button type="submit" class="btn btn-primary w-100">Continue</button>
                        </div>
                    </form>

                    <div class="text-center">
                        <a href="javascript:void(0);" id="toggle-recovery">Use a recovery code instead</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('toggle-recovery').addEventListener('click', function () {
    document.getElementById('code-group').classList.toggle('d-none');
    document.getElementById('recovery-group').classList.toggle('d-none');
});
</script>
@include('partials.scripts')
</body>
</html>