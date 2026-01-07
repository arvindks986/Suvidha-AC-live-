@extends( (Auth::user()->role_id != '19') ? 'layouts.theme' : 'admin.layouts.ac.theme')
@section('title', 'Affidavit Cadidate Details') @section('content')
<style type="text/css">
.affidavit_nav .step-current a,.affidavit_nav .step-success a{
    color:#fff!important;
}
.affidavit_nav a{
    color:#999!important;
}
.error {
    font-size: 12px;
    color: red;
}
.step-wrap.mt-4 ul li {
    margin-bottom: 21px;
}
.panel-heading.active {
    background-color: #e8e8e8;
}
#img_error_img {    
    float: left;
   width: 100%
}
#img_error_img p{    
    line-height: 1;
    margin-bottom: 1px;
    margin-top: 3px;
    text-align: center;
    width: 100%;
    margin-left: -29px;

}
div#political_party_loding {
    position: absolute;
    right: 0;
    margin-right: 1px;
    margin-top: -48px;
}
.input-group-append.epicsearch {
    position: absolute;
    right: 0;
    margin-top: -34px;
    margin-right: 17px;
}
tr#relation1 {
    /*display: none; */
}

.other_css {
    width: 60% !important;
}
.for_width_css {
    width: 40% !important;
    float: left;
}
.btn {
    font-size: 12px;
}
.inputLeft{
  width: 90px!important;
  float: left;
}
.inputRight{
  width: calc(100% - 105px)!important;
  float: right;
}
.underLine{
  margin: 7px 2px 2px 4px;
  float: left;
}
/* input#pan {
    text-transform: uppercase;
} */
.grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    border-top: 1px solid black;
    border-right: 1px solid black;
}
.grid > span {
    padding: 8px 4px;
    border-left: 1px solid black;
    border-bottom: 1px solid black;
}

.browse_image_outer .btn{
    width: 100px;
    float: left;
}
.bdr-0{
  border:0!important 
}

.accordion_body {
    
}
.plusminus {
  float: right;
}
.panel-title {
    font-size: 14px;
}

.panel-title > a {
    display: block;
    padding: 15px;
    text-decoration: none;
}

.more-less {
    float: right;
    color: #212121;
}
div#checkbtn_loding {
    position: absolute;
    right: 0;
    margin-top: -52px;
}

.panel-default>.panel-heading {
    color: #333;
    background-color: #f5f5f5;
    border-color: #ddd;
}
.panel-group .panel-heading {
    border-bottom: 0;
}
.card-header h4 {
    font-size: 18px;
}
.removecss{cursor: pointer;}
.file-frame img {
    width: 100px;
    height: 100px;
    float: left;
}
.file {
    float: right;
    background-color: #5342bf;
    border-color: #5342bf;
    color: #fff;
    width: 100%;
}
.file-frame-error {
    border: 2px solid red;
}
input#otp {
    width: 50%;
    float: left;
}
.disableCss {
    cursor: no-drop;
}
span#successOPTMsg_email {
    /*position: absolute;*/
}

</style>
<link rel="stylesheet" href="{{ asset('admintheme/css/jquery-ui.css') }}" id="theme-stylesheet" />
<link rel="stylesheet" href="{{ asset('affidavit/css/affidavit.css') }}" id="theme-stylesheet" />
<link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }} " type="text/css" />
<link rel="stylesheet" href="{{ asset('appoinment/css/custom.css') }} " type="text/css" />
<link rel="stylesheet" href="{{ asset('appoinment/css/custom-dark.css') }} " type="text/css" />
<link rel="stylesheet" href="{{ asset('affidavit/css/sweetalert.css') }} " type="text/css" />
<main role="main" class="inner cover mb-3">
    <style type="text/css">
        .alert.alert-danger ul li {
            list-style-type: none;
            text-align: left;
        }
    </style>
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
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $index=>$error)
                            <li><span style="color: #D04A8A;margin-right: 10px;">{{ $index+1 }}</span>{{ $error }}</li>
                        @endforeach
                    </ul>
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
                <li class="step-current"><b>&#10004;</b><span><a href="{{url($menu_action.'affidavit/candidatedetails')}}">{{Lang::get('affidavit.candidate_details') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'affidavit/pending-criminal-cases')}}">{{Lang::get('affidavit.court_cases') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'Affidavit/MovableAssets')}}">{{Lang::get('affidavit.movable_assets') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'immovable-assets')}}">{{Lang::get('affidavit.immovable_assets') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'liabilities')}}">{{Lang::get('affidavit.liabilities') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'Profession')}}">{{Lang::get('affidavit.profession') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'education')}}">{{Lang::get('affidavit.education')}}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'preview')}}">{{Lang::get('affidavit.preview_finalize') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'part-a-detailed-report')}}">{{Lang::get('affidavit.reports') }}</a></span></li>
            </ul>
        </div>
    <section>
        <div class="container-fluid">
    <div class="card">
        <div class="card-header text-center bdr-0">
            <!-- <div class="col-sm-12 text-left"><input type="checkbox" name="edit details" value="1" /> &nbsp; Click to edit details</div> -->
            <div class="">
                <h4>{{Lang::get('affidavit.form2b')}}</h4>
                <div>{{Lang::get('affidavit.see_rule_4') }}</div>
                <div class="mt-2">{{Lang::get('affidavit.affidavit_to_be_furnished_by_the_candidate_alongwith') }}</div>
            </div>
        </div>
        <form id="election_form" method="POST" onsubmit="return validateCandidate()" action="{{ url($action) }}" autocomplete="off" enctype="multipart/form-data" accept-charset="ISO-8859-1">{{ csrf_field() }}
        <input type="hidden" name="TblId" value="{{ Session::get('affidavit_id') }}" id="getTblId">
        <div class="card-body">
            <div class="accordion_head candidate_slide">{{Lang::get('affidavit.candidate_details') }}<span class="plusminus">+</span></div>
            <div class="accordion_body candidate_details_slide" style="display: none;">
                <div class="part-1">
				
				
				<div class="form-group ">                 
                    <div class="browse_image_outer">
                      <div class="avatar-upload btn file-frame">
                              <img src="@if(@$session_data->cimage) {{url($session_data->cimage)}} @elseif(@$nomination_data->image) {{url($nomination_data->image)}} @else {{url('img/vendor/avtar.jpg')}} @endif" class="img-responsive">
                              <button class="file btn" type="button">Browse <i class="fa fa-upload"></i></button>
                              <input type="hidden" name="userImage" id="file" class="image" value="@if(@$session_data->cimage) {{$session_data->cimage}} @elseif(@$nomination_data->image)  {{@$nomination_data->image}} @endif">
							  @if ($errors->has('userImage'))
								<span class="text-danger">{{ $errors->first('userImage') }}</span>
							  @endif
								<div id="img_error_img"></div>
                      </div>
                    </div>
                  
                </div>
				
				

                  <!--  <div class="form-group">
                            <div class="fullwidth float-right" style="width: 100%;">
                                <div class="browse_image_outer">
                                    <div class="avatar-upload btn file-frame">
                                        <img src="@if(@$session_data->cimage) {{ asset('affidavit/uploads/').'/'.$session_data->cimage }} @endif" class="img-responsive" />
                                        <!-- <button class="file btn" type="button">Browse <i class="fa fa-upload"></i></button> -->
                                      <!--  <input type="file" name="userImage" value="" id="file" accept="image/*" class="file inputFile">
                                         <input type="hidden" name="image_has" class="image"  value="@if(@$session_data->cimage){{$session_data->cimage}}@endif" />
                                    </div>
                                    @if ($errors->has('userImage'))
                                    <span class="text-danger">{{ $errors->first('userImage') }}</span>
                                    @endif
                                    <span id="img_error_img"></span>
                                    <!-- <small>(Only jpeg/jpg/png allow & max size 50 Kb only)</small> -->
                               <!-- </div>
                            </div>
                        </div> -->

                        <fieldset class="py-4 px-5 mb-4 mt-5">
                            <legend>{{Lang::get('affidavit.election') }}</legend>

                            <div class="row">
                                <div class="col-sm-6 col-12">
                                    <div class="form-group mt-2 mb-2">
                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.for_election_to') }}</label>
                                        <input type="text" value="General Election to Legislative Assembly" class="form-control nomination-field-2" readonly="readonly" />
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="form-group mt-2 mb-2">
                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.for_state') }}</label>
                                        <input type="text" placeholder="" value="@if(@$session_data->st_code){{ getstatebystatecode(@$session_data->st_code)->ST_NAME }} @endif" readonly="" class="form-control" />
                                    </div>
                                </div>
								
								
								
                                <div class="col-sm-6 col-12">
                                    <div class="form-group mt-2 mb-2">
                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.for_district') }}</label>
                                        <input value="@if(@$session_data->st_code)<?php $dist_name = App\Http\Controllers\Affidavit\CommanFunctionController::getDistName(@$session_data->st_code,@$session_data->ac_no); ?>{{ $dist_name->DIST_NAME_EN }}@endif" readonly="" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group mt-2 mb-2">
                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.form_name_of_the_consituency') }}</label>
                                        <input type="text" readonly="" value="@if(@$session_data->st_code){{ getacbyacno(@$session_data->st_code,@$session_data->ac_no)->AC_NAME }}@endif" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="form-group mt-2 mb-2">
                                        <label for="" class="">{{Lang::get('affidavit.name_of_the_candidate') }}</label>
                                        <input readonly="" value="{{ @$session_data->cand_name }}"class="form-control nomination-field-2"/>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="form-group mt-2 mb-2">
                                        <label for="">
                                            <span class="d-block">
											<input type="radio" name="relation_name" value="1" <?php if(@$session_data->relation_name == '1') echo 'checked="checked"'; ?> /> {{Lang::get('affidavit.son_of')}} 
											<input type="radio" name="relation_name" value="2" <?php if(@$session_data->relation_name == '2') echo 'checked="checked"'; ?> /> {{Lang::get('affidavit.daughter_of')}} 
											<input type="radio" name="relation_name" value="3" <?php if(@$session_data->relation_name == '3') echo 'checked="checked"'; ?> /> {{Lang::get('affidavit.wife_of')}}</span>
                                        </label>
                                        <input type="text" onkeypress="return blockSpecialChar_name(event)" name="son_daughter_wife_of" id="son_daughter_wife_of" class="form-control" value="{{$user_profile_data->father_name }}">
                                        @if ($errors->has('son_daughter_wife_of'))
                                          <span class="text-danger">{{ $errors->first('son_daughter_wife_of') }}</span>
                                        @endif
                                        <span id="error_cand_1"></span>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="form-group mt-2 mb-2">
                                        <label for="" class=""><input type="radio" name="dateofborth_remember" id="rememberDOB1" class="dateofborth_remember" value="1"/> {{Lang::get('affidavit.i_remember_my_date_of_birth')}}</label>

                                        <div class="input-group mb-3">        
                                        <div class="clearfix"></div>
                                        <input type="text" id="dateofbirth" name="date_of_birth" class="form-control dateofbirth" placeholder="dd-mm-yy" readonly="">
                                        <div class="input-group-append">
                                          <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                      </div>
                                        @if ($errors->has('date_of_birth'))
                                          <span class="text-danger">{{ $errors->first('date_of_birth') }}</span>
                                        @endif
                                        <span id="error_cand_dbo"></span>
                                    </div>
                                </div>
                               
                                <div class="col-sm-6 col-12">
                                    <div class="form-group mt-2 mb-2">
                                        <label for="" class="">
                                            <input type="radio" name="dateofborth_remember" value="2" class="dateofborth_remember" <?php if($user_profile_data->age!=""){ echo "checked"; } ?> /> {{Lang::get('affidavit.i_do_not_remember_my_date_of_birth')}}</label>
                                          
                                        <input type="text" name="age" id="age" placeholder="enter your Age" class="form-control removespecialcharacter" maxlength="2" value="{{ $user_profile_data->age }}">

                                        @if ($errors->has('age'))
                                          <span class="text-danger">{{ $errors->first('age') }}</span>
                                        @endif
                                        <span id="error_cand_age"></span>
                                        
                                    </div>
                                </div>
                                
                                <div class="col-sm-6 col-12">
                                    <div class="form-group mt-2 mb-2">
                                        <label for="" class="">{{Lang::get('affidavit.address_mention_full_postel_address')}}</label>
                                        <textarea name="postal_address" id="postal_address" onkeypress="return blockSpecialChar_address(event)" placeholder="Please enter your adddress" class="form-control">{{ $user_profile_data->address }}, {{ getdistrictbydistrictno($user_profile_data->state,$user_profile_data->district)->DIST_NAME }}, {{ getstatebystatecode($user_profile_data->state)->ST_NAME }}</textarea>
                                        @if ($errors->has('postal_address'))
                                          <span class="text-danger">{{ $errors->first('postal_address') }}</span>
                                        @endif
                                        <span id="error_cand_address"></span>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="py-4 px-5 mt-2 mb-4">
                            <legend>{{Lang::get('affidavit.party_information')}}</legend>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input candidate_setup_party" type="radio" name="candidate_setup_party" id="exampleRadios1" value="1" checked="" />
                                        <label class="form-check-label" for="exampleRadios1">
                                            {{Lang::get('affidavit.i_am_candidate_setup_by_the_political_party')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                                <div class="row">


                                <div class="col-sm-6 ">
                                    <div class="form-group mt-2 mb-2">
                                        <label for="" class="">{{Lang::get('affidavit.select_party_type')}}:</label>
                                        <select class="form-control" onchange="getPoliticalParty(this.value)" name="party_type" id="party_type">
                                            <option value="">--{{Lang::get('affidavit.select_party')}}--</option>
                                            <option value="N" <?php if((@$session_data->partytype == 'N') || (@$nomination_data['PARTYTYPE']->PARTYTYPE == 'N')) echo 'selected="selected"'; ?>>{{Lang::get('affidavit.national_party')}}</option>
                                            <option value="S" <?php if((@$session_data->partytype == 'S') || (@$nomination_data['PARTYTYPE']->PARTYTYPE == 'S')) echo 'selected="selected"'; ?>>{{Lang::get('affidavit.state_party')}}</option>
                                            <option value="U" <?php if((@$session_data->partytype == 'U') || (@$nomination_data['PARTYTYPE']->PARTYTYPE == 'U')) echo 'selected="selected"'; ?>>{{Lang::get('affidavit.unrecognized_party')}}</option>
                                        </select>
                                        @if ($errors->has('party_type'))
                                          <span class="text-danger">{{ $errors->first('party_type') }}</span>
                                        @endif
                                        <span id="error_party_type_address"></span>
                                        <div id='political_party_loding' style='display: none;'>
                                            <img src="{{ asset('img/loading-img.gif') }}" width='60px' height='60px'>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-12">
                                    <div class="form-group mt-2 mb-2">
                                        <label for="" class="">{{Lang::get('affidavit.name_of_the_political_party')}}:</label>
                                        <select name="political_party" id="political_party" class="form-control">
											@if(isset($session_data['partyData']) && $session_data['partyData'])
												@foreach($session_data['partyData'] as $data)
												<option value="{{$data->CCODE}}" <?php if(($session_data->partyabbre == $data->CCODE) || (@$nomination_data->party_id == $data->CCODE)) echo 'selected="selected"'; ?>>{{$data->PARTYNAME}} - {{$data->PARTYHNAME}}</option>
												@endforeach
											@elseif(isset($nomination_data['partyData']) && $nomination_data['partyData'])
												@foreach($nomination_data['partyData'] as $data)
												<option value="{{@$data->CCODE}}" <?php if(@$nomination_data->party_id == @$data->CCODE) echo 'selected="selected"'; ?>>{{@$data->PARTYNAME}} - {{@$data->PARTYHNAME}}</option>
												@endforeach
											@else
												<option value="">-{{Lang::get('affidavit.select_party_name')}}-</option>
											@endif
										
                                            
                                        </select>
                                        <span id="political_party_error"></span>
                                        @if ($errors->has('political_party'))
                                          <span class="text-danger">{{ $errors->first('political_party') }}</span>
                                        @endif

                                    </div>
                                </div>

                                <div class="col-sm-6 col-12">
                                    <div class="form-check mt-3">
                                        <input class="form-check-input candidate_setup_party" type="radio" name="candidate_setup_party" id="exampleRadios2" value="2" <?php if(empty(@$session_data->partyabbre) && empty(@$nomination_data->party_id)) { echo 'checked'; } ?> />
                                        <label class="form-check-label" for="exampleRadios2">
                                            {{Lang::get('affidavit.i_am_candidate_contesting_as_an_independent_candidate')}}
                                        </label>
                                    </div>
                                </div>


                            </div>
                          
                        </fieldset>

                        <fieldset class="py-4 px-5 mt-2 mb-4">
                            <legend>{{Lang::get('affidavit.my_name_is_enrolled_in')}}</legend>
                            <div class="row">
                                <div class="col-sm-6 col-12">
                                    <div class="form-group mt-2 mb-2">
                                        <label for="" class="">{{Lang::get('affidavit.state')}}:</label>
                                        @if(Session::get('st_code_by_epic')!="")
                                            <input readonly="readonly" type="text" name="state_name_epic" id="state_name_epic" class="form-control" value="{{ Session::get('st_name_by_epic') }}">

                                            <input type="hidden" id="st_code_from_epic" name="st_code_from_epic" value="{{ Session::get('st_code_by_epic') }}">

                                        @else
                                            <input readonly="readonly" type="text" name="state_name_epic" id="state_name_epic" class="form-control" value="{{ getstatebystatecode($user_profile_data->state)->ST_NAME }}">

                                            <input type="hidden" id="st_code_from_epic" name="st_code_from_epic" value="{{ $user_profile_data->state }}">
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="form-group mt-2 mb-2">
                                        <label for="" class="">{{Lang::get('affidavit.district')}}:</label>
                                        @if(Session::get('dist_no_by_epic')!="")
                                            <input readonly="readonly" type="text" name="district_name_epic" id="district_name_epic" class="form-control" value="{{ Session::get('dist_name_by_epic') }}">
                                            <input type="hidden" id="district_code_from_epic" name="district_code_from_epic" value="{{ Session::get('dist_no_by_epic') }}">
                                        @else
                                            <input readonly="readonly" type="text" name="district_name_epic" id="district_name_epic" class="form-control" value="{{ getdistrictbydistrictno($user_profile_data->state,$user_profile_data->district)->DIST_NAME }}">
                                            <input type="hidden" id="district_code_from_epic" name="district_code_from_epic" value="{{ $user_profile_data->district }}">
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 col-12">
                                    <div class="form-group mt-2 mb-2">
                                        <label for="" class="">{{Lang::get('affidavit.const')}}:</label>
                                        @if(Session::get('ac_no_by_epic')!="")
                                            <input readonly="readonly" value="{{ Session::get('ac_name_by_epic') }}" type="text" name="ac_name_epic" id="ac_name_epic" class="form-control">
                                            <input type="hidden" id="ac_code_from_epic" name="ac_code_from_epic" value="{{ Session::get('ac_no_by_epic') }}">
                                        @else
                                            <input readonly="readonly" type="text" name="ac_name_epic" id="ac_name_epic" class="form-control" value="{{ getacbyacno($user_profile_data->state,$user_profile_data->ac)->AC_NAME }}">

                                            <input type="hidden" id="ac_code_from_epic" name="ac_code_from_epic" value="{{ $user_profile_data->ac }}">
                                        @endif                                    
                                    </div>
                                </div>

                                <div class="col-sm-6 col-12">
                                    <div class="form-group mt-2 mb-2">
                                        <label for="" class=""> {{Lang::get('affidavit.part_number')}}:</label>
                                        @if(Session::get('part_number_by_epic')!="")
                                            <input maxlength="10" type="text" value="{{ Session::get('part_number_by_epic') }}" class="form-control removespecialcharacter" id="part_number" name="part_number" readonly="readonly">
                                        @else
                                            <input maxlength="10" type="text" value="{{ $user_profile_data->part_no }}" class="form-control removespecialcharacter" id="part_number" name="part_number" readonly="readonly">
                                        @endif    
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="form-group mt-2 mb-2">
                                        <label for="" class="">{{Lang::get('affidavit.serial_no')}}:</label>
                                        @if(Session::get('serial_no_by_epic')!="")
                                            <input maxlength="10" type="text" value="{{ Session::get('serial_no_by_epic') }}" class="form-control removespecialcharacter" id="serial_no" name="serial_no" readonly="readonly">
                                        @else
                                            <input maxlength="10" type="text" value="{{ $user_profile_data->serial_no }}" class="form-control removespecialcharacter" id="serial_no" name="serial_no" readonly="readonly">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="py-4 px-5 mt-2 mb-4">
                            <legend>{{Lang::get('affidavit.my_contact')}}</legend>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group mt-2 mb-2">
                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.mobile_number')}}</label>

                                        <div class="clearfix"></div>

                                            <div class="inputLeftSide">
                                        <input maxlength="10" type="text" value="{{ $user_profile_data->mobile }}" name="mobile_number" class="form-control removespecialcharacter" placeholder="Enter mobile number" id="mobile_number_check">
                                        @if ($errors->has('mobile_number'))
                                          <span class="text-danger">{{ $errors->first('mobile_number') }}</span>
                                        @endif
                                        <span id="verify_img"></span>
                                        <span id="verified_msg"></span>
                                        <span id="officer_id_js_error"></span>
                                        <div id='checkbtn_loding' style='display: none;'>
                                            <img src="{{ asset('img/loading-img.gif') }}" width='70px' height='70px'>
                                        </div>
                                        <input style="display: none;" type="text" name="otp" class="form-control mt-2 removespecialcharacter" id="otp" maxlength="6">
                                        <span id="otp_js_error"></span>
                                        &nbsp;&nbsp;<input style="display: none;" type="button" name="verifyOTP" value="Verify OTP" class="btn btn-success mt-2" id="verifyOTP" >
                                    </div>
                                    <div id="successOPTMsg"></div>

                                </div>

                                <div class="inputRightSide">
                                     <button id="VerifyMobileNo" class="btn btn-sm btn-success" type="button">{{Lang::get('affidavit.verify_mobile_no')}}</button>
                                    <input type="hidden" name="hidden_mobile_no" id="hidden_mobile_no" value="{{ $user_profile_data->mobile }}">
                                    </div>
                                </div>
                              <div class="col-sm-6">
                                    <div class="form-group mt-2 mb-2">
                                        <label for="" class="">{{Lang::get('affidavit.landline_number')}}</label>
                                        <div class="clearfix"></div>
                                        <input maxlength="4" type="text" class="form-control inputLeft removespecialcharacter" name="std_code" placeholder="st code" value="<?php if(@$session_data->std_code!="") { echo $session_data->std_code; } ?>">
                                        <span class="underLine">-</span>
                                        <input maxlength="8" class="form-control inputRight removespecialcharacter" type="text" name="landline_number" placeholder="landline no." value="<?php if(@$session_data->phoneno_2!="") { echo $session_data->phoneno_2; } ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">

                                    <div class="form-check mt-2 mb-2">
                                        <input class="form-check-input email_account" type="radio" name="email_account" value="1" checked="" />
                                        <label class="form-check-label" for="exampleRadios3">
                                            {{Lang::get('affidavit.i_have_an_e_mail_account')}}
                                        </label>
                                    </div>
                                   
                                         <!-- email section -->
                                        
                                    <div id="emailID" class="form-group mt-2 mb-2">
                                        <label for="" class="lbl-mandry">{{Lang::get('affidavit.enter_e_mail_id')}}</label>
                                        <div class="clearfix"></div>

                                     <div class="inputLeftSide">
                                        <input type="email" name="email_address" id="email_address" class="form-control" placeholder="enter emailid" value="{{ $user_profile_data->email }}" />
                                        @if ($errors->has('email_address'))
                                          <span class="text-danger">{{ $errors->first('email_address') }}</span>
                                        @endif
                                        <span id="email_id_js_error"></span>
                                        <span id="email_verify_img"></span>
                                        <span id="successOPTMsg_email"></span>
                                        <input style="display: none;" type="text" name="otp_emailid" class="form-control mt-2 removespecialcharacter" id="otp_emailid" maxlength="6">
                                        <span id="otp_js_error_email"></span>
                                        &nbsp;&nbsp;<input style="display: none;" type="button" name="verifyOTPEmail" value="Verify OTP" class="btn btn-success mt-2" id="verifyOTPEmail">
                                        <span id="error_email_id"></span>
                                    </div>


                                    </div>


                                    <div class="inputRightSide">
                                        <!-- <button id="VerifyEmailId" class="btn btn-success btn-sm" type="button">{{Lang::get('affidavit.verify_e_mail_id')}}</button> -->
                                        <input type="hidden" name="hidden_email_id" id="hidden_email_id" value="{{ $user_profile_data->email }}">
                                    </div>
                                    <!-- email section -->
                                     
                                   

                                </div>
                                
                              

                                <div class="col-sm-6">
                                    <div class="form-check mt-2 mb-2">
                                        <input class="form-check-input email_account" type="radio" name="email_account" value="2" <?php if(empty($session_data->emailid) && empty($user_profile_data->email)) { echo 'checked'; } ?> />
                                        <label class="form-check-label" for="exampleRadios4">
                                            {{Lang::get('affidavit.i_do_not_have_an_e_mail_account')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="py-4 px-5 mt-2 mb-4">
                            <legend>{{Lang::get('affidavit.self_spouse_dependent_huf_details')}}</legend>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive">
                                        <table class="table table-info table-bordered purpleTable" id="userTableDependentDetails">
                                            <thead>
                                                <tr>
                                                    <th>{{Lang::get('affidavit.relation_type')}}</th>
                                                    <th>{{Lang::get('affidavit.relation')}}</th>
                                                    <th>{{Lang::get('affidavit.name')}}</th>
                                                    <th>{{Lang::get('affidavit.action')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr id="lefttr">
                                                    <td>{{Lang::get('affidavit.self')}}</td>
                                                    <td>{{Lang::get('affidavit.self')}}</td>
                                                    <td>{{ Session::get('cand_name') }}</td>
                                                    <td>&nbsp;</td>
                                                </tr>
                                                @if($get_candidate_detail_data)
                                                    @foreach($get_candidate_detail_data as $pan_row)
                                                        <tr id="relation{{$pan_row->id}}">
                                                            <td>{{$pan_row->relation_type}}</td>
                                                            <td>{{$pan_row->relation}}</td>
                                                            <td>{{$pan_row->name}}</td>
                                                            <td><input type='button' value='Edit' class='editpersonalrecord btn btn-success btn-sm mt-1 mr-1' data-id='{{$pan_row->id}}' data-relation_type_code='{{$pan_row->relation_type_code}}' data-relation_code='{{$pan_row->relation_code}}'  data-name='{{$pan_row->name}}'>
															 @if(Auth::user()->role_id != '19')
															 <a href="javascript:void(0)" class="btn btn-info btn-danger btn-sm" title="{{Lang::get('affidavit.delete')}}" onclick="javascript:delete_spouse({{$pan_row->id}})">
															 <i class="fa fa-times"></i> {{Lang::get('affidavit.delete')}}</a>
															 @endif
															
															</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
												
												
												@if(Auth::user()->role_id != '19')
												
                                                <tr>
                                                  <td>
                                                    <select class="form-control" name="relation_type" id="relation_type">
                                                        <option value="">-{{Lang::get('affidavit.select')}}-</option>
                                                        @forelse($get_relation_type as $relation_type)
                                                            <option value="{{ $relation_type->relation_type_code }}">{{ ucfirst($relation_type->relation_type) }} - {{ $relation_type->relation_type_hi }}</option>
                                                        @empty
                                                            <option>{{Lang::get('affidavit.no_record_found')}}</option>
                                                        @endforelse
                                                    </select>
                                                    <span class="error_1"></span>
                                                  </td>
                                                  <td>
                                                    <select class="form-control" name="relation" id="relation">
                                                        <option value="">-{{Lang::get('affidavit.select')}}-</option>
                                                        @forelse($get_cand_relation as $cand_relation)
                                                            <option value="{{ $cand_relation->relation_code }}">{{ ucfirst($cand_relation->relation) }} - {{ $cand_relation->relation_hi }}</option>
                                                        @empty
                                                            <option>{{Lang::get('affidavit.no_record_found')}}</option>
                                                        @endforelse
                                                    </select>
                                                    <span class="error_2"></span>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="name" id="name" class="form-control" onkeypress="return blockSpecialChar_name(event)">
                                                        <span class="error_3"></span>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-success btn-sm mt-1 mr-1" id="add">{{Lang::get('affidavit.save')}}</button>
                                                    </td>
                                                </tr>
												
												@endif
												
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="py-4 px-5 mt-2 mb-4">
                            <legend>{{Lang::get('affidavit.social_media_account')}}:</legend>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive">
                                        <table class="table table-info table-bordered purpleTable" id="socialMediaTable">
                                            <thead>
                                                <tr class="table-info">
                                                    <!-- <th scope="row">S.No.</th> -->
                                                    <th>{{Lang::get('affidavit.social_media')}}</th>
                                                    <th>{{Lang::get('affidavit.account')}}</th>
                                                    <th>{{Lang::get('affidavit.edit')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody> 
                                                
                                                @if($cand_social_account)
                                                    @foreach($cand_social_account as $rows)
                                                        <tr id="media_accounts{{$rows->id}}">
                                                            <td id="media_account{{$rows->id}}">{{$rows->media_account}}</td>
                                                            <td id="other_account_name{{$rows->id}}">{{$rows->other_account_name}}</td>
                                                            <td><input type='button' value='{{Lang::get('affidavit.edit')}}' class='editrecord btn btn-success btn-sm mt-1 mr-1' data-id='{{$rows->id}}' data-media_account='{{$rows->media_account}}' data-other_account_name='{{$rows->other_account_name}}' data-social_media_code='{{$rows->social_media_code}}'></td>
                                                        </tr>
                                                    @endforeach
                                                @endif 


												@if(Auth::user()->role_id != '19')
												<tr>
                                                 <!--  <td>1</td> -->
                                                  <td><select class="form-control" name="social_media" id="social_media">
                                                        <option value="">-{{Lang::get('affidavit.please_select_social_media')}}-</option>
                                                        @forelse($get_social_media as $social_media)
                                                            <option value="{{ strtolower($social_media->social_media_code) }}">{{ ucfirst($social_media->social_media_name) }} - {{ $social_media->social_media_name_hi }}</option>
                                                        @empty
                                                            <option>{{Lang::get('affidavit.no_record_found')}}</option>
                                                        @endforelse
                                                    </select><span class="error_4"></span>
                                                <input type="text" id="other_social_media_account" name="other_social_media_account" class="form-control" style="display: none;">
                                                </td>
                                                  <td><input type="text" name="social_account" id="social_account" class="form-control" onkeypress="return blockSpecialChar_address(event)">
                                                    <span class="error_5"></span></td>
                                                  <td>
                                                        <button type="button" class="btn btn-success btn-sm mt-1 mr-1" id="socila_media_add">{{Lang::get('affidavit.save')}}</button>
                                                    </td>
                                                </tr>
												@endif

												
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                </div>
            </div>
            <!-- accrodion_body -->
            <!-- End Of part-1 Div -->
            <div class="accordion_head">{{Lang::get('affidavit.pan_details')}} <span class="plusminus">+</span></div>
            <div class="accordion_body" style="display: none;">
                <table class="table table-info table-bordered table-striped purpleTable " id="userTablePAN">
                    <thead>
                        <tr>
                            <th colspan="12"><strong>(2) {{Lang::get('affidavit.details_of_pan_and_status_of_filling_of_income_tax_return')}}:</strong></th>
                        </tr>
                        <tr class="table-info">
                          <th rowspan="2">{{Lang::get('affidavit.name')}}</th>
                          <th rowspan="2">{{Lang::get('affidavit.relation')}}</th>
                          <th rowspan="2">{{Lang::get('affidavit.permanent_account_number')}}</th>
                          <th rowspan="2">{{Lang::get('affidavit.the_financial_year_which_the_last_income_tax_return_has_been_filed')}}</th>
                          <th colspan="5" align="center" class="text-center">{{Lang::get('affidavit.toal_income_shown_in_income_tax_retuen')}}</th>          
                          <th rowspan="2">{{Lang::get('affidavit.action')}}</th>
                        </tr>
                        <tr>
                            <th>{{Lang::get('affidavit.financial_year')}} 2024-2025</th>
                            <th>{{Lang::get('affidavit.financial_year')}} 2023-2024</th>
                            <th>{{Lang::get('affidavit.financial_year')}} 2022-2023</th>
                          <th>{{Lang::get('affidavit.financial_year')}} 2021-2022</th>
                          <th>{{Lang::get('affidavit.financial_year')}} 2020-2021</th>
                            <!--<th>{{Lang::get('affidavit.financial_year')}} 2019-2020</th>
                         <th>{{Lang::get('affidavit.financial_year')}} 2018-2019</th> -->
                          <!-- <th>{{Lang::get('affidavit.financial_year')}} 2017-2018</th> -->
                        </tr>
                   </thead>

                    @if($getPANData)
                        @foreach($getPANData as $index=>$pan_row)
                            <tr id="pan_row_relation{{$pan_row->id}}">
                                <td>{{ $pan_row->name }}</td>
                                <td>{{ $pan_row->relation }}</td>
                                <td nowrap="nowrap"><input type="text" class="form-control" value="{{ $pan_row->pan}}" disabled></td>    
                                   
                                <td width="145"><input type="text" value="{{ $pan_row->financial_year }}" class="form-control removespecialcharacter" disabled></td>
                                <td><input type="text" value="{{ $pan_row->financialyr1 }}" class="form-control removespecialcharacter" disabled></td>
                                <td><input type="text" value="{{ $pan_row->financialyr2 }}" class="form-control removespecialcharacter" disabled></td>
                                <td><input type="text" value="{{ $pan_row->financialyr3 }}" class="form-control removespecialcharacter" disabled></td>
                                <td><input type="text" value="{{ $pan_row->financialyr4 }}" class="form-control removespecialcharacter" disabled></td> 
                                <td><input type="text" value="{{ $pan_row->financialyr5 }}" class="form-control" disabled></td>
                                <td><input type='button' value='{{Lang::get('affidavit.edit')}}' class='editpanrecord btn btn-success btn-sm mt-1 mr-1' data-id='{{$pan_row->id}}' data-name='{{$pan_row->name}}' data-relation_type_code='{{$pan_row->relation_type_code}}' data-relation_code='{{$pan_row->relation_code}}'  data-pan='{{$pan_row->pan}}' data-financial_year='{{$pan_row->financial_year}}' data-financialyr1='{{$pan_row->financialyr1}}' data-financialyr2='{{$pan_row->financialyr2}}' data-financialyr3='{{$pan_row->financialyr3}}' data-financialyr4='{{$pan_row->financialyr4}}' data-financialyr5='{{$pan_row->financialyr5}}'></td>
                            </tr>
                        @endforeach
                    @endif
                    </thead>
                    <tbody>
                    
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
                            <div class="row">
                                <div class="col-12">
                                    <a href="{{url($menu_action.'affidavitdashboard')}}" class="float-left backBtn">{{Lang::get('affidavit.back')}}</a>
                               
                                        <!-- <a href="{{url('affidavit/pending-criminal-cases')}}" type="submit" class="float-right nextBtn">{{Lang::get('affidavit.save')}} &amp; {{Lang::get('affidavit.next')}}</a> -->
                                        
                                        <button type="submit" class="float-right nextBtn">{{Lang::get('affidavit.save')}} &amp; {{Lang::get('affidavit.next')}}</button>
                                    
                                        <a href="{{url($menu_action.'affidavitdashboard')}}" class="float-right cencelBtn mr-2">{{Lang::get('affidavit.cancel')}}</a>
                                        &nbsp; &nbsp; &nbsp;
                                        
                                     </div>
                                
                            </div>
                        </div>
    </form>
    </div>
    <!-- End Of container-fluid Div -->
</div>
    </section>
</main>
<div class="modal fade" id="DependentDetailsModal" tabindex="-1" role="dialog" aria-labelledby="PersonalEditModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="PersonalEditModalLabel">{{Lang::get('affidavit.update_personal_detail')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form>
      <div class="modal-body">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="exampleInputname">{{Lang::get('affidavit.relation_type')}}</label>
                    <select class="form-control" name="relation_type_update" id="relation_type_update">
                        <option value="0">-{{Lang::get('affidavit.select_relation_type')}}-</option>
                    @forelse($get_relation_type as $relation_type)
                        <option value="{{ $relation_type->relation_type_code }}">{{ $relation_type->relation_type }} - {{ $relation_type->relation_type_hi }}</option>
                    @empty
                        <option>{{Lang::get('affidavit.no_record_found')}}</option>
                    @endforelse
                    </select>
                </div>
                <span class="error_2_prsnl"></span>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="exampleRelation">{{Lang::get('affidavit.relation')}}</label>
                    <select class="form-control" name="relation_code_update" id="relation_code_update">
                        <option value="0">-{{Lang::get('affidavit.select_relation')}}-</option>
                    @forelse($get_cand_relation as $cand_relation)
                        <option value="{{ $cand_relation->relation_code }}">{{ $cand_relation->relation }} - {{ $cand_relation->relation_hi }}</option>
                    @empty
                        <option>{{Lang::get('affidavit.no_record_found')}}</option>
                    @endforelse 
                    </select>
                    <span class="error_3_prsnl"></span>  
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="exampleRelation">{{Lang::get('affidavit.name')}}</label>
                    <input maxlength="100" type="text" name="name_update" class="form-control" id="name_update" placeholder="Name" onkeypress="return blockSpecialChar_name(event)">
                </div>
                <span class="error_1_prsnl"></span>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <div id="successMsgPernl"></div>
        <input type="hidden" name="tblPersonaId" id="tblPersonaId" value="">
        <button type="button" class="btn btn-primary" id="updatePerbtn" onclick="updatePeronalDetail()">{{Lang::get('affidavit.update')}}</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.close')}}</button>
      </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="PANEditModal" tabindex="-1" role="dialog" aria-labelledby="PANEditModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="PANEditModalLabel">{{Lang::get('affidavit.update_pan_details')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form>
      <div class="modal-body">
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="exampleInputname">{{Lang::get('affidavit.name')}}</label>
                    <input type="text" readonly="" onkeypress="return blockSpecialChar_name(event)" class="form-control" id="pan_name" placeholder="Enter name">
                </div>
                <span class="error_1_pan"></span>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="exampleRelation">{{Lang::get('affidavit.relation')}}</label>
                    <!-- <input type="text"  class="form-control" name="pan_relation_code" id="relation_code" placeholder="relation"> -->
                    <select class="form-control" name="pan_relation_code" id="relation_code" disabled="disabled">
                        @forelse($get_cand_relation_self as $cand_relation)
                            <option value="{{ $cand_relation->relation_code }}">{{ $cand_relation->relation }} - {{ $cand_relation->relation_hi }}</option>
                        @empty
                            <option>{{Lang::get('affidavit.no_record_found')}}</option>
                        @endforelse
                    </select>
                </div>
            </div>
          <div class="col-sm-4">
                <div class="form-group">
                    <label for="exampleRelation">{{Lang::get('affidavit.permanent_account_number')}}</label>
                    <input maxlength="10" type="text" name="pan" class="form-control alphanumeric" id="pan" placeholder="PAN" onkeyup="this.value = this.value.toUpperCase();">
                </div>
                <span class="error_2_pan"></span>
          </div>

          <div class="col-sm-4">
                <div class="form-group">
                    <label for="exampleRelation">{{Lang::get('affidavit.the_financial_year_for_which_the_last_income_tax_return_has_been_filed')}}</label>
                    <input type="text" name="financial_year" onkeypress="return blockSpecialChar_Financial_Year(event)"  class="form-control" id="financial_year" placeholder="2024-2025" maxlength="9">
                </div>
                <span class="error_3_pan"></span>
          </div>

          <!-- <div class="col-sm-4">
                <div class="form-group">
                    <label for="exampleRelation">Total income shown in Income Tax Return(in Rupees)</label>
                    <input type="text" name="total_income_shown" class="form-control" id="total_income_shown" placeholder="Total income shown">
                </div>
                <span class="error_4_pan"></span>
          </div>

        


           -->

          <div class="col-sm-4">
                <div class="form-group">
                    <label for="exampleRelation">{{Lang::get('affidavit.total_income_shown_for_financial_year')}}(2024-2025)</label>
                    <input type="text" name="financialyr1" class="form-control removespecialcharacter" id="financialyr1" placeholder="{{Lang::get('affidavit.total_income_shown')}}">
                </div>
                <span class="error_5_pan"></span>
          </div>
          <div class="col-sm-4">
                <div class="form-group">
                    <label for="exampleRelation">{{Lang::get('affidavit.total_income_shown_for_financial_year')}}(2023-2024)</label>
                    <input type="text" name="financialyr2" class="form-control removespecialcharacter" id="financialyr2" placeholder="{{Lang::get('affidavit.total_income_shown')}}">
                </div>
                <span class="error_6_pan"></span>
          </div>
          <div class="col-sm-4">
                <div class="form-group">
                    <label for="exampleRelation">{{Lang::get('affidavit.total_income_shown_for_financial_year')}}(2022-2023)</label>
                    <input type="text" name="financialyr3" class="form-control removespecialcharacter" id="financialyr3" placeholder="{{Lang::get('affidavit.total_income_shown')}}">
                </div>
                <span class="error_7_pan"></span>
          </div>
          <div class="col-sm-4">
                <div class="form-group">
                    <label for="exampleRelation">{{Lang::get('affidavit.total_income_shown_for_financial_year')}}(2021-2022)</label>
                    <input type="text" name="financialyr4" class="form-control removespecialcharacter" id="financialyr4" placeholder="{{Lang::get('affidavit.total_income_shown')}}">
                </div>
                <span class="error_8_pan"></span>
          </div>
           <div class="col-sm-4">
                <div class="form-group">
                    <label for="exampleRelation">{{Lang::get('affidavit.total_income_shown_for_financial_year')}}(2020-2021)</label>
                    <input type="text" name="financialyr5" class="form-control removespecialcharacter" id="financialyr5" placeholder="{{Lang::get('affidavit.total_income_shown')}}">
                </div>
                <span class="error_9_pan"></span>
          </div> 
        </div>



      </div>
      <div class="modal-footer">
        <div id="successMsg"></div>
        <input type="hidden" name="PANtblId" id="PANtblId" value="">
        <button type="button" class="btn btn-primary" id="updatePANbtn" onclick="updatePANDetail()">{{Lang::get('affidavit.update')}}</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.close')}}</button>
      </div>
      </form>
    </div>
  </div>
</div>


<div class="modal fade" id="socialMediaModal" tabindex="-1" role="dialog" aria-labelledby="socialMediaModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="socialMediaModalLabel">{{Lang::get('affidavit.update_social_media')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form>
      <div class="modal-body">
        <div class="modal-body">
            <label for="tagName">{{Lang::get('affidavit.social_media')}}: </label>
            <select class="form-control update_media" id="media_account_accounts" name="social_media_update">
                @forelse($get_social_media as $social_media)
                    <option value="{{ strtolower($social_media->social_media_code) }}">{{ $social_media->social_media_name }} - {{ $social_media->social_media_name_hi }}</option>
                @empty
                    <option>{{Lang::get('affidavit.no_record_found')}}</option>
                @endforelse
            </select>
        </div>
        <div class="modal-body">
            <label for="otherAccountName">{{Lang::get('affidavit.account')}}: </label>
            <input class="form-control other_account_update" type="text" id="other_account_name" name="other_account_name_update" />
        </div>
      </div>
      <div class="modal-footer">
        <div id="success_Media_Msg"></div>
        <input type="hidden" name="tblId" id="tblId" value="">
        <button type="button" class="btn btn-primary" id="updateSocialbtn" onclick="updateSocialMedia()">{{Lang::get('affidavit.update')}}</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.close')}}</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Spouse/Self Delete Modal Start-->
<div class="modal fade" id="deleteSpouseModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Delete Spouse/ Dependent/HUF Details</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <form>
               <h5>{{Lang::get('affidavit.are_you_sure_to_delete_this_entry')}}</h5>
               <input type="hidden" name="modal_delete_spouse_id" id="modal_delete_spouse_id">
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.no')}}</button>
            <button type="button" class="btn btn-primary" onclick="javascript:delete_spouse_entry()">{{Lang::get('affidavit.yes')}}</button>
         </div>
      </div>
   </div>
</div>
<!-- Spouse/Self Modal End-->

@endsection @section('script')
<script type="text/javascript" src="{{ asset('affidavit/js/remove_special_character.js') }}"></script>
<script type="text/javascript" src="{{ asset('affidavit/js/affidavit_validation.js') }}"></script>
<script type="text/javascript" src="{{ asset('affidavit/js/sweetalert.js') }}"></script>
<script type="text/javascript" src="{{ asset('affidavit/js/jquery-ui.js') }}"></script>

<script type="text/javascript" src="{{ asset('affidavit/js/crypto-js.min.js') }}"></script> 

<script type="text/javascript">
$(document).ready(function(){
$('.removespecialcharacter').keyup(function () { 
  this.value = this.value.replace(/[^0-9\.]/g,'');
});

    $('#dateofbirth').prop('disabled', true);
    $('#dateofbirth').addClass('disableCss');

$("#social_media").change(function(){
    var social_media_id = $("#social_media").val();

    if(social_media_id == "oth"){
        $("#social_media").addClass('for_width_css');
        $("#other_social_media_account").addClass('other_css');
        $("#other_social_media_account").show();
    } else {
        $("#other_social_media_account").hide();
        $("#social_media").removeClass('for_width_css');
        $("#other_social_media_account").removeClass('other_css');
    }
});


$("input[type='radio']").click(function(){
    var flag = false;
    if($(".email_account").is(":checked")) {
       var radioValue = $("input[name='email_account']:checked").val();
       if(radioValue == 1){
        var flag = true;
        $('#email_address').prop('readonly', false);
        $("#VerifyEmailId").show();
       } else if(radioValue == 2){
        var flag = true;
        $('#email_address').prop('readonly', true);
        $("#VerifyEmailId").hide();
        $("#email_address").val("");
       } else {
        //alert('ERROR2!.');
       }
    }

    if($(".candidate_setup_party").is(":checked")) {
       var radioValue = $("input[name='candidate_setup_party']:checked").val();
       if(radioValue == 1){
       $('#party_type').prop('disabled', false);
       $('#political_party').prop('disabled', false);
       } else if(radioValue == 2){
       $('#party_type').prop('disabled', 'disabled');
       $('#political_party').prop('disabled', 'disabled');
       $("#political_party").val("");
       $("#party_type").val("");
       } else {
       // alert('ERROR1!.');
       }
    }

    if($(".dateofborth_remember").is(":checked")) {
       var radioValue = $("input[name='dateofborth_remember']:checked").val();
       if(radioValue == 1){
       $('#dateofbirth').prop('disabled', false);
       $('#dateofbirth').removeClass('disableCss');
       } else if(radioValue == 2){
       $('#dateofbirth').prop('disabled', 'disabled');
       $('#dateofbirth').addClass('disableCss');
       $("#dateofbirth").val("");
       } else {
        //alert('ERROR3!.');
       }
    }
});


          
  $(".accordion_head").click(function() {
    if($('.accordion_body').is(':visible')) {
      $(".accordion_body").slideUp(300);
      $(".plusminus").text('+');
    }
    if($(this).next(".accordion_body").is(':visible')) {
      $(this).next(".accordion_body").slideUp(300);
      $(this).children(".plusminus").text('+');
    } else {
      $(this).next(".accordion_body").slideDown(300);
      $(this).children(".plusminus").text('-');
    }
});

$("#VerifyMobileNo").hide();

$("#VerifyMobileNo").click(function(){
    var flag = false;
   var mobile_no = $("#mobile_number_check").val();
   var getTblId = $("#getTblId").val();
   var dataString = 'mobile_no='+ btoa(mobile_no) + '&getTblId='+ btoa(getTblId);
   if(mobile_no == ""){
    $("#officer_id_js_error").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_mobile_number") }}</label>');
    flag = true;
   } else if(mobile_no.length!=10){
    $("#officer_id_js_error").html('<label class="text-danger">{{Lang::get("affidavit.mobile_number_length_should_be") }}</label>');
    flag = true;
   } else if(mobile_no=='0000000000'){
    $("#officer_id_js_error").html('<label class="text-danger">{{Lang::get("affidavit.invalid_formate") }}</label>');
    flag = true;
   } else {
    $("#officer_id_js_error").html('');
   }
    
   if(flag){
        $("#VerifyMobileNo").html('{{Lang::get("affidavit.verify_mobile_no") }}');
        return false;
    }

    $.ajax({
        url: "{{ route('check.mobile.no') }}",
        type: 'get',
        data: dataString,
        dataType: 'json', 
        beforeSend: function() {
            $('#checkbtn_loding').show();
        },      
        success: function(response) {
            if(response.status == 200){
                $("#otp").show();
                $("#verifyOTP").show();
                $("#successOPTMsg").html('<label class="text-success">'+response.msg+'</label>');
            } else {
                $("#otp").hide();
                $("#verifyOTP").show();
                $("#successOPTMsg").html('<label class="text-success">'+response.msg+'</label>');
            }
        },complete: function() {
          $('#checkbtn_loding').hide();
        }
    });
    return false;
  });

$("#verifyOTPEmail").click(function(){
   var flag = false;
   var email_address = $("#email_address").val();
   var otp_emailid = $("#otp_emailid").val();
   var dataString = 'email_address='+ btoa(email_address) + '&otp_emailid='+ btoa(otp_emailid);

   if(email_address == ""){
    $("#email_id_js_error").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_email_id")}}</label>');
    flag = true;
   } else {
    $("#email_id_js_error").html('');
   }
    
   if(flag){
        $("#verifyOTPEmail").val('{{Lang::get("affidavit.verify_otp")}}');
        return false;
    }

    $.ajax({
        url: "{{ route('verify.otp.emailId') }}",
        type: 'get',
        data: dataString,
        dataType: 'json', 
        beforeSend: function() {
            $('#verifyOTPEmail').val('Please Wait...');
        },      
        success: function(response) {
            if(response.status == 200 && response.error==false){
                $("#email_verify_img").html("<img src='{{ asset('img/logo/tick.png') }}' width='25'>");
                $("#successOPTMsg_email").html('<label class="text-success">'+response.msg+'</label>');
                $("#verifyOTPEmail").hide();
                $("#VerifyEmailId").hide();
                $("#otp_emailid").hide();
                $("#hidden_email_id").val(email_address);
            } else if(response.status == 401 && response.error==true){
                $("#email_verify_img").html('');
                $("#successOPTMsg_email").html('<label class="text-danger">'+response.msg+'</label>');
                $("#verifyOTPEmail").show();
                $("#VerifyEmailId").show();
                $("#otp_emailid").show();
                $("#hidden_email_id").val('');
            } else if(response.status == 402 && response.error==true){
                $("#successOPTMsg_email").html('<label class="text-danger">'+response.msg+'</label>');
                $("#email_verify_img").html('');
                $("#verifyOTPEmail").show();
                $("#VerifyEmailId").show();
                $("#otp_emailid").show();
                $("#hidden_email_id").val('');
            } else if(response.status == 201 && response.error==true){
                $("#successOPTMsg_email").html('<span class="text-danger">'+response.msg+'</span>');
                $("#email_verify_img").html('');
                $("#verifyOTPEmail").show();
                $("#VerifyEmailId").show();
                $("#otp_emailid").show();
                $("#hidden_email_id").val('');
            } else {    
                //alert("Internal Server ERROR.");
            }
        },complete: function() {
          $('#verifyOTPEmail').val('{{Lang::get("affidavit.verify_otp")}}');
        }
    });
    return false;
  });

});    

$("#VerifyEmailId").hide();

$("#VerifyEmailId").click(function(e){
    e.preventDefault();
    var flag = false;
    var email_address = $("#email_address").val();
    var getTblId = $("#getTblId").val();
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

    if(email_address==""){
        $("#email_id_js_error").html('<label class="text-danger">{{Lang::get("affidavit.enter_a_valid_email_address")}}</label>');
        $("#email_address").focus();
        flag = true;
    } else if(!emailReg.test(email_address)) {
        $("#email_id_js_error").html('<label class="text-danger">{{Lang::get("affidavit.enter_a_valid_email_address")}}</label>');
        flag = true;
    } else {
        $("#email_id_js_error").html('');
    }

    if(flag){
        $("#VerifyEmailId").html('{{Lang::get("affidavit.verify_email_id")}}');
        return false;
    }

    var dataString = 'email_address='+ btoa(email_address) + '&getTblId='+ btoa(getTblId);
    $.ajax({
        url: "{{ route('check.email.address') }}",
        type: 'get',
        data: dataString,
        dataType: 'json', 
        beforeSend: function() {
            $('#VerifyEmailId').html('Please Wait....');
        },      
        success: function(response) {
            if(response.status == 200){
                $("#otp_emailid").show();
                $("#verifyOTPEmail").show();
                $("#successOPTMsg_email").html('<label class="text-success">'+response.msg+'</label>');
            } else {
                $("#otp_emailid").hide();
                $("#verifyOTPEmail").hide();
                $("#successOPTMsg_email").html('<label class="text-success">'+response.msg+'</label>');
            }
        },complete: function() {
          $('#VerifyEmailId').html('Verify Email Id');
        }
    });
    return false;
})

$("#mobile_number_check").blur(function(){
    var hidden_mobile_no    = $("#hidden_mobile_no").val();
    var mobile_number_check = $("#mobile_number_check").val();
    if(hidden_mobile_no!=""){
        if(hidden_mobile_no!=mobile_number_check){
            $("#VerifyMobileNo").show();
            $("#verify_img").html('');
            $("#verified_msg").html('');
            $("#successOPTMsg").html('');
            $("#otp").val('');
        } else {
            $("#VerifyMobileNo").hide();
        }
    }
});

$("#email_address").blur(function(){
    var hidden_email_id    = $("#hidden_email_id").val();
    var email_address = $("#email_address").val();
    if(hidden_email_id!=""){
        if(hidden_email_id!=email_address){
            $("#VerifyEmailId").show();
            $("#email_verify_img").html('');
            $("#successOPTMsg_email").html('');
            $("#otp_emailid").val('');
        } else {
            $("#VerifyEmailId").hide();
        }
    }
});


$("#verifyOTP").click(function(e){
  e.preventDefault();
  var flag = false;
  var mobile_number = $("#mobile_number_check").val();
  var otpbtn        = $("#otp").val();

  if(mobile_number == ""){
    $("#officer_id_js_error").html('<span class="text-danger">{{Lang::get("affidavit.mobile_number_cannot_be_empty")}}</span>');
    flag = true;
  } else {
    $("#officer_id_js_error").html('');
  }

  if(otpbtn == "" && otpbtn.length!=6){
    $("#otp_js_error").html('<span class="text-danger">{{Lang::get("affidavit.please_enter_your_otp")}}</span>');
    flag = true;
  } else {
    $("#otp_js_error").html('');
  }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var dataString = 'mobile_number='+ btoa(mobile_number) + '&otpbtn='+ btoa(otpbtn);

    if(flag){
        $("#verifyOTP").html('{{Lang::get("affidavit.verify_otp")}}');
        return false;
    }

    $.ajax({
            url: "{{ route('verifyOTP') }}",
            type: 'post',
            data: dataString,
            dataType: 'json',
            beforeSend: function(){
                $("#verifyOTP").val('Please Wait...');
            },success: function(response) {
                if(response.status == 200 && response.error==false){
                    $("#verified_msg").html('<label class="text-success">'+response.msg+'</label>');
                    $("#successOPTMsg").html('');
                    $("#verifyOTP").hide();
                    $("#otp").hide();
                    $("#verify_img").html("<img src='{{ asset('img/logo/tick.png') }}' width='25'>");
                    $("#VerifyMobileNo").hide();
                    $("#hidden_mobile_no").val(mobile_number);
                } else if(response.status == 201 && response.error==true){
                    $("#verified_msg").html('<span class="text-danger">'+response.msg+'</span>');
                    $("#successOPTMsg").html('');
                    $("#otp").val('');
                    $("#verify_img").html('');
                    $("#VerifyMobileNo").show();
                } else if(response.status == 401 && response.error==true){
                    $("#verified_msg").html('<span class="text-danger">'+response.msg+'</span>');
                    $("#successOPTMsg").html('');
                    $("#otp").val('');
                    $("#verify_img").html('');
                    $("#VerifyMobileNo").show();
                } else if(response.status == 402 && response.error==true){
                    $("#verified_msg").html('<span class="text-danger">'+response.msg+'</span>');
                    $("#successOPTMsg").html('');
                    $("#otp").val('');
                    $("#verify_img").html('');
                    $("#VerifyMobileNo").show();
                } else {
                    //alert('Internal Server Error!');
                }
            },complete:function(data){
                $("#verifyOTP").val('{{Lang::get("affidavit.verify_otp")}}');
               }
    });
    return false;
});

function updatePeronalDetail(){
    var flag = false;
    var tblPersonaId           = $("#tblPersonaId").val();
    var name_update            = $("#name_update").val().replace(/[^a-z0-9\s]/gi, '');
    var relation_type_update   = $("#relation_type_update").val();
    var relation_type_name   = $("#relation_type_update option:selected").html();
    var relation_code_update   = $("#relation_code_update").val();
    var relation_code_name   = $("#relation_code_update option:selected").html();
    
    /* if($("#name_update").val().search(/\w/)< 0){
        $(".error_1_prsnl").html('<label class="text-danger">Please enter name.</label>');
    flag = true;
    } else {
        $(".error_1_prsnl").html('');
    } if($("#relation_type_update").val().search(/\w/)< 0){
        $(".error_2_prsnl").html('<label class="text-danger">Please select relation type.</label>');
        flag = true;
    } else {
        $(".error_2_prsnl").html('');
    } 

    if($('#relation_code_update').val().search(/\w/)< 0){
        $(".error_3_prsnl").html('<label class="text-danger">Please select relation.</label>');
        flag = true;
    } else {
        $(".error_3_prsnl").html('');
    }
	*/

    var dataString ={tblPersonaId:tblPersonaId, name_update:name_update, relation_type_update:relation_type_update, relation_code_update:relation_code_update} 
    /*'tblPersonaId='+ btoa(tblPersonaId) + '&name_update='+ btoa(name_update) + '&relation_type_update='+ btoa(relation_type_update) + '&relation_code_update='+ btoa(relation_code_update);*/

    if(flag){
        $("#updatePerbtn").html('Update');
        return false;
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ url($menu_action.'update-personal-details') }}",
        type: 'post',
        data: dataString,
        dataType: 'json', 
        beforeSend: function() {
            $('#updatePerbtn').html('Updating....');
        },      
        success: function(response) {
            if(response.status == 200){
                $("#successMsgPernl").html('<label class="text-success">'+response.msg+'</label>');
                $("#DependentDetailsModal").modal('hide');
                $("#relation_type_code_"+tblPersonaId).val(relation_type_name);
                $("#relation_code_"+tblPersonaId).val(relation_code_name);
                $("#name_"+tblPersonaId).val(name_update);
                
                $("#relation"+tblPersonaId).html("");
				
				
				<?php if(Auth::user()->role_id != '19') { ?>
					
				var del = '<a href="javascript:void(0)" title="Delete" onclick="javascript:delete_spouse('+tblPersonaId+')"><span class="btn btn-info btn-danger btn-sm"><i class="fa fa-times"></i> Delete</span></a>';
				
				<?php } else { ?>
				var del = '';	
				<?php } ?>
				
				
                var tr_str = "<td><input class='form-control' type='text' value='" + relation_type_name + "' id='relation_type_code_"+tblPersonaId+"' disabled></td>" + "<td><input class='form-control' type='text' value='" + relation_code_name + "' id='relation_code_"+tblPersonaId+"' disabled></td>" + "<td ><input class='form-control' type='text' value='" + name_update + "' id='name_"+tblPersonaId+"' disabled></td>" + "<td><input type='button' value='Edit' class='editpersonalrecord btn btn-success btn-sm mt-1 mr-1' data-id='"+tblPersonaId+"' data-relation_type_code='"+relation_type_update+"' data-relation_code='"+relation_code_update+"'  data-name='"+name_update+"'> "+del+"</td>";
                $("#relation"+tblPersonaId).html(tr_str);

                } else {
                $("#successMsgPernl").html('<label class="text-danger">'+response.msg+'</label>');
            }
        },complete: function() {
          $('#updatePerbtn').html('Update');
        }
    });
    return false;

}    
function updatePANDetail(){
    var flag = false;
    var PANtblId            = $("#PANtblId").val();
    var pan_name            = $("#pan_name").val();
    var relation_code       = $("#relation_code").val();
    var relation_code_name  = $("#relation_code option:selected").html();

    var pan                 = $("#pan").val();
    var financial_year      = $("#financial_year").val();

    //alert(financial_year);
    // var total_income_shown  = $("#total_income_shown").val();
    var financialyr1        = $("#financialyr1").val();
    var financialyr2        = $("#financialyr2").val();
    var financialyr3        = $("#financialyr3").val();
    var financialyr4        = $("#financialyr4").val();
    var financialyr5        = $("#financialyr5").val();

    var pan                 = pan;

     if($("#pan_name").val() == ''){
        $(".error_1_pan").html('<label class="text-danger">Please enter name.</label>');
    flag = true;
    }else {
        $(".error_1_pan").html('');
    }

	var regex = /[A-Z]{5}[0-9]{4}[A-Z]{1}$/; 


    if($("#pan").val() == ''){
        $(".error_2_pan").html('<label class="text-danger">Please enter PAN no.</label>');
        flag = true;
    }
    else if(!regex.test($("#pan").val())){      
	  $(".error_2_pan").html('<label class="text-danger">Please enter valid PAN no.</label>');
        flag = true;   
	  //return regex.test(inputvalues);    
  } else {
        $(".error_2_pan").html('');
    }

    if($('#financial_year').val() == ''){
        $(".error_3_pan").html('<label class="text-danger">Please enter financial year.</label>');
        flag = true;
    }else {
        $(".error_3_pan").html('');
    } 

    // if($('#total_income_shown').val().search(/\w/)< 0){
    //     $(".error_4_pan").html('<label class="text-danger">Please enter total income shown.</label>');
    //     flag = true;
    // }else {
    //     $(".error_4_pan").html('');
    // }

    if($('#financialyr1').val() == ''){
        $(".error_5_pan").html('<label class="text-danger">Please enter financial year(2024-2025).</label>');
        flag = true;
    } else {
        $(".error_5_pan").html('');
    }

    if($('#financialyr2').val() == ''){
        $(".error_6_pan").html('<label class="text-danger">Please enter financial year(2023-2024).</label>');
        flag = true;
    } else {
        $(".error_6_pan").html('');
    }

    if($('#financialyr3').val() == ''){
        $(".error_7_pan").html('<label class="text-danger">Please enter financial year(2022-2023).</label>');
        flag = true;
    } else {
        $(".error_7_pan").html('');
    }

    if($('#financialyr4').val() == ''){
        $(".error_8_pan").html('<label class="text-danger">Please enter financial year(2021-2022).</label>');
        flag = true;
    } else {
        $(".error_8_pan").html('');
    }

    if($('#financialyr5').val() == ''){
        $(".error_9_pan").html('<label class="text-danger">Please enter financial year(2020-2021).</label>');
        flag = true;
    } else {
        $(".error_9_pan").html('');
    }

    if(flag){
        $("#updatePANbtn").html('Update');
        return false;
    }

    var dataString = 'PANtblId='+ PANtblId + '&pan_name='+ pan_name + '&relation_code='+ relation_code + '&pan='+ pan + '&financial_year='+ financial_year + '&financialyr1='+ financialyr1 + '&financialyr2='+ financialyr2 + '&financialyr3='+ financialyr3 + '&financialyr4='+ financialyr4 + '&financialyr5='+ financialyr5;
    //alert(dataString);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ url($menu_action.'table/update-pan-details') }}",
        type: 'post',
        data: dataString,
        dataType: 'json', 
        beforeSend: function() {
            $('#updatePANbtn').html('Updating....');
        },      
        success: function(response) {
            if(response.status == 200){
               // $("#successMsg").html('<label class="text-success">'+response.msg+'</label>');
                $("#PANEditModal").modal('hide');

                $("#pan_row_relation"+PANtblId).html("");
 
                var tr_str = "<td>"+pan_name+"</td>"+ "<td>"+relation_code_name+"</td>"+"<td>"+pan+"</td>" + "<td>"+financial_year+"</td>" + "<td >"+financialyr1+"</td>" + "<td >"+financialyr2+"</td>" + "<td>"+financialyr3+"</td>" + "<td>"+financialyr4+"</td>" + "<td>"+financialyr5+"</td>" + "<td><input type='button' value='Edit' class='editpanrecord btn btn-success btn-sm mt-1 mr-1' data-id='"+PANtblId+"' data-name='"+pan_name+"'  data-pan='"+pan+"' data-relation_code='"+relation_code+"' data-financial_year='"+financial_year+"' data-financialyr1='"+financialyr1+"' data-financialyr2='"+financialyr2+"' data-financialyr3='"+financialyr3+"' data-financialyr4='"+financialyr4+"' data-financialyr5='"+financialyr5+"'></td>";

                $("#pan_row_relation"+PANtblId).html(tr_str);

            } else {
                $("#successMsg").html('<label class="text-danger">'+response.msg+'</label>');
            }
        },complete: function() {
          $('#updatePANbtn').html('Update');
        }
    });
    return false;
}    
function updateSocialMedia(){
    var tblId                = $("#tblId").val();
    var other_account_update = $(".other_account_update").val();
    var social_media_code    = $(".update_media").val();
    var media_account        = $(".update_media option:selected").text();
    var media_account_name   = $("#media_account_accounts option:selected").html();

    var other_account_update = other_account_update.replace(/[^a-z0-9 @]/gi, '');

    var dataString = 'tblId='+ tblId + '&other_account_update='+ other_account_update + '&social_media_code='+ social_media_code + '&media_account='+ media_account;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ url($menu_action.'table/update_social_media') }}",
        type: 'post',
        data: dataString,
        dataType: 'json', 
        beforeSend: function() {
            $('#updateSocialbtn').html('Updating....');
        },      
        success: function(response) {
            if(response.status == 200){
                $("#success_Media_Msg").html('<label class="text-success">'+response.msg+'</label>');
                $("#socialMediaModal").modal('hide');
                $("#media_account"+tblId).html(media_account_name);
                $("#other_account_name"+tblId).html(other_account_update);
            } else {
                $("#success_Media_Msg").html('<label class="text-danger">'+response.msg+'</label>');
            }
        },complete: function() {
          $('#updateSocialbtn').html('Update');
        }
    });
    return false;
}

function getPoliticalParty(party_type){
    var dataString = 'party_type='+ btoa(party_type);
    $.ajax({
        url: "{{ route('aff.political.party') }}",
        type: 'get',
        data: dataString,
        dataType: 'json', 
        beforeSend: function() {
            $('#political_party_loding').show();
        },      
        success: function(response) {
            if(response.status == 200){
                var items="";
                items += "<option value=''>--{{Lang::get('affidavit.select_political_party_name')}}--</option>";
                $.each(response.result,function(index, item) {
                    items+="<option value='"+$.trim(item.CCODE)+"'>"+$.trim(item.PARTYNAME)+" - "+$.trim(item.PARTYHNAME)+"</option>";
                });
            $("#political_party").html(items);

            }
        },complete: function() {
          $('#political_party_loding').hide();
        }
    });
    return false;
}



$(document).on('click', '#socila_media_add', function(){
    var flag = false;
    var social_media   = $('#social_media').val();
    var social_account = $('#social_account').val();
    var social_name = $( "#social_media option:selected" ).text();
    social_media_code = social_media;
    var social_media_name =  $("#social_media option:selected").html();
    //var social_account_name =  $("#social_account option:selected").html();
    var social_media_id = $('#social_media').val();

    if(social_media == ""){
        $(".error_4").html('<label class="text-danger">{{Lang::get("affidavit.please_select_social_media_account")}}</label>');
        flag = true;
    } else {
        $(".error_4").html('');
    }

     if($('#social_account').val() == ''){
        $(".error_5").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_social_account_name")}}</label>');
        flag = true;
    } else {
        $(".error_5").html('');
    } 
    
    if(flag){
        $("#socila_media_add").html('{{Lang::get("affidavit.save")}}');
        return false;
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var dataString = 'social_media='+ social_media + '&social_account='+ social_account + '&social_name='+ social_name;
	
	
	//alert(dataString);
	
	
    $.ajax({
        url: "{{ route('social_media_add_data') }}",
        type: 'POST',

        data: dataString,
        dataType: 'json', 
        beforeSend: function() {
            $('#socila_media_add').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
            $('#socila_media_add').prop('disabled', true);
        },      
        success: function(response) {
            if(response.status == 200){
                var len = 0;
                var len = response.result.length;
                  if(response['result'] != null){
                        var id = response['result'];
                        var tr_str = "<tr>" + "<td>" + social_media_name + "</td>" +"<td>" + social_account + "</td>" + "<td><input type='button' value='{{Lang::get('affidavit.edit')}}' class='editrecord btn btn-success btn-sm mt-1 mr-1' data-id='"+id+"' data-social_media_code='"+social_media_code+"' data-other_account_name='"+social_account+"'  data-media_account='"+social_media+"'></td>"+ "</tr>";
                        
                        $("#socialMediaTable tbody").append(tr_str);
                        $('#social_media').val("");
                        $('#social_account').val("");
              }
        }
        },complete: function() {
          $('#socila_media_add').prop('disabled', false);
          $('.loading_spinner').remove();
        }
    });
    return false;
});

$("#socialMediaTable").on('click', '.editrecord', function(){
    var rowId = $(this).data('id');
    $('#tblId').val($(this).data('id'));
	$("#success_Media_Msg").remove();
    var media_account       = $(this).data('social_media_code');
    var other_account_name  = $(this).data('other_account_name');
    $('#media_account_accounts').val(media_account);
    $('#other_account_name').val(other_account_name);
    $('#socialMediaModal').modal();
    return false;
});

$("#userTableDependentDetails").on('click', '.editpersonalrecord', function(){
	$("#successMsgPernl").remove();
    var tblPersonaId = $(this).data('id');
    $("#tblPersonaId").val(tblPersonaId);
    $("#relation_type_code").val($(this).data('relation_type_code'));
    $('#name_update').val($(this).data('name'));
    $('#relation_code').val($(this).data('relation_code'));
    var relation_code = $(this).data('relation_code');
    var relation_type_code = $(this).data('relation_type_code');
    $("#relation_type_update").val(relation_type_code);
    $("#relation_code_update").val(relation_code);
    $('#DependentDetailsModal').modal();
    return false;
});

$("#userTablePAN").on('click', '.editpanrecord', function(){
    var PANtblId = $(this).data('id');
    // alert($(this).data('relation_code') + PANtblId);
    $("#PANtblId").val($(this).data('id'));
    $('#pan_name').val($(this).data('name'));
    $("#relation_code").val($(this).data('relation_code'));
    $("#pan").val($(this).data('pan'));

    $("#financial_year").val($(this).data('financial_year'));
    $("#financialyr1").val($(this).data('financialyr1'));
    $("#financialyr2").val($(this).data('financialyr2'));
    $("#financialyr3").val($(this).data('financialyr3'));
    $("#financialyr4").val($(this).data('financialyr4'));
    $("#financialyr5").val($(this).data('financialyr5'));

    $('#PANEditModal').modal();
    return false;
});

$(document).on('click', '#add', function(){
var flag = false;
var relation_type   = $('#relation_type').val();
var relation        = $('#relation').val();
var relation_type_name =  $("#relation_type option:selected").html();
var relation_name =  $("#relation option:selected").html();
var name            = $('#name').val().replace(/[^a-z0-9\s]/gi, '');
 //alert(relation_type + relation + name);

if(relation_type == ""){
    $(".error_1").html('<label class="text-danger">{{Lang::get("affidavit.please_select_relation_name")}}</label>');
    flag = true;
}else {
    $(".error_1").html('');
}

if(relation == ""){
    $(".error_2").html('<label class="text-danger">{{Lang::get("affidavit.please_select_relation")}}</label>');
    flag = true;
}else {
    $(".error_2").html('');
}

 if($('#name').val() == ''){
    $(".error_3").html('<label class="text-danger">{{Lang::get("affidavit.please_select_name")}}</label>');
    flag = true;
}else {
    $(".error_3").html('');
} 

if(flag){
    $("#add").html('Save');
    return false;
    }

$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

var dataString = 'relation_type='+ relation_type + '&relation='+ relation + '&name='+name;

    $.ajax({
        url: "{{ route('table.add_data') }}",
        type: 'POST',
        data: dataString,
        dataType: 'json', 
        beforeSend: function() {
            $('#add').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
            $('#add').prop('disabled', true);
        },      
        success: function(response) {
          if(response.status == 200){
            var len = 0;
                var len = response.result.length;
                
                /*$('#userTableDependentDetails').find("tbody tr:not(:nth-child(1)):not(:nth-child(2)):not(:nth-child(3))").empty(); 

                $('#userTablePAN').find("tbody tr:not(:nth-child(1)):not(:nth-child(2)):not(:nth-child(3))").empty();*/
                
                if(response['result'] != null){
                /*if(len > 0){
                    var num = 1;*/
                    /*for(var i=0; i<len; i++){*/
                        var id = response['result'];

                       /* var relation_type_code = response['result'][i].relation_type_code;
                        var relation_code = response['result'][i].relation_code;
                        var name = response['result'][i].name;*/
                        // alert(id);
						
						
						
						<?php if(Auth::user()->role_id != '19') { ?>
					
						var del = '<a href="javascript:void(0)" title="Delete" onclick="javascript:delete_spouse('+id+')"><span class="btn btn-info btn-danger btn-sm"><i class="fa fa-times"></i> Delete</span></a>';
						
						<?php } else { ?>
						var del = '';	
						<?php } ?>
						
						
                        var tr_str = "<tr id='relation"+id+"'>" + "<td>"+relation_type_name+"</td>" + "<td>"+relation_name+"</td>" + "<td >"+name+"</td>" + "<td><input type='button' value='{{Lang::get('affidavit.edit')}}' class='editpersonalrecord btn btn-success btn-sm mt-1 mr-1' data-id='"+id+"' data-relation_type_code='"+relation_type+"' data-relation_code='"+relation+"'  data-name='"+name+"'>  "+del+" </td>"+ "</tr>";
						
						

                        var tr_str_pan = "<tr id='pan_row_relation"+id+"'>" + "<td>" + name + "</td>" + "<td>" + relation_name + "</td>" + "<td><input class='form-control' name='pan' type='text' value='' id='pan_"+id+"' disabled></td>" + "<td><input class='form-control' type='text' value='' name='financial_year' id='pan_financial_year_"+id+"' disabled></td>" + "<td ><input class='form-control removespecialcharacter' type='text' value='' name='financialyr1' id='pan_financialyr1_"+id+"' disabled></td>" + "<td ><input class='form-control' type='text' value='' name='financialyr2' id='pan_financialyr2_"+id+"' disabled></td>" + "<td ><input class='form-control' type='text' value='' name='financialyr3' id='pan_financialyr3_"+id+"' disabled></td>" + "<td ><input class='form-control' type='text' value='' name='financialyr4' id='pan_financialyr4_"+id+"' disabled></td>" + "<td ><input class='form-control' type='text' value='' name='financialyr5' id='pan_financialyr5_"+id+"' disabled></td>" + "<td><input type='button' value='{{Lang::get('affidavit.edit')}}' class='editpanrecord btn btn-success btn-sm mt-1 mr-1' data-id='"+id+"' data-name='"+name+"' data-relation_code='"+relation+"'></td>"+ "</tr>";

                        //$("#userTableDependentDetails").prepend(tr_str);
                        $("#userTableDependentDetails tbody tr:nth-child(1)").after(tr_str);
                        $("#userTablePAN").append(tr_str_pan);

                        $('#relation_type').val('');
                        $('#relation').val("");
                        $('#name').val('');
                       /* num++;*/
                    /*}*/

                  /*} else {
                    var tr_str = "<tr class='norecord'>" + "<td align='center' colspan='4'>No record found.</td>" + "</tr>";

                    var tr_str_pan = "<tr class='norecord'>" + "<td align='center' colspan='12'>No record found.</td>" + "</tr>";

                    $("#userTableDependentDetails tbody").prepend(tr_str);
                    $("#userTablePAN tbody").prepend(tr_str_pan);
                  }*/
              }
          }
        },complete: function() {
          $('#add').prop('disabled', false);
          $('.loading_spinner').remove();
        }
    });
    return false;
});

jQuery(function ($) {
      var $active = $('#accordion .panel-collapse.in').prev().addClass('active');
      $active.find('a').prepend('<i class="fa fa-plus"></i>');
      $('#accordion .panel-heading').not($active).find('a').prepend('<i class="fa fa-plus"></i>');
      $('#accordion').on('show.bs.collapse', function (e) {
          $('#accordion .panel-heading.active').removeClass('active').find('.glyphicon').toggleClass('fa-plus fa fa-minus');
          $(e.target).prev().addClass('active').find('.glyphicon').toggleClass('fa fa-plus fa fa-minus');
      })
});

$('.dateofbirth').datepicker({
    onSelect: function(value, ui) {
        var today = new Date(), 
            age = today.getFullYear() - ui.selectedYear;
        $('#age').val(age);
    },
    changeMonth: true,
    changeYear: true,
    maxDate: '-18Y',
    yearRange: '1925:2006',
    dateFormat: 'dd-mm-yy',
});

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
                items += "<option value=''>--{{Lang::get('affidavit.select_district_name')}}--</option>";
            $.each(response.result,function(index, item) {
                items+="<option value='"+$.trim(item.DIST_NO)+"'>"+$.trim(item.DIST_NAME)+"</option>";    
            });
            $("#district_name").html(items);
        },complete:function(response){
            $("#loader_dist").hide();
        }
    });
    return false;
}

function getAClist($dist_code) {
    var state_code = $("#state_name").val();
    var dist_code = $dist_code;
    $.ajax({
        type: 'get',
        url: '{{ route('getACList') }}',
        data: 'dist_code='+btoa(dist_code) + '&state_code=' + btoa(state_code),
        beforeSend: function(){
            $("#loader2").show();
        },success: function(response){
            var items="";
                items += "<option value=''>--{{Lang::get('affidavit.select_ac_name')}}--</option>";
            $.each(response.result,function(index, item) {
                items+="<option value='"+item.AC_NO+"'>"+item.AC_NAME+"</option>";    
            });
            $("#ac_name").html(items);
        },complete:function(response){
            $("#loader2").hide();
        }
    });
    return false;
}

$(document).on('click', '.browse', function(){
  var file = $(this).parent().parent().parent().find('.file');
  file.trigger('click');
});
$(document).on('change', '.file', function(){
  $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
});

</script>
<script type="text/javascript">
function validateCandidate(){
  var flag = false;
  var son_daughter_wife_of   = $("#son_daughter_wife_of").val();
  var postal_address         = $("#postal_address").val();
  

  var son_daughter_wife_of_len     = $("#son_daughter_wife_of").val().length;
  
  <?php if(empty($session_data->cimage)){ ?>
  
if ($('#file').val()=="") {
	$(".candidate_details_slide").show();
    $("#img_error_img").html('<p class="text-danger">{{Lang::get("affidavit.please_select_profile_image")}}</p>');
    $("#file").focus();     
    flag = true;
  } else {
    $("#img_error_img").html('');
  }
  <?php } ?>

if ($("#rememberDOB2").prop("checked")) {
   if($("#age").val()==""){
	 $(".candidate_details_slide").show(); 
     $("#error_cand_age").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_your_age")}}</label>');
     $('input[type="age"]').prop("disabled", true);
      flag = true;
   } else {
      $("#error_cand_age").html('');
   }
}


if ($("#rememberDOB1").prop("checked")) {
   if($("#dateofbirth").val()==""){
	 $(".candidate_details_slide").show(); 
     $("#error_cand_dbo").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_date_of_birth")}}</label>');
     $("#age").attr('readonly', true);
     $('input[type="age"]').prop("disabled", false);
      flag = true;
   } else {
      $("#error_cand_dbo").html('');
   }
}

  var minLength = 3;
  var maxLength = 100;

   if($("#son_daughter_wife_of").val() == '') {
	  $(".candidate_details_slide").show(); 
      $("#error_cand_1").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_son_daughter")}}</label>');
      $("#son_daughter_wife_of").focus();
      flag = true;
  } else if(son_daughter_wife_of_len < minLength){
	 $(".candidate_details_slide").show(); 
    $("#error_cand_1").html('<label class="text-danger">{{Lang::get("affidavit.length_is_short_minimum_required")}}</label>');
    $("#son_daughter_wife_of").focus();
    flag = true;
  } else if(son_daughter_wife_of_len > maxLength){
	  $(".candidate_details_slide").show(); 
    $("#error_cand_1").html('<label class="text-danger">Length is not valid, maximum '+BucklemaxLength+' characters allowed.</label>');
    $("#son_daughter_wife_of").focus();
    flag = true;
  } else {
      $("#error_cand_1").html('');
  }

   if($("#postal_address").val() == '') {
	   $(".candidate_details_slide").show(); 
      $("#error_cand_address").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_address")}}</label>');
      $("#postal_address").focus();
      flag = true;
  } else if($("#postal_address").val().length < minLength){
	  $(".candidate_details_slide").show(); 
    $("#error_cand_address").html('<label class="text-danger">{{Lang::get("affidavit.length_is_short_minimum_required")}}</label>');
    $("#postal_address").focus();
    flag = true;
  } else if($("#postal_address").val().length > maxLength){
	  $(".candidate_details_slide").show(); 
    $("#error_cand_address").html('<label class="text-danger">Length is not valid, maximum '+BucklemaxLength+' characters allowed.</label>');
    $("#postal_address").focus();
    flag = true;
  } else {
      $("#error_cand_address").html('');
  }

  if($(".candidate_setup_party").is(":checked")) {
    var radioValue = $("input[name='candidate_setup_party']:checked").val();
    if(radioValue == 1){
      if($("#party_type").val() == ""){
		  $(".candidate_details_slide").show(); 
        $("#error_party_type_address").html('<label class="text-danger">{{Lang::get("affidavit.please_select_party_type")}}</label>');
        flag = true;
      } else {
        $("#error_party_type_address").html('');
      }

      if($("#political_party").val() == ""){
		  $(".candidate_details_slide").show(); 
        $("#political_party_error").html('<label class="text-danger">{{Lang::get("affidavit.please_select_political_party")}}</label>');
        flag = true;
      } else {
        $("#political_party_error").html('');
      }

    } else {
      //alert('ERROR44!.');
    }
  }
  

  /*if($(".email_account").is(":checked")) {
    var radioValue = $("input[name='email_account']:checked").val();
    if(radioValue == 1){ alert(1);
      if($("#email_address").val()==""){
        $("#error_email_id").html('<label class="text-danger">Please enter valid email Id</label>');
        $('#email_address').prop('readonly', false);
        flag = true;
      } else {
        $("#error_email_id").html('');
      }
    } else if(radioValue == 2) {alert(2);
      $('#email_address').prop('readonly', true);
      $("#email_address").val();
      $("#error_email_id").html('');
    } else { alert(3);
      alert('ERROR!');
    }
  }*/

  if(flag){
      return false;
    }
}

</script>


<!-- Image Script  -->

<script type="text/javascript">
        $(document).ready(function () {
          $('.file').on('click', function() {
            $('#form-upload').remove();
            $('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" value="" /></form>');
            $('#form-upload input[name=\'file\']').trigger('click');
            if (typeof timer != 'undefined') {
              clearInterval(timer);
            } 
            timer = setInterval(function() {
              if ($('#form-upload input[name=\'file\']').val() != '') { 
                clearInterval(timer);
                $.ajax({
                  url: "upload?_token=<?php echo csrf_token(); ?>",
                  type: 'POST',
                  dataType: 'json',
                  data: new FormData($('#form-upload')[0]),
                  cache: false,
                  contentType: false,
                  processData: false,
                  beforeSend: function() {
                    $('.file-frame').removeClass("file-frame-error");
                    $('.file i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
                    $('.file').prop('disabled', true);
                    $('.text-danger').remove();
                  },
                  complete: function() {
                    $('.file i').replaceWith('<i class="fa fa-upload"></i>');
                    $('.file').prop('disabled', false);
                  },
                  success: function(json) {
                    if(json['success'] == false) {
                      $('.file-frame').after("<span class='text-danger'>"+json['errors']+"</span>");
                      $('.file-frame').addClass("file-frame-error");
                    }
                    if (json['success'] == true) {
                      $('.file-frame').find('.image').val(json['path']);
                      $('.file-frame').find('img').attr("src","<?php echo url('/'); ?>/"+json['path']);
                    }
                  },
                  error: function(xhr, ajaxOptions, thrownError) { 
                    console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    $('.file-frame').after("<span class='text-danger'><?php echo __('messages.file_type_error'); ?></span>");
                  }
                });
              }
            }, 500);
          });
        });
      </script>
	  <script type="text/javascript">
        $(document).on('click', '.browse', function(){
          var file = $(this).parent().parent().parent().find('.file');
          file.trigger('click');
        });
        $(document).on('change', '.file', function(){
          $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
        });
      </script>
	  <script type="text/javascript">
       function read_url(input, part) {
          if (input.files && input.files[0]) {
            var reader = new FileReader();    
            reader.onload = function(e) {
              $('.'+part+' .avatar-preview').html("<img src='"+ e.target.result+"' width='100px' height='100px'>");
            }
            reader.readAsDataURL(input.files[0]);
          }
        }
      </script>
	  
<script type="text/javascript">
   function delete_spouse(id)
   {
    $("#modal_delete_spouse_id").val(id);
       $("#deleteSpouseModal").modal('show');
   }
</script>  
<script type="text/javascript">
   function delete_spouse_entry()
   {
      var id = $("#modal_delete_spouse_id").val();
      if(id)
      {
           $.ajax({
               url: "{{ url('delete_spouse') }}",
               type: 'GET',
               data: {  id:id },            
               headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
               success:function(data){
               if(data==1)
               {
                   $('#relation'+id).remove();
                   $('#pan_row_relation'+id).remove();
                   $("#deleteSpouseModal").modal('hide');
               }
               }
           });
      }
   }
</script>
	  
@endsection
