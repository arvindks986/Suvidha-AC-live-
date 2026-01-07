<?php

namespace App\Http\Requests\Nomination;

use Illuminate\Foundation\Http\FormRequest;
use App\models\Nomination\ProfileModel;
 
use Auth;

class NominationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {    return true;
    }
    
    public function rules()
    {   
        $object = profileModel::get_candidate_profile();
		
        if($object){
            $id = $object['id'];
            return [
                'name'                 => 'required|min:3|max:255',
               // 'hname'                => 'required|min:3|max:255',
                'vname'                => 'required|min:3|max:255',
                //'alias_name'           => 'required|min:3|max:255',
                //'alias_hname'          => 'required|min:3|max:255',
                'father_name'          => 'required|min:3|max:255',
                //'father_hname'         => 'required|min:3|max:255',
                'father_vname'         => 'required|min:3|max:255',
                'category'             => 'required|in:sc,st,general,BL',
                //'email'                => 'required|email|unique:profile,email,'.$id,
                'mobile'               => 'required|mobile|unique:profile,mobile,'.$id,
                //'pan_number'           => 'required|min:10|max:10',
                'age'                  => 'required|numeric|between:25,120',
                'address'              => 'required|min:3|max:255',
                //'haddress'      	   => 'required|min:3|max:255',
                'vaddress' 			   => 'required|min:3|max:255',
                //'epic_no'              => 'required|unique:profile,epic_no,'.$id,
                'epic_no'              => 'required',
                'part_no'              => 'required|min:1|max:255',
                'serial_no'            => 'required|min:1|max:255',
                'state'                => 'required|exists:m_state,ST_CODE',
                'district'             => 'required|exists:m_district,DIST_NO',
                'ac'                   => 'required|exists:m_ac,AC_NO',
                'gender'               => 'required|in:male,female,third'

            ];
        }else{
            return [
                'name'                  => 'required|min:3|max:255',
                //'hname'           		=> 'required|min:3|max:255',
                'vname'      			=> 'required|min:3|max:255',
               // 'alias_name'           	=> 'required|min:3|max:255',
              //  'alias_hname'     		=> 'required|min:3|max:255',
                'father_name'          	=> 'required|min:3|max:255',
               // 'father_hname'         	=> 'required|min:3|max:255',
                'father_vname'    		=> 'required|min:3|max:255',
                'category'              => 'required|in:sc,st,general,BL',
                //'email'                	=> 'required|email|unique:profile,email',
                'mobile'               	=> 'required|mobile|unique:profile,mobile',
                //'pan_number'           	=> 'required|min:10|max:10',
                'age'                  	=> 'required|numeric|between:25,120',
                'address'            	=> 'required|min:3|max:255',
               // 'haddress'      		=> 'required|min:3|max:255',
                'vaddress' 				=> 'required|min:3|max:255',
                //'epic_no'               => 'required|unique:profile,epic_no',
                'epic_no'              => 'required',
                'part_no'               => 'required|min:1|max:255',
                'serial_no'             => 'required|min:1|max:255',
                'state'                 => 'required|exists:m_state,ST_CODE',
                'district'              => 'required|exists:m_district,DIST_NO',
                'ac'                    => 'required|exists:m_ac,AC_NO',
                'gender'                => 'required|in:male,female,third'
            ];
        }
        
    }
    
    public function messages()
    {
        return [
            'epic_no.required'    =>  __('step1.Epic_error'),
            'epic_no.required'    =>  __('step1.Epic_search_error'),
            'epic_no.unique'    =>  __('step1.Epic_unique_error'),
            'name.required'    =>   __('step1.name_en_error'),
            'hname.required'    =>   __('step1.name_hi_error'),
            'vname.required'    =>   __('step1.name_hi_error'),
            'father_name.required'    =>   __('step1.father_husband_name_erro'),
            'father_hname.required'    =>   __('step1.father_husband_name_erro'),
            'father_vname.required'    =>   __('step1.father_husband_name_erro'),
            //'alias_name.required'    =>   __('step1.alias_name_error'),
           // 'alias_hname.required'    =>   __('step1.alias_name_error'),
            //'email.required'    =>   __('step1.email_error'),
            //'email.email'    =>   __('step1.email_error'),
            //'email.unique'    =>   __('step1.email_error_unique'),
            'mobile.required'    =>   __('step1.mobile_error'),
            'mobile.digits'    =>   __('step1.mobile_error'),
            'mobile.mobile'    =>   __('step1.mobile_error'),
            'mobile.unique'    =>   __('step1.mobile_error_unique'),
			'gender.required'    =>   __('step1.gender_error'),
			//'pan_number.required'    =>   __('step1.pan_error'),
			//'pan_number.pan'    =>   __('step1.pan_error'),
			//'pan_number.min'    =>   __('step1.pan_error'),
			'age.required'    =>   __('step1.age_error'),
			'age.between'    =>   __('part3.aerror'),
			'category.required'    =>   __('step1.Category_error'),
			'serial_no.required'    =>   __('step1.seriel_error'),
			'part_no.required'    =>   __('step1.part_error'),
			'address.required'    =>   __('step1.adress_error_en'),
			//'haddress.required'    =>   __('step1.adress_error_hi'),
			'vaddress.required'    =>   __('step1.adress_error_v'),
			'state.required'    =>   __('step1.state_error'),
			'district.required'    =>   __('step1.dist_error'),
			'ac.required'    =>   __('step1.ac_error'),
        ];
    }
}
