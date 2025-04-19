@component('mail::message')

**From:** {{ $name }} ({{ $email }})
**Subject:** {{ $subject }}

**Message:**
{{ $message }}

@component('mail::button', ['url' => config('app.url')])
Visit Site
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent