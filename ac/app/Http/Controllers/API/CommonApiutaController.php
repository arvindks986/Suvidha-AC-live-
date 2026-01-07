<?php

namespace App\Http\Controllers\API;
use App\adminmodel\CandidateNApiModel;
use App\commonModel; 
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB; 

class CommonApiutaController extends BaseController
{
    public $commonModel = null;
    public $candidateModel = null;

    public function __construct()
    {
        $this->commonModel = new commonModel();
        $this->candidateModel = new CandidateNApiModel();
    }

    public $successStatus = 200;
    public $createdStatus = 201;
    public $nocontentStatus = 204;
    public $notmodifiedStatus = 304;
    public $badrequestStatus = 400;
    public $unauthorizedStatus = 401;
    public $notfoundStatus = 404;
    public $intservererrorStatus = 500;

    public function getElectionByDate()
    {    
            $filter_array = [
                'const_type'                => 'AC',
            ];

            $election_details = DB::connection('suivhdaaclivetest')->table('m_election_history')
                ->where($filter_array)
                ->orderby('id', 'desc')
                ->groupBy('election_id')
                ->limit(2)
                ->get();
 
        if (!empty($election_details) > 0) {
                foreach($election_details as $raw){
                $election_data[] = array(
                    "election_id"       => $raw->id,
                    "election_type_id"  => $raw->election_type_id,
                    "const_type"        => $raw->const_type,
                    "elect_type"        => $raw->elect_type,
                    "description"       => $raw->description,
                );
                }
                
        } else {
            return $this->sendError('No Record Found!', (object)[], $this->successStatus);
        }
        return $this->sendResponse($election_data, 'Record Found!');
    }


    
    public function getElectionByDatepc()
    {    // dd(DB::connection()->getDatabaseName());
            $filter_array = [
                'const_type'                => 'PC',
            ];
           
            $election_details = DB::connection('suvidhapc')->table('m_election_history')
                ->where($filter_array)
                ->orderby('id', 'desc')
                ->groupBy('election_id')
                ->limit(3)
                ->get();
                
                
        if (!empty($election_details) > 0) {
                foreach($election_details as $raw){
                $election_data[] = array(
                    "election_id"       => $raw->id,
                    "election_type_id"  => $raw->election_type_id,
                    "const_type"        => $raw->const_type,
                    "elect_type"        => $raw->elect_type,
                    "description"       => $raw->description,
                );
                }
                
        } else {
            return $this->sendError('No Record Found!', (object)[], $this->successStatus);
        }
        return $this->sendResponse($election_data, 'Record Found!');
    }

}
