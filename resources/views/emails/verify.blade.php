<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Handova - Email Verification</title>
</head>

<body style="margin:0; padding:0; background-color:#f2f4f6; font-family: Arial, sans-serif;">

    <table align="center" width="100%" cellpadding="0" cellspacing="0" style="padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:600px; background-color:#ffffff; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1); overflow:hidden;">

                    <!-- Logo -->
                    <tr>
                        <td align="center" style="padding:30px 0; background-color:#ffffff;">
                            <img src="{{ asset('public/images/handova.svg') }}" alt="Handova Logo" style="height:60px;">
                        </td>
                    </tr>

                    <!-- Email Body -->
                    <tr>
                        <td style="padding:30px; color:#333333;">
                            <h2 style="margin-top:0;">Hi,</h2>

                            <p style="line-height:1.6;">
                                Thank you for signing up for <strong>Handova</strong>! To complete your registration and
                                start enjoying all the benefits, please verify your email address by clicking the button
                                below.
                            </p>

                            <div style="text-align:center; margin:40px 0;">
                                <a href="{{ url('/verify-email/' . $token) }}"
                                    style="display:inline-block; background-color:#4CAF50; color:#ffffff; text-decoration:none; padding:15px 30px; border-radius:8px; font-weight:bold; font-size:16px;">
                                    Verify Email
                                </a>
                            </div>

                            <p style="line-height:1.6;">
                                If the button above does not work, copy and paste the following URL into your web
                                browser:
                            </p>

                            <p style="word-break:break-all; line-height:1.6; color:#555555; font-size:14px;">
                                <a href="{{ url('/verify-email/' . $token) }}"
                                    style="color:#4CAF50; text-decoration:none;">
                                    {{ url('/verify-email/' . $token) }}
                                </a>
                            </p>

                            <p style="line-height:1.6; margin-top:30px;">
                                Best Regards,<br>
                                <strong>Team Handova</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center"
                            style="padding:20px; background-color:#f8f9fa; font-size:12px; color:#888888;">
                            &copy; {{ date('Y') }} Handova. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>

</html>
