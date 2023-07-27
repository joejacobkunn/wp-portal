<div wire:init="init">
    @if ($loadData)
        @if(!empty($actionButtons))
            <div class="btn-group float-end" role="group" aria-label="Basic example">
            @foreach ($actionButtons as $button)
                @if(empty($button['hide']))
                <button wire:click="buttonClicked({{ json_encode($button) }})" type="button" class="btn btn-sm btn-outline-{{ $button['color'] ?? 'primary' }}"><i class="fa {{ $button['icon'] ?? 'fa-mouse-pointer' }}" aria-hidden="true"></i> {{ $button['title'] ?? '' }}</button>
                @endif
            @endforeach
            </div>
        @endif

        <x-modal :toggle="$actionConfirm">
            <x-slot name="title">{{ !empty($activeButton['confirm_header']) ? $activeButton['confirm_header'] : "Confirm?" }}</x-slot>
            <p>{{ !empty($activeButton['confirm_message']) ? $activeButton['confirm_message'] : "Are you sure to proceed?" }}</p>
            <x-slot name="footer">
                <button type="button" wire:click.prevent="actionConfirmed" class="btn btn-danger close-modal" data-dismiss="modal">{{ !empty($activeButton['confirm_button_text']) ? $activeButton['confirm_button_text'] : "Confirm" }}</button>
                <button type="button" wire:click.prevent="actionCancel" class="btn btn-secondary close-btn" data-dismiss="modal">Close</button>
            </x-slot>
        </x-modal>
    @else
        Loading data...
    @endif
</div>
