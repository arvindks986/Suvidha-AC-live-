@extends('layouts.theme') @section('title', 'Affidavit e-File') @section('content')
<link rel="stylesheet" href="{{ asset('appoinment/css/custom-dark.css') }} " type="text/css" />
<link rel="stylesheet" href="{{ asset('appoinment/css/jquery.dataTables.min.css') }} " type="text/css" />

<style type="text/css">

    .error {
        font-size: 12px;
        color: red;
    }
    .step-wrap.mt-4 ul li {
        margin-bottom: 21px;
    }
    .no-data-area {       
        padding: 0 0 1.7rem 0;
        
    }
    .tab-panel-bg {
      background-color: transparent;
      box-shadow: inset 0 0 3px #fff;
     }
     .tab-body {
        color: #212121;        
    } 
</style>
<main class="pt-3 pb-5 pl-5 pr-5">
    <section></section>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6 col-12">
                        <h4>{{Lang::get('affidavit.my_affidavit')}}</h4>
                    </div>
                    <div class="col-md-6 col-12"></div>
                </div>
            </div>
            <div class="custom-tab-area mt-3">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs">
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#submtd">{{Lang::get('affidavit.submitted')}}</a></li>
                    <li class="nav-item"><a class="nav-link active show" data-toggle="tab" href="#drft">{{Lang::get('affidavit.draft')}}</a></li>
                </ul>
                <div class="card card-shadow mt-4">
                    <div class="card-body p-0">
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div id="submtd" class="tab-pane">
                                <div class="tab-body tab-panel-bg">
                                    <table id="submitteddata" class="display table tableCustom table-bordered">
                                            @forelse($affidavit_finalized as $index=>$data)
                                                <tr>
                                                <td>{{ $index+1 }}</td>
                                                <td>
                                                    <div class="">
                                                        {{Lang::get('affidavit.affidavit_no')}} <span><strong>{{ $data->affidavit_id }}</strong></span>
                                                    </div>
                                                    <div><a href="{{ url('part-a-detailed-report?affidavit_id='.$data->affidavit_id) }}">Report View</a></div>
                                                </td>
                                                <td>
                                                    <div class="">
                                                        {{Lang::get('affidavit.name')}}: <span><strong>{{ $data->cand_name }}</strong></span>
                                                    </div>
                                                    <div class="">
                                                        {{Lang::get('affidavit.party')}} <span><strong> @if($data->partyabbre){{getpartybyid($data->partyabbre)->PARTYNAME}}@endif</strong></span>
                                                    </div>
                                                </td>
                                                <td>
													<div>
                                                        {{Lang::get('affidavit.state')}}: <span><strong>{{ getstatebystatecode($data->st_code)->ST_NAME }}</strong></span>
                                                    </div>
														
                                                    <div class="">
                                                        {{Lang::get('affidavit.ac_no')}} &amp; {{Lang::get('affidavit.name')}}: <span><strong> @if($data->ac_no){{ $data->ac_no }}-{{ getacbyacno($data->st_code,$data->ac_no)->AC_NAME }}@endif</strong></span>
                                                    </div>
                                                   <!-- <div>
                                                        {{Lang::get('affidavit.district')}}: <span><strong> @if($data->dist_no){{ getdistrictbydistrictno($data->st_code, $data->dist_no)->DIST_NAME }} @endif</strong></span>
                                                    </div>-->
                                                    
                                                </td>
                                                <td>
                                                    <div class="">
                                                        {{Lang::get('affidavit.election')}} <span><strong>{{Lang::get('affidavit.general')}}- {{date('Y')}}</strong></span>
                                                    </div>
                                                    <div class="dt-tm"><span>
                                                        <?php 
                                                        $d = strtotime($data->finalized_on);
                                                        echo date("d M Y h:i A", $d);
                                                        ?>
                                                        </span></div>
                                                </td>
                                            </tr>
                                            @empty
                                                <tr align="center">
                                                    <td align="center"  class="w-100"><h5 class="red w-100">{{Lang::get('affidavit.no_record_found')}}.</h5></td>
                                                </tr>
                                            @endforelse
                                    </table>
                                </div>
                                <div class="no-data-area">
                                     <div class="row justify-content-center align-items-center">
                                    <div class="col-sm-5">
                                    <div class="tab-actn-btn my-5">
                                        <div class="apply-btn d-inline-flex">
                                            <span class="apply-icon"></span>
                                            <a href="{{ route('affidavit.dashboard') }}">
                                                {{Lang::get('affidavit.apply_new_affidavit')}}
                                            </a>
                                             <div class="help-txt">{{Lang::get('affidavit.here_you_can_apply_for_new_affidavit_application')}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                                </div>
                            </div>
                            <div id="drft" class="tab-pane active show">
                                <div class="tab-body">
                                    <table id="draftdata" class="table tableCustom w-100">
                                        <tbody>
                                            @forelse($getcan_details as $index=>$data)
                                                <tr>
                                                <td>{{ $index+1 }}</td>
                                                <td>
                                                    <div class="">
                                                        {{Lang::get('affidavit.affidavit_no')}} <span><strong>{{ $data->affidavit_id }}</strong></span>
                                                    </div>
                                                    <div><a href="#">{{Lang::get('affidavit.draft')}}</a></div>
                                                </td>
                                                <td>
                                                    <div class="">
                                                        {{Lang::get('affidavit.name')}}: <span><strong>{{ $data->cand_name }}</strong></span>
                                                    </div>
                                                    <div class="">
                                                        {{Lang::get('affidavit.party')}} <span><strong>@if($data->partyabbre){{getpartybyid($data->partyabbre)->PARTYNAME}}@endif</strong></span>
                                                    </div>
                                                </td>
                                                <td>
													<div>
                                                        {{Lang::get('affidavit.state')}}: <span><strong>@if($data->st_code){{ getstatebystatecode($data->st_code)->ST_NAME }} @endif</strong></span>
                                                    </div>
												
                                                    <div class="">
                                                        {{Lang::get('affidavit.ac_no')}} &amp; {{Lang::get('affidavit.name')}}: <span><strong>@if($data->ac_no){{ $data->ac_no }}-{{ getacbyacno($data->st_code,$data->ac_no)->AC_NAME }}@endif</strong></span>
                                                    </div>
                                                  <!--  <div>
                                                        {{Lang::get('affidavit.district')}}: <span><strong>@if($data->dist_no){{ getdistrictbydistrictno($data->st_code, $data->dist_no)->DIST_NAME }} @endif</strong></span>
                                                    </div>-->
                                                    
                                                </td>
                                                <td>
                                                    <div class="">
                                                        {{Lang::get('affidavit.election')}} <span><strong>{{Lang::get('affidavit.general')}}- {{date('Y')}}</strong></span>
                                                    </div>
                                                    <div class="dt-tm"><span>
                                                        <?php 
                                                        $d = strtotime($data->created_at);
                                                        echo date("d M Y h:i A", $d);
                                                        ?>
                                                        </span></div>
                                                </td>

                                                 <td class="td-edt-btn">
                                                     <a href="{{ route('affidavit.edit', base64_encode(json_encode($data->id))) }}">
                                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i> <span>{{Lang::get('affidavit.edit')}}</span>
                                                    </a>
                                                </td> 

                                                 <!--  <td class="td-edt-btn">
                                                     <a href="{{ route('affidavit.edit', Illuminate\Support\Facades\Crypt::encrypt($data->id) ) }}">
                                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i> <span>{{Lang::get('affidavit.edit')}}</span>
                                                    </a>
                                                </td> -->
                                            </tr>
                                            @empty
                                                <tr align="center">
                                                    <td align="center"  class="w-100"><h5 class="red w-100">{{Lang::get('affidavit.no_record_found')}}.</h5></td>
                                                </tr>
                                            @endforelse
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection @section('script')
<script type="text/javascript">
    

    ( function( $ ) {
      $( function() {
        $("#draftdata").DataTable();
        $("#submitteddata").DataTable();
      });
    })( jQuery );    
</script>
@endsection
