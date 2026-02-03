<!DOCTYPE html>

<html>

<head>
    <meta charset="UTF-8">
    <title>Issue Assigned</title>
</head>

<body style="margin:0;padding:0;background-color:#f4f6f8;font-family:Arial,Helvetica,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8;padding:20px;">
        <tr>
            <td align="center">

                ```
                <!-- Email Container -->
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff;border-radius:6px;overflow:hidden;">

                    <!-- Header -->
                    <tr>
                        <td style="background:#0f172a;padding:20px;text-align:center;">
                            <h1 style="color:#ffffff;margin:0;font-size:20px;">
                                Handova
                            </h1>
                            <p style="color:#cbd5e1;margin:6px 0 0;font-size:13px;">
                                Issue Assignment Update
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:24px;color:#334155;font-size:14px;line-height:1.6;">

                            <p>Hello <strong>{{ $user->name ?? 'User' }}</strong>,</p>

                            <p>
                                Your issue request
                                <strong>{{ $issueReport->issue_number }}</strong>
                                has been successfully assigned to a service provider.
                            </p>

                            <!-- Issue Summary -->
                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="margin:20px 0;border-collapse:collapse;">
                                <tr>
                                    <td colspan="2" style="padding-bottom:10px;">
                                        <strong style="font-size:15px;">Issue Summary</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px;border:1px solid #e5e7eb;width:30%;">
                                        <strong>Issue Title</strong>
                                    </td>
                                    <td style="padding:8px;border:1px solid #e5e7eb;">
                                        {{ $issueReport->issue_title ?? 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px;border:1px solid #e5e7eb;">
                                        <strong>Urgency Level</strong>
                                    </td>
                                    <td style="padding:8px;border:1px solid #e5e7eb;">
                                        {{ $issueReport->issue_urgency_level ?? 'N/A' }}
                                    </td>
                                </tr>
                            </table>

                            <p>
                                You can log in to your dashboard to track progress, communicate with the service
                                provider,
                                and view further updates.
                            </p>

                            <!-- CTA -->
                            <div style="text-align:center;margin:30px 0;">
                                <a href="{{ url('/dashboard') }}"
                                    style="background:#2563eb;color:#ffffff;text-decoration:none;
                           padding:12px 24px;border-radius:4px;display:inline-block;font-weight:bold;">
                                    View Issue in Dashboard
                                </a>
                            </div>

                            <p>
                                If you need any assistance, please contact our support team.
                            </p>

                            <p style="margin-top:24px;">
                                Best regards,<br>
                                <strong>{{ config('app.name') }}</strong><br>
                                Support Team
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f1f5f9;padding:14px;text-align:center;font-size:12px;color:#64748b;">
                            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
                            This is an automated email. Please do not reply.
                        </td>
                    </tr>

                </table>
                <!-- End Container -->

            </td>
        </tr>
        ```

    </table>

</body>

</html>
