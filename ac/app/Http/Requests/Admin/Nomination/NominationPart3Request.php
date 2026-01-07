<?php

namespace App\Http\Requests\Admin\Nomination;

use Illuminate\Foundation\Http\FormRequest;
use App\models\Admin\Nomination\NominationModel;
use Auth, Redirect, Request, Session;
use App\models\Admin\Nomination\NominationApplicationModel;

class NominationPart3Request extends FormRequest
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
      $recognized_party = '';
      if(\Request::has('nomination_id')){
        $user_nomination = NominationApplicationModel::get_nomination_application(\Request::input('nomination_id'));
        if(!$user_nomination){
          Session::flash('status',0);
          Session::flash('flash-message',"Please try again.");
          return Redirect::back();
        }
        $recognized_party = $user_nomination['recognized_party'];
      }else{
        Session::flash('status',0);
        Session::flash('flash-message',"Please try again.");
        return Redirect::back();
      }

      if($recognized_party == '1'){
        return [
            'nomination_id'     => 'required|exists:nomination_application,id',
            'age'               => 'required|integer|between:18,240',
            'party_id'          => 'required|exists:m_party,CCODE',
            'language'          => 'required|min:3|max:255',
            'category'          => 'required|in:sc,st,general',
            'part3_date'        => 'required|date'
        ];
      }else{
        return [
            'nomination_id'     => 'required|exists:nomination_application,id',
            'age'               => 'required|integer|between:18,240',
            'party_id'          => 'required|exists:m_party,CCODE',
            'suggest_symbol_1'  => 'required|min:3|max:255',
            'suggest_symbol_2'  => 'required|min:3|max:255',
            'suggest_symbol_3'  => 'required|min:3|max:255',
            'language'          => 'required|min:3|max:255',
            'category'          => 'required|in:sc,st,general',
            'part3_date'        => 'required|date'
        ];  
      }      
    }
    
    public function messages()
    {
        return [
            'election_id'   =>  'Please choose a valid election type',
            'ac_no'         => 'Please choose a valid pc',
            'st_code'       => 'Please choose a valid state',
            'age.between'   => 'The age must be greater than 18',
        ];
    }
}
