<div class="row">
    <div class="col-12 col-md-12">
        <form wire:submit.prevent="importData">
            <div class="row">
                <div class="col-md-12 mb-3">
                    @if($showalert['status'])
                        <div class="alert alert-light-{{ $showalert['class']}} color-{{ $showalert['class']}}">
                            <i class="fas fa-info-circle"></i>
                           {{ $showalert['message'] }}
                        </div>
                    @else
                        <div class="alert alert-light-info color-info">
                            <i class="fas fa-info-circle"></i>
                            Please upload sms marketing file csv here, you will see a preview of the data in the next step before
                            import. <a href="#" wire:click.prevent="downloadDemo">
                                click here </a>to download csv file template <i class="fas fa-download"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-6 mb-3">
                    <x-forms.input label="Name" model="name" lazy />
                </div>
                <div class="col-md-12 mt-2" x-data="{ uploading: false, progress: 0 }" x-on:livewire-upload-start="uploading = true"
                    x-on:livewire-upload-finish="uploading = false" x-on:livewire-upload-cancel="uploading = false"
                    x-on:livewire-upload-error="uploading = false">
                    <label>Upload File</label>
                    <input type="file" id="csv-{{ $importIteration }}" class="form-control" wire:model="importFile">
                    @error('importFile')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <div x-show="uploading" class="mt-2">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                @if (!empty($validatedRows) || !empty($importErrorRows))
                    <div class="col-md-12 mb-3">
                        <hr>
                    </div>
                    @include('livewire.marketing.sms-marketing.partials.csv-preview')
                @endif
                <div class="col-md-12 mb-3">
                    <hr>
                </div>
                <div class="mt-2 float-start">

                    <button type="submit" class="btn btn-primary">
                        <div wire:loading wire:target="submit">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        {{ $button_text }}
                    </button>

                    <button type="button" wire:click="cancel" class="btn btn-light-secondary">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
