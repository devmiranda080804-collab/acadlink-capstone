{{-- Bulletproof button: a table cell carries the colour so it still shows when images or CSS are off. --}}
<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:24px 0;">
    <tr>
        <td align="center" bgcolor="{{ $color ?? '#0f2557' }}" style="border-radius:8px; background-color:{{ $color ?? '#0f2557' }};">
            <a href="{{ $url }}" target="_blank" style="display:inline-block; padding:13px 28px; font-family:Arial, Helvetica, sans-serif; font-size:15px; font-weight:bold; color:#ffffff; text-decoration:none; border-radius:8px;">{{ $label }}</a>
        </td>
    </tr>
</table>
