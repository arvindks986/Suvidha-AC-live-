<?php

namespace App\Http\Requests\Nomination;

use Illuminate\Foundation\Http\FormRequest;
use App\models\Nomination\ProfileModel;
use Auth;
use App\models\Nomination\NominationApplicationModel;

class NominationPart3aRequest extends FormRequest
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
      if(isset(Auth::user()->designation) && Auth::user()->designation == 'ROAC'){
        if(\Request::has('nomination_id')){
          $user_nomination = NominationApplicationModel::get_nomination_application_ro(\Request::input('nomination_id'));
          if(!$user_nomination){
            return redirect("roac/apply-nomination-step-4");
          }
          $recognized_party = $user_nomination['recognized_party'];
        }else{
          return redirect("roac/apply-nomination-step-4");
        }
      }else{
        if(\Request::has('nomination_id')){
          $user_nomination = NominationApplicationModel::get_nomination_application(\Request::input('nomination_id'));
          if(!$user_nomination){
            return redirect("nomination/apply-nomination-step-4");
          }
          $recognized_party = $user_nomination['recognized_party'];
        }else{
          return redirect("nomination/apply-nomination-step-4");
        }
      }
     
        return [
            'nomination_id'     => 'required|exists:nomination_application,id',
            'have_police_case'  => 'required|in:yes,no',
            'police_case'       => 'required_if:have_police_case,yes|array',
            'profit_under_govt' => 'required|in:yes,no',
            'office_held'       => 'required_if:profit_under_govt,yes',
            'court_insolvent'   => 'required|in:yes,no',
            'discharged_insolvency'         => 'required_if:court_insolvent,yes',
            'allegiance_to_foreign_country' => 'required|in:yes,no',
            'country_detail'                => 'required_if:allegiance_to_foreign_country,yes',
            'disqualified_section8A'        => 'required|in:yes,no',
            'disqualified_period'           => 'required_if:disqualified_section8A,yes',
            'disloyalty_status'             => 'required|in:yes,no',
            'date_of_dismissal'   => 'required_if:disloyalty_status,yes',
            'subsiting_gov_taken' => 'required|in:yes,no',
            'subsitting_contract' => 'required_if:subsiting_gov_taken,yes',
            'managing_agent'      => 'required|in:yes,no',
            'gov_detail'          => 'required_if:managing_agent,yes',
            'disqualified_by_comission_10Asec' => 'required|in:yes,no',
            'date_of_disqualification'  => 'required_if:disqualified_by_comission_10Asec,yes',
            'date_of_disloyal'          => 'required|date',
        ];     
    }
    
    public function messages()
    {
        return [
            'nomination_id.required'   			    =>   __('part3a.nomerror'), 
            'have_police_case.required'             =>  __('part3a.perror'), 
            'police_case.required_if'       		=> __('part3a.perror'), 
            'profit_under_govt.required'       		=> __('part3a.pue'), 
            'office_held.required_if'       		=> __('part3a.det'), 
            'court_insolvent.required'       		=> __('part3a.ine'), 
            'discharged_insolvency.required_if'     => __('part3a.couins'),
            'allegiance_to_foreign_country.required'=> __('part3a.fore'),
            'country_detail.required_if'       		=> __('part3a.fore2'),
            'disqualified_section8A.required'       => __('part3a.dis8'),
            'disqualified_period.required_if'       => __('part3a.dis8d'),
            'disloyalty_status.required'       		=> __('part3a.lod'),
            'date_of_dismissal.required_if'       		=> __('part3a.lod'),
            'subsiting_gov_taken.required'       		=> __('part3a.subg'),
            'subsitting_contract.required_if'       		=> __('part3a.conp'),
            'managing_agent.required'       		=> __('part3a.mana'),
            'gov_detail.required_if'       		=>  __('part3a.gde'),
            'disqualified_by_comission_10Asec.required'       		=> __('part3a.cdis'),
            'date_of_disqualification.required_if'       		=> __('part3a.datdi'),
            'date_of_disloyal.required'       		=> __('part3a.dada'),
        ];
    }
}
