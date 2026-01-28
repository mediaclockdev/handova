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

                    <div id="login-page" class="auth-page">

                        <p class="auth-subtext">Reset Password</p>

                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="email"
                                value="{{ old('email', session('email') ?? session('password_reset_verified_email')) }}">
                            <div class="input-group mb-3 rounded-pill border border-secondary-subtle">
                                <span class="input-group-text bg-transparent border-0 rounded-start-pill">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" name="password" class="form-control bg-transparent border-0"
                                    placeholder="New Password" id="password-input" required>
                                <span class="input-group-text bg-transparent border-0 rounded-end-pill cursor-pointer"
                                    onclick="togglePasswordVisibility('password-input')">
                                    <i class="bi bi-eye"></i>
                                </span>
                            </div>

                            <div class="input-group mb-3 rounded-pill border border-secondary-subtle">
                                <span class="input-group-text bg-transparent border-0 rounded-start-pill">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" name="password_confirmation"
                                    class="form-control bg-transparent border-0" placeholder="New Confirm Password"
                                    id="confirm-password-input" required>
                                <span class="input-group-text bg-transparent border-0 rounded-end-pill cursor-pointer"
                                    onclick="togglePasswordVisibility('confirm-password-input')">
                                    <i class="bi bi-eye"></i>
                                </span>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-custom-orange btn-lg rounded-pill">Reset
                                    Password</button>
                            </div>
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

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            toastr.error("{{ $error }}");
        @endforeach
    @endif
</script>

</html>
