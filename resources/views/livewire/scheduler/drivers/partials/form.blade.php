
<div class="row drivers">
    <div class="col-12 col-md-12">
        <form wire:submit.prevent="submit()">

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <h3 class="h5 mb-3"><i class="fas fa-user-alt me-2"></i>User Image</h3>
                        <x-forms.media
                        field-id="user_image"
                        model="form.user_image"
                        :entity="$form->user"
                        collection="user_image"
                        editable
                        rules="mimes:jpeg,png,webp"
                        />
                    </div>
                </div>

                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <h3 class="h5 mb-3">Skills</h3>
                        <div>
                            <div class="mb-2">
                                @foreach ($this->form->tags as $key => $tags)
                                    <span class="badge bg-primary me-2 p-2">
                                        <span>{{$tags}}</span>
                                        <button type="button" class="btn-close btn-close-white ms-2 skill-close-btn"  wire:click="removeTag({{$key}})"></button>
                                    </span>
                                @endforeach
                            </div>

                            <input
                                type="text"
                                wire:model="form.skills"
                                wire:keydown.enter.prevent="addTag"
                                wire:keydown.comma.prevent="addTag"
                                placeholder="Add skills "
                                class="form-control"
                            >
                        </div>
                    </div>
                </div>

            </div>
            <hr>
            <div class="mt-2 float-start">
                <button type="submit" class="btn btn-success">
                    <div wire:loading wire:target="submit">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>
                    {{ $button_text }}
                </button>
                <button type="button" wire:click="cancel" class="btn btn-light-secondary">Cancel</button>
            </div>
        </form>
    </div>
</div>

