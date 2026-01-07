@extends( (Auth::user()->role_id != '19') ? 'layouts.theme' : 'admin.layouts.ac.theme')
@section('title', 'Affidavit Cadidate Details') @section('content')
<style type="text/css">
.affidavit_nav .step-current a,.affidavit_nav .step-success a{
  color:#fff!important;
}
.affidavit_nav a{
  color:#999!important;
}
   .err{
   white-space: pre;
   color: red;
   font-size: 11px;
   font-weight: 600;
   }
   .error {
   font-size: 12px;
   color: red;
   }
   .step-wrap.mt-4 ul li {
   margin-bottom: 21px;
   }
   .panel-heading.active {
   background-color: #2c963c;
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
   .width100{
   width: 100px !important;
   }
   .accordion_head {
   font-size: 20px;
   padding: 8px 15px 1px;
   background-color: #007bff;
   color: white;
   cursor: pointer;                
   margin: 5px 0 10px 0;               
   border-radius: 4px;
   overflow: hidden;
   box-shadow: 0 4px 4px -2px rgba(0, 0, 0, 0.5);
   }
   .accordion_body {
   width: 100%;
   padding: 1em;
   box-shadow: 0 4px 4px -2px rgba(0, 0, 0, 0.5);
   margin-top: -10px;
   background: #fafafa;
   border: #e9e9e9 solid 1px;
   }            
   .plusminus {
   float: right;
   font-size: 30px;
   margin-top: -5px;
   }
   .purple {
   background-color: #9b59b6;
   }
   .purpleTable th{
   background-color: #9b59b6!important;
   color: #ffffff;
   }
   .nextBtn, button.nextBtn {
   border: 2px solid #9b59b6;
   padding: 0.65em 1.2em;
   border-radius: 2.5em;
   cursor: pointer;
   min-width: 131px;
   text-align: center;
   transition: all 0.25s;
   margin: 1em auto;
   box-sizing: border-box;               
   display: block;
   font-weight: 500;
   color: #9b59b6;
   outline: none;
   white-space: nowrap;
   }
   .nextBtn:hover , button.nextBtn:hover {
   background-color: #9b59b6;
   color: white;
   outline: none;
   box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
   }  
   .cencelBtn, button.cencelBtn {
   min-width: 131px;
   text-align: center;
   border: 2px solid #dc3545;
   padding: 0.65em 1.2em;
   border-radius: 2.5em;
   cursor: pointer;
   transition: all 0.25s;
   margin: 1em auto;
   box-sizing: border-box;               
   display: block;
   font-weight: 500;
   outline: none;
   white-space: nowrap;
   color: #dc3545;
   text-decoration: none!important;
   }
   .cencelBtn:hover , button.cencelBtn:hover {
   background-color: #dc3545;
   color: white;
   outline: none;
   box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
   }  
   .backBtn, button.backBtn {
   min-width: 131px;
   text-align: center;
   border: 2px solid #868e96;
   padding: 0.65em 1.2em;
   border-radius: 2.5em;
   cursor: pointer;
   transition: all 0.25s;
   margin: 1em auto;
   box-sizing: border-box;               
   display: block;
   font-weight: 400;
   outline: none;
   white-space: nowrap;
   text-decoration: none;
   color:#868e96;
   }
   .backBtn:hover , button.backBtn:hover {
   background-color: #868e96;
   color: white;
   outline: none;
   text-decoration: none;
   box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
   } 
   .footerSection{
   width: 100%;
   background: transparent!important;
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
   .modal-dialog .close, .modal-content button:hover {
   opacity: 1;
   color: #fff;                
   box-shadow: none;
   outline: 0;
   }
   .modal button.close {
   background-color: #f0587e;
   padding: 8px 16px;
   border: none;
   font-size: 20px;
   border: none;
   border: 1px solid #f0587e;
   }
   .footerSection{
        width: 100%;
        background: transparent!important;
    }

	.step-wrap {
			    text-align: center;
			}
			.step-wrap>ul>li {      
			    border-radius: 25px;            
			    padding: 0.15rem 1.05rem 0.15rem 0.18rem;
			}
			.step-wrap>ul>li>span {
			    display: inline-block;
			    vertical-align: middle;
			    width: 60px!important;
			    color: #999;
			    font-size: 0.80rem!important;
			    text-align: center;
			    line-height: 0.95rem!important;
			}
      .form-control {
        padding: .55rem .15rem!important;
      }
	  .table td, .table th {
    padding: .55rem .20rem;
	  }
	  
i.fa.fa-calendar.input-group-text.fa-lg {
    padding: 9px;
}

.calender-model{
	float: right;
    margin-top: -36px;
}
</style>
<!-- <link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }} " type="text/css" />
   <link rel="stylesheet" href="{{ asset('appoinment/css/custom.css') }} " type="text/css" /> -->
<link rel="stylesheet" href="{{ asset('appoinment/css/custom-dark.css') }} " type="text/css" />
<main role="main" class="inner cover mb-3">
   <section>
      <div class="container">
         @if (session('flash-message'))
         <div class="alert alert-success mt-4">{{session('flash-message') }}</div>
         @endif @if ($message = Session::get('Init'))
         <div class="alert alert-success alert-block">
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
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'affidavitdashboard')}}">{{Lang::get('affidavit.initial_details') }}</a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'affidavit/candidatedetails')}}">{{Lang::get('affidavit.candidate_details') }}</a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'affidavit/pending-criminal-cases')}}">{{Lang::get('affidavit.court_cases') }}</a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'Affidavit/MovableAssets')}}">{{Lang::get('affidavit.movable_assets') }}</a></span></li>
                <li class="step-current"><b>&#10004;</b><span><a href="{{url($menu_action.'immovable-assets')}}">{{Lang::get('affidavit.immovable_assets') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'liabilities')}}">{{Lang::get('affidavit.liabilities') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'Profession')}}">{{Lang::get('affidavit.profession') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'education')}}">{{Lang::get('affidavit.education')}}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'preview')}}">{{Lang::get('affidavit.preview_finalize') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'part-a-detailed-report')}}">{{Lang::get('affidavit.reports') }}</a></span></li>
            </ul>
        </div>
    </div>
   <section>
      <div class="col-md-12">
         <div class="row">
            <div class="card">
               <div class="card-header">
                  <div class="row">
                     <div class="col-md-12">
                        <h4 class="main_heading">{{Lang::get('affidavit.immovable_assets')}}</h4>
                     </div>
                  </div>
                  <div class="card-body">
 
    <div class="accordion_head">{{Lang::get('affidavit.agricultural_land')}}<span class="plusminus">+</span></div>
        <div class="accordion_body" style="display: none">
            @if(!empty($data))
            @foreach($data as $dp)
            <h6 class="text-left pt-2 py-3 text-uppercase">
                {{$dp->relation_type}} : {{$dp->name}}
            </h6>
            <div class="table-responsive">
            <table id="relativea{{$dp->relation_type_code}}" class="table table-striped table-bordered table-hover purpleTable">
                <thead>
                   <tr>
                      <th>{{Lang::get('affidavit.location')}}</th>
                      <th>{{Lang::get('affidavit.survey_no')}}</th>
                      <th>{{Lang::get('affidavit.area')}}</th>
                      <th>{{Lang::get('affidavit.property_type')}}</th>
                      <th>{{Lang::get('affidavit.whether_inherited_property')}}</th>
                      <th>{{Lang::get('affidavit.date_of_purchase_in_case_of_self_acquired_property')}}</th>
                      <th>Cost of Land(In case of purchase)(In Rs.) at the time of purchase</th>
                      <th>{{Lang::get('affidavit.any_investment_on_the_land_by_way_of_development')}}</th>
                      <th>{{Lang::get('affidavit.approximate_current_market_value')}}</th>
                      <th>{{Lang::get('affidavit.action')}}</th>
                   </tr>
                </thead>
                <tbody>
               @if(!empty($agricultural_land))
               @foreach($agricultural_land as $row)
               @if($row->relation_type_code==$dp->relation_type_code)
               <tr id="tr{{$row->id}}">
                  <td>{{$row->location}}</td>
                  <td>{{$row->survey_number}}</td>
                  <td>{{$row->area}}</td>
                  <td>{{$row->property_type}}
                     @if($row->property_type_id=="2")
                     {{$row->property_joint_with_name}}
                     @endif
                  </td>
                  <td nowrap="nowrap">{{$row->inherited_property}}</td>
                  <td>@if(@$row->date_of_purchase != '0000-00-00 00:00:00'){{\Carbon\Carbon::parse($row->date_of_purchase)->format('d/m/Y')}} @endif</td>
                  <td>{{$row->cost_at_purchase_time}}</td>
                  <td>{{$row->investment_on_land}}</td>
                  <td>{{$row->approx_current_market_value}}</td>
                  <td nowrap="nowrap">
                     <a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get('affidavit.edit')}}" onclick="javascript:open_modal({{$row->id}},{{$data}})"
                        data-location="{{$row->location}}"
                        data-survey_number="{{$row->survey_number}}"
                        data-area="{{$row->area}}"
                        data-property_type_id="{{$row->property_type_id}}"
                        data-property_joint_with="{{$row->property_joint_with}}"
                        data-joint_other_name="{{$row->joint_other_name}}"
                        data-inherited_property="{{$row->inherited_property}}"
                        data-date_of_purchase="@if(@$row->date_of_purchase != '0000-00-00 00:00:00'){{\Carbon\Carbon::parse($row->date_of_purchase)->format('d/m/Y')}} @endif"
                        data-cost_at_purchase_time="{{$row->cost_at_purchase_time}}"
                        data-investment_on_land="{{$row->investment_on_land}}"
                        data-approx_current_market_value="{{$row->approx_current_market_value}}"                        
                        data-relation_type_id="{{$row->relation_type_code}}"
                        data-candidate_id="{{$row->candidate_id}}"
                        id="edit_agricultural_land{{$row->id}}">
                    <i class="fa fa-edit"></i> {{Lang::get('affidavit.edit')}}
                     </a>
					 
					 @if(Auth::user()->role_id != '19')
                     <a href="javascript:void(0)" class="btn btn-info btn-danger btn-sm" title="{{Lang::get('affidavit.delete')}}" onclick="javascript:delete_agricultural_land({{$row->id}})">
                     <i class="fa fa-times"></i> {{Lang::get('affidavit.delete')}}</a>
					 @endif
					 
                  </td>
               </tr>
               @endif
               @endforeach                                                        
                   @endif
				   
				   @if(Auth::user()->role_id != '19')
                   <form id="form{{$dp->relation_type_code}}">
                      <tr id="agricultural{{$dp->relation_type_code}}">
                         <td width="120">
							<textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="location{{$dp->relation_type_code}}" id="location{{$dp->relation_type_code}}" required="required"></textarea>
                         </td>
                         <td width="120">
							<textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="survey_number{{$dp->relation_type_code}}" id="survey_number{{$dp->relation_type_code}}" required="required"></textarea>
                         </td>
                         <td>
                            <input type="text" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="area{{$dp->relation_type_code}}" id="area{{$dp->relation_type_code}}" style="width: 100px;" onkeydown="return NumbersOnly(event,this)" required="required" maxlength ="12">
                         </td>
                         <td>
                            <select class="form-control"  style="width: 110px;" name="property_type_id{{$dp->relation_type_code}}" id="property_type_id{{$dp->relation_type_code}}" onchange="javascript:get_relatives({{$dp->relation_type_code}});" required="required">
                               <option value="">{{Lang::get('affidavit.select')}}</option>
                               <option value="1">{{Lang::get('affidavit.individual')}}</option>
                               <option value="2">{{Lang::get('affidavit.joint')}}</option>
                            </select>
                            <br>
                            <div id="joint_div{{$dp->relation_type_code}}" style="display: none;">
                               <select class="form-control" name="property_joint_with{{$dp->relation_type_code}}[]" id="property_joint_with{{$dp->relation_type_code}}" multiple>
                                  @if($data)
                                  @foreach($data as $rel)
                                  @if($dp->relation_type_code!=$rel->relation_type_code)
                                  <option value="{{$rel->relation_type_code}}-{{$rel->name}}">{{$rel->name}}</option>
                                  @endif
                                  @endforeach
                                  @endif
                               </select>
                               <br>
                               <small>{{Lang::get('affidavit.property_joint_with')}}</small>
                               <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control"  name="joint_other_name{{$dp->relation_type_code}}" id="joint_other_name{{$dp->relation_type_code}}">
                                 </textarea>
                            </div>
                         </td>
                         <td nowrap="nowrap">
                            <select class="form-control" name="inherited_property{{$dp->relation_type_code}}" id="inherited_property{{$dp->relation_type_code}}" required="required" onchange="javascript:change_required({{$dp->relation_type_code}})">
                               <option value="Yes" selected>{{Lang::get('affidavit.yes')}}</option>
                               <option value="No">{{Lang::get('affidavit.no')}}</option>
                            </select>
                         </td>
                         <td width="160">
                          <div class="input-group">
                            <input type="text" class="form-control datepicker" name="date_of_purchase{{$dp->relation_type_code}}" id="date_of_purchase{{$dp->relation_type_code}}"  readonly placeholder="YYYY-MM-DD"  />
                            <i class="fa fa-calendar input-group-text fa-lg"></i> 
                          <div>  
                         </td>
                         <td>
                            <input type="text" class="form-control" name="cost_at_purchase_time{{$dp->relation_type_code}}" id="cost_at_purchase_time{{$dp->relation_type_code}}" onkeydown="return NumbersOnly(event,this)" maxlength="12"  />
                         </td>
                         <td>
                            <input type="text" class="form-control" name="investment_on_land{{$dp->relation_type_code}}" id="investment_on_land{{$dp->relation_type_code}}"  onkeydown="return NumbersOnly(event,this)" maxlength="12" >
                         </td>
                         <td> 
                            <input type="text" class="form-control" name="approx_current_market_value{{$dp->relation_type_code}}" id="approx_current_market_value{{$dp->relation_type_code}}"  onkeydown="return NumbersOnly(event,this)" maxlength="12" style="width: 100px;" required="required">
                         </td>
                         <td nowrap="nowrap"> 
                            <a href="javascript:void(0)" class="btn btn-success btn-sm" title="{{Lang::get('affidavit.save')}}" id="save{{$dp->id}}" onclick="javascript:save_agricultural_land({{$dp->candidate_id}}, {{$dp->relation_type_code}} )" >
                            <i class="fa fa-check"></i> {{Lang::get('affidavit.save')}}
                            </a>
                         </td>
                      </tr>
                   </form>
				   @endif
				   
                </tbody>
             </table>
         </div>
             @endforeach
             @endif
    </div>
    <!--  Non Agricultural Land --->
    <div class="accordion_head">{{Lang::get('affidavit.non_agricultural_land')}}<span class="plusminus">+</span></div>
        <div class="accordion_body" style="display: none">
            @if(!empty($data))
            @foreach($data as $dp)
            <h6 class="text-left pt-2 py-3 text-uppercase">
                {{$dp->relation_type}} : {{$dp->name}}
            </h6>
            <div class="table-responsive">
            <table id="relativeb{{$dp->relation_type_code}}" class="table table-striped table-bordered table-hover purpleTable">
                <thead>
                   <tr>
                      <th>{{Lang::get('affidavit.location')}}</th>
                      <th>{{Lang::get('affidavit.survey_no')}}</th>
                      <th>{{Lang::get('affidavit.area')}}</th>
                      <th>{{Lang::get('affidavit.property_type')}}</th>
                      <th>{{Lang::get('affidavit.whether_inherited_property')}}</th>
                      <th>{{Lang::get('affidavit.date_of_purchase_in_case_of_self_acquired_property')}}</th>
                      <th>{{Lang::get('affidavit.cost_of_land_at_the_time_of_purchase')}}</th>
                      <th>{{Lang::get('affidavit.any_investment_on_the_land_by_way_of_development')}}</th>
                      <th>{{Lang::get('affidavit.approximate_current_market_value')}}</th>
                      <th>{{Lang::get('affidavit.action')}}</th>
                   </tr>
                </thead>
                <tbody>
                   @if(!empty($non_agricultural_land))
                   @foreach($non_agricultural_land as $row)
                   @if($row->relation_type_code==$dp->relation_type_code)
                   <tr id="tr{{$row->id}}">
                      <td>{{$row->location}}</td>
                      <td>{{$row->survey_number}}</td>
                      <td>{{$row->area}}</td>
                      <td>{{$row->property_type}}
                         @if($row->property_type_id=="2")
                         {{$row->property_joint_with_name}}
                         @endif
                      </td>
                      <td nowrap="nowrap">{{$row->inherited_property}}</td>
                      <td>@if(@$row->date_of_purchase != '0000-00-00 00:00:00'){{\Carbon\Carbon::parse($row->date_of_purchase)->format('d/m/Y')}} @endif</td>
                      <td>{{$row->cost_at_purchase_time}}</td>
                      <td>{{$row->investment_on_land}}</td>
                      <td>{{$row->approx_current_market_value}}</td>
                      <td nowrap="nowrap">
                         <a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get('affidavit.edit')}}" onclick="javascript:open_modal2({{$row->id}},{{$data}})"
                            data-location="{{$row->location}}"
                            data-survey_number="{{$row->survey_number}}"
                            data-area="{{$row->area}}"
                            data-property_type_id="{{$row->property_type_id}}"
							data-property_joint_with="{{$row->property_joint_with}}"
							data-joint_other_name="{{$row->joint_other_name}}"
                            data-inherited_property="{{$row->inherited_property}}"
                            data-date_of_purchase="@if(@$row->date_of_purchase != '0000-00-00 00:00:00'){{\Carbon\Carbon::parse($row->date_of_purchase)->format('d/m/Y')}} @endif"
                            data-cost_at_purchase_time="{{$row->cost_at_purchase_time}}"
                            data-investment_on_land="{{$row->investment_on_land}}"
                            data-approx_current_market_value="{{$row->approx_current_market_value}}"
                            data-relation_type_id="{{$row->relation_type_code}}"
                            data-candidate_id="{{$row->candidate_id}}"
                            id="edit_non_agricultural_land{{$row->id}}">
                         <i class="fa fa-edit"></i> {{Lang::get('affidavit.edit')}}</a>
						 
						 @if(Auth::user()->role_id != '19')
                         <a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get('affidavit.delete')}}" onclick="javascript:delete_non_agricultural_land({{$row->id}})"><i class="fa fa-times"></i> {{Lang::get('affidavit.delete')}}</a>
						@endif
						
                      </td>
                   </tr>
                   @endif
                   @endforeach                                                        
                   @endif
				   
				   
				   @if(Auth::user()->role_id != '19')
				   
                   <form id="form{{$dp->relation_type_code}}">
                      <tr id="nonagricultural{{$dp->relation_type_code}}">
                         <td width="120">
							<textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="location{{$dp->relation_type_code}}" id="location2{{$dp->relation_type_code}}" required="required"></textarea>
                         </td>
                         <td width="120">
							<textarea col="10" row="5"onkeypress="return blockSpecialChar_address(event)" class="form-control" name="survey_number{{$dp->relation_type_code}}" id="survey_number2{{$dp->relation_type_code}}" required="required"></textarea>
                         </td>
                         <td>
                            <input type="text" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="area{{$dp->relation_type_code}}" id="area2{{$dp->relation_type_code}}" onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
                         </td>
                         <td width="135">
                            <select class="form-control" name="property_type_id{{$dp->relation_type_code}}" id="property_type_id2{{$dp->relation_type_code}}" onchange="javascript:get_relatives2({{$dp->relation_type_code}});" required="required" >
                               <option value="">{{Lang::get('affidavit.select')}}</option>
                               <option value="1">{{Lang::get('affidavit.individual')}}</option>
                               <option value="2">{{Lang::get('affidavit.joint')}}</option>
                            </select>
                            <br>
                            <div id="joint_div2{{$dp->relation_type_code}}" style="display: none;">
                               <select class="form-control" name="property_joint_with{{$dp->relation_type_code}}[]" id="property_joint_with2{{$dp->relation_type_code}}" multiple>
                                  @if($data)
                                  @foreach($data as $rel)
                                  @if($dp->relation_type_code!=$rel->relation_type_code)
                                  <option value="{{$rel->relation_type_code}}-{{$rel->name}}">{{$rel->name}}</option>
                                  @endif
                                  @endforeach
                                  @endif
                               </select>
                               <br>
                               <small>{{Lang::get('affidavit.property_joint_with')}}</small>
                               <textarea col="10" onkeypress="return blockSpecialChar_address(event)" row="5" class="form-control" name="joint_other_name{{$dp->relation_type_code}}" id="joint_other_name2{{$dp->relation_type_code}}">
                               </textarea>
                            </div>
                         </td>
                         <td>
                            <select class="form-control" name="inherited_property{{$dp->relation_type_code}}" id="inherited_property2{{$dp->relation_type_code}}" onchange="javascript:change_required2({{$dp->relation_type_code}})" required="required">
                               <option value="Yes">{{Lang::get('affidavit.yes')}}</option>
                               <option value="No">{{Lang::get('affidavit.no')}}</option>
                            </select>
                         </td>
                         <td width="160">
                          <div class="input-group">
                            <input type="text" class="form-control datepicker" name="date_of_purchase{{$dp->relation_type_code}}" id="date_of_purchase2{{$dp->relation_type_code}}"  readonly placeholder="YYYY-MM-DD">
                             <i class="fa fa-calendar input-group-text fa-lg"></i>
                            </div>              
                         </td>
                         <td>
                            <input type="text" class="form-control" name="cost_at_purchase_time{{$dp->relation_type_code}}" id="cost_at_purchase_time2{{$dp->relation_type_code}}" onkeydown="return NumbersOnly(event,this)" maxlength="12">
                         </td>
                         <td>
                            <input type="text" class="form-control" name="investment_on_land{{$dp->relation_type_code}}" id="investment_on_land2{{$dp->relation_type_code}}"  onkeydown="return NumbersOnly(event,this)" maxlength="12" >
                         </td>
                         <td> 
                            <input type="text" class="form-control" name="approx_current_market_value{{$dp->relation_type_code}}" id="approx_current_market_value2{{$dp->relation_type_code}}"  onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
                         </td>
                         <td nowrap="nowrap">
                            <a href="javascript:void(0)" class="btn btn-success btn-sm" title="{{Lang::get('affidavit.save')}}" id="save{{$dp->id}}" onclick="javascript:save_non_agricultural_land({{$dp->candidate_id}}, {{$dp->relation_type_code}} )"><i class="fa fa-check"></i>{{Lang::get('affidavit.save')}}
                            </a>
                         </td>
                      </tr>
                   </form>
				   
				   @endif
				   
                </tbody>
             </table>
         </div>
             @endforeach
             @endif    
        </div><!-- accordian body --> 
        <!--  Non Agricultural Land End--->
        <!--  Commercial Buildings(including apartments) -->
        <div class="accordion_head">{{Lang::get('affidavit.commercial_buildings')}}<span class="plusminus">+</span></div>
        <div class="accordion_body" style="display:none">
            @if(!empty($data))
             @foreach($data as $dp)
             <h6 class="text-left pt-2 py-3 text-uppercase">
                {{$dp->relation_type}} : {{$dp->name}}
             </h6>
             <div class="table-responsive">
             <table id="relativec{{$dp->relation_type_code}}" class="table table-striped table-bordered table-hover purpleTable">
                <thead>
                   <tr>
                      <th>{{Lang::get('affidavit.location')}}</th>
                      <th>{{Lang::get('affidavit.survey_no')}}</th>
                      <th>{{Lang::get('affidavit.area')}}</th>
                      <th>{{Lang::get('affidavit.built_up_area')}}</th>
                      <th>{{Lang::get('affidavit.property_type')}}</th>
                      <th>{{Lang::get('affidavit.whether_inherited_property')}}</th>
                      <th>{{Lang::get('affidavit.date_of_purchase_in_case_of_self_acquired_property')}}</th>
                      <th>{{Lang::get('affidavit.cost_of_land_at_the_time_of_purchase')}}</th>
                      <th>{{Lang::get('affidavit.any_investment_on_the_land_by_way_of_development')}}</th>
                      <th>{{Lang::get('affidavit.approximate_current_market_value')}}</th>
                      <th>{{Lang::get('affidavit.action')}}</th>
                   </tr>
                </thead>
                <tbody>
                   @if(!empty($commercial_buildings))
                   @foreach($commercial_buildings as $row)
                   @if($row->relation_type_code==$dp->relation_type_code)
                   <tr id="tr{{$row->id}}">
                      <td>{{$row->location}}</td>
                      <td>{{$row->survey_number}}</td>
                      <td>{{$row->area}}</td>
                      <td>{{$row->built_up_area}}</td>
                      <td nowrap="nowrap">{{$row->property_type}}
                         @if($row->property_type_id=="2")
                         {{$row->property_joint_with_name}}
                         @endif
                      </td>
                      <td>{{$row->inherited_property}}</td>
                      <td>@if(@$row->date_of_purchase != '0000-00-00 00:00:00'){{\Carbon\Carbon::parse($row->date_of_purchase)->format('d/m/Y')}} @endif</td>
                      <td>{{$row->cost_at_purchase_time}}</td>
                      <td>{{$row->investment_on_buildings}}</td>
                      <td>{{$row->approx_current_market_value}}</td>
                      <td nowrap="nowrap">
                         <a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get('affidavit.edit')}}" onclick="javascript:open_modal3({{$row->id}},{{$data}})"
                            data-location="{{$row->location}}"
                            data-survey_number="{{$row->survey_number}}"
                            data-area="{{$row->area}}"
                            data-built_up_area="{{$row->built_up_area}}"
                            data-property_type_id="{{$row->property_type_id}}"
							data-property_joint_with="{{$row->property_joint_with}}"
							data-joint_other_name="{{$row->joint_other_name}}"
                            data-inherited_property="{{$row->inherited_property}}"
                            data-date_of_purchase="@if(@$row->date_of_purchase != '0000-00-00 00:00:00'){{\Carbon\Carbon::parse($row->date_of_purchase)->format('d/m/Y')}} @endif"
                            data-cost_at_purchase_time="{{$row->cost_at_purchase_time}}"
                            data-investment_on_buildings="{{$row->investment_on_buildings}}"
                            data-approx_current_market_value="{{$row->approx_current_market_value}}"
                            data-relation_type_id="{{$row->relation_type_code}}"
                            data-candidate_id="{{$row->candidate_id}}"
                            id="edit_commercial{{$row->id}}">
                         <i class="fa fa-edit"></i> {{Lang::get('affidavit.edit')}}</a>
						 @if(Auth::user()->role_id != '19')
                         <a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get('affidavit.delete')}}" onclick="javascript:delete_commercial({{$row->id}})">
                         <i class="fa fa-times"></i> {{Lang::get('affidavit.delete')}}
                         </a>
						 @endif
                      </td>
                   </tr>
                   @endif
                   @endforeach                                                        
                   @endif
				   
				   @if(Auth::user()->role_id != '19')
                   <form id="form{{$dp->relation_type_code}}">
                      <tr id="commercial{{$dp->relation_type_code}}">
                         <td width="120">
							<textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="location{{$dp->relation_type_code}}" id="location3{{$dp->relation_type_code}}" required="required"></textarea>
                         </td>
                         <td width="120">
							<textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="survey_number{{$dp->relation_type_code}}" id="survey_number3{{$dp->relation_type_code}}" required="required"></textarea>
                         </td>
                         <td>
                            <input type="text" class="form-control" name="area{{$dp->relation_type_code}}" id="area3{{$dp->relation_type_code}}" onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
                         </td>
                         <td>
                            <input type="text" class="form-control" name="built_up_area{{$dp->relation_type_code}}" id="built_up_area3{{$dp->relation_type_code}}" onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
                         </td>
                          <td width="135">
                            <select class="form-control" name="property_type_id{{$dp->relation_type_code}}" id="property_type_id3{{$dp->relation_type_code}}" onchange="javascript:get_relatives3({{$dp->relation_type_code}});" required="required">
                               <option value="">{{Lang::get('affidavit.select')}}</option>
                               <option value="1">{{Lang::get('affidavit.individual')}}</option>
                               <option value="2">{{Lang::get('affidavit.joint')}}</option>
                            </select>
                            <br>
                            <div id="joint_div3{{$dp->relation_type_code}}" style="display: none;">
                               <select class="form-control" name="property_joint_with{{$dp->relation_type_code}}[]" id="property_joint_with3{{$dp->relation_type_code}}" multiple>
                                  @if($data)
                                  @foreach($data as $rel)
                                  @if($dp->relation_type_code!=$rel->relation_type_code)
                                  <option value="{{$rel->relation_type_code}}-{{$rel->name}}">{{$rel->name}}</option>
                                  @endif
                                  @endforeach
                                  @endif
                               </select>
                               <br>
                               <small>{{Lang::get('affidavit.property_joint_with')}}</small>
                               <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="joint_other_name{{$dp->relation_type_code}}" id="joint_other_name3{{$dp->relation_type_code}}">
                                                </textarea>
                            </div>
                         </td>
                         <td>
                            <select class="form-control" name="inherited_property{{$dp->relation_type_code}}" id="inherited_property3{{$dp->relation_type_code}}"  onchange="javascript:change_required3({{$dp->relation_type_code}})" required="required">
                               <option value="Yes">{{Lang::get('affidavit.yes')}}</option>
                               <option value="No">{{Lang::get('affidavit.no')}}</option>
                            </select>
                         </td>
                         <td width="160">
                          <div class="input-group">
                            <input type="text" class="form-control datepicker" name="date_of_purchase{{$dp->relation_type_code}}" id="date_of_purchase3{{$dp->relation_type_code}}"  readonly placeholder="YYYY-MM-DD">
                             <i class="fa fa-calendar input-group-text fa-lg"></i>
                           </div>   
                         </td>
                         <td>
                            <input type="text" class="form-control" name="cost_at_purchase_time{{$dp->relation_type_code}}" id="cost_at_purchase_time3{{$dp->relation_type_code}}" onkeydown="return NumbersOnly(event,this)" maxlength="12" />
                         </td>
                         <td>
                            <input type="text" class="form-control" name="investment_on_buildings{{$dp->relation_type_code}}" id="investment_on_buildings3{{$dp->relation_type_code}}"  onkeydown="return NumbersOnly(event,this)" maxlength="12" >
                         </td>
                         <td> 
                            <input type="text" class="form-control" name="approx_current_market_value{{$dp->relation_type_code}}" id="approx_current_market_value3{{$dp->relation_type_code}}"  onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
                         </td>
                         <td nowrap="nowrap"> 
                            <a href="javascript:void(0)" class="btn btn-sm btn-success" title="{{Lang::get('affidavit.save')}}" id="save{{$dp->id}}" onclick="javascript:save_commercial({{$dp->candidate_id}}, {{$dp->relation_type_code}} )"><i class="fa fa-check"></i> {{Lang::get('affidavit.save')}}
                            </a>
                         </td>
                      </tr>
                   </form>
				   
				   @endif
				   
                </tbody>
             </table>
         </div>
             @endforeach
             @endif    
        </div>
        <!--  Commercial Buildings(including apartments) End -->

        <!-- Residential Buildings(including apartments) -->
        <div class="accordion_head">{{Lang::get('affidavit.residential_buildings')}} <span class="plusminus">+</span></div>
        <div class="accordion_body" style="display: none"> 
            @if(!empty($data))
             @foreach($data as $dp)
             <h6 class="text-left pt-2 py-3 text-uppercase">
                {{$dp->relation_type}} : {{$dp->name}}
             </h6>
             <div class="table-responsive">
             <table id="relatived{{$dp->relation_type_code}}" class="table table-striped table-bordered table-hover purpleTable">
                <thead>
                   <tr>
                      <th>{{Lang::get('affidavit.location')}}</th>
                      <th>{{Lang::get('affidavit.survey_no')}}</th>
                      <th>{{Lang::get('affidavit.area')}}</th>
                      <th>{{Lang::get('affidavit.built_up_area')}}</th>
                      <th>{{Lang::get('affidavit.property_type')}}</th>
                      <th>{{Lang::get('affidavit.whether_inherited_property')}}</th>
                      <th>{{Lang::get('affidavit.date_of_purchase_in_case_of_self_acquired_property')}}</th>
                      <th>{{Lang::get('affidavit.cost_of_land_at_the_time_of_purchase')}}</th>
                      <th>{{Lang::get('affidavit.any_investment_on_the_land_by_way_of_development')}}</th>
                      <th>{{Lang::get('affidavit.approximate_current_market_value')}}</th>
                      <th>{{Lang::get('affidavit.action')}}</th>
                   </tr>
                </thead>
                <tbody>
                   @if(!empty($residential_buildings))
                   @foreach($residential_buildings as $row)
                   @if($row->relation_type_code==$dp->relation_type_code)
                   <tr id="tr{{$row->id}}">
                      <td>{{$row->location}}</td>
                      <td>{{$row->survey_number}}</td>
                      <td>{{$row->area}}</td>
                      <td>{{$row->built_up_area}}</td>
                      <td nowrap="nowrap">{{$row->property_type}}
                         @if($row->property_type_id=="2")
                         {{$row->property_joint_with_name}}
                         @endif
                      </td>
                      <td>{{$row->inherited_property}}</td>
                      <td>@if(@$row->date_of_purchase != '0000-00-00 00:00:00'){{\Carbon\Carbon::parse($row->date_of_purchase)->format('d/m/Y')}} @endif</td>
                      <td>{{$row->cost_at_purchase_time}}</td>
                      <td>{{$row->investment_on_buildings}}</td>
                      <td>{{$row->approx_current_market_value}}</td>
                      <td nowrap="nowrap">
                         <a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get('affidavit.edit')}}" onclick="javascript:open_modal4({{$row->id}},{{$data}})"
                            data-location="{{$row->location}}"
                            data-survey_number="{{$row->survey_number}}"
                            data-area="{{$row->area}}"
                            data-built_up_area="{{$row->built_up_area}}"
                            data-property_type_id="{{$row->property_type_id}}"
							data-property_joint_with="{{$row->property_joint_with}}"
							data-joint_other_name="{{$row->joint_other_name}}"
                            data-inherited_property="{{$row->inherited_property}}"
                            data-date_of_purchase="@if(@$row->date_of_purchase != '0000-00-00 00:00:00'){{\Carbon\Carbon::parse($row->date_of_purchase)->format('d/m/Y')}} @endif"
                            data-cost_at_purchase_time="{{$row->cost_at_purchase_time}}"
                            data-investment_on_buildings="{{$row->investment_on_buildings}}"
                            data-approx_current_market_value="{{$row->approx_current_market_value}}"
                            data-relation_type_id="{{$row->relation_type_code}}"
                            data-candidate_id="{{$row->candidate_id}}"
                            id="edit_residential{{$row->id}}">
                         <i class="fa fa-edit"></i> {{Lang::get('affidavit.edit')}}</a>
						 
						 @if(Auth::user()->role_id != '19')
							<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get('affidavit.delete')}}" onclick="javascript:delete_residential({{$row->id}})">
							<i class="fa fa-times"></i> {{Lang::get('affidavit.delete')}}</a>
						 @endif
						 
                      </td>
                   </tr>
                   @endif
                   @endforeach                                                        
                   @endif
				   
				   @if(Auth::user()->role_id != '19')
                   <form id="form{{$dp->relation_type_code}}">
                      <tr id="residential{{$dp->relation_type_code}}">
                         <td width="120">
							<textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="location{{$dp->relation_type_code}}" id="location4{{$dp->relation_type_code}}" required="required"></textarea>
                         </td>
                         <td width="120">
							<textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="survey_number{{$dp->relation_type_code}}" id="survey_number4{{$dp->relation_type_code}}" required="required"></textarea>
                         </td>
                         <td>
                            <input type="text" class="form-control" name="area{{$dp->relation_type_code}}" id="area4{{$dp->relation_type_code}}" onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
                         </td>
                         <td>
                            <input type="text" class="form-control" name="built_up_area{{$dp->relation_type_code}}" id="built_up_area4{{$dp->relation_type_code}}" onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
                         </td>
                          <td width="135">
                            <select class="form-control" name="property_type_id{{$dp->relation_type_code}}" id="property_type_id4{{$dp->relation_type_code}}" onchange="javascript:get_relatives4({{$dp->relation_type_code}});" required="required">
                               <option value="">{{Lang::get('affidavit.select')}}</option>
                               <option value="1">{{Lang::get('affidavit.individual')}}</option>
                               <option value="2">{{Lang::get('affidavit.joint')}}</option>
                            </select>
                            <br>
                            <div id="joint_div4{{$dp->relation_type_code}}" style="display: none;">
                               <select class="form-control" name="property_joint_with{{$dp->relation_type_code}}[]" id="property_joint_with4{{$dp->relation_type_code}}" multiple>
                                  @if($data)
                                  @foreach($data as $rel)
                                  @if($dp->relation_type_code!=$rel->relation_type_code)
                                  <option value="{{$rel->relation_type_code}}-{{$rel->name}}">{{$rel->name}}</option>
                                  @endif
                                  @endforeach
                                  @endif
                               </select>
                               <br>
                               <small>{{Lang::get('affidavit.property_joint_with')}}</small>
                               <textarea col="10" onkeypress="return blockSpecialChar_address(event)" class="form-control" row="5" name="joint_other_name{{$dp->relation_type_code}}" id="joint_other_name4{{$dp->relation_type_code}}">
                               </textarea>
                            </div>
                         </td>
                         <td>
                            <select class="form-control" name="inherited_property{{$dp->relation_type_code}}" id="inherited_property4{{$dp->relation_type_code}}" onchange="javascript:change_required4({{$dp->relation_type_code}})" required="required">
                               <option value="Yes">{{Lang::get('affidavit.yes')}}</option>
                               <option value="No">{{Lang::get('affidavit.no')}}</option>
                            </select>
                         </td>
                         <td width="160">
                          <div class="input-group">
                            <input type="text" class="form-control datepicker" name="date_of_purchase{{$dp->relation_type_code}}" id="date_of_purchase4{{$dp->relation_type_code}}"  readonly placeholder="YYYY-MM-DD">
                            <i class="fa fa-calendar input-group-text fa-lg"></i>
                           </div> 
                         </td>
                         <td>
                            <input type="text" class="form-control" name="cost_at_purchase_time{{$dp->relation_type_code}}" id="cost_at_purchase_time4{{$dp->relation_type_code}}" onkeydown="return NumbersOnly(event,this)" maxlength="12" >
                         </td>
                         <td>
                            <input type="text" class="form-control" name="investment_on_buildings{{$dp->relation_type_code}}" id="investment_on_buildings4{{$dp->relation_type_code}}"  onkeydown="return NumbersOnly(event,this)" maxlength="12" />
                         </td>
                         <td> 
                            <input type="text" class="form-control" name="approx_current_market_value{{$dp->relation_type_code}}" id="approx_current_market_value4{{$dp->relation_type_code}}"  onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
                         </td>
                         <td nowrap="nowrap"> 
                            <a href="javascript:void(0)" class="btn btn-success btn-sm" title="{{Lang::get('affidavit.save')}}" id="save{{$dp->id}}" onclick="javascript:save_residential({{$dp->candidate_id}}, {{$dp->relation_type_code}} )"><i class="fa fa-check"></i> {{Lang::get('affidavit.save')}}
                            </a>
                         </td>
                      </tr>
                   </form>
				   @endif
				   
                </tbody>
             </table>
         </div>
             @endforeach
             @endif
        </div>    
        <!-- Residential Buildings(including apartments) End -->
        <!-- Others (such as interest in property) -->
        <div class="accordion_head">{{Lang::get('affidavit.other_assets')}}<span class="plusminus">+</span></div>
        <div class="accordion_body" style="display: none"> 
            @if(!empty($data))
             @foreach($data as $dp)
             <h6 class="text-left pt-2 py-3 text-uppercase">
                {{$dp->relation_type}} : {{$dp->name}}
             </h6>
             <div class="table-responsive">
             <table id="relativee{{$dp->relation_type_code}}" class="table table-striped table-bordered table-hover purpleTable" >
                <thead>
                   <tr>
                      <th>{{Lang::get('affidavit.other_assets')}}</th>
                      <th>{{Lang::get('affidavit.amount_in_rs')}}</th>
                      <th>{{Lang::get('affidavit.action')}}</th>
                   </tr>
                </thead>
                <tbody>
                   @if(!empty($other_immovable))
                   @foreach($other_immovable as $row)
                   @if($row->relation_type_code==$dp->relation_type_code)
                   <tr id="tr{{$row->id}}">
                      <td>{{$row->brief_details}}</td>
                      <td>{{$row->amount}}</td>
                      <td>
                         <a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get('affidavit.edit')}}" onclick="javascript:open_modal5({{$row->id}},{{$data}})"
                            data-brief_details="{{$row->brief_details}}"
                            data-amount="{{$row->amount}}"
                            data-relation_type_id="{{$row->relation_type_code}}"
                            data-candidate_id="{{$row->candidate_id}}"
                            id="edit_other_immovable{{$row->id}}">
                         <i class="fa fa-edit"></i> {{Lang::get('affidavit.edit')}}
                         </a>
						 
						 @if(Auth::user()->role_id != '19')
							<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get('affidavit.delete')}}" onclick="javascript:delete_other_immovable({{$row->id}})">
							<i class="fa fa-times"></i> {{Lang::get('affidavit.delete')}}
							</a>
						 @endif
						 
                      </td>
                   </tr>
                   @endif
                   @endforeach                                                        
                   @endif
				   
				   @if(Auth::user()->role_id != '19')
                   <form id="form{{$dp->relation_type_code}}">
                      <tr id="other{{$dp->relation_type_code}}">
                         <td>
                            <input type="text" class="form-control" name="brief_details{{$dp->relation_type_code}}" id="brief_details{{$dp->relation_type_code}}" onkeypress="return blockSpecialChar_address(event)" required="required">
                         </td>
                         <td> 
                            <input type="text"  class="form-control" name="amount{{$dp->relation_type_code}}" id="amount{{$dp->relation_type_code}}"  onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
                         </td>
                         <td nowrap="nowrap"> 
                            <a href="javascript:void(0)" class="btn-sm btn btn-success" title="{{Lang::get('affidavit.save')}}" id="save{{$dp->id}}" onclick="javascript:save_other_immovable({{$dp->candidate_id}}, {{$dp->relation_type_code}} )" >
                            <i class="fa fa-check"></i> {{Lang::get('affidavit.save')}}
                            </a>
                         </td>
                      </tr>
                   </form>
				   @endif
				   
                </tbody>
             </table>
         </div>
             @endforeach
             @endif            
        </div>    
        <!-- Others (such as interest in property) End -->
                    
                  </div>
                  <div class="card-footer footerSection">
                     <div class="row">
                        <div class="col-12">
                            <a href="{{url($menu_action.'Affidavit/MovableAssets') }}" class="backBtn float-left">{{Lang::get('affidavit.back')}}</a>
                            <a href="{{url($menu_action.'liabilities')}}" type="submit" class="nextBtn float-right">{{Lang::get('affidavit.save')}} &amp; {{Lang::get('affidavit.next')}}</a>
                            <a href="{{url()->previous() }}" class="cencelBtn mr-2 float-right">{{Lang::get('affidavit.cancel')}}</a>
                        </div>                       
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>
</main>
<!-- Agricultural Land Edit Modal Start-->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.edit_agricultural_land_details')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <form id="model_agricultural">
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.location')}}:</label>
                        <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="modal_location" id="modal_location" required="required"></textarea>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.survey_no')}}:</label>
                        <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="modal_survey_number" id="modal_survey_number" required="required"></textarea>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.area')}}:</label>
                        <input type="text" class="form-control" name="modal_area" id="modal_area" onkeypress="return NumbersOnly(event,this)"  required="required">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.property_type')}}</label>
                        <select class="form-control" name="modal_property_type_id" id="modal_property_type_id" onchange="javascript:modal_get_relatives()" required="required">
                           <option value="">{{Lang::get('affidavit.select')}}</option>
                           <option value="1">{{Lang::get('affidavit.individual')}}</option>
                           <option value="2">{{Lang::get('affidavit.joint')}}</option>
                        </select>
                     </div>
                  </div>
               </div>
               <div id="modal_property_type_div" style="display: none;">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label>{{Lang::get('affidavit.property_joint_with')}}</label>
                           <select class="form-control" name="modal_property_joint_with[]" id="modal_property_joint_with" multiple>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label>{{Lang::get('affidavit.other')}}</label>
                           <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="modal_joint_other_name" id="modal_joint_other_name"></textarea>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.whether_inherited_property')}}</label>
                        <select class="form-control" name="modal_inherited_property" id="modal_inherited_property" required="required">
                           <option value="Yes">{{Lang::get('affidavit.yes')}}</option>
                           <option value="No">{{Lang::get('affidavit.no')}}</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.date_of_purchase_in_case_of_self_acquired_property')}}</label>
                        <input type="text" class="form-control datepicker" name="modal_date_of_purchase" id="modal_date_of_purchase" readonly>
						<i class="fa fa-calendar calender-model input-group-text fa-lg"></i> 
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.cost_of_land_at_the_time_of_purchase')}}</label>
                        <input type="text" class="form-control" name="modal_cost_at_purchase_time" id="modal_cost_at_purchase_time" onkeydown="return NumbersOnly(event,this)" maxlength="12" />
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.any_investment_on_the_land_by_way_of_development')}}</label>
                        <input type="text" class="form-control" name="modal_investment_on_land" id="modal_investment_on_land" onkeydown="return NumbersOnly(event,this)" maxlength="12" >
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.approximate_current_market_value')}}</label>
                        <input type="text" class="form-control" name="modal_approx_current_market_value" id="modal_approx_current_market_value" onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
                     </div>
                  </div>
               </div>
               <input type="hidden" name="modal_cand_id" id="modal_cand_id">
               <input type="hidden" name="modal_rel_id" id="modal_rel_id">
               <input type="hidden" name="modal_agricultural_id" id="modal_agricultural_id">
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.close')}}</button>
            <button type="button" class="btn btn-primary" onclick="javascript:update_agricultural_land()">{{Lang::get('affidavit.update')}}</button>
         </div>
      </div>
   </div>
</div>
<!-- Agricultural Land Edit Modal End-->
<!-- Agricultural Land Delete Modal Start-->
<div class="modal fade" id="deleteAgriculturalLandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.delete_agricultural_land_details')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <form>
               <h5>{{Lang::get('affidavit.are_you_sure_to_delete_this_entry')}}</h5>
               <input type="hidden" name="modal_delete_agricultural_id" id="modal_delete_agricultural_id">
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.no')}}</button>
            <button type="button" class="btn btn-primary" onclick="javascript:delete_agricultural_entry()">{{Lang::get('affidavit.yes')}}</button>
         </div>
      </div>
   </div>
</div>
<!-- Agricultural Land Delete Modal End-->
<!-- Non Agricultural Land Edit Modal Start-->
<div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.edit_non_agricultural_land_details')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <form id="model_nonagricultural">
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.location')}}:</label>
                        <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="modal2_location" id="modal2_location" required="required"></textarea>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.survey_no')}}:</label>
                        <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="modal2_survey_number" id="modal2_survey_number" required="required"></textarea>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.area')}}:</label>
                        <input type="text" class="form-control" name="modal2_area" id="modal2_area" onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.property_type')}}</label>
                        <select class="form-control" name="modal2_property_type_id" id="modal2_property_type_id" onchange="javascript:modal_get_relatives2()" required="required">
                           <option value="">{{Lang::get('affidavit.select')}}</option>
                           <option value="1">{{Lang::get('affidavit.individual')}}</option>
                           <option value="2">{{Lang::get('affidavit.joint')}}</option>
                        </select>
                     </div>
                  </div>
               </div>
               <div id="modal2_property_type_div" style="display: none;">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label>{{Lang::get('affidavit.property_joint_with')}}</label>
                           <select class="form-control" name="modal2_property_joint_with[]" id="modal2_property_joint_with" multiple>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label>{{Lang::get('affidavit.other')}}</label>
                           <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="modal2_joint_other_name" id="modal2_joint_other_name"></textarea>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.whether_inherited_property')}}</label>
                        <select class="form-control" name="modal2_inherited_property" id="modal2_inherited_property" required="required">
                           <option value="Yes">{{Lang::get('affidavit.yes')}}</option>
                           <option value="No">{{Lang::get('affidavit.no')}}</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-6">
				  		<label>{{Lang::get('affidavit.date_of_purchase_in_case_of_self_acquired_property')}}</label>	  
                     <div class="form-group">
                        
                        <input type="text" class="form-control datepicker" name="modal2_date_of_purchase" id="modal2_date_of_purchase" readonly>
                        <i class="fa fa-calendar calender-model input-group-text fa-lg"></i> 
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.cost_of_land_at_the_time_of_purchase')}}</label>
                        <input type="text" class="form-control" name="modal2_cost_at_purchase_time" id="modal2_cost_at_purchase_time" onkeydown="return NumbersOnly(event,this)" maxlength="12" >
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.any_investment_on_the_land_by_way_of_development')}}</label>
                        <input type="text" class="form-control" name="modal2_investment_on_land" id="modal2_investment_on_land" onkeydown="return NumbersOnly(event,this)" maxlength="12" />
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.approximate_current_market_value')}}</label>
                        <input type="text" class="form-control" name="modal2_approx_current_market_value" id="modal2_approx_current_market_value" onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
                     </div>
                  </div>
               </div>
               <input type="hidden" name="modal2_cand_id" id="modal2_cand_id">
               <input type="hidden" name="modal2_rel_id" id="modal2_rel_id">
               <input type="hidden" name="modal2_non_agricultural_id" id="modal2_non_agricultural_id">
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.close')}}</button>
            <button type="button" class="btn btn-primary" onclick="javascript:update_non_agricultural_land()">{{Lang::get('affidavit.update')}}</button>
         </div>
      </div>
   </div>
</div>
<!-- Non Agricultural Land Edit Modal End-->
<!-- Non Agricultural Land Delete Modal Start-->
<div class="modal fade" id="deleteNonAgriculturalLandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.delete_non_agricultural_land_details')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <form>
               <h5>{{Lang::get('affidavit.are_you_sure_to_delete_this_entry')}}</h5>
               <input type="hidden" name="modal_delete_non_agricultural_id" id="modal_delete_non_agricultural_id">
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.no')}}</button>
            <button type="button" class="btn btn-primary" onclick="javascript:delete_non_agricultural_entry()">{{Lang::get('affidavit.yes')}}</button>
         </div>
      </div>
   </div>
</div>
<!-- Non Agricultural Land Delete Modal End-->
<!-- Commercial Land Edit Modal Start-->
<div class="modal fade" id="exampleModal3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.edit_commercial_details')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <form id="model_commercial">
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.location')}}:</label>
                        <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="modal3_location" id="modal3_location" required="required"></textarea>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.survey_no')}}:</label>
                        <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="modal3_survey_number" id="modal3_survey_number" required="required"></textarea>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.area')}}:</label>
                        <input type="text" class="form-control" name="modal3_area" id="modal3_area" onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.built_up_area')}}:</label>
                        <input type="text" class="form-control" name="modal3_built_up_area" id="modal3_built_up_area" onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.property_type')}}</label>
                        <select class="form-control" name="modal3_property_type_id" id="modal3_property_type_id" onchange="javascript:modal_get_relatives3()" required="required">
                           <option value="">{{Lang::get('affidavit.select')}}</option>
                           <option value="1">{{Lang::get('affidavit.individual')}}</option>
                           <option value="2">{{Lang::get('affidavit.joint')}}</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.whether_inherited_property')}}</label>
                        <select class="form-control" name="modal3_inherited_property" id="modal3_inherited_property" required="required">
                           <option value="Yes">{{Lang::get('affidavit.yes')}}</option>
                           <option value="No">{{Lang::get('affidavit.no')}}</option>
                        </select>
                     </div>
                  </div>
               </div>
               <div id="modal3_property_type_div" style="display: none;">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label>{{Lang::get('affidavit.property_joint_with')}}</label>
                           <select class="form-control" name="modal3_property_joint_with[]" id="modal3_property_joint_with" multiple>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label>{{Lang::get('affidavit.other')}}</label>
                           <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="modal3_joint_other_name" id="modal3_joint_other_name"></textarea>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.date_of_purchase_in_case_of_self_acquired_property')}}</label>
                        <input type="text" class="datepicker form-control" name="modal3_date_of_purchase" id="modal3_date_of_purchase" readonly>
                        <i class="fa fa-calendar calender-model input-group-text fa-lg"></i> 
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.cost_of_land_at_the_time_of_purchase')}}</label>
                        <input type="text" class="form-control" name="modal3_cost_at_purchase_time" id="modal3_cost_at_purchase_time" onkeydown="return NumbersOnly(event,this)" maxlength="12" >
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.any_investment_on_the_land_by_way_of_development')}}</label>
                        <input type="text" class="form-control" name="modal3_investment_on_buildings" id="modal3_investment_on_buildings" onkeydown="return NumbersOnly(event,this)" maxlength="12" >
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.approximate_current_market_value')}}</label>
                        <input type="text" class="form-control" name="modal3_approx_current_market_value" id="modal3_approx_current_market_value" onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
                     </div>
                  </div>
               </div>
               <input type="hidden" name="modal3_cand_id" id="modal3_cand_id">
               <input type="hidden" name="modal3_rel_id" id="modal3_rel_id">
               <input type="hidden" name="modal3_commercial_id" id="modal3_commercial_id">
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.close')}}</button>
            <button type="button" class="btn btn-primary" onclick="javascript:update_commercial()">{{Lang::get('affidavit.update')}}</button>
         </div>
      </div>
   </div>
</div>
<!-- Commercial Land Edit Modal End-->
<!-- Commercial Delete Modal Start-->
<div class="modal fade" id="deleteCommercialModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.delete_commercial_details')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <form>
               <h5>{{Lang::get('affidavit.are_you_sure_to_delete_this_entry')}}</h5>
               <input type="hidden" name="modal_delete_commercial_id" id="modal_delete_commercial_id">
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.no')}}</button>
            <button type="button" class="btn btn-primary" onclick="javascript:delete_commercial_entry()">{{Lang::get('affidavit.yes')}}</button>
         </div>
      </div>
   </div>
</div>
<!-- Commercial Delete Modal End-->
<!-- Residential Land Edit Modal Start-->
<div class="modal fade" id="exampleModal4" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.edit_residential_details')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <form id="model_residential">
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.location')}}:</label>
                        <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="modal4_location" id="modal4_location" required="required"></textarea>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.survey_no')}}:</label>
                        <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="modal4_survey_number" id="modal4_survey_number" required="required"></textarea>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.area')}}:</label>
                        <input type="text" class="form-control" name="modal4_area" id="modal4_area" onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.built_up_area')}}:</label>
                        <input type="text" class="form-control" name="modal4_built_up_area" id="modal4_built_up_area" onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.property_type')}}</label>
                        <select class="form-control" name="modal4_property_type_id" id="modal4_property_type_id" onchange="javascript:modal_get_relatives4()" required="required">
                           <option value="">{{Lang::get('affidavit.select')}}</option>
                           <option value="1">{{Lang::get('affidavit.individual')}}</option>
                           <option value="2">{{Lang::get('affidavit.joint')}}</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.whether_inherited_property')}}</label>
                        <select class="form-control" name="modal4_inherited_property" id="modal4_inherited_property" required="required">
                           <option value="Yes">{{Lang::get('affidavit.yes')}}</option>
                           <option value="No">{{Lang::get('affidavit.no')}}</option>
                        </select>
                     </div>
                  </div>
               </div>
               <div id="modal4_property_type_div" style="display: none;">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label>{{Lang::get('affidavit.property_joint_with')}}</label>
                           <select class="form-control" name="modal4_property_joint_with[]" id="modal4_property_joint_with" multiple>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label>{{Lang::get('affidavit.other')}}</label>
                           <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="modal4_joint_other_name" id="modal4_joint_other_name"></textarea>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.date_of_purchase_in_case_of_self_acquired_property')}}</label>
                        <input type="text" class="form-control datepicker" name="modal4_date_of_purchase" id="modal4_date_of_purchase" readonly>
                        <i class="fa fa-calendar calender-model input-group-text fa-lg"></i> 
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.cost_of_land_at_the_time_of_purchase')}}</label>
                        <input type="text"  class="form-control" name="modal4_cost_at_purchase_time" id="modal4_cost_at_purchase_time" onkeydown="return NumbersOnly(event,this)" maxlength="12" >
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.any_investment_on_the_land_by_way_of_development')}}</label>
                        <input type="text"  class="form-control" name="modal4_investment_on_buildings" id="modal4_investment_on_buildings" onkeydown="return NumbersOnly(event,this)" maxlength="12" >
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.approximate_current_market_value')}}</label>
                        <input type="text"  class="form-control" name="modal4_approx_current_market_value" id="modal4_approx_current_market_value" onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
                     </div>
                  </div>
               </div>
               <input type="hidden" name="modal4_cand_id" id="modal4_cand_id">
               <input type="hidden" name="modal4_rel_id" id="modal4_rel_id">
               <input type="hidden" name="modal4_residential_id" id="modal4_residential_id">
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.close')}}</button>
            <button type="button" class="btn btn-primary" onclick="javascript:update_residential()">{{Lang::get('affidavit.update')}}</button>
         </div>
      </div>
   </div>
</div>
<!-- Residential Land Edit Modal End-->
<!-- Residential Delete Modal Start-->
<div class="modal fade" id="deleteResidentialModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.delete_residential_details')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <form>
               <h5>{{Lang::get('affidavit.are_you_sure_to_delete_this_entry')}}</h5>
               <input type="hidden" name="modal_delete_residential_id" id="modal_delete_residential_id">
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.no')}}</button>
            <button type="button" class="btn btn-primary" onclick="javascript:delete_residential_entry()">{{Lang::get('affidavit.yes')}}</button>
         </div>
      </div>
   </div>
</div>
<!-- Residential Delete Modal End-->
<!-- Other Land Edit Modal Start-->
<div class="modal fade" id="exampleModal5" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.edit_other_details')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <form id="model_other">
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.other_assets')}}:</label>
                        <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="modal5_brief_details" id="modal5_brief_details" required="required"></textarea>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>{{Lang::get('affidavit.amount_in_rs')}}:</label>
                        <input type="text"  class="form-control" name="modal5_amount" id="modal5_amount" onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
                     </div>
                  </div>
               </div>
               <input type="hidden" name="modal5_cand_id" id="modal5_cand_id">
               <input type="hidden" name="modal5_rel_id" id="modal5_rel_id">
               <input type="hidden" name="modal5_other_immovable_id" id="modal5_other_immovable_id">
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.close')}}</button>
            <button type="button" class="btn btn-primary" onclick="javascript:update_other_immovable()">{{Lang::get('affidavit.update')}}</button>
         </div>
      </div>
   </div>
</div>
<!-- Other Land Edit Modal End-->
<!-- Other Delete Modal Start-->
<div class="modal fade" id="deleteOtherImmovableModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.delete_other_details')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <form>
               <h5>{{Lang::get('affidavit.are_you_sure_to_delete_this_entry')}}</h5>
               <input type="hidden" name="modal_delete_other_immovable_id" id="modal_delete_other_immovable_id">
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.no')}}</button>
            <button type="button" class="btn btn-primary" onclick="javascript:delete_other_immovable_entry()">{{Lang::get('affidavit.yes')}}</button>
         </div>
      </div>
   </div>
</div>
<!-- Other Delete Modal End-->
@endsection @section('script')
<!-- <script type="text/javascript" src="{{ asset('admintheme/js/jquery-ui.js') }}"></script> -->
<script>
  function NumbersOnly(evt,obj) {

   var charCode = (evt.which) ? evt.which : evt.keyCode;
   
   if(charCode == 190 || charCode == 110)
   {
        return true;
    }
     var regex = new RegExp("^[0-9a-zA-Z. `\b]+$");
            var key = String.fromCharCode(!evt.charCode ? evt.which: evt.charCode);
            if (!regex.test(key)) 
                {
                    alert ( "Special characters are not allowed in this field" ); // Put any message here
                    event.preventDefault();
                    return false;
                } 

   var charCode = (evt.which) ? evt.which : evt.keyCode;
   if(charCode == 190 || charCode == 110)
   {
        return true;
   }else if (charCode >= 96 && charCode <= 106) {
       return true;
   }else if (charCode > 31 && (charCode < 48 || charCode > 57)) {
       return false;
   } else {
       if(charCode != 32)
       {
           return true;
       }
       else
       {
           return false;
       }
   }
   }
</script>
<script type="text/javascript">
   jQuery(function ($) {
         var $active = $('#accordion .panel-collapse.in').prev().addClass('active');
         $active.find('a').prepend('<i class="glyphicon glyphicon-minus"></i>');
         $('#accordion .panel-heading').not($active).find('a').prepend('<i class="glyphicon glyphicon-plus"></i>');
         $('#accordion').on('show.bs.collapse', function (e) {
             $('#accordion .panel-heading.active').removeClass('active').find('.glyphicon').toggleClass('glyphicon-plus glyphicon-minus');
             $(e.target).prev().addClass('active').find('.glyphicon').toggleClass('glyphicon-plus glyphicon-minus');
         })
   });
</script>
<link rel="stylesheet" type="text/css" href="{!! url('admintheme/css/jquery-ui.css') !!}">
<script type="text/javascript" src="{!! url('admintheme/js/jquery-ui.js') !!}"></script>
<script type="text/javascript" src="{!! url('js/jquery.validate.min.js') !!}"></script>
<script type="text/javascript" src="{!! url('js/additional-methods.min.js') !!}"></script>
<script type="text/javascript" src="{{ asset('affidavit/js/remove_special_character.js') }}"></script>
<script type="text/javascript" src="{{ asset('affidavit/js/affidavit_validation.js') }}"></script>
<script type="text/javascript" src="{{ asset('affidavit/js/sweetalert.js') }}"></script>
<script type="text/javascript" src="{{ asset('affidavit/js/jquery-ui.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {  
    $(".datepicker").datepicker({
		dateFormat: 'yy-mm-dd',
		maxDate: 0
    });
	$(".fa-calendar").on("click", function(){
			$(this).siblings("input").datepicker("show");    
		});
    });
</script>
<!-- Agricultural Land Script Start-->
<script type="text/javascript">
   function get_relatives(rel_id)
   {
       if(rel_id)
       {
           $("#property_joint_with_name"+rel_id).val('');
           $("#joint_other_name"+rel_id).val('');
           var property_type_id = $("#property_type_id"+rel_id).val();
           if(property_type_id=="2")
           {
               $("#joint_div"+rel_id).css("display", "block");
           }
           else
           {
               $("#joint_div"+rel_id).css("display", "none");
           }
       }
   }
</script>
<script type="text/javascript">
   function modal_get_relatives()
   {
       $("#modal_property_joint_with").val('');
       $("#modal_joint_other_name").val('');
       var property_type_id = $("#modal_property_type_id").val();
       if(property_type_id=="2")
       {
           $("#modal_property_type_div").css("display", "block");
       }
       else
       {
           $("#modal_property_type_div").css("display", "none");
       }
   }
</script>
<script type="text/javascript">
   function save_agricultural_land(cand_id, rel_id)
   {    
       var location                     = $("#location"+rel_id).val();
       var survey_number                = $("#survey_number"+rel_id).val();
       var area                         = $("#area"+rel_id).val();
       var property_type_id             = $("#property_type_id"+rel_id).val();
       var property_joint_with     		= $("#property_joint_with"+rel_id).val();
       var joint_other_name             = $("#joint_other_name"+rel_id).val();
       var inherited_property           = $("#inherited_property"+rel_id).val();
       var date_of_purchase             = $("#date_of_purchase"+rel_id).val();
       var cost_at_purchase_time        = $("#cost_at_purchase_time"+rel_id).val();
       var investment_on_land           = $("#investment_on_land"+rel_id).val();
       var approx_current_market_value = $("#approx_current_market_value"+rel_id).val();
       //alert(property_joint_with_name);
    if(validate("agricultural"+rel_id))
       {
       $.ajax({
           url: "{{ url('save_agricultural_land') }}",
           type: 'GET',
           data: { 
                   cand_id:cand_id, 
                   rel_type_id:rel_id,
                   location:location, 
                   survey_number:survey_number, 
                   area:area,
                   property_type_id:property_type_id,
                   property_joint_with:property_joint_with,
                   joint_other_name:joint_other_name,
                   inherited_property:inherited_property,
                   date_of_purchase:date_of_purchase,
                   cost_at_purchase_time:cost_at_purchase_time,
                   investment_on_land:investment_on_land,
                   approx_current_market_value:approx_current_market_value
           },            
           headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
           success:function(data){
               if(data!=0)
               {
                   datas = JSON.parse(data);
                 if(property_type_id=="2")
                       var display_account = "Joint "+datas.property_joint_with_name;
                   else
                        var display_account = "Individual";
   
                   //if(joint_other_name!="")
                      // display_account = display_account+","+joint_other_name;
   
                   var edit = '<a href="javascript:void(0)" title="Edit"onclick="javascript:open_modal('+datas.id+',{{$data}})"  data-location="'+location+'" data-survey_number="'+survey_number+'" data-area="'+area+'"  data-property_type_id="'+property_type_id+'" data-property_joint_with="'+datas.property_joint_with+'"   data-joint_other_name="'+joint_other_name+'"  data-inherited_property="'+inherited_property+'" data-date_of_purchase="'+date_of_purchase+'" data-cost_at_purchase_time="'+cost_at_purchase_time+'"  data-investment_on_land="'+investment_on_land+'" data-approx_current_market_value="'+approx_current_market_value+'"  data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_agricultural_land'+datas.id+'"> <span class="btn btn-info btn-sm"><i class="fa fa-edit"></i> Edit</span> </a>';
                   var del = '<a href="javascript:void(0)" title="Delete" onclick="javascript:delete_agricultural_land('+datas.id+')"><span class="btn btn-info btn-danger btn-sm"><i class="fa fa-times"></i> Delete</span></a>';
   
                    $('#relativea'+rel_id).prepend('<tr id="tr'+datas.id+'"><td>'+location+'</td><td>'+survey_number+'</td><td>'+area+'</td><td>'+display_account+'</td><td>'+inherited_property+'</td><td>'+date_of_purchase+'</td><td>'+cost_at_purchase_time+'</td><td>'+investment_on_land+'</td><td>'+approx_current_market_value+'</td>inherited_property</td><td nowrap="nowrap">'+edit+' '+del+'</td></tr>');
   
                   $("#location"+rel_id).val('');
                   $("#survey_number"+rel_id).val('');
                   $("#area"+rel_id).val('');
                   $("#property_type_id"+rel_id).val('');
                   $("#joint_other_name"+rel_id).val('');
                   $("#inherited_property"+rel_id).val('');
                   $("#date_of_purchase"+rel_id).val('');
                   $("#cost_at_purchase_time"+rel_id).val('');
                   $("#investment_on_land"+rel_id).val('');
                   $("#approx_current_market_value"+rel_id).val('');
                   $("#joint_div"+rel_id).css("display", "none");
               }
           }
       });
    }
   }
</script>
<script type="text/javascript">
   function open_modal(id, datas)
   {
       var location = "";
       var survey_number =  "";
       var area =  "";
       var property_type_id =  "";
       var property_joint_with =  "";
	   var joint_other_name =  "";
	   var inherited_property =  "";
       var date_of_purchase =  "";	   
       var cost_at_purchase_time =  "";
       var investment_on_land =  "";
       var approx_current_market_value =  "";
       var relation_type_id =  "";
       $("#modal_property_type_div").css("display", "none");
   
       location = $("#edit_agricultural_land"+id).data("location");
       survey_number = $("#edit_agricultural_land"+id).data("survey_number");
       area = $("#edit_agricultural_land"+id).data("area");
       property_type_id = $("#edit_agricultural_land"+id).data("property_type_id");
       property_joint_with = $("#edit_agricultural_land"+id).data("property_joint_with");
       joint_other_name = $("#edit_agricultural_land"+id).data("joint_other_name");
       inherited_property = $("#edit_agricultural_land"+id).data("inherited_property");
       date_of_purchase = $("#edit_agricultural_land"+id).data("date_of_purchase");
       cost_at_purchase_time = $("#edit_agricultural_land"+id).data("cost_at_purchase_time");
       investment_on_land = $("#edit_agricultural_land"+id).data("investment_on_land");
       approx_current_market_value = $("#edit_agricultural_land"+id).data("approx_current_market_value");
       relation_type_id = $("#edit_agricultural_land"+id).data("relation_type_id");
       candidate_id = $("#edit_agricultural_land"+id).data("candidate_id");
   
       var count = Object.keys(datas).length;
       var all = '';
       for (var i = 0; i < count; i++) { 
       //if(datas[i].id!=10){
           
           if(relation_type_id!=datas[i].relation_type_code)
           {
               if (property_joint_with.toString().indexOf(',') > -1)
               {
                   if(property_joint_with.includes(datas[i].relation_type_code))
                       all += '<option value="'+ datas[i].relation_type_code+'-'+datas[i].name+'" selected>'+ datas[i].name +'</option>'; 
                   else
                       all += '<option value="'+ datas[i].relation_type_code+'-'+datas[i].name+'">'+ datas[i].name +'</option>'; 
               }
               else
               {
                   if(property_joint_with== datas[i].relation_type_code)
                       all += '<option value="'+ datas[i].relation_type_code+'-'+datas[i].name+'" selected>'+ datas[i].name +'</option>';
                   else
                       all += '<option value="'+ datas[i].relation_type_code+'-'+datas[i].name+'">'+ datas[i].name +'</option>'; 
               }
           }
      // }
       }
       if(property_type_id=="2")
       {
           $("#modal_property_type_div").css("display", "block");
       }
   
       
       $("#modal_property_joint_with").html(all);
       $("#modal_location").val(location);
       $("#modal_survey_number").val(survey_number);
       $("#modal_area").val(area);
       $("#modal_property_type_id").val(property_type_id);
       //$("#modal_property_joint_with").val(property_joint_with);
       $("#modal_joint_other_name").val(joint_other_name);
       $("#modal_inherited_property").val(inherited_property);
       $("#modal_date_of_purchase").val(date_of_purchase);
       $("#modal_cost_at_purchase_time").val(cost_at_purchase_time);
       $("#modal_investment_on_land").val(investment_on_land);
       $("#modal_approx_current_market_value").val(approx_current_market_value);
       $("#modal_rel_id").val(relation_type_id);
       $("#modal_cand_id").val(candidate_id);
       $("#modal_agricultural_id").val(id);
       $("#exampleModal").modal('show');
   }
</script>
<script type="text/javascript">
   function update_agricultural_land()
   {
    //alert(12);
   
       var location                     = $("#modal_location").val();
       var survey_number                = $("#modal_survey_number").val();
       var area                         = $("#modal_area").val();
       var property_type_id             = $("#modal_property_type_id").val();
       var property_joint_with     		= $("#modal_property_joint_with").val();
       var joint_other_name             = $("#modal_joint_other_name").val();
       var inherited_property           = $("#modal_inherited_property").val();
       var date_of_purchase             = $("#modal_date_of_purchase").val();
       var cost_at_purchase_time        = $("#modal_cost_at_purchase_time").val();
       var investment_on_land           = $("#modal_investment_on_land").val();
       var approx_current_market_value 	= $("#modal_approx_current_market_value").val();
       var id                           = $("#modal_agricultural_id").val();
	   var rel_id                      	= $("#modal_rel_id").val();
       var cand_id                  	= $("#modal_cand_id").val();
       //alert(property_joint_with);
    if(validate("model_agricultural")){
       $.ajax({
           url: "{{ url($menu_action.'update_agricultural_land') }}",
           type: 'GET',
           data: { 
                   id:id, 
                   cand_id:cand_id, 
                   rel_type_id:rel_id,
                   location:location, 
                   survey_number:survey_number, 
                   area:area,
                   property_type_id:property_type_id,
                   property_joint_with:property_joint_with,
                   joint_other_name:joint_other_name,
                   inherited_property:inherited_property,
                   date_of_purchase:date_of_purchase,
                   cost_at_purchase_time:cost_at_purchase_time,
                   investment_on_land:investment_on_land,
                   approx_current_market_value:approx_current_market_value
           },            
           headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
           success:function(data){
               if(data!=0)
               {
                    datas = JSON.parse(data);
                   if(property_type_id=="2")
                       var display_account = "Joint "+datas.property_joint_with_name;
                   else
                        var display_account = "Individual";
   
                   //if(joint_other_name!="")
                       //display_account = display_account+","+joint_other_name;
                
                $('#relativea'+rel_id+' #tr'+id).remove();
   
                   var edit = '<a href="javascript:void(0)" title="Edit"onclick="javascript:open_modal('+datas.id+',{{$data}})"  data-location="'+location+'" data-survey_number="'+survey_number+'" data-area="'+area+'"  data-property_type_id="'+property_type_id+'" data-property_joint_with="'+datas.property_joint_with+'"   data-joint_other_name="'+joint_other_name+'"  data-inherited_property="'+inherited_property+'" data-date_of_purchase="'+date_of_purchase+'" data-cost_at_purchase_time="'+cost_at_purchase_time+'"  data-investment_on_land="'+investment_on_land+'" data-approx_current_market_value="'+approx_current_market_value+'"  data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_agricultural_land'+datas.id+'"> <span class="btn btn-info btn-sm"><i class="fa fa-edit"></i> Edit</span> </a>';
				   
				   <?php if(Auth::user()->role_id != '19') { ?>
					
					var del = '<a href="javascript:void(0)" title="Delete" onclick="javascript:delete_agricultural_land('+datas.id+')"><span class="btn btn-info btn-danger btn-sm"><i class="fa fa-times"></i> Delete</span></a>';
					
					<?php } else { ?>
					var del = '';	
					<?php } ?>
                   
   
                    $('#relativea'+rel_id).prepend('<tr id="tr'+datas.id+'"><td>'+location+'</td><td>'+survey_number+'</td><td>'+area+'</td><td>'+display_account+'</td><td>'+inherited_property+'</td><td>'+date_of_purchase+'</td><td>'+cost_at_purchase_time+'</td><td>'+investment_on_land+'</td><td>'+approx_current_market_value+'</td>inherited_property</td><td nowrap="nowrap">'+edit+' '+del+'</td></tr>');
   
                   $("#exampleModal").modal('hide');
               }
           }
       });
    }
   }
</script>
<script type="text/javascript">
   function delete_agricultural_land(id)
   {
    $("#modal_delete_agricultural_id").val(id);
       $("#deleteAgriculturalLandModal").modal('show');
   }
</script>
<script type="text/javascript">
   function delete_agricultural_entry()
   {
      var id = $("#modal_delete_agricultural_id").val();
      if(id)
      {
           $.ajax({
               url: "{{ url('delete_agricultural_land') }}",
               type: 'GET',
               data: {  id:id },            
               headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
               success:function(data){
               if(data==1)
               {
                   $('#tr'+id).remove();
                   $("#deleteAgriculturalLandModal").modal('hide');
               }
               }
           });
      }
   }
</script>
<!-- Agricultural Land Script End-->
<!-- Non Agricultural Land Script Start-->
<script type="text/javascript">
   function get_relatives2(rel_id)
   {
       if(rel_id)
       {
           $("#property_joint_with_name2"+rel_id).val('');
           $("#joint_other_name2"+rel_id).val('');
           var property_type_id = $("#property_type_id2"+rel_id).val();
           if(property_type_id=="2")
           {
               $("#joint_div2"+rel_id).css("display", "block");
           }
           else
           {
               $("#joint_div2"+rel_id).css("display", "none");
           }
       }
   }
</script>
<script type="text/javascript">
   function modal_get_relatives2()
   {
       $("#modal2_property_joint_with").val('');
       $("#modal2_joint_other_name").val('');
       var property_type_id = $("#modal2_property_type_id").val();
       if(property_type_id=="2")
       {
           $("#modal2_property_type_div").css("display", "block");
       }
       else
       {
           $("#modal2_property_type_div").css("display", "none");
       }
   }
</script>
<script type="text/javascript">
   function save_non_agricultural_land(cand_id, rel_id)
   {
    
    //alert(rel_id);
    
       var location                     = $("#location2"+rel_id).val();
       var survey_number                = $("#survey_number2"+rel_id).val();
       var area                         = $("#area2"+rel_id).val();
       var property_type_id             = $("#property_type_id2"+rel_id).val();
       var property_joint_with     		= $("#property_joint_with2"+rel_id).val();
       var joint_other_name             = $("#joint_other_name2"+rel_id).val();
       var inherited_property           = $("#inherited_property2"+rel_id).val();
       var date_of_purchase             = $("#date_of_purchase2"+rel_id).val();
       var cost_at_purchase_time        = $("#cost_at_purchase_time2"+rel_id).val();
       var investment_on_land           = $("#investment_on_land2"+rel_id).val();
       var approx_current_market_value = $("#approx_current_market_value2"+rel_id).val();
       //alert(property_joint_with_name);
    if(validate("nonagricultural"+rel_id)){
       $.ajax({
           url: "{{ url('save_non_agricultural_land') }}",
           type: 'GET',
           data: { 
                   cand_id:cand_id, 
                   rel_type_id:rel_id,
                   location:location, 
                   survey_number:survey_number, 
                   area:area,
                   property_type_id:property_type_id,
                   property_joint_with:property_joint_with,
                   joint_other_name:joint_other_name,
                   joint_other_name:joint_other_name,
                   inherited_property:inherited_property,
                   date_of_purchase:date_of_purchase,
                   cost_at_purchase_time:cost_at_purchase_time,
                   investment_on_land:investment_on_land,
                   approx_current_market_value:approx_current_market_value
           },            
           headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
           success:function(data){
               if(data!=0)
               {
                   datas = JSON.parse(data);
                   if(property_type_id=="2")
                       var display_account = "Joint "+datas.property_joint_with_name;
                   else
                        var display_account = "Individual";
   
                   //if(joint_other_name!="")
                       //display_account = display_account+","+joint_other_name;
   
                   var edit = '<a href="javascript:void(0)" title="Edit"onclick="javascript:open_modal2('+datas.id+',{{$data}})"  data-location="'+location+'" data-survey_number="'+survey_number+'" data-area="'+area+'"  data-property_type_id="'+property_type_id+'" data-property_joint_with="'+datas.property_joint_with+'"   data-joint_other_name="'+joint_other_name+'"  data-inherited_property="'+inherited_property+'" data-date_of_purchase="'+date_of_purchase+'" data-cost_at_purchase_time="'+cost_at_purchase_time+'"  data-investment_on_land="'+investment_on_land+'" data-approx_current_market_value="'+approx_current_market_value+'"  data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_non_agricultural_land'+datas.id+'"> <span class="btn btn-info btn-sm"><i class="fa fa-edit"></i> Edit</span> </a>';
                   var del = '<a href="javascript:void(0)" title="Delete" onclick="javascript:delete_non_agricultural_land('+datas.id+')"><span class="btn btn-info btn-danger btn-sm"><i class="fa fa-times"></i> Delete</span></a>';
   
                    $('#relativeb'+rel_id).prepend('<tr id="tr'+datas.id+'"><td>'+location+'</td><td>'+survey_number+'</td><td>'+area+'</td><td>'+display_account+'</td><td>'+inherited_property+'</td><td>'+date_of_purchase+'</td><td>'+cost_at_purchase_time+'</td><td>'+investment_on_land+'</td><td>'+approx_current_market_value+'</td>inherited_property</td><td nowrap="nowrap">'+edit+' '+del+'</td></tr>');
   
                   $("#location2"+rel_id).val('');
                   $("#survey_number2"+rel_id).val('');
                   $("#area2"+rel_id).val('');
                   $("#property_type_id2"+rel_id).val('');
                   $("#property_joint_with2"+rel_id).val('');
                   $("#joint_other_name2"+rel_id).val('');
                   $("#inherited_property2"+rel_id).val('');
                   $("#date_of_purchase2"+rel_id).val('');
                   $("#cost_at_purchase_time2"+rel_id).val('');
                   $("#investment_on_land2"+rel_id).val('');
                   $("#approx_current_market_value2"+rel_id).val('');
                   $("#joint_div2"+rel_id).css("display", "none");
               }
           }
       });
    }
   }
   
   
</script>
<script type="text/javascript">
   function open_modal2(id, datas)
   {
       var location = "";
       var survey_number =  "";
       var area =  "";
       var property_type_id =  "";
       var property_joint_with =  "";
       var inherited_property =  "";
       var date_of_purchase =  "";
       var joint_other_name =  "";
       var cost_at_purchase_time =  "";
       var investment_on_land =  "";
       var approx_current_market_value =  "";
       var relation_type_id =  "";
       $("#modal2_property_type_div").css("display", "none");
   
       location = $("#edit_non_agricultural_land"+id).data("location");
       survey_number = $("#edit_non_agricultural_land"+id).data("survey_number");
       area = $("#edit_non_agricultural_land"+id).data("area");
       property_type_id = $("#edit_non_agricultural_land"+id).data("property_type_id");
       property_joint_with = $("#edit_non_agricultural_land"+id).data("property_joint_with");
       joint_other_name = $("#edit_non_agricultural_land"+id).data("joint_other_name");
       inherited_property = $("#edit_non_agricultural_land"+id).data("inherited_property");
       date_of_purchase = $("#edit_non_agricultural_land"+id).data("date_of_purchase");
       cost_at_purchase_time = $("#edit_non_agricultural_land"+id).data("cost_at_purchase_time");
       investment_on_land = $("#edit_non_agricultural_land"+id).data("investment_on_land");
       approx_current_market_value = $("#edit_non_agricultural_land"+id).data("approx_current_market_value");
       relation_type_id = $("#edit_non_agricultural_land"+id).data("relation_type_id");
       candidate_id = $("#edit_non_agricultural_land"+id).data("candidate_id");
   
       var count = Object.keys(datas).length;
       var all = '';
       for (var i = 0; i < count; i++) { 
       if(datas[i].id!=10)
       {
           
           if(relation_type_id!=datas[i].relation_type_code)
           {
               if (property_joint_with.toString().indexOf(',') > -1)
               {
                   if(property_joint_with.includes(datas[i].relation_type_code))
                       all += '<option value="'+ datas[i].relation_type_code+'-'+datas[i].name+'" selected>'+ datas[i].name +'</option>'; 
                   else
                       all += '<option value="'+ datas[i].relation_type_code+'-'+datas[i].name+'">'+ datas[i].name +'</option>'; 
               }
               else
               {
                   if(property_joint_with== datas[i].relation_type_code)
                       all += '<option value="'+ datas[i].relation_type_code+'-'+datas[i].name+'" selected>'+ datas[i].name +'</option>';
                   else
                       all += '<option value="'+ datas[i].relation_type_code+'-'+datas[i].name+'">'+ datas[i].name +'</option>'; 
               }
           }
       }
       }
       if(property_type_id=="2")
       {
           $("#modal2_property_type_div").css("display", "block");
       }
   
       
       $("#modal2_property_joint_with").html(all);
       $("#modal2_location").val(location);
       $("#modal2_survey_number").val(survey_number);
       $("#modal2_area").val(area);
       $("#modal2_property_type_id").val(property_type_id);
       $("#modal2_joint_other_name").val(joint_other_name);
       $("#modal2_inherited_property").val(inherited_property);
       $("#modal2_date_of_purchase").val(date_of_purchase);
       $("#modal2_cost_at_purchase_time").val(cost_at_purchase_time);
       $("#modal2_investment_on_land").val(investment_on_land);
       $("#modal2_approx_current_market_value").val(approx_current_market_value);
       $("#modal2_rel_id").val(relation_type_id);
       $("#modal2_cand_id").val(candidate_id);
       $("#modal2_non_agricultural_id").val(id);
       $("#exampleModal2").modal('show');
   }
</script>
<script type="text/javascript">
   function update_non_agricultural_land()
   {
    //alert(12);
   
       var location                     = $("#modal2_location").val();
       var survey_number                = $("#modal2_survey_number").val();
       var area                         = $("#modal2_area").val();
       var property_type_id             = $("#modal2_property_type_id").val();
       var property_joint_with     		= $("#modal2_property_joint_with").val();
       var joint_other_name             = $("#modal2_joint_other_name").val();
       var inherited_property           = $("#modal2_inherited_property").val();
       var date_of_purchase             = $("#modal2_date_of_purchase").val();
       var cost_at_purchase_time        = $("#modal2_cost_at_purchase_time").val();
       var investment_on_land           = $("#modal2_investment_on_land").val();
       var approx_current_market_value 	= $("#modal2_approx_current_market_value").val();
       var id                           = $("#modal2_non_agricultural_id").val();
	   var rel_id                      	= $("#modal2_rel_id").val();
       var cand_id                  	= $("#modal2_cand_id").val();
       //alert(property_joint_with_name);
    if(validate("model_nonagricultural")){
       $.ajax({
           url: "{{ url($menu_action.'update_non_agricultural_land') }}",
           type: 'GET',
           data: { 
                   id:id, 
                   cand_id:cand_id, 
                   rel_type_id:rel_id,
                   location:location, 
                   survey_number:survey_number, 
                   area:area,
                   property_type_id:property_type_id,
                   property_joint_with:property_joint_with,
                   joint_other_name:joint_other_name,
                   inherited_property:inherited_property,
                   date_of_purchase:date_of_purchase,
                   cost_at_purchase_time:cost_at_purchase_time,
                   investment_on_land:investment_on_land,
                   approx_current_market_value:approx_current_market_value
           },            
           headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
           success:function(data){
               if(data!=0)
               {
                    datas = JSON.parse(data);
                   if(property_type_id=="2")
                       var display_account = "Joint "+datas.property_joint_with_name;
                   else
                        var display_account = "Individual";
   
                   //if(joint_other_name!="")
                       //display_account = display_account+","+joint_other_name;
                
                $('#relativeb'+rel_id+' #tr'+id).remove();
   
                   var edit = '<a href="javascript:void(0)" title="Edit"onclick="javascript:open_modal2('+datas.id+',{{$data}})"  data-location="'+location+'" data-survey_number="'+survey_number+'" data-area="'+area+'"  data-property_type_id="'+property_type_id+'" data-property_joint_with="'+datas.property_joint_with+'"   data-joint_other_name="'+joint_other_name+'"  data-inherited_property="'+inherited_property+'" data-date_of_purchase="'+date_of_purchase+'" data-cost_at_purchase_time="'+cost_at_purchase_time+'"  data-investment_on_land="'+investment_on_land+'" data-approx_current_market_value="'+approx_current_market_value+'"  data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_non_agricultural_land'+datas.id+'"> <span class="btn btn-info btn-sm"><i class="fa fa-edit"></i> Edit</span> </a>';
				   
				   <?php if(Auth::user()->role_id != '19') { ?>
					
					var del = '<a href="javascript:void(0)" title="Delete" onclick="javascript:delete_non_agricultural_land('+datas.id+')"><span class="btn btn-info btn-danger btn-sm"><i class="fa fa-times"></i> Delete</span></a>';
					
					<?php } else { ?>
					var del = '';	
					<?php } ?>
					
   
                    $('#relativeb'+rel_id).prepend('<tr id="tr'+datas.id+'"><td>'+location+'</td><td>'+survey_number+'</td><td>'+area+'</td><td>'+display_account+'</td><td>'+inherited_property+'</td><td>'+date_of_purchase+'</td><td>'+cost_at_purchase_time+'</td><td>'+investment_on_land+'</td><td>'+approx_current_market_value+'</td>inherited_property</td><td nowrap="nowrap">'+edit+' '+del+'</td></tr>');
   
                   $("#exampleModal2").modal('hide');
               }
           }
       });
    }
   }
</script>
<script type="text/javascript">
   function delete_non_agricultural_land(id)
   {
    $("#modal_delete_non_agricultural_id").val(id);
       $("#deleteNonAgriculturalLandModal").modal('show');
   }
</script>
<script type="text/javascript">
   function delete_non_agricultural_entry()
   {
      var id = $("#modal_delete_non_agricultural_id").val();
      if(id)
      {
           $.ajax({
               url: "{{ url('delete_non_agricultural_land') }}",
               type: 'GET',
               data: {  id:id },            
               headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
               success:function(data){
               if(data==1)
               {
                   $('#tr'+id).remove();
                   $("#deleteNonAgriculturalLandModal").modal('hide');
               }
               }
           });
      }
   }
</script>
<!-- Non Agricultural Land Script End-->
<!-- Commercial Land Script Start-->
<script type="text/javascript">
   function get_relatives3(rel_id)
   {
       if(rel_id)
       {
           $("#property_joint_with_name3"+rel_id).val('');
           $("#joint_other_name3"+rel_id).val('');
           var property_type_id = $("#property_type_id3"+rel_id).val();
           if(property_type_id=="2")
           {
               $("#joint_div3"+rel_id).css("display", "block");
           }
           else
           {
               $("#joint_div3"+rel_id).css("display", "none");
           }
       }
   }
</script>
<script type="text/javascript">
   function modal_get_relatives3()
   {
       $("#modal3_property_joint_with").val('');
       $("#modal3_joint_other_name").val('');
       var property_type_id = $("#modal3_property_type_id").val();
       if(property_type_id=="2")
       {
           $("#modal3_property_type_div").css("display", "block");
       }
       else
       {
           $("#modal3_property_type_div").css("display", "none");
       }
   }
</script>
<script type="text/javascript">
   function save_commercial(cand_id, rel_id)
   {
    
    //alert(rel_id);
    
       var location                     = $("#location3"+rel_id).val();
       var survey_number                = $("#survey_number3"+rel_id).val();
       var area                         = $("#area3"+rel_id).val();
       var built_up_area                = $("#built_up_area3"+rel_id).val();
       var property_type_id             = $("#property_type_id3"+rel_id).val();
       var property_joint_with     		= $("#property_joint_with3"+rel_id).val();
       var joint_other_name             = $("#joint_other_name3"+rel_id).val();
       var inherited_property           = $("#inherited_property3"+rel_id).val();
       var date_of_purchase             = $("#date_of_purchase3"+rel_id).val();
       var cost_at_purchase_time        = $("#cost_at_purchase_time3"+rel_id).val();
       var investment_on_buildings  = $("#investment_on_buildings3"+rel_id).val();
       var approx_current_market_value = $("#approx_current_market_value3"+rel_id).val();
       //alert(property_joint_with_name);
    if(validate("commercial"+rel_id)){
       $.ajax({
           url: "{{ url('save_commercial') }}",
           type: 'GET',
           data: { 
                   cand_id:cand_id, 
                   rel_type_id:rel_id,
                   location:location, 
                   survey_number:survey_number, 
                   area:area,
                   built_up_area:built_up_area,
                   property_type_id:property_type_id,
                   property_joint_with:property_joint_with,
                   joint_other_name:joint_other_name,
                   joint_other_name:joint_other_name,
                   inherited_property:inherited_property,
                   date_of_purchase:date_of_purchase,
                   cost_at_purchase_time:cost_at_purchase_time,
                   investment_on_buildings:investment_on_buildings,
                   approx_current_market_value:approx_current_market_value
           },            
           headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
           success:function(data){
               if(data!=0)
               {
                   datas = JSON.parse(data);
                   if(property_type_id=="2")
                       var display_account = "Joint "+datas.property_joint_with_name;
                   else
                        var display_account = "Individual";
   
                   //if(joint_other_name!="")
                       //display_account = display_account+","+joint_other_name;
   
                   var edit = '<a href="javascript:void(0)" title="Edit"onclick="javascript:open_modal3('+datas.id+',{{$data}})"  data-location="'+location+'" data-survey_number="'+survey_number+'" data-area="'+area+'" data-built_up_area="'+built_up_area+'"  data-property_type_id="'+property_type_id+'" data-property_joint_with="'+datas.property_joint_with+'"   data-joint_other_name="'+joint_other_name+'"  data-inherited_property="'+inherited_property+'" data-date_of_purchase="'+date_of_purchase+'" data-cost_at_purchase_time="'+cost_at_purchase_time+'"  data-investment_on_buildings="'+investment_on_buildings+'" data-approx_current_market_value="'+approx_current_market_value+'"  data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_commercial'+datas.id+'"> <span class="btn btn-info btn-sm"><i class="fa fa-edit"></i> Edit</span> </a>';
                   var del = '<a href="javascript:void(0)" title="Delete" onclick="javascript:delete_commercial('+datas.id+')"><span class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Delete</span></a>';
   
                    $('#relativec'+rel_id).prepend('<tr id="tr'+datas.id+'"><td>'+location+'</td><td>'+survey_number+'</td><td>'+area+'</td><td>'+built_up_area+'</td><td>'+display_account+'</td><td>'+inherited_property+'</td><td>'+date_of_purchase+'</td><td>'+cost_at_purchase_time+'</td><td>'+investment_on_buildings+'</td><td>'+approx_current_market_value+'</td>inherited_property</td><td nowrap="nowrap">'+edit+' '+del+'</td></tr>');
   
                   $("#location3"+rel_id).val('');
                   $("#survey_number3"+rel_id).val('');
                   $("#area3"+rel_id).val('');
                   $("#built_up_area3"+rel_id).val('');
                   $("#property_type_id3"+rel_id).val('');
                   $("#joint_other_name3"+rel_id).val('');
                   $("#inherited_property3"+rel_id).val('');
                   $("#date_of_purchase3"+rel_id).val('');
                   $("#cost_at_purchase_time3"+rel_id).val('');
                   $("#investment_on_buildings3"+rel_id).val('');
                   $("#approx_current_market_value3"+rel_id).val('');
                   $("#joint_div3"+rel_id).css("display", "none");
               }
           }
       });
    }
   }
   
</script>
<script type="text/javascript">
   function open_modal3(id, datas)
   {
       var location = "";
       var survey_number =  "";
       var area =  "";
       var built_up_area =  "";
       var property_type_id =  "";
       var property_joint_with =  "";
       var date_of_purchase =  "";
       var property_joint_with =  "";
       var joint_other_name =  "";
       var cost_at_purchase_time =  "";
       var investment_on_buildings =  "";
       var approx_current_market_value =  "";
       var relation_type_id =  "";
       $("#modal3_property_type_div").css("display", "none");
   
       location = $("#edit_commercial"+id).data("location");
       survey_number = $("#edit_commercial"+id).data("survey_number");
       area = $("#edit_commercial"+id).data("area");
       built_up_area = $("#edit_commercial"+id).data("built_up_area");
       property_type_id = $("#edit_commercial"+id).data("property_type_id");
       property_joint_with = $("#edit_commercial"+id).data("property_joint_with");
       joint_other_name = $("#edit_commercial"+id).data("joint_other_name");
       inherited_property = $("#edit_commercial"+id).data("inherited_property");
       date_of_purchase = $("#edit_commercial"+id).data("date_of_purchase");
       cost_at_purchase_time = $("#edit_commercial"+id).data("cost_at_purchase_time");
       investment_on_buildings = $("#edit_commercial"+id).data("investment_on_buildings");
       approx_current_market_value = $("#edit_commercial"+id).data("approx_current_market_value");
       relation_type_id = $("#edit_commercial"+id).data("relation_type_id");
       candidate_id = $("#edit_commercial"+id).data("candidate_id");
   
       var count = Object.keys(datas).length;
       var all = '';
       for (var i = 0; i < count; i++) { 
       if(datas[i].id!=10)
       {
           
           if(relation_type_id!=datas[i].relation_type_code)
           {
               if (property_joint_with.toString().indexOf(',') > -1)
               {
                   if(property_joint_with.includes(datas[i].relation_type_code))
                       all += '<option value="'+ datas[i].relation_type_code+'-'+datas[i].name+'" selected>'+ datas[i].name +'</option>'; 
                   else
                       all += '<option value="'+ datas[i].relation_type_code+'-'+datas[i].name+'">'+ datas[i].name +'</option>'; 
               }
               else
               {
                   if(property_joint_with== datas[i].relation_type_code)
                       all += '<option value="'+ datas[i].relation_type_code+'-'+datas[i].name+'" selected>'+ datas[i].name +'</option>';
                   else
                       all += '<option value="'+ datas[i].relation_type_code+'-'+datas[i].name+'">'+ datas[i].name +'</option>'; 
               }
           }
       }
       }
       if(property_type_id=="2")
       {
           $("#modal3_property_type_div").css("display", "block");
       }
   
       
       $("#modal3_property_joint_with").html(all);
       $("#modal3_location").val(location);
       $("#modal3_survey_number").val(survey_number);
       $("#modal3_area").val(area);
       $("#modal3_built_up_area").val(built_up_area);
       $("#modal3_property_type_id").val(property_type_id);
       $("#modal3_joint_other_name").val(joint_other_name);
       $("#modal3_inherited_property").val(inherited_property);
       $("#modal3_date_of_purchase").val(date_of_purchase);
       $("#modal3_cost_at_purchase_time").val(cost_at_purchase_time);
       $("#modal3_investment_on_buildings").val(investment_on_buildings);
       $("#modal3_approx_current_market_value").val(approx_current_market_value);
       $("#modal3_rel_id").val(relation_type_id);
       $("#modal3_cand_id").val(candidate_id);
       $("#modal3_commercial_id").val(id);
       $("#exampleModal3").modal('show');
   }
</script>
<script type="text/javascript">
   function update_commercial()
   {
    //alert(12);
   
       var location                     = $("#modal3_location").val();
       var survey_number                = $("#modal3_survey_number").val();
       var area                         = $("#modal3_area").val();
       var built_up_area                = $("#modal3_built_up_area").val();
       var property_type_id             = $("#modal3_property_type_id").val();
       var property_joint_with     		= $("#modal3_property_joint_with").val();
       var joint_other_name             = $("#modal3_joint_other_name").val();
       var inherited_property           = $("#modal3_inherited_property").val();
       var date_of_purchase             = $("#modal3_date_of_purchase").val();
       var cost_at_purchase_time        = $("#modal3_cost_at_purchase_time").val();
       var investment_on_buildings      = $("#modal3_investment_on_buildings").val();
       var approx_current_market_value 	= $("#modal3_approx_current_market_value").val();
       var id                           = $("#modal3_commercial_id").val();
	   var rel_id                      	= $("#modal3_rel_id").val();
       var cand_id                  	= $("#modal3_cand_id").val();
       //alert(property_joint_with_name);
    if(validate("model_commercial")){
       $.ajax({
           url: "{{ url($menu_action.'update_commercial') }}",
           type: 'GET',
           data: { 
                   id:id, 
                   cand_id:cand_id, 
                   rel_type_id:rel_id,
                   location:location, 
                   survey_number:survey_number, 
                   area:area,
                   built_up_area:built_up_area,
                   property_type_id:property_type_id,
                   property_joint_with:property_joint_with,
                   joint_other_name:joint_other_name,
                   inherited_property:inherited_property,
                   date_of_purchase:date_of_purchase,
                   cost_at_purchase_time:cost_at_purchase_time,
                   investment_on_buildings:investment_on_buildings,
                   approx_current_market_value:approx_current_market_value
           },            
           headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
           success:function(data){
               if(data!=0)
               {
                    datas = JSON.parse(data);
                   if(property_type_id=="2")
                       var display_account = "Joint "+datas.property_joint_with_name;
                   else
                        var display_account = "Individual";
   
                   //if(joint_other_name!="")
                       //display_account = display_account+","+joint_other_name;
                
                $('#relativec'+rel_id+' #tr'+id).remove();
   
                   var edit = '<a href="javascript:void(0)" title="Edit"onclick="javascript:open_modal3('+datas.id+',{{$data}})"  data-location="'+location+'" data-survey_number="'+survey_number+'" data-area="'+area+'" data-built_up_area="'+built_up_area+'"  data-property_type_id="'+property_type_id+'" data-property_joint_with="'+datas.property_joint_with+'"   data-joint_other_name="'+joint_other_name+'"  data-inherited_property="'+inherited_property+'" data-date_of_purchase="'+date_of_purchase+'" data-cost_at_purchase_time="'+cost_at_purchase_time+'"  data-investment_on_buildings="'+investment_on_buildings+'" data-approx_current_market_value="'+approx_current_market_value+'"  data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_commercial'+datas.id+'"> <span class="btn btn-info btn-sm"><i class="fa fa-edit"></i> Edit</span> </a>';
				   
				   <?php if(Auth::user()->role_id != '19') { ?>
					
					var del = '<a href="javascript:void(0)" title="Delete" onclick="javascript:delete_commercial('+datas.id+')"><span class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Delete</span></a>';
					
					<?php } else { ?>
					var del = '';	
					<?php } ?>
				   
   
                    $('#relativec'+rel_id).prepend('<tr id="tr'+datas.id+'"><td>'+location+'</td><td>'+survey_number+'</td><td>'+area+'</td><td>'+built_up_area+'</td><td>'+display_account+'</td><td>'+inherited_property+'</td><td>'+date_of_purchase+'</td><td>'+cost_at_purchase_time+'</td><td>'+investment_on_buildings+'</td><td>'+approx_current_market_value+'</td>inherited_property</td><td nowrap="nowrap">'+edit+' '+del+'</td></tr>');
   
                   $("#exampleModal3").modal('hide');
               }
           }
       });
    }
   }
</script>
<script type="text/javascript">
   function delete_commercial(id)
   {
    $("#modal_delete_commercial_id").val(id);
       $("#deleteCommercialModal").modal('show');
   }
</script>
<script type="text/javascript">
   function delete_commercial_entry()
   {
      var id = $("#modal_delete_commercial_id").val();
      if(id)
      {
           $.ajax({
               url: "{{ url('delete_commercial') }}",
               type: 'GET',
               data: {  id:id },            
               headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
               success:function(data){
               if(data==1)
               {
                   $('#tr'+id).remove();
                   $("#deleteCommercialModal").modal('hide');
               }
               }
           });
      }
   }
</script>
<!-- Commercial Land Script End-->
<!-- Residential Land Script Start-->
<script type="text/javascript">
   function get_relatives4(rel_id)
   {
       if(rel_id)
       {
           $("#property_joint_with4"+rel_id).val('');
           $("#joint_other_name4"+rel_id).val('');
           var property_type_id = $("#property_type_id4"+rel_id).val();
           if(property_type_id=="2")
           {
               $("#joint_div4"+rel_id).css("display", "block");
           }
           else
           {
               $("#joint_div4"+rel_id).css("display", "none");
           }
       }
   }
</script>
<script type="text/javascript">
   function modal_get_relatives4()
   {
       $("#modal4_property_joint_with").val('');
       $("#modal4_joint_other_name").val('');
       var property_type_id = $("#modal4_property_type_id").val();
       if(property_type_id=="2")
       {
           $("#modal4_property_type_div").css("display", "block");
       }
       else
       {
           $("#modal4_property_type_div").css("display", "none");
       }
   }
</script>
<script type="text/javascript">
   function save_residential(cand_id, rel_id)
   {
    
    //alert(rel_id);
    
       var location                     = $("#location4"+rel_id).val();
       var survey_number                = $("#survey_number4"+rel_id).val();
       var area                         = $("#area4"+rel_id).val();
       var built_up_area                = $("#built_up_area4"+rel_id).val();
       var property_type_id             = $("#property_type_id4"+rel_id).val();
       var property_joint_with     		= $("#property_joint_with4"+rel_id).val();
       var joint_other_name             = $("#joint_other_name4"+rel_id).val();
       var inherited_property           = $("#inherited_property4"+rel_id).val();
       var date_of_purchase             = $("#date_of_purchase4"+rel_id).val();
       var cost_at_purchase_time        = $("#cost_at_purchase_time4"+rel_id).val();
       var investment_on_buildings  	= $("#investment_on_buildings4"+rel_id).val();
       var approx_current_market_value 	= $("#approx_current_market_value4"+rel_id).val();
       //alert(property_joint_with_name);
    if(validate("residential"+rel_id)){
       $.ajax({
           url: "{{ url('save_residential') }}",
           type: 'GET',
           data: { 
                   cand_id:cand_id, 
                   rel_type_id:rel_id,
                   location:location, 
                   survey_number:survey_number, 
                   area:area,
                   built_up_area:built_up_area,
                   property_type_id:property_type_id,
                   property_joint_with:property_joint_with,
                   joint_other_name:joint_other_name,
                   joint_other_name:joint_other_name,
                   inherited_property:inherited_property,
                   date_of_purchase:date_of_purchase,
                   cost_at_purchase_time:cost_at_purchase_time,
                   investment_on_buildings:investment_on_buildings,
                   approx_current_market_value:approx_current_market_value
           },            
           headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
           success:function(data){
               if(data!=0)
               {
                   datas = JSON.parse(data);
                   if(property_type_id=="2")
                       var display_account = "Joint "+datas.property_joint_with_name;
                   else
                        var display_account = "Individual";
   
                   if(joint_other_name!="")
                       //display_account = display_account+","+joint_other_name;
   
					console.log(datas);
   
                   var edit = '<a href="javascript:void(0)" title="Edit"onclick="javascript:open_modal4('+datas.id+',{{$data}})"  data-location="'+location+'" data-survey_number="'+survey_number+'" data-area="'+area+'" data-built_up_area="'+built_up_area+'"  data-property_type_id="'+property_type_id+'" data-property_joint_with="'+datas.property_joint_with+'"   data-joint_other_name="'+joint_other_name+'"  data-inherited_property="'+inherited_property+'" data-date_of_purchase="'+date_of_purchase+'" data-cost_at_purchase_time="'+cost_at_purchase_time+'"  data-investment_on_buildings="'+investment_on_buildings+'" data-approx_current_market_value="'+approx_current_market_value+'"  data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_residential'+datas.id+'"> <span class="btn btn-info btn-sm"><i class="fa fa-edit"></i> Edit</span> </a>';
				   
				   //console.log(edit);
				   
				   
				   
				   
				   //var edit = 'Edit';
				   
				   
                   var del = '<a href="javascript:void(0)" title="Delete" onclick="javascript:delete_residential('+datas.id+')"><span class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Delete</span></a>';
   
                    $('#relatived'+rel_id).prepend('<tr id="tr'+datas.id+'"><td>'+location+'</td><td>'+survey_number+'</td><td>'+area+'</td><td>'+built_up_area+'</td><td>'+display_account+'</td><td>'+inherited_property+'</td><td>'+date_of_purchase+'</td><td>'+cost_at_purchase_time+'</td><td>'+investment_on_buildings+'</td><td>'+approx_current_market_value+'</td><td nowrap="nowrap">'+edit+' '+del+'</td></tr>');
   
                   $("#location4"+rel_id).val('');
                   $("#survey_number4"+rel_id).val('');
                   $("#area4"+rel_id).val('');
                   $("#built_up_area4"+rel_id).val('');
                   $("#property_type_id4"+rel_id).val('');
                   $("#joint_other_name4"+rel_id).val('');
                   $("#inherited_property4"+rel_id).val('');
                   $("#date_of_purchase4"+rel_id).val('');
                   $("#cost_at_purchase_time4"+rel_id).val('');
                   $("#investment_on_buildings4"+rel_id).val('');
                   $("#approx_current_market_value4"+rel_id).val('');
                   $("#joint_div4"+rel_id).css("display", "none");
               }
           }
       });
    }
   }
</script>
<script type="text/javascript">
   function open_modal4(id, datas)
   {
       var location = "";
       var survey_number =  "";
       var area =  "";
       var built_up_area =  "";
       var property_type_id =  "";
       var property_joint_with =  "";
       var date_of_purchase =  "";
       var property_joint_with =  "";
       var joint_other_name =  "";
       var cost_at_purchase_time =  "";
       var investment_on_buildings =  "";
       var approx_current_market_value =  "";
       var relation_type_id =  "";
       $("#modal4_property_type_div").css("display", "none");
   
       location = $("#edit_residential"+id).data("location");
       survey_number = $("#edit_residential"+id).data("survey_number");
       area = $("#edit_residential"+id).data("area");
       built_up_area = $("#edit_residential"+id).data("built_up_area");
       property_type_id = $("#edit_residential"+id).data("property_type_id");
       property_joint_with = $("#edit_residential"+id).data("property_joint_with");
       joint_other_name = $("#edit_residential"+id).data("joint_other_name");
       inherited_property = $("#edit_residential"+id).data("inherited_property");
       date_of_purchase = $("#edit_residential"+id).data("date_of_purchase");
       cost_at_purchase_time = $("#edit_residential"+id).data("cost_at_purchase_time");
       investment_on_buildings = $("#edit_residential"+id).data("investment_on_buildings");
       approx_current_market_value = $("#edit_residential"+id).data("approx_current_market_value");
       relation_type_id = $("#edit_residential"+id).data("relation_type_id");
       candidate_id = $("#edit_residential"+id).data("candidate_id");
   
       var count = Object.keys(datas).length;
       var all = '';
       for (var i = 0; i < count; i++) { 
       if(datas[i].id!=10)
       {
           
           if(relation_type_id!=datas[i].relation_type_code)
           {
               if (property_joint_with.toString().indexOf(',') > -1)
               {
                   if(property_joint_with.includes(datas[i].relation_type_code))
                       all += '<option value="'+ datas[i].relation_type_code+'-'+datas[i].name+'" selected>'+ datas[i].name +'</option>'; 
                   else
                       all += '<option value="'+ datas[i].relation_type_code+'-'+datas[i].name+'">'+ datas[i].name +'</option>'; 
               }
               else
               {
                   if(property_joint_with== datas[i].relation_type_code)
                       all += '<option value="'+ datas[i].relation_type_code+'-'+datas[i].name+'" selected>'+ datas[i].name +'</option>';
                   else
                       all += '<option value="'+ datas[i].relation_type_code+'-'+datas[i].name+'">'+ datas[i].name +'</option>'; 
               }
           }
       }
       }
       if(property_type_id=="2")
       {
           $("#modal4_property_type_div").css("display", "block");
       }
   
       
       $("#modal4_property_joint_with").html(all);
       $("#modal4_location").val(location);
       $("#modal4_survey_number").val(survey_number);
       $("#modal4_area").val(area);
       $("#modal4_built_up_area").val(built_up_area);
       $("#modal4_property_type_id").val(property_type_id);
       $("#modal4_joint_other_name").val(joint_other_name);
       $("#modal4_inherited_property").val(inherited_property);
       $("#modal4_date_of_purchase").val(date_of_purchase);
       $("#modal4_cost_at_purchase_time").val(cost_at_purchase_time);
       $("#modal4_investment_on_buildings").val(investment_on_buildings);
       $("#modal4_approx_current_market_value").val(approx_current_market_value);
       $("#modal4_rel_id").val(relation_type_id);
       $("#modal4_cand_id").val(candidate_id);
       $("#modal4_residential_id").val(id);
       $("#exampleModal4").modal('show');
   }
</script>
<script type="text/javascript">
   function update_residential()
   {
    //alert(12);
   
       var location                     = $("#modal4_location").val();
       var survey_number                = $("#modal4_survey_number").val();
       var area                         = $("#modal4_area").val();
       var built_up_area                = $("#modal4_built_up_area").val();
       var property_type_id             = $("#modal4_property_type_id").val();
       var property_joint_with     		= $("#modal4_property_joint_with").val();
       var joint_other_name             = $("#modal4_joint_other_name").val();
       var inherited_property           = $("#modal4_inherited_property").val();
       var date_of_purchase             = $("#modal4_date_of_purchase").val();
       var cost_at_purchase_time        = $("#modal4_cost_at_purchase_time").val();
       var investment_on_buildings      = $("#modal4_investment_on_buildings").val();
       var approx_current_market_value  = $("#modal4_approx_current_market_value").val();
       var id                           = $("#modal4_residential_id").val();
       var rel_id                      	= $("#modal4_rel_id").val();
       var cand_id                  	= $("#modal4_cand_id").val();
       //alert(property_joint_with_name);
    if(validate("model_residential")){
       $.ajax({
           url: "{{ url($menu_action.'update_residential') }}",
           type: 'GET',
           data: { 
                   id:id, 
                   cand_id:cand_id, 
                   rel_type_id:rel_id,
                   location:location, 
                   survey_number:survey_number, 
                   area:area,
                   built_up_area:built_up_area,
                   property_type_id:property_type_id,
                   property_joint_with:property_joint_with,
                   joint_other_name:joint_other_name,
                   joint_other_name:joint_other_name,
                   inherited_property:inherited_property,
                   date_of_purchase:date_of_purchase,
                   cost_at_purchase_time:cost_at_purchase_time,
                   investment_on_buildings:investment_on_buildings,
                   approx_current_market_value:approx_current_market_value
           },            
           headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
           success:function(data){
               if(data!=0)
               {
                    datas = JSON.parse(data);
                   if(property_type_id=="2")
                       var display_account = "Joint "+datas.property_joint_with_name;
                   else
                        var display_account = "Individual";
   
                   //if(joint_other_name!="")
                      // display_account = display_account+","+joint_other_name;
   
                $('#relatived'+rel_id+' #tr'+id).remove();
                
                   var edit = '<a href="javascript:void(0)" title="Edit"onclick="javascript:open_modal4('+datas.id+',{{$data}})"  data-location="'+location+'" data-survey_number="'+survey_number+'" data-area="'+area+'" data-built_up_area="'+built_up_area+'"  data-property_type_id="'+property_type_id+'" data-property_joint_with="'+datas.property_joint_with+'"   data-joint_other_name="'+joint_other_name+'"  data-inherited_property="'+inherited_property+'" data-date_of_purchase="'+date_of_purchase+'" data-cost_at_purchase_time="'+cost_at_purchase_time+'"  data-investment_on_buildings="'+investment_on_buildings+'" data-approx_current_market_value="'+approx_current_market_value+'"  data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_residential'+datas.id+'"> <span class="btn btn-info btn-sm"><i class="fa fa-edit"></i> Edit</span> </a>';
				   
				   <?php if(Auth::user()->role_id != '19') { ?>
					
					var del = '<a href="javascript:void(0)" title="Delete" onclick="javascript:delete_residential('+datas.id+')"><span class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Delete</span></a>';
					
					<?php } else { ?>
					var del = '';	
					<?php } ?>

   
                    $('#relatived'+rel_id).prepend('<tr id="tr'+datas.id+'"><td>'+location+'</td><td>'+survey_number+'</td><td>'+area+'</td><td>'+built_up_area+'</td><td>'+display_account+'</td><td>'+inherited_property+'</td><td>'+date_of_purchase+'</td><td>'+cost_at_purchase_time+'</td><td>'+investment_on_buildings+'</td><td>'+approx_current_market_value+'</td><td nowrap="nowrap">'+edit+' '+del+'</td></tr>');
   
                   $("#exampleModal4").modal('hide');
               }
           }
       });
    }
   }
</script>
<script type="text/javascript">
   function delete_residential(id)
   {
    $("#modal_delete_residential_id").val(id);
       $("#deleteResidentialModal").modal('show');
   }
</script>
<script type="text/javascript">
   function delete_residential_entry()
   {
      var id = $("#modal_delete_residential_id").val();
      if(id)
      {
           $.ajax({
               url: "{{ url('delete_residential') }}",
               type: 'GET',
               data: {  id:id },            
               headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
               success:function(data){
               if(data==1)
               {
                   $('#tr'+id).remove();
                   $("#deleteResidentialModal").modal('hide');
               }
               }
           });
      }
   }
</script>
<!-- Residential Land Script End-->
<!-- Other Script Start-->
<script type="text/javascript">
   function save_other_immovable(cand_id, rel_id)
   {
    
    //alert(rel_id);
    
       var brief_details                    = $("#brief_details"+rel_id).val();
       var amount                           = $("#amount"+rel_id).val();
    if(validate("other"+rel_id)){
       $.ajax({
           url: "{{ url('save_other_immovable') }}",
           type: 'GET',
           data: { 
                   cand_id:cand_id, 
                   rel_type_id:rel_id,
                   brief_details:brief_details, 
                   amount:amount
           },            
           headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
           success:function(data){
               if(data!=0)
               {
                   datas = JSON.parse(data);
   
                   var edit = '<a href="javascript:void(0)" title="Edit"onclick="javascript:open_modal5('+datas.id+',{{$data}})"  data-brief_details="'+brief_details+'" data-amount="'+amount+'"   data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_other_immovable'+datas.id+'"> <span class=" btn btn-info btn-sm"><i class="fa fa-edit"></i> Edit</span> </a>';
                   var del = '<a href="javascript:void(0)" title="Delete" onclick="javascript:delete_other_immovable('+datas.id+')"><span class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Delete</span></a>';
   
                    $('#relativee'+rel_id).prepend('<tr id="tr'+datas.id+'"><td>'+brief_details+'</td><td>'+amount+'</td><td>'+edit+' '+del+'</td></tr>');
                 
                   $("#brief_details"+rel_id).val('');
                   $("#amount"+rel_id).val('');
               }
           }
       });
    }
   }
</script>
<script type="text/javascript">
   function open_modal5(id, datas)
   {
       var brief_details = "";
       var amount =  "";
   
       brief_details = $("#edit_other_immovable"+id).data("brief_details");
       amount = $("#edit_other_immovable"+id).data("amount");
       relation_type_id = $("#edit_other_immovable"+id).data("relation_type_id");
       candidate_id = $("#edit_other_immovable"+id).data("candidate_id");
   
       var count = Object.keys(datas).length;
       var all = '';
       
       
   
       $("#modal5_brief_details").val(brief_details);
       $("#modal5_amount").val(amount);
       $("#modal5_rel_id").val(relation_type_id);
       $("#modal5_cand_id").val(candidate_id);
       $("#modal5_other_immovable_id").val(id);
       $("#exampleModal5").modal('show');
   }
</script>
<script type="text/javascript">
   function update_other_immovable()
   {
    //alert(12);
   
       var brief_details                = $("#modal5_brief_details").val();
       var amount                       = $("#modal5_amount").val();
       var id                           = $("#modal5_other_immovable_id").val();
    var rel_id                      = $("#modal5_rel_id").val();
       var cand_id                  = $("#modal5_cand_id").val();
       if(validate("model_other")){
       $.ajax({
           url: "{{ url($menu_action.'update_other_immovable') }}",
           type: 'GET',
           data: { 
                   id:id, 
                   cand_id:cand_id, 
                   rel_type_id:rel_id,
                   brief_details:brief_details, 
                   amount:amount
           },            
           headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
           success:function(data){
               if(data!=0)
               {
                   datas = JSON.parse(data);
                
                $('#relativee'+rel_id+' #tr'+id).remove();
                   
                   var edit = '<a href="javascript:void(0)" title="Edit"onclick="javascript:open_modal5('+datas.id+',{{$data}})"  data-brief_details="'+brief_details+'" data-amount="'+amount+'"   data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_other_immovable'+datas.id+'"> <span class=" btn btn-info mr-1"><i class="fa fa-edit"></i> Edit</span> </a>';
				   
				   <?php if(Auth::user()->role_id != '19') { ?>
					
					var del = '<a href="javascript:void(0)" title="Delete" onclick="javascript:delete_other_immovable('+datas.id+')"><span class=" btn btn-info mr-1"><i class="fa fa-times"></i> Delete</span></a>';
					
					<?php } else { ?>
					var del = '';	
					<?php } ?>
                   
   
                    $('#relativee'+rel_id).prepend('<tr id="tr'+datas.id+'"><td>'+brief_details+'</td><td>'+amount+'</td><td>'+edit+' '+del+'</td></tr>');
   
                   $("#exampleModal5").modal('hide');
               }
           }
       });
    }
   }
</script>
<script type="text/javascript">
   function delete_other_immovable(id)
   {
    $("#modal_delete_other_immovable_id").val(id);
       $("#deleteOtherImmovableModal").modal('show');
   }
</script>
<script type="text/javascript">
   function delete_other_immovable_entry()
   {
      var id = $("#modal_delete_other_immovable_id").val();
      
      if(id)
      {
           $.ajax({
               url: "{{ url('delete_other_immovable') }}",
               type: 'GET',
               data: {  id:id },            
               headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
               success:function(data){
               if(data==1)
               {
                   $('#tr'+id).remove();
                   $("#deleteOtherImmovableModal").modal('hide');
               }
               }
           });
      }
   }
</script>
<!-- Other Script End-->

<!-- validation -->
<script type="text/javascript">
function validate(formval)
{
    if(formval)
    {
        var result = true;
        $('#'+formval+' :input').each(function()
        {
            if($(this).prop('required')) 
            {
                var value = $(this).val();
                var id = $(this).attr('id');
                $("#span_"+id).remove();
                
				if(!value || value=='' || value.length==0 || value <= 0)
                {                  
                    $('#'+id).after('<span class="err" id="span_'+id+'">{{Lang::get("affidavit.this_field_is_required") }}</span>');      
                    $('#'+formval).css("border-color", "solid 1px red");          
                    result =  false;
                }
            }
        });
		
        return result;
		
		
    }
}
   
   
   
       $(document).ready(function() {            
           $(".accordion_head").click(function() {               
             if ($('.accordion_body').is(':visible')) {
                $(".accordion_body").slideUp(500);
                $(".plusminus").text('+');                                
             }
             if ($(this).next(".accordion_body").is(':visible')) {
               $(this).next(".accordion_body").slideUp(500);
               $(this).children(".plusminus").text('+');                
             } else {
               $(this).next(".accordion_body").slideDown(500);
               $(this).children(".plusminus").text('-');               
             }
           });
         }); 
</script>
<script>
function change_required(id)
{
	$("#span_date_of_purchase"+id).remove();
	$("#span_cost_at_purchase_time"+id).remove();
	var is_yes = $("#inherited_property"+id).val();
	if(is_yes=="Yes")
	{
		$("#date_of_purchase"+id).removeAttr('required');
		$("#cost_at_purchase_time"+id).removeAttr('required');		
	}
	else
	{
		$("#date_of_purchase"+id).attr('required','required');
		$("#cost_at_purchase_time"+id).attr('required','required');	
	}
}
function change_required2(id)
{
	$("#span_date_of_purchase2"+id).remove();
	$("#span_cost_at_purchase_time2"+id).remove();
	var is_yes = $("#inherited_property2"+id).val();
	if(is_yes=="Yes")
	{
		$("#date_of_purchase2"+id).removeAttr('required');
		$("#cost_at_purchase_time2"+id).removeAttr('required');		
	}
	else
	{
		$("#date_of_purchase2"+id).attr('required','required');
		$("#cost_at_purchase_time2"+id).attr('required','required');	
	}
}
function change_required3(id)
{
	$("#span_date_of_purchase3"+id).remove();
	$("#span_cost_at_purchase_time3"+id).remove();
	var is_yes = $("#inherited_property3"+id).val();
	if(is_yes=="Yes")
	{
		$("#date_of_purchase3"+id).removeAttr('required');
		$("#cost_at_purchase_time3"+id).removeAttr('required');		
	}
	else
	{
		$("#date_of_purchase3"+id).attr('required','required');
		$("#cost_at_purchase_time3"+id).attr('required','required');	
	}
}
function change_required4(id)
{
	$("#span_date_of_purchase4"+id).remove();
	$("#span_cost_at_purchase_time4"+id).remove();
	var is_yes = $("#inherited_property4"+id).val();
	if(is_yes=="Yes")
	{
		$("#date_of_purchase4"+id).removeAttr('required');
		$("#cost_at_purchase_time4"+id).removeAttr('required');		
	}
	else
	{
		$("#date_of_purchase4"+id).attr('required','required');
		$("#cost_at_purchase_time4"+id).attr('required','required');	
	}
}

</script>
<!-- validation -->
@endsection