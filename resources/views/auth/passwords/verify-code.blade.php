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

                    <!-- Verification Code Page Content -->
                    <div id="verification-page" class="auth-page">
                        <h4 class="auth-heading">Welcome!</h4>
                        <p class="auth-subtext">We've sent a 6-digit verification code to your phone number or email.
                            Please
                            check your messages and enter the code below to continue.</p>

                        <form method="POST" action="{{ route('password.verify-code') }}">
                            @csrf
                            <input type="hidden" name="email" value="{{ session('password_reset_verified_email') }}">

                            <div class="verification-input-group mb-4">
                                @for ($i = 0; $i < 6; $i++)
                                    <input type="text"
                                        class="form-control verification-input border border-secondary-subtle text-center"
                                        name="code[]" maxlength="1" required>
                                @endfor
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-custom-orange btn-lg rounded-pill">Next</button>
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

    @if (session('error'))
        toastr.error("{{ session('error') }}");
    @endif
</script>

</html>
