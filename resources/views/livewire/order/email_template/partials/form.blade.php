<div class="row">
    <div class="col-12 col-md-12">
        <div class="card card-body shadow-sm mb-4">
            <form wire:submit.prevent="submit()">

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input
                            label="Name"
                            model="name" 
                        />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.html-editor
                            label="Email Content"
                            :value="$emailContent"
                            model="emailContent"
                        />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.textarea
                            label="SMS Content"
                            model="smsContent"
                            hint="Max Length: 160 Chars"
                         />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-1">
                        <x-forms.checkbox
                            label="Is Active"
                            model="is_active"
                            :checked="$is_active"
                        />
                    </div>
                </div>

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