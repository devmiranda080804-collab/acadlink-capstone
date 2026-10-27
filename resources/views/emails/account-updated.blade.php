@php
    $info = [
        'details'          => ['info',    'Account Update',   'Your account was updated'],
        'archived'         => ['danger',  'Account Archived', 'Your account was archived'],
        'restored'         => ['success', 'Account Restored', 'Your account was restored'],
        'password_changed' => ['warning', 'Security Notice',  'Your password was changed'],
        'password_reset'   => ['warning', 'Security Notice',  'Your password was reset'],
    ][$event];
    $by = $actorName ? ' by <strong>' . e($actorName) . '</strong>' : '';
@endphp
@extends('emails.layouts.cbma')

@section('title', $info[2])
@section('preheader', $info[2] . ' on ' . $when . '.')

@section('content')
@include('emails.partials.heading', ['tone' => $info[0], 'label' => $info[1], 'title' => $info[2]])

<p style="margin:0 0 14px;">Hi {{ $name }},</p>

@switch($event)
@case('details')
<p style="margin:0 0 18px;">Your CBMA System account details were changed{!! $by !!} on {{ $when }}. Here is what changed:</p>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #e3e8f2; border-radius:10px; border-collapse:separate; overflow:hidden; font-size:14px; line-height:20px;">
    <tr style="background-color:#f6f8fc;">
        <td style="padding:10px 14px; font-size:11px; font-weight:bold; letter-spacing:.8px; text-transform:uppercase; color:#7a8297; border-bottom:1px solid #e3e8f2;">Detail</td>
        <td style="padding:10px 14px; font-size:11px; font-weight:bold; letter-spacing:.8px; text-transform:uppercase; color:#7a8297; border-bottom:1px solid #e3e8f2;">Before</td>
        <td style="padding:10px 14px; font-size:11px; font-weight:bold; letter-spacing:.8px; text-transform:uppercase; color:#7a8297; border-bottom:1px solid #e3e8f2;">Now</td>
    </tr>
    @foreach($changes as $change)
    <tr>
        <td style="padding:12px 14px; font-weight:bold; color:#2b3245;{{ $loop->last ? '' : ' border-bottom:1px solid #eef1f7;' }}">{{ $change['field'] }}</td>
        <td style="padding:12px 14px; color:#8a91a5; text-decoration:line-through;{{ $loop->last ? '' : ' border-bottom:1px solid #eef1f7;' }}">{{ $change['from'] }}</td>
        <td style="padding:12px 14px; font-weight:bold; color:#1d7a43;{{ $loop->last ? '' : ' border-bottom:1px solid #eef1f7;' }}">{{ $change['to'] }}</td>
    </tr>
    @endforeach
</table>
@break

@case('archived')
<p style="margin:0 0 14px;">Your CBMA System account was archived{!! $by !!} on {{ $when }}. You can no longer sign in.</p>
<p style="margin:0 0 14px;">If you think this is a mistake, please contact your Program Head or the Dean's office.</p>
@break

@case('restored')
<p style="margin:0 0 6px;">Your CBMA System account was restored{!! $by !!} on {{ $when }}. You can sign in again.</p>
@include('emails.partials.button', ['url' => $loginUrl, 'label' => 'Sign In'])
@break

@case('password_changed')
<p style="margin:0 0 14px;">The password for your CBMA System account was changed on {{ $when }}.</p>
@break

@case('password_reset')
<p style="margin:0 0 14px;">The password for your CBMA System account was reset using "Forgot password" on {{ $when }}.</p>
@break
@endswitch

@if(in_array($event, ['details', 'password_changed', 'password_reset']))
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:22px;">
    <tr>
        <td style="background-color:#fff8e6; border-left:4px solid #f2b705; border-radius:6px; padding:14px 16px; font-size:14px; line-height:21px; color:#5c4a12;">
            <strong>Didn't make this change?</strong> Reset your password right away and contact the Dean's office.
        </td>
    </tr>
</table>
@include('emails.partials.button', ['url' => $resetUrl, 'label' => 'Reset Password'])
@endif
@endsection
