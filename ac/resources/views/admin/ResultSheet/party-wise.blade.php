@extends('admin.layouts.ac.dashboard-theme')
@section('content')

<link rel="stylesheet" href="{{ asset('css/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/jquery.stickytable.min.css') }}">

<style type="text/css">
  .loader {
   position: fixed;
   left: 50%;
   right: 50%;
   border: 16px solid #f3f3f3; /* Light grey */
   border-top: 16px solid #3498db; /* Blue */
   border-radius: 50%;
   width: 120px;
   height: 120px;
   animation: spin 2s linear infinite;
   z-index: 99999;
  }
      @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
    }

#acViewBody a{
    text-decoration: none !important;
    color: #000 !important;
    cursor: default !important;
}

#acViewBody a:hover{
    text-decoration: none !important;
    color: #000 !important;
    cursor: default !important;
}
.bold{font-weight:bold;}

.swatch-yellow {
   color: #fff;
    background-color: #17a2b8; padding: 10px;
}
.form-control:disabled, .form-control[readonly]{background:#fff; height:46px; border:1px solid #d5d5d5;}
button.btn.dropdown-toggle.btn-light.bs-placeholder {
    background: #fff;
    border: 1px solid #d5d5d5;
    border-radius: 0px;
    height: 37px;
}
button.btn.dropdown-toggle.btn-light {
    background: #fff;
    border: 1px solid #d5d5d5;
    border-radius: 0px;
    height: 37px;
}
.form-control:disabled, .form-control[readonly]{height:37px;}
.form-control:focus, .form-control:hover{box-shadow:none;}
#divChart {
  margin: auto;
  width: 73%;
   border: 3px solid white;
   border:0px solid #ddd
}
#divChart1 {
  margin: auto;
  width: 70%;
  border: 0px !important;
}
</style>

<div class="loader" style="display:none;"></div>
<section class="statistics color-grey pt-4 pb-2">
<div class="container-fluid">
  <div class="row">
  <div class="col-md-6 pull-left">
   <h4 id="heading_con">Result Sheet</h4>
   Date : {{date('d/m/Y H:i:s A')}} 
  </div>
	
    <div class="col-md-3  pull-right text-right"> 
      <span id="button_con"  class="report-btn" id="export-pdf-btn"><a class="btn btn-primary" href="{{url('eci/party-wise?pdf=yes')}}" title="Download PDF" >Export PDF</a></span>  
    </div>
	
	<div class="col-md-3  pull-right text-right"> 
      User : {{$user_data->officerlevel}}
    </div>
  </div>
</div>  
</section>


<div class="container-fluid" id="DivIdToPrint">
<div class="row">
	<div  class="col mt-5">
	
		<table id="list-table"  class="table table-striped table-bordered datatable  ">
		
		
		
		@if(count($data) > 0 )
		
		@foreach($data as  $key => $data)
		
		@php $i=1 @endphp

			<thead>	
			
					<tr class="sticky-header">
						<th style="color:black;text-align: center;" colspan="5"> State - {{getstatebystatecode($key)->ST_NAME}}</th>
					</tr>
					<tr class="sticky-header">
					<th style="color:black;"> Sl. No. </th>
					<th style="color:black;">Party Name</th>
				
					<th style="color:black;">Won</th>
					<th style="color:black;">Leading</th>
					
					<th style="color:black;">Total </th>
					</tr>
			 </thead>
		
		<tbody>


			@if(count($data['topten']) >0)

			@foreach($data['topten'] as $ten)
			<tr>
				<td> {{$i}} </td> 
				<td> {{$ten->lead_cand_party}} </td> 
				<td> {{$ten->win}} </td> 
				<td> {{$ten->lead}} </td> 
				<td> {{$ten->win + $ten->lead}} </td> 
			</tr>
			@php $i++; @endphp
			@endforeach
		
			@endif
			
			@if($data['independent'])
			<tr>
				<td> {{$i}} </td> 
				<td> {{$data['independent']->lead_cand_party}} </td> 
				<td> {{$data['independent']->win}} </td> 
				<td> {{$data['independent']->lead}} </td> 
				<td> {{$data['independent']->win + $data['independent']->lead}} </td> 
			</tr>
			@php $i++; @endphp
			@endif
			
			
			@if(count($data['others'])> 0)
			<tr>
				<td> {{$i}} </td> 
				<td> {{$data['others']['lead_cand_party']}} </td> 
				<td> {{$data['others']['win']}} </td> 
				<td> {{$data['others']['lead']}} </td> 
				<td> {{$data['others']['win'] + $data['others']['lead']}} </td> 
			</tr>
			@endif
		
		
		@endforeach
		
		</tbody>
		
		@else 
		<tr>
			<td colspan="5">  No record available </td> 
		</tr>
		
		
		@endif
       
	   
	   </table>
	</div>
</div>
 </div>

@endsection




