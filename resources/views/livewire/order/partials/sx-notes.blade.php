<div class="card border border-light border-3 shadow-sm mb-4">
    <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
        <h3 class="h5 mb-2">SX Order Notes</h3>
    </div>

    <div class="card-body">
        <ul class="list-group overflow-scroll" style="height:650px">
            @if (!empty($order_notes))
                @forelse ($order_notes['notes'] as $notes)
                    @forelse ($notes as $note)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                {{ $note['notetext'] }}</span>
                            <span
                                class="badge bg-secondary badge-pill badge-round ms-1">{{ Carbon\Carbon::parse($note['transdt'])->diffForHumans() }}</span>
                        </li>
                    @empty
                        <li class="list-group-item">No notes</li>
                    @endforelse
                @empty
                    <li class="list-group-item">No notes</li>
                @endforelse
            @else
                <div class="card" aria-hidden="true">
                    <div class="card-body">
                        <h5 class="card-title placeholder-glow">
                            <span class="placeholder col-6"></span>
                        </h5>
                        <p class="card-text placeholder-glow">
                            <span class="placeholder col-7"></span>
                            <span class="placeholder col-4"></span>
                            <span class="placeholder col-4"></span>
                            <span class="placeholder col-6"></span>
                            <span class="placeholder col-8"></span>
                        </p>
                        <a href="#" tabindex="-1" class="btn btn-primary disabled placeholder col-6"></a>
                    </div>
                </div>

            @endif
        </ul>
    </div>


</div>
