<div class="dropdown-tab text-center">
        <button class="btn btn-icon" type="button" id="dropdownMenuButton{{ $id }}" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-ellipsis-v"></i>
        </button>
        <ul class="dropdown-menu custom-dropdown" aria-labelledby="dropdownMenuButton{{ $id }}">
            <li><a class="dropdown-item" href="{{ $orginal }}">Download Uploaded File</a></li>
            <li><a class="dropdown-item" href="{{ $failedPath }}">Download Failed Records</a></li>
        </ul>
    </div>
