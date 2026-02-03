<!DOCTYPE html>
<html lang="en">

<head>
    <title>Create Account</title>
    <script>
        window.FontAwesomeConfig = {
            autoReplaceSvg: 'nest'
        };
    </script>
    @include('partials.head')

    <style>
        .cursor-pointer {
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="builders_signup">
        <main>
            <img src="https://storage.googleapis.com/uxpilot-auth.appspot.com/3c9d19b0ae-5e3cf70623fecf263c82.png"
                alt="background" class="bg-img">
            <div class="overlay"></div>

            <section id="signup-section">
                <div class="text-center">
                    <img src="{{ asset('images/handova.svg') }}" alt="Handova Logo" class="logo">
                    <p class="brand-subtitle">SMART HANDOVERS</p>
                    <h2>Create Your Builder Account</h2>
                    <p>Join our platform to streamline your projects.</p>
                </div>

                <form id="signup-form" method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="grid mb-3">
                        <!-- FIRST NAME -->
                        <div>
                            <div class="input-group mb-1 rounded-pill border border-secondary-subtle bg-white">
                                <span class="input-group-text border-0 rounded-start-pill">
                                    <i class="fa-solid fa-user"></i>
                                </span>

                                <input type="text" id="first_name" name="first_name"
                                    class="form-control border-0 rounded-pill @error('first_name') is-invalid @enderror"
                                    placeholder="First Name *" value="{{ old('first_name') }}">
                            </div>

                            @error('first_name')
                                <div class="text-danger small mb-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- LAST NAME -->
                        <div>
                            <div class="input-group mb-1 rounded-pill border border-secondary-subtle bg-white">
                                <span class="input-group-text border-0 rounded-start-pill">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                                <input type="text" id="last_name" name="last_name"
                                    class="form-control border-0 rounded-pill @error('last_name') is-invalid @enderror"
                                    placeholder="Last Name *" value="{{ old('last_name') }}">
                            </div>

                            @error('last_name')
                                <div class="text-danger small mb-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- EMAIL -->
                    <div class="mb-3">
                        <div class="input-group mb-1 rounded-pill border border-secondary-subtle bg-white">
                            <span class="input-group-text border-0 rounded-start-pill">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <input type="email" id="email" name="email"
                                pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                                class="form-control border-0 rounded-pill @error('email') is-invalid @enderror"
                                placeholder="Company's Email Address *" value="{{ old('email') }}">
                        </div>
                        @error('email')
                            <div class="text-danger small mb-2">{{ $message }}</div>
                        @enderror
                    </div>



                    <!-- PHONE -->
                    <div class="mb-3">
                        <div class="input-group mb-1 rounded-pill border border-secondary-subtle bg-white">
                            <span class="input-group-text border-0 rounded-start-pill">
                                <i class="fa-solid fa-phone"></i>
                            </span>
                            <input type="tel" id="phone" name="phone"
                                class="form-control border-0 rounded-pill  @error('phone') is-invalid @enderror"
                                value="{{ old('phone', $nationalNumber) }}">

                            <input type="hidden" name="country_codes" id="country_codes"
                                value="{{ old('country_codes', $countryCode) }}">
                            <input type="hidden" id="country_isos" value="{{ $countryIso }}">
                        </div>
                        @error('phone')
                            <div class="text-danger small mb-2">{{ $message }}</div>
                        @enderror
                    </div>


                    {{-- <div class="col-md-6 phone_number_div">
                        <div>
                            <label>Phone Number <span style="color:red;">*</span></label>
                        </div>



                    </div> --}}

                    <!-- PASSWORD -->
                    <div class="mb-3">
                        <div class="input-group mb-1 rounded-pill border border-secondary-subtle bg-white">
                            <span class="input-group-text border-0 rounded-start-pill">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input type="password" id="password" name="password"
                                class="form-control border-0 rounded-pill @error('password') is-invalid @enderror"
                                placeholder="Password *">
                            <span class="input-group-text border-0 rounded-end-pill cursor-pointer"
                                onclick="togglePasswordVisibility('password', this)">
                                <i class="bi bi-eye"></i>
                            </span>
                        </div>

                        @error('password')
                            <div class="text-danger small mb-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- CONFIRM PASSWORD -->
                    <div class="mb-3">
                        <div class="input-group mb-1 rounded-pill border border-secondary-subtle bg-white">
                            <span class="input-group-text border-0 rounded-start-pill">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-control border-0 rounded-pill @error('password_confirmation') is-invalid @enderror"
                                placeholder="Confirm Password *">
                            <span class="input-group-text border-0 rounded-end-pill cursor-pointer"
                                onclick="togglePasswordVisibility('password_confirmation', this)">
                                <i class="bi bi-eye"></i>
                            </span>
                        </div>

                        @error('password_confirmation')
                            <div class="text-danger small mb-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- TERMS -->
                    <div class="terms mb-0">
                        <label class="checkbox-label">
                            <input type="checkbox" name="terms" id="terms"
                                class="@error('terms') is-invalid @enderror" {{ old('terms') ? 'checked' : '' }}>
                            <span>
                                I agree to the
                                <a href="#" target="_blank">Terms & Conditions</a>
                                and
                                <a href="#" target="_blank">Privacy Policy</a>
                            </span>
                        </label>
                    </div>

                    @error('terms')
                        <div class="text-danger small mb-2">{{ $message }}</div>
                    @enderror

                    <div class="form-group mt-2">
                        <button type="submit" class="btn">Create Account</button>
                    </div>
                </form>

                <div class="form-group">
                    <p class="mt-6 text-center">
                        Already have an account? <a href="{{ route('login') }}"><span class="link">Log
                                in</span></a>
                    </p>
                </div>
            </section>

            <section id="verification-section" class="hidden text-center">
                <div class="icon-wrapper">
                    <i class="fa-solid fa-paper-plane fa-2x" style="color: var(--brand-orange);"></i>
                </div>
                <h2>Verify Your Email</h2>
                <p>We've sent a verification link to <strong id="user-email-display"
                        style="color: var(--brand-gray)">your.email@example.com</strong>. Please check your inbox and
                    follow the instructions.</p>
                <button class="btn">Open Email App</button>
                <p class="mt-6">Didn't receive the email? <span class="link">Resend link</span></p>
            </section>
        </main>
    </div>

    <!-- PASSWORD VISIBILITY SCRIPT -->
    <script>
        function togglePasswordVisibility(id, iconWrapper) {
            const input = document.getElementById(id);
            const icon = iconWrapper.querySelector("i");

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            }
        }
    </script>

    @include('partials/scripts')
</body>

</html>
