<?php

namespace App\Http\Requests\Admin\Profile;

use Illuminate\Foundation\Http\FormRequest;

class PasswordRequest extends FormRequest
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
        return [
            'password'              => 'required|confirmed|min:6',
            'password_confirmation' => 'required|min:6'

        ];
    }
    
    public function messages()
    {
        return [
            'confirmed'    =>  "The password and confirm password are not matching",
        ];
    }
}
