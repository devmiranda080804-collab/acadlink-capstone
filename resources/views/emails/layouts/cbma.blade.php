<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="x-apple-disable-message-reformatting">
<title>@yield('title')</title>
</head>
{{-- Email clients (Gmail especially) drop <style> rules and external images, so everything is
     table-based with inline styles, and the logo is embedded in the message itself (CID). --}}
<body style="margin:0; padding:0; background-color:#eef1f7; -webkit-text-size-adjust:100%;">
<div style="display:none; max-height:0; overflow:hidden; mso-hide:all; font-size:1px; line-height:1px; color:#eef1f7;">@yield('preheader')</div>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#eef1f7;">
    <tr>
        <td align="center" style="padding:32px 12px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:600px; background-color:#ffffff; border-radius:14px; overflow:hidden; border:1px solid #dde2ee;">
                {{-- Letterhead --}}
                <tr>
                    <td align="center" style="background-color:#0f2557; padding:28px 24px 22px; border-bottom:4px solid #f2b705;">
                        <img src="{{ $message->embed(resource_path('templates/branding/cbma-email-logo.png')) }}" width="76" height="74" alt="CBMA logo" style="display:block; border:0; width:76px; height:74px; margin:0 auto 12px;">
                        <div style="font-family:Arial, Helvetica, sans-serif; font-size:11px; font-weight:bold; letter-spacing:1.6px; color:#f2b705; text-transform:uppercase; line-height:16px;">College of Business Management and Accountancy</div>
                        <div style="font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#c9d3ee; line-height:18px; margin-top:2px;">Urdaneta City University</div>
                    </td>
                </tr>

                {{-- Body --}}
                <tr>
                    <td style="padding:32px 36px 8px; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px; color:#2b3245;">
                        @yield('content')
                    </td>
                </tr>

                <tr>
                    <td style="padding:8px 36px 32px; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px; color:#2b3245;">
                        Thank you,<br>
                        <strong style="color:#0f2557;">CBMA System</strong>
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td align="center" style="background-color:#f6f8fc; border-top:1px solid #e3e8f2; padding:18px 24px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:18px; color:#7a8297;">
                        This is an automated message from the CBMA System. Please do not reply to this email.<br>
                        &copy; {{ date('Y') }} College of Business Management and Accountancy, Urdaneta City University
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
