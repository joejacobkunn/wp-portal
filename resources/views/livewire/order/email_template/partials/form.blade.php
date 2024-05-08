<div class="row">
    <div class="col-12 col-md-12">
        <div class="card card-body shadow-sm mb-4">
            <form wire:submit.prevent="submit()">

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Name" model="name" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-group">
                            <x-forms.select label="Template Type" model="templateType" :options="$templateTypes"
                                :selected="$template->type ?? null" default-selectable default-option-label="- None -" />
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Email Subject" model="emailSubject" />
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.html-editor label="Email Content" :value="$emailContent" model="emailContent"
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
                            <span class="list-group-item"><mark>[CustomerName]</mark> => Customer Name of Order</span>
                            <span class="list-group-item"><mark>[CustomerEmail]</mark> => Customer Email of Order</span>
                            <span class="list-group-item"><mark>[CustomerPhone]</mark> => Customer Phone of Order</span>
                            <span class="list-group-item"><mark>[OrderNumber]</mark> => Order Number</span>
                            <span class="list-group-item"><mark>[ShipVia]</mark> => Ship Via</span>
                            <span class="list-group-item"><mark>[ShippingTrackingNumber]</mark> => Shipping Tracking
                                Number</span>
                            <span class="list-group-item"><mark>[WarehousePhone]</mark> => Warehouse Phone Number of
                                Order</span>

                            <span class="list-group-item"><mark>[LineItems]</mark> => All Line Items in Order (Bulleted
                                List)</span>
                            <span class="list-group-item"><mark>[BackorderLineItems]</mark> => Backorder Line Items in
                                Order(Bulleted List)</span>
                            <span class="list-group-item"><mark>[DNRItems]</mark> => DNR Items in Order (Bulleted
                                List)</span>
                            <span class="list-group-item"><mark>[GolfItems]</mark> => Golf Items in Web Order (Bulleted
                                List)</span>
                            <span class="list-group-item"><mark>[NonDNRItems]</mark> => Non DNR Items in Order
                                (Bulleted List)</span>
                            <span class="list-group-item text-primary"><strong>For manual placeholders, use {}
                                    tag</strong></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.textarea label="SMS Content" model="smsContent" hint="Max Length: 160 Chars" />
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
