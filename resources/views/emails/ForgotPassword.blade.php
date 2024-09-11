@component('mail::message')
<p>Estimado(a) <strong>{{ $data["username"] }} </strong></p>

<p>Su contraseña de acceso al Sistema de Gestor Documental. </p>

<strong>{{ $data["token"] }} </strong>

<p>Si necesita ayuda, contacte por favor con el administrador del sitio/p>

@component('mail::button', ['url' => config('app.url_front')])
            Ir al sistema
@endcomponent

<p><strong>Atentamente</strong></p>
<p>Sistema de Gestor Documental</p>
<p style="color: #5a6268; font-size: .8em;">*La información de este correo, así como la contenida en los documentos que se adjuntan, puede ser objeto de solicitudes de acceso a la información</p>

@endcomponent
