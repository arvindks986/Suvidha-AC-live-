<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;

class CandidateCountModel extends Model
{
    protected $table = 'candidate_personal_detail';

    private $candidate_id = [];
	
//Jitendra Code 
public static function get_count_by_status_category($st_code){

    $results = [];
    $sql = CandidateModel::selectRaw("cn.st_code,m_ac.AC_TYPE as cand_category")
	->join("candidate_nomination_detail as cn","candidate_personal_detail.candidate_id","=","cn.candidate_id")
	->join("m_ac", function($join){ 
		$join->on("m_ac.ST_CODE","=","cn.st_code")
			->on("m_ac.AC_NO","=","cn.ac_no");
	})
	->join("m_election_details as med", function($join){ 
		$join->on("med.ST_CODE","=","cn.st_code")
			->on("med.CONST_NO","=","cn.ac_no");
	})
	->where('med.CONST_TYPE','AC')
	->where('med.CURRENTELECTION','Y')
	->where('party_id', '!=', '1180')
	->where('application_status', '!=','11')
	->where('cn.st_code',$st_code);
    $cand_results =  $sql->groupBy("m_ac.AC_TYPE")->get();
	
	//echo '<pre>'; print_r($cand_results); die;
	
	
   
    foreach ($cand_results as $key => $category) {

        $cont_male   = CandidateCountModel::get_nom_count_by_status_category($st_code, 6,1,'male', $category->cand_category);
        $cont_female = CandidateCountModel::get_nom_count_by_status_category($st_code, 6,1,'female', $category->cand_category);
        $cont_third  = CandidateCountModel::get_nom_count_by_status_category($st_code, 6,1,'third', $category->cand_category);

        $nom_male   = CandidateCountModel::get_nom_count_by_status_category($st_code, 0,0,'male', $category->cand_category);
        $nom_female = CandidateCountModel::get_nom_count_by_status_category($st_code, 0,0,'female', $category->cand_category);
        $nom_third  = CandidateCountModel::get_nom_count_by_status_category($st_code, 0,0,'third', $category->cand_category);

        $rej_male   = CandidateCountModel::get_nom_count_by_status_category($st_code, 4,0,'male', $category->cand_category);
        $rej_female = CandidateCountModel::get_nom_count_by_status_category($st_code, 4,0,'female', $category->cand_category);
        $rej_third  = CandidateCountModel::get_nom_count_by_status_category($st_code, 4,0,'third', $category->cand_category);

        $with_male   = CandidateCountModel::get_nom_count_by_status_category($st_code, 5,0,'male', $category->cand_category);
        $with_female = CandidateCountModel::get_nom_count_by_status_category($st_code, 5,0,'female', $category->cand_category);
        $with_third  = CandidateCountModel::get_nom_count_by_status_category($st_code, 5,0,'third', $category->cand_category);

        $results[$category->cand_category] = [
            'category'      => $category->cand_category,
            'nom_male'      => $nom_male,
            'nom_female'    => $nom_female,
            'nom_third'     => $nom_third,
            'nom_total'     => $nom_male+$nom_female+$nom_third,
            'rej_male'      => $rej_male,
            'rej_female'    => $rej_female,
            'rej_third'     => $rej_third,
            'rej_total'     => $rej_male+$rej_female+$rej_third,
            'with_male'      => $with_male,
            'with_female'    => $with_female,
            'with_third'     => $with_third,
            'with_total'     => $with_male+$with_female+$with_third,
            'cont_male'      => $cont_male,
            'cont_female'    => $cont_female,
            'cont_third'     => $cont_third,
            'cont_total'     => $cont_male+$cont_female+$cont_third, 
        ];
    }


        return $results;
        

    }


    public static function get_nom_count_by_status_category($st_code, $application_status, $finalaccepted, $cand_gender, $category){

        $sql = CandidateModel::join("candidate_nomination_detail as cn","candidate_personal_detail.candidate_id","=","cn.candidate_id")
		->join("m_ac", function($join){ 
			$join->on("m_ac.ST_CODE","=","cn.st_code")
				->on("m_ac.AC_NO","=","cn.ac_no");}
		)
		->join("m_election_details as med", function($join){ 
			$join->on("med.ST_CODE","=","cn.st_code")
				->on("med.CONST_NO","=","cn.ac_no");
		})
	->where('med.CONST_TYPE','AC')
	->where('med.CURRENTELECTION','Y')
		->where('party_id', '!=', '1180')->where('application_status', '!=','11');
        $sql->where("cn.st_code",$st_code)->where('m_ac.AC_TYPE', $category);
        if($application_status){
            $sql->where("application_status",$application_status);
        }
        $sql->where("cand_gender",$cand_gender);
        if($finalaccepted){
            $sql->where('finalaccepted', 1)->where('symbol_id', '!=','200');
        }

        if($application_status == '5'){
            $sql->whereRaw("candidate_personal_detail.candidate_id NOT IN (SELECT candidate_id FROM candidate_nomination_detail WHERE st_code = '".$st_code."' AND application_status = '6' AND finalaccepted = '1')");
        }

        if($application_status == '4'){
            $sql->whereRaw("candidate_personal_detail.candidate_id NOT IN (SELECT candidate_id FROM candidate_nomination_detail WHERE st_code = '".$st_code."' AND (application_status = '5' OR (application_status = '6' AND finalaccepted = '1')))");
        }

        //$query = $sql->count(DB::raw("DISTINCT (concat(cn.candidate_id,m_ac.AC_NO))"));
		$query = $sql->count(DB::raw("DISTINCT (concat(cn.candidate_id,',',m_ac.AC_NO))"));
        return ($query)?$query:0;
    }




public static function get_count_by_status_category_ac($st_code,$ac_no,$category){

    $results = [];
    $sql = CandidateModel::selectRaw("cn.st_code,cand_category,AC_NAME")
	->join("candidate_nomination_detail as cn","candidate_personal_detail.candidate_id","=","cn.candidate_id")
	->join("m_ac", function($join){ 
		$join->on("m_ac.ST_CODE","=","cn.st_code")
			->on("m_ac.AC_NO","=","cn.ac_no");
	})
	->join("m_election_details as med", function($join){ 
		$join->on("med.ST_CODE","=","cn.st_code")
			->on("med.CONST_NO","=","cn.ac_no");
	})
	->where('med.CONST_TYPE','AC')
	->where('med.CURRENTELECTION','Y')
	->where('party_id', '!=', '1180')
	->where('application_status', '!=','11')
	->where('cn.st_code',$st_code)
	->where('cand_category',$category)
	->where('cn.ac_no',$ac_no);
    $cand_results =  $sql->get();
	
	//echo '<pre>'; print_r($cand_results); die;
	
	
   
    foreach ($cand_results as $key => $cate) {

        $cont_male   = CandidateCountModel::get_nom_count_by_status_category_ac($st_code,$ac_no, 6,1,'male', $category);
        $cont_female = CandidateCountModel::get_nom_count_by_status_category_ac($st_code,$ac_no, 6,1,'female', $category);
        $cont_third  = CandidateCountModel::get_nom_count_by_status_category_ac($st_code,$ac_no, 6,1,'third', $category);

        $nom_male   = CandidateCountModel::get_nom_count_by_status_category_ac($st_code,$ac_no, 0,0,'male', $category);
        $nom_female = CandidateCountModel::get_nom_count_by_status_category_ac($st_code,$ac_no, 0,0,'female', $category);
        $nom_third  = CandidateCountModel::get_nom_count_by_status_category_ac($st_code,$ac_no, 0,0,'third', $category);

        $rej_male   = CandidateCountModel::get_nom_count_by_status_category_ac($st_code,$ac_no, 4,0,'male', $category);
        $rej_female = CandidateCountModel::get_nom_count_by_status_category_ac($st_code,$ac_no, 4,0,'female', $category);
        $rej_third  = CandidateCountModel::get_nom_count_by_status_category_ac($st_code,$ac_no, 4,0,'third', $category);

        $with_male   = CandidateCountModel::get_nom_count_by_status_category_ac($st_code,$ac_no, 5,0,'male', $category);
        $with_female = CandidateCountModel::get_nom_count_by_status_category_ac($st_code,$ac_no, 5,0,'female', $category);
        $with_third  = CandidateCountModel::get_nom_count_by_status_category_ac($st_code,$ac_no, 5,0,'third', $category);
		
		
		
		
		$table='counting_master_'.strtolower(trim($st_code));


		$dfdata = DB::select("SELECT
           TEMP1.CATEGORY,TEMP1.fdmale AS fdmale,
           TEMP1.fdfemale AS fdfemale,TEMP1.fdthird AS fdthird,TEMP1.FD AS fd
           FROM
           (
           SELECT TEMP.*
           FROM (
           SELECT C.cand_category as category,C.cand_gender,cp.ac_no,
           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.total_vote) as pctotalvotes FROM $table as cp1
           where cp1.party_id != 1180 and  cp1.ac_no = cp.ac_no and  C.cand_gender = 'male' 
           GROUP BY cp1.ac_no ),5) < .16666 THEN 1 ELSE 0 END) as fdmale,
            
           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.total_vote) as pctotalvotes FROM $table as cp1
           where cp1.party_id != 1180 and  cp1.ac_no = cp.ac_no and  C.cand_gender = 'female' 
           GROUP BY cp1.ac_no ),5) < .16666 THEN 1 ELSE 0 END) as fdfemale,
            
            
           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.total_vote) as pctotalvotes FROM $table as cp1
           where cp1.party_id != 1180 and  cp1.ac_no = cp.ac_no and  C.cand_gender = 'third'
           GROUP BY cp1.ac_no ),5) < .16666 THEN 1 ELSE 0 END) as fdthird,
            
           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.total_vote) as pctotalvotes
           FROM $table as cp1
           where cp1.party_id != 1180 and cp1.ac_no = cp.ac_no
           GROUP BY cp1.ac_no ),5) < .16666 THEN 1 ELSE 0 END) as fd
            
           FROM  $table cp ,m_ac M,candidate_personal_detail  C
           WHERE cp.candidate_id not in(select candidate_id from winning_leading_candidate as w1
           where w1.ac_no = cp.ac_no and w1.st_code = '$st_code' AND w1.ac_no='$ac_no')
           AND cp.party_id != '1180'
           AND cp.ac_no=M.AC_NO
           AND C.cand_gender IN ('male','female','third')
           and C.candidate_id = cp.candidate_id
           AND M.AC_NO=cp.ac_no 
           AND M.ST_CODE='$st_code'
           AND M.ac_no='$ac_no'
           AND C.cand_category ='$category'
           )TEMP
           )TEMP1;");
		$dfdata = json_decode( json_encode($dfdata), true);
		
		//dd($dfdata);
		

        $results = [
            'ac_no'      	=> $ac_no,
            'ac_name'      	=> $cate->AC_NAME,
            'category'      => $category,
            'nom_male'      => $nom_male,
            'nom_female'    => $nom_female,
            'nom_third'     => $nom_third,
            'nom_total'     => $nom_male+$nom_female+$nom_third,
            'rej_male'      => $rej_male,
            'rej_female'    => $rej_female,
            'rej_third'     => $rej_third,
            'rej_total'     => $rej_male+$rej_female+$rej_third,
            'with_male'      => $with_male,
            'with_female'    => $with_female,
            'with_third'     => $with_third,
            'with_total'     => $with_male+$with_female+$with_third,
            'cont_male'      => $cont_male,
            'cont_female'    => $cont_female,
            'cont_third'     => $cont_third,
            'cont_total'     => $cont_male+$cont_female+$cont_third, 
            'df_male'      	 => $dfdata[0]['fdmale'],
            'df_female'      => $dfdata[0]['fdfemale'],
            'df_third'       => $dfdata[0]['fdthird'],
            'df_total'       => $dfdata[0]['fd']
        ];
    }


        return $results;
        

    }


    public static function get_nom_count_by_status_category_ac($st_code,$ac_no, $application_status, $finalaccepted, $cand_gender, $category){

        $sql = CandidateModel::join("candidate_nomination_detail as cn","candidate_personal_detail.candidate_id","=","cn.candidate_id")
		->join("m_ac", function($join){ 
			$join->on("m_ac.ST_CODE","=","cn.st_code")
				->on("m_ac.AC_NO","=","cn.ac_no");}
		)
		->join("m_election_details as med", function($join){ 
			$join->on("med.ST_CODE","=","cn.st_code")
				->on("med.CONST_NO","=","cn.ac_no");
		})
	->where('med.CONST_TYPE','AC')
	->where('med.CURRENTELECTION','Y')
		->where('party_id', '!=', '1180')->where('application_status', '!=','11');
        $sql->where("cn.st_code",$st_code)->where("cn.ac_no",$ac_no)->where('cand_category', $category);
        if($application_status){
            $sql->where("application_status",$application_status);
        }
        $sql->where("cand_gender",$cand_gender);
        if($finalaccepted){
            $sql->where('finalaccepted', 1)->where('symbol_id', '!=','200');
        }

        if($application_status == '5'){
            $sql->whereRaw("candidate_personal_detail.candidate_id NOT IN (SELECT candidate_id FROM candidate_nomination_detail WHERE st_code = '".$st_code."' and ac_no = '".$ac_no."' AND application_status = '6' AND finalaccepted = '1')");
        }

        if($application_status == '4'){
            $sql->whereRaw("candidate_personal_detail.candidate_id NOT IN (SELECT candidate_id FROM candidate_nomination_detail WHERE st_code = '".$st_code."' and ac_no = '".$ac_no."' AND (application_status = '5' OR (application_status = '6' AND finalaccepted = '1')))");
        }

        //$query = $sql->count(DB::raw("DISTINCT (concat(cn.candidate_id,m_ac.AC_NO))"));
		$query = $sql->count(DB::raw("DISTINCT (concat(cn.candidate_id,',',m_ac.AC_NO))"));
        return ($query)?$query:0;
    }
	
	
	
	public static function get_count_by_status_category_state($st_code,$category){

    $results = [];
    $sql = CandidateModel::selectRaw("cn.st_code,cand_category,AC_NAME")
	->join("candidate_nomination_detail as cn","candidate_personal_detail.candidate_id","=","cn.candidate_id")
	->join("m_ac", function($join){ 
		$join->on("m_ac.ST_CODE","=","cn.st_code")
			->on("m_ac.AC_NO","=","cn.ac_no");
	})
	->join("m_election_details as med", function($join){ 
		$join->on("med.ST_CODE","=","cn.st_code")
			->on("med.CONST_NO","=","cn.ac_no");
	})
	->where('med.CONST_TYPE','AC')
	->where('med.CURRENTELECTION','Y')
	->where('party_id', '!=', '1180')
	->where('application_status', '!=','11')
	->where('cn.st_code',$st_code)
	->where('cand_category',$category);
    $cand_results =  $sql->get();
	
	//echo '<pre>'; print_r($cand_results); die;
	
	
   
    foreach ($cand_results as $key => $cate) {

        $cont_male   = CandidateCountModel::get_nom_count_by_status_category_state($st_code, 6,1,'male', $category);
        $cont_female = CandidateCountModel::get_nom_count_by_status_category_state($st_code, 6,1,'female', $category);
        $cont_third  = CandidateCountModel::get_nom_count_by_status_category_state($st_code, 6,1,'third', $category);

        $nom_male   = CandidateCountModel::get_nom_count_by_status_category_state($st_code, 0,0,'male', $category);
        $nom_female = CandidateCountModel::get_nom_count_by_status_category_state($st_code, 0,0,'female', $category);
        $nom_third  = CandidateCountModel::get_nom_count_by_status_category_state($st_code, 0,0,'third', $category);

        $rej_male   = CandidateCountModel::get_nom_count_by_status_category_state($st_code, 4,0,'male', $category);
        $rej_female = CandidateCountModel::get_nom_count_by_status_category_state($st_code, 4,0,'female', $category);
        $rej_third  = CandidateCountModel::get_nom_count_by_status_category_state($st_code, 4,0,'third', $category);

        $with_male   = CandidateCountModel::get_nom_count_by_status_category_state($st_code, 5,0,'male', $category);
        $with_female = CandidateCountModel::get_nom_count_by_status_category_state($st_code, 5,0,'female', $category);
        $with_third  = CandidateCountModel::get_nom_count_by_status_category_state($st_code, 5,0,'third', $category);
		
		
		
		
		$table='counting_master_'.strtolower(trim($st_code));


		$dfdata = DB::select("SELECT
           TEMP1.CATEGORY,TEMP1.fdmale AS fdmale,
           TEMP1.fdfemale AS fdfemale,TEMP1.fdthird AS fdthird,TEMP1.FD AS fd
           FROM
           (
           SELECT TEMP.*
           FROM (
           SELECT C.cand_category as category,C.cand_gender,cp.ac_no,
           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.total_vote) as pctotalvotes FROM $table as cp1
           where cp1.party_id != 1180 and  cp1.ac_no = cp.ac_no and  C.cand_gender = 'male' 
           GROUP BY cp1.ac_no ),5) < .16666 THEN 1 ELSE 0 END) as fdmale,
            
           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.total_vote) as pctotalvotes FROM $table as cp1
           where cp1.party_id != 1180 and  cp1.ac_no = cp.ac_no and  C.cand_gender = 'female' 
           GROUP BY cp1.ac_no ),5) < .16666 THEN 1 ELSE 0 END) as fdfemale,
            
            
           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.total_vote) as pctotalvotes FROM $table as cp1
           where cp1.party_id != 1180 and  cp1.ac_no = cp.ac_no and  C.cand_gender = 'third'
           GROUP BY cp1.ac_no ),5) < .16666 THEN 1 ELSE 0 END) as fdthird,
            
           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.total_vote) as pctotalvotes
           FROM $table as cp1
           where cp1.party_id != 1180 and cp1.ac_no = cp.ac_no
           GROUP BY cp1.ac_no ),5) < .16666 THEN 1 ELSE 0 END) as fd
            
           FROM  $table cp ,m_ac M,candidate_personal_detail  C
           WHERE cp.candidate_id not in(select candidate_id from winning_leading_candidate as w1
           where w1.ac_no = cp.ac_no and w1.st_code = '$st_code')
           AND cp.party_id != '1180'
           AND cp.ac_no=M.AC_NO
           AND C.cand_gender IN ('male','female','third')
           and C.candidate_id = cp.candidate_id
           AND M.AC_NO=cp.ac_no 
           AND M.ST_CODE='$st_code'
           AND C.cand_category ='$category'
           )TEMP
           )TEMP1;");
		$dfdata = json_decode( json_encode($dfdata), true);
		
		//dd($dfdata);
		

        $results = [
            'category'      => $category,
            'nom_male'      => $nom_male,
            'nom_female'    => $nom_female,
            'nom_third'     => $nom_third,
            'nom_total'     => $nom_male+$nom_female+$nom_third,
            'rej_male'      => $rej_male,
            'rej_female'    => $rej_female,
            'rej_third'     => $rej_third,
            'rej_total'     => $rej_male+$rej_female+$rej_third,
            'with_male'      => $with_male,
            'with_female'    => $with_female,
            'with_third'     => $with_third,
            'with_total'     => $with_male+$with_female+$with_third,
            'cont_male'      => $cont_male,
            'cont_female'    => $cont_female,
            'cont_third'     => $cont_third,
            'cont_total'     => $cont_male+$cont_female+$cont_third, 
            'df_male'      	 => $dfdata[0]['fdmale'],
            'df_female'      => $dfdata[0]['fdfemale'],
            'df_third'       => $dfdata[0]['fdthird'],
            'df_total'       => $dfdata[0]['fd']
        ];
    }


        return $results;
        

    }


    public static function get_nom_count_by_status_category_state($st_code, $application_status, $finalaccepted, $cand_gender, $category){

        $sql = CandidateModel::join("candidate_nomination_detail as cn","candidate_personal_detail.candidate_id","=","cn.candidate_id")
		->join("m_ac", function($join){ 
			$join->on("m_ac.ST_CODE","=","cn.st_code")
				->on("m_ac.AC_NO","=","cn.ac_no");}
		)
		->join("m_election_details as med", function($join){ 
			$join->on("med.ST_CODE","=","cn.st_code")
				->on("med.CONST_NO","=","cn.ac_no");
		})
	->where('med.CONST_TYPE','AC')
	->where('med.CURRENTELECTION','Y')
		->where('party_id', '!=', '1180')->where('application_status', '!=','11');
        $sql->where("cn.st_code",$st_code)->where('cand_category', $category);
        if($application_status){
            $sql->where("application_status",$application_status);
        }
        $sql->where("cand_gender",$cand_gender);
        if($finalaccepted){
            $sql->where('finalaccepted', 1)->where('symbol_id', '!=','200');
        }

        if($application_status == '5'){
            $sql->whereRaw("candidate_personal_detail.candidate_id NOT IN (SELECT candidate_id FROM candidate_nomination_detail WHERE st_code = '".$st_code."' AND application_status = '6' AND finalaccepted = '1')");
        }

        if($application_status == '4'){
            $sql->whereRaw("candidate_personal_detail.candidate_id NOT IN (SELECT candidate_id FROM candidate_nomination_detail WHERE st_code = '".$st_code."' AND (application_status = '5' OR (application_status = '6' AND finalaccepted = '1')))");
        }

        //$query = $sql->count(DB::raw("DISTINCT (concat(cn.candidate_id,m_ac.AC_NO))"));
		$query = $sql->count(DB::raw("DISTINCT (concat(cn.candidate_id,',',m_ac.AC_NO))"));
        return ($query)?$query:0;
    }
	
}
