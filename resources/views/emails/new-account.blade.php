@component('mail::message')
# Welcome to CBMA System

Hi {{ $name }},

Gumawa ng account para sa iyo bilang **{{ $role }}** sa CBMA System.

- **Email:** {{ $email }}
- **Temporary Password:** {{ $password }}

@component('mail::button', ['url' => $loginUrl])
Sign In Now
@endcomponent

Kailangan mong palitan ang password mo pagka-login.

Thanks,<br>
CBMA System
@endcomponent