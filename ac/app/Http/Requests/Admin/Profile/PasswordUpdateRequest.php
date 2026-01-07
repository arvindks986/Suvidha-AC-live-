<?php namespace App\Http\Requests\Admin\Profile;

use Illuminate\Foundation\Http\FormRequest;
use DB,Session,Auth;
class PasswordUpdateRequest extends FormRequest
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
		$user_details = DB::table('officer_login')->where('id',Auth::user()->id)->first();
		if($user_details->pass_flag == '1'){
			return [
				'old_password'          => 'required',
				'password'              => 'required|confirmed',
				'password_confirmation' => 'required'

			];
		}else{
			return [
				'password'              => 'required|confirmed',
				'password_confirmation' => 'required'

			];
		}
    }
    
    public function messages()
    {
        return [
            'confirmed'    =>  "The password and confirm password are not matching",
        ];
    }
}
