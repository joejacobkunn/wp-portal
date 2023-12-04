<?php

namespace App\Http\Livewire\POS;

use App\Classes\Fortis;
use App\Classes\SX;
use App\Helpers\StringHelper;
use App\Models\Core\Customer;
use App\Http\Livewire\Component\Component;
use App\Models\Product\Product;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use LivewireAlert;

    public $account;

    public $activeTab = 1;

    public $productSearchModal = false;
    public $loadingCart = false;
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
    public $paymentMethod = 'cash';
    public $terminals = [];
    public $selectedTerminal;
    public $orderStatus;
    public $orderData = [];

    protected $listeners = [
        'closeProductSearch',
        'closeCustomerSearch',
        'closeNewCustomer',
        'closeBreakdownModal',
        'customer:created' => 'newCustomerCreated',
        'customer:form:cancel' => 'closeNewCustomer',
        'product:cart:selected' => 'ackAddToCart', //ack prod selection from table
        'pos:processAddToCart' => 'addToCart', //process prod selection adn add to cart
    ];

    public function mount()
    {
        $this->account = account();
    }

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

    public function getIsOrderReadyProperty()
    {
        if ($this->paymentMethod == 'cash') {
            return true;
        }

        return $this->paymentMethod == 'card' && count($this->terminals) && $this->selectedTerminal;
    }
    
    public function showProductSearchModal()
    {
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

    public function updateQuantity($qty, $productCode)
    {
        //skip empty requests
        if (!isset($this->cart[$productCode])) {
            return;
        }

        $product = $this->cart[$productCode];
        $product['quantity'] = $product['quantity'] + ((int) $qty);

        if ($product['quantity'] < 1) {
            //if empty remove item from cart
            unset($this->cart[$productCode]);
            $this->preparePriceData();
            return;
        }

        if ($product['quantity'] > $product['stock']) {
            $product['quantity'] = $product['stock'];
        }

        $this->cart[$productCode]['quantity'] = $product['quantity'];
        $this->preparePriceData();
    }

    /**
     * Invoke event to fetch selected products
     */
    public function invokeAddToCart()
    {
        $this->loadingCart = true;
        $this->emit('product:table:addToCart');
    }

    /**
     * Invoke event to disaply loader in frontend
     */
    public function ackAddToCart($selected)
    {
        $this->productSearchModal = false;
        $this->emit('pos:processAddToCart', $selected);
    }

    /**
     * Fetch product data and add in cart.
     */
    public function addToCart($selected)
    {
        $productsSelected = Product::select('id', 'prod')->whereIn('id', $selected)->get();
        foreach ($productsSelected as $product) {
            $productCode = $product->prod;
            $searchResponse = $this->getproductData($productCode);

            $this->cart[$productCode] = [
                'product_code' => $productCode,
                'product_name' => $searchResponse['product_name'],
                'look_up_name' => $searchResponse['look_up_name'],
                'category' => $searchResponse['category'],
                'price' => $searchResponse['price'],
                'stock' => $searchResponse['stock'],
                'bin_location' => $searchResponse['bin_location'],
                'prodline' => $searchResponse['prodline'],
                'quantity' => isset($this->cart[$productCode]) ? ($this->cart[$productCode]['quantity'] > $searchResponse['stock'] ? $searchResponse['stock'] :  $this->cart[$productCode]['quantity']) : 1,
            ];
        }

        $this->preparePriceData();
        $this->alert('success', 'Added to cart');
        $this->loadingCart = false;
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
        $this->preparePriceData();

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

    public function setPaymentMethod($type)
    {
        $this->paymentMethod = $type;

        $fortis = app()->make(Fortis::class);
        if ($type == 'card') {
            $terminalData = json_decode($fortis->fetchTerminals(auth()->user()->location()->fortis_location_id), true);

            $this->terminals = [];
            foreach ($terminalData['list'] as $index => $terminal) {
                if ($terminal['active'])
                $this->terminals[$index]['id'] = $terminal['id'];
                $this->terminals[$index]['title'] = $terminal['title'];
                $this->terminals[$index]['location_id'] = $terminal['location_id'];
                $this->terminals[$index]['active'] = $terminal['active'];
                $this->terminals[$index]['is_provisioned'] = $terminal['is_provisioned'];
                $this->terminals[$index]['available'] = $terminal['active'] && !$terminal['is_provisioned'];
            }
        }
    }

    public function setTerminal($terminalId)
    {
        $this->selectedTerminal = $terminalId;
    }

    public function refreshOrder()
    {
        $this->reset();
    }

    public function proceedToOrder()
    {
        if ($this->paymentMethod == 'card') {
            $this->placeCardOrder();
        } else {
            $this->placeCashOrder();
        }
    }

    private function getOrderData()
    {
        $data = [
            'checkin_date' => date('Y-m-d'),
            'checkout_date' => date('Y-m-d'),
            'clerk_number' => auth()->user()->sx_operator_id,
            'custom_data' => [
                'sx_order_id' => '',
                'portal_user_id' => auth()->user()->id
            ],
            'location_id' => auth()->user()->location()->fortis_location_id,
            'transaction_amount' => $this->netPrice * 100,
        ];

        if (!$this->waiveCustomerInfo && $this->customerResultSelected) {
            $data['customer_id'] = (string) $this->customerResultSelected['sx_customer_number'];
        }

        return $data;
    }
    
    public function placeCashOrder()
    {
        $orderData = $this->getOrderData();
        $fortis = app()->make(Fortis::class);
        $transaction = $fortis->cashSale($orderData);
        
        if (isset($transaction['data']['id'])) {
            $this->orderStatus = 'completed';
            $this->orderData = $transaction['data'];
            $this->alert('success', 'Order successfully placed!');
        }
    }

    public function placeCardOrder()
    {
        $orderData = $this->getOrderData();
        $orderData['terminal_id'] = (string) $this->selectedTerminal;

        $fortis = app()->make(Fortis::class);
        $transaction = $fortis->terminalSale($orderData);
        $orderData = $fortis->transactionStatus($transaction['data']['async']['code']);

        if ($orderData['data']['id'] && $orderData['data']['progress'] == 100) {
            $this->orderStatus = 'completed';
            $this->orderData = $orderData['data'];
            $this->alert('success', 'Order successfully placed!');
        }
    }
}
