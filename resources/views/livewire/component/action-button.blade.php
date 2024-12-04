<div>
    @if (!empty($actionButtons))
        <div class="btn-group float-end {{ $btnGroupClass ?? '' }}" role="group" aria-label="Action Button">
            @foreach ($actionButtons as $actionButtonIndex => $button)
                @if (empty($button['hide']))
                    <button wire:click="buttonClicked({{ $actionButtonIndex }})" wire:loading.attr="disabled"
                        wire:target="buttonClicked({{ $actionButtonIndex }})" type="button"
                        class="{{ $button['class'] ?? ('btn btn-outline-' . $button['color'] ?? 'primary') }}">

                        <div wire:loading wire:target="buttonClicked({{ $actionButtonIndex }})">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>

                        <i class="{{ $button['iconType'] ?? 'fa' }} {{ $button['icon'] ?? 'fa-mouse-pointer' }}"
                            aria-hidden="true"></i>
                        {{ $button['title'] ?? '' }}
                    </button>
                @endif
            @endforeach

        </div>
    @endif

    <x-modal :toggle="$actionConfirm">
        <x-slot
            name="title">{{ !empty($activeButton['confirm_header']) ? $activeButton['confirm_header'] : 'Confirm?' }}</x-slot>
        <p>{{ !empty($activeButton['confirm_message']) ? $activeButton['confirm_message'] : 'Are you sure you want to proceed?' }}
        </p>
        <x-slot name="footer">
            <button type="button" wire:click.prevent="actionCancel" class="btn btn-secondary close-btn"
                data-dismiss="modal">Close</button>
            <button type="button" wire:click.prevent="actionConfirmed"
                class="btn btn-{{ !empty($activeButton['confirm_button_class']) ? $activeButton['confirm_button_class'] : 'danger' }} close-modal"
                data-dismiss="modal">{{ !empty($activeButton['confirm_button_text']) ? $activeButton['confirm_button_text'] : 'Yes' }}</button>
        </x-slot>
    </x-modal>
</div>
