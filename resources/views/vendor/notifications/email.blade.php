<x-mail::layout>
    {{-- Header --}}
    <x-slot:header>
        <x-mail::header :url="config('app.url')">
            <img src="https://via.placeholder.com/150x50.png?text=Wasel+Logo" class="logo" alt="Wasel Logo" style="height: 50px; width: auto; max-height: 50px;">
        </x-mail::header>
    </x-slot:header>

    {{-- Body --}}
    <div style="font-family: sans-serif; text-align: center; color: #333;">

        {{-- Greeting --}}
        @if (! empty($greeting))
        <h1 style="font-size: 24px; font-weight: bold; margin-bottom: 20px;">{{ $greeting }}</h1>
        @else
        @if ($level === 'error')
        <h1 style="font-size: 24px; font-weight: bold; margin-bottom: 20px;">@lang('Whoops!')</h1>
        @else
        <h1 style="font-size: 24px; font-weight: bold; margin-bottom: 20px;">@lang('Hello!')</h1>
        @endif
        @endif

        {{-- Intro Lines --}}
        @foreach ($introLines as $line)
        <p style="font-size: 16px; line-height: 1.5; margin-bottom: 20px;">{{ $line }}</p>
        @endforeach

        {{-- Action Button --}}
        @isset($actionText)
        <div style="margin: 30px 0;">
            <a href="{{ $actionUrl }}" style="background-color: #2563eb; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                {{ $actionText }}
            </a>
        </div>
        @endisset

        {{-- Outro Lines --}}
        @foreach ($outroLines as $line)
        <p style="font-size: 16px; line-height: 1.5; margin-bottom: 20px;">{{ $line }}</p>
        @endforeach

        {{-- Salutation --}}
        @if (! empty($salutation))
        <p style="font-size: 16px; margin-top: 30px;">{{ $salutation }}</p>
        @else
        <p style="font-size: 16px; margin-top: 30px;">
            @lang('Regards,')<br>
            <strong>Wasel</strong>
        </p>
        @endif

    </div>

    {{-- Subcopy --}}
    @isset($actionText)
    <x-slot:subcopy>
        <x-mail::subcopy>
            <p style="font-size: 12px; color: #666; text-align: center;">
                @lang(
                "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below into your web browser:",
                [
                'actionText' => $actionText,
                ]
                )
                <br>
                <span class="break-all" style="word-break: break-all;">
                    <a href="{{ $actionUrl }}" style="color: #2563eb;">{{ $actionUrl }}</a>
                </span>
            </p>
        </x-mail::subcopy>
    </x-slot:subcopy>
    @endisset

    {{-- Footer --}}
    <x-slot:footer>
        <x-mail::footer>
            &copy; {{ date('Y') }} Wasel. جميع الحقوق محفوظة.
        </x-mail::footer>
    </x-slot:footer>
</x-mail::layout>