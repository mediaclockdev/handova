<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>OTP Verification</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f7f7f7; padding:20px;">
    <div style="max-width:600px; margin:auto; background:#ffffff; padding:20px; border-radius:6px;">
        <h2 style="color:#333;">OTP Verification Required</h2>

        <p>Hello,</p>

        <p>
            Your service provider has requested to mark the service as <strong>completed</strong>.
            To confirm this action, please use the OTP below:
        </p>

        <h1 style="letter-spacing:4px; color:#2d89ef;">
            {{ $otp }}
        </h1>

        <p>
            This OTP is valid for <strong>10 minutes</strong>.
            Please do not share this OTP with anyone.
        </p>

        <p style="margin-top:30px;">
            Regards,<br>
            <strong>Handova</strong>
        </p>
    </div>
</body>
</html>
