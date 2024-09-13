@component('mail::message', ['welcome' => $welcome], ['token' => $token], ['email' => $email])

    {!!$welcome['template']!!}

@component('mail::button', ['url' => config('app.url_front')."/auth/reset-password/$token/$email"])
Resetear contraseña
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

