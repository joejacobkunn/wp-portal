<?php

namespace App\Http\Livewire\POS;

use App\Classes\SX;
use App\Helpers\StringHelper;
use App\Models\Core\Customer;
use App\Http\Livewire\Component\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use LivewireAlert;

    public $activeTab = 1;
    public $productSearchModal = false;

    public $productQuery = '';
    public $productResult;
    public $cart = [];

    public $customerSearchModal = false;
    public $waiveCustomerInfo = 0;
    public $customerQuery = '';
    public $lastCustomerQuery = '';
    public $customerResult;
    public $customerResultSelected;
    public $customerSelected = []; //for confirmation
    public $newCustomerModal = false;

    public $priceModel = [];
    public $priceDetails = [];
    public $netPrice;
    public $priceBreakdownModal = false;

    protected $listeners = [
        'closeProductSearch',
        'closeCustomerSearch',
        'closeNewCustomer',
        'closeBreakdownModal',
        'customer:created' => 'newCustomerCreated',
        'customer:form:cancel' => 'closeNewCustomer'
    ];

    public function render()
    {
        return $this->renderView('livewire.pos.index');
    }

    public function getCustomerPanelHintProperty()
    {
        if (!empty($this->waiveCustomerInfo)) {
            return '<span class="badge bg-light-warning"><i class="fas fa-user-slash"></i> <strong>Waived Customer Information</strong></span>';
        }

        return !empty($this->customerSelected) ? '<span class="badge bg-light-success">'.$this->customerSelected['name'].' (#'.$this->customerSelected['sx_customer_number'].')</span>' : '<span class="text-danger">'. (count($this->cart) ? '* Fill in customer information' : '* add products in cart') .'</span>';
    }
    

    public function searchProduct()
    {
        $this->resetValidation();
        $this->resetErrorBag();

        if (!$this->productQuery) {
            return $this->addError('productQuery', 'Enter Product Code.' );
        }

        $searchResponse = $this->getproductData($this->productQuery);
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

        if (! isset($this->customerSelected['sx_customer_number'])) {
        }

        $this->productSearchModal = true;
    }

    public function getproductData($productCode)
    {
        $pricingRequest = [
            'request' => [
                "companyNumber" => 10,
                "operatorInit" => "wpa",
                "operatorPassword" =>  "",
                "productCode" => $productCode,
                "customerNumber" => $this->customerSelected['sx_customer_number'] ?? 1,
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

        if ($searchResponse['status'] == 'success') {
            $this->priceModel[$productCode][$this->customerSelected['sx_customer_number'] ?? 1] = $searchResponse['price'];
        }

        return $searchResponse;
    }

    public function updateQuantity($qty, $productCode = null)
    {
        //skip empty requests
        if ($productCode && !isset($this->cart[$productCode])) {
            return;
        }

        $product = $productCode ? $this->cart[$productCode] : $this->productResult;

        $product['quantity'] = $product['quantity'] + ((int) $qty);

        if ($product['quantity'] < 1) {
            //if empty remove item from cart
            if ($productCode) {
                unset($this->cart[$productCode]);
                $this->preparePriceData();
                return;
            }

            $product['quantity'] = 1;
        }

        if ($product['quantity'] > $product['stock']) {
            $product['quantity'] = $product['stock'];
        }

        if ($productCode) {
            $this->cart[$productCode]['quantity'] = $product['quantity'];
            $this->preparePriceData();
        } else {
            $this->productResult['quantity'] = $product['quantity'];
        }
    }

    public function addToCart()
    {
        $this->cart[$this->productResult['product_code']] = $this->productResult;
        $this->productSearchModal = false;
        $this->preparePriceData();
        $this->alert('success', 'Added to cart');
    }

    public function searchCustomer()
    {
        $this->resetValidation();
        $this->resetErrorBag();

        if (!$this->customerQuery) {    
            return $this->addError('customerQuery', 'Enter customer info to search.' );
        }

        $searchTerm = trim($this->customerQuery);
        $customerQuery = Customer::where('account_id', account()->id)
            ->where('sx_customer_number', '!=', 1)
            ->orderBy('updated_at', 'desc')
            ->select(
                'id',
                'sx_customer_number',
                'name',
                'customer_type',
                'phone',
                'email',
                'address',
                'address2',
                'city',
                'state',
                'zip',
                'is_active',
                'updated_at'
            );

            if (preg_match('/^\d{10}$/', $searchTerm) || preg_match('/^\(\d{3}\) \d{3}-\d{4}$/', $searchTerm)) {
                $formattedSearchTerm = StringHelper::extractDigits($searchTerm);
                //check for phone number
                $customerQuery->where(function ($subQuery) use ($formattedSearchTerm) {
                    $subQuery->where('phone', $formattedSearchTerm);
                    $subQuery->orWhere('sx_customer_number', $formattedSearchTerm);
                });
            } elseif (preg_match('/^\d+$/', $searchTerm)) {
                //if all numbers search for sx #
                $customerQuery->where('sx_customer_number', $searchTerm);
            } elseif (preg_match('/^[a-zA-Z-\s]+$/', $searchTerm)) {
                //if all are alphabets search for sx #
                $customerQuery->where('name', $searchTerm);
            } elseif (preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $searchTerm)) {
                //check for email match
                $customerQuery->where('email', $searchTerm);
            } else {
                $customerQuery->where('address', $searchTerm);
            }

        $this->customerResult = $customerQuery->limit(20)->get()
            ->keyBy('id')
            ->map(function ($item) {
                $item->full_address = $item->getFullAddress();
                return $item;
            })
            ->toArray();

        if (! count($this->customerResult)) {    
            return $this->addError('customerQuery', 'Customer not found' );
        }

        $this->customerResultSelected = current($this->customerResult);
        $this->lastCustomerQuery = $this->customerQuery;
        $this->reset('customerQuery', 'customerSelected');

        $this->customerSearchModal = true;   
    }
    
    public function selectTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function selectCustomer($id)
    {
        $this->customerResultSelected = $this->customerResult[$id];
    }

    public function proceedToPayment()
    {
        $this->customerSelected = $this->customerResultSelected;
        $this->activeTab = 3;
        $this->preparePriceData();
        $this->customerSearchModal = false;
    }

    public function updatedWaiveCustomerInfo()
    {
        $this->resetValidation();
        $this->resetErrorBag();
        $this->resetCustomerSelection();

        if (empty($this->waiveCustomerInfo)) {
            $this->customerSelected = [];
            $this->activeTab = 2;
        } else {
            $this->customerSelected = [];
            $this->activeTab = 3;
        }

        $this->preparePriceData();
    }

    public function resetCustomerSelection()
    {
        $this->reset(
            'customerQuery',
            'customerSelected',
            'customerResultSelected',
            'customerResult'
        );
        $this->preparePriceData();
    }

    public function closeProductSearch()
    {
        $this->reset(
            'productQuery',
            'productResult',
            'productSearchModal',
        );
    }

    public function closeCustomerSearch()
    {
        $this->reset(
            'customerQuery',
            'customerSelected',
            'customerResultSelected',
            'customerResult',
            'customerSearchModal',
        );
    }

    public function closeNewCustomer()
    {
        $this->newCustomerModal = false;
    }

    public function newCustomer()
    {
        $this->newCustomerModal = true;
    }

    public function newCustomerCreated($customerId)
    {
        $customer = Customer::where('account_id', account()->id)
            ->select(
                'id',
                'sx_customer_number',
                'name',
                'customer_type',
                'phone',
                'email',
                'address',
                'address2',
                'city',
                'state',
                'zip',
                'is_active',
                'updated_at'
            )
            ->find($customerId);

        $this->customerResultSelected = $customer->toArray();
        $this->customerResultSelected['full_address'] = $customer->getFullAddress();
        $this->closeNewCustomer();
        $this->proceedToPayment();

        $this->alert('success', 'Created customer');
    }

    public function preparePriceData()
    {
        $this->netPrice = 0;
        $customerId = !empty($this->customerSelected) ? $this->customerSelected['sx_customer_number'] : 1;
        
        foreach ($this->cart as $index => $item) {

            if (! isset($this->priceModel[$item['product_code']][$customerId])) {
                $priceData = $this->getproductData($item['product_code']);
                $price = $priceData['price'];
            } else {
                $price = $this->priceModel[$item['product_code']][$customerId];
            }

            $this->cart[$index]['price'] = $price;
            $this->cart[$index]['total_price'] = $item['quantity'] * $price;

            $this->netPrice +=  $this->cart[$index]['total_price'];
        }
    }
    
    public function showPriceBreakdown()
    {
        $this->priceBreakdownModal = true;    
    }

    public function closeBreakdownModal()
    {
        $this->priceBreakdownModal = false;    
    }

    public function cc_sale_data()
    {
        return [
            'checkin_date' => date('Y-m-d'),
            'checkout_date' => date('Y-m-d'),
            'clerk_number' => auth()->user()->sx_operator_id,
            'custom_data' => [
                'sx_order_id' => '',
                'location' => auth()->user()->location()->location,
                'portal_user_id' => auth()->user()->id
            ],
            'customer_id' => $sx_customer_number,
            'terminal_id' => $terminal_id,
            'location_id' => auth()->user()->location()->fortis_location_id
        ];
    }
}
