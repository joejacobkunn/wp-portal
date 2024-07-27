<?php

namespace App\Rules;

use App\Models\Product\Product;
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
        $product = Product::with('line')->where('prod',$value)->first();

        if (!$product || !$product->line) {
            $fail('The selected product is invalid.');
            return;
        }

        // Check if the product is active and not labor
        if ($product->active === 'inactive' || $product->active === 'labor') {
            $fail('The selected product cannot be inactive or labor.');
            return;
        }

        $line = $product->line->name;

        if (!str_ends_with($line, '-E') && !str_ends_with($line, '-A')) {
            $fail('The selected product must be an Equipment or Accessory');
        }
    }
}
