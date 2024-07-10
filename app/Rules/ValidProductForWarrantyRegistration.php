<?php

namespace App\Rules;

use App\Models\Equipment\Warranty\BrandConfigurator\BrandWarranty;
use App\Models\Product\Brand;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class ValidProductForWarrantyRegistration implements ValidationRule
{
    public $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(config('sx.mock')) {
            $value = mt_rand(0,1);
            if(!$value) $fail('The product/serial could not be found');
        } else{
            $brand = Brand::whereRaw('LOWER(name) = ?', [strtolower($this->data['brand'])])->first();
            if(is_null($brand)) $fail('The brand could not be found');
            $brand_config = BrandWarranty::where('brand_id', $brand->id)->first();
            if(empty($brand_config)) $fail('The brand config could not be found');

            if(!empty($brand_config))
            {
                $icses = DB::connection('sx')->select("select serialno from pub.icses where cono = ? and LOWER(prod) = ? and LOWER(serialno) = ? and whseto = ? and currstatus = ? with (nolock)", [10, strtolower($brand_config->prefix.$this->data['model']), strtolower($this->data['serial']), '', 's']);
                if(count($icses) != 1) $fail('The product/serial could not be found');
            }

        }
    }
}
