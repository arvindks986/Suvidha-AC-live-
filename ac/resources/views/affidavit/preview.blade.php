@extends( (Auth::user()->role_id != '19') ? 'layouts.theme' : 'admin.layouts.ac.theme')
@section('title', 'Detailed Report') 
@section('content')
<style type="text/css">
.affidavit_nav .step-current a,.affidavit_nav .step-success a{
	color:#fff!important;
}
.affidavit_nav a{
	color:#999!important;
}

.err {
	white-space: pre;
	color: red;
	font-size: 13px;
	font-weight: 600;
	float: left;
	width: 100%;
}
table {
  max-width:824px;
  margin:0 auto;
  border-collapse: separate;
  border-spacing: 0;
  color: #4a4a4d;
  font: 12px/1.4 "Helvetica Neue", Helvetica, Arial, sans-serif;
}
body { font-family: freeserif; }          	  
table, th, td {
  border-collapse: collapse;			  
  padding:05px;
  color:#101010;
  line-height:1.5;			  
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
.thHeading{
	background-color:#ccc;
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
}
.nextBtn:hover {
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
      </style>
<link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }} " type="text/css" />
<link rel="stylesheet" href="{{ asset('appoinment/css/custom.css') }} " type="text/css" />
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
	
	
	@if($data['affidavit_yes'] == '1')
		
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
                <li class="step-current"><b>&#10004;</b><span><a href="{{url($menu_action.'preview')}}">{{Lang::get('affidavit.preview_finalize') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'part-a-detailed-report')}}">{{Lang::get('affidavit.reports') }}</a></span></li>
            </ul>
        </div>
    </div>
	
	@endif
	
    <section>


	
        <div class="col-md-12">
            <div class="row">
                <div class="card">
                    <div class="card-header">   
                    <div class="row justify-content-center">                 
                    <div class="col-sm-10">	
                    	<h3 class="term mt-3">{{Lang::get('affidavit.preview_finalize') }}</h3>
	                	<div class="form-check mt-3">
							  <input class="form-check-input" type="checkbox" value="1" id="defaultCheck1">
							  <label class="form-check-label" for="defaultCheck1">
							    {{Lang::get('affidavit.i_accept_that_all_the_fields_that_i_have') }}
							  </label>
						</div>
						<button onclick="return previewForm();"  class="nextBtn mt-3 float-left">{{Lang::get('affidavit.preview') }}</button>
					</div>
				</div>
                <div class="card-body">	
		<!--<form method="post" name="preview" action="{{url('/finalize')}}" enctype="multipart/form-data">
			<input type="hidden" name="_token" value="{{csrf_token()}}">
			<input type="hidden" name="id" value="{{Auth::user()->id}}">		
			<div class="fullwidth" style="margin-top: 30px;"> 
			<div class="form-group">
			<div class="col">
				<a href id="" class="btn btn-secondary float-left font-big">Back</a>
			</div>
			<div class="col ">
				<div class="form-group row float-right">
					<div style="background:#ee577f;margin-right:50px;color:white;" class="btn btn-primary save_next font-big" ><a style="background:#ee577f; color:white;" href="">Cancel</a></div>
					<div style="background:#D04A8A;margin-right:50px;" class="btn btn-primary save_next font-big" onclick="return finalize();">Finalize</div>
				</div>
			</div>
			</div>
			</div>
		</form> -->
</div>		
</div>	
</div>		
</section>
</main>

<!-- preview Modal -->
<div class="modal fade" id="previewForm" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">{{Lang::get('affidavit.preview') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!--  table section -->
		@include('affidavit.report_common')
        <!--  table Section -->
		
		
      </div>
		<div class="col-sm-12">
			<p class="text-center"> {{Lang::get('affidavit.cilck_on_finalize') }}</p>			 
		</div>	
      <div class="modal-footer">	 	 
      	 <!-- <a href id="" class="backBtn float-left">Back</a>-->	 	
		 
		  <button  class="cencelBtn float-left mr-2" data-dismiss="modal" >{{Lang::get('affidavit.cancel') }}</button>
		  @if(Auth::user()->role_id != '19')
		  <button  class="nextBtn float-right" onclick="return finalize();">{{Lang::get('affidavit.finalize') }}</button>
		  @else
			  <a href="{{url($menu_action.'part-a-detailed-report')}}" class="nextBtn float-right">{{Lang::get('affidavit.next') }}</a>
		  @endif
        <!-- <button type="button" class="cencelBtn" data-dismiss="modal">Close</button>
        <button type="button" class="nextBtn">Finalize</button> -->
      </div>
    </div>
  </div>
</div>
<!-- preview Modal -->
	<!-- The Confirmation Modal Starts Here -->
  <div class="modal fade modal-confirm" id="confirm">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="pop-header py-4">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h2 class="modal-title">{{Lang::get('affidavit.confirmation') }}</h2>
		<div class="header-caption px-4">
		  <p class="font-big">{{Lang::get('affidavit.after_clicking_the_finalize_button') }}</p>	
		</div>		
        </div>
        <!-- Modal footer -->
        <div class="confirm-footer">
			<form method="post" name="preview" action="{{url($menu_action.'finalize')}}" enctype="multipart/form-data">
			<input type="hidden" name="_token" value="{{csrf_token()}}">
			<input type="hidden" name="id" value="{{Auth::user()->id}}">


		  <button type="button" class="btn dark-pink-btn font-big mr-4" data-dismiss="modal">{{Lang::get('affidavit.cancel') }}</button>
          <button type="button" class="btn dark-purple-btn font-big" onclick="submitForm();">{{Lang::get('affidavit.finalize') }}</button>
		  </form>
        </div>
      </div>
    </div>
  </div><!-- End Of confirm Modal popup Div -->
@endsection @section('script')
 <script> 
	function previewForm(){
		$("#span_").remove();
		if($('#defaultCheck1:checked').val()){
			$('#previewForm').modal('show');
		}else{
			$('.form-check').after('<span class="err" id="span_">{{Lang::get("affidavit.please_select_the_above_checkbox") }}</span>');      
            $('#defaultCheck1').css("border-color", "solid 1px red"); 
		}	
	 
	} 
	function submitForm(){
		document.preview.submit();
	}
	function finalize(){
		//alert($('#defaultCheck1').val());
		
	 $('#confirm').modal('show');
	}
  </script>
@endsection
