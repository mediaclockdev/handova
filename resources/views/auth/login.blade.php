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
                        <h4 class="auth-heading">Welcome!</h4>
                        <p class="auth-subtext">Log in to continue</p>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="input-group mb-3 rounded-pill border border-secondary-subtle bg-white">
                                <span class="input-group-text bg-transparent border-0 rounded-start-pill">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" name="email" class="form-control bg-transparent border-0 rounded-pill"
                                    placeholder="Username/Email" required value="{{ old('email') }}">
                            </div>

                            <div class="input-group mb-3 rounded-pill border border-secondary-subtle bg-white">
                                <span class="input-group-text bg-transparent border-0 rounded-start-pill">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" name="password" class="form-control bg-transparent border-0 rounded-pill"
                                    placeholder="Password" id="password-input" required>
                                <span class="input-group-text bg-transparent border-0 rounded-end-pill cursor-pointer"
                                    onclick="togglePasswordVisibility('password-input')">
                                    <i class="bi bi-eye"></i>
                                </span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember"
                                        id="rememberMeCheck">
                                    <label class="form-check-label" for="rememberMeCheck">
                                        Remember me
                                    </label>
                                </div>
                                <a href="{{ route('password.request') }}" class="forgot-password-link">Forgot your
                                    password?</a>
                            </div>
                            {{-- @if ($errors->any())
                                <p style="color:red">{{ $errors->first() }}</p>
                            @endif --}}


                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-custom-orange btn-lg rounded-pill">Log in</button>
                            </div>
                            <p class="mt-6 text-center new_account">
                                Create a New Account <a href="{{ route('register') }}"><span class="link">Sign Up</a></span>
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

    @if(session('success'))
    toastr.success("{{ session('success') }}");
    @endif

    @if(session('error'))
    toastr.error("{{ session('error') }}");
    @endif
</script>

</html>