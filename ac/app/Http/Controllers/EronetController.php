<?php

namespace App\Http\Controllers;

use App\adminmodel\PartyMaster;
use App\commonModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessEronetACElectrosDataJobs;
use App\Jobs\ProcessEronetElectrosDataJobs;
use App\models\Admin\ElectorModel;
use App\models\Admin\mparty\DPartyModel;
use App\models\Admin\mparty\SymbolModel;
use App\models\Admin\polling_station\PollingStationModel;
use App\Services\GetGenderWiseElectorsCountService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use SoapClient;

ini_set("memory_limit", "1500M");
set_time_limit('360');
class EronetController extends Controller
{
    public function __construct()
  {
    $this->commonModel  = new commonModel();
  }
    public function fecthGetGenderWiseElectorsCountForPC(Request $request)
    {
        $limit = 50;
        $page = $request->input('page', '1');
        $offset = $limit * $page;
        $offset = $offset - $limit;
        $queryParam = "";
        $pollingStations = PollingStationModel::where(function ($q) use ($request, &$queryParam) {
            if ($request->has('st_code') && $request->input('st_code') != '') {
                $q->where('ST_CODE', $request->input('st_code'));
                $queryParam .= "st_code=" . $request->input('st_code');
            }
            if ($request->has('ac_no') && $request->input('ac_no') != '') {
                $q->where('AC_NO', $request->input('ac_no'));
                $queryParam .= "&ac_no=" . $request->input('ac_no');
            }
            if ($request->has('part_no') && $request->input('part_no') != '') {
                $q->where('PART_NO', $request->input('part_no'));
                $queryParam .= "&part_no=" . $request->input('part_no');
            }
            if ($request->has('for_failed') && $request->input('for_failed') == 'yes') {
                $q->where('is_fecthed_from_eronet', 2);
                $queryParam .= "&for_failed=" . $request->input('for_failed');
            }
        })->limit($limit)->offset($offset)->get();
        // dd($pollingStations->count());
        if ($pollingStations->count() > 0) {
            foreach ($pollingStations as $pollingStation) {
                ProcessEronetElectrosDataJobs::dispatch($pollingStation);
            }
            $request->merge([
                'page' => $page + 1
            ]);
            return $this->fecthGetGenderWiseElectorsCountForPC($request, ($page + 1));
        } else {
            Session::flash('error', 'Jobs are created for fecthing electros data from EROnet.');
            $queryParam = '';
            if ($request->has('st_code') && $request->input('st_code') != '') {
                $queryParam .= "state=" . $request->input('st_code');
            }
            if ($request->has('ac_no') && $request->input('ac_no') != '') {
                $queryParam .= "&ac_id=" . $request->input('ac_no');
            }
            return redirect(url('eci/turnout/fetchElectorsCountPanel?' . $queryParam));
        }
    }

    public function fecthGetGenderWiseElectorsCountForAC(Request $request)
    {
        $limit = 50;
        $page = $request->input('page', '1');
        $offset = $limit * $page;
        $offset = $offset - $limit;
        $queryParam = "";
        $electors = ElectorModel::where(function ($q) use ($request, &$queryParam) {
            if ($request->has('st_code') && $request->input('st_code') != '' && $request->input('st_code') != 'all') {
                $q->where('st_code', $request->input('st_code'));
                $queryParam .= "st_code=" . $request->input('st_code');
            }
            if ($request->has('ac_no') && $request->input('ac_no') != '') {
                $q->where('ac_no', $request->input('ac_no'));
                $queryParam .= "&ac_no=" . $request->input('ac_no');
            }
            if ($request->has('for_failed') && $request->input('for_failed') == 'yes') {
                $q->where('is_fecthed_from_eronet', 2);
                $queryParam .= "&for_failed=" . $request->input('for_failed');
            }
        })->limit($limit)->offset($offset)->get();
        if ($electors->count() > 0) {
            foreach ($electors as $elector) {
                ProcessEronetACElectrosDataJobs::dispatch($elector);
            }
            $request->merge([
                'page' => $page + 1
            ]);
            return $this->fecthGetGenderWiseElectorsCountForAC($request, ($page + 1));
        } else {
            Session::flash('error', 'Jobs are created for fecthing electros data from EROnet.');
            $queryParam = '';
            if ($request->has('st_code') && $request->input('st_code') != '') {
                $queryParam .= "state=" . $request->input('st_code');
            }
            if ($request->has('ac_no') && $request->input('ac_no') != '') {
                $queryParam .= "&ac_id=" . $request->input('ac_no');
            }
            return redirect(url('eci/turnout/fetchACElectorsCountPanel?' . $queryParam));
        }
    }

    public function getMpartywcf($case)
    {
        $client     = new SoapClient('http://164.100.213.224/mpartywcf/Service1.svc?wsdl');
        switch ($case) {
            case 'GET_DPARTY':
            default:
                $data = $client->__soapCall("GET_DPARTY", []);
                return json_decode($data->GET_DPARTYResult, true);
                break;
            
            case 'GET_MPARTY':
                $data = $client->__soapCall("GET_MPARTY", []);
                return json_decode($data->GET_MPARTYResult, true);
                break;
            
            case 'GET_MSYMBOL':
                $data = $client->__soapCall("GET_MSYMBOL", []);
                return json_decode($data->GET_MSYMBOLResult, true);
                break;

        }
    }

    public function getDparty($ccode)
    {
        return DPartyModel::where('CCODE', $ccode)->count();
    }

    public function dpartyDatalist(Request $request)
    {
        try {
            $data = [];
            $data['heading_title'] = "D_Party Data";
            $data['results'] = $this->getMpartywcf('GET_DPARTY');
            $data['existingresult'] = DPartyModel::count();
            // dd($data['results']);
            $data['newdparty'] = count($data['results']) - $data['existingresult'];
            $data['self'] = $this;
            $data['user_data'] = $this->commonModel->getunewserbyuserid(Auth::user()->id);
            $data['update_link'] = url('eci/eci-dparty-update-list');
            return view('admin.ac.eci.datalist.dpartyDatalist', $data);
        } catch (\Exception $e) {
            Log::error($e);
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }

    public function updateDPartyData(Request $request)
    {
        try {
            $data = $this->getMpartywcf('GET_DPARTY');
            foreach ($data as $key => $value) {
                DPartyModel::updateOrCreate(['CCODE'=>$value['ccode']],[
                    'PARTYABBRE' => $value['PARTYABBRE'],
                    'PARTYSYM' => $value['PARTYSYM'],
                    'ST_CODE' => $value['ST_CODE'],
                ]);
            }
            return Redirect('/eci/eci-dparty-data-list')->with('error', 'DParty table is updated');
        } catch (\Exception $e) {
            Log::error($e);
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }

    public function getMparty($ccode)
    {
        return PartyMaster::where('CCODE', $ccode)->count();
    }

    public function mpartyDatalist(Request $request)
    {
        try {
            $data = [];
            $data['heading_title'] = "M_Party Data";
            $result = $this->getMpartywcf('GET_MPARTY');
            
            $data['results'] = [];
            foreach ($result as $key => $value) {
                $temp = $value;
                $temp['isExist'] = $this->getMparty($value['CCODE']);
                $data['results'][] = $temp;
            }
            // dd(count($data['results']));
            // echo "<code><pre>";
            // print_r($data['results']);
            // echo "</code></pre>";
            // die();
            $data['existingresult'] = PartyMaster::count();
            $data['newdparty'] = count($data['results']) - $data['existingresult'];
            $data['self'] = $this;
            $data['user_data'] = $this->commonModel->getunewserbyuserid(Auth::user()->id);
            $data['update_link'] = url('eci/eci-mparty-update-list');
            return view('admin.ac.eci.datalist.mpartyDatalist', $data);
        } catch (\Exception $e) {
            Log::error($e);
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }

    public function updateMPartyData(Request $request)
    {
        try {
            $data = $this->getMpartywcf('GET_MPARTY');
            foreach ($data as $key => $value) {
                PartyMaster::updateOrCreate(['CCODE'=>$value['CCODE']],[
                    'PARTYABBRE' => $value['PARTYABBRE'],
                    'PARTYHABBR' => $value['PARTYHABBR'],
                    'PARTYHFOCABBR' => $value['PARTYHFOCABBR'],
                    'PARTYHFOCNAME' => $value['PARTYHFOCNAME'],
                    'PARTYHNAME' => $value['PARTYHNAME'],
                    'PARTYNAME' => $value['PARTYNAME'],
                    'PARTYSYM' => $value['PARTYSYM'],
                    'PARTYTYPE' => $value['PARTYTYPE'],
                    'deleteflag' => $value['deleteflag'],
                ]);
            }
            return Redirect('/eci/eci-mparty-data-list')->with('error', 'MParty table is updated');
        } catch (\Exception $e) {
            Log::error($e);
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }

    public function getSymbol($symbolno)
    {
        return SymbolModel::where('SYMBOL_NO', $symbolno)->count();
    }

    public function msymbolDatalist(Request $request)
    {
        try {
            $data = [];
            $data['heading_title'] = "M_Symbol Data";
            $data['results'] = $this->getMpartywcf('GET_MSYMBOL');
            // dd($data['results']);
            $data['existingresult'] = SymbolModel::count();
            $data['newdsymbol'] = count($data['results']) - $data['existingresult'];
            $data['self'] = $this;
            $data['user_data'] = $this->commonModel->getunewserbyuserid(Auth::user()->id);
            $data['update_link'] = url('eci/eci-msymbol-update-list');
            return view('admin.ac.eci.datalist.msymbolDatalist', $data);
        } catch (\Exception $e) {
            Log::error($e);
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }

    public function updateSymbolData(Request $request)
    {
        try {
            $data = $this->getMpartywcf('GET_MSYMBOL');
            foreach ($data as $key => $value) {
                SymbolModel::updateOrCreate(['SYMBOL_NO'=>$value['SYMBOL_NO']],[
                    'SYMBOL_DES' => $value['SYMBOL_DES'],
                    'SYMBOL_HDES' => $value['SYMBOL_HDES'],
                    'SYMBOL_BMP' => $value['SYMBOL_BMP'],
                    'SYMBOL_HFOCDES' => $value['SYMBOL_HFOCDES'],
                    'Ind_Symbol' => $value['Ind_Symbol'],
                    'Symbol_Img' => $value['Symbol_Img'],
                ]);
            }
            return Redirect('/eci/eci-msymbol-data-list')->with('error', 'Symbols table is updated');
        } catch (\Exception $e) {
            Log::error($e);
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }
}  // end class