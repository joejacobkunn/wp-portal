<div class="row">
    <div class="col-12 col-md-12">
        <div class="card card-body shadow-sm mb-4">
            <form wire:submit.prevent="submit()">

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Name" model="form.name" />
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Email Subject" model="form.emailSubject" />
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.html-editor label="Email Content" :value="$form->emailContent" model="form.emailContent"
                            :key="'email-content'" />
                    </div>
                </div>

                <p>
                    <a class="btn btn-sm btn-link" data-bs-toggle="collapse" href="#collapseExample" role="button"
                        aria-expanded="false" aria-controls="collapseExample">
                        Show template placeholders
                    </a>
                </p>
                <div class="collapse" id="collapseExample">
                    <div class="card card-body">
                        <div class="list-group">
                            <span class="list-group-item"><mark>[CustomerName]</mark> =>Customer Name</span>
                            <span class="list-group-item"><mark>[Warehouse]</mark> => Warehouse</span>
                            <span class="list-group-item"><mark>[OrderNumber]</mark> => Order Number</span>
                            <span class="list-group-item"><mark>[LineItems]</mark> =>  Line Items</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.textarea label="SMS Content" model="form.smsContent" hint="Max Length: 300 Chars" />
                    </div>
                </div>


                <hr>

                <div class="mt-2 float-start">

                    <button type="submit" class="btn btn-primary">
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
</div>
