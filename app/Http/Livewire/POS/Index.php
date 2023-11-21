<?php

namespace App\Http\Livewire\POS;

use App\Classes\SX;
use App\Http\Livewire\Component\Component;

class Index extends Component
{
    public $productSearchModal = false;

    public $productQuery = '';
    public $productResult;
    public $cart = [];

    public function render()
    {
        return $this->renderView('livewire.pos.index');
    }

    public function searchProduct()
    {
        $this->resetValidation();
        $this->resetErrorBag();

        if (!$this->productQuery) {
            return $this->addError('productQuery', 'Enter Product Code.' );
        }

        $pricingRequest = [
            'request' => [
                "companyNumber" => 10,
                "operatorInit" => "wpa",
                "operatorPassword" =>  "",
                "productCode" => $this->productQuery,
                "customerNumber" => 1,
                "shipTo" => "",
                "unitOfMeasure" => "EA",
                "includeSellingPrice" => true,
                "warehouse" => "utic",
                "quantityOrdered" =>  0,
                "tInfieldvalue" => [
                    "t-infieldvalue" => [
                        "level" => "string",
                        "lineno"=> 0,
                        "seqno" => 0,
                        "fieldname" => "string",
                        "fieldvalue" =>  "string"

                    ]
                ]
            ]

        ];

        $sx = new SX();
        $searchResponse = $sx->get_product($pricingRequest);

        if (empty($searchResponse['status']) || $searchResponse['status'] == 'error') {
            return $this->addError('productQuery', 'Invalid Product Code.' );
        }

        if ($searchResponse['stock'] < 1) {
            return $this->addError('productQuery', 'Out of stock.' );
        }

        $this->productResult = [
            'product_code' => $this->productQuery,
            'product_name' => $searchResponse['product_name'],
            'look_up_name' => $searchResponse['look_up_name'],
            'category' => $searchResponse['category'],
            'price' => $searchResponse['price'],
            'stock' => $searchResponse['stock'],
            'bin_location' => $searchResponse['bin_location'],
            'prodline' => $searchResponse['prodline'],
            'quantity' => isset($this->cart[$this->productQuery]) ? ($this->cart[$this->productQuery]['quantity'] > $searchResponse['stock'] ? $searchResponse['stock'] :  $this->cart[$this->productQuery]['quantity']) : 1,
        ];
        $this->reset('productQuery');

        $this->productSearchModal = true;
    }

    public function updateQuantity($qty, $productCode = null)
    {
        $product = $productCode ? $this->cart[$productCode] : $this->productResult;

        $product['quantity'] = $product['quantity'] + ((int) $qty);
        if ($product['quantity'] < 1) {
            $product['quantity'] = 1;
        }

        if ($productCode) {
            $this->cart[$productCode]['quantity'] = $product['quantity'];
        } else {
            $this->productResult['quantity'] = $product['quantity'];
        }

    }

    public function addToCart()
    {
        $this->cart[$this->productResult['product_code']] = $this->productResult;
        $this->productSearchModal = false;
        $this->sendAlert('success', 'Added to cart');
    }
}
