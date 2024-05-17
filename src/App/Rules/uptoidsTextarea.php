<?php
namespace Amerhendy\Employment\App\Rules;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class uptoidsTextarea implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(\Str::isJson($value) == false){
            $fail('The :attribute must has User.');
            return;
        }
        $variable=json_decode($value,true);
        foreach ($variable as $key => $value) {
            if(!is_array($value)){$fail('The :attribute must has User.');return;}
            if(!array_key_exists('id',$value)){
                $fail('The :attribute must has User.');
            }else{
                if(!is_numeric($value['id'])){
                    $fail('The :attribute must has User.');
                }else{
                    $check=\Amerhendy\Employment\App\Models\Employment_People::find($value['id']);
                    if(empty($check)){
                        $fail('The :attribute must has User.');
                    }
                    //dd($check,$value['id']);
                }

            }
        }
    }
}
