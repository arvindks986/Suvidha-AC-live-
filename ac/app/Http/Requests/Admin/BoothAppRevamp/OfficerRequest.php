<?php namespace App\Http\Requests\Admin\BoothAppRevamp;

use Illuminate\Foundation\Http\FormRequest;

class OfficerRequest extends FormRequest
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
        $role_id = 0;
        if(\Request::has('role_id')){
            $role_id = \Request::post('role_id');
        }
        if(\Request::has('id')){
          $id = decrypt_string(\Request::post('id'));
        }
        if($role_id == '34'){
            $rules = [
                  'name'   => 'required',
                  'status' => 'required|in:1,0',
                  'role_id' => 'required|in:33,34,35',
                  'pin'              => 'required_if:role_id,34|confirmed|pin',
                  'pin_confirmation' => 'required_if:role_id,34|pin',
                  'ps_no'            => 'required'
            ];
        }else{
            $rules = [
                  'name'   => 'required',
                  'status' => 'required|in:1,0',
                  'role_id' => 'required|in:33,34,35',
                  'ps_no'            => 'required'
            ];
        }
        if(isset($id)){
          $rules['mobile'] = 'required|mobile|unique:polling_station_officer,mobile_number,'.$id;
        }else{
          $rules['mobile'] = 'required|mobile|unique:polling_station_officer,mobile_number';
        }
         
        return $rules;
        
    }
    
    public function messages()
    {
        return [
            'confirmed'     =>  "The pin and confirm pin are not matching",
            'pin'           => "Please enter a valid 4 digit pin.",
            'role_id'       => "Please select a role",
            'status'        => "Pin value status",
            'pin.required_if'   => 'The pin field is required when role is PO',
            'pin_confirmation.required_if'   => 'The pin confirmation field is required when role is PO',
            'mobile' => 'please enter valid 10 digit mobile number'
        ];
    }
}
