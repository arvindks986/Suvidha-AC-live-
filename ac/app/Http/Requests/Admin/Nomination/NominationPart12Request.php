<?php

namespace App\Http\Requests\Admin\Nomination;

use Illuminate\Foundation\Http\FormRequest;
use App\models\Admin\Nomination\NominationModel;
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
        'recognized_party' => 'required|in:1,2',
        'legislative_assembly' => 'required|exists:m_ac,AC_NO',
        'name' => 'required|min:3|max:255',
        'father_name' => 'required|min:3|max:255',
        'address' => 'required|min:3|max:255',
        'serial_no' => 'required|min:1|max:255',
        'part_no' => 'required|min:1|max:255',
        'resident_ac_no' => 'required|exists:m_ac,AC_NO',
        'proposer_name' => 'required_if:recognized_party,1|min:3|max:255',
        'proposer_serial_no' => 'required_if:recognized_party,1|min:1|max:255',
        'proposer_part_no' => 'required_if:recognized_party,1|min:1|max:255',
        'proposer_assembly' => 'required_if:recognized_party,1|exists:m_ac,AC_NO',
        'apply_date'                => 'required|date',
        'non_recognized_proposers'  => 'required_if:recognized_party,2|array'
      ];


    }
    
    public function messages()
    {
      return [
        'election_id'   =>  'Please choose a valid election type',
        'ac_no'         => 'Please choose a valid pc',
        'st_code'       => 'Please choose a valid state',
        'profileimg.required' => 'please choose a profile image',
        'profileimg.image'    => 'please choose a valid profile image',
        'non_recognized_proposers.required'  => 'please fill non recognized proposers details',
        'non_recognized_proposers.array' => 'please fill non recognized proposers details',
        'part_no.min' => 'S.No is required and greater than 1 digit',
        'proposer_name.required' => 'please  enter valide propeser name'
      ];
    }
  }
