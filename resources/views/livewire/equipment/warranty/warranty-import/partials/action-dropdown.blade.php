<div wire:ignore class="table-action">
    <div class="dropdown-tab text-center" x-data="{ isOpen: false }">
    <button class="btn btn-icon" type="button" id="dropdownMenuButton{{ $id }}" @click="isOpen = true">
        <i class="fas fa-ellipsis-v"></i>
    </button>

    <div x-show="isOpen" @click.away="isOpen = false" class="popup">
        <div class="popup-content">
        <a class="popup-item" href="{{ $orginal }}">Download Uploaded File</a>
        @if ($failedPath)
            <a class="popup-item" href="{{ $failedPath }}">Download Failed Records</a>
        @endif
        </div>
    </div>
    </div>
</div>
