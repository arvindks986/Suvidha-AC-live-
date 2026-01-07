@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
@section('description', '')
@section('content')
@php
  $st_code=!empty($st_code) ? $st_code : '0';
  $cons_no=!empty($cons_no) ? $cons_no : '0';
  $st=getstatebystatecode($st_code);
  $distname=getdistrictbydistrictno($st_code,$user_data->dist_no);
  $acdetails=getacbyacno($st_code, $cons_no); 
  $acName=!empty($acdetails->AC_NAME) ? $acdetails->AC_NAME : 'ALL';
  $stateName=!empty($st->ST_NAME) ? $st->ST_NAME : 'ALL';
  //echo $st_code.'cons_no=>'.$cons_no;
  
// Get the current URL without the query string...
//  echo $namePrefix = \Route::current()->action['prefix'];
$segments = explode('/', $_SERVER['REQUEST_URI']);
//echo   $nameSuffix = $segments['2'];  $last_date_prescribed_acct_lodge = !empty($noticeatDEO[0]->last_date_prescribed_acct_lodge) && strtotime($noticeatDEO[0]->last_date_prescribed_acct_lodge) > 0 ?date('d-m-Y', strtotime($noticeatDEO[0]->last_date_prescribed_acct_lodge)) : "22-06-2019";

$routesegment=array_slice(explode('/', url()->previous()), -1, 1);
$backurl= ($routesegment[0]=='mis-officer') ? 'eci-expenditure/mis-officer' : 'eci-expenditure/expdashboard';
 

  
@endphp
<main role="main" class="inner cover mb-1">     
    <section class="breadcrumb-section">
        <div class="container-fluid">
            <div class=" row">
                <div class="col-md-5 mt-2 mb-2"><h5 class="mr-auto">Notice At DEO: {{$count}}</h5></div> 
                <div class="col-md-7 mt-2 mb-2 text-right"><p class="mb-0">
                        <b>State Name:</b> 
                        <span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
                        <b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
                        <b>AC:</b> <span class="badge badge-info">{{ $acName }}</span>
                       <!-- <a href="{{url('/eci-expenditure/pendingatroPDF')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">PDF Download</a>--> &nbsp;&nbsp;
                        <a href="{{url('/eci-expenditure/noticeatdeoEXL')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;

                        <b></b> <a href="{{url('/')}}/{{$backurl}}"> <button type="button" id="Back" class="btn btn-primary">Back</button></a>

                    </p></div>
            </div> <!-- end row -->
        </div>

    </section>
    <section class="mt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-8">
                    <div class="card text-left" style="width:100%;">
                        <!--SELECT CANDIDATE-->
                        <div class="card-body" id="demo" class="collapse show">  
						  <div class="table-responsive">
                            <table id="example1" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <tr>
		  <th>Action</th>
	      <th>State Name</th>
          <th>AC No & Name</th>
          <th>Candidate Name</th>
          <th>Party Name</th>
          <th>Last Date of Submission</th>
          <th>Date of Scrutiny Report Submission</th>
          <th>Date of Lodging A/C By Candidate</th>
          <th>Date of Sending Notice to the DEO</th>
          <th>Date of Sending Scrutiny form</th>
        </tr>
        </thead>
<?php $j=0; 


?>
    @if(!empty($noticeatDEO))
    @foreach($noticeatDEO as $candDetails)  
      <?php
       //dd($candDetails);
	    $stdetails=getstatebystatecode($candDetails->ST_CODE);
       $acDetails=getacbyacno($candDetails->ST_CODE,$candDetails->ac_no);
       $date = new DateTime($candDetails->created_at);
       //echo $date->format('d.m.Y'); // 31.07.2012
       $lodgingDate=$date->format('d-m-Y'); // 31-07-2012
     $last_date_prescribed_acct_lodge = !empty($candDetails->last_date_prescribed_acct_lodge) && strtotime($candDetails->last_date_prescribed_acct_lodge) > 0 ?date('d-m-Y', strtotime($candDetails->last_date_prescribed_acct_lodge)) : "N/A";
        $j++; 
        ?>
<tr>
<td>  @if(!empty($candDetails->ac_no))
                <a href="{{url('/')}}/eci-expenditure/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary btn-sm width-75" target="_blank">Report</a> 
                @endif <a href="javascript:void(0)" class="btn btn-info btn-sm width-75"
							 onclick="showTracking({{($candDetails->candidate_id)}})" >Tracking</a></td>
<td>@if(!empty($stdetails->ST_NAME)) {{ $stdetails->ST_NAME}} @endif</td>
<td>@if(!empty($candDetails->ac_no)) {{ $candDetails->ac_no}} - {{ $acDetails->AC_NAME}} @endif</td>
<td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
<td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
<td>{{$last_date_prescribed_acct_lodge}}</td>
<td>@if(!empty($candDetails->report_submitted_date) && strtotime($candDetails->report_submitted_date)>0) {{ date('d-m-Y',strtotime($candDetails->report_submitted_date))}}  @else {{ 'N/A'}} @endif</td>
<td>@if(!empty($candDetails->date_orginal_acct) && strtotime($candDetails->date_orginal_acct)>0) {{ date('d-m-Y',strtotime($candDetails->date_orginal_acct))}} @else {{ 'N/A'}} @endif</td>
<td>@if(!empty($candDetails->date_sending_notice_service_to_deo) && $candDetails->date_sending_notice_service_to_deo !='0000-00-00') {{  date('d-m-Y',strtotime($candDetails->date_sending_notice_service_to_deo))}} @else {{ 'N/A'}} @endif</td>
<td>@if(!empty($candDetails->report_submitted_date) && ($candDetails->report_submitted_date !='0000-00-00')) {{ date('d-m-Y',strtotime($candDetails->report_submitted_date))}}  @else {{ 'N/A'}} @endif</td>

</tr>
@endforeach 
@endif 
<tbody>
             </tbody>
            </table>
                         </div> </div>
                    </div>
                    <!--END OF SELECT CANDIDATE-->
                </div>
               <!--Start Of Tracking Div-->	
<div class="col-lg-4 col-md-4 col-sm-4 menu1" style="">
	<div class="card" id="showTracking" style="display: none;"></div>
</div>
	</div>
    </div>
  </div>
 </div>
<!--End Of Tracking Div-->
    </section>
</main>
<script  src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script>
$(document).ready(function() {
    var table = $('#example1').DataTable({   
     dom: 'lBfrtip', 
     lengthMenu: [ [10, 50, 100, -1], [10, 50, 100, 'All'] ],
     pageLength: 10,
     buttons: [
            {
                extend: 'pdfHtml5',               
                pageSize: 'LEGAL',
               filename: function() {
                return 'noticeatDEO-report';    
              },
             title: function() {
                  return '<?php echo 'State Name:'.$stateName.'   AC:'.$acName.''; ?>'
              },
            }],
    });
  })
  
  function showTracking(candidate_id){
		 $('#showTracking').css('display','block');
		var candidate_id = candidate_id;
		//alert(candidate_id);
		 $.ajax({
			url: '<?php echo url('/') ?>/eci-expenditure/getCandTracking/'+candidate_id,
            type: 'GET',
           // data: { _token: '{{csrf_token()}}' },
		    success: function(response){
			// Code
			var html = '';
			//console.log(response);
			$('#showTracking').html(response);
		}
		});
	}
  </script>

@endsection