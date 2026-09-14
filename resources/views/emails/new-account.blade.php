@component('mail::message')
# Welcome to CBMA System

Hi {{ $name }},

An account has been created for you as a **{{ $role }}** in the CBMA System.

- **Email:** {{ $email }}
- **Temporary Password:** {{ $password }}

@component('mail::button', ['url' => $loginUrl])
Sign In Now
@endcomponent

You will need to change your password after logging in.

Thanks,<br>
CBMA System
@endcomponent