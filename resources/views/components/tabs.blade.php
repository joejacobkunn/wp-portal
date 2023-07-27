<div>
    @php
        $id = $tabId;
        $urlParam = $urlParam ?? 'tab';
    @endphp
    <div id="{{ $id }}" >
        <div class="nav nav-tabs nav-tabs-ul mb-3 {{ $class ?? '' }}" id="nav-tab" role="tablist" wire:ignore >
            @foreach($tabs as $tabIndex => $tabTitle)
                <a class="nav-item nav-link {{ $this->{$activeTabIndex} == $tabIndex ? 'active' : '' }}" data-tab="{{ $tabIndex }}" role="tab" aria-controls="nav-pending-approval" aria-selected="false">
                @if(!empty(${"tab_header_" . $tabIndex}))
                    {{ ${"tab_header_" . $tabIndex} }}
                @else
                    {{ $tabTitle }}
                @endif
                </a>
            @endforeach
        </div>
        <div class="tab-content">
            @if(!empty($content))
                {{ $content }}
            @endif
            @foreach($tabs as $tabIndex => $tabTitle)
                @if($this->{$activeTabIndex} == $tabIndex && !empty(${"tab_content_". $tabIndex}))
                    @if(isset(${"tab_content_". $tabIndex}->attributes['component']))
                        @livewire(${"tab_content_". $tabIndex}->attributes['component'], (${"tab_content_". $tabIndex}->attributes->except('component')->getAttributes()), key('item-'.$tabIndex))
                    @endif

                    {{ ${"tab_content_". $tabIndex} }}
                @endif
            @endforeach
        </div>
    </div>
    <div>
<script>
    (function () {
        var tabId = '{{ $id }}';
        var initialRun = true;
        var requestProcessing = false;

        document.addEventListener('livewire:load', function () {
            initializeTabs();
            initialRun = false
            let url = new URL(window.location.href);
            let searchParams = url.searchParams;
            if (!document.querySelectorAll('#{{$id }} .nav-item.active').length) {
                if (searchParams.get('{{ $urlParam }}') && document.querySelector('#{{$id }} .nav-item[data-tab='+ searchParams.get('{{ $urlParam }}') +']')) {
                    document.querySelector('#{{$id }} .nav-item[data-tab='+ searchParams.get('{{ $urlParam }}') +']').click()
                } else {
                    document.querySelector('#{{$id }} .nav-item').click()
                }
            }
        });
        function initializeTabs() {
            document.addEventListener('click', function(event) {
                if (event.target.matches('#{{$id }} .nav-item')) {
                    event.preventDefault();
                    indexActiveTabChanged(event.target)
                }
            }, false);
        }
        function indexActiveTabChanged (el) {
            if (el.classList.contains('active')) return
            let status = el.dataset.tab
            document.querySelectorAll('#{{$id }} .nav-item').forEach((tab) => {
                if (tab.classList.contains('active')) {
                    tab.classList.remove('active')
                    if (tab.querySelector('.badge')) {
                        tab.querySelector('.badge').classList.remove('badge-light')
                        tab.querySelector('.badge').classList.add('bg-primary')
                    }
                }
            })
            el.classList.add('active')
            if (el.querySelector('.badge')) {
                el.querySelector('.badge').classList.remove('bg-primary')
                el.querySelector('.badge').classList.add('badge-light')
                el.querySelector('.badge').style.visibility = "hidden"
            }
            let tabContentDom = document.querySelector('#{{$id }} .tab-content')
            tabContentDom.classList.add('loading-skeleton');
            el.innerHTML = el.innerHTML + '<i class="fas fa-spinner fa-spin"></i>'

            new Promise(function(resolve, reject) {
                if (requestProcessing) {
                  setInterval(() => {
                    resolve(true)
                  }, 500)
                } else {
                    resolve(true)
                }

            }).then(() => {

                @this.processActiveTabChange(status, '{{ $activeTabIndex }}', '{{ $id }}').then(() => {
                    let element = document.querySelector('#{{$id }} [data-tab='+ (el.dataset.tab) +']')
                    if (element.querySelector('.badge')) {
                        element.querySelector('.badge').style.visibility = "visible"
                    }
                    if (element.querySelector('.fas')) {
                        element.querySelector('.fas').remove()
                    }

                    setTimeout(() => {
                        tabContentDom.classList.remove('loading-skeleton');
                        if (document.querySelector('#{{ $id }} .nav-tabs .div-overlay')) {
                            document.querySelector('#{{ $id }} .nav-tabs .div-overlay').remove()
                        }
                    }, 800)
                })

            })
        }


        document.addEventListener("DOMContentLoaded", () => {
            let counter = 0;
            Livewire.hook('message.sent', (message, component) => {
                counter++;
                requestProcessing = true

                if (document.querySelector('#{{ $id }} .nav-tabs') && !document.querySelector('#{{ $id }} .nav-tabs .div-overlay')) {
                    let loaderDom = document.createElement('div');
                    loaderDom.classList.add('div-overlay')
                    document.querySelector('#{{ $id }} .nav-tabs').appendChild(loaderDom)
                }
            })

            Livewire.hook('message.processed', (message, component) => {
                counter--;

                if (counter == 0 && document.querySelector('#{{ $id }} .tab-content')) {
                    setTimeout(() => {
                        let tabContentDom = document.querySelector('#{{$id }} .tab-content')
                        if (tabContentDom) {
                            tabContentDom.classList.remove('loading-skeleton');
                            if (document.querySelector('#{{ $id }} .nav-tabs .div-overlay')) {
                                document.querySelector('#{{ $id }} .nav-tabs .div-overlay').remove()
                            }
                        }
                    }, 500)
                }
            })
        });
    })()
    </script>
</div>
</div>
