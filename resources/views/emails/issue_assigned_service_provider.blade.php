<!DOCTYPE html>

<html>

<head>
    <meta charset="UTF-8">
    <title>New Issue Assigned</title>
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
                                Service Issue Notification
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:24px;color:#334155;font-size:14px;line-height:1.6;">

                            <p>Hello <strong>{{ $serviceProvider->name ?? 'Service Provider' }}</strong>,</p>

                            <p>
                                You have been assigned a new issue that requires your attention.
                                Please review the details below and proceed accordingly.
                            </p>

                            <!-- Issue Details -->
                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="margin:20px 0;border-collapse:collapse;">
                                <tr>
                                    <td colspan="2" style="padding-bottom:10px;">
                                        <strong style="font-size:15px;">Issue Details</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px;border:1px solid #e5e7eb;width:30%;"><strong>Title</strong>
                                    </td>
                                    <td style="padding:8px;border:1px solid #e5e7eb;">{{ $issueReport->issue_title }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px;border:1px solid #e5e7eb;"><strong>Urgency</strong></td>
                                    <td style="padding:8px;border:1px solid #e5e7eb;">
                                        {{ $issueReport->issue_urgency_level }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px;border:1px solid #e5e7eb;vertical-align:top;">
                                        <strong>Description</strong>
                                    </td>
                                    <td style="padding:8px;border:1px solid #e5e7eb;">
                                        {{ $issueReport->issue_details }}
                                    </td>
                                </tr>
                            </table>

                            <!-- Property Details -->
                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="margin:20px 0;border-collapse:collapse;">
                                <tr>
                                    <td colspan="2" style="padding-bottom:10px;">
                                        <strong style="font-size:15px;">Property Information</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px;border:1px solid #e5e7eb;width:30%;">
                                        <strong>Property</strong>
                                    </td>
                                    <td style="padding:8px;border:1px solid #e5e7eb;">
                                        {{ $property->property_title ?? 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px;border:1px solid #e5e7eb;"><strong>Address</strong></td>
                                    <td style="padding:8px;border:1px solid #e5e7eb;">
                                        {{ $property->address ?? 'N/A' }}
                                    </td>
                                </tr>

                                @if (!empty($property->latitude) && !empty($property->longitude))
                                    <tr>
                                        <td style="padding:8px;border:1px solid #e5e7eb;">
                                            <strong>Coordinates</strong>
                                        </td>
                                        <td style="padding:8px;border:1px solid #e5e7eb;">
                                            Latitude: {{ $property->latitude }}<br>
                                            Longitude: {{ $property->longitude }}<br>
                                            <a href="https://www.google.com/maps?q={{ $property->latitude }},{{ $property->longitude }}"
                                                target="_blank" style="color:#2563eb;text-decoration:none;">
                                                View on Google Maps
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            </table>

                            <!-- CTA -->
                            <div style="text-align:center;margin:30px 0;">
                                <a href="{{ url('/service-provider/login') }}"
                                    style="background:#2563eb;color:#ffffff;text-decoration:none;
                           padding:12px 24px;border-radius:4px;display:inline-block;font-weight:bold;">
                                    Login to Your Panel
                                </a>
                            </div>

                            <p>
                                If you require any additional information or assistance, please contact our support
                                team.
                            </p>

                            <p style="margin-top:24px;">
                                Regards,<br>
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
