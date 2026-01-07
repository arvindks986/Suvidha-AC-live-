@extends( (Auth::user()->role_id != '19') ? 'layouts.theme' : 'admin.layouts.ac.theme', $data)

@section('title', 'Affidavit e-File') 
@section('content')
<style type="text/css">
.step-current a,.step-success a{
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
#overlay {
  background: #000;
  color: #666666;
  position: fixed;
  height: 100%;
  width: 100%;
  z-index: 5000;
  top: 0;
  left: 0;
  float: left;
  text-align: center;
  padding-top: 25%;
  opacity: .80;
}
button {
  margin: 40px;
  padding: 5px 20px;
  cursor: pointer;
}
.spinner {
    margin: 0 auto;
    height: 64px;
    width: 64px;
    animation: rotate 0.8s infinite linear;
    border: 5px solid firebrick;
    border-right-color: transparent;
    border-radius: 50%;
}
@keyframes rotate {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}  

.searchBox{
    width: 100%;
    position: relative;
}
.searchBox button{
    position: absolute;
    right: 0;
    top: 0;
    min-height: 36px;
    border-radius: 0;    
}
.main_heading {
    position: relative;
    font-size: 1.50rem;
    font-weight: 600;
    margin-top: 12px;
    margin-bottom: 10px;
    text-align: center;
    color: #101010;
    padding-bottom: 7px;
}
.main_heading::before {
    background: #d0d0d0;
    bottom: -2px;
    content: "";
    height: 1px;
    left: 50%;
    position: absolute;
    transform: translateX(-50%);
    width: 200px;
}
.main_heading::after {
    background: #ed457e;
    bottom: -3px;
    content: "";
    height: 3px;
    left: 50%;
    position: absolute;
    transform: translateX(-50%);
    width: 50px;
}


</style>
<link rel="stylesheet" href="{{ asset('affidavit/css/affidavit.css') }}" id="theme-stylesheet" />
<link rel="stylesheet" href="{{ asset('admintheme/css/nomination.css') }}" id="theme-stylesheet" />
<link rel="stylesheet" href="{{ asset('admintheme/css/jquery-ui.css') }}" id="theme-stylesheet" />

<link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }} " type="text/css" />
<link rel="stylesheet" href="{{ asset('appoinment/css/custom.css') }} " type="text/css" />
<link rel="stylesheet" href="{{ asset('appoinment/css/custom-dark.css') }} " type="text/css" />
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
				
				@if ($message = Session::get('error'))
					<br />
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
		
		
 <div class="container-fliud">
        <div class="step-wrap mt-4">
            <ul class="affidavit_nav">
                <li class="step-current"><b>&#10004;</b><span><a href="{{url($menu_action.'affidavitdashboard')}}">{{Lang::get('affidavit.initial_details') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'affidavit/candidatedetails')}}">{{Lang::get('affidavit.candidate_details') }}</a></span></li>
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
    </div>
        <section>
            <div class="container p-0">
                <div class="row">
                    <div class="card">
                        <form id="election_form" onsubmit="return validateFormInitialDetails()" method="POST" action="{{ url($action) }}" autocomplete="off" enctype="x-www-urlencoded">
                            {{ csrf_field() }}
                        <input type="hidden" name="editId" value="{{ Request::segment(3) }}">    
                        <div class="card-header">
						<div class="row mt-4 text-center">
						<div class="col-md-4 offset-md-3">
						<span>{{Lang::get('affidavit.choose_the_language_to_file_e_affidavit')}} :</span>
						</div>
							<?php $locale = "en";
                                if(Session::get('locale'))
                                {
                                    $locale = Session::get('locale');
                                }
                                ?>
												
						<div class="col-md-2">
						<select class="form-control" id="locale" onchange="javascript:change_locale()">
							<option value="en" <?php if($locale=="en") echo "selected='selected'"; ?> >English</option>
                            <option value="hi" <?php if($locale=="hi") echo "selected='selected'"; ?>>Hindi</option>
						</select>
						</div>
						<div class="col-md-12 mt-2 mb-2">
						<small>{{Lang::get('affidavit.note')}} : {{Lang::get('affidavit.the_report_will_be_available_in_the_above_selected_language')}}</small>
						</div>
						
						</div>
						

						
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-12"><h4 class="main_heading">{{Lang::get('affidavit.initial_details')}}</h4></div>
                                    <div class="text-center py-2"><strong>{{Lang::get('affidavit.please_fill_the_following_details_for_the_contesting_candidate') }}</strong></div>
                                  <div class="row">
                                        <div class="col-sm-6 mx-auto">
                                            <div class="searchBox mt-4">
                                                <input class="form-control alphanumeric" id="epic_no" type="search" maxlength="20" placeholder="Search by EPCI No." value="{{ $user_profile_data->epic_no }}">
                                                <button class="btn btn-success" type="button" onclick="return getDetailsByEPIC();">{{Lang::get('affidavit.search')}}</button>
                                            </div>
                                            <div id="error_epic"></div>
                                        </div>
                                  </div>
                                    <div class="col-md-6 float-right">                             
                                        <div id="error_epic"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                  <div class="form-group">
                                    <label for="Name of the Contesting Candidate">{{Lang::get('affidavit.as_in_epic')}} <span class="red">*</span></label>
                                     @if(empty($getData))
                                        <input onkeypress="return blockSpecialChar_name(event)"class="form-control" id="name_on_epic" name="name_on_epic" readonly="" value="{{ $user_profile_data->name }}" />
                                     @else
                                        <input onkeypress="return blockSpecialChar_name(event)" type="text" class="form-control" id="name_on_epic" name="name_on_epic" readonly="" value="{{ $user_profile_data->name }}" />
                                     @endif
                                  </div>
                                </div>
                                <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="statename">{{Lang::get('affidavit.as_in_affidavit')}}</label>
                                    @if($user_profile_data->name!="")
                                        <input onkeypress="return blockSpecialChar_name(event)" type="text" class="form-control" id="candidate_name" name="candidate_name" value=" @if(@$session_data->cand_name) {{ @$session_data->cand_name }}  @else {{ $user_profile_data->name }} @endif" />
                                    @else
                                        <input onkeypress="return blockSpecialChar_name(event)" type="text" class="form-control" id="candidate_name" name="candidate_name" value="{{ @$getData->cand_name }}" />
                                    @endif
                                </div>
                                </div>
                            </div> 
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="statename">{{Lang::get('affidavit.for_state')}} <span class="red">*</span></label>
                                    </div>
                                    <div class="col">
                                        <div class="" style="width: 100%;">
                                            @if(!empty($getData))
											
                                            <select name="state_name" onchange="getAClist(this.value);" class="form-control" id="st_code">
                                                <option value="">-{{Lang::get('affidavit.select_state_name')}}</option>
                                                @forelse($getState as $state)
                                                <option value="{{ $state->ST_CODE }}" <?php if($state->ST_CODE==$getData->st_code) echo 'selected="selected"'; ?>>{{ $state->ST_NAME }} - {{ $state->ST_NAME_HI }}</option>
                                                @empty
                                                <option value="0">{{Lang::get('affidavit.data_not_found')}}!</option>
                                                @endforelse
                                            </select>
                                            @elseif(!empty($session_data))
											
                                                <select name="state_name" onchange="getAClist(this.value);" class="form-control" id="st_code">
                                                <option value="">-{{Lang::get('affidavit.select_state_name')}}</option>
                                                @forelse($getState as $state)
                                                <option value="{{ $state->ST_CODE }}" <?php if($state->ST_CODE == @$session_data->st_code) echo 'selected="selected"'; ?>>{{ $state->ST_NAME }} - {{ $state->ST_NAME_HI }}</option>
                                                @empty
                                                <option value="0">{{Lang::get('affidavit.data_not_found')}}!</option>
                                                @endforelse
                                            </select>
                                            @else
                                            <select name="state_name" onchange="getAClist(this.value);" class="form-control" id="st_code">
                                                <option value="">-{{Lang::get('affidavit.select_state_name')}}-</option>
                                                @forelse($getState as $state)
                                                <option value="{{ $state->ST_CODE }}">{{ $state->ST_NAME }} - {{ $state->ST_NAME_HI }}</option>
                                                @empty
                                                <option value="0">{{Lang::get('affidavit.data_not_found')}}!</option>
                                                @endforelse
                                                </select>
                                            @endif
                                            <span class="error_st_code"></span>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ac">{{Lang::get('affidavit.from_name_of_the_constituency')}} <span class="red">*</span></label>
                                    </div>
                                    <div class="col">
                                        <div class="" style="width: 100%;">
                                            @if(!empty($getData))
                                            <select onkeypress="return blockSpecialChar(event)" name="ac_name" class="consttype form-control aclist" id="ac_no">
                                                <option value="">-{{Lang::get('affidavit.select_ac_name')}}-</option>
												@php $acList = getacbystate($session_data->st_code); @endphp
												@foreach($acList as $raw)
                                               <option value="{{@$raw->AC_NO}}" <?php if(@$raw->AC_NO == $getData->ac_no) echo 'selected="selected"'; ?>>{{@$raw->AC_NO}}-{{ @$raw->AC_NAME }} </option>											   
											   @endforeach
											   											   
                                            </select>
                                            @elseif(!empty($session_data))
                                            <select onkeypress="return blockSpecialChar(event)" name="ac_name" class="consttype form-control aclist" id="ac_no">
											<option value="">-{{Lang::get('affidavit.select_ac_name')}}-</option>
												@php $acList = getacbystate($session_data->st_code); @endphp
												@foreach($acList as $raw)
                                               <option value="{{@$raw->AC_NO}}" <?php if(@$raw->AC_NO == $session_data->ac_no) echo 'selected="selected"'; ?>>{{@$raw->AC_NO}}-{{ @$raw->AC_NAME }} </option>											   
											   @endforeach
											
                                                
                                              
                                            </select>
                                            @else
                                                <select onkeypress="return blockSpecialChar(event)" name="ac_name" class="consttype form-control aclist" id="ac_no">
                                                <option value="">-{{Lang::get('affidavit.select_ac_name')}}-</option>
                                            </select>
                                            @endif
                                            <span class="error_ac"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="st_code_by_epic" value="" id="st_code_by_epic">
                            <input type="hidden" name="st_name_by_epic" value="" id="st_name_by_epic">

                            <input type="hidden" name="dist_no_by_epic" value="" id="dist_no_by_epic">
                            <input type="hidden" name="dist_name_by_epic" value="" id="dist_name_by_epic">

                            <input type="hidden" name="ac_no_by_epic" value="" id="ac_no_by_epic">
                            <input type="hidden" name="ac_name_by_epic" value="" id="ac_name_by_epic">

                            <input type="hidden" name="part_number_by_epic" value="" id="part_number_by_epic">
                            <input type="hidden" name="serial_no_by_epic" value="" id="serial_no_by_epic">
                            
                            <div class="card-footer footerSection">
                                <div class="row">
                                    <div class="col-12">
                                        <a href="{{ url()->previous() }}" class="float-left backBtn">{{Lang::get('affidavit.back') }}</a>
                                            
                                            <button type="submit" class="float-right nextBtn">{{Lang::get('affidavit.save') }} &amp; {{Lang::get('affidavit.next') }}</button>

                                             <a href="{{url()->previous() }}" class="float-right cencelBtn mr-2">{{Lang::get('affidavit.cancel') }}</a>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>    
                    </div>
                </div>
            </div>
            <div id="overlay" style="display:none;">
                <div class="spinner"></div>
                <br/>
                Fetching details...
            </div>
        </section>
</main>
@endsection @section('script')
<script type="text/javascript" src="{{ asset('affidavit/js/affidavit_validation.js') }}"></script>
<script type="text/javascript" src="{{ asset('affidavit/js/remove_special_character.js') }}"></script>
<script type="text/javascript">

    $( document ).ready(function() {

    
  $('#epic_no').keyup(function() {
  if (this.value.match(/[^a-zA-Z0-9/]/g)) {
    this.value = this.value.replace(/[^a-zA-Z0-9/]/g, '');
  } 
     });

  });

function validateFormInitialDetails(){
	var flag = false;
  var epic_no = $("#epic_no").val();
	var candidate_name = $("#candidate_name").val();
	var st_code = $("#st_code").val();
	var district_no = $("#district_no").val();
	var ac_no = $("#ac_no").val();
	
	var can_len = $("#candidate_name").val().length;
	
	var minLength = 3;
	var maxLength = 100;
	
  if($("#epic_no").val() == ""){
    $("#error_epic").html("<label class='text-danger'>{{Lang::get("affidavit.please_enter_the_epic_no") }}</label>");
    flag = true;
  } else {
    $("#error_epic").html('');
  }

  if($("#candidate_name").val() == ""){
    $(".error_candidate").html('<label class="text-danger">{{Lang::get("affidavit.candidate_name_cannot_be_empty") }}</label>');
    flag = true;
  } else if(can_len < minLength){
    $(".error_candidate").html('<label class="text-danger">{{Lang::get("affidavit.length_is_short_minimum_required") }}</label>');
    flag = true;
  } else if(can_len > maxLength){
    $(".error_candidate").html('<label class="text-danger">Length is not valid, maximum '+maxLength+' characters allowed.</label>');
    flag = true;
  } else {
    $(".error_candidate").html('');
  }

  if($("#st_code").val() == "" && $("#st_code").val()==""){
    $(".error_st_code").html('<label class="text-danger">{{Lang::get("affidavit.please_select_state_name") }}</label>');
    flag = true;
  } else {
    $(".error_st_code").html('');
  }

  if($("#district_no").val() == "" && $("#district_no").val()==""){
    $(".error_district").html('<label class="text-danger">{{Lang::get("affidavit.please_select_district_name") }}</label>');
    flag = true;
  } else {
    $(".error_district").html('');
  }

  if($("#ac_no").val() == "" && $("#ac_no").val()==""){
    $(".error_ac").html('<label class="text-danger">{{Lang::get("affidavit.please_select_ac_name") }}</label>');
    flag = true;
  } else {
    $(".error_ac").html('');
  }
  if(flag){
        return false;
    }
}
</script>
<script type="text/javascript">
function getDetailsByEPIC(){
    var epic = $("#epic_no").val();
    if(epic==""){
        $("#error_epic").html('<label class="text-danger">{{Lang::get("affidavit.please_enter_the_epic_no") }}</label>');
        return false;
    }
    $.ajax({
        url: "{{ route('search.epic') }}",
        type: 'GET',
        data: 'epic_no='+$('#epic_no').val(),
        dataType: 'json', 
        beforeSend: function() {
            $('#overlay').show();
        },      
        success: function(response) {
        if(response.status == 200 && response.error == false){
           $("#candidate_name").val(response.result.fullName);
            $("#name_on_epic").val(response.result.fullName);

            $("#st_name_by_epic").val(response.result.stateName);
            $("#st_code_by_epic").val(response.result.stateCd);

            $("#dist_name_by_epic").val(response.result.districtValue);
            $("#dist_no_by_epic").val(response.result.districtNo);

            $("#ac_name_by_epic").val(response.result.asmblyName);
            $("#ac_no_by_epic").val(response.result.acNumber);

            $("#part_number_by_epic").val(response.result.partNumber);
            $("#serial_no_by_epic").val(response.result.partSerialNumber);

        } else if(response.status == 401 && response.result=="") {
            $("#error_epic").html('<label class="text-danger">'+response.msg+'</label>');
          } else {
            //alert('Please try again, Something wrong with EPIC number');
          }
        },complete: function() {
          $('#overlay').hide();
        }
      });
}

window.onload = (event) => {
    function loadStateDistrict($st_code,$dist_code,$acId){
        alert($st_code + $dist_code + $acId);
       
    }
}

/* function getDistrictList(val){
    var state_code = val;
    $.ajax({
        type: 'get',
        url: '{{ route('getdistricts') }}',
        data: 'state_code='+btoa(state_code),
        beforeSend: function(){
            $("#loader2").show();
        },success: function(response){
            var items="";
                items += "<option value=''>--Select District--</option>";
            $.each(response.result,function(index, item) {
                // if(item.AC_NO == $acId){
                //     items+="<option selected='selected' value='"+item.AC_NO+"'>"+item.AC_NAME+"</option>";
                // } else {
                    items+="<option value='"+item.DIST_NO+"'>"+item.DIST_NAME+"</option>";    
                // }
                
            });
            $(".districtlist").html(items);
        },complete:function(response){
            $("#loader2").hide();
        }
    });
    return false;
} */
function getAClist(st_code) {
	
	//alert('aaa');
	
    //var state_code = $("#st_code").val();
   // var state_code = st_code;
    //alert(state_code + dist_code);
    $.ajax({
        type: 'get',
        url: '{{ route('getACList') }}',
        data: 'state_code=' + btoa(st_code),
        beforeSend: function(){
            $("#loader2").show();
        },success: function(response){
            var items="";
                items += "<option value=''>--{{Lang::get("affidavit.select_ac_name") }}--</option>";
            $.each(response.result,function(index, item) {
                // if(item.AC_NO == $acId){
                //     items+="<option selected='selected' value='"+item.AC_NO+"'>"+item.AC_NAME+"</option>";
                // } else {
                    //items+="<option //value='"+item.AC_NO+"'>"+item.AC_NO+"-"+item.AC_NAME+"-"+item.AC_NAME_HI+"</option>";

                     items+="<option value='"+item.AC_NO+"'>"+item.AC_NO+"-"+item.AC_NAME+"</option>";    
                //}
                
            });
            $(".aclist").html(items);
        },complete:function(response){
            $("#loader2").hide();
        }
    });
    return false;
}    
</script>
<script type="text/javascript">
    function change_locale()
    {
        var locale = $("#locale").val();
        console.log('{{ url("setlocale") }}/'+locale);
        window.location.href = '{{ url("setlocale") }}/'+locale;
    }
</script>
@endsection
