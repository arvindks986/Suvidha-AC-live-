<?php

namespace App\Http\Requests\Nomination;

use Illuminate\Foundation\Http\FormRequest;
use App\models\Nomination\ProfileModel;
use Auth;

class NominationPart12Request extends FormRequest
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
        'nomination_id' => 'required|exists:nomination_application,id',
        'image'         => 'required',
        'recognized_party' => 'required|in:recognized,not-recognized,both',
        'legislative_assembly' => 'required|exists:m_ac,AC_NO',
        'name' => 'required|min:3|max:255',
        'father_name' => 'required|min:3|max:255',
        'address' => 'required|min:3|max:255',
        'serial_no' => 'required|min:1|max:255',
        'part_no' => 'required|min:1|max:255',
        'resident_ac_no' => 'required|exists:m_ac,AC_NO',
        'proposer_name' => 'required_if:recognized_party,recognized,both|min:3|max:255',
        'proposer_serial_no' => 'required_if:recognized_party,recognized,both|min:1|max:255',
		'proposer_part_no' => 'required_if:recognized_party,recognized,both|numeric|min:0|not_in:0',
        //'proposer_assembly' => 'required_if:recognized_party,recognized,both|exists:m_ac,AC_NO',
        'apply_date'                => 'required|date',
        'non_recognized_proposers'  => 'required_if:recognized_party,not-recognized,both|array'
      ];


    }
    
    public function messages()
    {
      return [
        
		'nomination_id.required'   =>  __('step3.nomination_id'),
		'recognized_party.required'   =>  __('step3.recognized_party'),
		'legislative_assembly.required'   =>  __('step3.legislative_assembly'),
		'name.required'   =>  __('step3.name'),
		'father_name.required'   =>  __('step3.father_name'),
		'serial_no.required'   =>  __('step3.serial_no'),
		'address.required'   =>  __('step3.address'),
		'proposer_name.required_if'   =>  __('step3.proposer_name'),
		'part_no.required'   =>  __('step3.part_no'),
		'resident_ac_no.required'   =>  __('step3.resident_ac_no'),
		'proposer_serial_no.required_if'   =>  __('step3.proposer_serial_no'),
		'proposer_part_no.required_if'   =>  __('step3.proposer_part_no'),
		'proposer_assembly.required_if'   =>  __('step3.proposer_assembly'),
		'nomination_id.required'   => __('step3.nomination_id'),
        'ac_no'         => __('step3.ac_no'),
        'st_code'       => __('step3.st_code'),
        'image.required' => __('step3.image'),
        'image.image'    => __('step3.image'),
        'non_recognized_proposers.required_if'  => __('step3.non_recognized_proposers'),
        'non_recognized_proposers.array' => __('step3.non_recognized_proposers'),
        'part_no.min' => __('step3.part_no'),
      ];
    }
  }
