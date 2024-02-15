@component('mail::message')
    @if (!app()->environment('production'))
        <div
            style="position: relative; padding: 0.75rem 1.25rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: 0.25rem; text-align: center; color: #856404; background-color: #fff3cd; border-color: #ffeeba;">
            Note: This email is generated for testing purpose only.</div>
    @endif

    {{-- Greeting --}}
    @if (!empty($greeting))
        # {{ $greeting }}
    @else
        @if ($level === 'error')
            # @lang('Whoops!')
        @else
            # @lang('Hello!')
        @endif
    @endif

    {{-- Intro Lines --}}
    @foreach ($introLines as $line)
        {!! $line !!}
    @endforeach

    {{-- Action Button --}}
    @isset($actionText)
        <?php
        switch ($level) {
            case 'success':
            case 'error':
                $color = $level;
                break;
            default:
                $color = 'primary';
        }
        ?>
        @component('mail::button', ['url' => $actionUrl, 'color' => $color])
            {{ $actionText }}
        @endcomponent
    @endisset

    {{-- Outro Lines --}}
    @foreach ($outroLines as $line)
        {!! $line !!}
    @endforeach

    {{-- Salutation --}}
    @if (!empty($salutation))
        {{ $salutation }}
    @else
        @lang('Regards'),<br>
        {{ config('app.name') }}
    @endif

    {{-- Subcopy --}}
    @isset($actionText)
        @slot('subcopy')
            @lang("If you’re having trouble clicking the \":actionText\" button, copy and paste the URL below\n" . 'into your web browser:', [
                'actionText' => $actionText,
            ]) <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
        @endslot
    @endisset
@endcomponent
