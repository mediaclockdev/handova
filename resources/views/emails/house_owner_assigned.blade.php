<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Handova - Property Assignment Notification</title>
</head>

<body style="margin:0; padding:0; background-color:#f2f4f6; font-family:Arial, sans-serif;">

    <table align="center" width="100%" cellpadding="0" cellspacing="0" style="padding:40px 0;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:600px; background-color:#ffffff; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1); overflow:hidden;">

                    <!-- Logo Section -->
                    <tr>
                        <td align="center" style="padding:30px 0; background-color:#ffffff;">
                            <img src="{{ asset('images/handova.svg') }}" alt="Handova Logo" style="height:60px;">
                        </td>
                    </tr>

                    <!-- Email Body -->
                    <tr>
                        <td style="padding:30px; color:#333333;">

                            <h2 style="margin-top:0;">Hello {{ $owner->first_name }} {{ $owner->last_name }},</h2>

                            <p style="line-height:1.6;">
                                Great news! Your property has been successfully assigned from builder
                                <strong>{{ $builderId }}</strong>.
                            </p>

                            <p style="line-height:1.6;">
                                To view and manage your property details, please download the
                                <strong>Handova</strong> mobile app and log in using your registered email:
                            </p>

                            <div style="text-align:center; margin:25px 0;">
                                <div
                                    style="display:inline-block; background-color:#f0f0f0; padding:12px 20px; border-radius:8px; font-size:16px; font-weight:bold; letter-spacing:0.5px; color:#333;">
                                    {{ $owner->email_address }}
                                </div>
                            </div>

                            <p style="line-height:1.6; text-align:center;">
                                <a href="https://play.google.com/store/apps/details?id=YOUR_APP_ID"
                                    style="display:inline-block; margin:10px; background-color:#4CAF50; color:#ffffff; text-decoration:none; padding:12px 28px; border-radius:8px; font-size:15px; font-weight:bold;">
                                    Download App
                                </a>
                            </p>

                            <p style="line-height:1.6; margin-top:30px;">
                                Thank you,<br>
                                <strong>The Handova Team</strong>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center"
                            style="padding:20px; background-color:#f8f9fa; font-size:12px; color:#888;">
                            &copy; {{ date('Y') }} Handova. All rights reserved.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>
