<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;

class SX
{
    private $endpoint;

    private $web_endpoint;

    private $auth_endpoint;

    private $client_id;

    private $client_secret;

    private $username;

    private $password;

    private $grant_type;

    public function __construct()
    {
        $this->endpoint = config('sx.endpoint');

        $this->web_endpoint = config('sx.web_endpoint');

        $this->auth_endpoint = config('sx.auth_endpoint');

        $this->client_id = config('sx.client_id');

        $this->client_secret = config('sx.client_secret');

        $this->username = config('sx.username');

        $this->password = config('sx.password');

        $this->grant_type = config('sx.grant_type');
    }

    private function token()
    {
        $request = Http::asForm()->post($this->auth_endpoint, [
            'grant_type' => $this->grant_type,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'password' => $this->password,
            'username' => $this->username,
        ]);

        if ($request->ok()) {
            return $request['access_token'];
        }
    }

    public function create_order($request)
    {
        $response = Http::withToken($this->token())
            ->acceptJson()
            ->withBody(json_encode($request), 'application/json')
            ->post($this->endpoint.'/sxapioefullordermntv6');

        if ($response->ok()) {
            $response_body = json_decode($response->body());

            $order_id = $response_body->response->sxt_func_ack->sxt_func_ack[0]->data1;

            if (empty($order_id)) {
                return [
                    'status' => 'failure',
                    'message' => $response_body->response->sxt_func_ack->sxt_func_ack[0]->msg,
                ];
            }

            return [
                'status' => 'success',
                'order_id' => $response_body->response->sxt_func_ack->sxt_func_ack[0]->data1,
            ];

        }

        if ($response->badRequest()) {
            $response_body = json_decode($response->body());

            return [
                'status' => 'error',
                'message' => $response_body->response->cErrorMessage,
            ];

        }

    }

    public function cancel_order($order_number, $order_suffix, $reason)
    {

        $request = [
            'request' => [
                'companyNumber' => 10,
                'operatorInit' => 'web',
                'operatorPassword' => '',
                'orderNumber' => $order_number,
                'orderSuffix' => $order_suffix,
                "deleteOrderFlag" => false,
                "lostBusinessReason" => $reason
            ]

        ];

        $response = Http::withToken($this->token())
            ->acceptJson()
            ->withBody(json_encode($request), 'application/json')
            ->post($this->endpoint.'/sxapioeorderdeleteorcancel');

            if ($response->ok()) {
                $response_body = json_decode($response->body());
                $message = $response_body->response->cErrorMessage;

                if(empty($message))
                {
                    return [
                        'status' => 'success',
                    ];
         
                }
                else{
                    return [
                        'status' => 'error',
                        'message' => $message
                    ];

                }
            }

            return [
                'status' => 'error',
            ];
    
    }

    public function check_credit_status($request)
    {
        $response = Http::withToken($this->token())
            ->acceptJson()
            ->withBody(json_encode($request), 'application/json')
            ->post($this->endpoint.'/sxapiargetcustomerdatacreditmess');

        if ($response->ok()) {
            $response_body = json_decode($response->body());

            return [
                'status' => 'success',
                'message' => $response_body->response->cMessage,
            ];

        }
    }

    public function create_order_note($note,$order_number)
    {
        $request = [
            'request' => [
                "companyNumber" => 10,
                "operatorInit" => "web",
                "operatorPassword" => "",
                "notesTable" => 'oeeh',
                "primaryKey" => $order_number,
                "secondaryKey" => "",
                "tInnotes" => [
                    "t-innotes" => [
                        "notestype" => "o",
                        "pageno" => 0,
                        "primarykey" => $order_number,
                        "secondarykey" => "",
                        "newrecordfl" => true,
                        "newrecordglobalfl" => true,
                        "deleterecordfl" => false,
                        "changerecordfl" => false,
                        "forcerefreshallpagesfl" => false,
                        "securefl" => false,
                        "notedata" => $note,
                        "printfl" => false,
                        "printfl2" => false,
                        "printfl3" => false,
                        "printfl4" => false,
                        "printfl5" => false,
                        "requirefl" => false,
                        "extradata" => ""
                    ]
                ]
            ]
        ];
        $response = Http::withToken($this->token())
            ->acceptJson()
            ->withBody(json_encode($request), 'application/json')
            ->post($this->endpoint.'/sxapisanotechange');

        if ($response->ok()) {
            $response_body = json_decode($response->body());

            return [
                'status' => 'success',
            ];

        }

        if ($response->badRequest()) {
            $response_body = json_decode($response->body());

            return [
                'status' => 'error',
                'message' => $response_body->response->cErrorMessage,
            ];

        }

    }


    public function get_notes($request)
    {
        $response = Http::withToken($this->token())
            ->acceptJson()
            ->withBody(json_encode($request), 'application/json')
            ->post($this->endpoint.'/sxapisagetnoteslist');

        if ($response->ok()) {
            $response_body = json_decode($response->body());

            return [
                'status' => 'success',
                'notes' => json_decode(json_encode($response_body->response->tNotes), true),
            ];

        }
    }

    public function create_customer($request)
    {
        $response = Http::withToken($this->token())
            ->acceptJson()
            ->withBody(json_encode($request), 'application/json')
            ->post($this->endpoint.'/sxapiarcustomermnt');

        if ($response->ok()) {
            $response_body = json_decode($response->body());

            $return_data = $response_body->response->returnData;
            preg_match_all('#(\d{4,})#', $return_data , $matches);

            $sx_customer_number = $matches[0][0];

            if (empty($sx_customer_number)) {
                return [
                    'status' => 'failure',
                    'message' => $response_body->response->sxt_func_ack->sxt_func_ack[0]->msg,
                ];
            }

            return [
                'status' => 'success',
                'sx_customer_number' => $sx_customer_number,
            ];

        }

        if ($response->badRequest()) {
            $response_body = json_decode($response->body());

            return [
                'status' => 'error',
                'message' => $response_body->response->cErrorMessage,
            ];

        }

    }

    public function get_product($request)
    {
        if(config('sx.mock')) return $this->mock(__FUNCTION__, $request);
        
        $response = Http::withToken($this->token())
            ->acceptJson()
            ->withBody(json_encode($request), 'application/json')
            ->post($this->endpoint.'/sxapisrgetwhseproductdata');

        if ($response->ok()) {
            $response_body = json_decode($response->body());

            $return_data = $response_body->response;

            $error_message = $return_data->cErrorMessage;

            if(!empty($error_message)){
                return [
                    'status' => 'error',
                    'message' => 'Product not found',
                ];
            }

            $key_var = 't-srprodwhsedata';

            $product_name = $return_data->tSrprodwhsedata->$key_var[0]->descrip1.' '.$return_data->tSrprodwhsedata->$key_var[0]->descrip2.' ('.$return_data->tSrprodwhsedata->$key_var[0]->prod.')';
            $look_up_name = $return_data->tSrprodwhsedata->$key_var[0]->prodcat;
            $category = $return_data->tSrprodwhsedata->$key_var[0]->prodcat;
            $price =  $return_data->tSrprodwhsedata->$key_var[0]->sellprice;
            $stock = $return_data->tSrprodwhsedata->$key_var[0]->netavail;
            $prodline = $return_data->tSrprodwhsedata->$key_var[0]->prodline;
            $bin_location = $return_data->tSrprodwhsedata->$key_var[0]->binloc1;
            $product_code = $return_data->tSrprodwhsedata->$key_var[0]->prod;
            $unit_of_measure = strtoupper($return_data->tSrprodwhsedata->$key_var[0]->unitstock);

            return [
                'status' => 'success',
                'product_name' => $product_name,
                'look_up_name' => $look_up_name,
                'category' => $category,
                'price' => $price,
                'stock' => $stock,
                'prodline' => $prodline,
                'bin_location' => $bin_location,
                'product_code' => $product_code,
                'unit_of_measure' => $unit_of_measure
            ];

        }

        if ($response->badRequest()) {
            
            $response_body = json_decode($response->body());

            return [
                'status' => 'error',
                'message' => $response_body->response->cErrorMessage,
            ];

        }

    }

    public function search_product($request)
    {
        if(config('sx.mock')) return $this->mock(__FUNCTION__, $request);
        
        $response = Http::withToken($this->token())
            ->acceptJson()
            ->withBody(json_encode($request), 'application/json')
            ->post($this->endpoint.'/sxapiicgetwhseproductlistv3');

        if ($response->ok()) {
            $response_body = json_decode($response->body());

            $return_data = $response_body->response;
            $error_message = $return_data->cErrorMessage;

            if(!empty($error_message)){
                return [
                    'status' => 'error',
                    'message' => 'Product not found',
                ];
            }

            $key_var = 't-srprodwhsedata';

            $product_name = $return_data->tSrprodwhsedata->$key_var[0]->descrip1.' '.$return_data->tSrprodwhsedata->$key_var[0]->descrip2.' ('.$return_data->tSrprodwhsedata->$key_var[0]->prod.')';
            $look_up_name = $return_data->tSrprodwhsedata->$key_var[0]->prodcat;
            $category = $return_data->tSrprodwhsedata->$key_var[0]->prodcat;
            $price =  $return_data->tSrprodwhsedata->$key_var[0]->sellprice;
            $stock = $return_data->tSrprodwhsedata->$key_var[0]->netavail;
            $prodline = $return_data->tSrprodwhsedata->$key_var[0]->prodline;
            $bin_location = $return_data->tSrprodwhsedata->$key_var[0]->binloc1;
            $product_code = $return_data->tSrprodwhsedata->$key_var[0]->prod;

            return [
                'status' => 'success',
                'product_name' => $product_name,
                'look_up_name' => $look_up_name,
                'category' => $category,
                'price' => $price,
                'stock' => $stock,
                'prodline' => $prodline,
                'bin_location' => $bin_location,
                'product_code' => $product_code
            ];

        }

        if ($response->badRequest()) {
            
            $response_body = json_decode($response->body());

            return [
                'status' => 'error',
                'message' => $response_body->response->cErrorMessage,
            ];

        }

    }

    //get total invoice price with tax

    public function get_total_invoice_data($request)
    {
        if(config('sx.mock')) return $this->mock(__FUNCTION__, $request);

        $line_items = [];
        foreach($request['cart'] as $item) {
            $itemData = [
                "itemnumber" => $item['product_code'],
                "orderqty" => $item['quantity'],
                "unitofmeasure" => $item['unit_of_measure'] ?? 'EA',
                "warehouseid" => $request['warehouse'] ?? 'utic',
            ];

            if (!empty($item['price_overridden'])) {
                $itemData['actualsellprice'] = $item['total_price'];
                $itemData['nonstockflag'] = 'y';
            }

            $line_items[] = $itemData;
        }

        $invoice_request = [
            "request" => [
                "companyNumber" => $request['company_number'] ?? 10,
                "operatorInit" => $request['sx_operator_id'] ?? "wpa",
                "operatorPassword" => "",
                "tInputccdata" => ["t-inputccdata" => []],
                "tInputheaderdata" => [
                    "t-inputheaderdata" => [
                        [
                            "customerid" => "0010". $request['sx_customer_number'],
                            "warehouseid" => $request['warehouse'] ?? 'utic',
                            "webtransactiontype" => $request['web_transaction_type'] ?? 'tsf',
                        ],
                    ],
                ],
                "tInputlinedata" => [
                    "t-inputlinedata" => $line_items,
                ],
                "tInputheaderextradata" => ["t-inputheaderextradata" => []],
                "tInputlineextradata" => ["t-inputlineextradata" => []],
                "tInfieldvalue" => ["t-infieldvalue" => []],
            ],
        ];


        $response = Http::withToken($this->token())
            ->acceptJson()
            ->withBody(json_encode($invoice_request), 'application/json')
            ->post($this->endpoint.'/sxapisfoeordertotloadv4');

        if ($response->ok()) {
            $response_body = json_decode($response->body());

            $return_data = $response_body->response;
            $key_var = 't-ordtotdata';

            return [
                'status' => 'success',
                'total_tax' => $return_data->tOrdtotdata->$key_var[0]->tottaxamt,
                'total_line_amount' => $return_data->tOrdtotdata->$key_var[0]->totlineamt,
                'total_invoice_amount' => $return_data->tOrdtotdata->$key_var[0]->totinvamt,
            ];
        }
    }

    public function get_available_units($product_code, $cono)
    {
        if(config('sx.mock')) return $this->mock(__FUNCTION__, []);

        $request = [
            'request' => [
                "companyNumber" => $cono,
                'productCode' => $product_code,
                "operatorInit" => "wpa",
                "operatorPassword" => ""
            ]
        ];

        $response = Http::withToken($this->token())
            ->acceptJson()
            ->withBody(json_encode($request), 'application/json')
            ->post($this->endpoint.'/sxapiicgetproductunitofmeasurelist');

            if ($response->ok()) {
                $data = [];
                $response_body = json_decode($response->body());
    
                $return_data = $response_body->response;
                $key_var = 't-prod-uom';
                foreach($return_data->tProdUom->$key_var as $unit)
                {
                    $data[] = ['label' => $unit->descrip, 'value' => $unit->units, 'whole_value' => $unit->wholevalueunits];
                }

                return [
                    'status' => 'success',
                    'data' => $data,
                ];
    
            }
    }

    public function warehouse_transfer($request)
    {
        $response = Http::withToken($this->token())
        ->acceptJson()
        ->withBody(json_encode($request), 'application/json')
        ->post($this->endpoint.'/sxapiwttransferordermnt');

        if ($response->ok()) {
            $data = [];
            $response_body = json_decode($response->body());

            $return_data = $response_body->response;
            
            return [
                'status' => 'success',
                'wt_number' => $response_body->response->createdWarehouseTransferNumber,
            ];

        }

    }

    public function tie_warehouse_transfer($request)
    {
        $response = Http::withToken($this->token())
        ->acceptJson()
        ->withBody(json_encode($request), 'application/json')
        ->post($this->web_endpoint.'/sxapiSASubmitReportV2');

        if ($response->ok()) {
            $data = [];
            $response_body = json_decode($response->body());

            $return_data = $response_body->response;
            
            return [
                'status' => 'success',
            ];

        }

    }


    public function receive_po($request)
    {
        $response = Http::withToken($this->token())
        ->acceptJson()
        ->timeout(60)
        ->withBody(json_encode($request), 'application/json')
        ->post($this->web_endpoint.'/sxapisrreceivepo');

        if ($response->ok()) {
            $data = [];
            $response_body = json_decode($response->body());

            $return_data = $response_body->response;
            
            return [
                'status' => 'success',
            ];

        }
    }




    public function mock($function, $request)
    {
        $faker = \Faker\Factory::create();

        //add delay
        sleep(2);

        if($function == 'get_product')
        {
            return [
                'status' => $faker->randomElement(['success', 'error']),
                'product_name' => $faker->word().' '.$faker->word().' ('.$faker->word().')',
                'look_up_name' => $faker->word(),
                'category' => $faker->word(),
                'price' => $faker->randomFloat(2),
                'stock' => $faker->randomDigit(),
                'prodline' => $faker->word(),
                'bin_location' => $faker->word(),
                'product_code' => $faker->word(),
                'unit_of_measure' => 'EA'
            ];
        }

        if($function == 'create_order')
        {
            return [
                'status' => $faker->randomElement(['success']),
                'order_id' => $faker->randomNumber(7, true)
            ];
        }

        if($function == 'create_customer')
        {
            return [
                'status' => $faker->randomElement(['success']),
                'sx_customer_number' => $faker->randomNumber(7, true)
            ];
        }

        if($function == 'get_total_invoice_data')
        {
            return [
                'status' => 'success',
                'total_tax' => $faker->randomFloat(2,1,10),
                'total_line_amount' => $faker->randomFloat(2,20,100),
                'total_invoice_amount' => $faker->randomFloat(2,30,110),
            ];

        }

        if($function == 'get_available_units')
        {
            return [
                'status' => 'success',
                'data' => [
                    ["label" => "BOTTLE","value" => "BTL","whole_value" => 1.0],
                    ["label" => "CASE OF 12","value" => "CASE","whole_value" => 12.0] 
                ]
            ];

        }


    }
}
