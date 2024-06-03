<?php

namespace App\Http\Livewire\POS;

use App\Classes\SX;
use App\Classes\Fortis;
use App\Models\Product\Line;
use App\Helpers\StringHelper;
use App\Models\Core\Customer;
use App\Models\Core\Warehouse;
use App\Models\Product\Product;
use Illuminate\Support\Facades\Cache;
use App\Http\Livewire\Component\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use LivewireAlert;

    public $account;

    public $activeTab = 1;

    public $productSearchModal = false;
    public $loadingCart = false;
    public $cart = [];
    public $warehouseDropdown = false;
    public $warehouses = [];
    public $selectedWareHouses = [];

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
    public $netTax;
    public $priceBreakdownModal = false;
    public $paymentMethod = 'cash';
    public $terminals = [];
    public $selectedTerminal;
    public $orderStatus;
    public $orderData = [];

    public $webTransactionType = 'tsf';
    public $productQuery;
    public $priceUpdateModal = false;
    public $priceUpdateData = [
        'index' => null,
        'value' => null,
        'current_price' => null,
        'product_code' => null,
        'reason' => null,
    ];
    public $nonQtyProductBrands = [
        'labor',
    ];
    public $excemptedProductLines = [];
    public $couponProductLines = [
        'CP-P',
        'CP-E',
    ];
    public $excemptedProductLineIds;

    public $measureUpdateModal = false;
    public $measureUpdateData = [
        'index' => null,
        'value' => null,
        'current_measure' => null,
        'product_code' => null,
        'options' => [],
    ];
    public $deliveryMethod = null;
    public $shippingOptionSelected = null;
    public $shippingOptions = [
        'U11' => 'U11 - UPS Ground',
        'U02' => 'U02 - UPS AirSaver',
        'U01' => 'U01 - UPS Next Day',
        'FEDX' => 'FEDX - FedEx Ground',
        'SUBR' => 'Suburban Truck',
        'WEIN' => 'Weingartz Truck',
    ];
    public $selectedContactMethod = 'SMS';
    public $contactMethodValue = '';
    public $collectedAmount;
    public $returnAmount;
    public $checkNumber;
    public $couponCode;
    public $couponProduct;
    public $couponDiscount;

    public $couponSearchModal = false;

    protected $listeners = [
        'closeProductSearch',
        'closeCustomerSearch',
        'closeNewCustomer',
        'closeBreakdownModal',
        'closePriceUpdateModal',
        'closeMeasureUpdateModal',
        'customer:created' => 'newCustomerCreated',
        'customer:form:cancel' => 'closeNewCustomer',
        'product:cart:selected' => 'ackAddToCart', //ack prod selection from table
        'pos:processAddToCart' => 'addToCart', //process prod selection adn add to cart
        'pos:addedToCart' => '$refresh',
        'unit_of_measure:updated' => 'updatedUnitOfMeasure',
        'product:coupon:selected' => 'ackCouponSelected', //ack prod selection from table
    ];

    public function mount()
    {
        $this->account = account();
        $wareHouses = Warehouse::where('cono', auth()->user()->account->sx_company_number)->get();
        $this->warehouses = $wareHouses->pluck('title', 'short')->toArray();
        $this->excemptedProductLines = array_merge($this->excemptedProductLines, $this->couponProductLines);
        $this->warehouseDropdown = false;
        $this->selectedWareHouses = $wareHouses->where('default_selected', 1)->pluck('short')->toArray();
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
        if (in_array($this->paymentMethod, ['cash', 'house_account', 'check'])) {
            return true;
        }

        return $this->paymentMethod == 'card' && count($this->terminals) && $this->selectedTerminal;
    }
    
    public function showProductSearchModal()
    {
        $this->productSearchModal = true;    
    }

    public function getproductData($productCode, $unitOfMeasure = null)
    {
        $pricingRequest = [
            'request' => [
                "companyNumber" => 10,
                "operatorInit" => auth()->user()->sx_operator_id ?? "wpa",
                "operatorPassword" =>  "",
                "productCode" => $productCode,
                "customerNumber" => $this->customerSelected['sx_customer_number'] ?? 1,
                "shipTo" => "",
                "unitOfMeasure" => !empty($unitOfMeasure) ? $unitOfMeasure : 'EA',
                "includeSellingPrice" => true,
                "warehouse" => $this->selectedWareHouses,
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

    public function getTotalInvoiceData($items)
    {
        $invoice_request = [
            'sx_operator_id' => auth()->user()->sx_operator_id,
            'sx_customer_number' => $this->customerSelected['sx_customer_number'] ?? 1,
            'warehouse' => $this->selectedWareHouses,
            'cart' => $items,
        ];

        $sx = new SX();
        return $sx->get_total_invoice_data($invoice_request);

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
        $this->dispatch('product:table:addToCart');
    }

    /**
     * Invoke event to disaply loader in frontend
     */
    public function ackAddToCart($selected)
    {
        $this->loadingCart = true;
        //$this->productSearchModal = false;
        $this->dispatch('pos:processAddToCart', $selected);
    }

    /**
     * Fetch product data and add in cart.
     */
    public function addToCart($prodId)
    {
        $product = Product::select('id', 'prod', 'unit_sell', 'brand_id')->with('brand:id,name')->find($prodId);
        $productCode = $product->prod;
        $searchResponse = $this->getproductData($productCode, $product->default_unit_sell);

        $this->cart[$productCode] = [
            'product_code' => $productCode,
            'product_name' => $searchResponse['product_name'],
            'look_up_name' => $searchResponse['look_up_name'],
            'category' => $searchResponse['category'],
            'brand_name' => strtolower((string) $product->brand?->name),
            'unit_sell' => $product->unit_sell,
            'unit_of_measure' => $product->default_unit_sell,
            'price' => $searchResponse['price'],
            'stock' => $searchResponse['stock'],
            'bin_location' => $searchResponse['bin_location'],
            'prodline' => $searchResponse['prodline'],
            'quantity' => isset($this->cart[$productCode]) ? ($this->cart[$productCode]['quantity'] + 1 <= $searchResponse['stock'] ? $this->cart[$productCode]['quantity']+1 : $searchResponse['stock']) : 1,
        ];

        $this->preparePriceData();
        $this->alert('success', 'Added to cart');
        $this->loadingCart = false;
        $this->dispatch('pos:addedToCart');
    }

    public function updatedSelectedWareHouses($shortName)
    {
        $this->warehouseDropdown = true;
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
                'updated_at',
                'preferred_contact_data'
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
                $preferredContactDetails = explode("---", $item->preferred_contact_data);
                $item->preferred_contact_type = !empty($preferredContactDetails[0]) ? $preferredContactDetails[0] : 'SMS';
                $item->preferred_contact_details = $preferredContactDetails[1] ?? '';
                return $item;
            })
            ->toArray();

        if (! count($this->customerResult)) {    
            return $this->addError('customerQuery', 'Customer not found' );
        }

        $this->selectCustomer(current($this->customerResult)['id']);
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
        $this->contactMethodValue = '';
    }

    public function proceedToPayment()
    {
        $this->customerSelected = $this->customerResultSelected;

        $this->selectedContactMethod = $this->customerSelected['preferred_contact_type'];
        $this->contactMethodValue = $this->customerSelected['preferred_contact_details'];

        if (empty($this->contactMethodValue)) {
            if ($this->selectedContactMethod == 'Email') {
                $this->contactMethodValue = empty($this->contactMethodValue) && !empty($this->customerSelected['email']) ? $this->customerSelected['email'] : $this->contactMethodValue;
            } else {
                $this->contactMethodValue = empty($this->contactMethodValue) && !empty($this->customerSelected['phone']) ? $this->customerSelected['phone'] : $this->contactMethodValue;
            }
        }

        $this->preparePriceData();
        $this->customerSearchModal = false;
    }

    public function updatedWaiveCustomerInfo()
    {
        $this->resetValidation();
        $this->resetErrorBag();
        $this->resetCustomerSelection();
        $this->customerSelected = [];

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

            //check if price is overridden
            if (isset($this->priceModel[$item['product_code']]['new_set_price'])) {
                $price = $this->priceModel[$item['product_code']]['new_set_price'];
            } elseif (! isset($this->priceModel[$item['product_code']][$customerId])) {
                $priceData = $this->getproductData($item['product_code'], $item['unit_of_measure']);
                $price = $priceData['price'];
                $this->cart[$index]['stock'] = $priceData['stock'];
            } else {
                $price = $this->priceModel[$item['product_code']][$customerId];
            }

            $this->cart[$index]['price'] = $price;
            $this->cart[$index]['total_price'] = $item['quantity'] * $price;

        }

        $apiCart = $this->cart;

        $this->couponDiscount = null;
        if (!empty($this->couponProduct)) {
            $couponSearchResponse = $this->getproductData($this->couponProduct->prod);

            if (!empty($searchResponse['status']) && $searchResponse['status'] != 'error') {
                $apiCart[$this->couponProduct->prod] =  [
                    'product_code' => $this->couponProduct->prod,
                    'product_name' => $couponSearchResponse['product_name'],
                    'look_up_name' => $couponSearchResponse['look_up_name'],
                    'category' => $couponSearchResponse['category'],
                    'brand_name' => '',
                    'unit_sell' => '',
                    'unit_of_measure' => '',
                    'price' => $couponSearchResponse['price'],
                    'stock' => $couponSearchResponse['stock'],
                    'bin_location' => $couponSearchResponse['bin_location'],
                    'prodline' => $couponSearchResponse['prodline'],
                    'quantity' => 1,
                ];
                $this->couponDiscount = $couponSearchResponse['price'];
            }
        }


        $invoice_request = [
            'sx_operator_id' => auth()->user()->sx_operator_id,
            'sx_customer_number' => $this->customerSelected['sx_customer_number'] ?? 1,
            'warehouse' => $this->selectedWareHouses,
            'cart' => $apiCart,
        ];
        
        $sx = new SX();
        $invoceData = $sx->get_total_invoice_data($invoice_request);

        $this->netTax =  $invoceData['total_tax'];
        $this->netPrice =  $invoceData['total_invoice_amount'];
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
        $this->terminals = [];

        if ($type == 'card') {
            if(!empty(auth()->user()->location()->fortis_location_id)){
                $terminalData = json_decode($fortis->fetchTerminals(auth()->user()->location()->fortis_location_id), true);
                $terminalIds = [];
                foreach ($terminalData['list'] as $index => $terminal) {
                    if ($terminal['active']) {
                        $this->terminals[$index]['id'] = $terminal['id'];
                        $this->terminals[$index]['title'] = $terminal['title'];
                        $this->terminals[$index]['location_id'] = $terminal['location_id'];
                        $this->terminals[$index]['active'] = $terminal['active'];
                        $this->terminals[$index]['is_provisioned'] = $terminal['is_provisioned'];
                        $this->terminals[$index]['available'] = $terminal['active'] && !$terminal['is_provisioned'];
                        $terminalIds[] = $terminal['id'];
        
                    }
                }
    
                $cachedTerminalPreference = Cache::get('preference.terminal.' . auth()->user()->location()->fortis_location_id);
                $this->selectedTerminal = '';
                if ($cachedTerminalPreference && in_array($cachedTerminalPreference, $terminalIds)) {
                    $this->selectedTerminal = $cachedTerminalPreference;
                }
            }
        }
    }

    public function setTerminal($terminalId)
    {
        $this->selectedTerminal = $terminalId;

        Cache::put('preference.terminal.' . auth()->user()->location()->fortis_location_id, $terminalId);
    }

    public function refreshOrder()
    {
        $this->reset();
    }

    public function proceedToOrder()
    {
        if ($this->paymentMethod == 'card') {
            $this->placeCardOrder();
        } elseif ($this->paymentMethod == 'check') {
            $this->resetValidation();
            if (!$this->checkNumber) {
                return $this->addError('checkNumber', 'Check Number Field is required.' );
            }

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
            $this->saveCustomerContactPreference();
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

    public function searchProduct()
    {
        $this->resetValidation();
        $this->resetErrorBag();

        if (!$this->productQuery) {
            return $this->addError('productQuery', 'Enter Product Code.' );
        }

        //if excempted product lines not fetched
        if ($this->excemptedProductLineIds === null) {
            $this->excemptedProductLineIds = Line::select('id')->whereIn('name', $this->excemptedProductLines)->pluck('id')->toArray();
        }

        $product = Product::select('id', 'prod', 'unit_sell', 'product_line_id')
            ->where('prod', $this->productQuery)
            ->first();

        if (is_array($this->excemptedProductLineIds) && in_array($product->product_line_id, $this->excemptedProductLineIds)) {
            return $this->addError('productQuery', 'Invalid Product Line.' );
        }

        $searchResponse = $this->getproductData($this->productQuery, $product?->default_unit_sell);
        if (empty($searchResponse['status']) || $searchResponse['status'] == 'error') {
            return $this->addError('productQuery', 'Invalid Product Code.' );
        }

        $productCode = $this->productQuery;
        $this->cart[$productCode] = [
            'product_code' => $productCode,
            'product_name' => $searchResponse['product_name'],
            'look_up_name' => $searchResponse['look_up_name'],
            'category' => $searchResponse['category'],
            'unit_sell' => !empty($product->unit_sell) ? $product->unit_sell : ["EA"],
            'unit_of_measure' => !empty($product->unit_sell) ? $product->default_unit_sell : 'EA',
            'price' => $searchResponse['price'],
            'stock' => $searchResponse['stock'],
            'bin_location' => $searchResponse['bin_location'],
            'prodline' => $searchResponse['prodline'],
            'quantity' => isset($this->cart[$productCode]) ? ($this->cart[$productCode]['quantity'] > $searchResponse['stock'] ? $searchResponse['stock'] :  $this->cart[$productCode]['quantity']) : 1,
        ];
        $this->preparePriceData();

        $this->productQuery = '';
        $this->alert('success', 'Added to cart');
        $this->productSearchModal = false;
    }

    public function showOverridePriceModal($cartIndex)
    {
        $this->priceUpdateModal = true;
        $this->priceUpdateData['index'] = $cartIndex;
        $this->priceUpdateData['value'] = null;
        $this->priceUpdateData['reason'] = null;


        $product = $this->cart[$cartIndex];
        $this->priceUpdateData['product_code'] = $cartIndex;
        $this->priceUpdateData['current_price'] = $product['price'];
    }

    public function confirmOverridePrice()
    {
        $this->resetValidation();
        
        if (!$this->priceUpdateData['value'] || !$this->priceUpdateData['reason']) {
            if (trim($this->priceUpdateData['value']) == '') {
                $this->addError('priceUpdateData.value', 'Price field is required.' );
            }
            if (trim($this->priceUpdateData['reason']) == '') {
                $this->addError('priceUpdateData.reason', 'Reason field is required.' );
            }

            return;
        }

        $productCode = $this->priceUpdateData['index'];
        $this->priceModel[$productCode]['new_set_price'] = $this->priceUpdateData['value'];
        $this->cart[$productCode]['price_overridden'] = true;
        $this->cart[$productCode]['price_overridde_reason'] = $this->priceUpdateData['reason'];
        $this->preparePriceData();
        $this->closePriceUpdateModal();
    }


    public function closePriceUpdateModal()
    {
        $this->priceUpdateModal = false;
        $this->priceUpdateData['index'] = null;
        $this->priceUpdateData['value'] = null;
        $this->priceUpdateData['product_code'] = null;
        $this->priceUpdateData['current_price'] = null;
    }

    public function showChangeUnitOfMeasure($cartIndex)
    {
        $product = $this->cart[$cartIndex];

        if (empty($product['unit_sell']) || !is_array($product['unit_sell']) || count($product['unit_sell']) < 2) {
            return;
        }

        $this->measureUpdateModal = true;
        $this->measureUpdateData['index'] = $cartIndex;
        $this->measureUpdateData['value'] = $product['unit_of_measure'];

        $this->measureUpdateData['product_code'] = $cartIndex;
        $this->measureUpdateData['options'] = $product['unit_sell'];
        $this->measureUpdateData['current_measure'] = $product['unit_of_measure'];

        $this->measureUpdateModal = true;
    }

    public function updatedUnitOfMeasure($cartIndex, $value, $recheckValidation = true)
    {
        $this->measureUpdateData['index'] = $cartIndex;
        $this->measureUpdateData['value'] = $value;
    }

    public function confirmMeasureUpdate()
    {
        $cartIndex = $this->measureUpdateData['index'];
        $name = 'cart.'. $cartIndex . '.unit_of_measure';
        $this->fieldUpdated($name, $this->measureUpdateData['value'], true);

        $prodId = $this->cart[$cartIndex]['product_code'];
        if (empty($this->cart[$cartIndex]['price_overridden'])) {
            $this->priceModel[$prodId] = [];
        }
        $this->preparePriceData();
        $this->closeMeasureUpdateModal();
    }

    public function closeMeasureUpdateModal()
    {
        $this->measureUpdateModal = false;
        $this->measureUpdateData['index'] = null;
        $this->measureUpdateData['value'] = null;
        $this->measureUpdateData['options'] = [];
        $this->measureUpdateData['product_code'] = null;
        $this->measureUpdateData['current_price'] = null;
    }

    public function updateContactMethod($contactMethod)
    {
        $this->selectedContactMethod = $contactMethod;

        if ($contactMethod == 'Email') {
            $this->contactMethodValue =  is_numeric($this->contactMethodValue) || empty($this->contactMethodValue) && !empty($this->customerSelected['email']) ? $this->customerSelected['email'] : $this->contactMethodValue;
        } else {
            $this->contactMethodValue =  strpos($this->contactMethodValue, '@') !== false || empty($this->contactMethodValue) && !empty($this->customerSelected['phone']) ? $this->customerSelected['phone'] : $this->contactMethodValue;
        }
    }

    public function updatedCollectedAmount()
    {
        if ($this->collectedAmount) {
            $this->returnAmount = $this->collectedAmount - $this->netPrice;
        } else {
            $this->returnAmount = null;
        }
    }

    public function applyCoupon()
    {
        $this->resetValidation('couponCode');
        $this->resetErrorBag('couponCode');

        $this->couponProduct = Product::join('product_lines', 'product_lines.id', '=', 'products.product_line_id')
            ->select('products.*')
            ->where('prod', $this->couponCode)
            ->first();
        
        if (!$this->couponProduct) {    
            return $this->addError('couponCode', 'Invalid coupon code.' );
        }

        $this->reset('couponCode');

        $this->alert('info', 'Coupon applied successfully!');
        $this->preparePriceData();
    }

    public function clearCoupon()
    {
        $this->reset('couponProduct', 'couponCode', 'couponDiscount');
        $this->resetErrorBag('couponCode');
        $this->preparePriceData();
    }

    private function saveCustomerContactPreference()
    {
        Customer::where('account_id', account()->id)
            ->where('sx_customer_number', '!=', 1)
            ->where('id', $this->customerSelected['id'])
            ->update([
                'preferred_contact_data' => $this->selectedContactMethod .'---'. $this->contactMethodValue
            ]);
    }

    public function getPaymentTabActivatedProperty()
    {
        return (!empty($this->customerSelected) || !empty($this->waiveCustomerInfo)) && $this->deliveryMethod && ($this->deliveryMethod != 'Shipping' || $this->shippingOptionSelected);
    }

    public function showCouponSearchModal()
    {
        $this->couponSearchModal = true;    
    }

    public function closeCouponSearch()
    {
        $this->couponSearchModal = false;    
    }
    
    /**
     * Invoke event to disaply loader in frontend
     */
    public function ackCouponSelected($prodId)
    {
        $product = Product::find($prodId);
        $this->couponCode = $product->prod;
        $this->applyCoupon();
        $this->closeCouponSearch();
    }
}
