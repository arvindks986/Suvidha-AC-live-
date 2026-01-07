<?php

namespace App\models\Admin\mparty;

use Illuminate\Database\Eloquent\Model;
use DB;

class DPartyModel extends Model
{
  public $timestamps = false;
  protected $table = 'd_party';
  public $fillable = ['id', 'CCODE', 'PARTYABBRE', 'PARTYSYM', 'ST_CODE', 'added_update_at', 'remarks', 'created_at', 'added_created_at', 'updated_at', 'created_by', 'updated_by', 'deleted'];

  public static function add_mparty($data = array())
  {
    // dd($data);
    if (!empty($data['ccode']) && isset($data['ccode'])) {
      $object = MPartyModel::where('CCODE', $data['ccode'])->first();

      $object->PARTYABBRE = $data['partyabbre'];
      $object->PARTYHABBR = $data['partyhabbr'];
      $object->PARTYNAME = $data['partyname'];
      $object->PARTYHNAME = $data['partyhname'];
      $object->PARTYTYPE = $data['partytype'];
      $object->party_reg_date = $data['party_reg_date'];
      $object->deleteflag = 'N';
      $object->PARTYSYM = '0';
      $object->party_typeid = $data['party_typeid'];
      $object->remarks = $data['remarks'];
      // $object->updated_at = date('Y-m-d H:i:s');
      $object->added_update_at = date('Y-m-d');
      $object->updated_by = \Auth::user()->officername;
      MPartyModel::where('CCODE', $data['ccode'])->update($object);
      //dd($object );
    } else {
      $object = new MPartyModel();
      $object->PARTYABBRE = $data['partyabbre'];
      $object->PARTYHABBR = $data['partyhabbr'];
      $object->PARTYNAME = $data['partyname'];
      $object->PARTYHNAME = $data['partyhname'];
      $object->PARTYTYPE = $data['partytype'];
      $object->party_reg_date = $data['party_reg_date'];
      $object->deleteflag = 'N';
      $object->party_typeid = $data['party_typeid'];
      $object->remarks = $data['remarks'];
      $object->created_at = date('Y-m-d H:i:s');
      $object->added_created_at = date('Y-m-d');
      $object->created_by = \Auth::user()->officername;
    }

    if (!$object->save()) {
      return false;
    }

    return true;
  }


  public static function getallstate_bypartyabbre($abbre)
  {
    $sql = DPartyModel::leftjoin('m_state', 'm_state.ST_CODE', '=', 'd_party.ST_CODE');
    $sql->where('d_party.PARTYABBRE', $abbre);
    $sql->where('d_party.deleted', '0');
    $sql->selectRaw("d_party.*,m_state.ST_NAME");
    $query = $sql->orderByRaw('d_party.ST_CODE')->get();
    // dd($query);
    if (!$query) {
      return false;
    }
    return $query->toArray();
  }
}
