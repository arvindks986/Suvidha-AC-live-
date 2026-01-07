<?php

namespace App\Http\Requests\Admin\Nomination;

use Illuminate\Foundation\Http\FormRequest;
use App\models\Admin\Nomination\NominationModel;
use Auth, Session;
use App\models\Admin\Nomination\NominationApplicationModel;

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
     
        return [
            'nomination_id'     => 'required|exists:nomination_application,id',
            'have_police_case'  => 'required|in:1,2',
            'police_case'       => 'required_if:have_police_case,1|array',
            'profit_under_govt' => 'required|in:1,2',
            'office_held'       => 'required_if:profit_under_govt,1',
            'court_insolvent'   => 'required|in:1,2',
            'discharged_insolvency'         => 'required_if:court_insolvent,1',
            'allegiance_to_foreign_country' => 'required|in:1,2',
            'country_detail'                => 'required_if:allegiance_to_foreign_country,1',
            'disqualified_section8A'        => 'required|in:1,2',
            'disqualified_period'           => 'required_if:disqualified_section8A,1',
            'disloyalty_status'             => 'required|in:1,2',
            'date_of_dismissal'   => 'required_if:disloyalty_status,1',
            'subsiting_gov_taken' => 'required|in:1,2',
            'subsitting_contract' => 'required_if:subsiting_gov_taken,1',
            'managing_agent'      => 'required|in:1,2',
            'gov_detail'          => 'required_if:managing_agent,1',
            'disqualified_by_comission_10Asec' => 'required|in:1,2',
            'date_of_disqualification'  => 'required_if:disqualified_by_comission_10Asec,1',
            'date_of_disloyal'          => 'required|date',
        ];     
    }
    
    public function messages()
    {
        return [
            'election_id'   =>  'Please choose a valid election type',
            'ac_no'         => 'Please choose a valid pc',
            'st_code'       => 'Please choose a valid state',
        ];
    }
}
