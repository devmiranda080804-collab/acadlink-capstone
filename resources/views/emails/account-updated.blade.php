@component('mail::message')
@switch($event)
@case('details')
# Your account was updated

Hi {{ $name }},

Your CBMA System account details were changed{{ $actorName ? ' by ' . $actorName : '' }} on {{ $when }}.

@component('mail::table')
| Detail | Before | Now |
| :----- | :----- | :-- |
@foreach($changes as $change)
| {{ $change['field'] }} | {{ $change['from'] }} | {{ $change['to'] }} |
@endforeach
@endcomponent
@break

@case('archived')
# Your account was archived

Hi {{ $name }},

Your CBMA System account was archived{{ $actorName ? ' by ' . $actorName : '' }} on {{ $when }}. You can no longer sign in.

If you think this is a mistake, please contact your Program Head or the Dean's office.
@break

@case('restored')
# Your account was restored

Hi {{ $name }},

Your CBMA System account was restored{{ $actorName ? ' by ' . $actorName : '' }} on {{ $when }}. You can sign in again.

@component('mail::button', ['url' => $loginUrl])
Sign In
@endcomponent
@break

@case('password_changed')
# Your password was changed

Hi {{ $name }},

The password for your CBMA System account was changed on {{ $when }}.
@break

@case('password_reset')
# Your password was reset

Hi {{ $name }},

The password for your CBMA System account was reset using "Forgot password" on {{ $when }}.
@break
@endswitch

@if(in_array($event, ['details', 'password_changed', 'password_reset']))
If you didn't expect this change, reset your password right away and contact the Dean's office.

@component('mail::button', ['url' => $resetUrl])
Reset Password
@endcomponent
@endif

Thanks,<br>
CBMA System
@endcomponent
