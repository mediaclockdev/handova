<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Handova - Email Verification Code</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f2f4f6; font-family: Arial, sans-serif;">

    <table align="center" width="100%" cellpadding="0" cellspacing="0" style="padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0"
                    style="max-width: 600px; background-color: #ffffff; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); overflow: hidden;">
                    <tr>
                        <td align="center" style="padding: 30px 0; background-color: #ffffff;">
                            <img src="{{ asset('images/handova.svg') }}" alt="Handova Logo" style="height: 60px;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 30px; color: #333333;">
                            <h2 style="margin-top: 0;">Hi {{ $userName }},</h2>

                            <p style="line-height: 1.6;">
                                Thanks for signing up for <strong>Handova</strong>! To complete your registration and
                                start enjoying all the benefits, please verify your email address.
                            </p><br>

                            <p style="line-height: 1.6;">
                                We've sent you a One-Time Password (OTP) to ensure that
                                <strong>{{ $email }}</strong> is a valid email address and belongs to you.
                            </p><br>

                            <div style="text-align: center; margin: 30px 0;">
                                <p style="font-size: 18px; margin-bottom: 10px;">Your OTP is:</p>
                                <div
                                    style="display: inline-block; background-color: #f0f0f0; padding: 15px 25px; border-radius: 8px; font-size: 28px; font-weight: bold; letter-spacing: 6px;">
                                    {{ $code }}
                                </div>
                            </div>

                            <p style="line-height: 1.6;">
                                This OTP is valid for the next <strong>10 minutes</strong>. Please enter this code on
                                the verification page to activate your account.
                            </p>

                            <p style="line-height: 1.6; margin-top: 30px;">
                                Best Regards,<br>
                                <strong>Team Handova</strong>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center"
                            style="padding: 20px; background-color: #f8f9fa; font-size: 12px; color: #888888;">
                            &copy; {{ date('Y') }} Handova. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>

</html>
