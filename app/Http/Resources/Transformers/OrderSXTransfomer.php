<?php

namespace App\Http\Resources\Transformers;

use App\Models\Core\Account;
use App\Models\SX\DealerInfo;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderSXTransfomer extends JsonResource
{
   /**
    * Transform the resource into an array.
    *
    * @return array<string, mixed>
    */
   public function toArray(Request $request): array
   {
      $account = Account::find(app('domain')->getClientId());

      $customer_number = $this->fetch_customer_number($request->customer_number, $request->customer_type, $account);

      return [
          'request' => [
              'companyNumber' => $account->sx_company_number,
              'operatorInit' => 'wpa',
              'operatorPassword' => '',
              'sxt_orderV4' => [
                  'sxt_orderV4' => [
                      [
                          'actionType' => 'original',
                          'orderDisp' => '',
                          'poNo' => $request->order_id,
                          'refer' => $request->customer_number,
                          'shipVia' => $request->shipping['method'],
                          'slsRepIn' => '',
                          'takenBy' => 'WEB',
                          'transType' => 'SO',
                          'whse' => $request->warehouse ?? 'RICH',
                      ],
                  ],
              ],
              'sxt_customer' => [
                  'sxt_customer' => [
                      [
                          'custNo' => $customer_number,
                      ],
                  ],
              ],
              'sxt_itemV4' => [
                  'sxt_itemV4' => $this->create_items_array($request->items),
              ],
              'sxt_shipfm' => [
                  'sxt_shipfm' => [],
              ],
              'sxt_shipto' => [
                  'sxt_shipto' => [
                      [
                          'address1' => $request->shipping['address'],
                          'address2' => $request->shipping['address_2'] ?: '',
                          'city' => $request->shipping['city'],
                          'contact' => $request->shipping['first_name'].' '.$request->shipping['last_name'],
                          'countryCd' => $request->shipping['country'],
                          'name' => $request->shipping['first_name'].' '.$request->shipping['last_name'],
                          'postalCd' => $request->shipping['zip'],
                          'shipToNo' => $request->ship_to ?? $this->fetch_ship_to($account->sx_company_number, $request->customer_number, $request->customer_type),
                          'state' => $request->shipping['state'],
                      ],
                  ],
              ],
              'sxt_billto' => [
                  'sxt_billto' => [],
              ],
              'sxt_terms' => [
                  'sxt_terms' => [],
              ],
              'sxt_schedule' => [
                  'sxt_schedule' => [],
              ],
              'sxt_total' => [
                  'sxt_total' => [],
              ],
              'sxt_header_extra' => [
                  'sxt_header_extra' => [],
              ],
              'sxt_line_extra' => [
                  'sxt_line_extra' => [],
              ],
              'sxt_line_component' => [
                  'sxt_line_component' => [],
              ],
          ],
      ];
   }

   private function fetch_customer_number($customer_number, $customer_type, $account)
   {
      if ($customer_type == 'exmark') {

         //if exmark , lets query the zzarscc table and get the actual sx customer number
         return DealerInfo::select('custno')
            ->where('cono', $account->sx_company_number)
            ->where('exmarknm', $customer_number)->first()->custno;

      }

      return $customer_number;
   }

   private function fetch_ship_to($company_number, $customer_number, $customer_type)
   {
      $ship_to = '';

      if ($customer_type == 'exmark') {
         $ship_to = DealerInfo::select('shipto')
            ->where('cono', $company_number)
            ->where('exmarknm', $customer_number)->first()->shipto;
      }

      return $ship_to ?: 'RICH';
   }

   private function create_items_array($order_items)
   {
      $items = [];

      foreach ($order_items as $item) {
         $items[] = [
             'buyerProd' => $item['part_number'],
             'qtyOrd' => $item['quantity'],
             'sellerProd' => $item['part_number'],
             'upc' => $item['part_number'],
         ];
      }

      return $items;
   }
}
