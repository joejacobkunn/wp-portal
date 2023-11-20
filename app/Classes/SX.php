<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;

class SX
{
    private $endpoint;

    private $auth_endpoint;

    private $client_id;

    private $client_secret;

    private $username;

    private $password;

    private $grant_type;

    public function __construct()
    {
        $this->endpoint = config('sx.endpoint');

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
            ->post($this->endpoint.'/sxapiicgetproductdatageneralv3');

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

            $product_name = $return_data->description1.' '.$return_data->description2.' ('.$return_data->crossReferenceProduct.')';
            $look_up_name = $return_data->lookupName;
            $entered_date = $return_data->enteredDate;
            $category = $return_data->productCategory;


            return [
                'status' => 'success',
                'product_name' => $product_name,
                'look_up_name' => $look_up_name,
                'entered_date' => $entered_date,
                'category' => $category
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
                'entered_date' => $faker->date(),
                'category' => $faker->word(),
                'price' => $faker->randomFloat(2),
                'stock' => $faker->randomDigit()
            ];
        }
    }
}
