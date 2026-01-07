<?php namespace App\models\Common;

use Illuminate\Database\Eloquent\Model;

class PartyModel extends Model
{
  
  protected $table = 'm_party';
	
	public static function get_parties_all_national($data = array()){
		$sql = PartyModel::leftjoin('d_party','d_party.CCODE','=','m_party.CCODE');
		$sql->leftjoin('m_symbol','m_symbol.SYMBOL_NO','=','d_party.PARTYSYM');
		$sql->where('deleteflag','N');
		if(!empty($data['is_recognized']) && !empty($data['st_code'])){
			//state is mendatory for is recognized party
			$sql->whereRaw("(m_party.PARTYTYPE = 'N' OR m_party.PARTYTYPE = 'S') OR m_party.CCODE IN (SELECT m_party.CCODE FROM d_party LEFT JOIN m_party ON (d_party.PARTYABBRE = m_party.PARTYABBRE) WHERE m_party.PARTYTYPE = 'S' AND ST_CODE = '".$data['st_code']."')");
		}else{ 
			$sql->whereRaw("m_party.PARTYTYPE = 'N' OR m_party.CCODE IN (SELECT m_party.CCODE FROM d_party LEFT JOIN m_party ON (d_party.PARTYABBRE = m_party.PARTYABBRE) WHERE m_party.PARTYTYPE != 'S' AND ST_CODE != '".$data['st_code']."')");
		}
		$sql->selectRaw("CONCAT(m_party.PARTYNAME,'-',m_party.PARTYHNAME) as name, CONCAT(m_party.PARTYABBRE,'-',m_party.PARTYHABBR) as abbr, m_party.*, CONCAT(SYMBOL_DES,'-',SYMBOL_HDES), m_party.CCODE as party_id");
		$sql->orderByRaw('m_party.PARTYABBRE');
    	return $sql->get();
	}
	
	public static function get_parties_all_state($data = array()){
		$sql = PartyModel::leftjoin('d_party','d_party.CCODE','=','m_party.CCODE');
		$sql->leftjoin('m_symbol','m_symbol.SYMBOL_NO','=','d_party.PARTYSYM');
		$sql->where('deleteflag','N');
		if(!empty($data['is_recognized']) && !empty($data['st_code'])){
			//state is mendatory for is recognized party
			$sql->whereRaw("(m_party.PARTYTYPE = 'S' OR m_party.PARTYTYPE = 'S') OR m_party.CCODE IN (SELECT m_party.CCODE FROM d_party LEFT JOIN m_party ON (d_party.PARTYABBRE = m_party.PARTYABBRE) WHERE m_party.PARTYTYPE = 'S' AND ST_CODE = '".$data['st_code']."')");
		}else{ 
			$sql->whereRaw("m_party.PARTYTYPE = 'S' OR m_party.CCODE IN (SELECT m_party.CCODE FROM d_party LEFT JOIN m_party ON (d_party.PARTYABBRE = m_party.PARTYABBRE) WHERE m_party.PARTYTYPE != 'S' AND ST_CODE != '".$data['st_code']."')");
		}
		$sql->selectRaw("CONCAT(m_party.PARTYNAME,'-',m_party.PARTYHNAME) as name, CONCAT(m_party.PARTYABBRE,'-',m_party.PARTYHABBR) as abbr, m_party.*, CONCAT(SYMBOL_DES,'-',SYMBOL_HDES), m_party.CCODE as party_id");
		$sql->orderByRaw('m_party.PARTYABBRE');
    	return $sql->get();
	}
	
	
	public static function get_parties_all($data = array()){
		$sql = PartyModel::leftjoin('d_party','d_party.CCODE','=','m_party.CCODE');
		$sql->leftjoin('m_symbol','m_symbol.SYMBOL_NO','=','d_party.PARTYSYM');
		$sql->where('deleteflag','N');
		if(!empty($data['is_recognized']) && !empty($data['st_code'])){
			//state is mendatory for is recognized party
			$sql->whereRaw("(m_party.PARTYTYPE = 'N' OR m_party.PARTYTYPE = 'S') OR m_party.CCODE IN (SELECT m_party.CCODE FROM d_party LEFT JOIN m_party ON (d_party.PARTYABBRE = m_party.PARTYABBRE) WHERE m_party.PARTYTYPE = 'S' AND ST_CODE = '".$data['st_code']."')");
		}else{
			$sql->whereRaw("m_party.PARTYTYPE != 'N' AND m_party.CCODE = '1180' OR m_party.CCODE IN (SELECT m_party.CCODE FROM d_party LEFT JOIN m_party ON (d_party.PARTYABBRE = m_party.PARTYABBRE) WHERE m_party.PARTYTYPE = 'S' AND ST_CODE != '".$data['st_code']."')");
		}
		$sql->selectRaw("CONCAT(m_party.PARTYNAME,'-',m_party.PARTYHNAME) as name, CONCAT(m_party.PARTYABBRE,'-',m_party.PARTYHABBR) as abbr, m_party.*, CONCAT(SYMBOL_DES,'-',SYMBOL_HDES), m_party.CCODE as party_id");
		$sql->orderByRaw('m_party.PARTYABBRE');
    	return $sql->get();
	}
	
	public static function setup_party($data = array()){
		$sql = PartyModel::leftjoin('d_party','d_party.CCODE','=','m_party.CCODE');
		$sql->leftjoin('m_symbol','m_symbol.SYMBOL_NO','=','d_party.PARTYSYM');
		$sql->where('deleteflag','N');
		if(!empty($data['is_recognized']) && !empty($data['st_code'])){
			//state is mendatory for is recognized party
			$sql->whereRaw("(m_party.PARTYTYPE = 'U' OR m_party.PARTYTYPE = 'Z') OR m_party.CCODE IN (SELECT m_party.CCODE FROM d_party LEFT JOIN m_party ON (d_party.PARTYABBRE = m_party.PARTYABBRE) WHERE m_party.PARTYTYPE = 'U' AND ST_CODE = '".$data['st_code']."')");
		}else{
			$sql->whereRaw("m_party.PARTYTYPE = 'U' or  m_party.PARTYTYPE = 'Z' OR m_party.CCODE IN (SELECT m_party.CCODE FROM d_party LEFT JOIN m_party ON (d_party.PARTYABBRE = m_party.PARTYABBRE) WHERE m_party.PARTYTYPE = 'U' AND ST_CODE != '".$data['st_code']."')");
		}
		$sql->selectRaw("CONCAT(m_party.PARTYNAME,'-',m_party.PARTYHNAME) as name, CONCAT(m_party.PARTYABBRE,'-',m_party.PARTYHABBR) as abbr, m_party.*, CONCAT(SYMBOL_DES,'-',SYMBOL_HDES), m_party.CCODE as party_id");
		$sql->orderByRaw('m_party.PARTYABBRE');
    	return $sql->get();
	}
	
	
	
	
	public static function get_parties($data = array()){
		$sql = PartyModel::leftjoin('d_party','d_party.CCODE','=','m_party.CCODE');
		$sql->leftjoin('m_symbol','m_symbol.SYMBOL_NO','=','d_party.PARTYSYM');
		$sql->where('deleteflag','N');
		if(!empty($data['is_recognized']) && !empty($data['st_code'])){
			//state is mendatory for is recognized party
			$sql->whereRaw("m_party.PARTYTYPE = 'N' OR m_party.CCODE IN (SELECT m_party.CCODE FROM d_party INNER JOIN m_party ON (d_party.PARTYABBRE = m_party.PARTYABBRE) WHERE m_party.PARTYTYPE = 'S' AND ST_CODE = '".$data['st_code']."')");
		}else{
			$sql->whereRaw("m_party.PARTYTYPE != 'N' AND m_party.CCODE = '1180' OR m_party.CCODE IN (SELECT m_party.CCODE FROM d_party INNER JOIN m_party ON (d_party.PARTYABBRE = m_party.PARTYABBRE) WHERE m_party.PARTYTYPE = 'S' AND ST_CODE != '".$data['st_code']."')");
		}
		$sql->selectRaw("CONCAT(m_party.PARTYNAME,'-',m_party.PARTYHNAME) as name, CONCAT(m_party.PARTYABBRE,'-',m_party.PARTYHABBR) as abbr, m_party.*, CONCAT(SYMBOL_DES,'-',SYMBOL_HDES), m_party.CCODE as party_id");
		$sql->orderByRaw('m_party.PARTYABBRE');
    	return $sql->get();
	}

	public static function get_party($party_id, $data = array()){
		$party = [];
		$sql = PartyModel::leftjoin('d_party','d_party.CCODE','=','m_party.CCODE');
		$sql->leftjoin('m_symbol','m_symbol.SYMBOL_NO','=','d_party.PARTYSYM');
		$sql->where('deleteflag','N');
		if(!empty($data['is_recognized']) && !empty($data['st_code'])){
			//state is mendatory for is recognized party
			$sql->whereRaw("m_party.PARTYTYPE = 'N' OR m_party.CCODE IN (SELECT m_party.CCODE FROM d_party INNER JOIN m_party ON (d_party.PARTYABBRE = m_party.PARTYABBRE) WHERE m_party.PARTYTYPE = 'S' AND ST_CODE = '".$data['st_code']."')");
		}else{
			$sql->whereRaw("m_party.PARTYTYPE != 'N' AND m_party.CCODE = '1180' OR m_party.CCODE IN (SELECT m_party.CCODE FROM d_party INNER JOIN m_party ON (d_party.PARTYABBRE = m_party.PARTYABBRE) WHERE m_party.PARTYTYPE = 'S' AND ST_CODE != '".$data['st_code']."')");
		}
		$query = $sql->where("m_party.CCODE", $party_id)->first();
		if(!$query){
			return false;
		}
    	return $query->toArray();
	}

}