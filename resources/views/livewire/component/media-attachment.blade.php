<div class="x-media-library">
    @if($editable)
        @if($multiple)
            <x-media-library-collection 
                name="file"
                :model="$entity"
                :rules="$rules"
                :collection="$collection"
                propertiesView="livewire.component.media-properties"
                wire:key="{{ $fieldId }}"
                :fields-view="!empty($extraFieldView) ? $extraFieldView : null"
            />
        @else
            <x-media-library-attachment
                name="file" 
                :rules="$rules"
                wire:key="{{ $fieldId }}"
                :fields-view="!empty($extraFieldView) ? $extraFieldView : null"
            />
        @endif
    @else

    @if(!$hideLayoutSwitch && $medias->count())
    <div class="overflow-auto mb-2">
        <div class="btn-group float-end" role="group">
            <button 
                wire:click="toggleView('grid')"
                type="button"
                onclick="toggleViewType('grid')"
                class="btn btn-outline-primary {{ $viewType == 'grid' ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
            </button>
            <button
                type="button"
                class="btn btn-outline-primary {{ $viewType == 'list' ? 'active' : '' }}"
                onclick="toggleViewType('list')"
                wire:click="toggleView('list')">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
    @endif


    <div class="row">
        <div wire:loading wire:target="toggleView">
            <div class="d-block text-center text-primary p-5" >
                <span class="spinner-grow text-primary spinner-grow-sm me-1" role="status" aria-hidden="true"></span>
                <span class="spinner-grow text-primary spinner-grow-sm me-1" role="status" aria-hidden="true"></span>
                <span class="spinner-grow text-primary spinner-grow-sm me-1" role="status" aria-hidden="true"></span>
            </div>
        </div>
    </div>

    <div class="media-view-div" wire:loading.remove> 
        @if($viewType == 'grid')
            <div class="media-grid-div">
            @if($gridView)
                @include($gridView, ['medias' => $medias])
            @else
                <div class="row">
                @foreach($medias as $index => $mediaItem)
                <div class="col-12 col-sm-2 {{ $index > 11 ? 'hidden-row' : ''}}">
                    <div class="image-thumb-div mb-5">
                        @if($mediaItem->previewUrl)
                            <div class="preview-div" wire:click="showPreview({{ $index }})"><i class="fas fa-eye"></i></div>
                            <img src="{{ $mediaItem->previewUrl }}" class="media-library-thumb-img img-thumbnail" alt="{{ $mediaItem->fileName }}" />
                        @else
                        <span class="media-library-thumb-extension img-thumbnail">
                            <span class="media-library-thumb-extension-truncate">
                                @if($mediaItem->extension =='pdf' )
                                <a href="{{ $mediaItem->originalUrl }}" target="_blank">
                                    <div class="preview-div"><i class="fas fa-eye"></i></div>
                                </a>

                                <i class="fas fa-file-pdf"></i>
                                @elseif($mediaItem->extension =='xlsx' )
                                <i class="fas fa-file-excel"></i>
                                @else
                                <i class="fas fa-file-alt"></i>
                                @endif
                            </span>
                        </span>
                        @endif
                        <a href="{{ $mediaItem->originalUrl }}" download class="download-media-btn"><i class="fas fa-download"></i> Download</a>

                        <div>
                            {{ $mediaItem->file_name }}
                        </div>
                    </div>
                </div>
                @endforeach
                </div>

                <div class="row justify-content-center {{ $medias->count() > 12 ? '' : 'd-none' }}">
                    <div class="d-grid gap-2 col-sm-2">
                        <button class="btn btn-xs btn-outline-secondary" id="view-more-button" type="button"><i class="fas fa-plus"></i> View More</button>
                    </div>
                </div>

            @endif
            </div>
        @else
            <div class="media-list-div">
            @if($listView)
                @include($listView, ['medias' => $medias])
            @else
                <div>
                    @forelse($medias as $index => $mediaItem)
                    <div class=" {{ $index > 1 ? 'hidden-row' : '' }}">
                        <div class="overflow-hidden">
                            <div class="image-thumb-div mb-4 me-5">
                                @if($mediaItem->previewUrl)
                                    <div class="preview-div" wire:click="showPreview({{ $index }})"><i class="fas fa-eye"></i></div>
                                    <img src="{{ $mediaItem->previewUrl }}" class="media-library-thumb-img img-thumbnail" alt="{{ $mediaItem->fileName }}" />
                                @else
                                <span class="media-library-thumb-extension img-thumbnail">
                                    <span class="media-library-thumb-extension-truncate">
                                        @if($mediaItem->extension =='pdf' )
                                        <a href="{{ $mediaItem->originalUrl }}" target="_blank">
                                            <div class="preview-div"><i class="fas fa-eye"></i></div>
                                        </a>

                                        <i class="fas fa-file-pdf"></i>
                                        @elseif($mediaItem->extension =='xlsx' )
                                        <i class="fas fa-file-excel"></i>
                                        @else
                                        <i class="fas fa-file-alt"></i>
                                        @endif
                                    </span>
                                </span>
                                @endif
                                <a href="{{ $mediaItem->originalUrl }}" download class="download-media-btn"><i class="fas fa-download"></i> Download</a>

                                <div>
                                    {{ $mediaItem->file_name }}
                                </div>
                            </div>
                            
                            @if($extraFieldView)
                                @include($extraFieldView, ['mediaItem' => $mediaItem, 'readonly' => true])
                            @endif
                        </div>
                        <hr/>
                    </div>
                    @empty
                        <p>No items found!</p>
                    @endforelse

                    <div class="row justify-content-center {{ $medias->count() > 2 ? '' : 'd-none' }}">
                        <div class="d-grid gap-2 col-sm-2">
                            <button class="btn btn-xs btn-outline-secondary" id="view-more-button" type="button"><i class="fas fa-plus"></i> View More</button>
                        </div>
                    </div>
                </div>
            @endif
            </div>
        @endif

    </div>
    @endif

    @if($previewImage)
        <div class="modal fade show" id="modal-achievement" tabindex="-1" role="dialog" aria-labelledby="modal-achievement" aria-modal="true" style="display: block; padding-left: 0px;">
        
        <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close" wire:click="closePreview"><i class="fas fa-times"></i></button>    
        <div class="modal-dialog modal-tertiary modal-dialog-centered" role="document">
                <div class="modal-content">

                    <div class="modal-body p-0">
                        <img src="{{ $previewImage->originalUrl }}" class="media-library-thumb-img img-thumbnail" alt="">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

@if($editable && !$multiple && count($medias))
    <script>
        @this.loadUploaderMedia()
    </script>
@endif

    <script>
        var detail;
        var timer = timer || null;
        let initialRun = false;

        function postMedia() {
            if (timer) {
                clearTimeout(timer)
            }

            timer = setTimeout(() => {
                window.livewire.emit('{{ $listener }}' , '{{ $model }}', detail.media)
            }, 600)
        }
        function toggleViewType (type) {
            localStorage.setItem('media-view-type', type);
        }

        window.addEventListener('media-updated', event => {
            detail = {
                collection: event.detail.collection,
                media: event.detail.media,
            }

            postMedia()
        })

        document.addEventListener('livewire:load', function () {
            @this.toggleView(getViewType()).then(() => {
                initializeLayout()
            })

            initialRun = true
        })


        window.addEventListener('contentChanged', event => {
            if (initialRun) {
                initialRun = false
                return
            }

            try {
                initialRun = true
                @this.toggleView(getViewType()).then(() => {
                    initializeLayout()
                })
            } catch ($e) {}
        });
        
        function initializeLayout() {
            let viewMoreButton = document.getElementById('view-more-button')

            if (viewMoreButton) {
                document.getElementById('view-more-button').addEventListener("click", (element) => {
                    let currentStatus = element.target.dataset.status;
                    currentStatus = currentStatus != undefined ? parseInt(currentStatus) : 0;
                    element.target.dataset.status = 1 - currentStatus

                    if (currentStatus) {
                        var elems = document.querySelectorAll(".hidden-row-temp");
                        [].forEach.call(elems, function(el) {
                            el.classList.add("hidden-row");
                            el.classList.remove("hidden-row-temp");
                        });

                        viewMoreButton.innerHTML = '<i class="fas fa-plus"></i> View More'
                    } else {
                        var elems = document.querySelectorAll(".hidden-row");
                        [].forEach.call(elems, function(el) {
                            el.classList.add("hidden-row-temp");
                            el.classList.remove("hidden-row");
                        });

                        viewMoreButton.innerHTML = '<i class="fas fa-minus"></i> View Less'
                    }
                });
            }
        }

        function getViewType() {
            let viewType = localStorage.getItem('media-view-type')
            return viewType ? viewType : '{{ $viewType }}'
        }
    </script>
</div>