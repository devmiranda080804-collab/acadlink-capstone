@extends('emails.layouts.cbma')

@section('title', 'Reset your password')
@section('preheader', 'Use the link inside to reset your CBMA System password. It is valid for 60 minutes.')

@section('content')
@include('emails.partials.heading', ['tone' => 'info', 'label' => 'Password Reset', 'title' => 'Reset your password'])

<p style="margin:0 0 14px;">Hi {{ $name }},</p>
<p style="margin:0 0 6px;">We received a request to reset the password for your CBMA System account. Click the button below to choose a new one.</p>

@include('emails.partials.button', ['url' => $url, 'label' => 'Reset Password'])

<p style="margin:0 0 14px; font-size:14px; color:#5b6377;">This link is valid for <strong>60 minutes</strong> only. If you did not request this, you can safely ignore this email.</p>
<p style="margin:0 0 14px; font-size:12px; line-height:18px; color:#8a91a5;">If the button doesn't work, copy this link into your browser:<br><a href="{{ $url }}" style="color:#1f4fbf; word-break:break-all;">{{ $url }}</a></p>
@endsection
