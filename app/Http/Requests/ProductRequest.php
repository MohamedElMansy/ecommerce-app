<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'ar_name'=>'required|max:50',
            'en_name'=>'required|max:50',
            'description'=>'required',
            'price'=>'required|numeric',
            'store_id'=>'required'

        ];
    }
}
