<?php

namespace App\Http\Requests\Nomination;

use Illuminate\Foundation\Http\FormRequest;
use App\models\Nomination\ProfileModel;
use Auth;

class NominationApplicationRequest extends FormRequest
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
                'st_code'         => 'required|exists:m_state,ST_CODE',
                'ac_no'           => 'required|exists:m_ac,AC_NO',
                'election_id'     => 'required|exists:m_election_details,ELECTION_TYPEID',
            ];
       
        
    }
    
    public function messages()
    {
        return [
            'election_id'   =>  "Please choose a valid election type",
            'ac_no'         => "Please choose a valid pc",
            'st_code'       => "Please choose a valid state",
        ];
    }
}
