@extends( (Auth::user()->role_id != '19') ? 'layouts.theme' : 'admin.layouts.ac.theme', $data) 
@section('title', 'Affidavit Cadidate Details') @section('content')
<style type="text/css">
.affidavit_nav .step-current a, .affidavit_nav .step-success a{
    color:#fff!important;
}
.affidavit_nav a{
    color:#999!important;
}
.step-wrap.mt-4 ul li {
    margin-bottom: 21px;
}

hr.hrwidth {
    width: 100%;
}
.panel-title > a {
    display: block;
    padding: 15px;
    text-decoration: none;
}

.textLeft{
    width: calc(100% - 55px);
    float: left;
}
.textRight{
    width: 50px;
    float: right;
    display: flex;
    justify-content: center;
}

.more-less {
    float: right;
    color: #212121;
}
input.not_applicable {
    text-transform: uppercase;
}
.accordion_head .textLeft{
    font-size: 0.95rem;
}
.greenBtn{

}
.greenBtn {
    min-width: 131px;
    border: 2px solid #28a745;
    padding: 0.65em 1.2em;
    border-radius: 2.5em;
    cursor: pointer;
    transition: all 0.25s;
    margin: 1em auto;
    box-sizing: border-box;
    max-width: 70%;
    display: block;
    font-weight: 400;
    outline: none;
}
.greenBtn:hover {
    background-color: #28a745;
    color: white;
    outline: none;
    box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
}
</style>
<link rel="stylesheet" href="{{ asset('affidavit/css/affidavit.css') }}" id="theme-stylesheet" />
<link rel="stylesheet" href="{{ asset('admintheme/css/jquery-ui.css') }}" id="theme-stylesheet" />
<link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }} " type="text/css" />
<link rel="stylesheet" href="{{ asset('appoinment/css/custom.css') }} " type="text/css" />
<link rel="stylesheet" href="{{ asset('appoinment/css/custom-dark.css') }} " type="text/css" />
<link rel="stylesheet" href="{{ asset('affidavit/css/sweetalert.css') }} " type="text/css" />
<main role="main" class="inner cover mb-3">
    <section>
        <div class="container">
            @if (session('flash-message'))
            <div class="alert alert-success mt-4">{{session('flash-message') }}</div>
            @endif 
            @if ($message = Session::get('Init'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif

            @if ($message = Session::get('msgerror'))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
        </div>
    </section>
<?php if(Auth::user()->role_id == '19'){
	$menu_action = 'roac/';
}else{
	$menu_action = '';
} ?>	
	
<div class="step-wrap mt-4">
            <ul class="affidavit_nav">
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'affidavitdashboard')}}">{{Lang::get('affidavit.initial_details') }}</a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'affidavit/candidatedetails')}}">{{Lang::get('affidavit.candidate_details') }}</a></span></li>
                <li class="step-current"><b>&#10004;</b><span><a href="{{url($menu_action.'affidavit/pending-criminal-cases')}}">{{Lang::get('affidavit.court_cases') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'Affidavit/MovableAssets')}}">{{Lang::get('affidavit.movable_assets') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'immovable-assets')}}">{{Lang::get('affidavit.immovable_assets') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'liabilities')}}">{{Lang::get('affidavit.liabilities') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'Profession')}}">{{Lang::get('affidavit.profession') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'education')}}">{{Lang::get('affidavit.education')}}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'preview')}}">{{Lang::get('affidavit.preview_finalize') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'part-a-detailed-report')}}">{{Lang::get('affidavit.reports') }}</a></span></li>
            </ul>
        </div>
<div class="container-fliud">
    <div class="row">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="main_heading">{{Lang::get('affidavit.criminal_cases_details')}}</h4>
                    </div>
                </div>
                <div class="card-body">
                                <div class="accordion_head pb-2"><span class="textLeft">{{Lang::get('affidavit.cases_pending_againt_me_in_which_charges_have_been_for')}}</span><span class="textRight"> <span class="plusminus">+</span></span></div>
                                    <div class="accordion_body" style="display: none"> 
                                        <form id="election_form" method="POST" action="{{ route('save.pending.criminal.cases') }}" onsubmit="return validateCriminalCase()" autocomplete="off" enctype="x-www-urlencoded">
                                  {{ csrf_field() }}
                                    <div class="panel-body">
                                        <div class="row pt-3 col-md-12">
                                            <div class="form-check">
											
											
											
                                            <input type="radio" name="convictionType" class="form-check-input convictionType" value="1" {{count($get_criminal_cases_applicable)}} @if(count($get_criminal_cases_applicable) > 0 ) checked  @endif id="convictionType_id_1" />
                                            <label class="form-check-label" for="exampleRadios1">
                                                (i) {{Lang::get('affidavit.i_declare_that_i_have_been_convicted_for_any_criminal_offence')}}
                                            </label>
                                            </div>
                                            <table class="table table-info table-bordered purpleTable" id="get_criminal_cases_applicable_1" style="display: none;">
                                            <tr class="table-info">
                                              <th scope="row">{{Lang::get('affidavit.sr_no')}}</th>
                                              <th>{{Lang::get('affidavit.not_applicable')}}</th>
                                              <th>{{Lang::get('affidavit.modify_date')}}</th>
                                              <th>{{Lang::get('affidavit.action')}}</th>
                                            </tr>
                                            @forelse($get_criminal_cases_applicable as $index=>$cases)
                                              <tr>
                                                <td>{{ $index+1 }}</td>
                                                <td>{{ $cases->not_applicable }}</td>
                                                <td>{{ \Carbon\Carbon::parse($cases->modified_on)->format('d-m-Y') }}</td>
                                                <td>
												@if(Auth::user()->role_id != '19')
												<a href="{{ route('criminal.record.destroy',$cases->id) }}" class="btn btn-danger" id="criminalRecord" data-id="{{ $cases->id }}"><i class="fa fa-trash"></i>{{Lang::get('affidavit.delete')}}</a>
												@endif
                                                </td>
                                              </tr>
                                            @empty
                                              <tr><td colspan="10" align="center" style="color: red">{{Lang::get('affidavit.no_record_found')}}.</td></tr>
                                            @endforelse
                                        </table>
                                        </div>
                                        <div class="col-md-12 text-center"><strong>{{Lang::get('affidavit.or')}}</strong></div>
                                        <div class="row pt-2 pb-3">
                                            <div class="col-md-9">
                                                <div class="form-check">
                                                <input type="radio" name="convictionType" class="form-check-input convictionType" value="2" {{count($get_criminal_cases)}} id="convictionType_id_2" @if(count($get_criminal_cases) > 0 ) checked @endif />
                                                <label class="form-check-label"> (ii) {{Lang::get('affidavit.the_following_criminal_cases_are_pending_against_me')}}</label>
                                                </div>
                                            </div>
											@if(Auth::user()->role_id != '19')
                                            <div class="col-md-3">
                                                <input maxlength="14" placeholder="NOT APPLICABLE" type="text" name="not_applicable" class="form-control not_applicable" id="not_applicable" />
                                                <span id="not_applicable_error"></span>
                                            </div>
											@endif
                                        </div>
                                        <span id="success"></span>
                                        <table class="table table-info table-bordered purpleTable table-responsive" id="get_all_court_cases" style="display: none;">
                                            <tr class="table-info">
                                              <th scope="row">{{Lang::get('affidavit.sr_no')}}</th>
                                              <th>{{Lang::get('affidavit.fir_no')}}</th>
                                              <th>{{Lang::get('affidavit.state')}}/{{Lang::get('affidavit.district')}}</th>
                                              <th>{{Lang::get('affidavit.police_station')}}</th>
                                              <th>{{Lang::get('affidavit.police_station_address')}}</th>
                                              <th>{{Lang::get('affidavit.case_number')}}</th>
                                              <th>{{Lang::get('affidavit.name_of_court')}}</th>
                                              <th>{{Lang::get('affidavit.acts')}}/{{Lang::get('affidavit.section')}}</th>
                                              <th>{{Lang::get('affidavit.brief_description')}}</th>
                                              <th>{{Lang::get('affidavit.whether_charges_have_been_framed')}}</th>
                                              <th>{{Lang::get('affidavit.date')}}</th>
                                              <th>{{Lang::get('affidavit.whether_any_appeal_application_for_revision')}}</th>
                                              <th>{{Lang::get('affidavit.action')}}</th>
                                            </tr>
                                            @forelse($get_criminal_cases as $index=>$cases)
                                              <tr>
                                                <td>{{ $index+1 }}</td>
                                                <td>{{ $cases->fir_no }}</td>
                                                <td>{{ getstatebystatecode($cases->st_code)->ST_NAME }}/{{ getdistrictbydistrictno($cases->st_code,$cases->dist_no)->DIST_NAME }}</td>
                                                <td>{{ $cases->police_station }}</td>
                                                <td>{{ $cases->police_station_address }}</td>
                                                <td>{{ $cases->case_no }}</td>
                                                <td>{{ $cases->name_court_cognizance }}</td>
                                                <td>{{ $cases->acts }} / {{ $cases->sections }}</td>
                                                <td>{{ $cases->offence_description }}</td>
                                                <td>
                                                    @if($cases->framed_charge == 1)
                                                        {{Lang::get('affidavit.yes')}}
                                                    @elseif($cases->framed_charge == 2)
                                                        {{Lang::get('affidavit.no')}}   
                                                    @endif    
                                                </td>
												
												<td>
                                                    @if($cases->framed_charge == 1)
                                                        {{ \Carbon\Carbon::parse($cases->date_charges)->format('d-m-Y') }} 
                                                    @endif    
                                                </td>

                                                <td>@if($cases->appeal_application == 1)
                                                        {{Lang::get('affidavit.yes')}}
                                                    @elseif($cases->appeal_application == 2)
                                                        {{Lang::get('affidavit.no')}} 
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(Auth::user()->role_id != '19')
													<a href="{{ route('criminal.record.destroy',$cases->id) }}" class="btn btn-danger" id="criminalRecord" data-id="{{ $cases->id }}"><i class="fa fa-trash"></i> {{Lang::get('affidavit.delete')}}</a>
													@endif
													
													</td>
                                              </tr>
                                            @empty
                                              <tr><td colspan="13" align="center" style="color: red">{{Lang::get('affidavit.no_criminal_record_found')}}.</td></tr>
                                            @endforelse
                                        </table>

										@if(Auth::user()->role_id != '19')

                                        <div class="row" id="add_new_case" style="display: none;">
                                            <div class="col-md-12 text-right">
                                                <button class="nextBtn float-right" type="button">{{Lang::get('affidavit.add_new_case')}}</button></div>
                                        </div>
										
										@endif

                                        <fieldset class="py-4 px-5 mb-4" id="courtcaseInformation" class="courtcaseInformation" style="display: none;">
                                            <legend>{{Lang::get('affidavit.court_case_information')}}</legend>
                                            <div class="row">
                                                <div class="col-sm-3 col-12">
                                                    <div class="form-group mt-2 mb-2">
                                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.fir_no')}}</label>
                                                        <input type="text" name="fir_no" id="fir_no" class="form-control nomination-field-2" placeholder="FIR No." value="" />
                                                        <span id="error_1"></span>
                                                        @if ($errors->has('fir_no'))
                                                          <span class="text-danger">{{ $errors->first('fir_no') }}</span>
                                                        @endif 
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 col-12">
                                                    <div class="form-group mt-2 mb-2">
                                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.state')}}</label>
                                                        <select class="form-control" id="st_name" onchange="getDistrictList(this.value)" name="st_name">
                                                            <option value="0">-{{Lang::get('affidavit.select_state')}}-</option>
                                                            @forelse($statename as $state)
                                                            <option value="{{ $state->ST_CODE }}">{{ $state->ST_NAME }} - {{ $state->ST_NAME_HI }}</option>
                                                            @empty
                                                            <option>{{Lang::get('affidavit.no_record_found')}}</option>
                                                            @endforelse
                                                        </select>
                                                        <span id="error_2"></span>
                                                        @if ($errors->has('st_name'))
                                                          <span class="text-danger">{{ $errors->first('st_name') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="form-group mt-2 mb-2">
                                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.district')}}</label>
                                                        <select class="form-control" name="district_name" id="district_name">
                                                            <option value="0">--{{Lang::get('affidavit.select_district')}}--</option>
                                                        </select>
                                                        <span id="error_3"></span>
                                                        @if ($errors->has('district_name'))
                                                          <span class="text-danger">{{ $errors->first('district_name') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 col-12">
                                                    <div class="form-group mt-2 mb-2">
                                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.police_station')}}</label>
                                                        <input type="text" name="police_station" id="police_station" class="form-control nomination-field-2" placeholder="Police Station." />
                                                        <span id="error_4"></span>
                                                        @if ($errors->has('police_station'))
                                                          <span class="text-danger">{{ $errors->first('police_station') }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-sm-3 col-12">
                                                    <div class="form-group mt-2 mb-2">
                                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.police_station_address')}}</label>
                                                        <input type="text" name="police_station_address" id="police_station_address" onkeypress="return blockSpecialChar_address(event)"  class="form-control nomination-field-2" placeholder="Police Station Address." />
                                                        <span id="error_5"></span>
                                                        @if ($errors->has('police_station_address'))
                                                          <span class="text-danger">{{ $errors->first('police_station_address') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 col-12">
                                                    <div class="form-group mt-2 mb-2">
                                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.case_number')}}</label>
                                                        <input type="text" name="case_number" id="case_number" placeholder="Case Number" class="form-control nomination-field-2" onkeypress="return blockSpecialChar_address(event)" value="" />
                                                        <span id="error_6"></span>
                                                        @if ($errors->has('case_number'))
                                                          <span class="text-danger">{{ $errors->first('case_number') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 col-12">
                                                    <div class="form-group mt-2 mb-2">
                                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.name_of_court')}}</label>
                                                        <input type="text" name="name_court" id="name_court" placeholder="Name of court" class="form-control nomination-field-2" onkeypress="return blockSpecialChar_address(event)"  value="" />
                                                        <span id="error_7"></span>
                                                        @if ($errors->has('name_court'))
                                                          <span class="text-danger">{{ $errors->first('name_court') }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-sm-4 col-12">
                                                    <div class="form-group mt-2 mb-2">
                                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.acts')}}</label>
                                                        <input type="text" name="acts" id="acts" placeholder="act(s)" class="form-control nomination-field-2" onkeypress="return blockSpecialChar_address(event)" value="" />
                                                        <span id="error_8"></span>
                                                        @if ($errors->has('acts'))
                                                          <span class="text-danger">{{ $errors->first('acts') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-12">
                                                    <div class="form-group mt-2 mb-2">
                                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.section')}} <small>{{Lang::get('affidavit.give_no_of_the_section')}}</small></label>
                                                        <textarea name="section" id="section" onkeypress="return blockSpecialChar_address(event)" class="form-control" placeholder="Section"></textarea>
                                                        <span id="error_9"></span>
                                                        @if ($errors->has('section'))
                                                          <span class="text-danger">{{ $errors->first('section') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-12">
                                                    <div class="form-group mt-2 mb-2">
                                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.brief_description_of_the_offence')}}</label>
                                                        <textarea name="short_description" id="short_description" onkeypress="return blockSpecialChar_address(event)" placeholder="Brief Description" class="form-control"></textarea>
                                                        <span id="error_10"></span>
                                                        @if ($errors->has('short_description'))
                                                          <span class="text-danger">{{ $errors->first('short_description') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <hr class="hrwidth" />
                                                <div class="col-sm-4 col-12">
                                                    <div class="form-group mt-2 mb-2">
                                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.whether_charges_have_been_framed')}} ?</label>
                                                    </div>
                                                </div>

                                                <div class="col-sm-4 col-12">
                                                    <div class="form-group mt-2 mb-2"><input type="radio" name="court_framed_the_charge" class="court_framed_the_charge" value="1" checked="" /> {{Lang::get('affidavit.yes')}} <input value="2" type="radio" class="court_framed_the_charge" name="court_framed_the_charge" /> {{Lang::get('affidavit.no')}}</div>
                                                </div>

                                                <div class="col-sm-4 col-12" id="dateshowhide">
                                                    <div class="form-group mt-2 mb-2">
                                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.date')}}</label>
														
														<div class="input-group mb-3">        
														<div class="clearfix"></div>
														<input type="text" id="date" name="date" class="form-control dateofbirth" placeholder="Date" readonly="">
														<div class="input-group-append">
														  <span class="input-group-text"><i class="fa fa-calendar calendar_date"></i></span>
														</div>
													  </div>
														
                                                        <span id="error_11"></span>
                                                    </div>
                                                </div>

                                                <hr class="hrwidth" />
                                                <div class="col-sm-6 col-12">
                                                    <div class="form-group mt-2 mb-2">
                                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.whether_any_appeal_application_for_revision')}} ?</label>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6 col-12">
                                                    <div class="form-group mt-2 mb-2"><input type="radio" name="appeal_application" value="1" checked="" /> {{Lang::get('affidavit.yes')}} <input value="2" type="radio" name="appeal_application" /> {{Lang::get('affidavit.no')}}</div>
                                                </div>
                                            </div>
                                        </fieldset>
										
										@if(Auth::user()->role_id != '19')
                                        <div class="col-12 text-center">
                                            <button type="submit" class="greenBtn">{{Lang::get('affidavit.save')}}</button>
                                        </div>
										@endif
										
                                        <div class="clearfix"></div>
                                    </div>
                                     </form>
                                    </div>

                                <div class="accordion_head pb-2">
                                    <span class="textLeft">{{Lang::get('affidavit.cases_pending_againt_me_in_which_cognizance_has_been_taken_by_court')}}</span><span class="textRight"><span class="plusminus">+</span></span></div>
                                  <div class="accordion_body" style="display: none"> 
                                    <form id="case_of_conviction" method="POST" action="{{ route('save.case.of.conviction.cases') }}" onsubmit="return validateConvictionCases()" autocomplete="off" enctype="x-www-urlencoded">
                                  {{ csrf_field() }}
                                    <div class="panel-body">
                                      <div class="row pt-3 col-md-12">
                                        <div class="form-check">
										
											<?php //dd(count($get_conviction_cases_applicable));?>
										
                                            <input type="radio" name="convictionType_step_2" class="form-check-input convictionType_step_2" value="1" @if(count($get_conviction_cases_applicable) > 0 ) checked @endif id="conviction_case_step_1" />
                                            <label class="form-check-label" for="exampleRadios1">
                                                (i) {{Lang::get('affidavit.i_declare_that_i_have_not_been_convicted_for_any_criminal_offence')}}
                                            </label>
                                            <table class="table table-info table-bordered purpleTable" id="get_all_convition_cases_step_1" style="display: none;">
                                            <tr class="table-info">
                                              <th scope="row">{{Lang::get('affidavit.sr_no')}}</th>
                                              <th>{{Lang::get('affidavit.not_applicable')}}</th>
                                              <th>{{Lang::get('affidavit.modify_date')}}</th>
                                              <th>{{Lang::get('affidavit.action')}}</th>
                                            </tr>
                                            @forelse($get_conviction_cases_applicable as $index=>$cases)
                                              <tr>
                                                <td>{{ $index+1 }}</td>
                                                <td>{{ $cases->not_applicable }}</td>
                                                <td>{{ \Carbon\Carbon::parse($cases->modified_on)->format('d-m-Y') }}</td>
                                                <td>
												
												@if(Auth::user()->role_id != '19')
												<a href="{{ route('conviction.record.destroy',$cases->id) }}" class="btn btn-danger" id="convictionRecord" data-id="{{ $cases->id }}"><i class="fa fa-trash"></i>{{Lang::get('affidavit.delete')}}</a>
												@endif
												
                                                </td>
                                              </tr>
                                            @empty
                                              <tr><td colspan="10" align="center" style="color: red">{{Lang::get('affidavit.no_record_found')}}</td></tr>
                                            @endforelse
                                        </table>
                                        </div>
                                        </div>
                                        <div class="col-md-12 text-center"><strong>{{Lang::get('affidavit.or')}}</strong></div>
                                        <div class="row pt-2 pb-3">
                                            <div class="col-md-9">
                                                 <div class="form-check">
                                                <input type="radio" name="convictionType_step_2" class="form-check-input convictionType_step_2" value="2" id="conviction_case_step_2" @if(count($get_conviction_cases) > 0 ) checked @endif />
                                                <label class="form-check-label" >
                                                (ii) {{Lang::get('affidavit.i_have_been_convicted_for_the_offences_mentioned_below')}}</label>
                                            </div>
                                            </div>
											
											@if(Auth::user()->role_id != '19')
											
                                            <div class="col-md-3">
                                                <input maxlength="14" placeholder="NOT APPLICABLE" type="text" name="not_applicable_step_2" class="form-control not_applicable" id="not_applicable_step_2" />
                                                <span id="not_applicable_error_step_2"></span>
                                            </div>
											
											@endif
											
                                        </div>
                                        <table class="table table-info table-bordered purpleTable" id="get_all_convition_cases" style="display: none;">
                                            <tr class="table-info">
                                              <th scope="row">{{Lang::get('affidavit.sr_no')}}</th>
                                              <th>{{Lang::get('affidavit.case_number')}}</th>
                                              <th>{{Lang::get('affidavit.name_of_court')}}</th>
                                              <th>{{Lang::get('affidavit.acts')}} / {{Lang::get('affidavit.section')}}</th>
                                              <th>{{Lang::get('affidavit.brief_description_of_the_offence_for_which_conviction')}}</th>
                                              <th>{{Lang::get('affidavit.date_of_order')}}</th>
                                              <th>{{Lang::get('affidavit.punishment_imposed')}}</th>
                                              <th>{{Lang::get('affidavit.whether_any_appeal_has_been_filed_against_conviction_order')}}</th>
                                              <th>{{Lang::get('affidavit.details_and_present_status_of_appeal')}}</th>
                                              <th>{{Lang::get('affidavit.action')}}</th>
                                            </tr>
                                            @forelse($get_conviction_cases as $index=>$cases)
                                              <tr>
                                                <td>{{ $index+1 }}</td>
                                                <td>{{ $cases->case_no }}</td>
                                                <td>{{ $cases->convicting_court }}</td>
                                                <td>{{ $cases->acts }} / {{ $cases->sections }}</td>
                                                <td>{{ $cases->offence_description }}</td>
                                                <td>{{ \Carbon\Carbon::parse($cases->order_date)->format('d-m-Y') }}</td>
                                                <td>{{ $cases->punish }}</td>
                                                <td>
                                                    @if($cases->appeal_filed == 1)
                                                        {{Lang::get('affidavit.yes')}}
                                                    @else
                                                        {{Lang::get('affidavit.no')}}
                                                    @endif    
                                                </td>
												<td>
                                                    @if($cases->appeal_filed == 1)
                                                        {{ $cases->appeal }}
                                                    @endif    
                                                </td>
                                                <td>
												@if(Auth::user()->role_id != '19')
												<a href="{{ route('conviction.record.destroy',$cases->id) }}" class="btn btn-danger" id="convictionRecord" data-id="{{ $cases->id }}"><i class="fa fa-trash"></i>{{Lang::get('affidavit.delete')}}</a>
												@endif
                                                </td>
                                              </tr>
                                            @empty
                                              <tr><td colspan="10" align="center" style="color: red">{{Lang::get('affidavit.no_convicting_record_found')}}.</td></tr>
                                            @endforelse
                                        </table>

										@if(Auth::user()->role_id != '19')
                                        <div class="row" id="add_new_case_convicted" style="display: none;">
                                            <div class="col-md-12 text-right"><button class="btn btn-lg font-big dark-purple-btn pop-actn mb-3" type="button">{{Lang::get('affidavit.add_new_convited_case')}}</button></div>
                                        </div>
										@endif

                                        <fieldset class="py-4 px-5 mb-4" id="step_2_convicted" class="step_2_convicted" style="display: none;">
                                            <legend>{{Lang::get('affidavit.convicted_court_case_information')}}</legend>
                                            <div class="row">
                                                <div class="col-sm-4 col-12">
                                                    <div class="form-group mt-2 mb-2">
                                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.case_number')}}</label>
                                                        <input type="text" name="conviction_case_no" id="conviction_case_no" class="form-control nomination-field-2" onkeypress="return blockSpecialChar_address(event)"  placeholder="Case No." value="" />
                                                        <span id="error_1_convicted"></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-12">
                                                    <div class="form-group mt-2 mb-2">
                                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.name_of_court')}}</label>
                                                        <input type="text" name="name_of_the_court_conviction" id="name_of_the_court_conviction" class="form-control nomination-field-2" placeholder="name of the court" onkeypress="return blockSpecialChar_address(event)"  value="" />
                                                        <span id="error_2_convicted"></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-12">
                                                    <div class="form-group mt-2 mb-2">
                                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.acts')}}</label>
                                                        <textarea name="acts_conviction" id="acts_conviction" onkeypress="return blockSpecialChar_address(event)" class="form-control nomination-field-2" placeholder="Act(s)"></textarea>
                                                        <span id="error_3_convicted"></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-12">
                                                    <div class="form-group mt-2 mb-2">
                                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.sections')}}</label>
                                                        <textarea name="section_conviction" id="section_conviction" class="form-control nomination-field-2" onkeypress="return blockSpecialChar_address(event)" placeholder="Section(s)"></textarea>
                                                        <span id="error_4_convicted"></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-12">
                                                    <div class="form-group mt-2 mb-2">
                                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.brief_description_of_the_offence_for_which_conviction')}}</label>
                                                        <textarea
                                                            name="brief_description_conviction"
                                                            id="brief_description_conviction"
                                                            class="form-control nomination-field-2"
                                                            placeholder="Brief Description of the offence for which conviction"
                                                        ></textarea>
                                                        <span id="error_5_convicted"></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-12">
                                                    <div class="form-group mt-2 mb-2">
                                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.date_of_order')}}</label>
														
														
														
														<div class="input-group mb-3">        
														<div class="clearfix"></div>
														<input type="text" id="date_of_order_conviction" name="date_of_order_conviction" class="form-control dateofbirth" placeholder="Date of order" readonly="">
														<div class="input-group-append">
														  <span class="input-group-text"><i class="fa fa-calendar calendar_conviction"></i></span>
														</div>
													  </div>
														


                                                        <span id="error_6_convicted"></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-12">
                                                    <div class="form-group mt-2 mb-2">
                                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.punishment_imposed')}}</label>
                                                        <textarea name="punishment_imposed_conviction" id="punishment_imposed_conviction" class="form-control nomination-field-2" placeholder="punishment imposed"></textarea>
                                                        <span id="error_7_convicted"></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-12">
                                                    <div class="form-group mt-2 mb-2">
                                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.whether_any_appeal_has_been_filed_against_conviction_order')}}</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-1 col-12">
                                                    <div class="form-group mt-2 mb-2">
                                                        <input type="radio" name="conviction_order_conviction" class="conviction_order_conviction" checked="checked" value="1" /> {{Lang::get('affidavit.yes')}}
                                                        <input type="radio" name="conviction_order_conviction" class="conviction_order_conviction" value="2" /> {{Lang::get('affidavit.no')}}
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 col-12 yerornocheckorder">
                                                    <div class="form-group mt-2 mb-2">
                                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.details_and_present_status_of_appeal')}}</label>
                                                        <input type="text" name="details_present_appeal_conviction" id="details_present_appeal_conviction" class="form-control nomination-field-2" placeholder="Details and present status of Appeal" />
                                                        <span id="error_8_convicted"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
										
										@if(Auth::user()->role_id != '19')
                                        <div class="col-12 text-center">
                                            <button type="submit" class="greenBtn">{{Lang::get('affidavit.save')}}</button>
                                        </div>   
										@endif
										
                                        <div class="clearfix"></div>
                                    </div>
                                  </form>  
                                  </div>
                            </div>
                      <form onsubmit="return validateChechbox()" action="{{ url($action) }}" method="POST">
                        {{ csrf_field() }}
                        <div class="col-md-12 mb-2">
   
                          <div class="clearfix"></div>
                          <div class="form-check">
                          <input type="checkbox" class="form-check-input convictionType_step_2"  name="checkedbox" value="1" id="finalclickcheck"> 
                           <label class="form-check-label">{{Lang::get('affidavit.i_have_given_full_and_up_to_date_information_to_my_political_party')}}</label>
						   <div id="checkedbox_error"></div>
                        </div>    

                       </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12">
                                <a href="{{ url($menu_action.'affidavit/candidatedetails')}}" class="backBtn float-left ">{{Lang::get('affidavit.back')}}</a>

                                    <!-- <a href="{{url('Affidavit/MovableAssets')}}" type="submit" class="nextBtn float-right">{{Lang::get('affidavit.save')}} &amp; {{Lang::get('affidavit.next')}}</a> -->

                                    <button type="submit" class="nextBtn float-right">{{Lang::get('affidavit.save')}} &amp; {{Lang::get('affidavit.next')}}</button>

                                     <a href="{{url($menu_action.'affidavit/candidatedetails')}}" class="cencelBtn float-right mr-2">{{Lang::get('affidavit.cancel')}}</a>&nbsp; &nbsp; &nbsp;
                                     </div>
                        </div>
                    </div>
                  </form>  
            </div>
        </div>
    </div>
</div>
</main>
@endsection @section('script')
<script type="text/javascript" src="{{ asset('affidavit/js/remove_special_character.js') }}"></script>
<script type="text/javascript" src="{{ asset('affidavit/js/affidavit_validation.js') }}"></script>
<script type="text/javascript" src="{{ asset('affidavit/js/sweetalert.js') }}"></script>
<script type="text/javascript" src="{{ asset('affidavit/js/jquery-ui.js') }}"></script>
<script type="text/javascript">
$(document).ready(function() {
  $(".accordion_head").click(function() {
    if ($('.accordion_body').is(':visible')) {
      $(".accordion_body").slideUp(300);
      $(".plusminus").text('+');
    }
    if ($(this).next(".accordion_body").is(':visible')) {
      $(this).next(".accordion_body").slideUp(300);
      $(this).children(".plusminus").text('+');
    } else {
      $(this).next(".accordion_body").slideDown(300);
      $(this).children(".plusminus").text('-');
    }
  });

  $("body").on("click","#criminalRecord",function(e){
    e.preventDefault();
    var id = $(this).data("id");
    var token = $("meta[name='csrf-token']").attr("content");
    var url = e.target;

    swal({
        title: '{{Lang::get("affidavit.are_you_sure")}}',
        text: '{{Lang::get("affidavit.are_you_sure")}}',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{Lang::get("affidavit.yes_delete_it")}}'
    },
    function() {
    $.ajax({
          url: url.href, //or you can use url: "company/"+id,
          type: 'DELETE',
          data: {
            _token: token,
                id: id
        },
        success: function (response){
            $("#success").html('<label class="text-success">'+response.success+'</label>');
            window.location.href = response.url;
        }
     });
    });
      return false;
   });


  $("body").on("click","#convictionRecord",function(e){
    e.preventDefault();
    var id = $(this).data("id");
    var token = $("meta[name='csrf-token']").attr("content");
    var url = e.target;

    swal({
        title: '{{Lang::get("affidavit.are_you_sure")}}',
        text: '{{Lang::get("affidavit.you_want_to_delete_this_record")}}',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{Lang::get("affidavit.yes_delete_it")}}'
    },
    function() {
    $.ajax({
          url: url.href, //or you can use url: "company/"+id,
          type: 'DELETE',
          data: {
            _token: token,
                id: id
        },
        success: function (response){
            $("#success").html('<label class="text-success">'+response.success+'</label>');
            window.location.href = response.url;
        }
     });
    });
      return false;
   });

});

$('#date').datepicker({
    changeMonth: true,
    changeYear: true,
    dateFormat: 'dd-mm-yy',
    maxDate:'-0',
});
$('.calendar_date').click(function() {
		$("#date").focus();
	});
$('#date_of_order_conviction').datepicker({
    changeMonth: true,
    changeYear: true,
    dateFormat: 'dd-mm-yy',
    maxDate:'-0',
});
$('.calendar_conviction').click(function() {
		$("#date_of_order_conviction").focus();
	});


/*$(document).on('click', '.AddNewCases', function(){
  $('#courtcaseInformation').clone().appendTo('.newclonerow');
});*/  

$("#election_form").on('click', '#add_new_case', function(){
  $("#courtcaseInformation").slideToggle();
});

$("#case_of_conviction").on('click', '#add_new_case_convicted', function(){
  $("#step_2_convicted").slideToggle();
});

$(document).ready(function(){
	

  if($(".convictionType").is(":checked")) {
           var radioValue = $("input[name='convictionType']:checked").val();
           // alert(radioValue);
           if(radioValue == 1){
                $.ajax({
                    type: 'get',
					url: "{{ url($menu_action.'CriminalDataAvailableNull') }}",
                    datatype: 'JSON',
                    success: function(response){
                        if(response.status==200){
                            $('#convictionType_id_2').prop("disabled", true);
                        } else {
                            $('#convictionType_id_2').prop("disabled", false);
                        }
                    }
                });
            $("#courtcaseInformation").hide();
            $("#add_new_case").hide();
            $("#get_all_court_cases").hide();
            $("#not_applicable").prop("disabled", false);
            $("#add_new_case").hide();
            $("#get_criminal_cases_applicable_1").show();
           } else if(radioValue == 2){
                $.ajax({
                    type: 'get',
					url: "{{ url($menu_action.'CriminalDataAvailableNotNull') }}",
                    datatype: 'JSON',
                    success: function(response){
                        if(response.status==200){
                            $('#convictionType_id_1').prop("disabled", true);
                        } else {
                            $('#convictionType_id_1').prop("disabled", false);
                        }
                    }
                });
            $("#add_new_case").show();
            $("#get_all_court_cases").show();
            $("#add_new_case").show();
            $("#not_applicable").prop("disabled", true);
            $("#not_applicable_error").hide();
            $("#get_criminal_cases_applicable_1").hide();
           } else {
            alert('ERROR!.');
           }
        }

        if($(".convictionType_step_2").is(":checked")) {
           var radioValue = $("input[name='convictionType_step_2']:checked").val();
		   
		   
           if(radioValue == 1){
            $.ajax({
                    type: 'get',
					url: "{{ url($menu_action.'getconvictionDataAvailablenull') }}",
                    datatype: 'JSON',
                    success: function(response){
                        if(response.status==200){
                            $('#conviction_case_step_2').prop("disabled", true);
                        } else {
                            $('#conviction_case_step_2').prop("disabled", false);
                        }
                    }
                });

            $("#step_2_convicted").hide();
            $("#add_new_case_convicted").hide();
            $("#get_all_convition_cases").hide();
            $("#not_applicable_step_2").prop("disabled", false);
            $("#get_all_convition_cases_step_1").show();
           } else if(radioValue == 2){
                $.ajax({
                    type: 'get',
					url: "{{ url($menu_action.'getconvictionDataAvailablenotnull') }}",
                    datatype: 'JSON',
                    success: function(response){
                        if(response.status==200){
                            $('#conviction_case_step_1').prop("disabled", true);
                        } else {
                            $('#conviction_case_step_1').prop("disabled", false);
                        }
                    }
                });

            $("#add_new_case_convicted").show();
            $("#get_all_convition_cases").show();
            $("#not_applicable_step_2").prop("disabled", true);
            $("#not_applicable_error_step_2").hide();
            $("#get_all_convition_cases_step_1").hide();
           } else {
            //alert('ERROR!.');
           }
        }


 



	
    $("input[type='radio']").click(function(){
		
        if($(".court_framed_the_charge").is(":checked")) {
           var radioValue = $("input[name='court_framed_the_charge']:checked").val();
           if(radioValue == 1){
            $("#dateshowhide").show();
           } else if(radioValue == 2){
            $("#dateshowhide").hide();
           } else {
            alert('ERROR!.');
           }
        }

        if($(".convictionType").is(":checked")) {
           var radioValue = $("input[name='convictionType']:checked").val();
           // alert(radioValue);
           if(radioValue == 1){
                $.ajax({
                    type: 'get',
					url: "{{ url($menu_action.'CriminalDataAvailableNull') }}",
                    datatype: 'JSON',
                    success: function(response){
                        if(response.status==200){
                            $('#convictionType_id_2').prop("disabled", true);
                        } else {
                            $('#convictionType_id_2').prop("disabled", false);
                        }
                    }
                });
            $("#courtcaseInformation").hide();
            $("#add_new_case").hide();
            $("#get_all_court_cases").hide();
            $("#not_applicable").prop("disabled", false);
            $("#add_new_case").hide();
            $("#get_criminal_cases_applicable_1").show();
           } else if(radioValue == 2){
                $.ajax({
                    type: 'get',
					url: "{{ url($menu_action.'CriminalDataAvailableNotNull') }}",
                    datatype: 'JSON',
                    success: function(response){
                        if(response.status==200){
                            $('#convictionType_id_1').prop("disabled", true);
                        } else {
                            $('#convictionType_id_1').prop("disabled", false);
                        }
                    }
                });
            $("#add_new_case").show();
            $("#get_all_court_cases").show();
            $("#add_new_case").show();
            $("#not_applicable").prop("disabled", true);
            $("#not_applicable_error").hide();
            $("#get_criminal_cases_applicable_1").hide();
           } else {
            alert('ERROR!.');
           }
        }

        if($(".convictionType_step_2").is(":checked")) {
           var radioValue = $("input[name='convictionType_step_2']:checked").val();
		   
		   
           if(radioValue == 1){
            $.ajax({
                    type: 'get',
					url: "{{ url($menu_action.'getconvictionDataAvailablenull') }}",
                    datatype: 'JSON',
                    success: function(response){
                        if(response.status==200){
                            $('#conviction_case_step_2').prop("disabled", true);
                        } else {
                            $('#conviction_case_step_2').prop("disabled", false);
                        }
                    }
                });

            $("#step_2_convicted").hide();
            $("#add_new_case_convicted").hide();
            $("#get_all_convition_cases").hide();
            $("#not_applicable_step_2").prop("disabled", false);
            $("#get_all_convition_cases_step_1").show();
           } else if(radioValue == 2){
                $.ajax({
                    type: 'get',
					url: "{{ url($menu_action.'getconvictionDataAvailablenotnull') }}",
                    datatype: 'JSON',
                    success: function(response){
                        if(response.status==200){
                            $('#conviction_case_step_1').prop("disabled", true);
                        } else {
                            $('#conviction_case_step_1').prop("disabled", false);
                        }
                    }
                });

            $("#add_new_case_convicted").show();
            $("#get_all_convition_cases").show();
            $("#not_applicable_step_2").prop("disabled", true);
            $("#not_applicable_error_step_2").hide();
            $("#get_all_convition_cases_step_1").hide();
           } else {
            alert('ERROR!.');
           }
        }

        if($(".conviction_order_conviction").is(":checked")) {
           var radioValue = $("input[name='conviction_order_conviction']:checked").val();
           if(radioValue == 1){
            $(".yerornocheckorder").show();
           } else if(radioValue == 2){
           $(".yerornocheckorder").hide();
           } else {
            alert('ERROR!.');
           }
        }
    });

});


<?php 

//dd(count($get_criminal_cases_applicable));


if( count($get_criminal_cases) == 0 ) { ?>
$('#convictionType_id_1').prop("checked", true);
<?php } ?>

<?php if(count($get_conviction_cases) == 0) { ?>
$('#conviction_case_step_1').prop("checked", true);
<?php } ?>



function getDistrictList(val){
    var state_code = val;
    $.ajax({
        type: 'get',
        url: '{{ route('getdistricts') }}',
        data: 'state_code='+btoa(state_code),
        beforeSend: function(){
            $("#loader_dist").show();
        },success: function(response){
            var items="";
                items += "<option value=''>--{{Lang::get("affidavit.select_district") }}--</option>";
            $.each(response.result,function(index, item) {
                items+="<option value='"+$.trim(item.DIST_NO)+"'>"+$.trim(item.DIST_NAME)+" - "+$.trim(item.DIST_NAME_HI)+"</option>";
            });
            $("#district_name").html(items);
        },complete:function(response){
            $("#loader_dist").hide();
        }
    });
    return false;
}

function validateCriminalCase(){
  var flag = false;
  if($(".convictionType").is(":checked")) {
    var radioValue = $("input[name='convictionType']:checked").val();
    if(radioValue == 1){
    var not_applicable = $("#not_applicable").val().toUpperCase();
    if(not_applicable!="NOT APPLICABLE"){
      $("#not_applicable_error").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_if_you_are_not_convicted")}}</label>');
      $("#not_applicable").focus();
      flag = true;
    } else {
      $("#not_applicable_error").html('');
    }
  } else if(radioValue == 2){
    var fir_no = $("#fir_no").val();
    var st_name = $("#st_name").val();
    var district_name = $("#district_name").val();
    var police_station = $("#police_station").val();
    var police_station_address = $("#police_station_address").val();
    var case_number = $("#case_number").val();
    var name_court = $("#name_court").val();
    var acts = $("#acts").val();
    var section = $("#section").val();
    var short_description = $("#short_description").val();
    var court_framed_the_charge = $("#court_framed_the_charge").val();
    var date = $("#date").val();

//alert(fir_no);


    if($("#fir_no").val() == "") {
      $("#error_1").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_fir_no")}}</label>');
      $("#fir_no").focus();
      flag = true;
    } else {
      $("#error_1").html('');
    }

    if($("#st_name").val() == "0") {
      $("#error_2").html('<label class="text-danger">{{Lang::get("affidavit.please_select_state_name")}}</label>');
      $("#st_name").focus();
      flag = true;
    } else {
      $("#error_2").html('');
    }

    if($("#district_name").val() == "0") {
      $("#error_3").html('<label class="text-danger">{{Lang::get("affidavit.please_select_district_name")}}</label>');
      $("#district_name").focus();
      flag = true;
    } else {
      $("#error_3").html('');
    }

    if($("#police_station").val() == "") {
      $("#error_4").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_police_station_name")}}</label>');
      $("#police_station").focus();
      flag = true;
    } else {
      $("#error_4").html('');
    }

    if($("#police_station_address").val() == "") {
      $("#error_5").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_police_station_address")}}</label>');
      $("#police_station_address").focus();
      flag = true;
    } else {
      $("#error_5").html('');
    }

    if($("#case_number").val() == "") {
      $("#error_6").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_case_number")}}</label>');
      $("#case_number").focus();
      flag = true;
    } else {
      $("#error_6").html('');
    }

    if($("#name_court").val() == "") {
      $("#error_7").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_court_name")}}</label>');
      $("#name_court").focus();
      flag = true;
    } else {
      $("#error_7").html('');
    }

    if($("#acts").val() == "") {
      $("#error_8").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_acts")}}</label>');
      $("#acts").focus();
      flag = true;
    } else {
      $("#error_8").html('');
    }
    if($("#section").val() == "") {
      $("#error_9").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_section_name")}}</label>');
      $("#section").focus();
      flag = true;
    } else {
      $("#error_9").html('');
    }
    if($("#short_description").val() == "") {
      $("#error_10").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_short_description")}}</label>');
      $("#short_description").focus();
      flag = true;
    } else {
      $("#error_10").html('');
    }
    
    var checkValue = $("input[name='court_framed_the_charge']:checked").val();
      if(checkValue==1){
        if($("#date").val() == "") {
        $("#error_11").html('<label class="text-danger">{{Lang::get("affidavit.please_select_date")}}</label>');
        $("#date").focus();
        flag = true;
      } else {
        $("#error_11").html('');
      }
    }

  } else {
    //alert('500 Internal Server Error');
  }
}

  if(flag){
    return false;
  }
}

function validateConvictionCases(){
  var flag = false;
  if($(".convictionType_step_2").is(":checked")) {
    var radioValue = $("input[name='convictionType_step_2']:checked").val();
    if(radioValue == 1){
    var not_applicable = $("#not_applicable_step_2").val().toUpperCase();
    if(not_applicable != "NOT APPLICABLE"){
      $("#not_applicable_error_step_2").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_if_you_are_not_convicted")}}</label>');
      $("#not_applicable_step_2").focus();
      flag = true;
    } else {
      $("#not_applicable_error_step_2").html('');
    }
  } else if(radioValue == 2){
    var conviction_case_no = $("#conviction_case_no").val();
    var name_of_the_court_conviction = $("#name_of_the_court_conviction").val();
    var acts_conviction = $("#acts_conviction").val();
    var section_conviction = $("#section_conviction").val();
    var brief_description_conviction = $("#brief_description_conviction").val();
    var date_of_order_conviction = $("#date_of_order_conviction").val();
    var punishment_imposed_conviction = $("#punishment_imposed_conviction").val();
    

    if($("#conviction_case_no").val() == "") {
      $("#error_1_convicted").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_case_number")}}</label>');
      $("#conviction_case_no").focus();
      flag = true;
    } else {
      $("#error_1_convicted").html('');
    }

    if($("#name_of_the_court_conviction").val() == "") {
      $("#error_2_convicted").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_court_name")}}</label>');
      $("#name_of_the_court_conviction").focus();
      flag = true;
    } else {
      $("#error_2_convicted").html('');
    }

    if($("#acts_conviction").val() == "") {
      $("#error_3_convicted").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_acts")}}</label>');
      $("#acts_conviction").focus();
      flag = true;
    } else {
      $("#error_3_convicted").html('');
    }

    if($("#section_conviction").val() == "") {
      $("#error_4_convicted").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_section_name")}}</label>');
      $("#section_conviction").focus();
      flag = true;
    } else {
      $("#error_4_convicted").html('');
    }

    if($("#brief_description_conviction").val() == "") {
      $("#error_5_convicted").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_brief_description_conviction")}}</label>');
      $("#brief_description_conviction").focus();
      flag = true;
    } else {
      $("#error_5_convicted").html('');
    }

    if($("#date_of_order_conviction").val() == "") {
      $("#error_6_convicted").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_date_of_order_conviction")}}</label>');
      $("#date_of_order_conviction").focus();
      flag = true;
    } else {
      $("#error_6_convicted").html('');
    }

    if($("#punishment_imposed_conviction").val() == "") {
      $("#error_7_convicted").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_punishment_imposed")}}</label>');
      $("#punishment_imposed_conviction").focus();
      flag = true;
    } else {
      $("#error_7_convicted").html('');
    }

    var checkValue = $("input[name='conviction_order_conviction']:checked").val();
      if(checkValue==1){
        if($("#details_present_appeal_conviction").val() == "") {
        $("#error_8_convicted").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_status_of_appeal")}}</label>');
        $("#details_present_appeal_conviction").focus();
        flag = true;
      } else {
        $("#error_8_convicted").html('');
      }
    }

  } else {
    //alert('500 Internal Server Error');
  }
}

if(flag){
    return false;
  }

}

function validateChechbox(){
  var flag = false;
  if($("#finalclickcheck").prop('checked') == false){
    $("#checkedbox_error").html('<label class="text-danger">{{Lang::get("affidavit.please_select_the_above_checkbox") }}</label>');
    var flag = true;
  } else {
    $("#checkedbox_error").html('');
  }
  if(flag){
    return false;
  }
}
</script>
@endsection
