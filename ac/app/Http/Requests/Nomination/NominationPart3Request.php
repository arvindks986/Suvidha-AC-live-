<?php

namespace App\Http\Requests\Nomination;

use Illuminate\Foundation\Http\FormRequest;
use App\models\Nomination\ProfileModel;
use Auth;
use App\models\Nomination\NominationApplicationModel;

class NominationPart3Request extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
      $recognized_party = '';
      if(isset(Auth::user()->designation) && Auth::user()->designation == 'ROAC'){
        if(\Request::has('nomination_id')){
          $user_nomination = NominationApplicationModel::get_nomination_application_ro(\Request::input('nomination_id'));
          if(!$user_nomination){
            return redirect("roac/apply-nomination-step-3");
          }
          $recognized_party = $user_nomination['recognized_party'];
        }else{
          return redirect("roac/apply-nomination-step-3");
        }
      }else{
        if(\Request::has('nomination_id')){
          $user_nomination = NominationApplicationModel::get_nomination_application(\Request::input('nomination_id'));
          if(!$user_nomination){
            return redirect("nomination/apply-nomination-step-3");
          }
          $recognized_party = $user_nomination['recognized_party'];
        }else{
          return redirect("nomination/apply-nomination-step-3");
        }
      }
      
      if($recognized_party == '1'){
        return [
            'nomination_id'     => 'required|exists:nomination_application,id',
            'age'               => 'required|integer|between:25,140',
            'party_id'          => 'required|exists:m_party,CCODE',
            'language'          => 'required|min:3|max:255',
            //'part3_cast_state'  => 'required',
           // 'part3_address'  => 'required',
            'part3_legislative_state'  => 'required',
           // 'category'          => 'required|in:sc,st,general',
            'part3_date'        => 'required|date'
        ];
      }else{
        return [
            'nomination_id'     => 'required|exists:nomination_application,id',
            'age'               => 'required|integer|between:25,140',
            'party_id'          => 'required',
            'party_id2'          => 'required|exists:m_party,CCODE',
            'suggest_symbol_1'  => 'required|min:3|max:255',
            'suggest_symbol_2'  => 'required|min:3|max:255',
            'suggest_symbol_3'  => 'required|min:3|max:255',
            'language'          => 'required|min:3|max:255',
            //'part3_cast_state'  => 'required',
            //'part3_address'  => 'required',
            'part3_legislative_state'  => 'required',
            //'category'          => 'required|in:sc,st,general',
            'part3_date'        => 'required|date'
        ];  
      }      
    }
    
    public function messages()
    {	
    $recognized_party = '';
    if(isset(Auth::user()->designation) && Auth::user()->designation == 'ROAC'){
      if(\Request::has('nomination_id')){
        $user_nomination = NominationApplicationModel::get_nomination_application_ro(\Request::input('nomination_id'));
        if(!$user_nomination){
          return redirect("roac/apply-nomination-step-3");
        }
        $recognized_party = $user_nomination['recognized_party'];
      }else{
        return redirect("roac/apply-nomination-step-3");
      }
    }else{
      if(\Request::has('nomination_id')){
        $user_nomination = NominationApplicationModel::get_nomination_application(\Request::input('nomination_id'));
        if(!$user_nomination){
          return redirect("nomination/apply-nomination-step-3");
        }
        $recognized_party = $user_nomination['recognized_party'];
      }else{
        return redirect("nomination/apply-nomination-step-3");
      }
    }
	
	   if($recognized_party == '1'){	
		return [
			'election_id.required'   =>  'Please choose a valid election type',
			'nomination_id.required'   =>  'Please choose a nomination',
			'party_id.required'   	=>  __('part3.partyerror'),
			'language.required'   	=>   __('part3.lerror'),
			'part3_address.required'   	=> __('part3.relerror'),
			'part3_legislative_state.required'   	=>    __('part3.logerror'),
			'age.between'   =>   __('part3.aerror'),
			'age.required'   =>   __('part3.aerror'),
			'part3_date.required'   =>  __('part3.derror'),
		];
	   } else {
		 return [
			'election_id.required'   =>  'Please choose a valid election type',
			'nomination_id.required'   =>  'Please choose a nomination',
			'party_id.required'   	=>   __('part3.partyerror'),
			'party_id2.required'   	=>   __('part3.partyerror'),
			'language.required'   	=>   __('part3.lerror'),
			'suggest_symbol_1.required'   	=>   __('part3.s1'),
			'suggest_symbol_2.required'   	=>   __('part3.s2'),
			'suggest_symbol_3.required'   	=>   __('part3.s3'),
			'part3_legislative_state.required'   	=>  __('part3.logerror'),
			'age.between'   =>    __('part3.aerror'),
			'age.required'   =>    __('part3.aerror'),
			'part3_date.required'   => __('part3.derror'),
		];
		   
	   }	
    }
}
