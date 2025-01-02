<div class="row service-area">
    <div class="col-12 col-md-12">
        <form wire:submit.prevent="submit()">

            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <h3 class="h5 mb-3"><i class="fas fa-user-alt me-2"></i>User Image</h3>
                        <x-forms.media
                        field-id="user_image"
                        model="form.user_image"
                        :entity="$form->staffInfo"
                        collection="user_image"
                        editable
                        rules="mimes:jpeg,png"
                        />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <x-forms.textarea label="Description" model="form.description" rows="5" key="{{ 'description' }}" />
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
