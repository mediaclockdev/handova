<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Service Completed</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f7f7f7; padding:20px;">
    <div style="max-width:600px; margin:auto; background:#ffffff; padding:20px; border-radius:6px;">
        <h2 style="color:#28a745;">Service Completed ✅</h2>

        <p>Hello,</p>

        <p>
            We’re happy to inform you that your service request has been
            <strong>successfully completed</strong> by the service provider.
        </p>

        <table style="width:100%; margin-top:15px;">
            <tr>
                <td><strong>Issue ID:</strong></td>
                <td>{{ $issueReport->id }}</td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td>{{ ucfirst($issueReport->issue_status) }}</td>
            </tr>
        </table>

        <p style="margin-top:20px;">
            If you have any concerns or feedback, feel free to contact us.
        </p>

        <p style="margin-top:30px;">
            Thank you for choosing <strong>Handova</strong>.
        </p>

        <p>
            Regards,<br>
            <strong>Handova Team</strong>
        </p>
    </div>
</body>
</html>
