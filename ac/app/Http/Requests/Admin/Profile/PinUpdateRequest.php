<?php namespace App\Http\Requests\Admin\Profile;

use Illuminate\Foundation\Http\FormRequest;

class PinUpdateRequest extends FormRequest
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
            'old_pin'          => 'required|digits:6',
            'pin'              => 'required|confirmed|digits:6',
            'pin_confirmation' => 'required|digits:6'
        ];
    }
    
    public function messages()
    {
        return [
            'confirmed'    =>  "The pin and confirm pin are not matching",
            'pin'           => "Please enter a valid 6 digit pin."
        ];
    }
}
