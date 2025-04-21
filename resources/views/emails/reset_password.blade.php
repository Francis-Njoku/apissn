@component('mail::message')
# Reset Password

Your six-digit PIN is: **{{ $pin }}**

Please do not share your One Time Pin with anyone. You made a request to reset your password. Please discard if this wasn't you.

Thanks,
{{ config('app.name') }}
@endcomponent
