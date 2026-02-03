@component('mail::message')
# Hello Super Admin,

We received a request to reset your password.  

Click the button below to reset it:

@component('mail::button', ['url' => $resetUrl])
Reset Password
@endcomponent

If you didn’t request this, please ignore this email.  

Thanks,  
{{ config('app.name') }}
@endcomponent
