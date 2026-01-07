@extends( (Auth::user()->role_id != '19') ? 'layouts.theme' : 'admin.layouts.ac.theme')
@section('title', 'Affidavit Cadidate Details') @section('content')
<link rel="stylesheet" href="{{ asset('appoinment/css/custom-dark.css') }} " type="text/css" />
<link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap-multiselect.css') }} " type="text/css" />
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
.more-less {
    float: right;
    color: #212121;
}
.width100{
width: 100px !important;
}
.err
{
    white-space: pre;
    color: red;
    font-size: 11px;
    font-weight: 600;
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
.accordion_head .lefts{
    width: calc(100% - 55px);
    float: left;
}
 .accordion_head .rights{
    width: 50px;
    float: right;
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
.step-wrap>ul>li>b {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    font-size: 1.5rem;
    text-align: center;
    background-color: #ffffff;
    color: #e8e8e8;
    display: inline-block;
    line-height: 35px;
    vertical-align: middle;
    margin-right: 0.25rem;
    margin-left: 0; 
}
</style>
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

<div class="step-wrap mt-4">
            <ul class="affidavit_nav">
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'affidavitdashboard')}}">{{Lang::get('affidavit.initial_details') }}</a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'affidavit/candidatedetails')}}">{{Lang::get('affidavit.candidate_details') }}</a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'affidavit/pending-criminal-cases')}}">{{Lang::get('affidavit.court_cases') }}</a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'Affidavit/MovableAssets')}}">{{Lang::get('affidavit.movable_assets') }}</a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'immovable-assets')}}">{{Lang::get('affidavit.immovable_assets') }}</a></span></li>
                <li class="step-current"><b>&#10004;</b><span><a href="{{url($menu_action.'liabilities')}}">{{Lang::get('affidavit.liabilities') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'Profession')}}">{{Lang::get('affidavit.profession') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'education')}}">{{Lang::get('affidavit.education')}}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'preview')}}">{{Lang::get('affidavit.preview_finalize') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'part-a-detailed-report')}}">{{Lang::get('affidavit.reports') }}</a></span></li>
            </ul>
        </div>
<section>
<div class="container p-0">
    <div class="row">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="main_heading">{{Lang::get('affidavit.liabilities') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Institution  -->
                    <div class="accordion_head">{{Lang::get('affidavit.loan_or_dues_to_bank_financial_institution') }}<span class="plusminus">+</span></div>
                    <div class="accordion_body" style="display: none"> 
                        @if(!empty($data))
                        @foreach($data as $loan)
                           <h6 class="text-left pt-2 py-3 text-uppercase">
                            {{$loan->relation_type}} : {{$loan->name}}
                            </h6>                            
                            <table id="loan_relative{{$loan->relation_type_code}}" class="table table-striped table-bordered table-hover purpleTable" >
                            <thead>
                                <tr>
                                    <th>{{Lang::get('affidavit.name_of_bank_or_financial_institution') }}</th>
                                    <th>{{Lang::get('affidavit.nature_of_loan') }}</th>
                                    <th>{{Lang::get('affidavit.loan_account_type') }}</th>
                                    <th>{{Lang::get('affidavit.amount_outstanding') }}(in &#x20b9;)</th>
                                    <th>{{Lang::get('affidavit.action') }}</th>          
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($loan_details))
                                    @foreach($loan_details as $debt)
                                    @if($debt->relation_type_code==$loan->relation_type_code)
                                    <tr id="trloan{{$debt->id}}">                                    
                                        <td>{{$debt->bank_inst_name}}</td>    
                                        <td>{{$debt->loan_type}}<br>
                                            @if(!empty($debt->other_loan_type))
                                                    {{$debt->other_loan_type}}
                                                @endif
                                        </td>    
                                        <td>{{$debt->loan_account_type}}
                                            @if($debt->loan_account_type=="Joint")
                                                {{$debt->joint_account_with_name}}
                                            @endif
                                        </td>                                
                                        <td>{{$debt->outstanding_amount}}</td>     
                                        <td nowrap="nowrap">
                                            <a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get('affidavit.edit') }}" onclick="javascript:edit_loan({{$debt->id}},{{$data}})"
                                            data-loan_type_id="{{$debt->loan_type_id}}"
                                            data-loan_type_other="{{$debt->other_loan_type}}"
                                            data-account_type="{{$debt->loan_account_type}}"data-joint_account_with="{{$debt->joint_account_with}}" data-loan_to="{{$debt->bank_inst_name}}" data-nature_of_loan="{{$debt->nature_of_loan}}"
                                            data-amount="{{$debt->outstanding_amount}}"
                                            data-joint_other_name="{{$debt->joint_other_name}}"
                                            data-relation_type_id="{{$debt->relation_type_code}}"
                                            data-candidate_id="{{$debt->candidate_id}}"
                                            id="edit_loan{{$debt->id}}">
                                        <i class="fa fa-edit"></i>{{Lang::get('affidavit.edit') }}</a>
										@if(Auth::user()->role_id != '19')
											<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get('affidavit.delete') }}" onclick="javascript:delete_loan({{$debt->id}})">
											<i class="fa fa-times"></i>{{Lang::get('affidavit.delete') }}</a> 
										@endif
                                    </tr>
                                    @endif
                                    @endforeach                                                        
                                @endif
								
								@if(Auth::user()->role_id != '19')
                                <form>
                                <tr id="loan_form{{$loan->relation_type_code}}">    
                                    <td>
                                        <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="loan_to{{$loan->relation_type_code}}" id="loan_to{{$loan->relation_type_code}}" required="required"></textarea>
                                    </td>                                                  
                                    <td>
                                        <select class="form-control" name="loan_type{{$loan->relation_type_code}}" id="loan_type{{$loan->relation_type_code}}" onchange="javascript:get_loan_type({{$loan->relation_type_code}});"  required="required">
                                            <option value="">{{Lang::get('affidavit.select') }}</option>
                                            @if($loan_type)
                                                @foreach($loan_type as $loan_type_row)
                                                    <option value="{{$loan_type_row->loan_type_id}}">{{$loan_type_row->loan_type}}-{{$loan_type_row->loan_type_hi}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div id="loan_type_div{{$loan->relation_type_code}}" style="display: none;">
                                            <small>{{Lang::get('affidavit.other') }}</small><br>
                                            <textarea col="10" row="5" class="form-control" name="loan_type_other{{$loan->relation_type_code}}" id="loan_type_other{{$loan->relation_type_code}}">
                                            </textarea>
                                        </div>
                                    </td>
                                    <td>
                                        <select class="form-control " name="loan_account_type{{$loan->relation_type_code}}" id="loan_account_type{{$loan->relation_type_code}}" onchange="javascript:get_loan_relatives({{$loan->relation_type_code}});" required="required" >
                                            <option value="">{{Lang::get('affidavit.select') }}</option>
                                            <option value="Individual">{{Lang::get('affidavit.individual') }}</option>
                                            <option value="Joint">{{Lang::get('affidavit.joint') }}</option>
                                        </select>
                                        <br>
                                        <div id="joint_loan_div{{$loan->relation_type_code}}" style="display: none;">
                                            <select class="form-control selectOne" multiple="multiple"  name="loan_joint_account_with{{$loan->relation_type_code}}[]" id="loan_joint_account_with{{$loan->relation_type_code}}">
                                                @if($data)
                                                    @foreach($data as $rel)
                                                    @if($loan->relation_type_code!=$rel->relation_type_code)
                                                        <option value="{{$rel->relation_type_code}}-{{$rel->name}}">{{$rel->name}}</option>
                                                    @endif
                                                    @endforeach
                                                @endif
                                            </select><br>
                                            <small>{{Lang::get('affidavit.other_joint') }}</small><br>
                                            <textarea col="10" row="5" class="form-control" name="loan_joint_account_with_name{{$loan->relation_type_code}}" id="loan_joint_account_with_name{{$loan->relation_type_code}}">
                                            </textarea>
                                        </div>
                                    </td> 
                                    <td>
                                        <input type="text" name="loan_amount{{$loan->relation_type_code}}" id="loan_amount{{$loan->relation_type_code}}"  onkeydown="return NumbersOnly(event,this)" class="form-control" maxlength="15" required="required">
                                    </td>
                                    <td nowrap="nowrap"> 
                                        <a href="javascript:void(0)" class="btn btn-success btn-sm" title=" {{Lang::get('affidavit.save') }}" onclick="javascript:save_loans({{$loan->candidate_id}}, {{$loan->relation_type_code}} )"><i class="fa fa-check"></i> {{Lang::get('affidavit.save') }}</a>
                                    </td>
                                </tr>
                            </form>
							
							@endif
							
                            </tbody>
                            </table>
                        
                        @endforeach
                        @endif
                    </div>    
                    <!-- Institution  -->
                    <!-- Individuals/entity  -->
                    <div class="accordion_head">{{Lang::get('affidavit.loan_or_dues_to_any_other_individuals_entity') }}<span class="plusminus">+</span></div>
                    <div class="accordion_body" style="display: none">
                        @if(!empty($data))
                        @foreach($data as $indi_loan)
                            <h6 class="text-left pt-2 py-3 text-uppercase">
                            {{$indi_loan->relation_type}} : {{$indi_loan->name}}
                            </h6>
                            <table id="indi_loan_relative{{$indi_loan->relation_type_code}}" class="table table-striped table-bordered table-hover purpleTable" >
                            <thead>
                                <tr>
                                    <th>{{Lang::get('affidavit.name_of_individual_entity') }}</th>
                                    <th>{{Lang::get('affidavit.nature_of_loan') }}</th>
                                    <th>{{Lang::get('affidavit.loan_account_type') }}</th>
                                    <th>{{Lang::get('affidavit.amount_outstanding') }}(in &#x20b9;)</th>
                                    <th>{{Lang::get('affidavit.action') }}</th>          
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($indi_loan_details))
                                    @foreach($indi_loan_details as $indi_debt)
                                    @if($indi_debt->relation_type_code==$indi_loan->relation_type_code)
                                    <tr id="trindi_loan{{$indi_debt->id}}">                                    
                                        <td>{{$indi_debt->individual_entity_name}}</td>    
                                        <td>{{$indi_debt->loan_type}}<br>
                                            @if(!empty($indi_debt->other_loan_type))
                                                    {{$indi_debt->other_loan_type}}
                                                @endif
                                        </td>    
                                        <td>{{$indi_debt->loan_account_type}}
                                            @if($indi_debt->loan_account_type=="Joint")
                                                {{$indi_debt->joint_account_with_name}}
                                            @endif
                                        </td>                                
                                        <td>{{$indi_debt->outstanding_amount}}</td>     
                                        <td nowrap="nowrap">
                                            <a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get('affidavit.edit') }}" onclick="javascript:edit_indi_loan({{$indi_debt->id}},{{$data}})"
                                            data-loan_type_id="{{$indi_debt->loan_type_id}}"
                                            data-loan_type_other="{{$indi_debt->other_loan_type}}"
                                            data-account_type="{{$indi_debt->loan_account_type}}"data-joint_account_with="{{$indi_debt->joint_account_with}}" data-loan_to="{{$indi_debt->individual_entity_name}}" 
                                            data-amount="{{$indi_debt->outstanding_amount}}"
                                            data-joint_other_name="{{$indi_debt->joint_other_name}}"
                                            data-relation_type_id="{{$indi_debt->relation_type_code}}"
                                            data-candidate_id="{{$indi_debt->candidate_id}}"
                                            id="edit_indi_loan{{$indi_debt->id}}">
                                        <i class="fa fa-edit"></i> {{Lang::get('affidavit.edit') }}</a>
										@if(Auth::user()->role_id != '19')
											<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get('affidavit.action') }}" onclick="javascript:delete_indi_loan({{$indi_debt->id}})">
											<i class="fa fa-times"></i>{{Lang::get('affidavit.delete') }}</a>
										@endif
                                    </tr>
                                    @endif
                                    @endforeach                                                        
                                @endif
								
								@if(Auth::user()->role_id != '19')
                                <form>
                                <tr id="indi_loan_form{{$indi_loan->relation_type_code}}">
                                    <td>
                                        <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="indi_loan_to{{$indi_loan->relation_type_code}}" id="indi_loan_to{{$indi_loan->relation_type_code}}" required="required"></textarea>
                                    </td>                                                  
                                    <td>
                                        <select class="form-control" name="indi_loan_type{{$indi_loan->relation_type_code}}" id="indi_loan_type{{$indi_loan->relation_type_code}}" onchange="javascript:get_indi_loan_type({{$indi_loan->relation_type_code}});" required="required">
                                            <option value="">{{Lang::get('affidavit.select') }}</option>
                                            @if($loan_type)
                                                @foreach($loan_type as $loan_type_row)
                                                    <option value="{{$loan_type_row->loan_type_id}}">{{$loan_type_row->loan_type}}-{{$loan_type_row->loan_type_hi}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div id="indi_loan_type_div{{$indi_loan->relation_type_code}}" style="display: none;">
                                            <small>{{Lang::get('affidavit.other') }}</small><br>
                                            <textarea col="10" row="5" class="form-control" name="indi_loan_type_other{{$indi_loan->relation_type_code}}" id="indi_loan_type_other{{$indi_loan->relation_type_code}}">
                                            </textarea>
                                        </div>
                                    </td>
                                    <td>
                                        <select class="form-control" name="indi_loan_account_type{{$indi_loan->relation_type_code}}" id="indi_loan_account_type{{$indi_loan->relation_type_code}}" onchange="javascript:get_indi_loan_relatives({{$indi_loan->relation_type_code}});" required="required">
                                            <option value="">{{Lang::get('affidavit.select') }}</option>
                                            <option value="Individual">{{Lang::get('affidavit.individual') }}</option>
                                            <option value="Joint">{{Lang::get('affidavit.joint') }}</option>
                                        </select>
                                        <br>
                                        <div id="indi_joint_loan_div{{$indi_loan->relation_type_code}}" style="display: none;">
                                            <select class="form-control selectOne" name="indi_loan_joint_account_with{{$indi_loan->relation_type_code}}[]" id="indi_loan_joint_account_with{{$indi_loan->relation_type_code}}" multiple>
                                                @if($data)
                                                    @foreach($data as $rel)
                                                    @if($indi_loan->relation_type_code!=$rel->relation_type_code)
                                                        <option value="{{$rel->relation_type_code}}-{{$rel->name}}">{{$rel->name}}</option>
                                                    @endif
                                                    @endforeach
                                                @endif
                                            </select><br>
                                            <small>{{Lang::get('affidavit.other_joint') }}</small><br>
                                            <textarea col="10" row="5" class="form-control" name="indi_loan_joint_account_with_name{{$indi_loan->relation_type_code}}" id="indi_loan_joint_account_with_name{{$indi_loan->relation_type_code}}">
                                            </textarea>
                                        </div>
                                    </td> 
                                    <td>
                                        <input type="text" name="indi_loan_amount{{$indi_loan->relation_type_code}}" id="indi_loan_amount{{$indi_loan->relation_type_code}}"  onkeydown="return NumbersOnly(event,this)" class="form-control" maxlength="15" required="required">
                                    </td>
                                    <td> 
                                        <a href="javascript:void(0)" class="btn btn-success btn-sm" title="{{Lang::get('affidavit.save') }}" onclick="javascript:save_indi_loans({{$indi_loan->candidate_id}}, {{$indi_loan->relation_type_code}} )"><i class="fa fa-check"></i>{{Lang::get('affidavit.save') }}</a>
                                    </td>
                                </tr>
                            </form>
							@endif
							
                            </tbody>
                            </table>
                        @endforeach
                        @endif    
                    </div> 
                    <!--  Individuals/entity -->
                    <!-- ---------------Government Dues------------ -->
                    <div class="accordion_head">{{Lang::get('affidavit.government_dues') }}<span class="plusminus">+</span></div>
                    <div class="accordion_body" style="display: none"> 
                        @if(!empty($data))
                        @foreach($data as $govt_row)
                            <h6 class="text-left pt-2 py-3 text-uppercase">
                            {{$govt_row->relation_type}} : {{$govt_row->name}}
                            </h6>
                            <div class="table-responsive">
                            <table id="govt_dues_relative{{$govt_row->relation_type_code}}" class="table table-striped table-bordered table-hover purpleTable">
                            <thead>
                                <tr>
                                    <th>{{Lang::get('affidavit.government_department_name') }}</th>
                                    <th>{{Lang::get('affidavit.due_details') }}</th>
                                    <th>{{Lang::get('affidavit.amount') }} (in &#x20b9;)</th>
                                    <th>{{Lang::get('affidavit.action') }}</th>          
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($govt_dues))
                                    @foreach($govt_dues as $govt_due)
                                    @if($govt_due->relation_type_code==$govt_row->relation_type_code)
                                    <tr id="trgovt_dues{{$govt_due->id}}">                 
                                        <td>{{$govt_due->govt_dept_name}}<br>
                                            @if(!empty($govt_due->other_dept))
                                                    {{$govt_due->other_dept}}
                                                @endif
                                        </td>    
                                        <td>
                                            @if($govt_due->govt_dept_name_code==1)
                                                @if($govt_due->is_government_accomodation==0)
                                                    {{Lang::get('affidavit.no') }}
                                                @else
                                                    <label>{{Lang::get('affidavit.address_of_the_government_accommodation') }}:</label><br>
                                                     <strong>{{$govt_due->government_accomodation_address}}</strong>
                                                      <label>{{Lang::get('affidavit.there_is_no_dues_payable') }}</label>
                                                    <ol type="A">
                                                      <li>{{Lang::get('affidavit.rent') }}</li>
                                                      <li>{{Lang::get('affidavit.electricity_charges') }}</li>
                                                      <li>{{Lang::get('affidavit.water_charges') }}</li>
                                                      <li>{{Lang::get('affidavit.telephone_charges_as_on') }} : <strong>{{\Carbon\Carbon::parse($govt_due->telephone_charges)->format('d/m/Y')}}</strong><br>
                                                        @if(!empty($govt_due->no_dues_file))
                                                        <label>{{Lang::get('affidavit.no_dues_file') }}:</label> 
                                                        <a href="{{ url('/').'/affidavit/uploads/govt_dues_liabitilies/'.$govt_due->no_dues_file}}" target="_new">{{Lang::get('affidavit.click_here_to_open_the_file') }}</a>
                                                        @endif </li>
                                                    </ol>
                                                @endif
                                            @else
                                                {{$govt_due->due_details}}
                                            @endif</td>                                
                                        <td>{{$govt_due->amount}}</td>     
                                        <td nowrap="nowrap">
                                            <a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get('affidavit.edit') }}" onclick="javascript:edit_govt_dues({{$govt_due->id}})"
                                            data-govt_dept_name_code="{{$govt_due->govt_dept_name_code}}"
                                            data-other_dept="{{$govt_due->other_dept}}"
                                            data-due_details="{{$govt_due->due_details}}"
                                            data-amount="{{$govt_due->amount}}"
                                            data-is_government_accomodation="{{$govt_due->is_government_accomodation}}"
                                            data-government_accomodation_address="{{$govt_due->government_accomodation_address}}"
                                            data-telephone_charges="{{$govt_due->telephone_charges}}"
                                            data-relation_type_id="{{$govt_due->relation_type_code}}"
                                            data-candidate_id="{{$govt_due->candidate_id}}"
                                            id="edit_govt_dues{{$govt_due->id}}">
                                        <i class="fa fa-edit"></i> {{Lang::get('affidavit.edit') }}</a>
										
										@if(Auth::user()->role_id != '19')
											<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get('affidavit.delete') }}" onclick="javascript:delete_govt_dues({{$govt_due->id}})">
											<i class="fa fa-times"></i> {{Lang::get('affidavit.delete') }}</a> 
										@endif
                                    </tr>
                                    @endif
                                    @endforeach                                                        
                                @endif
								
								@if(Auth::user()->role_id != '19')
                                <form>
                                <tr id="govt_dues_form{{$govt_row->relation_type_code}}">
                                    <td>
                                        <select class="form-control" name="govt_dept_name_code{{$govt_row->relation_type_code}}" id="govt_dept_name_code{{$govt_row->relation_type_code}}" onchange="javascript:get_govt_dept_type({{$govt_row->relation_type_code}});" required="required">
                                            <option value="">{{Lang::get('affidavit.select') }}</option>
                                            @if($govt_dept)
                                                @foreach($govt_dept as $govt_dept_row)
                                                    <option value="{{$govt_dept_row->govt_dept_name_code}}">{{$govt_dept_row->govt_dept_name}}-{{$govt_dept_row->govt_dept_name_hi}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div id="govt_dept_div{{$govt_row->relation_type_code}}" style="display: none;">
                                            <small>{{Lang::get('affidavit.other') }}</small><br>
                                            <textarea col="10" row="5" class="form-control" name="other_dept{{$govt_row->relation_type_code}}" id="other_dept{{$govt_row->relation_type_code}}">
                                            </textarea>
                                        </div>
                                    </td>
                                    <td>
                                        <div id="govt_dept_due_details_div{{$govt_row->relation_type_code}}">
                                            <textarea col="10" row="5" class="form-control" class="form-control" name="due_details{{$govt_row->relation_type_code}}" id="due_details{{$govt_row->relation_type_code}}" required="required"></textarea>
                                        </div>
                                        <div id="govt_dept_due_details_radio_div{{$govt_row->relation_type_code}}" style="display: none;">
                                            <small >{{Lang::get('affidavit.has_the_deponent_been') }}</small><br>
                                            <label class="radio-inline">
                                            <input type="radio" name="is_government_accomodation{{$govt_row->relation_type_code}}" id="is_government_accomodation_yes{{$govt_row->relation_type_code}}" value="1" onclick="javascript:radio_click({{$govt_row->relation_type_code}}, 1)"> {{Lang::get('affidavit.yes') }}
                                            </label>
                                            <label class="radio-inline">
                                            <input type="radio" name="is_government_accomodation{{$govt_row->relation_type_code}}" id="is_government_accomodation_no{{$govt_row->relation_type_code}}" value="0" onclick="javascript:radio_click({{$govt_row->relation_type_code}}, 0)" checked> {{Lang::get('affidavit.no') }}
                                            </label>
                                        </div>
                                        <div id="is_government_accomodation_div{{$govt_row->relation_type_code}}" style="display: none;">
                                            <label>{{Lang::get('affidavit.address_of_the_government_accommodation') }}</label>
                                            <textarea col="10" row="5" class="form-control" class="form-control" name="government_accomodation_address{{$govt_row->relation_type_code}}" id="government_accomodation_address{{$govt_row->relation_type_code}}"></textarea>
                                            <label>{{Lang::get('affidavit.there_is_no_dues_payable') }}</label>
                                            <ol type="A">
                                              <li>{{Lang::get('affidavit.rent') }}</li>
                                              <li>{{Lang::get('affidavit.electricity_charges') }}</li>
                                              <li>{{Lang::get('affidavit.water_charges') }}</li>
                                              <li>{{Lang::get('affidavit.telephone_charges_as_on') }} <input type="text" class="form-control datepicker" name="telephone_charges{{$govt_row->relation_type_code}}" id="telephone_charges{{$govt_row->relation_type_code}}" placeholder="Select Date" >
                                                <small>{{Lang::get('affidavit.note_the_date_should') }}</small><br>
                                                <label>{{Lang::get('affidavit.upload_no_dues_certificate') }}</label><br>
                                                <input type="file" class="form-control" name="no_dues_file{{$govt_row->relation_type_code}}" id="no_dues_file{{$govt_row->relation_type_code}}" accept=".pdf">
                                                <span id="errfilesize{{$govt_row->relation_type_code}}" style="color: red"></span><br>
                                                <small>{{Lang::get('affidavit.note_no_dues_certificate') }}</small>
                                            </li>
                                            </ol>
                                        </div>
                                    </td> 
                                    <td>
                                        <input type="text" name="govt_due_amount{{$govt_row->relation_type_code}}" id="govt_due_amount{{$govt_row->relation_type_code}}"  onkeydown="return NumbersOnly(event,this)" class="form-control" maxlength="15" required="required">
                                    </td>
                                    <td nowrap="nowrap"> 
                                        <a class="btn btn-success btn-sm" href="javascript:void(0)" title="{{Lang::get('affidavit.save') }}"  onclick="javascript:save_govt_due({{$govt_row->candidate_id}}, {{$govt_row->relation_type_code}} )" >
                                        <i class="fa fa-check"></i>{{Lang::get('affidavit.save') }}</a>
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
                    <!-- ---------------Government Dues------------ -->

                    <!-- Any other liabilities  -->
                    <div class="accordion_head">{{Lang::get('affidavit.any_other_liabilities') }}<span class="plusminus">+</span></div>
                    <div class="accordion_body" style="display: none"> 
                        @if(!empty($data))
                        @foreach($data as $other)
                            <h6 class="text-left pt-2 py-3 text-uppercase">
                            {{$other->relation_type}} : {{$other->name}}
                            </h6>
                            <div class="table-responsive">
                            <table id="other_relative{{$other->relation_type_code}}" class="table table-striped table-bordered table-hover purpleTable">
                            <thead>
                                <tr>
                                    <th>{{Lang::get('affidavit.authority_name') }}</th>
                                    <th>{{Lang::get('affidavit.brief_details') }}</th>
                                    <th>{{Lang::get('affidavit.amount') }} (in &#x20b9;)</th>
                                    <th>{{Lang::get('affidavit.action') }}</th>          
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($other_details))
                                    @foreach($other_details as $other_row)
                                    @if($other_row->relation_type_code==$other->relation_type_code)
                                    <tr id="trother{{$other_row->id}}">    
                                        <td>{{$other_row->authority_name}}</td>    
                                        <td>{{$other_row->details}}</td>   
                                        <td>{{$other_row->amount}}</td>     
                                        <td nowrap="nowrap">
                                            <a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get('affidavit.edit') }}" onclick="javascript:edit_other({{$other_row->id}})"
                                            data-asset_type="{{$other_row->authority_name}}"
                                            data-brief_details="{{$other_row->details}}" data-amount="{{$other_row->amount}}"
                                            data-relation_type_id="{{$other_row->relation_type_code}}"
                                            data-candidate_id="{{$other_row->candidate_id}}"
                                            id="edit_other{{$other_row->id}}">
                                        <i class="fa fa-edit"></i> {{Lang::get('affidavit.edit') }}
                                    </a>
									
									@if(Auth::user()->role_id != '19')
										<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get('affidavit.delete') }}" onclick="javascript:delete_other({{$other_row->id}})">
                                        <i class="fa fa-times"></i> {{Lang::get('affidavit.delete') }}
										</a> 
									@endif
									
                                    </tr>
                                    @endif
                                    @endforeach                                                        
                                @endif
								
								@if(Auth::user()->role_id != '19')
                                <form>
                                <tr id="other_form{{$other->relation_type_code}}">  
                                    <td>
                                        <textarea col="10" row="5" class="form-control" name="asset_type{{$other->relation_type_code}}"  onkeypress="return blockSpecialChar_address(event)" id="asset_type{{$other->relation_type_code}}" required="required">
                                        </textarea>
                                    </td>
                                    <td>
                                        <textarea col="10" row="5" class="form-control" name="brief_details{{$other->relation_type_code}}" id="brief_details{{$other->relation_type_code}}"  onkeypress="return blockSpecialChar_address(event)" required="required">
                                        </textarea>
                                    </td>
                                    <td>
                                        <input type="text" name="other_amount{{$other->relation_type_code}}" id="other_amount{{$other->relation_type_code}}" onkeydown="return NumbersOnly(event,this)" class="form-control" maxlength="15" required="required">
                                    </td>
                                    <td nowrap="nowrap"> 
                                        <a href="javascript:void(0)" class="btn btn-success btn-sm" title="{{Lang::get('affidavit.save') }}" onclick="javascript:save_other({{$other->candidate_id}}, {{$other->relation_type_code}} )" >
                                        <i class="fa fa-check"></i> {{Lang::get('affidavit.save') }}</a>
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
                    <!--  Any other liabilities  -->

                    <!--  Any other liabilities under Dispute  -->
                    <div class="accordion_head">{{Lang::get('affidavit.any_other_liabilities_under_dispute') }}<span class="plusminus">+</span></div>
                    <div class="accordion_body" style="display: none">
                        @if(!empty($data))
                        @foreach($data as $other_dis)
                            <h6 class="text-left pt-2 py-3 text-uppercase">
                            {{$other->relation_type}} : {{$other_dis->name}}
                            </h6>
                            <div class="table-responsive">
                            <table id="other_relative_dis{{$other_dis->relation_type_code}}" class="table table-striped table-bordered table-hover purpleTable">
                            <thead>
                                <tr>
                                    <th>{{Lang::get('affidavit.authority_name') }}</th>
                                    <th>{{Lang::get('affidavit.brief_details') }}</th>
                                    <th>{{Lang::get('affidavit.amount') }} (in &#x20b9;)</th>
                                    <th>{{Lang::get('affidavit.action') }}</th>          
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($other_disputes))
                                    @foreach($other_disputes as $other_row_dis)
                                    @if($other_row_dis->relation_type_code==$other_dis->relation_type_code)
                                    <tr id="trother_dis{{$other_row_dis->id}}">    
                                        <td>{{$other_row_dis->authority_name}}</td>    
                                        <td>{{$other_row_dis->details}}</td>   
                                        <td>{{$other_row_dis->amount}}</td>     
                                        <td nowrap="nowrap">
                                            <a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get('affidavit.edit') }}"  onclick="javascript:edit_other_dis({{$other_row_dis->id}})"
                                            data-asset_type="{{$other_row_dis->authority_name}}"
                                            data-brief_details="{{$other_row_dis->details}}" data-amount="{{$other_row_dis->amount}}"
                                            data-relation_type_id="{{$other_row_dis->relation_type_code}}"
                                            data-candidate_id="{{$other_row_dis->candidate_id}}"
                                            id="edit_other_dis{{$other_row_dis->id}}">
                                        <i class="fa fa-edit"></i> {{Lang::get('affidavit.edit') }}</a>
										@if(Auth::user()->role_id != '19')
											<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get('affidavit.delete') }}" onclick="javascript:delete_other_dis({{$other_row_dis->id}})"><i class="fa fa-times"></i> {{Lang::get('affidavit.delete') }}</a>
										@endif
                                    </tr>
                                    @endif
                                    @endforeach                                                     
                                @endif
								
								@if(Auth::user()->role_id != '19')
                                <form>
                                <tr id="other_form_dis{{$other_dis->relation_type_code}}">
                                    <td>
                                        <textarea col="10" row="5" class="form-control" name="asset_type_dis{{$other_dis->relation_type_code}}" id="asset_type_dis{{$other_dis->relation_type_code}}"  onkeypress="return blockSpecialChar_address(event)" required="required">
                                        </textarea>
                                    </td>
                                    <td>
                                        <textarea col="10" row="5" class="form-control" name="brief_details_dis{{$other_dis->relation_type_code}}" id="brief_details_dis{{$other_dis->relation_type_code}}"  onkeypress="return blockSpecialChar_address(event)" required="required">
                                        </textarea>
                                    </td>
                                    <td>
                                        <input type="text" name="other_amount_dis{{$other_dis->relation_type_code}}" id="other_amount_dis{{$other_dis->relation_type_code}}" onkeydown="return NumbersOnly(event,this)"  class="form-control" maxlength="15" required="required">
                                    </td>
                                    <td nowrap="nowrap"> 
                                        <a href="javascript:void(0)" class="btn btn-success btn-sm" title="Edit" onclick="javascript:save_other_dis({{$other_dis->candidate_id}}, {{$other_dis->relation_type_code}} )">
                                        <i class="fa fa-check"></i>{{Lang::get('affidavit.save') }}</a>
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
                    <!--  Any other liabilities under Dispute  -->
                    
                </div>
                <div class="card-footer footerSection"> 
                    <div class="row">
                        <div class="col-12">
                            <a href="{{url($menu_action.'immovable-assets') }}" class="backBtn float-left">{{Lang::get('affidavit.back') }}</a>
							<a href="{{url($menu_action.'Profession')}}" class="nextBtn float-right">{{Lang::get('affidavit.save') }} &amp; {{Lang::get('affidavit.next') }}</a>
                             <a href="{{url()->previous() }}" class="cencelBtn float-right mr-2">{{Lang::get('affidavit.cancel') }}</a>
                        </div>                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</section>
</main>
<!-- Loan Edit Modal -->
<div class="modal fade" id="loanModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.edit_loan_or_dues_to_bank_financial_institution') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form id="form_loanModal">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{Lang::get('affidavit.name_of_bank_or_financial_institution') }}:</label>
                    <textarea  class="form-control" col="10" row="5" onkeypress="return blockSpecialChar_address(event)" name="modal_loan_to" id="modal_loan_to" required="required"></textarea>
                </div>
            </div>
            <div class="col-md-6">                    
                <div class="form-group">
                    <label>{{Lang::get('affidavit.loan_account_type') }}</label>
                    <select class="form-control" name="modal_loan_account_type" id="modal_loan_account_type" onchange="javascript:get_modal_loan_relatives()" required="required">
                    <option value="">{{Lang::get('affidavit.select') }}</option>
                    <option value="Individual">{{Lang::get('affidavit.individual') }}</option>
                    <option value="Joint">{{Lang::get('affidavit.joint') }}</option>
                    </select>
                </div>
            </div>            
        </div>
        <div class="row">             
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{Lang::get('affidavit.loan_type') }}</label>
                    <select class="form-control" name="modal_loan_type" id="modal_loan_type" onchange="javascript:get_modal_loan_type();" required="required">
                    <option value="">{{Lang::get('affidavit.select') }}</option>
                    @if($loan_type)
                        @foreach($loan_type as $loan_type_row)
                            <option value="{{$loan_type_row->loan_type_id}}">{{$loan_type_row->loan_type}}-{{$loan_type_row->loan_type_hi}}</option>
                        @endforeach
                    @endif
                </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{Lang::get('affidavit.amount') }} (in &#x20b9;)</label>
                    <input  class="form-control" type="text" name="modal_loan_amount" id="modal_loan_amount" onkeydown="return NumbersOnly(event,this)" maxlength="15" required="required">
                </div>
            </div>           
        </div>

        <div id="modal_loan_div" style="display: none;">
            <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                        <label>{{Lang::get('affidavit.other_loan_type') }}</label>
                        <textarea col="10" row="5" class="form-control" name="modal_loan_type_other" id="modal_loan_type_other"></textarea>
                    </div>
                </div>      
            </div>
        </div>

        <div id="modal_loan_account_type_div" style="display: none;">                
            <div class="row">
                <div class="col-md-6">                 
                <div class="form-group">
                    <label>{{Lang::get('affidavit.other_account_type') }}</label>
                    <select class="form-control" name="modal_loan_joint_other[]" id="modal_loan_joint_other" multiple>
                    </select>
                </div>
            </div>
            <div class="col-md-6">                 
                <div class="form-group">
                    <label>{{Lang::get('affidavit.other_joint_holders') }}</label>
                    <textarea col="10" row="5" class="form-control" name="modal_loan_joint_other_name" id="modal_loan_joint_other_name"></textarea>
                </div>
            </div>
            </div>
        </div>

        <input type="hidden" name="modal_loan_cand_id" id="modal_loan_cand_id">
        <input type="hidden" name="modal_loan_rel_id" id="modal_loan_rel_id">
        <input type="hidden" name="modal_loan_loan_id" id="modal_loan_loan_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.close') }}</button>
    <button type="button" class="btn btn-primary" onclick="javascript:update_loan()">{{Lang::get('affidavit.update') }}</button>
    </div>
    </div>
    </div>
</div>
<!-- Loan Edit Modal -->

<!-- Loan Delete Modal -->
<div class="modal fade" id="deleteLoanModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.delete_loan_or_dues_to_bank_financial_institution') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form>
        <h5>{{Lang::get('affidavit.are_you_sure_to_delete_this_entry') }}</h5>
        <input type="hidden" name="modal_delete_loan_id" id="modal_delete_loan_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.no') }}</button>
    <button type="button" class="btn btn-primary" onclick="javascript:delete_loan_entry()">{{Lang::get('affidavit.yes') }}</button>
    </div>
    </div>
    </div>
</div>
<!-- Loan Delete Modal -->

<!-- Individual Loan Edit Modal -->
<div class="modal fade" id="indiLoanModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.edit_loan_or_dues_to_any_other_individuals_entity') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form id="form_indiLoanModal">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{Lang::get('affidavit.name_of_individual_entity') }}:</label>
                    <textarea  class="form-control" col="10" row="5" onkeypress="return blockSpecialChar_address(event)" name="modal_indi_loan_to" id="modal_indi_loan_to" required="required"></textarea>
                </div>
            </div>
            <div class="col-md-6">                    
                <div class="form-group">
                    <label>{{Lang::get('affidavit.loan_account_type') }}</label>
                    <select class="form-control" name="modal_indi_loan_account_type" id="modal_indi_loan_account_type" onchange="javascript:get_modal_indi_loan_relatives()" required="required">
                    <option value="">{{Lang::get('affidavit.select') }}</option>
                    <option value="Individual">{{Lang::get('affidavit.individual') }}</option>
                    <option value="Joint">{{Lang::get('affidavit.joint') }}</option>
                    </select>
                </div>
            </div>            
        </div>
        <div class="row">             
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{Lang::get('affidavit.loan_type') }}</label>
                    <select class="form-control" name="modal_indi_loan_type" id="modal_indi_loan_type" onchange="javascript:get_modal_indi_loan_type();" required="required">
                    <option value="">{{Lang::get('affidavit.select') }}</option>
                    @if($loan_type)
                        @foreach($loan_type as $loan_type_row)
                            <option value="{{$loan_type_row->loan_type_id}}">{{$loan_type_row->loan_type}}-{{$loan_type_row->loan_type_hi}}</option>
                        @endforeach
                    @endif
                </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{Lang::get('affidavit.amount') }} (in &#x20b9;)</label>
                    <input  class="form-control" type="text" name="modal_indi_loan_amount" id="modal_indi_loan_amount" onkeydown="return NumbersOnly(event,this)" maxlength="15" required="required">
                </div>
            </div>           
        </div>

        <div id="modal_indi_loan_div" style="display: none;">
            <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                        <label>{{Lang::get('affidavit.other_loan_type') }}</label>
                        <textarea col="10" row="5" class="form-control" name="modal_indi_loan_type_other" id="modal_indi_loan_type_other" ></textarea>
                    </div>
                </div>      
            </div>
        </div>

        <div id="modal_indi_loan_account_type_div" style="display: none;">                
            <div class="row">
                <div class="col-md-6">                 
                <div class="form-group">
                    <label>{{Lang::get('affidavit.other_account_type') }}</label>
                    <select class="form-control" name="modal_indi_loan_joint_other[]" id="modal_indi_loan_joint_other" multiple>
                    </select>
                </div>
            </div>
            <div class="col-md-6">                 
                <div class="form-group">
                    <label>{{Lang::get('affidavit.other_joint_holders') }}</label>
                    <textarea col="10" row="5" class="form-control" name="modal_indi_loan_joint_other_name" id="modal_indi_loan_joint_other_name"></textarea>
                </div>
            </div>
            </div>
        </div>
        <input type="hidden" name="modal_indi_loan_cand_id" id="modal_indi_loan_cand_id">
        <input type="hidden" name="modal_indi_loan_rel_id" id="modal_indi_loan_rel_id">
        <input type="hidden" name="modal_indi_loan_loan_id" id="modal_indi_loan_loan_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.close') }}</button>
    <button type="button" class="btn btn-primary" onclick="javascript:update_indi_loan();">{{Lang::get('affidavit.update') }}</button>
    </div>
    </div>
    </div>
</div>
<!-- Individual Loan Edit Modal -->

<!-- Loan Delete Modal -->
<div class="modal fade" id="deleteIndiLoanModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.delete_loan_or_dues_to_any_other_individuals_entity') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form>
        <h5>{{Lang::get('affidavit.are_you_sure_to_delete_this_entry') }}</h5>
        <input type="hidden" name="modal_indi_delete_loan_id" id="modal_indi_delete_loan_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.no') }}</button>
    <button type="button" class="btn btn-primary" onclick="javascript:delete_indi_loan_entry()">{{Lang::get('affidavit.yes') }}</button>
    </div>
    </div>
    </div>
</div>
<!-- Loan Delete Modal -->

<!-- Government Dues Edit Modal -->
<div class="modal fade" id="govtDueModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.edit_government_dues') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form id="form_govtDueModal">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{Lang::get('affidavit.government_department_name') }}:</label>
                    <select class="form-control" name="modal_govt_dept_name_code" id="modal_govt_dept_name_code" onchange="javascript:get_modal_govt_dept_type();" required="required">
                    <option value="">{{Lang::get('affidavit.select') }}</option>
                    @if($govt_dept)
                        @foreach($govt_dept as $govt_dept_row)
                            <option value="{{$govt_dept_row->govt_dept_name_code}}">{{$govt_dept_row->govt_dept_name}}-{{$govt_dept_row->govt_dept_name_hi}}</option>
                        @endforeach
                    @endif
                </select>
                </div>
            </div>
            <div class="col-md-6">
                <div id="modal_govt_dept_due_details_div">
                    <div class="form-group">
                        <label>{{Lang::get('affidavit.due_details') }}</label>
                        <textarea col="10" row="5" class="form-control" name="modal_due_details" id="modal_due_details">
                        </textarea>
                    </div>
                </div>
            </div>  
        </div>

        <div class="row"> 
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{Lang::get('affidavit.amount') }} (in &#x20b9;)</label>
                    <input  class="form-control" type="text" name="modal_govt_due_amount" id="modal_govt_due_amount" onkeydown="return NumbersOnly(event,this)" maxlength="15" required="required">
                </div>
            </div>           
        </div>
        <div id="modal_govt_dept_div" style="display: none;"> 
            <div class="row"> 
                <div class="col-md-6">                 
                    <div class="form-group">
                        <label>{{Lang::get('affidavit.other_department_name') }}</label>
                        <textarea col="10" row="5" class="form-control" name="modal_other_dept" id="modal_other_dept"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div id="modal_govt_dept_due_details_radio_div" style="display: none;">
            <div class="row"> 
                <div class="col-md-6">  
                    <small >{{Lang::get('affidavit.has_the_deponent_been') }}</small><br>
                    <label class="radio-inline">
                    <input type="radio" name="modal_is_government_accomodation" id="modal_is_government_accomodation_yes" value="1" onclick="javascript:modal_radio_click(1)"> {{Lang::get('affidavit.yes') }}
                    </label>
                    <label class="radio-inline">
                    <input type="radio" name="modal_is_government_accomodation" id="modal_is_government_accomodation_no" value="0" onclick="javascript:modal_radio_click(0)" checked> {{Lang::get('affidavit.no') }}
                    </label>
                </div>
                <div class="col-md-12" id="modal_is_government_accomodation_div" style="display: none;"> 
                        <label>{{Lang::get('affidavit.address_of_the_government_accommodation') }}</label>
                        <textarea col="10" row="5" class="form-control" class="form-control" name="modal_government_accomodation_address" id="modal_government_accomodation_address"></textarea>
                        <label>{{Lang::get('affidavit.there_is_no_dues_payable') }}</label>
                        <ol type="A">
                          <li>{{Lang::get('affidavit.rent') }}</li>
                          <li>{{Lang::get('affidavit.electricity_charges') }}</li>
                          <li>{{Lang::get('affidavit.water_charges') }}</li>
                          <li>{{Lang::get('affidavit.telephone_charges_as_on') }} <input type="text" class="form-control datepicker" placeholder="Select Date" name="modal_telephone_charges" id="modal_telephone_charges"></li><br>
                            <label>{{Lang::get('affidavit.upload_no_dues_certificate') }}</label><br>
                            <input type="file" name="modal_no_dues_file" id="modal_no_dues_file" accept=".pdf">
                            <span id="modal_errfilesize" style="color: red"></span><br>
                            <small>{{Lang::get('affidavit.note_no_dues_certificate') }}</small>
                        </ol>
                </div>
            </div>
        </div>

        <input type="hidden" name="modal_govt_due_cand_id" id="modal_govt_due_cand_id">
        <input type="hidden" name="modal_govt_due_rel_id" id="modal_govt_due_rel_id">
        <input type="hidden" name="modal_govt_due_id" id="modal_govt_due_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.close') }}</button>
    <button type="button" class="btn btn-primary" onclick="javascript:update_govt_due()">{{Lang::get('affidavit.update') }}</button>
    </div>
    </div>
    </div>
</div>
<!-- Government Dues Edit Modal -->

<!-- Government Dues Delete Modal -->
<div class="modal fade" id="deleteGovtDueModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.delete_government_dues') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form>
        <h5>{{Lang::get('affidavit.are_you_sure_to_delete_this_entry') }}</h5>
        <input type="hidden" name="modal_govt_due_delete_id" id="modal_govt_due_delete_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.no') }}</button>
    <button type="button" class="btn btn-primary" onclick="javascript:delete_govt_dues_entry()">{{Lang::get('affidavit.yes') }}</button>
    </div>
    </div>
    </div>
</div>
<!-- Government Dues Delete Modal -->


<!-- Other Asset Edit Modal -->
<div class="modal fade" id="otherModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.edit_any_other_liabilities') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form id="form_otherModal">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{Lang::get('affidavit.asset_type') }}</label>
                    <textarea col="10" row="5" class="form-control" name="modal_asset_type" id="modal_asset_type" required="required"></textarea>
                </div> 
            </div> 
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{Lang::get('affidavit.brief_details') }}</label>
                    <textarea col="10" row="5" class="form-control" name="modal_brief_details" id="modal_brief_details" required="required"></textarea>
                </div> 
            </div> 
        </div>
        <div class="row"> 
             <div class="col-md-6">
                <div class="form-group">
                    <label>{{Lang::get('affidavit.amount') }} (in &#x20b9;)</label>
                    <input  class="form-control" type="text" name="modal_other_amount" id="modal_other_amount" onkeydown="return NumbersOnly(event,this)" maxlength="15" required="required">
                </div>
            </div>
        </div>
        <input type="hidden" name="modal_other_cand_id" id="modal_other_cand_id">
        <input type="hidden" name="modal_other_rel_id" id="modal_other_rel_id">
        <input type="hidden" name="modal_other_id" id="modal_other_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.close') }}</button>
    <button type="button" class="btn btn-primary" onclick="javascript:update_others()">{{Lang::get('affidavit.update') }}</button>
    </div>
    </div>
    </div>
</div>
<!-- Other Asset Edit Modal -->

<!-- Other Asset Delete Modal -->
<div class="modal fade" id="deleteOtherModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.delete_any_other_liabilities') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form>
        <h5>{{Lang::get('affidavit.are_you_sure_to_delete_this_entry') }}</h5>
        <input type="hidden" name="modal_delete_other_id" id="modal_delete_other_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.no') }}</button>
    <button type="button" class="btn btn-primary" onclick="javascript:delete_other_entry()">{{Lang::get('affidavit.yes') }}</button>
    </div>
    </div>
    </div>
</div>
<!-- Other Asset Delete Modal -->


<!-- Other Liability Dispute Edit Modal -->
<div class="modal fade" id="otherModal_dis" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.edit_any_other_liabilities_under_dispute') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form id="form_otherModal_dis">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{Lang::get('affidavit.asset_type') }}</label>
                    <textarea col="10" row="5" class="form-control" name="modal_asset_type_dis" id="modal_asset_type_dis" required="required"></textarea>
                </div> 
            </div> 
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{Lang::get('affidavit.brief_details') }}</label>
                    <textarea col="10" row="5" class="form-control" name="modal_brief_details_dis" id="modal_brief_details_dis" required="required"></textarea>
                </div> 
            </div> 
        </div>
        <div class="row"> 
             <div class="col-md-6">
                <div class="form-group">
                    <label>{{Lang::get('affidavit.amount') }} (in &#x20b9;)</label>
                    <input  class="form-control" type="text" name="modal_other_amount_dis" id="modal_other_amount_dis" onkeydown="return NumbersOnly(event,this)" maxlength="15" required="required">
                </div>
            </div>
        </div>
        <input type="hidden" name="modal_other_cand_id_dis" id="modal_other_cand_id_dis">
        <input type="hidden" name="modal_other_rel_id_dis" id="modal_other_rel_id_dis">
        <input type="hidden" name="modal_other_id_dis" id="modal_other_id_dis">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.close') }}</button>
    <button type="button" class="btn btn-primary" onclick="javascript:update_others_dis()">{{Lang::get('affidavit.update') }}</button>
    </div>
    </div>
    </div>
</div>
<!-- Other Asset Edit Modal -->

<!-- Other Asset Delete Modal -->
<div class="modal fade" id="deleteOtherModal_dis" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.delete_any_other_liabilities_under_dispute') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form>
        <h5>{{Lang::get('affidavit.are_you_sure_to_delete_this_entry') }}</h5>
        <input type="hidden" name="modal_delete_other_id_dis" id="modal_delete_other_id_dis">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.no') }}</button>
    <button type="button" class="btn btn-primary" onclick="javascript:delete_other_entry_dis()">{{Lang::get('affidavit.yes') }}</button>
    </div>
    </div>
    </div>
</div>
<!-- Other Asset Delete Modal -->

@endsection @section('script')
<!-- <script type="text/javascript" src="{{ asset('admintheme/js/jquery-ui.js') }}"></script> -->
<script type="text/javascript" src="{{ asset('affidavit/js/remove_special_character.js') }}"></script>
<script type="text/javascript" src="{{ asset('affidavit/js/affidavit_validation.js') }}"></script>
<script type="text/javascript" src="{{ asset('affidavit/js/sweetalert.js') }}"></script>
<script type="text/javascript" src="{{ asset('affidavit/js/jquery-ui.js') }}"></script>
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
<script type="text/javascript">
    $(document).ready(function() {  
    $(".datepicker").datepicker({
    dateFormat: 'yy-mm-dd',
    maxDate: 0
    });
    }); 
</script>



<!-- Institution -->
<script type="text/javascript">
function get_loan_type(rel_id)
{
    if(rel_id)
    {
        $("#loan_type_other"+rel_id).val('');
        var loan_type = $("#loan_type"+rel_id).val();
        if(loan_type==5)
        {
            $("#loan_type_div"+rel_id).css("display", "block");
            $("#loan_type_other"+rel_id).attr("required", "required");
        }
        else
        {
            $("#loan_type_div"+rel_id).css("display", "none");
            $("#loan_type_other"+rel_id).removeAttr("required");

        }            
    }
}
</script>
<script type="text/javascript">
function get_loan_relatives(rel_id)
{
    if(rel_id)
    {
        $("#loan_joint_account_with"+rel_id).val('');
        $("#loan_joint_account_with_name"+rel_id).val('');
        var loan_account_type = $("#loan_account_type"+rel_id).val();
        if(loan_account_type=="Joint")
        {
            $("#joint_loan_div"+rel_id).css("display", "block");
            //$("#loan_joint_account_with"+rel_id).attr("required", "required");
            //$("#loan_joint_account_with_name"+rel_id).attr("required", "required");
        }
        else
        {
            $("#joint_loan_div"+rel_id).css("display", "none");
            //$("#loan_joint_account_with"+rel_id).removeAttr("required");
            //$("#loan_joint_account_with_name"+rel_id).removeAttr("required");
        }
    }
}
</script>

<script type="text/javascript">
function save_loans(cand_id, rel_id)
{
    var loan_type = $("#loan_type"+rel_id).val();
    var loan_type_other = $("#loan_type_other"+rel_id).val();
    var loan_type_name =  $("#loan_type"+rel_id+" option:selected").html();
    var loan_account_type = $("#loan_account_type"+rel_id).val();
    var loan_joint_account_with = $("#loan_joint_account_with"+rel_id).val();
    var loan_joint_account_with_name = $("#loan_joint_account_with_name"+rel_id).val();    
    var loan_to = $("#loan_to"+rel_id).val();
    var loan_amount = $("#loan_amount"+rel_id).val();

    if(validate("loan_form"+rel_id))
    {
        $.ajax({
        url: "{{ url('save_loan_bank') }}",
        type: 'GET',
        data: { 
                cand_id:cand_id, 
                rel_type_id:rel_id,
                bank_inst_name:loan_to, 
                loan_type:loan_type, 
                loan_type_other:loan_type_other,
                account_type:loan_account_type,
                joint:loan_joint_account_with,
                joint_other:loan_joint_account_with_name,
                amount:loan_amount
        },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
            if(data!=0)
            {
                 datas = JSON.parse(data);
                 
                if(loan_type==5)
                    loan_type_name = loan_type_name+"<br>"+loan_type_other;
                
                if(loan_account_type=="Joint")
                    var display_account = loan_account_type+" with "+datas.joint_account_with_name;
                else
                     var display_account = loan_account_type;

                if(loan_joint_account_with_name!="")
                    display_account = display_account+","+loan_joint_account_with_name;

                var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get("affidavit.edit") }}" onclick="javascript:edit_loan('+datas.id+',{{$data}})"  data-loan_type_id="'+loan_type+'" data-loan_type_other="'+loan_type_other+'" data-account_type="'+loan_account_type+'" data-joint_account_with="'+datas.joint_account_with+'" data-loan_to="'+loan_to+'" data-amount="'+loan_amount+'" data-joint_other_name="'+loan_joint_account_with_name+'"  data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_loan'+datas.id+'"> <i class="fa fa-edit"></i> {{Lang::get("affidavit.edit") }} </a>';

                var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get("affidavit.delete") }}" onclick="javascript:delete_loan('+datas.id+')"> <i class="fa fa-times"></i> {{Lang::get("affidavit.delete") }}</a>';

                 $('#loan_relative'+rel_id).prepend('<tr id="trloan'+datas.id+'"><td>'+loan_to+'</td><td>'+loan_type_name+'</td><td>'+display_account+'</td><td>'+loan_amount+'</td><td>'+edit+' '+del+'</td></tr>');

                $("#loan_type"+rel_id).val('');
                $("#loan_type_other"+rel_id).val('');
                $("#loan_account_type"+rel_id).val('');
                $("#saving_joint"+rel_id).val('');
                $("#loan_joint_account_with_name"+rel_id).val('');
                $("#loan_to"+rel_id).val('');
                $("#loan_amount"+rel_id).val('');
                $("#loan_type_div"+rel_id).css("display", "none");
                $("#joint_loan_div"+rel_id).css("display", "none");
            }
        }
        });
    }
}
</script>

<script type="text/javascript">
function get_modal_loan_type()
{

    $("#modal_loan_type_other").val('');
    var modal_loan_type = $("#modal_loan_type").val();
    if(modal_loan_type==5)
    {
        $("#modal_loan_div").css("display", "block");
        $("#modal_loan_type_other").attr("required", "required");
    }
    else
    {
        $("#modal_loan_div").css("display", "none");
        $("#modal_loan_type_other").removeAttr("required");

    }  
}
</script>
<script type="text/javascript">
function get_modal_loan_relatives()
{
    $("#modal_loan_joint_other").val('');
    $("#modal_loan_joint_other_name").val('');
    var modal_loan_account_type = $("#modal_loan_account_type").val();
    if(modal_loan_account_type=="Joint")
    {
        $("#modal_loan_account_type_div").css("display", "block");
        //$("#modal_loan_joint_other").attr("required", "required");
        //$("#modal_loan_joint_other_name").attr("required", "required");
    }
    else
    {
        $("#modal_loan_account_type_div").css("display", "none");
        //$("#modal_loan_joint_other").removeAttr("required");
        //$("#modal_loan_joint_other_name").removeAttr("required");
    }
}
</script>
<script type="text/javascript">
function edit_loan(id, datas)
{
    var loan_type_id = "";
    var loan_type_other =  "";
    var account_type =  "";
    var joint_account_with =  "";
    var joint_other_name =  "";
    var loan_to =  "";
    var amount =  "";
    var relation_type_id =  "";
    var candidate_id =  "";
    $("#modal_loan_account_type_div").css("display", "none");
    $("#modal_loan_div").css("display", "none");

    loan_type_id = $("#edit_loan"+id).data("loan_type_id");
    loan_type_other = $("#edit_loan"+id).data("loan_type_other");
    account_type = $("#edit_loan"+id).data("account_type");
    joint_account_with = $("#edit_loan"+id).data("joint_account_with");
    joint_other_name = $("#edit_loan"+id).data("joint_other_name");
    loan_to = $("#edit_loan"+id).data("loan_to");
    amount = $("#edit_loan"+id).data("amount");
    relation_type_id = $("#edit_loan"+id).data("relation_type_id");
    candidate_id = $("#edit_loan"+id).data("candidate_id");

    var count = Object.keys(datas).length;
    var all = '';
    for (var i = 0; i < count; i++) { 
        if(relation_type_id!=datas[i].relation_type_code)
        {
            if (joint_account_with.toString().indexOf(',') > -1)
            {
                if(joint_account_with.includes(datas[i].relation_type_code))
                    all += '<option value="'+ datas[i].relation_type_code+'-'+datas[i].name+'" selected>'+ datas[i].name +'</option>'; 
                else
                    all += '<option value="'+ datas[i].relation_type_code+'-'+datas[i].name+'">'+ datas[i].name +'</option>'; 
            }
            else
            {
                if(joint_account_with== datas[i].relation_type_code)
                    all += '<option value="'+ datas[i].relation_type_code+'-'+datas[i].name+'" selected>'+ datas[i].name +'</option>';
                else
                    all += '<option value="'+ datas[i].relation_type_code+'-'+datas[i].name+'">'+ datas[i].name +'</option>'; 
            }
        }
    }
    if(account_type=="Joint")
    {
        $("#modal_loan_account_type_div").css("display", "block");
        //$("#modal_loan_joint_other").attr("required", "required");
        //$("#modal_loan_joint_other_name").attr("required", "required");
    }
    if(loan_type_id==5)
    {
        $("#modal_loan_div").css("display", "block");
        $("#modal_loan_type_other").attr("required", "required");
    }

    $("#modal_loan_joint_other").html(all);
    $("#modal_loan_type").val(loan_type_id);
    $("#modal_loan_type_other").val(loan_type_other);
    $("#modal_loan_type option:selected").html();
    $("#modal_loan_account_type").val(account_type);
    $("#modal_loan_joint_other_name").val(joint_other_name);
    $("#modal_loan_to").val(loan_to);
    $("#modal_loan_amount").val(amount);
    $("#modal_loan_rel_id").val(relation_type_id);
    $("#modal_loan_cand_id").val(candidate_id);
    $("#modal_loan_loan_id").val(id);
    $("#loanModal").find('span').remove();
    $("#loanModal").modal('show');
}
</script>

<script type="text/javascript">
function update_loan()
{
    var modal_loan_type = $("#modal_loan_type").val();
    var modal_loan_type_other = $("#modal_loan_type_other").val();
    var modal_loan_type_other_name =  $("#modal_loan_type option:selected").html();
    var modal_loan_account_type = $("#modal_loan_account_type").val();
    var joint = $("#modal_loan_joint_other").val();
    var joint_other = $("#modal_loan_joint_other_name").val();
    var rel_id = $("#modal_loan_rel_id").val();
    var cand_id = $("#modal_loan_cand_id").val();
    var loan_to = $("#modal_loan_to").val();
    var amount = $("#modal_loan_amount").val();
    var loan_id = $("#modal_loan_loan_id").val();

    if(validate("form_loanModal"))
    {
        $.ajax({
        url: "{{ url($menu_action.'update_loan_bank') }}",
        type: 'GET',
        data: { 
                loan_id:loan_id, 
                cand_id:cand_id, 
                rel_type_id:rel_id,
                bank_inst_name:loan_to,
                loan_type:modal_loan_type,
                loan_type_other:modal_loan_type_other,
                account_type:modal_loan_account_type,
                joint:joint,
                joint_other:joint_other,
                amount:amount
        },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
            if(data!=0)
            {
                datas = JSON.parse(data);
                 
                if(modal_loan_type==5)
                    modal_loan_type_other_name = modal_loan_type_other_name+"<br>"+modal_loan_type_other;
                
                if(modal_loan_account_type=="Joint")
                    var display_account = modal_loan_account_type+" with "+datas.joint_account_with_name;
                else
                     var display_account = modal_loan_account_type;

                if(joint_other!="")
                    display_account = display_account+","+joint_other;

                $('#trloan'+loan_id).html('');

                var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get("affidavit.edit") }}" onclick="javascript:edit_loan('+datas.id+',{{$data}})"  data-loan_type_id="'+modal_loan_type+'" data-loan_type_other="'+modal_loan_type_other+'" data-account_type="'+modal_loan_account_type+'" data-joint_account_with="'+datas.joint_account_with+'" data-loan_to="'+loan_to+'"  data-amount="'+amount+'" data-joint_other_name="'+joint_other+'"  data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_loan'+datas.id+'"> <i class="fa fa-edit"></i> {{Lang::get("affidavit.edit") }} </a>';

                
				
				<?php if(Auth::user()->role_id != '19') { ?>
					
                var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get("affidavit.delete") }}" onclick="javascript:delete_loan('+datas.id+')"><i class="fa fa-times"></i> {{Lang::get("affidavit.delete") }}</a>';
				
				<?php } else { ?>
				var del = '';	
				<?php } ?>

                $('#trloan'+loan_id).html('<td>'+loan_to+'</td><td>'+modal_loan_type_other_name+'</td><td>'+display_account+'</td><td>'+amount+'</td><td>'+edit+' '+del+'</td>');
                $("#loanModal").modal('hide');
            }
        }
        });
    }
}
</script>
<script type="text/javascript">
function delete_loan(id)
{
    $("#modal_delete_loan_id").val(id);
    $("#deleteLoanModal").modal('show');
}
</script>
<script type="text/javascript">
function delete_loan_entry()
{
    var id = $("#modal_delete_loan_id").val();
    if(id)
    {
    $.ajax({
        url: "{{ url('delete_loan_bank') }}",
        type: 'GET',
        data: {  loan_id:id },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
        if(data==1)
        {
            $('#trloan'+id).remove();
            $("#deleteLoanModal").modal('hide');
        }
        }
    });
    }
}
</script>
<!-- Institution -->

<!-- Individuals/entity -->
<script type="text/javascript">
function get_indi_loan_type(rel_id)
{
    if(rel_id)
    {
        $("#indi_loan_type_other"+rel_id).val('');
        var loan_type = $("#indi_loan_type"+rel_id).val();
        if(loan_type==5)
        {
            $("#indi_loan_type_div"+rel_id).css("display", "block");
            $("#indi_loan_type_other"+rel_id).attr("required", "required");
        }
        else
        {
            $("#indi_loan_type_div"+rel_id).css("display", "none");
            $("#indi_loan_type_other"+rel_id).removeAttr("required");

        }            
    }
}
</script>
<script type="text/javascript">
function get_indi_loan_relatives(rel_id)
{
    if(rel_id)
    {
        $("#indi_loan_joint_account_with"+rel_id).val('');
        $("#indi_loan_joint_account_with_name"+rel_id).val('');
        var loan_account_type = $("#indi_loan_account_type"+rel_id).val();
        if(loan_account_type=="Joint")
        {
            $("#indi_joint_loan_div"+rel_id).css("display", "block");
           // $("#indi_loan_joint_account_with"+rel_id).attr("required", "required");
           // $("#indi_loan_joint_account_with_name"+rel_id).attr("required", "required");
        }
        else
        {
            $("#indi_joint_loan_div"+rel_id).css("display", "none");
           // $("#indi_loan_joint_account_with"+rel_id).removeAttr("required");
           // $("#indi_loan_joint_account_with_name"+rel_id).removeAttr("required");
        }
    }
}
</script>

<script type="text/javascript">
function save_indi_loans(cand_id, rel_id)
{
    var loan_type = $("#indi_loan_type"+rel_id).val();
    var loan_type_other = $("#indi_loan_type_other"+rel_id).val();
    var loan_type_name =  $("#indi_loan_type"+rel_id+" option:selected").html();
    var loan_account_type = $("#indi_loan_account_type"+rel_id).val();
    var loan_joint_account_with = $("#indi_loan_joint_account_with"+rel_id).val();
    var loan_joint_account_with_name = $("#indi_loan_joint_account_with_name"+rel_id).val();    
    var loan_to = $("#indi_loan_to"+rel_id).val();
    var loan_amount = $("#indi_loan_amount"+rel_id).val();

    if(validate("indi_loan_form"+rel_id))
    {
        $.ajax({
        url: "{{ url('save_indi_loan_bank') }}",
        type: 'GET',
        data: { 
                cand_id:cand_id, 
                rel_type_id:rel_id,
                individual_entity_name:loan_to, 
                loan_type:loan_type, 
                loan_type_other:loan_type_other,
                account_type:loan_account_type,
                joint:loan_joint_account_with,
                joint_other:loan_joint_account_with_name,
                amount:loan_amount
        },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
            if(data!=0)
            {
                 datas = JSON.parse(data);
                 
                if(loan_type==5)
                    loan_type_name = loan_type_name+"<br>"+loan_type_other;
                
                if(loan_account_type=="Joint")
                    var display_account = loan_account_type+" with "+datas.joint_account_with_name;
                else
                     var display_account = loan_account_type;

                if(loan_joint_account_with_name!="")
                    display_account = display_account+","+loan_joint_account_with_name;

                var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get("affidavit.edit") }}" onclick="javascript:edit_indi_loan('+datas.id+',{{$data}})"  data-loan_type_id="'+loan_type+'" data-loan_type_other="'+loan_type_other+'" data-account_type="'+loan_account_type+'" data-joint_account_with="'+datas.joint_account_with+'" data-loan_to="'+loan_to+'" data-amount="'+loan_amount+'" data-joint_other_name="'+loan_joint_account_with_name+'"  data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_indi_loan'+datas.id+'"> <span class=" btn btn-info mr-1"><i class="fa fa-edit"></i> {{Lang::get("affidavit.edit") }}</span> </a>';

                var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get("affidavit.delete") }}" onclick="javascript:delete_indi_loan('+datas.id+')"><i class="fa fa-times"></i> {{Lang::get("affidavit.delete") }}</a>';

                 $('#indi_loan_relative'+rel_id).prepend('<tr id="trindi_loan'+datas.id+'"><td>'+loan_to+'</td><td>'+loan_type_name+'</td><td>'+display_account+'</td><td>'+loan_amount+'</td><td>'+edit+' '+del+'</td></tr>');

                $("#indi_loan_type"+rel_id).val('');
                $("#indi_loan_type_other"+rel_id).val('');
                $("#indi_loan_account_type"+rel_id).val('');
                $("#indi_saving_joint"+rel_id).val('');
                $("#indi_loan_joint_account_with_name"+rel_id).val('');
                $("#indi_loan_to"+rel_id).val('');
                $("#indi_loan_amount"+rel_id).val('');
                $("#indi_loan_type_div"+rel_id).css("display", "none");
                $("#indi_joint_loan_div"+rel_id).css("display", "none");
            }
        }
        });
    }
}
</script>

<script type="text/javascript">
function get_modal_indi_loan_type()
{

    $("#modal_indi_loan_type_other").val('');
    var modal_loan_type = $("#modal_indi_loan_type").val();
    if(modal_loan_type==5)
    {
        $("#modal_indi_loan_div").css("display", "block");
        $("#modal_indi_loan_type_other").attr("required", "required");
    }
    else
    {
        $("#modal_indi_loan_div").css("display", "none");
        $("#modal_indi_loan_type_other").removeAttr("required");

    }  
}
</script>
<script type="text/javascript">
function get_modal_indi_loan_relatives()
{
    $("#modal_indi_loan_joint_other").val('');
    $("#modal_indi_loan_joint_other_name").val('');
    var modal_loan_account_type = $("#modal_indi_loan_account_type").val();
    if(modal_loan_account_type=="Joint")
    {
        $("#modal_indi_loan_account_type_div").css("display", "block");
        //$("#modal_indi_loan_joint_other").attr("required", "required");
        //$("#modal_indi_loan_joint_other_name").attr("required", "required");
    }
    else
    {
        $("#modal_indi_loan_account_type_div").css("display", "none");
        //$("#modal_indi_loan_joint_other").removeAttr("required");
       // $("#modal_indi_loan_joint_other_name").removeAttr("required");
    }
}
</script>
<script type="text/javascript">
function edit_indi_loan(id, datas)
{
    var loan_type_id = "";
    var loan_type_other =  "";
    var account_type =  "";
    var joint_account_with =  "";
    var joint_other_name =  "";
    var loan_to =  "";
    var amount =  "";
    var relation_type_id =  "";
    var candidate_id =  "";
    $("#modal_indi_loan_account_type_div").css("display", "none");
    $("#modal_indi_loan_div").css("display", "none");

    loan_type_id = $("#edit_indi_loan"+id).data("loan_type_id");
    loan_type_other = $("#edit_indi_loan"+id).data("loan_type_other");
    account_type = $("#edit_indi_loan"+id).data("account_type");
    joint_account_with = $("#edit_indi_loan"+id).data("joint_account_with");
    joint_other_name = $("#edit_indi_loan"+id).data("joint_other_name");
    loan_to = $("#edit_indi_loan"+id).data("loan_to");
    amount = $("#edit_indi_loan"+id).data("amount");
    relation_type_id = $("#edit_indi_loan"+id).data("relation_type_id");
    candidate_id = $("#edit_indi_loan"+id).data("candidate_id");

    var count = Object.keys(datas).length;
    var all = '';
    for (var i = 0; i < count; i++) { 
        if(relation_type_id!=datas[i].relation_type_code)
        {
            if (joint_account_with.toString().indexOf(',') > -1)
            {
                if(joint_account_with.includes(datas[i].relation_type_code))
                    all += '<option value="'+ datas[i].relation_type_code+'-'+datas[i].name+'" selected>'+ datas[i].name +'</option>'; 
                else
                    all += '<option value="'+ datas[i].relation_type_code+'-'+datas[i].name+'">'+ datas[i].name +'</option>'; 
            }
            else
            {
                if(joint_account_with== datas[i].relation_type_code)
                    all += '<option value="'+ datas[i].relation_type_code+'-'+datas[i].name+'" selected>'+ datas[i].name +'</option>';
                else
                    all += '<option value="'+ datas[i].relation_type_code+'-'+datas[i].name+'">'+ datas[i].name +'</option>'; 
            }
        }
    }
    if(account_type=="Joint")
    {
        $("#modal_indi_loan_account_type_div").css("display", "block");
        //$("#modal_indi_loan_joint_other").attr("required", "required");
        //$("#modal_indi_loan_joint_other_name").attr("required", "required");
    }
    if(loan_type_id==5)
    {
        $("#modal_indi_loan_div").css("display", "block");
        $("#modal_indi_loan_type_other").attr("required", "required");
    }

    $("#modal_indi_loan_joint_other").html(all);
    $("#modal_indi_loan_type").val(loan_type_id);
    $("#modal_indi_loan_type_other").val(loan_type_other);
    $("#modal_indi_loan_type option:selected").html();
    $("#modal_indi_loan_account_type").val(account_type);
    $("#modal_indi_loan_joint_other_name").val(joint_other_name);
    $("#modal_indi_loan_to").val(loan_to);
    $("#modal_indi_loan_amount").val(amount);
    $("#modal_indi_loan_rel_id").val(relation_type_id);
    $("#modal_indi_loan_cand_id").val(candidate_id);
    $("#modal_indi_loan_loan_id").val(id);
    $("#indiLoanModal").find('span').remove();
    $("#indiLoanModal").modal('show');
}
</script>

<script type="text/javascript">
function update_indi_loan()
{

    var modal_loan_type = $("#modal_indi_loan_type").val();
    var modal_loan_type_other = $("#modal_indi_loan_type_other").val();
    var modal_loan_type_other_name =  $("#modal_indi_loan_type option:selected").html();
    var modal_loan_account_type = $("#modal_indi_loan_account_type").val();
    var joint = $("#modal_indi_loan_joint_other").val();
    var joint_other = $("#modal_indi_loan_joint_other_name").val();
    var rel_id = $("#modal_indi_loan_rel_id").val();
    var cand_id = $("#modal_indi_loan_cand_id").val();
    var loan_to = $("#modal_indi_loan_to").val();
    var amount = $("#modal_indi_loan_amount").val();
    var loan_id = $("#modal_indi_loan_loan_id").val();


	//alert(validate("form_indiLoanModal"));


    if(validate("form_indiLoanModal"))
    {
		
        $.ajax({
        url: "{{ url($menu_action.'update_indi_loan_bank') }}",
        type: 'GET',
        data: { 
                loan_id:loan_id, 
                cand_id:cand_id, 
                rel_type_id:rel_id,
                individual_entity_name:loan_to,
                loan_type:modal_loan_type,
                loan_type_other:modal_loan_type_other,
                account_type:modal_loan_account_type,
                joint:joint,
                joint_other:joint_other,
                amount:amount
        },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
            if(data!=0)
            {
                datas = JSON.parse(data);
                 
                if(modal_loan_type==5)
                    modal_loan_type_other_name = modal_loan_type_other_name+"<br>"+modal_loan_type_other;
                
                if(modal_loan_account_type=="Joint")
                    var display_account = modal_loan_account_type+" with "+datas.joint_account_with_name;
                else
                     var display_account = modal_loan_account_type;

                if(joint_other!="")
                    display_account = display_account+","+joint_other;

                $('#trindi_loan'+loan_id).html('');

                var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get("affidavit.edit") }}" onclick="javascript:edit_indi_loan('+datas.id+',{{$data}})"  data-loan_type_id="'+modal_loan_type+'" data-loan_type_other="'+modal_loan_type_other+'" data-account_type="'+modal_loan_account_type+'" data-joint_account_with="'+datas.joint_account_with+'" data-loan_to="'+loan_to+'"  data-amount="'+amount+'" data-joint_other_name="'+joint_other+'"  data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_indi_loan'+datas.id+'"> <i class="fa fa-edit"></i> {{Lang::get("affidavit.edit") }} </a>';

				<?php if(Auth::user()->role_id != '19') { ?>
					
                var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get("affidavit.delete") }}" onclick="javascript:delete_indi_loan('+datas.id+')"> <i class="fa fa-times"></i> {{Lang::get("affidavit.delete") }}</a>';
				
				<?php } else { ?>
				var del = '';	
				<?php } ?>

                


                $('#trindi_loan'+loan_id).html('<td>'+loan_to+'</td><td>'+modal_loan_type_other_name+'</td><td>'+display_account+'</td><td>'+amount+'</td><td>'+edit+' '+del+'</td>');
                $("#indiLoanModal").modal('hide');
            }
        }
        });
    }
}
</script>
<script type="text/javascript">
function delete_indi_loan(id)
{
    $("#modal_indi_delete_loan_id").val(id);
    $("#deleteIndiLoanModal").modal('show');
}
</script>
<script type="text/javascript">
function delete_indi_loan_entry()
{
    var id = $("#modal_indi_delete_loan_id").val();
    if(id)
    {
    $.ajax({
        url: "{{ url('delete_indi_loan_bank') }}",
        type: 'GET',
        data: {  loan_id:id },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
        if(data==1)
        {
            $('#trindi_loan'+id).remove();
            $("#deleteIndiLoanModal").modal('hide');
        }
        }
    });
    }
}
</script>
<!-- Individuals/entity -->


<!-- Government Dues -->
    <script type="text/javascript">
    function get_govt_dept_type(rel_id)
    {
        if(rel_id)
        {
            $("#govt_dues_form"+rel_id).find("span").remove();
            $("#other_dept"+rel_id).val('');
            var govt_dept_name_code = $("#govt_dept_name_code"+rel_id).val();
            if(govt_dept_name_code!=1)
            {
                $("#govt_dept_due_details_div"+rel_id).css("display", "block");            
                $("#govt_dept_due_details_radio_div"+rel_id).css("display", "none");
                $("#is_government_accomodation_div"+rel_id).css("display", "none");
                $("#is_government_accomodation_no"+rel_id).prop("checked", true);
                $("#government_accomodation_address"+rel_id).removeAttr("required");
                $("#telephone_charges"+rel_id).removeAttr("required");
                $("#no_dues_file"+rel_id).removeAttr("required");

                if(govt_dept_name_code==10)
                {
                    $("#govt_dept_div"+rel_id).css("display", "block");
                    $("#due_details"+rel_id).attr("required", "required");
                    $("#other_dept"+rel_id).attr("required", "required");
                }
                else
                {
                    $("#govt_dept_div"+rel_id).css("display", "none");
                    $("#other_dept"+rel_id).removeAttr("required");
                } 
            }           
            else
            {
                $("#govt_dept_div"+rel_id).css("display", "none");
                $("#govt_dept_due_details_div"+rel_id).css("display", "none");
                $("#govt_dept_due_details_radio_div"+rel_id).css("display", "block");
                $("#due_details"+rel_id).removeAttr("required");
                $("#other_dept"+rel_id).removeAttr("required");
            }
        }
    }
    </script>
    <script type="text/javascript">
        function radio_click(rel_id, value)
        {
            if(value==1)
            {
                $("#is_government_accomodation_div"+rel_id).css("display", "block");
                $("#government_accomodation_address"+rel_id).attr("required", "required");
                $("#telephone_charges"+rel_id).attr("required", "required");
                $("#no_dues_file"+rel_id).attr("required", "required");
            }
            else
            {
                $("#is_government_accomodation_div"+rel_id).css("display", "none");
                $("#government_accomodation_address"+rel_id).removeAttr("required");
                $("#telephone_charges"+rel_id).removeAttr("required");
                $("#no_dues_file"+rel_id).removeAttr("required");
            }
        }
    </script>


    <script type="text/javascript">
    function save_govt_due(cand_id, rel_id)
    {
        $("#errfilesize"+rel_id).html('');
        var govt_dept_name_code = $("#govt_dept_name_code"+rel_id).val();
        var other_dept = $("#other_dept"+rel_id).val();
        var govt_dept_name_code_name =  $("#govt_dept_name_code"+rel_id+" option:selected").html();
        var due_details = $("#due_details"+rel_id).val();  
        var govt_due_amount = $("#govt_due_amount"+rel_id).val();
        var is_government_accomodation = $("input[name='is_government_accomodation"+rel_id+"']:checked").val();
        var government_accomodation_address = $("#government_accomodation_address"+rel_id).val();
        var telephone_charges = $("#telephone_charges"+rel_id).val();
        var no_dues_file = $("#no_dues_file"+rel_id)[0].files[0];
        if(no_dues_file)
        {
            var size = $("#no_dues_file"+rel_id)[0].files[0].size;
            var file_size = Math.round((size / 1024)); 
            var ext = $("#no_dues_file"+rel_id).val().split('.').pop();
        }


        var due_details_name;
        var img_url;
        var edit;
        var del;

        if(validate("govt_dues_form"+rel_id))
        {
            $.ajax({
            url: "{{ url('save_govt_due') }}",
            type: 'GET',
            data: { 
                    cand_id:cand_id, 
                    rel_type_id:rel_id,
                    govt_dept_name_code:govt_dept_name_code, 
                    other_dept:other_dept,
                    due_details:due_details,
                    amount:govt_due_amount,
                    is_government_accomodation:is_government_accomodation,
                    government_accomodation_address:government_accomodation_address,
                    telephone_charges:telephone_charges
            },            
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
                if(data!=0)
                {
                     datas = JSON.parse(data);
                     
                    if(govt_dept_name_code==10)
                        govt_dept_name_code_name = govt_dept_name_code_name+"<br>"+other_dept;

                    if(no_dues_file!="")
                    {
                        if(file_size<='2048')
                        {
                            if(ext=='pdf')
                            {
                                var fd = new FormData();
                                var files = $("#no_dues_file"+rel_id)[0].files[0];
                                fd.append('file', files);
                                fd.append('govt_due_id', datas.id);

                                $.ajax({
                                    url: "{{ url('save_govt_due_image') }}",
                                    async: false,
                                    type: 'post',
                                    data: fd,
                                    contentType: false,
                                    processData: false,
                                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                    success: function(response){
                                        if(response!=0)
                                        {
                                            datas_file = JSON.parse(response);
                                            img_url = datas_file.image_url;
                                        }
                                    },
                                });
                            }
                            else
                            {
                                $("#errfilesize"+rel_id).html("Only pdf files are allowed.");
                            }
                        }
                        else
                        {
                            $("#errfilesize"+rel_id).html("Maximum file size is 2 MB.");
                        }
                    }
                    if(govt_dept_name_code==1)
                    {
                        if(is_government_accomodation==1)
                        {
                            if(img_url!="")
                            {
                                due_details_name = '<label>Address of the Government accommodation:</label><br><strong>'+government_accomodation_address+'</strong><label>There is no dues payable in respect of above Government accommodation, towards</label><ol type="A"><li>Rent</li><li>Electricity charges</li><li>Water charges</li><li>Telephone charges as on : <strong>'+datas.telephone_charges+'</strong><br><label>No dues file:</label> <a href="'+img_url+'" target="_new">Click here to open the file</a></li></ol>';
                            }
                            else
                            {

                                due_details_name = '<label>Address of the Government accommodation:</label><br><strong>'+government_accomodation_address+'</strong><label>There is no dues payable in respect of above Government accommodation, towards</label><ol type="A"><li>Rent</li><li>Electricity charges</li><li>Water charges</li><li>Telephone charges as on : <strong>'+datas.telephone_charges+'</strong></li></ol>';
                            }
                        }
                        else
                        {
                            due_details_name = "No";
                        }

                    }
                    else
                    {
                        due_details_name = due_details;
                    }

                    edit = '<a href="javascript:void(0)" title="Edit" onclick="javascript:edit_govt_dues('+datas.id+')"  data-govt_dept_name_code="'+govt_dept_name_code+'" data-other_dept="'+other_dept+'" data-due_details="'+due_details+'" data-amount="'+govt_due_amount+'" data-is_government_accomodation="'+is_government_accomodation+'" data-government_accomodation_address="'+government_accomodation_address+'" data-telephone_charges="'+telephone_charges+'" data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_govt_dues'+datas.id+'"> <span class=" btn btn-info mr-1"><i class="fa fa-edit"></i> Edit</span> </a>';

                    del = '<a href="javascript:void(0)" title="Delete" onclick="javascript:delete_govt_dues('+datas.id+')"><span class=" btn btn-info mr-1"><i class="fa fa-times"></i> Delete</span></a>';

                     $('#govt_dues_relative'+rel_id).prepend('<tr id="trgovt_dues'+datas.id+'"><td>'+govt_dept_name_code_name+'</td><td>'+due_details_name+'</td><td>'+govt_due_amount+'</td><td>'+edit+' '+del+'</td></tr>');

                    img_url = "";
                    $("#govt_dept_name_code"+rel_id).val('');
                    $("#other_dept"+rel_id).val('');
                    $("#due_details"+rel_id).val('');
                    $("#govt_due_amount"+rel_id).val('');
                    $("#govt_dept_div"+rel_id).css("display", "none");
                    $("#govt_dept_due_details_div"+rel_id).css("display", "block");            
                    $("#govt_dept_due_details_radio_div"+rel_id).css("display", "none");
                    $("#is_government_accomodation_div"+rel_id).css("display", "none");
                    $("#is_government_accomodation_no"+rel_id).prop("checked", true);
                }
            }
            });
        }
    }
    </script>

    <script type="text/javascript">
    function get_modal_govt_dept_type()
    {

        $("#modal_other_dept").val('');
        $("#modal_due_details").val('');
        var modal_govt_dept_name_code = $("#modal_govt_dept_name_code").val();
        if(modal_govt_dept_name_code!=1)
        {
            $("#modal_govt_dept_due_details_div").css("display", "block");            
            $("#modal_govt_dept_due_details_radio_div").css("display", "none");
            $("#modal_is_government_accomodation_div").css("display", "none");
            $("#modal_is_government_accomodation_no").prop("checked", true);
            $("#modal_due_details").attr("required", "required");
            $("#modal_government_accomodation_address").removeAttr("required");
            $("#modal_telephone_charges").removeAttr("required");
            $("#modal_no_dues_file").removeAttr("required");
            if(modal_govt_dept_name_code==10)
            {
                $("#modal_govt_dept_div").css("display", "block");
                $("#modal_other_dept").attr("required", "required");
            }
            else
            {
                $("#modal_govt_dept_div").css("display", "none");
                $("#modal_other_dept").removeAttr("required");
            } 
        }
        else
        {
            $("#modal_govt_dept_div").css("display", "none");
            $("#modal_govt_dept_due_details_div").css("display", "none");
            $("#modal_govt_dept_due_details_radio_div").css("display", "block");        
            $("#modal_due_details"+rel_id).removeAttr("required");
            $("#modal_other_dept"+rel_id).removeAttr("required");
        }
     
    }
    </script>

    <script type="text/javascript">
    function edit_govt_dues(id)
    {
        var govt_dept_name_code = "";
        var other_dept =  "";
        var due_details =  "";
        var amount =  "";
        var relation_type_id =  "";
        var candidate_id =  "";
        var is_government_accomodation =  "";
        $("#modal_govt_dept_div").css("display", "none");
        $("#modal_govt_dept_due_details_div").css("display", "block");
        $("#modal_govt_dept_due_details_radio_div").css("display", "none");

        govt_dept_name_code = $("#edit_govt_dues"+id).data("govt_dept_name_code");
        other_dept = $("#edit_govt_dues"+id).data("other_dept");
        due_details = $("#edit_govt_dues"+id).data("due_details");
        amount = $("#edit_govt_dues"+id).data("amount");
        is_government_accomodation = $("#edit_govt_dues"+id).data("is_government_accomodation");
        government_accomodation_address = $("#edit_govt_dues"+id).data("government_accomodation_address");
        telephone_charges = $("#edit_govt_dues"+id).data("telephone_charges");
        relation_type_id = $("#edit_govt_dues"+id).data("relation_type_id");
        candidate_id = $("#edit_govt_dues"+id).data("candidate_id");

        if(govt_dept_name_code==10)
        {
            $("#modal_govt_dept_div").css("display", "block");
        }

        if(govt_dept_name_code==1)
        {
            $("#modal_govt_dept_due_details_div").css("display", "none");
            $("#modal_govt_dept_due_details_radio_div").css("display", "block");
        }
        if(is_government_accomodation==1)
        {
            $("#modal_is_government_accomodation_yes").prop("checked", true);
            $("#modal_is_government_accomodation_div").css("display", "block");
        }
        else
        {
            $("#modal_is_government_accomodation_no").prop("checked", true);
            $("#modal_is_government_accomodation_div").css("display", "none");
        }

        $("#modal_govt_dept_name_code").val(govt_dept_name_code);
        $("#modal_other_dept").val(other_dept);
        $("#modal_due_details").val(due_details);
        $("#modal_govt_due_amount").val(amount);
        $("#modal_government_accomodation_address").val(government_accomodation_address);
        $("#modal_telephone_charges").val(telephone_charges);
        $("#modal_govt_due_rel_id").val(relation_type_id);
        $("#modal_govt_due_cand_id").val(candidate_id);
        $("#modal_govt_due_id").val(id);
        $("#govtDueModal").modal('show');
    }
    </script>
    <script type="text/javascript">
        function modal_radio_click(value)
        {
            $("#modal_government_accomodation_address").val('');
            $("#modal_telephone_charges").val('');
            if(value==1)
            {
                $("#modal_is_government_accomodation_div").css("display", "block");
                $("#modal_government_accomodation_address").attr("required", "required");
                $("#modal_telephone_charges").attr("required", "required");
                $("#modal_no_dues_file").attr("required", "required");
            }
            else
            {
                $("#modal_is_government_accomodation_div").css("display", "none");
                $("#modal_government_accomodation_address").removeAttr("required");
                $("#modal_telephone_charges").removeAttr("required");
                $("#modal_no_dues_file").removeAttr("required");
            }
        }
    </script>
    <script type="text/javascript">
    function update_govt_due()
    {
        $("#modal_errfilesize").html('');
        var govt_dept_name_code = $("#modal_govt_dept_name_code").val();
        var other_dept = $("#modal_other_dept").val();
        var govt_dept_name_code_name =  $("#modal_govt_dept_name_code option:selected").html();
        var due_details = $("#modal_due_details").val();  
        var govt_due_amount = $("#modal_govt_due_amount").val();
        var rel_id = $("#modal_govt_due_rel_id").val();
        var cand_id = $("#modal_govt_due_cand_id").val();
        var govt_due_id = $("#modal_govt_due_id").val();
        var is_government_accomodation = $("input[name='modal_is_government_accomodation']:checked").val();
        var government_accomodation_address = $("#modal_government_accomodation_address").val();
        var telephone_charges = $("#modal_telephone_charges").val();
        var no_dues_file = $("#modal_no_dues_file")[0].files[0];
        if(no_dues_file)
        {
            var size = $("#modal_no_dues_file")[0].files[0].size;
            var file_size = Math.round((size / 1024)); 
            var ext = $("#modal_no_dues_file").val().split('.').pop();
        }

        var due_details_name;
        var img_url;
        
        if(validate("form_govtDueModal"))
        {
            $.ajax({
            url: "{{ url($menu_action.'update_govt_due') }}",
            type: 'GET',
            data: { 
                    govt_due_id:govt_due_id, 
                    cand_id:cand_id, 
                    rel_type_id:rel_id,
                    govt_dept_name_code:govt_dept_name_code, 
                    other_dept:other_dept,
                    due_details:due_details,
                    amount:govt_due_amount,
                    is_government_accomodation:is_government_accomodation,
                    government_accomodation_address:government_accomodation_address,
                    telephone_charges:telephone_charges
            },        
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
                if(data!=0)
                {
                    datas = JSON.parse(data);
                     
                    if(govt_dept_name_code==10)
                        govt_dept_name_code_name = govt_dept_name_code_name+"<br>"+other_dept;

                    if(no_dues_file!="")
                        {
                            if(file_size<='2048')
                            {
                                if(ext=='pdf')
                                {
                                    var fd = new FormData();
                                    var files = $("#modal_no_dues_file")[0].files[0];
                                    fd.append('file', files);
                                    fd.append('govt_due_id', govt_due_id);

                                    $.ajax({
                                        url: "{{ url($menu_action.'save_govt_due_image') }}",
                                        async: false,
                                        type: 'post',
                                        data: fd,
                                        contentType: false,
                                        processData: false,
                                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                        success: function(response){
                                            if(response!=0)
                                            {
                                                datas_file = JSON.parse(response);
                                                img_url = datas_file.image_url;
                                            }
                                        },
                                    });
                                }
                                else
                                {
                                    $("#modal_errfilesize").html("Only pdf files are allowed.");
                                }
                            }
                            else
                            {
                                $("#modal_errfilesize").html("Maximum file size is 2 MB.");
                            }
                        }

                    if(govt_dept_name_code==1)
                    {
                        if(is_government_accomodation==1)
                        {
                            if(img_url!="")
                                {
                                    due_details_name = '<label>Address of the Government accommodation:</label><br><strong>'+government_accomodation_address+'</strong><label>There is no dues payable in respect of above Government accommodation, towards</label><ol type="A"><li>Rent</li><li>Electricity charges</li><li>Water charges</li><li>Telephone charges as on : <strong>'+datas.telephone_charges+'</strong><br><label>No dues file:</label> <a href="'+img_url+'" target="_new">Click here to open the file</a></li></ol>';
                                }
                                else
                                {

                                    due_details_name = '<label>Address of the Government accommodation:</label><br><strong>'+government_accomodation_address+'</strong><label>There is no dues payable in respect of above Government accommodation, towards</label><ol type="A"><li>Rent</li><li>Electricity charges</li><li>Water charges</li><li>Telephone charges as on : <strong>'+datas.telephone_charges+'</strong></li></ol>';
                                }
                        }
                        else
                        {
                            due_details_name = "No";
                        }

                    }
                    else
                    {
                        due_details_name = due_details;
                    }

                    $('#trgovt_dues'+govt_due_id).html('');

                    /*var del = '<a href="javascript:void(0)" title="Delete" onclick="javascript:delete_indi_loan('+datas.id+')"><span class=" btn btn-info mr-1"><i class="fa fa-times"></i> Delete</span></a>';*/

                    var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get("affidavit.edit") }}" onclick="javascript:edit_govt_dues('+datas.id+')"  data-govt_dept_name_code="'+govt_dept_name_code+'" data-other_dept="'+other_dept+'" data-due_details="'+due_details+'" data-amount="'+govt_due_amount+'" data-is_government_accomodation="'+is_government_accomodation+'" data-government_accomodation_address="'+government_accomodation_address+'" data-telephone_charges="'+telephone_charges+'"data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_govt_dues'+datas.id+'"> <i class="fa fa-edit"></i> {{Lang::get("affidavit.edit") }} </a>';

					<?php if(Auth::user()->role_id != '19') { ?>
					
					var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get("affidavit.delete") }}" onclick="javascript:delete_govt_dues('+datas.id+')"> <i class="fa fa-times"></i> {{Lang::get("affidavit.delete") }}</a>';
					
					<?php } else { ?>
					var del = '';	
					<?php } ?>

                    $('#trgovt_dues'+govt_due_id).html('<td>'+govt_dept_name_code_name+'</td><td>'+due_details_name+'</td><td>'+govt_due_amount+'</td><td>'+edit+' '+del+'</td>');
                    $("#govtDueModal").modal('hide');
                }
            }
            });
        }
    }
    </script>
    <script type="text/javascript">
    function delete_govt_dues(id)
    {
        $("#modal_govt_due_delete_id").val(id);
        $("#deleteGovtDueModal").modal('show');
    }
    </script>
    <script type="text/javascript">
    function delete_govt_dues_entry()
    {
        var id = $("#modal_govt_due_delete_id").val();
        if(id)
        {
        $.ajax({
            url: "{{ url('delete_govt_due') }}",
            type: 'GET',
            data: {  id:id },            
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
            if(data==1)
            {
                $('#trgovt_dues'+id).remove();
                $("#deleteGovtDueModal").modal('hide');
            }
            }
        });
        }
    }
    </script>
<!-- Government Dues -->

<!-- Other -->
    <script type="text/javascript">
    function save_other(cand_id, rel_id)
    {
        var asset_type = $("#asset_type"+rel_id).val();
        var brief_details = $("#brief_details"+rel_id).val();  
        var other_amount = $("#other_amount"+rel_id).val();
        
        if(validate("other_form"+rel_id))
        {
            $.ajax({
            url: "{{ url('save_other_liabilities') }}",
            type: 'GET',
            data: { 
                    cand_id:cand_id, 
                    rel_type_id:rel_id,
                    asset_type:asset_type, 
                    brief_details:brief_details,
                    other_amount:other_amount
            },            
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
                if(data!=0)
                {
                    datas = JSON.parse(data);

                    var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get("affidavit.edit") }}" onclick="javascript:edit_other('+datas.id+')"  data-asset_type="'+asset_type+'" data-brief_details="'+brief_details+'" data-amount="'+other_amount+'" data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_other'+datas.id+'"> <i class="fa fa-edit"></i> {{Lang::get("affidavit.edit") }} </a>';

                    var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get("affidavit.delete") }}" onclick="javascript:delete_other('+datas.id+')"><i class="fa fa-times"></i> {{Lang::get("affidavit.delete") }}</a>';

                     $('#other_relative'+rel_id).prepend('<tr id="trother'+datas.id+'"><td>'+asset_type+'</td><td>'+brief_details+'</td><td>'+other_amount+'</td><td>'+edit+' '+del+'</td></tr>');

                    $("#asset_type"+rel_id).val('');
                    $("#brief_details"+rel_id).val('');
                    $("#other_amount"+rel_id).val('');
                }
            }
            });
        }
    }
    </script>
    <script type="text/javascript">
    function edit_other(id)
    {
        var asset_type = "";
        var brief_details =  "";
        var amount =  "";
        var relation_type_id =  "";
        var candidate_id =  "";

        asset_type = $("#edit_other"+id).data("asset_type");
        brief_details = $("#edit_other"+id).data("brief_details");
        amount = $("#edit_other"+id).data("amount");
        relation_type_id = $("#edit_other"+id).data("relation_type_id");
        candidate_id = $("#edit_other"+id).data("candidate_id");

        $("#modal_asset_type").val(asset_type);
        $("#modal_brief_details").val(brief_details);
        $("#modal_other_amount").val(amount);
        $("#modal_other_rel_id").val(relation_type_id);
        $("#modal_other_cand_id").val(candidate_id);
        $("#modal_other_id").val(id);
        $("#otherModal").modal('show');
    }
    </script>

    <script type="text/javascript">
    function update_others()
    {
       
        var asset_type = $("#modal_asset_type").val();
        var brief_details = $("#modal_brief_details").val();  
        var amount = $("#modal_other_amount").val();
        var cand_id = $("#modal_other_cand_id").val();
        var rel_id = $("#modal_other_rel_id").val();
        var other_id = $("#modal_other_id").val();

        if(validate("form_otherModal"))
        {
            $.ajax({
            url: "{{ url($menu_action.'update_other_liabilities') }}",
            type: 'GET',
            data: { 
                    other_id:other_id, 
                    cand_id:cand_id, 
                    rel_type_id:rel_id,
                    asset_type:asset_type, 
                    brief_details:brief_details,
                    other_amount:amount
            },            
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
                if(data!=0)
                {
                    datas = JSON.parse(data);

                    $('#trother'+other_id).html('');

                    var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get("affidavit.edit") }}" onclick="javascript:edit_other('+datas.id+')"  data-asset_type="'+asset_type+'" data-brief_details="'+brief_details+'" data-amount="'+amount+'"  data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_other'+datas.id+'"> <i class="fa fa-edit"></i> {{Lang::get("affidavit.edit") }} </a>';

                    
					
					<?php if(Auth::user()->role_id != '19') { ?>
					
					var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get("affidavit.delete") }}" onclick="javascript:delete_other('+datas.id+')"><i class="fa fa-times"></i> {{Lang::get("affidavit.delete") }}</a>';
					
					<?php } else { ?>
					var del = '';	
					<?php } ?>

                    $('#trother'+other_id).html('<td>'+asset_type+'</td><td>'+brief_details+'</td><td>'+amount+'</td><td>'+edit+' '+del+'</td>');
                    $("#otherModal").modal('hide');
                }
            }
            });
        }
    }
    </script>

    <script type="text/javascript">
    function delete_other(id)
    {
        $("#modal_delete_other_id").val(id);
        $("#deleteOtherModal").modal('show');
    }
    </script>
    <script type="text/javascript">
    function delete_other_entry()
    {
        var id = $("#modal_delete_other_id").val();
        if(id)
        {
        $.ajax({
            url: "{{ url('delete_other_liabilities') }}",
            type: 'GET',
            data: {  other_id:id },            
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
            if(data==1)
            {
                $('#trother'+id).remove();
                $("#deleteOtherModal").modal('hide');
            }
            }
        });
        }
    }
    </script>
<!-- Other -->


<!-- Other Disputes-->
<script type="text/javascript">
function save_other_dis(cand_id, rel_id)
{
    var asset_type = $("#asset_type_dis"+rel_id).val();
    var brief_details = $("#brief_details_dis"+rel_id).val();  
    var other_amount = $("#other_amount_dis"+rel_id).val();
    
    if(validate("other_form_dis"+rel_id))
    {
        $.ajax({
        url: "{{ url('save_other_disputes_liabilities') }}",
        type: 'GET',
        data: { 
                cand_id:cand_id, 
                rel_type_id:rel_id,
                asset_type:asset_type, 
                brief_details:brief_details,
                other_amount:other_amount
        },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
            if(data!=0)
            {
                datas = JSON.parse(data);

                var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get("affidavit.edit") }}" onclick="javascript:edit_other_dis('+datas.id+')"  data-asset_type="'+asset_type+'" data-brief_details="'+brief_details+'" data-amount="'+other_amount+'" data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_other_dis'+datas.id+'"> <i class="fa fa-edit"></i> {{Lang::get("affidavit.edit") }} </a>';

                var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get("affidavit.delete") }}" onclick="javascript:delete_other_dis('+datas.id+')"><i class="fa fa-times"></i> {{Lang::get("affidavit.delete") }}</a>';

                 $('#other_relative_dis'+rel_id).prepend('<tr id="trother_dis'+datas.id+'"><td>'+asset_type+'</td><td>'+brief_details+'</td><td>'+other_amount+'</td><td>'+edit+' '+del+'</td></tr>');

                $("#asset_type_dis"+rel_id).val('');
                $("#brief_details_dis"+rel_id).val('');
                $("#other_amount_dis"+rel_id).val('');
            }
        }
        });
    }
}
</script>
<script type="text/javascript">
function edit_other_dis(id)
{
    var asset_type = "";
    var brief_details =  "";
    var amount =  "";
    var relation_type_id =  "";
    var candidate_id =  "";

    asset_type = $("#edit_other_dis"+id).data("asset_type");
    brief_details = $("#edit_other_dis"+id).data("brief_details");
    amount = $("#edit_other_dis"+id).data("amount");
    relation_type_id = $("#edit_other_dis"+id).data("relation_type_id");
    candidate_id = $("#edit_other_dis"+id).data("candidate_id");

    $("#modal_asset_type_dis").val(asset_type);
    $("#modal_brief_details_dis").val(brief_details);
    $("#modal_other_amount_dis").val(amount);
    $("#modal_other_rel_id_dis").val(relation_type_id);
    $("#modal_other_cand_id_dis").val(candidate_id);
    $("#modal_other_id_dis").val(id);
    $("#otherModal_dis").modal('show');
}
</script>

<script type="text/javascript">
function update_others_dis()
{
   
    var asset_type = $("#modal_asset_type_dis").val();
    var brief_details = $("#modal_brief_details_dis").val();  
    var amount = $("#modal_other_amount_dis").val();
    var cand_id = $("#modal_other_cand_id_dis").val();
    var rel_id = $("#modal_other_rel_id_dis").val();
    var other_id = $("#modal_other_id_dis").val();

    if(validate("form_otherModal_dis"))
    {
        $.ajax({
        url: "{{ url($menu_action.'update_other_disputes_liabilities') }}",
        type: 'GET',
        data: { 
                other_id:other_id, 
                cand_id:cand_id, 
                rel_type_id:rel_id,
                asset_type:asset_type, 
                brief_details:brief_details,
                other_amount:amount
        },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
            if(data!=0)
            {
                datas = JSON.parse(data);

                $('#trother_dis'+other_id).html('');

                var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get("affidavit.edit") }}" onclick="javascript:edit_other_dis('+datas.id+')"  data-asset_type="'+asset_type+'" data-brief_details="'+brief_details+'" data-amount="'+amount+'"  data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_other_dis'+datas.id+'"> <i class="fa fa-edit"></i> {{Lang::get("affidavit.edit") }} </a>';

				<?php if(Auth::user()->role_id != '19') { ?>
					
                var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get("affidavit.delete") }}" onclick="javascript:delete_other_dis('+datas.id+')"><i class="fa fa-times"></i> {{Lang::get("affidavit.delete") }}</a>';
				
				<?php } else { ?>
				var del = '';	
				<?php } ?>

                $('#trother_dis'+other_id).html('<td>'+asset_type+'</td><td>'+brief_details+'</td><td>'+amount+'</td><td>'+edit+' '+del+'</td>');
                $("#otherModal_dis").modal('hide');
            }
        }
        });
    }
}
</script>

<script type="text/javascript">
function delete_other_dis(id)
{
    $("#modal_delete_other_id_dis").val(id);
    $("#deleteOtherModal_dis").modal('show');
}
</script>
<script type="text/javascript">
function delete_other_entry_dis()
{
    var id = $("#modal_delete_other_id_dis").val();
    if(id)
    {
    $.ajax({
        url: "{{ url('delete_other_disputes_liabilities') }}",
        type: 'GET',
        data: {  other_id:id },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
        if(data==1)
        {
            $('#trother_dis'+id).remove();
            $("#deleteOtherModal_dis").modal('hide');
        }
        }
    });
    }
}
</script>
<!-- Other Disputes-->

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
                
                    /* alert(id);
                    alert(value);
                    alert(value.length); */
               
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


<!-- validation -->
@endsection