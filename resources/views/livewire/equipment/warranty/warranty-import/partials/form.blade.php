<div class="row">
    <div class="col-12 col-md-12">
        <div class="card card-body shadow-sm mb-4">
            <form wire:submit.prevent="">
                <div class="row">
                    <div class="col-md-12 mb-3"
                        x-data="{ uploading: false, progress: 0 }"
                        x-on:livewire-upload-start="uploading = true"
                        x-on:livewire-upload-finish="uploading = false"
                        x-on:livewire-upload-cancel="uploading = false"
                        x-on:livewire-upload-error="uploading = false">
                        <input type="file" id="csv-{{ $importIteration }}" class="form-control" wire:model="csvFile">
                        @error('csvFile') <span class="text-danger">{{ $message }}</span> @enderror
                        <div x-show="uploading" class="mt-2">
                            <div class="text-center">
                                <div class="spinner-border" role="status">
                                  <span class="sr-only">Loading...</span>
                                </div>
                              </div>
                    </div>
                </div>
                @if(!empty($rows))
                    @include('livewire.equipment.warranty.warranty-import.partials.csv-preview')
                @endif
                <hr>

                <div class="mt-2 float-start">

                    <button type="submit" class="btn btn-primary">
                        <div wire:loading wire:target="submit">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        {{$button_text}}
                    </button>

                    <button type="button" wire:click="cancel" class="btn btn-light-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
