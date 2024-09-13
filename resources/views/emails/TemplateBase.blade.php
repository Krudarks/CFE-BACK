@component('mail::layout')
    @slot('header')
        @component('mail::header', ['url' => env('FRONT_URL')])
            <!-- header here -->
            @if(!is_null($logo))
                <div class="" style="height: 70px">
                    <img src="{{ $logo }}" class="title-logo" style='height: 100%; width:100%; object-fit: contain;'>
                </div>
            @endif
        @endcomponent
    @endslot

    {!! $message!!}
    @component('mail::button', ['url' => env('FRONT_URL')])
        Ir al sistema
    @endcomponent

    @slot('footer')
        @component('mail::footer')
            {{ env('APP_NAME') }}
        @endcomponent
    @endslot
@endcomponent
