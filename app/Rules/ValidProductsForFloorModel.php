<?php

namespace App\Rules;

use App\Models\Product\Product;
use App\Models\SX\Product as SXProduct;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidProductsForFloorModel implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(config('sx.mock'))
        {
            $product = Product::with('line')->where('prod',$value)->first();

            if (!$product || !$product->line) {
                $fail("The Selected Product doesn't exist.");
            }
    
            // Check if the product is active and not labor
            if ($product->active === 'inactive' || $product->active === 'labor') {
                $fail('The selected product is not active.');
            }
    
            $line = $product->line->name;
    
            if (!str_ends_with($line, '-E') && !str_ends_with($line, '-A')) {
                $fail('The selected product must be an Equipment or Accessory');
            }
        }else
        {
            $product = SXProduct::where('cono', 10)->where('prod', strtoupper($value))->first();

            if (!$product) {
                $fail("The selected product doesn't exist");
                return;
            }

            if(strtoupper($product->statustype) == 'I'){
                $fail('Product is inactive');
            }

            if(strtoupper($product->statustype) == 'L'){
                $fail('Product cannot be a labour service');
            }

            $meta_data = $product->getMetaData()[0];

            if (!str_ends_with($meta_data->ProdLine, '-E') && !str_ends_with($meta_data->ProdLine, '-A')) {
                $fail('The selected product must be an Equipment or Accessory');
            }



        }
    }
}
