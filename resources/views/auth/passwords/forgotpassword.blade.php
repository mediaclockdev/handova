<!DOCTYPE html>
<html>

<head>
    <title>Handova - User Authentication</title>
    @include('partials.head')
</head>

<body>
    <div class="">

        <div class="auth-content">
            <div class="auth-card">
                <div class="card-body text-center">

                    <img src="{{ asset('images/handova.svg') }}" alt="Handova Logo" class="logo">

                    <div id="forgot-page" class="auth-page">

                        <h4 class="auth-heading">Welcome!</h4>
                        <p class="auth-subtext">Forgot your username or password?</p>

                        @if (session('status'))
                            <p style="color: green">{{ session('status') }}</p>
                        @endif

                        <form method="POST" action="#">
                            @csrf

                            <div class="input-group mb-4 rounded-pill border border-secondary-subtle">
                                <span class="input-group-text bg-transparent border-0 rounded-start-pill">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" name="email" class="form-control bg-transparent border-0"
                                    placeholder="Enter your email" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-custom-orange btn-lg rounded-pill">
                                    Send
                                </button>
                            </div>
                            <p class="mt-6 text-center new_account">
                                Back to <a href="/login"><span class="link">Login</span></a>
                            </p>
                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div>
    @include('partials/scripts')
</body>
<script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "5000"
    };

    @if (session('success'))
        toastr.success("{{ session('success') }}");
    @endif

    @if (session('error'))
        toastr.error("{{ session('error') }}");
    @endif

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            toastr.error("{{ $error }}");
        @endforeach
    @endif
</script>

</html>
