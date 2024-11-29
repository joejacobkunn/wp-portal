<div class="activity-log">
    @if($defaultLoad)
    <div {{ $deferLoad ? 'wire:init=loadLogs' : '' }} wire:target="loadLogs" wire:loading.class="loading-skeleton">
    @else
    <div>
    @endif
        <div class="card border-light shadow-sm mb-4">
            <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
                <h3 class="h5 mb-0"><i class="fas fa-history me-1"></i> {{ $title }}</h3>
                <div class="card-body">

                    @if(!$defaultLoad)
                    <div>
                        <button type="button" class="btn btn-pill btn-outline-gray-500" wire:click="loadLogs()">Click to Load..</button>
                    </div>
                    @else

                        @forelse($logs as $log)
                            <div class="my-2">
                                <i class="{{ $log['icon'] }} me-1"></i>  <span>{!! $log['title'] !!}</span>
                                <span class="timestamp-div" title="{{ $log['timestamp'] }}"><i class="far fa-clock"></i> {{ $log['timestamp_string'] }}</span>

                                @if(!empty($log['changes']))
                                <div class="ms-1 ps-3 activity-change-info">
                                    @foreach($log['changes'] as $change)
                                        <div class="my-2">
                                            <label>{{ $change['label'] }}</label>

                                            <div class="row small desc">
                                                <div class="col-md-5">
                                                    <span>{!! $change['old_value'] !!}</span>
                                                </div>
                                                <div class="col-md-2">
                                                    <i class="fas fa-long-arrow-alt-right"></i>
                                                </div>
                                                <div class="col-md-5">
                                                    <span>{!! $change['new_value'] !!}</span>
                                                </div>          
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @elseif(isset($log['description']))
                                    <div class="ms-1 ps-3 activity-change-info">
                                        <div class="my-2">
                                            <span>{!! $log['description'] !!}</span>
                                        </div>
                                    </div>
                                @endif
                                <div role="separator" class="dropdown-divider my-3"></div>
                            </div>
                        @empty
                            <p>No activity info found!</p>
                        @endforelse

                        @if($nextPage)
                        <center>
                            <button wire:click="loadLogs()" class="btn btn-sm btn-gray-200 mb-2" type="button">
                                <div wire:loading>
                                    <span class="spinner-border spinner-border-sm " role="status" aria-hidden="true"></span>
                                </div>
                                Load Previous
                            </button>
                        </center>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>