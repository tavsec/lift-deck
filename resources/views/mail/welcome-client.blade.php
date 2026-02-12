<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 32px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width: 600px; width: 100%;">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: {{ $coach->primary_color ?? '#2563EB' }}; padding: 24px 32px; border-radius: 8px 8px 0 0; text-align: center;">
                            @if($coach->logo)
                                <img src="{{ url(\Illuminate\Support\Facades\Storage::url($coach->logo)) }}" alt="{{ $coach->gym_name ?? $coach->name }}" style="max-height: 48px; margin-bottom: 8px;">
                            @endif
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: bold;">
                                {{ $coach->gym_name ?? $coach->name }}
                            </h1>
                        </td>
                    </tr>
                    <!-- Body -->
                    <tr>
                        <td style="background-color: #ffffff; padding: 32px;">
                            <p style="margin: 0 0 16px; font-size: 16px; color: #374151;">
                                Hi {{ $client->name }},
                            </p>
                            <p style="margin: 0 0 24px; font-size: 16px; color: #374151; line-height: 1.5;">
                                @if($coach->welcome_email_text)
                                    {{ $coach->welcome_email_text }}
                                @else
                                    We're excited to have you on board! Your coach is ready to help you reach your fitness goals.
                                @endif
                            </p>
                            <table cellpadding="0" cellspacing="0" style="margin: 0 auto;">
                                <tr>
                                    <td style="background-color: {{ $coach->primary_color ?? '#2563EB' }}; border-radius: 6px; padding: 12px 32px;">
                                        <a href="{{ route('login') }}" style="color: #ffffff; text-decoration: none; font-size: 16px; font-weight: bold;">
                                            Get Started
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 16px 32px; text-align: center; border-radius: 0 0 8px 8px; background-color: #f9fafb;">
                            <p style="margin: 0; font-size: 12px; color: #9ca3af;">
                                {{ $coach->gym_name ?? $coach->name }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
