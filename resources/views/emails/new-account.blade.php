@extends('emails.layouts.cbma')

@section('title', 'Welcome to CBMA System')
@section('preheader', 'Your CBMA System account is ready. Sign in with your temporary password.')

@section('content')
@include('emails.partials.heading', ['tone' => 'success', 'label' => 'New Account', 'title' => 'Welcome to CBMA System'])

<p style="margin:0 0 14px;">Hi {{ $name }},</p>
<p style="margin:0 0 18px;">An account has been created for you as a <strong>{{ $role }}</strong> in the CBMA System. Use these details to sign in:</p>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f6f8fc; border:1px solid #e3e8f2; border-radius:10px; font-size:14px; line-height:20px;">
    <tr>
        <td style="padding:14px 16px 6px; color:#7a8297; width:150px;">Email</td>
        <td style="padding:14px 16px 6px; font-weight:bold; color:#2b3245;">{{ $email }}</td>
    </tr>
    <tr>
        <td style="padding:6px 16px 14px; color:#7a8297;">Temporary Password</td>
        <td style="padding:6px 16px 14px;"><span style="font-family:Consolas, 'Courier New', monospace; font-size:15px; font-weight:bold; color:#0f2557; background-color:#ffffff; border:1px dashed #b9c3dc; border-radius:6px; padding:4px 10px;">{{ $password }}</span></td>
    </tr>
</table>

@include('emails.partials.button', ['url' => $loginUrl, 'label' => 'Sign In Now'])

<p style="margin:0 0 14px; font-size:14px; color:#5b6377;">You will be asked to change your password after you sign in.</p>
@endsection
