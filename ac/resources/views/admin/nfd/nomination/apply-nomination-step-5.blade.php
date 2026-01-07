@extends('admin.layouts.ac.theme')
    @section('content')
    <style type="text/css">
     .error{font-size: 12px; color: red;}
        .display_none{display: none;}
        .form_steps p{padding: 15px 15px;}
        .heading-part1 p{padding: 0px !important;}
        .fullwidth{width: 100%;float: left;}
        #imagePreview{width: 150px;height: 150px; border: 1px solid #efefef;}
        .button-next{margin-top: 30px;}
        .button-next button{float: right;}
		ul{margin-bottom:0px;}		
		.marked {display: block; position: relative; padding-left: 35px; margin-bottom: 12px; cursor: pointer; font-size: 22px; -webkit-user-select: none; -moz-user-select: none;  -ms-user-select: none;  user-select: none;}
.marked input { position: absolute; opacity: 0; cursor: pointer;}
.checkmark { position: absolute; top: 0; left: 0; height: 25px; width: 25px; background-color: #eee; border-radius: 50%;}
.marked:hover input ~ .checkmark {background-color: #ccc;}
.marked input:checked ~ .checkmark { background-color: #c1448f;}
.checkmark:after {content: ""; position: absolute; display: none;}
.marked input:checked ~ .checkmark:after {display: block;}
.marked .checkmark:after {top: 9px;	left: 9px;	width: 8px;	height: 8px;	border-radius: 50%;	background: white;}
.information {padding-bottom: 10px;  position: relative;   padding-left: 32px;  text-transform: uppercase;  letter-spacing: 1px;  color: #bb4292;}
fieldset{width:100%;}
.nomination-detail .form-control{margin: 0 0 5px 0;   padding: 0 10px;   font-size: 14px;   font-weight: 500;}.nomination-field-2.date_jqueryui{background:transparent;}
    </style>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" id="theme-stylesheet">
    <link rel="stylesheet" href="{{ asset('admintheme/css/jquery-ui.css') }}" id="theme-stylesheet">
    <main role="main" class="inner cover mb-3">
      <section class="mt-5">    
        

        <div class="container">
           @if(count($errors->all())>0)
             <div class="alert alert-danger">
              <ul>
               @foreach($errors->all() as $iterate_error)
               <li><p class="text-left">{!! $iterate_error !!}</p></li>
               @endforeach
             </ul>
           </div>
           @endif
           @if (session('flash-message'))
           <div class="alert alert-success"> {{session('flash-message') }}</div>
           @endif 
           @if (session('success_mes'))
           <div class="alert alert-success"> {{session('success_mes') }}</div>
           @endif  
</div>		   
   </section>
   <div class="container">

  <div class="row">
   <div class="col-md-12 mt-3">
     <ul style="text-align:center;margin-bottom:40px;" class="arrow-steps clearfix">
       <li class="step step1  first">Part I/II</li>
       <li class="step step2 ">Part III<span></span></li>
       <li class="step step3 current">Part IIIA<span></span></li>
       <li class="step step4">Upload Affidavit<span></span></li>
       <li class="step step4">Finalize Application<span></span></li>
     </ul>
   </div>
 </div>

</div>
   <section>
    <div class="container p-0">
      <div class="row">

        <div class="col-md-12">
          <div class="card">
           <div class="card-header d-flex align-items-center">
             <h4>{!! $heading_title !!}</h4>
           </div>
		     <form method="post" action="{!! $action !!}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="nomination_id" value="{{$nomination_id}}">
                    <input type="hidden" name="st_code" value="{{$st_code}}">
                    <input type="hidden" name="ac_no" value="{{$ac_no}}">
                    <input type="hidden" name="election_id" value="{{$election_id}}">
           <div class="card-body form-inline">
             <div class="row">
               <div class="col">
                 <div class="nomination-detail">
             
                 

                 <div class="part3 form_steps">
                

                    
                    <div class="nomination-parts">
                      <div class="nomination-form-heading text-center">
                        <h3><strong>PART IIIA </strong></h3>
                        (To be filled by the candidate) 
                      </div>

                      <div class="nomination-detail">
                        <div class="criminal-section">
                         (1) Whether the candidate—
                          <ul class="Ullist mb-3">
							  <li>(i) has been convicted— 
								  <ul class="">
								  <li>(a) of any offense(s) under sub-section (1) or</li> <br />
								  <li>(b) for contravention of any law specified in sub-section (2), of section 8 of the Representation of the People Act, 1951 (43 of 1951); or -</li> <br />
								  </ul>
							  </li> <br />
						  <li>(ii) has been convicted for any other offense(s) for which he has been sentenced to imprisonment for two years or more. </li>
						 </ul>
                        </div>  
						<div class="askQue mt-2 mb-3">
							<div class="row">
							
								<div class="col pl-5"><label class="case marked">Yes
													  @if($have_police_case=='1')
													  <input type="radio" class="have_police_case form-control" name="have_police_case" value="1" checked="checked">
													  @else
													  <input type="radio" class="have_police_case form-control" name="have_police_case" value="1" >
													  @endif
													  <span class="checkmark"></span>
													</label></div>
								<div class="col pl-5"> <label class="case marked">No
													  @if($have_police_case=='2')
													  <input type="radio" class="have_police_case form-control" name="have_police_case" value="2" checked="checked">
													  @else
													  <input type="radio" class="have_police_case form-control" name="have_police_case" value="2">
													  @endif
													  <span class="checkmark"></span>
													</label></div>
													<div class="col-md-7"></div>
							</div>
						
                      
                       </div>
						
                        <!-- Police Case -->
                        
                        <div class="criminal-section have_police_case_div field_wrapper display_none">      
                         <div class="information"><strong>[If the answer is "Yes", the candidate shall furnish the following information:]</strong></div>

                          <?php $i = 0; $case_heading = 1;?>
                          <div class="fullwidth have_police_case_record">
                            
                            <table class="table">
                              <tbody class="police_case_body"> 
                                @foreach($police_cases as $iterate_police_case)
                               <tr class="row_class_{{$i}}">
                                 <td class="nomination-detail" colsan="2">
								 <fieldset>
								 <legend><h5>Case <?php echo $case_heading; ?></h5></legend>
								 
                                    <ol type="i">
                                      <li>(i) Case/first information report No./Nos <input type="text" name="police_case[{{$i}}][case_no]" class="form-control nomination-field-2 " value="{{$iterate_police_case['case_no']}}"></li>
                                      <li>(ii) Police station(s)<input type="text" name="police_case[{{$i}}][police_station]" class="form-control nomination-field-2" value="{{$iterate_police_case['police_station']}}"><br>
                                        State(s) 
                                        <select name="police_case[{{$i}}][case_st_code]" class="form-control nomination-field-2" onchange="load_district(this.value, <?php echo $i ?>)">
                                          <option value="">-- Select States --</option>
                                          @foreach($states as $iterate_state)
                                          @if($iterate_state['st_code'] == $iterate_police_case['case_st_code'])
                                          <option value="{{$iterate_state['st_code']}}" selected="selected">{{$iterate_state['st_name']}}</option>
                                          @else
                                          <option value="{{$iterate_state['st_code']}}">{{$iterate_state['st_name']}}</option>
                                          @endif
                                          @endforeach
                                        </select>
                                        District(s) 
                                        <select name="police_case[{{$i}}][case_dist_no]" class=" form-control nomination-field-2 district_<?php echo $i ?>" style="width:160px">
                                          <option value="">-- Select Ditricts --</option>
                                          @foreach($districts as $iterate_district)
                                          @if($iterate_district['st_code'] == $iterate_police_case['case_st_code'])

                                          @if($iterate_district['st_code'] == $iterate_police_case['case_st_code'] and  $iterate_district['district_no'] == $iterate_police_case['case_dist_no'])
                                          <option value="{{$iterate_district['district_no']}}" selected="selected">{{$iterate_district['district_name']}}</option>
                                          @else
                                          <option value="{{$iterate_district['district_no']}}">{{$iterate_district['district_name']}}</option>
                                          @endif

                                          @endif
                                          @endforeach
                                        </select>
                                      </li>
                                      <li>(iii) Section(s) of the concerned Act(s) and brief description of the offense(s) for which he has been convicted <input type="text" name="police_case[{{$i}}][convicted_des]" class="form-control nomination-field-2" value="{{$iterate_police_case['convicted_des']}}"></li>

                                      <li>(iv)Date(s) of conviction(s) <input id="date_of_conviction<?php echo $i ?>" type="text" name="police_case[{{$i}}][date_of_conviction]" class="form-control  nomination-field-2 date_jqueryui" readonly="readonly" value="{{$iterate_police_case['date_of_conviction']}}"></li>
                                      
                                      <li>(v) Court(s) which convicted the candidate <input type="text" name="police_case[{{$i}}][court_name]" class="form-control nomination-field-2" value="{{$iterate_police_case['court_name']}}"></li>

                                      <li>(vi)Punishment(s) imposed [indicate period of imprisonment(s) and/or quantum offine(s)] <input type="text" name="police_case[{{$i}}][punishment_imposed]" class="form-control nomination-field-2" value="{{$iterate_police_case['punishment_imposed']}}"></li>

                                      <li>(vii) Date(s) of release from prison <input type="text" id="date_of_release<?php echo $i ?>" name="police_case[{{$i}}][date_of_release]" class="form-control nomination-field-2 date_jqueryui" readonly="readonly" value="{{$iterate_police_case['date_of_release']}}"></li>
                                      <li>(viii)Was/were any appeal(s)/revision(s) filed against above conviction(s) 
                                        <select name="police_case[{{$i}}][revision_against_conviction]" class="form-control nomination-field-2 against_conviction">
                                          @foreach($yes_no_lists as $iterate_lis)
                                          @if($iterate_lis['id'] == $iterate_police_case['revision_against_conviction'])
                                          <option value="{{$iterate_lis['id']}}" selected="selected">{{$iterate_lis['name']}}</option>
                                          @else
                                          <option value="{{$iterate_lis['id']}}">{{$iterate_lis['name']}}</option>
                                          @endif
                                          @endforeach
                                        </select>

                                        <div class="revisedfiled">
                                        </div></li>
										<li>(ix) Date and particulars of appeal(s)/application(s) for revision filed <input type="text" id="revision_appeal_date<?php echo $i ?>" name="police_case[{{$i}}][revision_appeal_date]" class="form-control nomination-field-2 date_jqueryui" readonly="readonly" value="{{$iterate_police_case['revision_appeal_date']}}"></li>

                                        <li>(x) Name of the court(s) before which the appeal(s)/application(s) for revision filed <input type="text" name="police_case[{{$i}}][rev_court_name]" class="form-control nomination-field-2" value="{{$iterate_police_case['rev_court_name']}}"></li>

                                        <li>(xi) Whether the said appeal(s)/application(s) for revision has/have been disposed of or is/are pending 
                                          <select name="police_case[{{$i}}][status]" class="form-control nomination-field-2 status">
                                            @foreach($yes_no_lists as $iterate_lis)
                                            @if($iterate_lis['id'] == $iterate_police_case['status'])
                                            <option value="{{$iterate_lis['id']}}" selected="selected">{{$iterate_lis['name']}}</option>
                                            @else
                                            <option value="{{$iterate_lis['id']}}">{{$iterate_lis['name']}}</option>
                                            @endif
                                            @endforeach

                                          </select>
                                        </li>

                                        <li class="statusReport">(xii) If the said appeal(s)/application(s) for revision has/have been disposed of—

                                          <ul>
                                            <li>(a) Date(s) of disposal <input type="text" id="revision_disposal_date<?php echo $i ?>" name="police_case[{{$i}}][revision_disposal_date]" class="form-control date_jqueryui"  readonly="readonly" value="{{$iterate_police_case['revision_disposal_date']}}"></li>
                                            
                                            <li>(b) Nature of order(s) passed <input type="text" name="police_case[{{$i}}][revision_order_description]" class="form-control nomination-field-2" value="{{$iterate_police_case['revision_order_description']}}"></li>
                                          </ul>
                                        </li>

                                        
                                        
                                      </ol>
                                      <button type="button" class="btn btn-default remove_police_case mb-3 float-right" id="remove_<?php echo $i; ?>" onclick="remove_police_case('row_class_<?php echo $i ?>')">Remove Case</button>
									  </fieldset>
                                    </td>
                                  </tr>
                                  
                                  <?php $i++; $case_heading++; ?>
                                  @endforeach
                                </tbody>
                                <tfoot>
                                  <tr>
                                       <td>
                                      
                                    </td>
                                    <td class="text-right">
                                      <button type="button" class="btn btn-primary add_police_case">Add Case</button>
                                    </td>
                                 
                                  </tr>
                                </tfoot>
                              </table>
                              
                            </div>


                          </div>
                          

                          <!-- End Police Case -->

                        </div>



                        <div class="casesec">
                       (2) Whether the candidate is holding any office of profit under the Government of India or State Government? <select name="profit_under_govt" class="form-control nomination-field-2">
                            @foreach($yes_no_lists as $iterate_lis)
                            @if($iterate_lis['id'] == $profit_under_govt)
                            <option value="{{$iterate_lis['id']}}" selected="selected">{{$iterate_lis['name']}}</option>
                            @else
                            <option value="{{$iterate_lis['id']}}">{{$iterate_lis['name']}}</option>
                            @endif
                            @endforeach
                          </select>
                        <small>(Yes/No)</small>
                        <ul><li>If Yes, details of the office held <input type="text" name="office_held" class="form-control nomination-field-2" value="{{$office_held}}"></li></ul>

                       (3) Whether the candidate has been declared insolvent by any Court?  <select name="court_insolvent" class="form-control nomination-field-2">
                          @foreach($yes_no_lists as $iterate_lis)
                          @if($iterate_lis['id'] == $court_insolvent)
                          <option value="{{$iterate_lis['id']}}" selected="selected">{{$iterate_lis['name']}}</option>
                          @else
                          <option value="{{$iterate_lis['id']}}">{{$iterate_lis['name']}}</option>
                          @endif
                          @endforeach
                        </select><small>(Yes/No)</small>
                        <ul><li>-If Yes, has he been discharged from insolvency <input type="text" name="discharged_insolvency" class="form-control nomination-field-2" value="{{$discharged_insolvency}}"></li></ul>

                      (4) Whether the candidate is under allegiance or adherence to any foreign country? <select name="allegiance_to_foreign_country" class="form-control nomination-field-2">
                          @foreach($yes_no_lists as $iterate_lis)
                          @if($iterate_lis['id'] == $allegiance_to_foreign_country)
                          <option value="{{$iterate_lis['id']}}" selected="selected">{{$iterate_lis['name']}}</option>
                          @else
                          <option value="{{$iterate_lis['id']}}">{{$iterate_lis['name']}}</option>
                          @endif
                          @endforeach
                        </select><small>(Yes/No)</small>
                        <ul><li>-If Yes, give details <input type="text" name="country_detail" class="form-control nomination-field-2" value="{{$country_detail}}"> </li></ul>

                       (5) Whether the candidate has been disqualified under section 8A of the said Act by an order of the President? <select name="disqualified_section8A" class="form-control nomination-field-2">
                          @foreach($yes_no_lists as $iterate_lis)
                          @if($iterate_lis['id'] == $disqualified_section8A)
                          <option value="{{$iterate_lis['id']}}" selected="selected">{{$iterate_lis['name']}}</option>
                          @else
                          <option value="{{$iterate_lis['id']}}">{{$iterate_lis['name']}}</option>
                          @endif
                          @endforeach
                        </select><small>(Yes/No)</small>
                        <ul><li>-If Yes, the period for which disqualified <input type="text" name="disqualified_period" class="form-control nomination-field-2"  value="{{$disqualified_period}}"></li></ul>

                      (6) Whether the candidate was dismissed for corruption or for disloyalty while holding office under the Government of India or the Government of any State? <select name="disloyalty_status" class="form-control nomination-field-2">
                          @foreach($yes_no_lists as $iterate_lis)
                          @if($iterate_lis['id'] == $disloyalty_status)
                          <option value="{{$iterate_lis['id']}}" selected="selected">{{$iterate_lis['name']}}</option>
                          @else
                          <option value="{{$iterate_lis['id']}}">{{$iterate_lis['name']}}</option>
                          @endif
                          @endforeach
                        </select><small>(Yes/No)</small>
                        <ul><li>-If Yes, the date of such dismissal <input type="text" id="date_of_dismissal" name="date_of_dismissal" class="form-control nomination-field-2 date_jqueryui" value="{{$date_of_dismissal}}" readonly="readonly"></li></ul>

                        (7) Whether the candidate has any subsisting contract(s) with the Government either in individual capacity or by trust or partnership in which the candidate has a share for supply of any goods to that Government or for execution of works undertaken by that Government?<select name="subsiting_gov_taken" class="form-control nomination-field-2">
                          @foreach($yes_no_lists as $iterate_lis)
                          @if($iterate_lis['id'] == $subsiting_gov_taken)
                          <option value="{{$iterate_lis['id']}}" selected="selected">{{$iterate_lis['name']}}</option>
                          @else
                          <option value="{{$iterate_lis['id']}}">{{$iterate_lis['name']}}</option>
                          @endif
                          @endforeach
                        </select><small>(Yes/No)</small>
                        <ul><li>-If Yes, with which Government and details of subsisting contract(s)<input type="text" name="subsitting_contract" class="form-control nomination-field-2" value="{{$subsitting_contract}}"></li></ul>

                       (8) Whether the candidate is a managing agent, or manager or Secretary of any company or Corporation (other than a cooperative society) in the capital of which the Central/ Government or State Government has not less than twenty-five percent share?<select name="managing_agent" class="form-control nomination-field-2">
                          @foreach($yes_no_lists as $iterate_lis)
                          @if($iterate_lis['id'] == $managing_agent)
                          <option value="{{$iterate_lis['id']}}" selected="selected">{{$iterate_lis['name']}}</option>
                          @else
                          <option value="{{$iterate_lis['id']}}">{{$iterate_lis['name']}}</option>
                          @endif
                          @endforeach
                        </select><small>(Yes/No)</small>
                        <ul><li>-If Yes, with which Government and the details thereof <input type="text" name="gov_detail" class="form-control nomination-field-2" value="{{$gov_detail}}"></li></ul>

                       (9) Whether the candidate has been disqualified by the Commission under section 10A of the said Act<select name="disqualified_by_comission_10Asec" class="form-control nomination-field-2">
                          @foreach($yes_no_lists as $iterate_lis)
                          @if($iterate_lis['id'] == $disqualified_by_comission_10Asec)
                          <option value="{{$iterate_lis['id']}}" selected="selected">{{$iterate_lis['name']}}</option>
                          @else
                          <option value="{{$iterate_lis['id']}}">{{$iterate_lis['name']}}</option>
                          @endif
                          @endforeach
                        </select><small>(Yes/No)</small>
                        <ul><li>-If yes, the date of disqualification <input type="text" id="date_of_disqualification" name="date_of_disqualification" class="form-control nomination-field-2 date_jqueryui" value="{{$date_of_disqualification}}" readonly="readonly"></li></ul>

                      </div>


                      <div class="nomination-signature">
                        <span class="nomination-date left">Date: <input type="text" name="date_of_disloyal" id="date_of_disloyal" class="nomination-field-4 date_jqueryui" value="{{$date_of_disloyal}}" readonly="readonly"></span>
                        
                      </div>
                      
                    </div>
 </div>
             
            </div>  </div>
        </div></div>




               <div class="card-footer">
          <div class="form-group row ">
            <div class="col">
        
            </div>
            <div class="col ">
              <div class="form-group row float-right">
   
              <button type="submit" class="btn btn-primary save_next float-right">Next</button>
            </div>
            </div>
            </div>
         </div>

                

               

            

         
      
	  </form>
    </div>
  </div>
</div>
</div>    
</section>

</main>
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('admintheme/js/jquery-ui.js') }}"></script>

<script>
  $(document).ready(function(){  
   if($('#breadcrumb').length){
     var breadcrumb = '';
     $.each({!! json_encode($breadcrumbs) !!},function(index, object){
      breadcrumb += "<li><a href='"+object.href+"'>"+object.name+"</a></li>";
    });
     $('#breadcrumb').html(breadcrumb);
   }


   $('.have_police_case').change(function(e){
    have_police_case();
  });

   initailize_datepicker();
   have_police_case();

   var i = "<?php echo $i ?>";
   var case_heading = "<?php echo $case_heading ?>";
   
   $('.add_police_case').click(function(e){
    var remove_case = "remove_"+i;
    var row_class = "row_class_"+i;
    var html = "";
    html += "<tr class='"+row_class+"'>";
    html += "<td colspan='2' class='nomination-detail'>";
    html += "<fieldset>";
    html += "<legend class='text-center'><h6 class='p-2'> Case "+case_heading+" </h6></legend>";
    html += "<ol type='i'>";
    html += "<li>";
    html += "Case/first information report No./Nos";
    html += "<input type='text' class='form-control' name='police_case["+i+"][case_no]'>";
    html += "</li>";
    html += "<li>";
    html += "Police station(s)";
    html += "<input type='text' class='form-control' name='police_case["+i+"][police_station]'>";
    html += "State(s) ";
    html += "<select name='police_case["+i+"][case_st_code]' class='form-control' onchange='load_district(this.value, "+i+")'>";
    html += "<option value=''>-- Select States --</option>";
    <?php foreach($states as $iterate_state){ ?>
      html += "<option value='{{$iterate_state['st_code']}}'>{{$iterate_state['st_name']}}</option>";
    <?php } ?>
    html += "</select>";
    html += "District(s) ";
    html += "<select name='police_case["+i+"][case_dist_no]' class='form-control district_"+i+"' style='width:160px;'>";
    html += "<option value=''>-- Select Ditricts --</option>";
    html += "</select>";
    html += "</li>";
    html += "<li>";
    html += "Section(s) of the concerned Act(s) and brief description of the offense(s) for which he has been convicted"; 
    html += "<input type='text' class='form-control' name='police_case["+i+"][convicted_des]' value='' >";
    html += "</li>";
    html += "<li>";
    html += "Date(s) of conviction(s) ";
    html += "<input id='date_of_conviction"+i+"' type='text' name='police_case["+i+"][date_of_conviction]' class='form-control nomination-field-2 date_jqueryui' value=''  readonly='readonly'>";
    html += "</li>";
    html += "<li>";
    html += "Court(s) which convicted the candidate ";
    html += "<input type='text' name='police_case["+i+"][court_name]' class='form-control' value=''>";
    html += "</li>";
    html += "<li>";
    html += "Punishment(s) imposed [indicate period of imprisonment(s) and/or quantum offine(s)]";
    html += "<input type='text' name='police_case["+i+"][punishment_imposed]' class='form-control' value=''>";
    html += "</li>";
    html += "<li>";
    html += "Date(s) of release from prison ";
    html += "<input type='text' id='date_of_release"+i+"' name='police_case["+i+"][date_of_release]' class='form-control nomination-field-2 date_jqueryui' value=''  readonly='readonly'>";
    html += "</li>";
    html += "<li>";
    html += "Was/were any appeal(s)/revision(s) filed against above conviction(s) ";
    html += "<select name='police_case["+i+"][revision_against_conviction]' class='form-control nomination-field-2 against_conviction'>";
    <?php foreach($yes_no_lists as $iterate_lis){ ?>
      html += "<option value='{{$iterate_lis['id']}}'>{{$iterate_lis['name']}}</option>";
    <?php } ?>
    html += "</select>";
    html += "</li>";
    html += "<li>";
    html += "Date and particulars of appeal(s)/application(s) for revision filed ";
    html += "<input type='text' id='revision_appeal_date"+i+"' value='' name='police_case["+i+"][revision_appeal_date]' class='form-control nomination-field-2 date_jqueryui' readonly='readonly'>";
    html += "</li>";
    html += "<li>";
    html += "Name of the court(s) before which the appeal(s)/application(s) for revision filed ";
    html += "<input type='text' name='police_case["+i+"][rev_court_name]' class='form-control' value=''>";
    html += "</li>";
    html += "<li>";
    html += "Whether the said appeal(s)/application(s) for revision has/have been disposed of or is/are pending"; 
    html += "<select name='police_case["+i+"][status]' class='form-control nomination-field-2 status'>";
    <?php foreach($yes_no_lists as $iterate_lis){ ?>
      html += "<option value='{{$iterate_lis['id']}}'>{{$iterate_lis['name']}}</option>";
    <?php } ?>
    html += "</select>";
    html += "</li>";
    html += "<li class='statusReport'>";
    html += "If the said appeal(s)/application(s) for revision has/have been disposed of—";
    html += "<ul>";
    html += "<li>";
    html += "(a) Date(s) of disposal ";
    html += "<input type='text' id='revision_disposal_date"+i+"' name='police_case["+i+"][revision_disposal_date]' class='form-control date_jqueryui' value='' readonly='readonly'>";
    html += "</li>";
    html += "<li>";
    html += "(b) Nature of order(s) passed ";
    html += "<input type='text' name='police_case["+i+"][revision_order_description]' class='form-control'  value=''>";
    html += "</li>";
    html += "</ul>";
    html += "</li>";
    html += "</ol>";
    html += "<button type='button' class='btn btn-default remove_police_case mb-3 float-right' id='"+remove_case+"' onclick=remove_police_case('"+row_class+"')>Remove Case</button>";
    html += "</fieldset>";
    html += "</td>";
    html += "<tr>";
    $('.police_case_body').append(html);
    /*
    $('.remove_police_case').click(function(e){
      $(this).parent('td').parent('tr').remove();
    });
    */

    if($(".row_class_"+i+" .date_jqueryui").length>0){
      $(".row_class_"+i+" .date_jqueryui").each(function(index,object){
        $(".row_class_"+i+" #"+$(object).attr('id')).datepicker({
          dateFormat: 'dd-mm-yy',
          maxDate: 0,
          changeYear: true
        });
      });
    }

    case_heading++;
    i++;
  });

  });


  function remove_police_case(id){
    $('.'+id).remove();
  }

    function initailize_datepicker(){
      if($('.date_jqueryui').length>0){
        $('.date_jqueryui').each(function(index,object){
          $('#'+$(object).attr('id')).datepicker({
            dateFormat: 'dd-mm-yy',
            maxDate: 0,
            changeYear: true
          });
        });
      }
    }

    function have_police_case(){
      if($(".have_police_case:checked").val() == '1'){
        $('.have_police_case_div').removeClass('display_none');
      }else{
        $('.have_police_case_div').addClass('display_none');
      }
    }

    function load_district(id, row_number){
      html = '';
      html += "<option value=''>Select</option>";
      var districts = <?php echo json_encode($districts); ?>;
      $.each(districts, function(index, object){
        if(object.st_code == id){
          html += "<option value='"+object.district_no+"'>"+object.district_name+"</option>";
        }
      });
      $(".district_"+row_number).empty().append(html);
      $(".district_"+row_number).val($(".district_"+row_number+" option:first").val());
    }


    $(document).ready(function(e){
      <?php foreach($custom_errors as $key => $custom_error){ ?>
        <?php foreach($custom_error as $second_key => $err){ ?>
          <?php if($err){ ?>
            $("input[name = 'police_case[<?php echo $key ?>][<?php echo $second_key ?>]'").after("<span class='text-danger small_text'><?php echo $err; ?></span>");
            $("input[name = 'police_case[<?php echo $key ?>][<?php echo $second_key ?>]'").addClass('is-valid');
            $("select[name = 'police_case[<?php echo $key ?>][<?php echo $second_key ?>]'").after("<span class='text-danger small_text'><?php echo $err; ?></span>");
            $("select[name = 'police_case[<?php echo $key ?>][<?php echo $second_key ?>]'").addClass('is-valid');
          <?php } ?>
        <?php } ?>
      <?php } ?>
    });
  </script>
@if (session('success_mes'))
<script type="text/javascript">
 success_messages("{{session('success_mes') }}");
 </script>
@endif
@if (session('error_mes'))
  <script type="text/javascript">
  error_messages("{{session('error_mes') }}");
</script>
@endif

  @endsection