<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
    
    public function rules()
    {
     
        if(!\Request::has('two_step') && !in_array(\Request::get('two_step'), [0,1])){
            return false;
        }

        return [
            'two_step'    => 'required|digits_between:0,1'

        ];
    }
    
    public function messages()
    {
        return [
            'two_step'    =>  "You can't leave this field as blank.",
        ];
    }
}
