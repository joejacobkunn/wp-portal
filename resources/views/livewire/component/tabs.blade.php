<div>
    @php
        $id = $tabId;
        $urlParam = $urlParam ?? 'tab';
        $xid = preg_replace("/[^a-zA-Z0-9]+/", "", $id);
    @endphp

    <div id="{{ $id }}">
        <div class="nav nav-tabs nav-tabs-ul mb-3 {{ $class ?? '' }}" id="nav-tab" role="tablist">
            @foreach($tabs as $tabIndex => $tabTitle)
                <a class="nav-item nav-link {{ $componentActiveTab == $tabIndex ? 'active' : '' }}" wire:click="updateTab('{{ $tabId }}', '{{ $tabIndex }}')" data-tab="{{ $tabIndex }}" role="tab" aria-controls="nav-pending-approval" aria-selected="false">
                    @if(!empty($tabHeaders[$tabIndex]))
                        {!! $tabHeaders[$tabIndex] !!}
                    @else
                        {{ $tabTitle }}
                    @endif
                
                    <i class="fas fa-spinner fa-spin ms-1 {{ $componentActiveTab == $tabIndex && $componentActiveTab != $activeTab ? '' : 'd-none' }}" ></i>
                </a>
            @endforeach
        </div>
        <div class="tab-content {{ $componentActiveTab != $activeTab ? 'loading-skeleton' : '' }}">
            @if(!empty($commonContent))
                @if(!empty($commonContent['component']))
                    @livewire($commonContent['component'], $commonContent['attributes'], key('item-'.$tabIndex))
                @else
                    {!! $commonContent['html'] !!}
                @endif
            @endif
            
            @foreach($tabs as $tabIndex => $tabTitle)
                @if($activeTab == $tabIndex && isset($tabContents[$tabIndex]))
                    @if(!empty($tabContents[$tabIndex]))
                        @if(isset($tabContents[$tabIndex]['component']))
                            @livewire($tabContents[$tabIndex]['component'], $tabContents[$tabIndex]['attributes'], key('content-item-'.$tabIndex))
                        @else
                            {!! $tabContents[$tabIndex]['html'] !!}
                        @endif
                    @endif
                @endif
            @endforeach
        </div>
    </div>
</div>