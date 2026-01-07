@extends( (Auth::user()->role_id != '19') ? 'layouts.theme' : 'admin.layouts.ac.theme')
@section('title', 'Part A Detailed Report') 
@section('content')
<link rel="stylesheet" href="{{ asset('appoinment/css/custom-dark.css') }} " type="text/css" />
<style type="text/css">
.affidavit_nav .step-current a,.affidavit_nav .step-success a{
	color:#fff!important;
}
.affidavit_nav a{
	color:#999!important;
}
			table {
			  max-width:824px;
			  margin:0 auto;
			  border-collapse: separate;
			  border-spacing: 0;
			  line-height:1.6;
			  color: #4a4a4d;
			  font: 12px/1.4 "Helvetica Neue", Helvetica, Arial, sans-serif;
			}
           body { font-family: freeserif; }          	  
		   table, th, td {
		      border-collapse: collapse;			  
			  padding:05px;
			  color:#101010;
			  line-height:1.6;
		   }
		   th{
		   	font-weight: bold
		   }
		   th, td {
			  padding: 10px;
			  vertical-align: middle;	
			  font-size:14px;			  
			}
			.top th, .top td {
			  padding: 10px;
			  vertical-align: top;	
			  font-size:14px;			  
			}
			.bold{font-weight: bold;}
			input{
				border:0;
				outline: 0;
				border-bottom: dotted 0.5px ;
			}
			textarea{
				outline: 0;
				width: 100%;
				border:0;
				border-bottom: dotted 0.5px ;ss
			}
			.padd-0{
				padding: 0px!important;
			}
			.bdrLeass{
				border-style: hidden!important;
			}
			.red{color:red;}
			.block{
				display: block;
			}
			.inBlock{
				display: inline-block;
			}
			.w-20{width: 20px; display: inline-block;}
			.pad-20{
				padding-left: 27px;
			}
			.pad-35{
				padding-left: 35px;
			}

			.top td, .top td * {
			    vertical-align: top;
			}
			.top td{
			    vertical-align: top;
			}
			.top td{
			    vertical-align: top;
			}
			.top-20{
				margin-top: 20px;
			}
			.w-100{
				width: 100%;
			}
			ul.list{
				width: 100%;
				list-style: none;
				margin: 0px;
				padding: 0px;
				margin-top: 15px;
			}
			ul.list li{
				margin-top: 15px;
				line-height: 1.9;
			}
			#example7 { text-align: justify; }
			td.justify {
				text-align:justify!important;text-align: justify; text-justify: inter-word; }
  		    tr.noBorder td {
		      border: 0!important;
		    }
		tr.noBorder th{
		  border: 0!important;
		}
			.lineHeght-25{
				line-height: 25px;
			}
			.inputLine{
				padding-left: 10px; 
				padding-right: 10px; 
				width: 150px; 
				font-weight: bold;
			}

			.nextBtn {
                border: 2px solid #9b59b6;
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
                color: #9b59b6;
                text-decoration: none!important;
            }
            .nextBtn:hover {
                background-color: #9b59b6;
                color: white;
                outline: none;
                text-decoration: none;
                box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
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
			.top th, .top td {
			  padding: 10px;
			  vertical-align: top;	
			  font-size:14px;			  
			}
			.bold{font-weight: bold;}
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
			.reporthd{
				background-color:#ccc;
			}
			.thHeading{
				background-color:#ccc;
			}
      </style>


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
        </div>
    </section>
<?php if(Auth::user()->role_id == '19'){
	$menu_action = 'roac/';
}else{
	$menu_action = '';
} ?>
	
	@if($data['affidavit_yes'] =='1')
		
	<style>
	
	section.breadcrumb-section {
		display: none;
	}
	
	</style>
	@else
		
	<div class="container-fliud">
       <div class="step-wrap mt-4">
            <ul class="affidavit_nav">
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'affidavitdashboard')}}">{{Lang::get('affidavit.initial_details') }}</a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'affidavit/candidatedetails')}}">{{Lang::get('affidavit.candidate_details') }}</a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'affidavit/pending-criminal-cases')}}">{{Lang::get('affidavit.court_cases') }}</a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'Affidavit/MovableAssets')}}">{{Lang::get('affidavit.movable_assets') }}</a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'immovable-assets')}}">{{Lang::get('affidavit.immovable_assets') }}</a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'liabilities')}}">{{Lang::get('affidavit.liabilities') }}</a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'Profession')}}">{{Lang::get('affidavit.profession') }}</a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'education')}}">{{Lang::get('affidavit.education')}}</a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'preview')}}">{{Lang::get('affidavit.preview_finalize') }}</a></span></li>
                <li class="step-current"><b>&#10004;</b><span><a href="{{url($menu_action.'part-a-detailed-report')}}">{{Lang::get('affidavit.reports') }}</a></span></li>
            </ul>
        </div>
    </div>
	
	@endif
	

    <section>
        <div class="col-md-12">
            <div class="row">
                <div class="card">
                    <div class="card-header">
                        <div class="container">
                            <div class="col-sm-12">
							<div class="row">
                            <div class="col-sm-4">
                                <h4 class="pt-2 "><strong>{{Lang::get('affidavit.detailed_report')}}</strong></h4>
								<br/>
								<b style="font-size: 15px; ">{{Lang::get('affidavit.affidavit_id')}} </b>: {{@$data['cand_details']->affidavit_id}}
							</div>
							<div class="col-sm-4">	

								@if(Auth::user()->role_id != '19')
                                <a href="{{url($menu_action.'affidavit-e-file')}}" style="border: 2px solid #d04a8a; color: #d04a8a; text-align: center;" class="nextBtn">{{Lang::get('affidavit.go_to_my_affidavit')}}</a>
								@endif
								
								
							</div>
							<div class="col-sm-4">							
                                <a href="{{url($menu_action.'part-a-detailed-report?pdf=yes&affidavit_id='.@$data['cand_details']->affidavit_id)}}" class="nextBtn float-right">{{Lang::get('affidavit.download')}}</a>
                            </div>							
                        </div>
                        </div>
                        </div>
                        <div class="card-body pt-5"> 
    
						@include('affidavit.report_common')

						</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection @section('script')
@endsection
