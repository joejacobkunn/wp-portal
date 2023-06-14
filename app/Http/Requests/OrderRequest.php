<?php

namespace App\Http\Requests;

use App\Rules\ValidCustomer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    private $available_shipping_methods = [
        'FEDG' => 'Fedex Ground',
        'UPS2' => 'UPS 2nd Day',
        'UPS3' => 'UPS 3rd Day',
        'UPSA' => 'UPS Air Saver',
        'UPSG' => 'UPS Ground',
        'UPSN' => 'UPS Next Day',
        'UPSS' => 'UPS Saturday',
        'USPS' => 'USPS',
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'order_id' => 'required', //"EX0000001",
            'customer_number' => ['required', new ValidCustomer($this->customer_type)], //421S220
            'customer_type' => 'required|in:default,exmark',
            'purchase_date' => 'required|date',
            'warehouse' => 'required|min:1|max:4',
            'ship_to' => 'sometimes|in:MCDO,RICH',

            'shipping.method' => Rule::in(array_keys($this->available_shipping_methods)), // get values from mark
            'shipping.cost' => 'numeric',
            'shipping.first_name' => 'required',
            'shipping.last_name' => 'required',
            'shipping.address' => 'required',
            'shipping.address_2' => 'nullable',
            'shipping.state' => 'required|min:2|max:2',
            'shipping.city' => 'required',
            'shipping.zip' => 'required',
            'shipping.country' => 'required|in:US,CA',

            'customer.first_name' => 'required',
            'customer.last_name' => 'required',
            'customer.address' => 'required',
            'customer.state' => 'required|min:2|max:2',
            'customer.city' => 'required',
            'customer.zip' => 'required',
            'customer.address' => 'required',
            'customer.address_2' => 'nullable',
            'customer.country' => 'required|in:US,CA',
            'customer.phone' => 'sometimes|digits:10',
            'customer.email' => 'sometimes|email',

            'items' => 'array|required',
            'items.*.part_number' => 'required',
            'items.*.quantity' => 'numeric|min:1',

        ];
    }
}

//whse should default to RICH
//transType = SO
//takenby = WEB
//slsRepIn = blank
//shipVia from request
//refer is seller_id
//orderid is pono
// "actionType": "original",
//           "orderDisp": "",

//if ship to , use arss
//icsd
//shipToNo is ship_to
