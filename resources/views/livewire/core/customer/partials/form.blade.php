<div class="row">
    <div class="col-12 col-md-12">
        <div class="card card-body shadow-sm mb-4">
            <form wire:submit.prevent="submit">

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Customer Email" model="customer.email" prepend-icon="fas fa-at" lazy />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Customer Phone" model="customer.phone"
                            prepend-icon="fas fa-phone-square-alt" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-group">
                            <x-forms.select label="Type" model="customer.customer_type" :options="$customer_types"
                                :selected="$customer->customer_type ?? null" default-selectable default-option-label="- None -" />
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Customer Name" model="customer.name" lazy />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3" wire:ignore>
                        <x-forms.input label="Customer Address Line 1" model="customer.address" lazy />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Customer Address Line 2" model="customer.address2" lazy />
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <x-forms.input label="ZIP Code" model="customer.zip" lazy />
                    </div>

                    <div class="col-md-4 mb-3">
                        <x-forms.input label="City" model="customer.city" lazy />
                    </div>

                    <div class="col-md-4 mb-3">
                        <x-forms.input label="State" model="customer.state" />
                    </div>

                </div>


                <div class="mt-2">
                    <button type="submit" class="btn btn-success">
                        <div wire:loading wire:target="submit">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>

                        {{ $button_text }}

                    </button>
                    <button type="button" wire:click="cancel" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    @assets
        <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('google.api_key') }}&libraries=places&v=weekly"
        defer
        ></script>
    @endassets

    @script
        <script>
            (function () {
                let autocomplete;
                let address1Field;
                let address2Field;
                let postalField;

                function waitForMapJs(callback) {

                    var checkGoogleMaps = setInterval(function() {
                        if (google && google.maps) {
                            clearInterval(checkGoogleMaps);
                            callback();
                        }
                    }, 300); // Check every 100ms
                }

                waitForMapJs(function () {
                    address1Field = document.getElementById("customer.address_input-field");
                    address2Field = document.getElementById("customer.address2_input-field");
                    postalField = document.getElementById("customer.zip_input-field");
                    
                    autocomplete = new google.maps.places.Autocomplete(address1Field, {
                        componentRestrictions: { country: ["us", "ca"] },
                        fields: ["address_components", "geometry"],
                        types: ["address"],
                    });
                    
                    autocomplete.addListener("place_changed", fillInAddress);
                });

                function fillInAddress() {
                    
                    const place = autocomplete.getPlace();
                    let address1 = "";
                    let postcode = "";

                    for (const component of place.address_components) {
                        const componentType = component.types[0];

                        switch (componentType) {
                            case "street_number": {
                                address1 = `${component.long_name} ${address1}`;
                                break;
                            }

                            case "route": {
                                address1 += component.short_name;
                                break;
                            }

                            case "postal_code": {
                                postcode = `${component.long_name}${postcode}`;
                                break;
                            }

                            case "postal_code_suffix": {
                                postcode = `${postcode}-${component.long_name}`;
                                break;
                            }

                            case "locality":
                                document.getElementById("customer.city_input-field").value = component.long_name;
                                $wire.set('customer.city', component.long_name);
                                break;

                            case "administrative_area_level_1": {
                                document.getElementById("customer.state_input-field").value = component.short_name;
                                $wire.set('customer.state', component.short_name);
                                break;
                            }
                        }
                    }

                    address1Field.value = address1;
                    postalField.value = postcode;
                    $wire.set('customer.address', address1);
                    $wire.set('customer.zip', postcode);
                    
                    address2Field.focus();
                }
            })();
        </script> 
    @endscript

</div>
