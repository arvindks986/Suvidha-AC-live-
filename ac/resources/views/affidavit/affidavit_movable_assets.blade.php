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
.datepicker{
    font-size: 0.85rem;
} 
.form-control {    
    padding: .45rem .45rem;
}
.w-130{
    width: 130px;
}
i.fa.fa-calendar.input-group-text.fa-lg {
    padding: 9px;
}

.calender-model{
	float: right;
    margin-top: -36px;
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
                <li class="step-current"><b>&#10004;</b><span><a href="{{url($menu_action.'Affidavit/MovableAssets')}}">{{Lang::get('affidavit.movable_assets') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'immovable-assets')}}">{{Lang::get('affidavit.immovable_assets') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'liabilities')}}">{{Lang::get('affidavit.liabilities') }}</a></span></li>
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
                    <div class="col-12">
                        <h4 class="main_heading">{{Lang::get('affidavit.movable_assets') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <!-- ---------------Cash In Hand------------ -->
                    <div class="accordion_head">{{Lang::get('affidavit.cash_in_hand') }}<span class="plusminus">+</span></div>
                    <div class="accordion_body" style="display: none"> 
                        <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover purpleTable">
                                <thead>
                                    <tr>
                                        <th>{{Lang::get('affidavit.sr_no') }}</th>
                                        <th>{{Lang::get('affidavit.name') }}</th>
                                        <th>{{Lang::get('affidavit.relation_type') }}</th>
                                        <th>{{Lang::get('affidavit.cash_in_hand') }} (in &#x20b9;)</th>
                                        <th>{{Lang::get('affidavit.action') }}</th>          
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($data))
                                    <?php $i = 1; ?>
                                    @foreach($data as $ps)
                                        <tr id="cashinhand{{$ps->id}}">
                                        <td>{{$i}}</td>
                                        <td>{{$ps->name}}</td>
                                        <td>{{$ps->relation_type}}</td>                                                
                                        <td>
                                        <input type="text" class="form-control" disabled="disabled" required="required" value="{{$ps->cash?$ps->cash:'0.00'}}" id="cash{{$ps->id}}" onkeydown="return NumbersOnly(event,this)" maxlength="12" >
                                        </td>
                                        <td nowrap="nowrap" class="edt-all">
                                            <a href="javascript:void(0)" class="btn btn-info btn-sm" title="Edit" id="edit{{$ps->id}}" onclick="javascript:edit_cash({{$ps->id}})" >
                                                <i class="fa fa-edit"></i> {{Lang::get('affidavit.edit') }}
                                            </a>
                                            <a href="javascript:void(0)" class="btn btn-info btn-sm" title="Update" style="display: none" id="update{{$ps->id}}" onclick="javascript:update_cash({{$ps->id}}, {{$ps->candidate_id}}, {{$ps->relation_type_code}})" >
                                               <i class="fa fa-check"></i> {{Lang::get('affidavit.update') }}
                                            </a>
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                    @endforeach
                                    @else
                                    <tr><td colspan="6">{{Lang::get('affidavit.data_not_found') }}</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>    
                    <!-- ---------------Cash In Hand------------ -->
                    <!-- ---------------Deposit------------ -->
                    <div class="accordion_head">{{Lang::get('affidavit.deposit_in_bank_accounts_financial_institutions') }} <span class="plusminus">+</span></div>
                    <div class="accordion_body" style="display: none"> 
                        @if(!empty($data))
                        @foreach($data as $dp)
                            <h6 class="text-left pt-2 py-3 text-uppercase">
                            {{$dp->relation_type}} : {{$dp->name}}
                            </h6>
                            <table id="relative{{$dp->relation_type_code}}" class="table table-striped table-bordered table-hover purpleTable" >
                            <thead>
                                <tr>
                                    <th>{{Lang::get('affidavit.bank_company_name') }}</th>
                                    <th>{{Lang::get('affidavit.branch_address') }}</th>
                                    <th>{{Lang::get('affidavit.deposit_type') }}</th>
                                    <th>{{Lang::get('affidavit.account_type') }}</th>
                                    <th>{{Lang::get('affidavit.date_of_deposit') }}</th>
                                    <th>{{Lang::get('affidavit.amount') }} (in &#x20b9;)</th>
                                    <th>{{Lang::get('affidavit.action') }}</th>          
                                </tr>
                            </thead>
                            <tbody>                                                
                                @if(!empty($bank_details))
                                    @foreach($bank_details as $bank_row)
                                        @if($bank_row->relation_type_code==$dp->relation_type_code)    
                                        <tr id="tr{{$bank_row->id}}">
                                            <td>{{$bank_row->bank_name}}</td>     
                                            <td>{{$bank_row->branch_address}}</td>     
                                            <td>{{$bank_row->deposit_type}}<br>
                                                @if(!empty($bank_row->deposit_type_other))
                                                        {{$bank_row->deposit_type_other}}
                                                    @endif
                                            </td>     
                                            <td>{{$bank_row->account_type}}
                                                @if($bank_row->account_type=="Joint")
                                                    {{$bank_row->joint_account_with_name}}
                                                @endif
                                            </td>     
                                            <td>{{\Carbon\Carbon::parse($bank_row->deposit_date)->format('d/m/Y')}}</td>     
                                            <td>{{$bank_row->amount}}</td>     
                                            <td nowrap="nowrap">
                                                <a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get('affidavit.edit') }}" onclick="javascript:open_modal({{$bank_row->id}},{{$data}})"
                                                data-bank_name="{{$bank_row->bank_name}}"
                                                data-branch_address="{{$bank_row->branch_address}}"
                                                data-deposit_type="{{$bank_row->deposit_type_id}}"
                                                data-deposit_type_other="{{$bank_row->deposit_type_other}}"
                                                data-account_type="{{$bank_row->account_type}}"
                                                data-joint_account_with="{{$bank_row->joint_account_with}}"
                                                data-deposit_date="{{\Carbon\Carbon::parse($bank_row->deposit_date)->format('Y-m-d')}}"
                                                data-amount="{{$bank_row->amount}}"
                                                data-joint_other_name="{{$bank_row->joint_other_name}}"
                                                data-relation_type_id="{{$bank_row->relation_type_code}}"
                                                data-candidate_id="{{$bank_row->candidate_id}}"
                                                id="edit_deposit{{$bank_row->id}}">
                                            <i class="fa fa-edit"></i> {{Lang::get('affidavit.edit') }}
                                        </a>
										@if(Auth::user()->role_id != '19')
										
                                        <a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get('affidavit.delete') }}" onclick="javascript:delete_deposit({{$bank_row->id}})">
                                            <i class="fa fa-times"></i> {{Lang::get('affidavit.delete') }}
                                        </a> 

										@endif
                                        </tr>
                                        @endif
                                    @endforeach                                                        
                                @endif
								
								@if(Auth::user()->role_id != '19')
								
                                <form>
                                <tr id="deposit{{$dp->relation_type_code}}">
                                    <td>
                                        <textarea col="10" row="5" name="bank_name{{$dp->relation_type_code}}" id="bank_name{{$dp->relation_type_code}}" class="form-control" required="required" onkeypress="return blockSpecialChar_address(event)"></textarea>
                                    </td>
                                    <td>
                                        <textarea col="10" row="5" class="form-control" name="branch_address{{$dp->relation_type_code}}" id="branch_address{{$dp->relation_type_code}}" required="required"onkeypress="return blockSpecialChar_address(event)">  
                                        </textarea>
                                    </td>
                                    <td width="160">
                                        <select class="form-control" name="deposit_type{{$dp->relation_type_code}}" id="deposit_type{{$dp->relation_type_code}}" onchange="javascript:get_deposit_type({{$dp->relation_type_code}});"  required="required">
                                            <option value=""> {{Lang::get('affidavit.select') }}</option>
                                            @if($deposit_type)
                                                @foreach($deposit_type as $row)
                                                    <option value="{{$row->deposit_type_id}}">{{$row->deposit_type}} - {{$row->deposit_type_hi}}</option>
                                                @endforeach
                                            @endif
                                        </select> 
                                        <div id="deposit_div{{$dp->relation_type_code}}" style="display: none;">
                                            <small> {{Lang::get('affidavit.other_deposit_type') }}</small>
                                            <textarea col="10" onkeypress="return blockSpecialChar_address(event)" row="5" class="form-control" name="deposit_other{{$dp->relation_type_code}}" id="deposit_other{{$dp->relation_type_code}}">
                                            </textarea>
                                        </div>
                                    </td>
                                    <td class="w-130">
                                        <select class="form-control"  name="account_type{{$dp->relation_type_code}}" id="account_type{{$dp->relation_type_code}}" onchange="javascript:get_relatives({{$dp->relation_type_code}});"  required="required">
                                            <option value=""> {{Lang::get('affidavit.select') }}</option>
                                            <option value="Individual"> {{Lang::get('affidavit.individual') }}</option>
                                            <option value="Joint"> {{Lang::get('affidavit.joint') }}</option>
                                        </select>
                                        <br>
                                        <div id="joint_div{{$dp->relation_type_code}}" style="display: none;">
                                            <select class="form-control selectOne" name="joint{{$dp->relation_type_code}}[]" id="joint{{$dp->relation_type_code}}" multiple>
                                                @if($data)
                                                    @foreach($data as $rel)
                                                    @if($dp->relation_type_code!=$rel->relation_type_code)
                                                        <option value="{{$rel->relation_type_code}}-{{$rel->name}}">{{$rel->name}}</option>
                                                    @endif
                                                    @endforeach
                                                @endif
                                            </select><br>
                                            <small> {{Lang::get('affidavit.other_joint_holders') }}</small>
                                            <textarea col="10" onkeypress="return blockSpecialChar_address(event)" row="5" class="form-control" name="joint_other{{$dp->relation_type_code}}" id="joint_other{{$dp->relation_type_code}}">
                                            </textarea>
                                        </div>
                                    </td>
                                    <td width="165">
                                        <div class="input-group">
                                        <input type="text" class="form-control datepicker" name="deposit_date{{$dp->relation_type_code}}" id="deposit_date{{$dp->relation_type_code}}" placeholder="Select Date" required="required" readonly>          
                                        <i class="fa fa-calendar input-group-text fa-lg"></i>
                                    </div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="amount{{$dp->relation_type_code}}" id="amount{{$dp->relation_type_code}}" onkeydown="return NumbersOnly(event,this)"  maxlength="12" maxlength="15"  required="required" placeholder="Amount"></td>
                                    <td nowrap="nowrap"> 
                                        <a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get('affidavit.save') }}" id="save{{$ps->id}}" onclick="javascript:save_deposit({{$dp->candidate_id}}, {{$dp->relation_type_code}} )" >
                                        <i class="fa fa-check"></i> {{Lang::get('affidavit.save') }}
                                    </a>
                                    </td>
                                </tr>
                            </form>
							
							@endif
							
                            </tbody>
                            </table>
                        @endforeach
                        @endif
                    </div>
                    <!-- ---------------Deposit------------ -->
                    <!--  Investment In Bonds  -->
                    <div class="accordion_head">{{Lang::get('affidavit.details_of_investement_in_bonds') }}<span class="plusminus">+</span></div>
                    <div class="accordion_body" style="display: none"> 
                        @if(!empty($data))
                        @foreach($data as $insco)
                            <h6 class="text-left pt-2 py-3 text-uppercase">
                            {{$insco->relation_type}} : {{$insco->name}}
                            </h6>
                            <div class="table-responsive">
                            <table id="ins_relative{{$insco->relation_type_code}}" class="table table-striped table-bordered table-hover purpleTable">
                            <thead>
                                <tr>
                                    <th>{{Lang::get('affidavit.company_name') }}</th>
                                    <th>{{Lang::get('affidavit.security_investment_type') }}</th>
                                    <th>{{Lang::get('affidavit.no_of_units_if_applicable') }}</th>
                                    <th>{{Lang::get('affidavit.account_type') }}</th>
                                    <th>{{Lang::get('affidavit.amount') }} (in &#x20b9;)</th>
                                    <th>{{Lang::get('affidavit.action') }}</th>          
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($company_details))
                                    @foreach($company_details as $company_row)
                                    @if($company_row->relation_type_code==$insco->relation_type_code)
                                    <tr id="company{{$company_row->id}}">
                                        <td>{{$company_row->company}}</td>     
                                        <td>{{$company_row->company_investment_type}}<br>
                                            @if(!empty($company_row->company_investment_type_other))
                                                    {{$company_row->company_investment_type_other}}
                                                @endif
                                        </td>     
                                        <td>{{$company_row->number_of_units}}</td>     
                                        <td>{{$company_row->account_type}}
                                            @if($company_row->account_type=="Joint")
                                                {{$company_row->joint_account_with_name}}
                                            @endif
                                        </td>      
                                        <td>{{$company_row->amount}}</td>     
                                        <td nowrap="nowrap">
                                            <a href="javascript:void(0)" class="btn btn-info btm-sm" title="Edit" onclick="javascript:edit_investment({{$company_row->id}},{{$data}})"
                                            data-company="{{$company_row->company}}"
                                            data-company_investment_type_id="{{$company_row->company_investment_type_id}}"
                                            data-company_investment_type_other="{{$company_row->company_investment_type_other}}"data-number_of_units="{{$company_row->number_of_units}}"
                                            data-account_type="{{$company_row->account_type}}"data-joint_account_with="{{$company_row->joint_account_with}}"
                                            data-amount="{{$company_row->amount}}"
                                            data-joint_other_name="{{$company_row->joint_other_name}}"
                                            data-relation_type_id="{{$company_row->relation_type_code}}"
                                            data-candidate_id="{{$company_row->candidate_id}}"
                                            id="edit_invest{{$company_row->id}}">
                                       <i class="fa fa-edit"></i> {{Lang::get('affidavit.edit') }}
                                    </a>
									@if(Auth::user()->role_id != '19')
                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" title="Delete" onclick="javascript:delete_invest({{$company_row->id}})">
                                       <i class="fa fa-times"></i> {{Lang::get('affidavit.delete') }}
                                    </a>  
									@endif
                                    </tr>
                                    @endif
                                    @endforeach                                                        
                                @endif
								
								@if(Auth::user()->role_id != '19')
								
                                <form>
                                <tr id="company_form{{$insco->relation_type_code}}">
                                    <td>
                                        <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="company_name{{$insco->relation_type_code}}" id="company_name{{$insco->relation_type_code}}" required="required"></textarea>
                                    </td>
                                    <td width="290">
                                        <select class="form-control" name="invest_type{{$insco->relation_type_code}}" id="invest_type{{$insco->relation_type_code}}" onchange="javascript:get_invetment_type({{$insco->relation_type_code}});" required="required">
                                            <option value="">{{Lang::get('affidavit.select') }}</option>
                                            @if($company_investment_type)
                                                @foreach($company_investment_type as $ins_row)
                                                    <option value="{{$ins_row->company_investment_type_id}}">{{$ins_row->company_investment_type}}-{{$ins_row->company_investment_type_hi}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div id="invest_div{{$insco->relation_type_code}}" style="display: none;">
                                            <small>{{Lang::get('affidavit.other_investment_type') }}</small><br>
                                            <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control"  name="ins_deposit_other{{$insco->relation_type_code}}" id="ins_deposit_other{{$insco->relation_type_code}}">
                                            </textarea>
                                        </div>
                                    </td>                                                            
                                    <td>
                                        <input type="text" class="form-control" name="number_of_units{{$insco->relation_type_code}}" id="number_of_units{{$insco->relation_type_code}}" onkeydown="return NumbersOnly(event,this)"  maxlength="12" required="required"></td>
                                    <td width="135">
                                        <select class="form-control" name="ins_account_type{{$insco->relation_type_code}}" id="ins_account_type{{$insco->relation_type_code}}" onchange="javascript:get_ins_relatives({{$insco->relation_type_code}});" required="required">
                                            <option value="">{{Lang::get('affidavit.select') }}</option>
                                            <option value="Individual">{{Lang::get('affidavit.individual') }}</option>
                                            <option value="Joint">{{Lang::get('affidavit.joint') }}</option>
                                        </select>
                                        <br>
                                        <div id="joint_ins_div{{$insco->relation_type_code}}" style="display: none;">
                                            <select class="form-control selectOne" name="ins_joint{{$insco->relation_type_code}}[]" id="ins_joint{{$insco->relation_type_code}}" multiple>
                                                @if($data)
                                                    @foreach($data as $rel)
                                                    @if($insco->relation_type_code!=$rel->relation_type_code)
                                                        <option value="{{$rel->relation_type_code}}-{{$rel->name}}">{{$rel->name}}</option>
                                                    @endif
                                                    @endforeach
                                                @endif
                                            </select><br>
                                            <small>{{Lang::get('affidavit.other_joint_holders') }}</small>
                                            <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control"  name="ins_joint_other{{$insco->relation_type_code}}" id="ins_joint_other{{$insco->relation_type_code}}">
                                            </textarea>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="ins_amount{{$insco->relation_type_code}}" id="ins_amount{{$insco->relation_type_code}}" onkeydown="return NumbersOnly(event,this)"  maxlength="12" class="form-control"  required="required"></td>
                                    <td nowrap="nowrap"> 
                                        <a href="javascript:void(0)" class="btn btn-success btn-sm" title="Save" onclick="javascript:save_investment({{$insco->candidate_id}}, {{$insco->relation_type_code}} )" >
                                        <i class="fa fa-check"></i> {{Lang::get('affidavit.save') }}
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
                    <!--  Investment In Bonds  -->

                    <!--  Investment In NSS -->
                    <div class="accordion_head">{{Lang::get('affidavit.details_of_investment_in_nSS_postal_saving_insurance_policies') }} <span class="plusminus">+</span></div>
                    <div class="accordion_body" style="display: none"> 
                        @if(!empty($data))
                        @foreach($data as $save)
                            <h6 class="text-left pt-2 py-3 text-uppercase">
                            {{$save->relation_type}} : {{$save->name}}
                            </h6>
                            <div class="table-responsive">
                            <table id="save_relative{{$save->relation_type_code}}" class="table table-striped table-bordered table-hover purpleTable">
                            <thead>
                                <tr>
                                    <th>{{Lang::get('affidavit.company_name') }}</th>
                                    <th>{{Lang::get('affidavit.saving_type') }}</th>
                                    <th>{{Lang::get('affidavit.account_type') }}</th>
                                    <th>{{Lang::get('affidavit.amount') }} (in &#x20b9;)</th>
                                    <th>{{Lang::get('affidavit.action') }}</th>          
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($saving_details))
                                    @foreach($saving_details as $save_row)
                                    @if($save_row->relation_type_code==$save->relation_type_code)
                                    <tr id="trsave{{$save_row->id}}">
                                        <td>{{$save_row->company}}</td>     
                                        <td>{{$save_row->saving_type}}<br>
                                            @if(!empty($save_row->saving_type_other))
                                                    {{$save_row->saving_type_other}}
                                                @endif
                                        </td>    
                                        <td>{{$save_row->account_type}}
                                            @if($save_row->account_type=="Joint")
                                                {{$save_row->joint_account_with_name}}
                                            @endif
                                        </td>      
                                        <td>{{$save_row->amount}}</td>     
                                        <td nowrap="nowrap">
                                            <a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get('affidavit.edit') }}" onclick="javascript:edit_save({{$save_row->id}},{{$data}})"
                                            data-company="{{$save_row->company}}"
                                            data-saving_type_id="{{$save_row->saving_type_id}}"
                                            data-saving_type_other="{{$save_row->saving_type_other}}"
                                            data-account_type="{{$save_row->account_type}}"data-joint_account_with="{{$save_row->joint_account_with}}"
                                            data-amount="{{$save_row->amount}}"
                                            data-joint_other_name="{{$save_row->joint_other_name}}"
                                            data-relation_type_id="{{$save_row->relation_type_code}}"
                                            data-candidate_id="{{$save_row->candidate_id}}"
                                            id="edit_save{{$save_row->id}}">
                                        <i class="fa fa-edit"></i> {{Lang::get('affidavit.edit') }}
                                    </a>
									@if(Auth::user()->role_id != '19')									
                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" title="Delete" onclick="javascript:delete_save({{$save_row->id}})">
                                        <i class="fa fa-times"></i> {{Lang::get('affidavit.delete') }}
                                    </a>
									@endif
                                    </tr>
                                    @endif
                                    @endforeach                                                        
                                @endif
								
								@if(Auth::user()->role_id != '19')
                                <form>
                                <tr id="nss_form{{$save->relation_type_code}}">
                                    <td>
                                        <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="saving_name{{$save->relation_type_code}}" id="saving_name{{$save->relation_type_code}}" required="required"></textarea>
                                    </td>
                                    <td>
                                        <select class="form-control" name="saving_type{{$save->relation_type_code}}" id="saving_type{{$save->relation_type_code}}" onchange="javascript:get_saving_type({{$save->relation_type_code}});"  required="required">
                                            <option value="">{{Lang::get('affidavit.select') }}</option>
                                            @if($saving_type)
                                                @foreach($saving_type as $save_row)
                                                    <option value="{{$save_row->saving_type_id}}">{{$save_row->saving_type}}-{{$save_row->saving_type_hi}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div id="saving_type_div{{$save->relation_type_code}}" style="display: none;">
                                            <small>{{Lang::get('affidavit.other_saving_type') }}</small><br>
                                            <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="saving_type_other{{$save->relation_type_code}}" id="saving_type_other{{$save->relation_type_code}}">
                                            </textarea>
                                        </div>
                                    </td>
                                    <td>
                                        <select class="form-control"  name="saving_account_type{{$save->relation_type_code}}" id="saving_account_type{{$save->relation_type_code}}" onchange="javascript:get_saving_relatives({{$save->relation_type_code}});"  required="required">
                                            <option value="">{{Lang::get('affidavit.select') }}</option>
                                            <option value="Individual">{{Lang::get('affidavit.individual') }}</option>
                                            <option value="Joint">{{Lang::get('affidavit.joint') }}</option>
                                        </select>
                                        <br>
                                        <div id="joint_saving_div{{$save->relation_type_code}}" style="display: none;">
                                            <select class="form-control selectOne"  name="saving_joint{{$save->relation_type_code}}[]" id="saving_joint{{$save->relation_type_code}}" multiple>
                                                @if($data)
                                                    @foreach($data as $rel)
                                                    @if($save->relation_type_code!=$rel->relation_type_code)
                                                        <option value="{{$rel->relation_type_code}}-{{$rel->name}}">{{$rel->name}}</option>
                                                    @endif
                                                    @endforeach
                                                @endif
                                            </select><br>
                                            <small>{{Lang::get('affidavit.other_joint_holders') }}</small><br>
                                            <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="saving_joint_other{{$save->relation_type_code}}" id="saving_joint_other{{$save->relation_type_code}}">
                                            </textarea>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="saving_amount{{$save->relation_type_code}}" id="saving_amount{{$save->relation_type_code}}"  onkeydown="return NumbersOnly(event,this)" maxlength="12" class="form-control"   required="required" placeholder="Amount"></td>
                                    <td nowrap="nowrap"> 
                                        <a href="javascript:void(0)" class="btn btn-success btn-sm" title="{{Lang::get('affidavit.save') }}" onclick="javascript:save_savings({{$save->candidate_id}}, {{$save->relation_type_code}} )" >
                                       <i class="fa fa-check"></i> {{Lang::get('affidavit.save') }}
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
                    <!--  Investment In NSS  -->
                    <!-- Receivables from debtors  -->
                    <div class="accordion_head"> {{Lang::get('affidavit.receivables_from_debtors') }} <span class="plusminus">+</span></div>
                    <div class="accordion_body" style="display: none"> 
                         @if(!empty($data))
                        @foreach($data as $loan)
                            <h6 class="text-left pt-2 py-3 text-uppercase">
                            {{$loan->relation_type}} : {{$loan->name}}
                            </h6>
                            <table id="loan_relative{{$loan->relation_type_code}}" class="table table-striped table-bordered table-hover purpleTable" >
                            <thead>
                                <tr>
                                    <th>{{Lang::get('affidavit.loan_type') }}</th>
                                    <th>{{Lang::get('affidavit.loan_account_type') }}</th>
                                    <th>{{Lang::get('affidavit.loan_to') }}</th>
                                    <th>{{Lang::get('affidavit.nature_of_loan') }}</th>
                                    <th>{{Lang::get('affidavit.amount') }} (in &#x20b9;)</th>
                                    <th>{{Lang::get('affidavit.action') }}</th>          
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($loan_details))
                                    @foreach($loan_details as $debt)
                                    @if($debt->relation_type_code==$loan->relation_type_code)
                                    <tr id="trloan{{$debt->id}}">    
                                        <td width="160">  
                                            @if($debt->loan_type_other==1)
                                                {{"Short Term"}}
                                            @else
                                                 {{"Long Term"}}
                                            @endif
                                        </td>    
                                        <td>{{$debt->loan_account_type}}
                                            @if($debt->loan_account_type=="Joint")
                                                {{$debt->joint_account_with_name}}
                                            @endif
                                        </td>                                                          
                                        <td>{{$debt->loan_to}}</td>     
                                        <td>{{$debt->nature_of_loan}}</td>     
                                        <td>{{$debt->outstanding_amount}}</td>     
                                        <td nowrap="nowrap">
                                            <a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get('affidavit.edit') }}" onclick="javascript:edit_loan({{$debt->id}},{{$data}})"
                                            data-loan_type_id="{{$debt->loan_type_id}}"
                                            data-loan_type_other="{{$debt->loan_type_other}}"
                                            data-account_type="{{$debt->loan_account_type}}"data-joint_account_with="{{$debt->joint_account_with}}" data-loan_to="{{$debt->loan_to}}" data-nature_of_loan="{{$debt->nature_of_loan}}"
                                            data-amount="{{$debt->outstanding_amount}}"
                                            data-joint_other_name="{{$debt->joint_other_name}}"
                                            data-relation_type_id="{{$debt->relation_type_code}}"
                                            data-candidate_id="{{$debt->candidate_id}}"
                                            id="edit_loan{{$debt->id}}">
                                        <i class="fa fa-edit"></i> {{Lang::get('affidavit.edit') }}
                                    </a>
									@if(Auth::user()->role_id != '19')
									
                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get('affidavit.delete') }}" onclick="javascript:delete_loan({{$debt->id}})">
                                        <i class="fa fa-times"></i> {{Lang::get('affidavit.delete') }}
                                    </a> 

									@endif
									
                                    </tr>
                                    @endif
                                    @endforeach                                                        
                                @endif
								
								@if(Auth::user()->role_id != '19')
                                <form>
                                <tr id="loan_form{{$loan->relation_type_code}}">   
                                    <td width="160">
                                        <select class="form-control" name="loan_type{{$loan->relation_type_code}}" id="loan_type{{$loan->relation_type_code}}" required="required" >
                                            <option value="">{{Lang::get('affidavit.select') }}</option>
                                            <option value="1">{{Lang::get('affidavit.short_term') }}</option>
                                            <option value="2">{{Lang::get('affidavit.long_term') }}</option>                       
                                        </select>
                                        <div id="loan_type_div{{$loan->relation_type_code}}" style="display: none;">
                                            <small>{{Lang::get('affidavit.other_loan_type') }}</small><br>
                                            <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="loan_type_other{{$loan->relation_type_code}}" id="loan_type_other{{$loan->relation_type_code}}">
                                            </textarea>
                                        </div>
                                    </td>
                                    <td>
                                        <select class="form-control" name="loan_account_type{{$loan->relation_type_code}}" id="loan_account_type{{$loan->relation_type_code}}" onchange="javascript:get_loan_relatives({{$loan->relation_type_code}});"  required="required">
                                            <option value="">{{Lang::get('affidavit.select') }}</option>
                                            <option value="Individual">{{Lang::get('affidavit.individual') }}</option>
                                            <option value="Joint">{{Lang::get('affidavit.joint') }}</option>
                                        </select>
                                        <br>
                                        <div id="joint_loan_div{{$loan->relation_type_code}}" style="display: none;">
                                            <select class="form-control selectOne" name="loan_joint_account_with{{$loan->relation_type_code}}[]" id="loan_joint_account_with{{$loan->relation_type_code}}" multiple>
                                                @if($data)
                                                    @foreach($data as $rel)
                                                    @if($loan->relation_type_code!=$rel->relation_type_code)
                                                        <option value="{{$rel->relation_type_code}}-{{$rel->name}}">{{$rel->name}}</option>
                                                    @endif
                                                    @endforeach
                                                @endif
                                            </select><br>
                                            <small>{{Lang::get('affidavit.other_joint') }}</small><br>
                                            <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control"  name="loan_joint_account_with_name{{$loan->relation_type_code}}" id="loan_joint_account_with_name{{$loan->relation_type_code}}">
                                            </textarea>
                                        </div>
                                    </td>                                                    
                                    <td>
                                        <textarea col="10" row="5" class="form-control" name="loan_to{{$loan->relation_type_code}}" id="loan_to{{$loan->relation_type_code}}" onkeypress="return blockSpecialChar_address(event)"  required="required"></textarea>
                                    </td>
                                    <td>
                                        <input type="text" name="nature_of_loan{{$loan->relation_type_code}}" id="nature_of_loan{{$loan->relation_type_code}}" class="form-control"  required="required">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="loan_amount{{$loan->relation_type_code}}" id="loan_amount{{$loan->relation_type_code}}"  onkeydown="return NumbersOnly(event,this)"  maxlength="12" class="form-control" required="required">
                                    </td>
                                    <td nowrap="nowrap"> 
                                        <a href="javascript:void(0)" class="btn btn-success btn-sm" title="{{Lang::get('affidavit.save') }}" onclick="javascript:save_loans({{$loan->candidate_id}}, {{$loan->relation_type_code}} )" >
                                        <i class="fa fa-check"></i> {{Lang::get('affidavit.save') }}
                                    </a>
                                    </td>
                                </tr>
                            </form>
							
							@endif
							
                            </tbody>
                            </table>
                        @endforeach
                        @endif    
                    </div>    
                    <!-- Receivables from debtors  -->
                    <!--  Vehicles  -->
                    <div class="accordion_head">{{Lang::get('affidavit.motor_vehicles_aircraft_yachts_ships') }} <span class="plusminus">+</span></div>
                    <div class="accordion_body" style="display: none"> 
                       @if(!empty($data))
                        @foreach($data as $veh)
                            <h6 class="text-left pt-2 py-3 text-uppercase">
                            {{$veh->relation_type}} : {{$veh->name}}
                            </h6>
                            <div class="table-responsive">
                            <table id="vehicle_relative{{$veh->relation_type_code}}" class="table table-striped table-bordered table-hover purpleTable">
                            <thead>
                                <tr>
                                    <th>{{Lang::get('affidavit.vehicle_type') }}</th>
                                    <th>{{Lang::get('affidavit.make') }}</th>
                                    <th>{{Lang::get('affidavit.registration_no') }}</th>
                                    <th>{{Lang::get('affidavit.year_of_purchase') }}</th>
                                    <th>{{Lang::get('affidavit.amount') }} (in &#x20b9;)</th>
                                    <th>{{Lang::get('affidavit.action') }}</th>          
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($vehicle_details))
                                    @foreach($vehicle_details as $vehicle)
                                    @if($vehicle->relation_type_code==$veh->relation_type_code)
                                    <tr id="trvehicle{{$vehicle->id}}">    
                                        <td>{{$vehicle->vehicle_type}}<br>
                                            @if(!empty($vehicle->vehicle_type_other))
                                                    {{$vehicle->vehicle_type_other}}
                                                @endif
                                        </td>    
                                        <td>{{$vehicle->make}}</td>                              
                                        <td>{{$vehicle->registration_no}}</td>     
                                        <td>{{$vehicle->year_of_purchase}}</td>     
                                        <td>{{$vehicle->amount}}</td>     
                                        <td nowrap="nowrap">
                                            <a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get('affidavit.edit') }}" onclick="javascript:edit_vehicle({{$vehicle->id}})"
                                            data-vehicle_type_id="{{$vehicle->vehicle_type_id}}"
                                            data-vehicle_type_other="{{$vehicle->vehicle_type_other}}" data-make="{{$vehicle->make}}" data-registration_no="{{$vehicle->registration_no}}"data-year_of_purchase="{{$vehicle->year_of_purchase}}"
                                            data-amount="{{$vehicle->amount}}"
                                            data-relation_type_id="{{$vehicle->relation_type_code}}"
                                            data-candidate_id="{{$vehicle->candidate_id}}"
                                            id="edit_vehicle{{$vehicle->id}}">
                                       <i class="fa fa-edit"></i> {{Lang::get('affidavit.edit') }}</a>
									   
									   @if(Auth::user()->role_id != '19')
                                    <a href="javascript:void(0)" title="{{Lang::get('affidavit.delete') }}" class="btn btn-danger btn-sm" onclick="javascript:delete_vehicle({{$vehicle->id}})">
                                        <i class="fa fa-times"></i> {{Lang::get('affidavit.delete') }}</a> 
										@endif
										
                                    </tr>
                                    @endif
                                    @endforeach                                                        
                                @endif
								
								@if(Auth::user()->role_id != '19')
                                <form>
                                <tr id="vehicle_form{{$veh->relation_type_code}}">
                                    <td width="290">
                                        <select class="form-control" name="vehicle_type{{$veh->relation_type_code}}" id="vehicle_type{{$veh->relation_type_code}}" onchange="javascript:get_vehicle_type({{$veh->relation_type_code}});" required="required">
                                            <option value=""> {{Lang::get('affidavit.select') }}</option>
                                            @if($vehicle_type)
                                                @foreach($vehicle_type as $veh_type_row)
                                                    <option value="{{$veh_type_row->vehicle_type_id}}">{{$veh_type_row->vehicle_type}}-{{$veh_type_row->vehicle_type_hi}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div id="vehicle_type_div{{$veh->relation_type_code}}" style="display: none;">
                                            <small>{{Lang::get('affidavit.other_vehicle_type') }}</small><br>
                                            <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="vehicle_type_other{{$veh->relation_type_code}}" id="vehicle_type_other{{$veh->relation_type_code}}">
                                            </textarea>
                                        </div>
                                    </td>
                                    <td>
                                        <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="make{{$veh->relation_type_code}}" id="make{{$veh->relation_type_code}}"  required="required"></textarea>
                                    </td>                                                    
                                    <td>
                                        <input type="text" name="registration_no{{$veh->relation_type_code}}" id="registration_no{{$veh->relation_type_code}}" class="form-control" required="required">
                                    </td>
                                    <td>                                       
										<select class="form-control" name="year_of_purchase{{$veh->relation_type_code}}" id="year_of_purchase{{$veh->relation_type_code}}"  required="required">
                                            <option value=""> {{Lang::get('affidavit.select') }}</option>
                                                @foreach( range( date('Y'), 1950 ) as $i )
                                                    <option value="{{$i}}">{{$i}}</option>
                                                @endforeach 
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="vehicle_amount{{$veh->relation_type_code}}" id="vehicle_amount{{$veh->relation_type_code}}"  onkeydown="return NumbersOnly(event,this)" maxlength="12" class="form-control" maxlength="15" required="required">
                                    </td>
                                    <td nowrap="nowrap"> 
                                        <a href="javascript:void(0)" class="btn btn-success btn-sm" title="{{Lang::get('affidavit.save') }}" onclick="javascript:save_vehicle({{$veh->candidate_id}}, {{$veh->relation_type_code}} )" ><i class="fa fa-check"></i> {{Lang::get('affidavit.save') }}</a>
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
                    <!--  Vehicles  -->
                    <!-- Jewellery  -->
                    <div class="accordion_head">{{Lang::get('affidavit.jewellery_bullion_and_valuable_thing') }}<span class="plusminus">+</span></div>
                    <div class="accordion_body" style="display: none">
                        @if(!empty($data))
                        @foreach($data as $jew)
                            <h6 class="text-left pt-2 py-3 text-uppercase">
                            {{$jew->relation_type}} : {{$jew->name}}
                            </h6>
                            <div class="table-responsive">
                            <table id="jewellery_relative{{$jew->relation_type_code}}" class="table table-striped table-bordered table-hover purpleTable" >
                            <thead>
                                <tr>
                                    <th>{{Lang::get('affidavit.valuable_things_type') }}</th>
                                    <th>{{Lang::get('affidavit.weight') }}</th>
                                    <th>{{Lang::get('affidavit.amount') }} (in &#x20b9;)</th>
                                    <th>{{Lang::get('affidavit.action') }}</th>          
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($jewellery_details))
                                    @foreach($jewellery_details as $jewel)
                                    @if($jewel->relation_type_code==$jew->relation_type_code)
                                    <tr id="trjewellery{{$jewel->id}}">    
                                        <td>{{$jewel->valuable_type}}<br>
                                            @if(!empty($jewel->valuable_type_other))
                                                    {{$jewel->valuable_type_other}}
                                                @endif
                                        </td>    
                                        <td>{{$jewel->weight}} 
                                            @if(!empty($jewel->valuable_weight))
                                                @if($jewel->weight_unit_id!=5)
                                                    {{$jewel->valuable_weight}}
                                                @else
                                                    {{$jewel->weight_unit_other}}
                                                @endif
                                            @endif
                                        </td>   
                                        <td>{{$jewel->amount}}</td>     
                                        <td nowrap="nowrap">
                                            <a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get('affidavit.edit') }}" onclick="javascript:edit_jewellery({{$jewel->id}})"
                                            data-valuable_type_id="{{$jewel->valuable_type_id}}"
                                            data-valuable_type_other="{{$jewel->valuable_type_other}}" data-weight="{{$jewel->weight}}" data-weight_unit_id="{{$jewel->weight_unit_id}}"data-weight_unit_other="{{$jewel->weight_unit_other}}"
                                            data-amount="{{$jewel->amount}}"
                                            data-relation_type_id="{{$jewel->relation_type_code}}"
                                            data-candidate_id="{{$jewel->candidate_id}}"
                                            id="edit_jewellery{{$jewel->id}}">
                                        <i class="fa fa-edit"></i>{{Lang::get('affidavit.edit') }}</a>
										@if(Auth::user()->role_id != '19')
											<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get('affidavit.delete') }}" onclick="javascript:delete_jewellery({{$jewel->id}})"><i class="fa fa-times"></i> {{Lang::get('affidavit.delete') }}</a> 
										@endif
                                    </tr>
                                    @endif
                                    @endforeach    
                                @endif
								
								@if(Auth::user()->role_id != '19')
                                <form>
                                <tr id="jewellery_form{{$jew->relation_type_code}}">
                                    <td>
                                        <select class="form-control" name="jewel_type{{$jew->relation_type_code}}" id="jewel_type{{$jew->relation_type_code}}" onchange="javascript:get_jewel_type({{$jew->relation_type_code}});" required="required">
                                            <option value="">{{Lang::get('affidavit.select') }}</option>
                                            @if($valuable_things)
                                                @foreach($valuable_things as $jew_type_row)
                                                    <option value="{{$jew_type_row->valuable_type_id}}">{{$jew_type_row->valuable_type}}-{{$jew_type_row->valuable_type_hi}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div id="jewellery_type_div{{$jew->relation_type_code}}" style="display: none;">
                                            <label>{{Lang::get('affidavit.other_jewellery_type') }}</label><br>
                                            <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="val_type_other{{$jew->relation_type_code}}" id="val_type_other{{$jew->relation_type_code}}">
                                            </textarea>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <input type="text" name="weight_value{{$jew->relation_type_code}}" id="weight_value{{$jew->relation_type_code}}" onkeydown="return NumbersOnly(event,this)" class="form-control" maxlength="12"  required="required">
                                            </div>
                                            <div class="col-sm-4">
                                                <select class="form-control" name="val_weight{{$jew->relation_type_code}}" id="val_weight{{$jew->relation_type_code}}" onchange="javascript:get_val_weight_type({{$jew->relation_type_code}});" required="required">
                                                    <option value="">{{Lang::get('affidavit.select') }}</option>
                                                    @if($valuable_weight)
                                                        @foreach($valuable_weight as $jew_weight_row)
                                                            <option value="{{$jew_weight_row->valuable_weight_id}}">{{$jew_weight_row->valuable_weight}}-{{$jew_weight_row->valuable_weight_hi}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <div id="val_weight_div{{$jew->relation_type_code}}" style="display: none;">
                                                    <input type="text" name="other_valuable_weight{{$jew->relation_type_code}}" id="other_valuable_weight{{$jew->relation_type_code}}" class="form-control" placeholder="Other Weight Type">   
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="jewellery_amount{{$jew->relation_type_code}}" id="jewellery_amount{{$jew->relation_type_code}}" onkeydown="return NumbersOnly(event,this)" maxlength="12" class="form-control" required="required">
                                    </td>
                                    <td> 
                                        <a href="javascript:void(0)" class="btn btn-success btn-sm" title="{{Lang::get('affidavit.save') }}" onclick="javascript:save_jewellery({{$jew->candidate_id}}, {{$jew->relation_type_code}} )" ><i class="fa fa-check"></i>{{Lang::get('affidavit.save') }}</a>
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
                    <!-- Jewellery  -->
                    <!-- ---------------Any Other Movable Assets------------ -->
                    <div class="accordion_head">{{Lang::get('affidavit.any_other_movable_assets') }}<span class="plusminus">+</span></div>
                    <div class="accordion_body" style="display: none">
                    @if(!empty($data))
                    @foreach($data as $other)
                        <h6 class="text-left pt-2 py-3 text-uppercase">
                        {{$other->relation_type}} : {{$other->name}}
                        </h6>
                        <table id="other_relative{{$other->relation_type_code}}" class="table table-striped table-bordered table-hover purpleTable" >
                        <thead>
                            <tr>
                                <th>{{Lang::get('affidavit.asset_type') }}</th>
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
                                    <td>{{$other_row->asset_type}}</td>    
                                    <td>{{$other_row->brief_details}}</td>   
                                    <td>{{$other_row->amount}}</td>     
                                    <td nowrap="nowrap">
                                        <a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get('affidavit.edit') }}" onclick="javascript:edit_other({{$other_row->id}})"
                                        data-asset_type="{{$other_row->asset_type}}"
                                        data-brief_details="{{$other_row->brief_details}}" data-amount="{{$other_row->amount}}"
                                        data-relation_type_id="{{$other_row->relation_type_code}}"
                                        data-candidate_id="{{$other_row->candidate_id}}"
                                        id="edit_other{{$other_row->id}}">
                                    <i class="fa fa-edit"></i>{{Lang::get('affidavit.edit') }}</a>
									@if(Auth::user()->role_id != '19')
										<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get('affidavit.delete') }}" onclick="javascript:delete_other({{$other_row->id}})"><i class="fa fa-times"></i> {{Lang::get('affidavit.delete') }}</a>
									@endif
                                </tr>
                                @endif
                                @endforeach                                                     
                            @endif
							
							@if(Auth::user()->role_id != '19')
                            <form>
                            <tr id="other_form{{$other->relation_type_code}}">
                                <td>
                                    <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="asset_type{{$other->relation_type_code}}" id="asset_type{{$other->relation_type_code}}" required="required">
                                    </textarea>
                                </td>
                                <td>
                                    <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="brief_details{{$other->relation_type_code}}" id="brief_details{{$other->relation_type_code}}" required="required">
                                    </textarea>
                                </td>
                                <td>
                                    <input type="text" name="other_amount{{$other->relation_type_code}}" id="other_amount{{$other->relation_type_code}}" onkeydown="return NumbersOnly(event,this)"  maxlength="12" required="required" class="form-control">
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
                    @endforeach
                    @endif    
                    </div>    
                   
                </div>
                <div class="card-footer footerSection">
                    <div class="row">
                        <div class="col-12">
                            <a href="{{url($menu_action.'affidavit/pending-criminal-cases') }}" class="backBtn float-left">{{Lang::get('affidavit.back') }}</a>
                           
							 <a href="{{url($menu_action.'immovable-assets')}}" class="nextBtn float-right">
							 
							 {{Lang::get('affidavit.save') }} &amp; {{Lang::get('affidavit.next') }}</a>

                             <a href="{{url()->previous() }}" class="cencelBtn mr-2 float-right">{{Lang::get('affidavit.cancel') }}</a>&nbsp; &nbsp; &nbsp;
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</section>
</main>
<!-- Deposit Edit Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.edit_deposit_details') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form id="modal_deposit_form">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.bank_company_name') }}:</label>
                    <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" name="modal_bank_name" id="modal_bank_name" required="required"></textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.branch_address') }}:</label>
                    <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" name="modal_branch_address" id="modal_branch_address"  required="required"></textarea>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.deposit_type') }}</label>
                    <select class="form-control" name="modal_deposit_type" id="modal_deposit_type" onchange="javascript:modal_get_deposit_type();" required="required">
                    <option value="">{{Lang::get('affidavit.select') }}</option>
                    @if($deposit_type)
                        @foreach($deposit_type as $row)
                            <option value="{{$row->deposit_type_id}}">{{$row->deposit_type}}-{{$row->deposit_type_hi}}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
            </div>
            <div class="col-md-6">                    
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.account_type') }}</label>
                    <select class="form-control" name="modal_account_type" id="modal_account_type" onchange="javascript:modal_get_relatives()" required="required">
                    <option value="">{{Lang::get('affidavit.select') }}</option>
                    <option value="Individual">{{Lang::get('affidavit.individual') }}</option>
                    <option value="Joint">{{Lang::get('affidavit.joint') }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6" id="modal_deposit_div" style="display: none;">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.deposit_type') }}</label>
                    <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" name="modal_deposit_other" id="modal_deposit_other"></textarea>
                </div>
            </div>
        </div>
        <div id="modal_account_type_div" style="display: none;">                
            <div class="row">
                <div class="col-md-6">                 
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.account_type') }}</label>
                    <select class="form-control " name="modal_joint_other[]" id="modal_joint_other" multiple>
                    </select>
                    <script type="text/javascript">
                        $('#modal_joint_other').multiselect();
                    </script>
                </div>
            </div>
            <div class="col-md-6">                 
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.other_joint_holders') }}</label>
                    <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="modal_joint_other_name" id="modal_joint_other_name"></textarea>
                </div>
            </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.date_of_deposit') }}</label>
                    <input type="text" class="form-control datepicker" readonly name="modal_deposit_date" id="modal_deposit_date" required="required">
					<i class="fa fa-calendar calender-model input-group-text fa-lg"></i> 
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.amount') }} (in &#x20b9;)</label>
                    <input type="text" class="form-control" name="modal_amount" id="modal_amount" onkeydown="return NumbersOnly(event,this)" maxlength="12"  required="required">
                </div>
            </div>
        </div>
        <input type="hidden" name="modal_cand_id" id="modal_cand_id">
        <input type="hidden" name="modal_rel_id" id="modal_rel_id">
        <input type="hidden" name="modal_bank_id" id="modal_bank_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.close') }}</button>
    <button type="button" class="btn btn-primary" onclick="javascript:update_deposit()">{{Lang::get('affidavit.update') }}</button>
    </div>
    </div>
    </div>
</div>
<!-- Deposit Edit Modal -->
<!-- Deposit Delete Modal -->
<div class="modal fade" id="deleteDepositModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.delete_deposit_details') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form>
        <h5>{{Lang::get('affidavit.are_you_sure_to_delete_this_entry') }}</h5>
        <input type="hidden" name="modal_delete_bank_id" id="modal_delete_bank_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.no') }}</button>
    <button type="button" class="btn btn-primary" onclick="javascript:delete_deposit_entry()">{{Lang::get('affidavit.yes') }}</button>
    </div>
    </div>
    </div>
</div>
<!-- Deposit Delete Modal -->

<!-- Investment Edit Modal -->
<div class="modal fade" id="investModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.edit_investments_in_bonds_mutual_funds') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form id="form_investModal">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.company_name') }}:</label>
                    <textarea  class="form-control" col="10" onkeypress="return blockSpecialChar_address(event)" row="5" name="modal_bank_name" id="modal_company_name" required="required"></textarea>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.no_of_units_if_applicable') }}</label>
                    <input  class="form-control" type="text" name="modal_number_of_units" id="modal_number_of_units" onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.security_investment_type') }}</label>
                    <select class="form-control" name="modal_invest_type" id="modal_invest_type" onchange="javascript:get_modal_invetment_type();" required="required">
                    <option value="">{{Lang::get('affidavit.select') }}</option>
                    @if($company_investment_type)
                        @foreach($company_investment_type as $ins_row)
                            <option value="{{$ins_row->company_investment_type_id}}">{{$ins_row->company_investment_type}}-{{$ins_row->company_investment_type_hi}}</option>
                        @endforeach
                    @endif
                </select>
                </div>
            </div>
            <div class="col-md-6">                    
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.account_type') }}</label>
                    <select class="form-control" name="modal_ins_account_type" id="modal_ins_account_type" onchange="javascript:modal_get_investment_relatives()" required="required">
                    <option value="">{{Lang::get('affidavit.select') }}</option>
                    <option value="Individual">{{Lang::get('affidavit.individual') }}</option>
                    <option value="Joint">{{Lang::get('affidavit.joint') }}</option>
                    </select>
                </div>
            </div>        
        </div>

        <div class="row">
            <div class="col-md-6" id="modal_investment_div" style="display: none;">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.other_security_investment_type') }}</label>
                    <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" name="modal_ins_deposit_other" id="modal_ins_deposit_other"></textarea>
                </div>
            </div>
        </div>
        <div id="modal_investment_account_type_div" style="display: none;">                
            <div class="row">
                <div class="col-md-6">                 
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.account_type') }}</label>
                    <select class="form-control" name="modal_ins_joint_other[]" id="modal_ins_joint_other" multiple>
                    </select>
                </div>
            </div>
            <div class="col-md-6">                 
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.other_joint_holders') }}</label>
                    <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="modal_ins_joint_other_name" id="modal_ins_joint_other_name"></textarea>
                </div>
            </div>
            </div>
        </div>
        <div class="row"> 
             <div class="col-md-6">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.amount') }} (in &#x20b9;)</label>
                    <input  class="form-control" type="text" name="modal_amount" id="modal_ins_amount" required="required" onkeydown="return NumbersOnly(event,this)"  maxlength="12">
                </div>
            </div>
        </div>
        <input type="hidden" name="modal_ins_cand_id" id="modal_ins_cand_id">
        <input type="hidden" name="modal_ins_rel_id" id="modal_ins_rel_id">
        <input type="hidden" name="modal_ins_company_id" id="modal_ins_company_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.close') }}</button>
    <button type="button" class="btn btn-primary" onclick="javascript:update_investment()">{{Lang::get('affidavit.update') }}</button>
    </div>
    </div>
    </div>
</div>
<!-- Investment Edit Modal -->

<!-- Investment Delete Modal -->
<div class="modal fade" id="deleteInvestModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.delete_investment_details') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form>
        <h5>{{Lang::get('affidavit.are_you_sure_to_delete_this_entry') }}</h5>
        <input type="hidden" name="modal_delete_company_id" id="modal_delete_company_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.no') }}</button>
    <button type="button" class="btn btn-primary" onclick="javascript:delete_invest_entry()">{{Lang::get('affidavit.yes') }}</button>
    </div>
    </div>
    </div>
</div>
<!-- Investment Delete Modal -->


<!-- Saving Edit Modal -->
<div class="modal fade" id="savingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.edit_investment_in_nSS_postal_saving_insurance_policies') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form id="form_savingModal">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.company_name') }}:</label>
                    <textarea  class="form-control" col="10" onkeypress="return blockSpecialChar_address(event)" row="5" name="modal_saving_name" id="modal_saving_name" required="required"></textarea>
                </div>
            </div>
            
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.saving_type') }}</label>
                    <select class="form-control" name="modal_saving_type" id="modal_saving_type" onchange="javascript:get_modal_saving_type();"  required="required">
                    <option value="">{{Lang::get('affidavit.select') }}</option>
                    @if($saving_type)
                        @foreach($saving_type as $save_row)
                            <option value="{{$save_row->saving_type_id}}">{{$save_row->saving_type}}-{{$save_row->saving_type_hi}}</option>
                        @endforeach
                    @endif
                </select>
                </div>
            </div>
            <div class="col-md-6">                    
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.account_type') }}</label>
                    <select class="form-control" name="modal_saving_account_type" id="modal_saving_account_type" onchange="javascript:get_modal_saving_relatives()"  required="required">
                    <option value="">{{Lang::get('affidavit.select') }}</option>
                    <option value="Individual">{{Lang::get('affidavit.individual') }}</option>
                    <option value="Joint">{{Lang::get('affidavit.joint') }}</option>
                    </select>
                </div>
            </div>            
        </div>

        <div id="modal_saving_div" style="display: none;">
            <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.yes') }}</label>
                        <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="modal_saving_type_other" id="modal_saving_type_other"></textarea>
                    </div>
                </div>      
            </div>
        </div>

        <div id="modal_saving_account_type_div" style="display: none;">                
            <div class="row">
                <div class="col-md-6">                 
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.account_type') }}</label>
                    <select class="form-control" name="modal_save_joint_other[]" id="modal_save_joint_other" multiple>
                    </select>
                </div>
            </div>
            <div class="col-md-6">                 
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.other_joint_holders') }}</label>
                    <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="modal_save_joint_other_name" id="modal_save_joint_other_name"></textarea>
                </div>
            </div>
            </div>
        </div>
        <div class="row"> 
             <div class="col-md-6">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.amount') }} (in &#x20b9;)</label>
                    <input  class="form-control" type="text" name="modal_save_amount" id="modal_save_amount" onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
                </div>
            </div>
        </div>
        <input type="hidden" name="modal_save_cand_id" id="modal_save_cand_id">
        <input type="hidden" name="modal_save_rel_id" id="modal_save_rel_id">
        <input type="hidden" name="modal_save_save_id" id="modal_save_save_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.close') }}</button>
    <button type="button" class="btn btn-primary" onclick="javascript:update_saving()">{{Lang::get('affidavit.update') }}</button>
    </div>
    </div>
    </div>
</div>
<!-- Saving Edit Modal -->

<!-- Saving Delete Modal -->
<div class="modal fade" id="deleteSavingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.delete_investment_in_nSS_postal_saving_insurance_policies') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form>
        <h5>{{Lang::get('affidavit.are_you_sure_to_delete_this_entry') }}</h5>
        <input type="hidden" name="modal_delete_saving_id" id="modal_delete_saving_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.no') }}</button>
    <button type="button" class="btn btn-primary" onclick="javascript:delete_save_entry()">{{Lang::get('affidavit.yes') }}</button>
    </div>
    </div>
    </div>
</div>
<!-- Saving Delete Modal -->


<!-- Loan Edit Modal -->
<div class="modal fade" id="loanModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.edit_receivables_from_debtors') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form id="form_loanModal">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.loan_type') }}</label>
                    <select class="form-control" name="modal_loan_type" id="modal_loan_type" required="required">
                    <option value="">{{Lang::get('affidavit.select') }}</option>
                    <option value="1">{{Lang::get('affidavit.short_term') }}</option>
                    <option value="2">{{Lang::get('affidavit.long_term') }}</option>
                </select>
                </div>
            </div>
            <div class="col-md-6">                    
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.loan_account_type') }}</label>
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
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.loan_to') }}:</label>
                    <textarea  class="form-control" onkeypress="return blockSpecialChar_address(event)" col="10" row="5" name="modal_loan_to" id="modal_loan_to" required="required"></textarea>
                </div>
            </div> 
            <div class="col-md-6">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.nature_of_loan') }}</label>
                    <input  class="form-control" type="text" name="modal_nature_of_loan" id="modal_nature_of_loan" required="required" onkeypress="return blockSpecialChar_address(event)">
                </div>
            </div>           
        </div>

        <div id="modal_loan_div" style="display: none;">
            <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.other_loan_type') }}</label>
                        <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="modal_loan_type_other" id="modal_loan_type_other"></textarea>
                    </div>
                </div>      
            </div>
        </div>

        <div id="modal_loan_account_type_div" style="display: none;">                
            <div class="row">
                <div class="col-md-6">                 
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.account_type') }}</label>
                    <select class="form-control" name="modal_loan_joint_other[]" id="modal_loan_joint_other" multiple>
                    </select>
                </div>
            </div>
            <div class="col-md-6">                 
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.other_joint_holders') }}</label>
                    <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="modal_loan_joint_other_name" id="modal_loan_joint_other_name"></textarea>
                </div>
            </div>
            </div>
        </div>
        <div class="row"> 
             <div class="col-md-6">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.amount') }} (in &#x20b9;)</label>
                    <input  class="form-control" type="text" name="modal_loan_amount" id="modal_loan_amount" onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
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
    <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.delete_receivables_from_debtors') }}</h5>
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

<!-- Vehicle Edit Modal -->
<div class="modal fade" id="vehicleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.edit_motor_vehicles_aircraft_yachts_ships') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form id="form_vehicleModal">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{Lang::get('affidavit.loan_type') }}</label>
                    <select class="form-control" name="modal_vehicle_type" id="modal_vehicle_type" onchange="javascript:get_modal_vehicle_type();" required="required">
                    <option value="">{{Lang::get('affidavit.select') }}</option>
                    @if($vehicle_type)
                        @foreach($vehicle_type as $veh_type_row)
                            <option value="{{$veh_type_row->vehicle_type_id}}">{{$veh_type_row->vehicle_type}}-{{$veh_type_row->vehicle_type_hi}}</option>
                        @endforeach
                    @endif
                </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{Lang::get('affidavit.make') }}:</label>
                    <textarea  class="form-control" col="10" onkeypress="return blockSpecialChar_address(event)" row="5" name="modal_make" id="modal_make" required="required"></textarea>
                </div>
            </div>           
        </div>
        <div class="row">
             <div class="col-md-6">
                <div class="form-group">
                    <label >{{Lang::get('affidavit.registration_no') }}</label>
                    <input  class="form-control" type="text" name="modal_registration_no" id="modal_registration_no"  required="required" onkeypress="return blockSpecialChar_address(event)">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.year_of_purchase') }}</label>
                    <input  class="form-control" type="text" name="modal_year_of_purchase" id="modal_year_of_purchase" onkeydown="return NumbersOnly(event,this)" maxlength="4"  required="required">
                </div>
            </div>           
        </div>

        <div id="modal_vehicle_div" style="display: none;">
            <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.other_vehicle_type') }}</label>
                        <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="modal_vehicle_type_other" id="modal_vehicle_type_other" ></textarea>
                    </div>
                </div>      
            </div>
        </div>
        <div class="row"> 
             <div class="col-md-6">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.amount') }} (in &#x20b9;)</label>
                    <input  class="form-control" type="text" name="modal_vehicle_amount" id="modal_vehicle_amount" onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
                </div>
            </div>
        </div>
        <input type="hidden" name="modal_loan_cand_id" id="modal_vehicle_cand_id">
        <input type="hidden" name="modal_loan_rel_id" id="modal_vehicle_rel_id">
        <input type="hidden" name="modal_vehicle_id" id="modal_vehicle_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.close') }}</button>
    <button type="button" class="btn btn-primary" onclick="javascript:update_vehicle()">{{Lang::get('affidavit.update') }}</button>
    </div>
    </div>
    </div>
</div>
<!-- Vehicle Edit Modal -->

<!-- Vehicle Delete Modal -->
<div class="modal fade" id="deleteVehicleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.delete_motor_vehicles_aircraft_yachts_ships') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form>
        <h5>{{Lang::get('affidavit.are_you_sure_to_delete_this_entry') }}</h5>
        <input type="hidden" name="modal_delete_vehicle_id" id="modal_delete_vehicle_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.no') }}</button>
    <button type="button" class="btn btn-primary" onclick="javascript:delete_vehicle_entry()">{{Lang::get('affidavit.yes') }}</button>
    </div>
    </div>
    </div>
</div>
<!-- Vehicle Delete Modal -->

<!-- Jewellery Edit Modal -->
<div class="modal fade" id="jewelleryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.edit_jewellery_bullion_and_valuable_thing') }} </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form id="form_jewelleryModal">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label>{{Lang::get('affidavit.valuable_things_type') }}</label>
                    <select class="form-control" name="modal_jewel_type" id="modal_jewel_type" onchange="javascript:get_modal_jewel_type();" required="required">
                    <option value="">{{Lang::get('affidavit.select') }}</option>
                    @if($valuable_things)
                        @foreach($valuable_things as $jew_type_row)
                            <option value="{{$jew_type_row->valuable_type_id}}">{{$jew_type_row->valuable_type}}-{{$jew_type_row->valuable_type_hi}}</option>
                        @endforeach
                    @endif
                </select>
                </div>
            </div>
            <div class="col-sm-6">
                <div id="modal_jewellery_type_div" style="display: none;">
                    <div class="form-group">
                        <label>{{Lang::get('affidavit.other_jewellery_type') }}</label>
                        <textarea col="10" row="5" onkeypress="return blockSpecialChar_address(event)" class="form-control" name="modal_val_type_other" id="modal_val_type_other"></textarea>
                    </div>
                </div> 
            </div> 
        </div>

        <div class="row">  
            <div class="col-sm-3">
                <div class="form-group">
                    <label>{{Lang::get('affidavit.weight') }}:</label>
                    <input  class="form-control" type="text"  name="modal_weight_value" id="modal_weight_value"  onkeydown="return NumbersOnly(event,this)" maxlength="10" required="required" >
                </div>
            </div>           
            <div class="col-sm-3">
                <div class="form-group">
                    <label>{{Lang::get('affidavit.unit') }}:</label>
                     <select class="form-control" name="modal_val_weight" id="modal_val_weight" onchange="javascript:get_modal_val_weight_type();" required="required">
                        <option value="">{{Lang::get('affidavit.select') }}</option>
                        @if($valuable_weight)
                            @foreach($valuable_weight as $jew_weight_row)
                                <option value="{{$jew_weight_row->valuable_weight_id}}">{{$jew_weight_row->valuable_weight}}-{{$jew_weight_row->valuable_weight_hi}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>            
            <div class="col-sm-6">
                <div id="modal_val_weight_div" style="display: none;">
                    <div class="form-group">
                        <label>{{Lang::get('affidavit.other_unit') }}:</label>
                         <input type="text" class="form-control" name="modal_other_valuable_weight" id="modal_other_valuable_weight" class="form-control" placeholder="Other Weight Type" onkeypress="return blockSpecialChar_address(event)">
                    </div>
                </div> 
            </div>      
        </div>
        <div class="row"> 
             <div class="col-sm-6">
                <div class="form-group">
                    <label>{{Lang::get('affidavit.amount') }} (in &#x20b9;)</label>
                    <input  class="form-control" type="text" name="modal_jewellery_amount" id="modal_jewellery_amount" onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
                </div>
            </div>
        </div>
        <input type="hidden" name="modal_jewellery_cand_id" id="modal_jewellery_cand_id">
        <input type="hidden" name="modal_jewellery_rel_id" id="modal_jewellery_rel_id">
        <input type="hidden" name="modal_jewellery_id" id="modal_jewellery_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.close') }}</button>
    <button type="button" class="btn btn-primary" onclick="javascript:update_jewellery()">{{Lang::get('affidavit.update') }}</button>
    </div>
    </div>
    </div>
</div>
<!-- Jewellery Edit Modal -->

<!-- Jewellery Delete Modal -->
<div class="modal fade" id="deleteJewelleryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.delete_jewellery_bullion_and_valuable_thing') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form>
        <h5>{{Lang::get('affidavit.are_you_sure_to_delete_this_entry') }}</h5>
        <input type="hidden" name="modal_delete_jewellery_id" id="modal_delete_jewellery_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.no') }}</button>
    <button type="button" class="btn btn-primary" onclick="javascript:delete_jewellery_entry()">{{Lang::get('affidavit.yes') }}</button>
    </div>
    </div>
    </div>
</div>
<!-- Jewellery Delete Modal -->


<!-- Other Asset Edit Modal -->
<div class="modal fade" id="otherModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.edit_any_other_movable_assets') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form id="form_otherModal">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.asset_type') }}</label>
                    <textarea col="10" row="5" class="form-control" name="modal_asset_type" id="modal_asset_type" required="required" onkeypress="return blockSpecialChar_address(event)"></textarea>
                </div> 
            </div> 
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.brief_details') }}</label>
                    <textarea col="10" row="5" class="form-control" name="modal_brief_details" id="modal_brief_details" required="required" onkeypress="return blockSpecialChar_address(event)"></textarea>
                </div> 
            </div> 
        </div>
        <div class="row"> 
             <div class="col-sm-6">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.amount') }} (in &#x20b9;)</label>
                    <input  class="form-control" type="text" name="modal_other_amount" id="modal_other_amount" onkeydown="return NumbersOnly(event,this)" maxlength="12" required="required">
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
    <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.delete_any_other_movable_assets') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form>
        <h5>{{Lang::get('affidavit.are_you_sure_to_delete_this_entry') }}</h5>
        <input type="hidden" class="form-control" name="modal_delete_other_id" id="modal_delete_other_id">
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

@endsection @section('script')
<!-- <script type="text/javascript" src="{{ asset('admintheme/js/jquery-ui.js') }}"></script> -->
<script>
function NumbersOnly(evt,obj) {
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
<!-- Cash In Hand -->
<script type="text/javascript">
function edit_cash(id)
{
    if(id)
    {
        $("#edit"+id).css("display", "none");
        $("#update"+id).css("display", "block");
        $("#cash"+id).removeAttr("disabled");
        $("#cash"+id).focus();
    }
}

function update_cash(id, cand_id, rel_type_id)
{
    if(id && cand_id && rel_type_id)
    {
        var cash = $("#cash"+id).val();
        console.log(validate("cashinhand"+id));
        if(validate("cashinhand"+id))
        {
            $("#edit"+id).css("display", "block");
            $("#update"+id).css("display", "none");
            $.ajax({
                url: "{{ url($menu_action.'update_cash') }}",
                type: 'GET',
                data: {id:id, cand_id:cand_id, rel_type_id:rel_type_id, cash:cash},            
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success:function(data){
                    if(data==1)
                        $("#cash"+id).attr('disabled', 'disabled');
                }
            });
        }
    }
}
</script>
<link rel="stylesheet" type="text/css" href="{!! url('admintheme/css/jquery-ui.css') !!}">
<script type="text/javascript" src="{!! url('admintheme/js/jquery-ui.js') !!}"></script>
<script type="text/javascript" src="{!! url('js/jquery.validate.min.js') !!}"></script>
<script type="text/javascript" src="{{ asset('affidavit/js/remove_special_character.js') }}"></script>
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
<!-- Cash In Hand -->

<!-- Deposit In Banks -->
<script type="text/javascript">
function get_relatives(rel_id)
{
    if(rel_id)
    {
        $("#joint"+rel_id).val('');
        $("#joint_other"+rel_id).val('');
        var account_type = $("#account_type"+rel_id).val();
        if(account_type=="Joint")
        {
            $("#joint_div"+rel_id).css("display", "block");
            //$("#joint"+rel_id).attr("required", "required");
            //$("#joint_other"+rel_id).attr("required", "required");
        }
        else
        {
            $("#joint_div"+rel_id).css("display", "none");
            //$("#joint"+rel_id).removeAttr("required");
            //$("#joint_other"+rel_id).removeAttr("required");
        }
    }
}
</script>
<script type="text/javascript">
function get_deposit_type(rel_id)
{
    if(rel_id)
    {
        $("#deposit_other"+rel_id).val('');
        var deposit_type = $("#deposit_type"+rel_id).val();
        if(deposit_type==5)
        {
            $("#deposit_div"+rel_id).css("display", "block");
            $("#deposit_other"+rel_id).attr("required", "required");
        }
        else
        {
            $("#deposit_div"+rel_id).css("display", "none");
            $("#deposit_other"+rel_id).removeAttr("required");
        }            
    }
}
</script>
<script type="text/javascript">
function save_deposit(cand_id, rel_id)
{
    var bank_name = $("#bank_name"+rel_id).val();
    var branch_address = $("#branch_address"+rel_id).val();
    var deposit_type = $("#deposit_type"+rel_id).val();
    var deposit_type_name =  $("#deposit_type"+rel_id+" option:selected").html();
    var deposit_other = $("#deposit_other"+rel_id).val();
    var account_type = $("#account_type"+rel_id).val();
    var joint = $("#joint"+rel_id).val();
    var joint_other = $("#joint_other"+rel_id).val();
    var deposit_date = $("#deposit_date"+rel_id).val();
    var amount = $("#amount"+rel_id).val();

    if(validate("deposit"+rel_id))
    {
        $.ajax({
        url: "{{ url('save_deposit') }}",
        type: 'GET',
        data: { 
                cand_id:cand_id, 
                rel_type_id:rel_id,
                bank_name:bank_name, 
                branch_address:branch_address, 
                deposit_type:deposit_type,
                deposit_other:deposit_other,
                account_type:account_type,
                joint:joint,
                joint_other:joint_other,
                deposit_date:deposit_date,
                amount:amount
        },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
            if(data!=0)
            {
                 datas = JSON.parse(data);
                 
                if(deposit_type==5)
                    deposit_type_name = deposit_type_name+"<br>"+deposit_other;
                
                if(account_type=="Joint")
                    var display_account = account_type+" with "+datas.joint_account_with_name;
                else
                     var display_account = account_type;

                if(joint_other!="")
                    display_account = display_account+","+joint_other;

                var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm"  title="{{Lang::get("affidavit.edit") }}" onclick="javascript:open_modal('+datas.id+',{{$data}})"  data-bank_name="'+bank_name+'" data-branch_address="'+branch_address+'" data-deposit_type="'+deposit_type+'" data-deposit_type_other="'+deposit_other+'" data-account_type="'+account_type+'" data-joint_account_with="'+datas.joint_account_with+'" data-deposit_date="'+datas.deposit_date_edit+'" data-amount="'+amount+'"  data-joint_other_name="'+joint_other+'"  data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_deposit'+datas.id+'"> <i class="fa fa-edit"></i> {{Lang::get("affidavit.edit") }}</a>';

                var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm"  title="{{Lang::get("affidavit.delete") }}" onclick="javascript:delete_deposit('+datas.id+')"><i class="fa fa-times"></i> {{Lang::get("affidavit.delete") }}</a>';

                 $('#relative'+rel_id).prepend('<tr id="tr'+datas.id+'"><td>'+bank_name+'</td><td>'+branch_address+'</td><td>'+deposit_type_name+'</td><td>'+display_account+'</td><td>'+datas.deposit_date+'</td><td>'+amount+'</td><td>'+edit+' '+del+'</td></tr>');

                $("#bank_name"+rel_id).val('');
                $("#branch_address"+rel_id).val('');
                $("#deposit_type"+rel_id).val('');
                $("#deposit_other"+rel_id).val('');
                $("#account_type"+rel_id).val('');
                $("#joint"+rel_id).val('');
                $("#joint_other"+rel_id).val('');
                $("#deposit_date"+rel_id).val('');
                $("#amount"+rel_id).val('');
                $("#joint_div"+rel_id).css("display", "none");
                $("#deposit_div"+rel_id).css("display", "none");
            }
        }
        });
    }
}
</script>
<script type="text/javascript">
function modal_get_deposit_type()
{
    $("#modal_deposit_other").val('');
    var deposit_type = $("#modal_deposit_type").val();
    if(deposit_type==5)
    {
        $("#modal_deposit_div").css("display", "block");
        $("#modal_deposit_other").attr("required", "required");
    }
    else
    {
        $("#modal_deposit_div").css("display", "none");
        $("#modal_deposit_other").removeAttr("required");
    }  
}
</script>
<script type="text/javascript">
function modal_get_relatives()
{
    $("#modal_joint_other").val('');
    $("#modal_joint_other_name").val('');
    var account_type = $("#modal_account_type").val();
    if(account_type=="Joint")
    {
        $("#modal_account_type_div").css("display", "block");
        //$("#modal_joint_other").attr("required", "required");
        //$("#modal_joint_other_name").attr("required", "required");
    }
    else
    {
        $("#modal_account_type_div").css("display", "none");
        //$("#modal_joint_other").removeAttr("required");
        //$("#modal_joint_other_name").removeAttr("required");
    }
}
</script>
<script type="text/javascript">
function open_modal(id, datas)
{
    var bank_name = "";
    var branch_address =  "";
    var deposit_type =  "";
    var deposit_type_other =  "";
    var account_type =  "";
    var joint_account_with =  "";
    var deposit_date =  "";
    var joint_other_name =  "";
    var amount =  "";
    var relation_type_id =  "";
    $("#modal_account_type_div").css("display", "none");
    $("#modal_deposit_div").css("display", "none");

    bank_name = $("#edit_deposit"+id).data("bank_name");
    branch_address = $("#edit_deposit"+id).data("branch_address");
    deposit_type = $("#edit_deposit"+id).data("deposit_type");
    deposit_type_other = $("#edit_deposit"+id).data("deposit_type_other");
    account_type = $("#edit_deposit"+id).data("account_type");
    joint_account_with = $("#edit_deposit"+id).data("joint_account_with");
    deposit_date = $("#edit_deposit"+id).data("deposit_date");
    joint_other_name = $("#edit_deposit"+id).data("joint_other_name");
    amount = $("#edit_deposit"+id).data("amount");
    relation_type_id = $("#edit_deposit"+id).data("relation_type_id");
    candidate_id = $("#edit_deposit"+id).data("candidate_id");

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
        $("#modal_account_type_div").css("display", "block");
       // $("#modal_joint_other").attr("required", "required"); 
        //$("#modal_joint_other_name").attr("required", "required"); 
    }
    if(deposit_type==5)
    {
        $("#modal_deposit_div").css("display", "block");
        $("#modal_deposit_other").attr("required", "required");        
    }

    $("#modal_joint_other").html(all);
    $("#modal_bank_name").val(bank_name);
    $("#modal_branch_address").val(branch_address);
    $("#modal_deposit_type").val(deposit_type);
    $("#modal_deposit_type option:selected").html();
    $("#modal_deposit_other").val(deposit_type_other);
    $("#modal_account_type").val(account_type);
    $("#modal_joint_other_name").val(joint_other_name);
    $("#modal_deposit_date").val(deposit_date);
    $("#modal_amount").val(amount);
    $("#modal_rel_id").val(relation_type_id);
    $("#modal_cand_id").val(candidate_id);
    $("#modal_bank_id").val(id);
    $('#exampleModal').find('span').remove();
    $("#exampleModal").modal('show');
}
</script>
<script type="text/javascript">
function update_deposit()
{
    /* alert('#form'+rel_id);*/
    var bank_name = $("#modal_bank_name").val();
    var branch_address = $("#modal_branch_address").val();
    var deposit_type = $("#modal_deposit_type").val();
    var deposit_type_name =  $("#modal_deposit_type option:selected").html();
    var deposit_other = $("#modal_deposit_other").val();
    var account_type = $("#modal_account_type").val();
    var joint = $("#modal_joint_other").val();
    var joint_other = $("#modal_joint_other_name").val();
    var deposit_date = $("#modal_deposit_date").val();
    var rel_id = $("#modal_rel_id").val();
    var cand_id = $("#modal_cand_id").val();
    var amount = $("#modal_amount").val();
    var bank_id = $("#modal_bank_id").val();
    /*alert(account_type);
    alert(joint);*/
    if(validate("modal_deposit_form"))
    {
        $.ajax({
        url: "{{ url($menu_action.'update_deposit') }}",
        type: 'GET',
        data: { 
                bank_id:bank_id, 
                cand_id:cand_id, 
                rel_type_id:rel_id,
                bank_name:bank_name, 
                branch_address:branch_address, 
                deposit_type:deposit_type,
                deposit_other:deposit_other,
                account_type:account_type,
                joint:joint,
                joint_other:joint_other,
                deposit_date:deposit_date,
                amount:amount
        },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
            if(data!=0)
            {
                datas = JSON.parse(data);
                 
                if(deposit_type==5)
                    deposit_type_name = deposit_type_name+"<br>"+deposit_other;
                
                if(account_type=="Joint")
                    var display_account = account_type+" with "+datas.joint_account_with_name;
                else
                     var display_account = account_type;

                if(joint_other!="")
                    display_account = display_account+","+joint_other;

                $('#tr'+bank_id).remove();

                var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get("affidavit.edit") }}" onclick="javascript:open_modal('+bank_id+',{{$data}})"  data-bank_name="'+bank_name+'" data-branch_address="'+branch_address+'" data-deposit_type="'+deposit_type+'" data-deposit_type_other="'+deposit_other+'" data-account_type="'+account_type+'" data-joint_account_with="'+datas.joint_account_with+'" data-deposit_date="'+datas.deposit_date_edit+'" data-amount="'+amount+'"  data-joint_other_name="'+joint_other+'"  data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_deposit'+bank_id+'"> <i class="fa fa-edit"></i> {{Lang::get("affidavit.edit") }} </a>';



				<?php if(Auth::user()->role_id != '19') { ?>
					
                var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get("affidavit.delete") }}" onclick="javascript:delete_deposit('+datas.id+')"><i class="fa fa-times"></i> {{Lang::get("affidavit.delete") }}</a>';
				
				<?php } else { ?>
				var del = '';	
				<?php } ?>


                

                 $('#relative'+rel_id).prepend('<tr id="tr'+datas.id+'"><td>'+bank_name+'</td><td>'+branch_address+'</td><td>'+deposit_type_name+'</td><td>'+display_account+'</td><td>'+datas.deposit_date+'</td><td>'+amount+'</td><td>'+edit+' '+del+'</td></tr>');
                $("#exampleModal").modal('hide');
            }
        }
        });
    }
}
</script>
<script type="text/javascript">
function delete_deposit(id)
{
    $("#modal_delete_bank_id").val(id);
    $("#deleteDepositModal").modal('show');
}
</script>
<script type="text/javascript">
function delete_deposit_entry()
{
    var id = $("#modal_delete_bank_id").val();
    if(id)
    {
    $.ajax({
        url: "{{ url('delete_deposit') }}",
        type: 'GET',
        data: {  bank_id:id },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
        if(data==1)
        {
            $('#tr'+id).remove();
            $("#deleteDepositModal").modal('hide');
        }
        }
    });
    }
}
</script>
<!-- Deposit In Banks -->

<!-- Investment In Bonds -->
<script type="text/javascript">
function get_invetment_type(rel_id)
{
    if(rel_id)
    {
        $("#ins_deposit_other"+rel_id).val('');
        var invest_type = $("#invest_type"+rel_id).val();
        if(invest_type==4)
        {
            $("#invest_div"+rel_id).css("display", "block");
            $("#ins_deposit_other"+rel_id).attr("required", "required");
        }
        else
        {
            $("#invest_div"+rel_id).css("display", "none");
            $("#ins_deposit_other"+rel_id).removeAttr("required");

        }            
    }
}
</script>
<script type="text/javascript">
function get_ins_relatives(rel_id)
{
    if(rel_id)
    {
        $("#ins_joint"+rel_id).val('');
        $("#ins_joint_other"+rel_id).val('');
        var ins_account_type = $("#ins_account_type"+rel_id).val();
        if(ins_account_type=="Joint")
        {
            $("#joint_ins_div"+rel_id).css("display", "block");
            //$("#ins_joint"+rel_id).attr("required", "required");
            //$("#ins_joint_other"+rel_id).attr("required", "required");
        }
        else
        {
            $("#joint_ins_div"+rel_id).css("display", "none");
            //$("#ins_joint"+rel_id).removeAttr("required");
            //$("#ins_joint_other"+rel_id).removeAttr("required");
        }
    }
}
</script>
<script type="text/javascript">
function save_investment(cand_id, rel_id)
{
    var company = $("#company_name"+rel_id).val();
    var invest_type = $("#invest_type"+rel_id).val();
    var ins_deposit_other = $("#ins_deposit_other"+rel_id).val();
    var invest_type_name =  $("#invest_type"+rel_id+" option:selected").html();
    var number_of_units = $("#number_of_units"+rel_id).val();
    var ins_account_type = $("#ins_account_type"+rel_id).val();
    var ins_joint = $("#ins_joint"+rel_id).val();
    var ins_joint_other = $("#ins_joint_other"+rel_id).val();
    var ins_amount = $("#ins_amount"+rel_id).val();
 
    if(validate("company_form"+rel_id))
    {
        $.ajax({
        url: "{{ url('save_investment') }}",
        type: 'GET',
        data: { 
                cand_id:cand_id, 
                rel_type_id:rel_id,
                company:company, 
                invest_type:invest_type, 
                ins_deposit_other:ins_deposit_other,
                number_of_units:number_of_units,
                account_type:ins_account_type,
                joint:ins_joint,
                joint_other:ins_joint_other,
                amount:ins_amount
        },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
            if(data!=0)
            {
                 datas = JSON.parse(data);
                 
                if(invest_type==4)
                    invest_type_name = invest_type_name+"<br>"+ins_deposit_other;
                
                if(ins_account_type=="Joint")
                    var display_account = ins_account_type+" with "+datas.joint_account_with_name;
                else
                     var display_account = ins_account_type;

                if(ins_joint_other!="")
                    display_account = display_account+","+ins_joint_other;

                var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get("affidavit.edit") }}" onclick="javascript:edit_investment('+datas.id+',{{$data}})"  data-company="'+company+'" data-company_investment_type_id="'+invest_type+'" data-company_investment_type_other="'+ins_deposit_other+'" data-number_of_units='+number_of_units+' data-account_type="'+ins_account_type+'" data-joint_account_with="'+datas.joint_account_with+'" data-amount="'+ins_amount+'" data-joint_other_name="'+ins_joint_other+'"  data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_invest'+datas.id+'"> <i class="fa fa-edit"></i> {{Lang::get("affidavit.edit") }}</a>';

                var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get("affidavit.delete") }}" onclick="javascript:delete_invest('+datas.id+')"><i class="fa fa-times"></i> {{Lang::get("affidavit.delete") }}</a>';

                 $('#ins_relative'+rel_id).prepend('<tr id="company'+datas.id+'"><td>'+company+'</td><td>'+invest_type_name+'</td><td>'+number_of_units+'</td><td>'+display_account+'</td><td>'+ins_amount+'</td><td>'+edit+' '+del+'</td></tr>');

                $("#company_name"+rel_id).val('');
                $("#invest_type"+rel_id).val('');
                $("#ins_deposit_other"+rel_id).val('');
                $("#number_of_units"+rel_id).val('');
                $("#ins_account_type"+rel_id).val('');
                $("#ins_joint"+rel_id).val('');
                $("#ins_joint_other"+rel_id).val('');
                $("#ins_amount"+rel_id).val('');
                $("#joint_ins_div"+rel_id).css("display", "none");
                $("#invest_div"+rel_id).css("display", "none");
            }
        }
        });
    }
}
</script>
<script type="text/javascript">
function get_modal_invetment_type()
{

    $("#modal_ins_deposit_other").val('');
    var modal_invest_type = $("#modal_invest_type").val();
    if(modal_invest_type==4)
    {
        $("#modal_investment_div").css("display", "block");
        $("#modal_ins_deposit_other").attr("required", "required");
    }
    else
    {
        $("#modal_investment_div").css("display", "none");
        $("#modal_ins_deposit_other").removeAttr("required");
    }  
}
</script>
<script type="text/javascript">
function modal_get_investment_relatives()
{
    $("#modal_ins_joint_other").val('');
    $("#modal_ins_joint_other_name").val('');
    var modal_ins_account_type = $("#modal_ins_account_type").val();
    if(modal_ins_account_type=="Joint")
    {
        $("#modal_investment_account_type_div").css("display", "block");
        //$("#modal_ins_joint_other").attr("required", "required");
        //$("#modal_ins_joint_other_name").attr("required", "required");
    }
    else
    {
        $("#modal_investment_account_type_div").css("display", "none");
       // $("#modal_ins_joint_other").removeAttr("required");
       // $("#modal_ins_joint_other_name").removeAttr("required");
    }
}
</script>
<script type="text/javascript">
function edit_investment(id, datas)
{
    var company_name = "";
    var company_investment_type_id =  "";
    var company_investment_type_other =  "";
    var number_of_units =  "";
    var account_type =  "";
    var joint_account_with =  "";
    var deposit_date =  "";
    var joint_other_name =  "";
    var amount =  "";
    var relation_type_id =  "";
    var candidate_id =  "";
    $("#modal_investment_account_type_div").css("display", "none");
    $("#modal_investment_div").css("display", "none");

    company_name = $("#edit_invest"+id).data("company");
    company_investment_type_id = $("#edit_invest"+id).data("company_investment_type_id");
    company_investment_type_other = $("#edit_invest"+id).data("company_investment_type_other");
    number_of_units = $("#edit_invest"+id).data("number_of_units");
    account_type = $("#edit_invest"+id).data("account_type");
    joint_account_with = $("#edit_invest"+id).data("joint_account_with");
    joint_other_name = $("#edit_invest"+id).data("joint_other_name");
    amount = $("#edit_invest"+id).data("amount");
    relation_type_id = $("#edit_invest"+id).data("relation_type_id");
    candidate_id = $("#edit_invest"+id).data("candidate_id");

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
        $("#modal_investment_account_type_div").css("display", "block");
       // $("#modal_ins_joint_other").attr("required", "required");
       // $("#modal_ins_joint_other_name").attr("required", "required");
    }
    if(company_investment_type_id==4)
    {
        $("#modal_investment_div").css("display", "block");
        $("#modal_ins_deposit_other").attr("required", "required");
    }

    $("#modal_ins_joint_other").html(all);
    $("#modal_company_name").val(company_name);
    $("#modal_number_of_units").val(number_of_units);
    $("#modal_invest_type").val(company_investment_type_id);
    $("#modal_invest_type option:selected").html();
    $("#modal_ins_deposit_other").val(company_investment_type_other);
    $("#modal_ins_account_type").val(account_type);
    $("#modal_ins_joint_other_name").val(joint_other_name);
    $("#modal_ins_amount").val(amount);
    $("#modal_ins_rel_id").val(relation_type_id);
    $("#modal_ins_cand_id").val(candidate_id);
    $("#modal_ins_company_id").val(id);
    $("#investModal").find('span').remove();
    $("#investModal").modal('show');
}
</script>
<script type="text/javascript">
function update_investment()
{
    /* alert('#form'+rel_id);*/
    var company_name = $("#modal_company_name").val();
    var number_of_units = $("#modal_number_of_units").val();
    var modal_invest_type = $("#modal_invest_type").val();
    var modal_ins_deposit_other = $("#modal_ins_deposit_other").val();
    var modal_ins_deposit_other_name =  $("#modal_invest_type option:selected").html();
    var modal_ins_account_type = $("#modal_ins_account_type").val();
    var joint = $("#modal_ins_joint_other").val();
    var joint_other = $("#modal_ins_joint_other_name").val();
    var rel_id = $("#modal_ins_rel_id").val();
    var cand_id = $("#modal_ins_cand_id").val();
    var amount = $("#modal_ins_amount").val();
    var company_id = $("#modal_ins_company_id").val();

    if(validate("form_investModal"))
    {
        $.ajax({
        url: "{{ url($menu_action.'update_investment') }}",
        type: 'GET',
        data: { 
                company_id:company_id, 
                cand_id:cand_id, 
                rel_type_id:rel_id,
                company:company_name, 
                number_of_units:number_of_units, 
                invest_type:modal_invest_type,
                ins_deposit_other:modal_ins_deposit_other,
                account_type:modal_ins_account_type,
                joint:joint,
                joint_other:joint_other,
                amount:amount
        },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
            if(data!=0)
            {
                datas = JSON.parse(data);
                 
                if(modal_invest_type==4)
                    modal_ins_deposit_other_name = modal_ins_deposit_other_name+"<br>"+modal_ins_deposit_other;
                
                if(modal_ins_account_type=="Joint")
                    var display_account = modal_ins_account_type+" with "+datas.joint_account_with_name;
                else
                     var display_account = modal_ins_account_type;

                if(joint_other!="")
                    display_account = display_account+","+joint_other;

                $('#company'+company_id).html('');

                var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get("affidavit.edit") }}" onclick="javascript:edit_investment('+datas.id+',{{$data}})"  data-company="'+company_name+'" data-company_investment_type_id="'+modal_invest_type+'" data-company_investment_type_other="'+modal_ins_deposit_other+'" data-number_of_units="'+number_of_units+'" data-account_type="'+modal_ins_account_type+'" data-joint_account_with="'+datas.joint_account_with+'" data-amount="'+amount+'" data-joint_other_name="'+joint_other+'"  data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_invest'+datas.id+'"> <i class="fa fa-edit"></i> {{Lang::get("affidavit.edit") }} </a>';


				<?php if(Auth::user()->role_id != '19') { ?>
					
                 var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get("affidavit.delete") }}" onclick="javascript:delete_invest('+datas.id+')"><i class="fa fa-times"></i> {{Lang::get("affidavit.delete") }}</a>';
				
				<?php } else { ?>
				var del = '';	
				<?php } ?>



                   

                    $('#company'+company_id).html('<td>'+company_name+'</td><td>'+modal_ins_deposit_other_name+'</td><td>'+number_of_units+'</td><td>'+display_account+'</td><td>'+amount+'</td><td>'+edit+' '+del+'</td>');

                $("#investModal").modal('hide');
            }
        }
        });
    }
}
</script>
<script type="text/javascript">
function delete_invest(id)
{
    $("#modal_delete_company_id").val(id);
    $("#deleteInvestModal").modal('show');
}
</script>
<script type="text/javascript">
function delete_invest_entry()
{
    var id = $("#modal_delete_company_id").val();
    if(id)
    {
    $.ajax({
        url: "{{ url('delete_investment') }}",
        type: 'GET',
        data: {  bank_id:id },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
        if(data==1)
        {
            $('#company'+id).remove();
            $("#deleteInvestModal").modal('hide');
        }
        }
    });
    }
}
</script>
<!-- Investment In Bonds -->


<!-- Investment In NSS -->
<script type="text/javascript">
function get_saving_type(rel_id)
{
    if(rel_id)
    {
        $("#saving_type_other"+rel_id).val('');
        var saving_type = $("#saving_type"+rel_id).val();
        if(saving_type==4)
        {
            $("#saving_type_div"+rel_id).css("display", "block");
            $("#saving_type_other"+rel_id).attr("required", "required");
        }
        else
        {
            $("#saving_type_div"+rel_id).css("display", "none");
            $("#saving_type_other"+rel_id).removeAttr("required");

        }            
    }
}
</script>
<script type="text/javascript">
function get_saving_relatives(rel_id)
{
    if(rel_id)
    {
        $("#saving_joint"+rel_id).val('');
        $("#saving_joint_other"+rel_id).val('');
        var saving_account_type = $("#saving_account_type"+rel_id).val();
        if(saving_account_type=="Joint")
        {
            $("#joint_saving_div"+rel_id).css("display", "block");
            //$("#saving_joint"+rel_id).attr("required", "required");
            //$("#saving_joint_other"+rel_id).attr("required", "required");
        }
        else
        {
            $("#joint_saving_div"+rel_id).css("display", "none");
           // $("#saving_joint"+rel_id).removeAttr("required");
           // $("#saving_joint_other"+rel_id).removeAttr("required");
        }
    }
}
</script>

<script type="text/javascript">
function save_savings(cand_id, rel_id)
{
    var company = $("#saving_name"+rel_id).val();
    var saving_type = $("#saving_type"+rel_id).val();
    var saving_type_other = $("#saving_type_other"+rel_id).val();
    var saving_type_name =  $("#saving_type"+rel_id+" option:selected").html();
    var saving_account_type = $("#saving_account_type"+rel_id).val();
    var saving_joint = $("#saving_joint"+rel_id).val();
    var saving_joint_other = $("#saving_joint_other"+rel_id).val();
    var saving_amount = $("#saving_amount"+rel_id).val();

    if(validate("nss_form"+rel_id))
    {
        $.ajax({
        url: "{{ url('save_savings') }}",
        type: 'GET',
        data: { 
                cand_id:cand_id, 
                rel_type_id:rel_id,
                company:company, 
                saving_type:saving_type, 
                saving_type_other:saving_type_other,
                account_type:saving_account_type,
                joint:saving_joint,
                joint_other:saving_joint_other,
                amount:saving_amount
        },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
            if(data!=0)
            {
                 datas = JSON.parse(data);
                 
                if(saving_type==4)
                    saving_type_name = saving_type_name+"<br>"+saving_type_other;
                
                if(saving_account_type=="Joint")
                    var display_account = saving_account_type+" with "+datas.joint_account_with_name;
                else
                     var display_account = saving_account_type;

                if(saving_joint_other!="")
                    display_account = display_account+","+saving_joint_other;

                var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get("affidavit.edit") }}" onclick="javascript:edit_save('+datas.id+',{{$data}})"  data-company="'+company+'" data-saving_type_id="'+saving_type+'" data-saving_type_other="'+saving_type_other+'" data-account_type="'+saving_account_type+'" data-joint_account_with="'+datas.joint_account_with+'" data-amount="'+saving_amount+'" data-joint_other_name="'+saving_joint_other+'"  data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_save'+datas.id+'"> <i class="fa fa-edit"></i> {{Lang::get("affidavit.edit") }} </a>';

                var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get("affidavit.delete") }}" onclick="javascript:delete_save('+datas.id+')"><i class="fa fa-times"></i> {{Lang::get("affidavit.delete") }}</a>';

                 $('#save_relative'+rel_id).prepend('<tr id="trsave'+datas.id+'"><td>'+company+'</td><td>'+saving_type_name+'</td><td>'+display_account+'</td><td>'+saving_amount+'</td><td>'+edit+' '+del+'</td></tr>');

                $("#saving_name"+rel_id).val('');
                $("#saving_type"+rel_id).val('');
                $("#saving_type_other"+rel_id).val('');
                $("#number_of_units"+rel_id).val('');
                $("#saving_account_type"+rel_id).val('');
                $("#saving_joint"+rel_id).val('');
                $("#saving_joint_other"+rel_id).val('');
                $("#saving_amount"+rel_id).val('');
                $("#joint_saving_div"+rel_id).css("display", "none");
                $("#saving_type_div"+rel_id).css("display", "none");
            }
        }
        });
    }
}
</script>

<script type="text/javascript">
function get_modal_saving_type()
{
    $("#modal_saving_type_other").val('');
    var modal_saving_type = $("#modal_saving_type").val();
    if(modal_saving_type==4)
    {
        $("#modal_saving_div").css("display", "block");
        $("#modal_saving_type_other").attr("required", "required");
    }
    else
    {
        $("#modal_saving_div").css("display", "none");
        $("#modal_saving_type_other").removeAttr("required");

    } 
}
</script>
<script type="text/javascript">
function get_modal_saving_relatives()
{
    $("#modal_save_joint_other").val('');
    $("#modal_save_joint_other_name").val('');
    var modal_saving_account_type = $("#modal_saving_account_type").val();
    if(modal_saving_account_type=="Joint")
    {
        $("#modal_saving_account_type_div").css("display", "block");
        //$("#modal_save_joint_other").attr("required", "required");
        //$("#modal_save_joint_other_name").attr("required", "required");
    }
    else
    {
        $("#modal_saving_account_type_div").css("display", "none");
        //$("#modal_save_joint_other").removeAttr("required");
        //$("#modal_save_joint_other_name").removeAttr("required");
    }
}
</script>
<script type="text/javascript">
function edit_save(id, datas)
{
    var company_name = "";
    var saving_type_id =  "";
    var saving_type_other =  "";
    var account_type =  "";
    var joint_account_with =  "";
    var deposit_date =  "";
    var joint_other_name =  "";
    var amount =  "";
    var relation_type_id =  "";
    var candidate_id =  "";
    $("#modal_saving_account_type_div").css("display", "none");
    $("#modal_saving_div").css("display", "none");

    company_name = $("#edit_save"+id).data("company");
    saving_type_id = $("#edit_save"+id).data("saving_type_id");
    saving_type_other = $("#edit_save"+id).data("saving_type_other");
    account_type = $("#edit_save"+id).data("account_type");
    joint_account_with = $("#edit_save"+id).data("joint_account_with");
    joint_other_name = $("#edit_save"+id).data("joint_other_name");
    amount = $("#edit_save"+id).data("amount");
    relation_type_id = $("#edit_save"+id).data("relation_type_id");
    candidate_id = $("#edit_save"+id).data("candidate_id");

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
        $("#modal_saving_account_type_div").css("display", "block");
        //$("#modal_save_joint_other").attr("required", "required");
        //$("#modal_save_joint_other_name").attr("required", "required");
    }
    if(saving_type_id==4)
    {
        $("#modal_saving_div").css("display", "block");
        $("#modal_saving_type_other").attr("required", "required");
    }

    $("#modal_save_joint_other").html(all);
    $("#modal_saving_name").val(company_name);
    $("#modal_saving_type").val(saving_type_id);
    $("#modal_saving_type option:selected").html();
    $("#modal_saving_type_other").val(saving_type_other);
    $("#modal_saving_account_type").val(account_type);
    $("#modal_save_joint_other_name").val(joint_other_name);
    $("#modal_save_amount").val(amount);
    $("#modal_save_rel_id").val(relation_type_id);
    $("#modal_save_cand_id").val(candidate_id);
    $("#modal_save_save_id").val(id);
    $("#savingModal").find('span').remove();
    $("#savingModal").modal('show');
}
</script>

<script type="text/javascript">
function update_saving()
{
   
    var modal_saving_name = $("#modal_saving_name").val();
    var modal_saving_type = $("#modal_saving_type").val();
    var modal_saving_type_other = $("#modal_saving_type_other").val();
    var modal_saving_type_other_name =  $("#modal_saving_type option:selected").html();
    var modal_saving_account_type = $("#modal_saving_account_type").val();
    var joint = $("#modal_save_joint_other").val();
    var joint_other = $("#modal_save_joint_other_name").val();
    var rel_id = $("#modal_save_rel_id").val();
    var cand_id = $("#modal_save_cand_id").val();
    var amount = $("#modal_save_amount").val();
    var saving_id = $("#modal_save_save_id").val();

    if(validate("form_savingModal"))
    {
        $.ajax({
        url: "{{ url($menu_action.'update_savings') }}",
        type: 'GET',
        data: { 
                saving_id:saving_id, 
                cand_id:cand_id, 
                rel_type_id:rel_id,
                company:modal_saving_name, 
                saving_type:modal_saving_type,
                saving_type_other:modal_saving_type_other,
                account_type:modal_saving_account_type,
                joint:joint,
                joint_other:joint_other,
                amount:amount
        },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
            if(data!=0)
            {
                datas = JSON.parse(data);
                 
                if(modal_saving_type==4)
                    modal_saving_type_other_name = modal_saving_type_other_name+"<br>"+modal_saving_type_other;
                
                if(modal_saving_account_type=="Joint")
                    var display_account = modal_saving_account_type+" with "+datas.joint_account_with_name;
                else
                     var display_account = modal_saving_account_type;

                if(joint_other!="")
                    display_account = display_account+","+joint_other;

                $('#trsave'+saving_id).html('');

                var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get("affidavit.edit") }}" onclick="javascript:edit_save('+datas.id+',{{$data}})"  data-company="'+modal_saving_name+'" data-saving_type_id="'+modal_saving_type+'" data-saving_type_other="'+modal_saving_type_other+'" data-account_type="'+modal_saving_account_type+'" data-joint_account_with="'+datas.joint_account_with+'" data-amount="'+amount+'" data-joint_other_name="'+joint_other+'"  data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_save'+datas.id+'"> <i class="fa fa-edit"></i> {{Lang::get("affidavit.edit") }} </a>';

				<?php if(Auth::user()->role_id != '19') { ?>
					
                var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get("affidavit.delete") }}" onclick="javascript:delete_save('+datas.id+')"><i class="fa fa-times"></i> {{Lang::get("affidavit.delete") }}</a>';
				
				<?php } else { ?>
				var del = '';	
				<?php } ?>


                

                $('#trsave'+saving_id).html('<td>'+modal_saving_name+'</td><td>'+modal_saving_type_other_name+'</td><td>'+display_account+'</td><td>'+amount+'</td><td>'+edit+' '+del+'</td>');

                $("#savingModal").modal('hide');
            }
        }
        });
    }
}
</script>
<script type="text/javascript">
function delete_save(id)
{
    $("#modal_delete_saving_id").val(id);
    $("#deleteSavingModal").modal('show');
}
</script>
<script type="text/javascript">
function delete_save_entry()
{
    var id = $("#modal_delete_saving_id").val();
    if(id)
    {
    $.ajax({
        url: "{{ url('delete_savings') }}",
        type: 'GET',
        data: {  bank_id:id },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
        if(data==1)
        {
            $('#trsave'+id).remove();
            $("#deleteSavingModal").modal('hide');
        }
        }
    });
    }
}
</script>
<!-- Investment In NSS -->

<!-- Loans -->
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
           // $("#loan_joint_account_with"+rel_id).attr("required", "required");
           // $("#loan_joint_account_with"+rel_id).attr("required", "required");
        }
        else
        {
            $("#joint_loan_div"+rel_id).css("display", "none");
           // $("#loan_joint_account_with"+rel_id).removeAttr("required");
           // $("#loan_joint_account_with_name"+rel_id).removeAttr("required");
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
    var nature_of_loan = $("#nature_of_loan"+rel_id).val();
    var loan_amount = $("#loan_amount"+rel_id).val();

    if(validate("loan_form"+rel_id))
    {
        $.ajax({
        url: "{{ url('save_loan') }}",
        type: 'GET',
        data: { 
                cand_id:cand_id, 
                rel_type_id:rel_id,
                loan_type:loan_type, 
                loan_type_other:loan_type_other,
                account_type:loan_account_type,
                joint:loan_joint_account_with,
                joint_other:loan_joint_account_with_name,
                loan_to:loan_to, 
                nature_of_loan:nature_of_loan, 
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

                var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get("affidavit.edit") }}" onclick="javascript:edit_loan('+datas.id+',{{$data}})"  data-loan_type_id="'+loan_type+'" data-loan_type_other="'+loan_type_other+'" data-account_type="'+loan_account_type+'" data-joint_account_with="'+datas.joint_account_with+'" data-loan_to="'+loan_to+'" data-nature_of_loan="'+nature_of_loan+'" data-amount="'+loan_amount+'" data-joint_other_name="'+loan_joint_account_with_name+'"  data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_loan'+datas.id+'"> <i class="fa fa-edit"></i> {{Lang::get("affidavit.edit") }} </a>';

                var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get("affidavit.delete") }}" onclick="javascript:delete_loan('+datas.id+')"><i class="fa fa-times"></i> {{Lang::get("affidavit.delete") }}</a>';

                 $('#loan_relative'+rel_id).prepend('<tr id="trloan'+datas.id+'"><td>'+loan_type_name+'</td><td>'+display_account+'</td><td>'+loan_to+'</td><td>'+nature_of_loan+'</td><td>'+loan_amount+'</td><td>'+edit+' '+del+'</td></tr>');

                $("#loan_type"+rel_id).val('');
                $("#loan_type_other"+rel_id).val('');
                $("#loan_account_type"+rel_id).val('');
                $("#saving_joint"+rel_id).val('');
                $("#loan_joint_account_with_name"+rel_id).val('');
                $("#loan_to"+rel_id).val('');
                $("#nature_of_loan"+rel_id).val('');
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
       // $("#modal_loan_joint_other").attr("required", "required");
       // $("#modal_loan_joint_other_name").attr("required", "required");
    }
    else
    {
        $("#modal_loan_account_type_div").css("display", "none");
       // $("#modal_loan_joint_other").removeAttr("required");
       // $("#modal_loan_joint_other_name").removeAttr("required");
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
    var nature_of_loan =  "";
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
    nature_of_loan = $("#edit_loan"+id).data("nature_of_loan");
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
    $("#modal_nature_of_loan").val(nature_of_loan);
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
    var nature_of_loan = $("#modal_nature_of_loan").val();
    var amount = $("#modal_loan_amount").val();
    var loan_id = $("#modal_loan_loan_id").val();

    if(validate("form_loanModal"))
    {
        $.ajax({
        url: "{{ url($menu_action.'update_loan') }}",
        type: 'GET',
        data: { 
                loan_id:loan_id, 
                cand_id:cand_id, 
                rel_type_id:rel_id,
                loan_type:modal_loan_type,
                loan_type_other:modal_loan_type_other,
                account_type:modal_loan_account_type,
                joint:joint,
                joint_other:joint_other,
                loan_to:loan_to,
                nature_of_loan:nature_of_loan,
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

                var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get("affidavit.edit") }}" onclick="javascript:edit_loan('+datas.id+',{{$data}})"  data-loan_type_id="'+modal_loan_type+'" data-loan_type_other="'+modal_loan_type_other+'" data-account_type="'+modal_loan_account_type+'" data-joint_account_with="'+datas.joint_account_with+'" data-loan_to="'+loan_to+'" data-nature_of_loan="'+nature_of_loan+'" data-amount="'+amount+'" data-joint_other_name="'+joint_other+'"  data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_loan'+datas.id+'"> <i class="fa fa-edit"></i> {{Lang::get("affidavit.edit") }} </a>';

				<?php if(Auth::user()->role_id != '19') { ?>
					
                var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get("affidavit.delete") }}" onclick="javascript:delete_loan('+datas.id+')"><i class="fa fa-times"></i> {{Lang::get("affidavit.delete") }}</a>';
				
				<?php } else { ?>
				var del = '';	
				<?php } ?>

                


                $('#trloan'+loan_id).html('<td>'+modal_loan_type_other_name+'</td><td>'+display_account+'</td><td>'+loan_to+'</td><td>'+nature_of_loan+'</td><td>'+amount+'</td><td>'+edit+' '+del+'</td>');
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
        url: "{{ url('delete_loan') }}",
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
<!-- Loans -->


<!-- Vehicle -->
<script type="text/javascript">
function get_vehicle_type(rel_id)
{
    if(rel_id)
    {
        $("#vehicle_type_other"+rel_id).val('');
        var vehicle_type = $("#vehicle_type"+rel_id).val();
        if(vehicle_type==11)
        {
            $("#vehicle_type_div"+rel_id).css("display", "block");
            $("#vehicle_type_other"+rel_id).attr("required", "required");
        }
        else
        {
            $("#vehicle_type_div"+rel_id).css("display", "none");
            $("#vehicle_type_other"+rel_id).removeAttr("required");

        }            
    }
}
</script>

<script type="text/javascript">
function save_vehicle(cand_id, rel_id)
{
    var vehicle_type = $("#vehicle_type"+rel_id).val();
    var vehicle_type_other = $("#vehicle_type_other"+rel_id).val();
    var vehicle_type_name =  $("#vehicle_type"+rel_id+" option:selected").html();   
    var make = $("#make"+rel_id).val();
    var registration_no = $("#registration_no"+rel_id).val();
    var year_of_purchase = $("#year_of_purchase"+rel_id).val();
    var vehicle_amount = $("#vehicle_amount"+rel_id).val();

    if(validate("vehicle_form"+rel_id))
    {
        $.ajax({
        url: "{{ url('save_vehicle') }}",
        type: 'GET',
        data: { 
                cand_id:cand_id, 
                rel_type_id:rel_id,
                vehicle_type:vehicle_type, 
                vehicle_type_other:vehicle_type_other,
                make:make,
                registration_no:registration_no,
                year_of_purchase:year_of_purchase,
                amount:vehicle_amount
        },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
            if(data!=0)
            {
                 datas = JSON.parse(data);
                 
                if(vehicle_type==11)
                    vehicle_type_name = vehicle_type_name+"<br>"+vehicle_type_other;

                var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get("affidavit.edit") }}" onclick="javascript:edit_vehicle('+datas.id+')"  data-vehicle_type_id="'+vehicle_type+'" data-vehicle_type_other="'+vehicle_type_other+'" data-make="'+make+'" data-registration_no="'+registration_no+'" data-year_of_purchase="'+year_of_purchase+'" data-amount="'+vehicle_amount+'" data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_vehicle'+datas.id+'"> <i class="fa fa-edit"></i> {{Lang::get("affidavit.edit") }} </a>';

                var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get("affidavit.delete") }}" onclick="javascript:delete_vehicle('+datas.id+')"><i class="fa fa-times"></i> {{Lang::get("affidavit.delete") }}</a>';

                 $('#vehicle_relative'+rel_id).prepend('<tr id="trvehicle'+datas.id+'"><td>'+vehicle_type_name+'</td><td>'+make+'</td><td>'+registration_no+'</td><td>'+year_of_purchase+'</td><td>'+vehicle_amount+'</td><td>'+edit+' '+del+'</td></tr>');

                $("#vehicle_type"+rel_id).val('');
                $("#vehicle_type_other"+rel_id).val('');
                $("#make"+rel_id).val('');
                $("#registration_no"+rel_id).val('');
                $("#year_of_purchase"+rel_id).val('');
                $("#vehicle_amount"+rel_id).val('');
                $("#vehicle_type_div"+rel_id).css("display", "none");
            }
        }
        });
    }
}
</script>

<script type="text/javascript">
function get_modal_vehicle_type()
{
    $("#modal_vehicle_type_other").val('');
    var modal_vehicle_type = $("#modal_vehicle_type").val();
    if(modal_vehicle_type==11)
    {
        $("#modal_vehicle_div").css("display", "block");
        $("#modal_vehicle_type_other").attr("required", "required");
    }
    else
    {
        $("#modal_vehicle_div").css("display", "none");
        $("#modal_vehicle_type_other").removeAttr("required");

    }
}
</script>

<script type="text/javascript">
function edit_vehicle(id, datas)
{
    var vehicle_type_id = "";
    var vehicle_type_other =  "";
    var make =  "";
    var registration_no =  "";
    var year_of_purchase =  "";
    var amount =  "";
    var nature_of_loan =  "";
    var amount =  "";
    var relation_type_id =  "";
    var candidate_id =  "";
    $("#modal_vehicle_div").css("display", "none");

    vehicle_type_id = $("#edit_vehicle"+id).data("vehicle_type_id");
    vehicle_type_other = $("#edit_vehicle"+id).data("vehicle_type_other");
    make = $("#edit_vehicle"+id).data("make");
    registration_no = $("#edit_vehicle"+id).data("registration_no");
    year_of_purchase = $("#edit_vehicle"+id).data("year_of_purchase");
    amount = $("#edit_vehicle"+id).data("amount");
    relation_type_id = $("#edit_vehicle"+id).data("relation_type_id");
    candidate_id = $("#edit_vehicle"+id).data("candidate_id");

    if(vehicle_type_id==11)
    {
        $("#modal_vehicle_div").css("display", "block");
        $("#modal_vehicle_type_other").attr("required", "required");
    }

    $("#modal_vehicle_type").val(vehicle_type_id);
    $("#modal_vehicle_type_other").val(vehicle_type_other);
    $("#modal_vehicle_type option:selected").html();
    $("#modal_make").val(make);
    $("#modal_registration_no").val(registration_no);
    $("#modal_year_of_purchase").val(year_of_purchase);
    $("#modal_vehicle_amount").val(amount);
    $("#modal_vehicle_rel_id").val(relation_type_id);
    $("#modal_vehicle_cand_id").val(candidate_id);
    $("#modal_vehicle_id").val(id);
    $("#vehicleModal").find('span').remove();
    $("#vehicleModal").modal('show');
}
</script>

<script type="text/javascript">
function update_vehicle()
{
   
    var vehicle_type = $("#modal_vehicle_type").val();
    var vehicle_type_other = $("#modal_vehicle_type_other").val();
    var vehicle_type_name =  $("#modal_vehicle_type option:selected").html();   
    var make = $("#modal_make").val();
    var registration_no = $("#modal_registration_no").val();
    var year_of_purchase = $("#modal_year_of_purchase").val();
    var vehicle_amount = $("#modal_vehicle_amount").val();
    var cand_id = $("#modal_vehicle_cand_id").val();
    var rel_id = $("#modal_vehicle_rel_id").val();
    var vehicle_id = $("#modal_vehicle_id").val();

    if(validate("form_vehicleModal"))
    {
        $.ajax({
        url: "{{ url($menu_action.'update_vehicle') }}",
        type: 'GET',
        data: { 
                vehicle_id:vehicle_id, 
                cand_id:cand_id, 
                rel_type_id:rel_id,
                vehicle_type:vehicle_type, 
                vehicle_type_other:vehicle_type_other,
                make:make,
                registration_no:registration_no,
                year_of_purchase:year_of_purchase,
                amount:vehicle_amount
        },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
            if(data!=0)
            {
                datas = JSON.parse(data);
                 
                if(vehicle_type==11)
                    vehicle_type_name = vehicle_type_name+"<br>"+vehicle_type_other;

                $('#trvehicle'+vehicle_id).html('');

                var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get("affidavit.edit") }}" onclick="javascript:edit_vehicle('+datas.id+')"  data-vehicle_type_id="'+vehicle_type+'" data-vehicle_type_other="'+vehicle_type_other+'" data-make="'+make+'" data-registration_no="'+registration_no+'" data-year_of_purchase="'+year_of_purchase+'" data-amount="'+vehicle_amount+'" data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_vehicle'+datas.id+'"> <i class="fa fa-edit"></i> {{Lang::get("affidavit.edit") }} </a>';

				<?php if(Auth::user()->role_id != '19') { ?>
					
                var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get("affidavit.delete") }}" onclick="javascript:delete_vehicle('+datas.id+')"><i class="fa fa-times"></i> {{Lang::get("affidavit.delete") }}</a>';
				
				<?php } else { ?>
				var del = '';	
				<?php } ?>

                

                $('#trvehicle'+vehicle_id).html('<td>'+vehicle_type_name+'</td><td>'+make+'</td><td>'+registration_no+'</td><td>'+year_of_purchase+'</td><td>'+vehicle_amount+'</td><td>'+edit+' '+del+'</td>');
                $("#vehicleModal").modal('hide');
            }
        }
        });
    }
}
</script>

<script type="text/javascript">
function delete_vehicle(id)
{
    $("#modal_delete_vehicle_id").val(id);
    $("#deleteVehicleModal").modal('show');
}
</script>
<script type="text/javascript">
function delete_vehicle_entry()
{
    var id = $("#modal_delete_vehicle_id").val();
    if(id)
    {
    $.ajax({
        url: "{{ url('delete_vehicle') }}",
        type: 'GET',
        data: {  vehicle_id:id },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
        if(data==1)
        {
            $('#trvehicle'+id).remove();
            $("#deleteVehicleModal").modal('hide');
        }
        }
    });
    }
}
</script>
<!-- Vehicle -->


<!-- Jewellery -->
<script type="text/javascript">
function get_val_weight_type(rel_id)
{
    if(rel_id)
    {
        $("#other_valuable_weight"+rel_id).val('');
        var val_weight = $("#val_weight"+rel_id).val();
        if(val_weight==5)
        {
            $("#val_weight_div"+rel_id).css("display", "block");
            $("#other_valuable_weight"+rel_id).attr("required", "required");
        }
        else
        {
            $("#val_weight_div"+rel_id).css("display", "none");
            $("#other_valuable_weight"+rel_id).removeAttr("required");

        }            
    }
}
</script>

<script type="text/javascript">
function get_jewel_type(rel_id)
{
    if(rel_id)
    {
        $("#val_type_other"+rel_id).val('');
        var jewel_type = $("#jewel_type"+rel_id).val();
        if(jewel_type==5)
        {
            $("#jewellery_type_div"+rel_id).css("display", "block");
            $("#val_type_other"+rel_id).attr("required", "required");
        }
        else
        {
            $("#jewellery_type_div"+rel_id).css("display", "none");
            $("#val_type_other"+rel_id).removeAttr("required");

        }            
    }
}
</script>

<script type="text/javascript">
function save_jewellery(cand_id, rel_id)
{
    var jewel_type = $("#jewel_type"+rel_id).val();
    var val_type_other = $("#val_type_other"+rel_id).val();
    var jewel_type_name =  $("#jewel_type"+rel_id+" option:selected").html();   
    var weight_value = $("#weight_value"+rel_id).val();
    var val_weight = $("#val_weight"+rel_id).val();
    var weight_type_other = $("#val_type_other"+rel_id).val();
    var val_weight_name =  $("#val_weight"+rel_id+" option:selected").html();   
    var jewellery_amount = $("#jewellery_amount"+rel_id).val();
    
    if(validate("jewellery_form"+rel_id))
    {
        $.ajax({
        url: "{{ url('save_jewellery') }}",
        type: 'GET',
        data: { 
                cand_id:cand_id, 
                rel_type_id:rel_id,
                jewel_type:jewel_type, 
                val_type_other:val_type_other,
                weight_value:weight_value,
                val_weight:val_weight,
                weight_type_other:weight_type_other,
                jewellery_amount:jewellery_amount
        },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
            if(data!=0)
            {
                 datas = JSON.parse(data);
                 
                if(jewel_type==5)
                    jewel_type_name = jewel_type_name+"<br>"+val_type_other;

                if(val_weight==5)
                    display_weight = weight_value+" "+weight_type_other;
                else
                    display_weight = weight_value+" "+val_weight_name;

                var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get("affidavit.edit") }}" onclick="javascript:edit_jewellery('+datas.id+')"  data-valuable_type_id="'+jewel_type+'" data-valuable_type_other="'+val_type_other+'" data-weight="'+weight_value+'" data-weight_unit_id="'+val_weight+'" data-weight_unit_other="'+weight_type_other+'" data-amount="'+jewellery_amount+'" data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_jewellery'+datas.id+'"> <i class="fa fa-edit"></i> {{Lang::get("affidavit.edit") }} </a>';

                var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get("affidavit.delete") }}" onclick="javascript:delete_jewellery('+datas.id+')"><i class="fa fa-times"></i> {{Lang::get("affidavit.delete") }}</a>';

                 $('#jewellery_relative'+rel_id).prepend('<tr id="trjewellery'+datas.id+'"><td>'+jewel_type_name+'</td><td>'+display_weight+'</td><td>'+jewellery_amount+'</td><td>'+edit+' '+del+'</td></tr>');

                $("#jewel_type"+rel_id).val('');
                $("#val_type_other"+rel_id).val('');
                $("#weight_value"+rel_id).val('');
                $("#val_weight"+rel_id).val('');
                $("#weight_type_other"+rel_id).val('');
                $("#jewellery_amount"+rel_id).val('');
                $("#jewellery_type_div"+rel_id).css("display", "none");
                $("#val_weight_div"+rel_id).css("display", "none");
            }
        }
        });
    }
}
</script>

<script type="text/javascript">
function get_modal_jewel_type()
{
    $("#modal_val_type_other").val('');
    var modal_jewel_type = $("#modal_jewel_type").val();
    if(modal_jewel_type==5)
    {
        $("#modal_jewellery_type_div").css("display", "block");
        $("#modal_val_type_other").attr("required", "required");
    }
    else
    {
        $("#modal_jewellery_type_div").css("display", "none");
        $("#modal_val_type_other").removeAttr("required");

    }  
}
</script>

<script type="text/javascript">
function get_modal_val_weight_type()
{
    $("#modal_other_valuable_weight").val('');
    var modal_val_weight = $("#modal_val_weight").val();
    if(modal_val_weight==5)
    {
        $("#modal_val_weight_div").css("display", "block");
        $("#modal_other_valuable_weight").attr("required", "required");
    }
    else
    {
        $("#modal_val_weight_div").css("display", "none");
        $("#modal_other_valuable_weight").removeAttr("required");

    }
}
</script>

<script type="text/javascript">
function edit_jewellery(id)
{
    var valuable_type_id = "";
    var valuable_type_other =  "";
    var weight =  "";
    var weight_unit_id =  "";
    var weight_unit_other =  "";
    var amount =  "";
    var relation_type_id =  "";
    var candidate_id =  "";
    $("#modal_jewellery_type_div").css("display", "none");
    $("#modal_val_weight_div").css("display", "none");

    valuable_type_id = $("#edit_jewellery"+id).data("valuable_type_id");
    valuable_type_other = $("#edit_jewellery"+id).data("valuable_type_other");
    weight = $("#edit_jewellery"+id).data("weight");
    weight_unit_id = $("#edit_jewellery"+id).data("weight_unit_id");
    weight_unit_other = $("#edit_jewellery"+id).data("weight_unit_other");
    amount = $("#edit_jewellery"+id).data("amount");
    relation_type_id = $("#edit_jewellery"+id).data("relation_type_id");
    candidate_id = $("#edit_jewellery"+id).data("candidate_id");

    if(valuable_type_id==5)
    {
        $("#modal_jewellery_type_div").css("display", "block");
        $("#modal_val_type_other").attr("required", "required");
    }
    if(weight_unit_id==5)
    {
        $("#modal_val_weight_div").css("display", "block");
        $("#modal_other_valuable_weight").attr("required", "required");
    }

    $("#modal_jewel_type").val(valuable_type_id);
    $("#modal_val_type_other").val(valuable_type_other);
    $("#modal_jewel_type option:selected").html();
    $("#modal_weight_value").val(weight);
    $("#modal_val_weight").val(weight_unit_id);
    $("#modal_other_valuable_weight").val(weight_unit_other);
    $("#modal_jewellery_amount").val(amount);
    $("#modal_jewellery_rel_id").val(relation_type_id);
    $("#modal_jewellery_cand_id").val(candidate_id);
    $("#modal_jewellery_id").val(id);
    $("#jewelleryModal").find('span').remove();
    $("#jewelleryModal").modal('show');
}
</script>

<script type="text/javascript">
function update_jewellery()
{
   
    var jewel_type = $("#modal_jewel_type").val();
    var val_type_other = $("#modal_val_type_other").val();
    var jewel_type_name =  $("#modal_jewel_type option:selected").html();   
    var weight_value = $("#modal_weight_value").val();
    var val_weight = $("#modal_val_weight").val();
    var weight_type_other = $("#modal_other_valuable_weight").val();
    var val_weight_name =  $("#modal_val_weight option:selected").html();   
    var jewellery_amount = $("#modal_jewellery_amount").val();
    var cand_id = $("#modal_jewellery_cand_id").val();
    var rel_id = $("#modal_jewellery_rel_id").val();
    var jewel_id = $("#modal_jewellery_id").val();

    if(validate("form_jewelleryModal"))
    {
        $.ajax({
        url: "{{ url($menu_action.'update_jewellery') }}",
        type: 'GET',
        data: { 
                jewel_id:jewel_id, 
                cand_id:cand_id, 
                rel_type_id:rel_id,
                jewel_type:jewel_type, 
                val_type_other:val_type_other,
                weight_value:weight_value,
                val_weight:val_weight,
                weight_type_other:weight_type_other,
                jewellery_amount:jewellery_amount
        },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
            if(data!=0)
            {
                datas = JSON.parse(data);
                 
                if(jewel_type==5)
                    jewel_type_name = jewel_type_name+"<br>"+val_type_other;

                if(val_weight==5)
                    display_weight = weight_value+" "+weight_type_other;
                else
                    display_weight = weight_value+" "+val_weight_name;

                $('#trjewellery'+jewel_id).html('');

                var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get("affidavit.edit") }}" onclick="javascript:edit_jewellery('+datas.id+')"  data-valuable_type_id="'+jewel_type+'" data-valuable_type_other="'+val_type_other+'" data-weight="'+weight_value+'" data-weight_unit_id="'+val_weight+'" data-weight_unit_other="'+weight_type_other+'" data-amount="'+jewellery_amount+'" data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_jewellery'+datas.id+'"> <i class="fa fa-edit"></i> {{Lang::get("affidavit.edit") }} </a>';


				<?php if(Auth::user()->role_id != '19') { ?>
					
                var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get("affidavit.delete") }}" onclick="javascript:delete_jewellery('+datas.id+')"><i class="fa fa-times"></i> {{Lang::get("affidavit.delete") }}</a>';
				
				<?php } else { ?>
				var del = '';	
				<?php } ?>

                

                $('#trjewellery'+jewel_id).html('<td>'+jewel_type_name+'</td><td>'+display_weight+'</td><td>'+jewellery_amount+'</td><td>'+edit+' '+del+'</td>');
                $("#jewelleryModal").modal('hide');
            }
        }
        });
    }
}
</script>

<script type="text/javascript">
function delete_jewellery(id)
{
    $("#modal_delete_jewellery_id").val(id);
    $("#deleteJewelleryModal").modal('show');
}
</script>
<script type="text/javascript">
function delete_jewellery_entry()
{
    var id = $("#modal_delete_jewellery_id").val();
    if(id)
    {
    $.ajax({
        url: "{{ url('delete_jewellery') }}",
        type: 'GET',
        data: {  jewellery:id },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
        if(data==1)
        {
            $('#trjewellery'+id).remove();
            $("#deleteJewelleryModal").modal('hide');
        }
        }
    });
    }
}
</script>
<!-- Jewellery -->

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
        url: "{{ url('save_other') }}",
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

                var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get("affidavit.delete") }}" onclick="javascript:delete_other('+datas.id+')"> <i class="fa fa-times"></i> {{Lang::get("affidavit.delete") }}</a>';

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
    $("#otherModal").find('span').remove();
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
        url: "{{ url($menu_action.'update_other') }}",
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

                var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="{{Lang::get("affidavit.edit") }}" onclick="javascript:edit_other('+datas.id+')"  data-asset_type="'+asset_type+'" data-brief_details="'+brief_details+'" data-amount="'+amount+'"  data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_other'+datas.id+'"> <sp<i class="fa fa-edit"></i> {{Lang::get("affidavit.edit") }} </a>';

				<?php if(Auth::user()->role_id != '19') { ?>
					
                var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get("affidavit.delete") }}" onclick="javascript:delete_other('+datas.id+')"> <i class="fa fa-times"></i> {{Lang::get("affidavit.delete") }}</a>';
				
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
        url: "{{ url('delete_other') }}",
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
                /*if(id=="modal_make")
                {
                    alert(id);
                    alert(value);
                    alert(value.length);
                }*/
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
<script type="text/javascript" src="{{ asset('appoinment/js/bootstrap-multiselect.js') }}" /></script>
<script type="text/javascript">
    $(document).ready(function() {

        $('.selectOne').multiselect();
    });
</script>
<!-- validation -->
@endsection