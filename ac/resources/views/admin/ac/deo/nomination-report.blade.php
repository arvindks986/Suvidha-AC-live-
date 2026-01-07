@extends('admin.layouts.ac.dashboard-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Nomination Report')
@section('content')

<?php
 $st=getstatebystatecode($user_data->st_code); 
 $dist=getdistrictbydistrictno($st_code,$dist_no);
$url = URL::to("/"); $j=1;
?>
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
  </style>
<main role="main" class="inner cover mb-3">
	<section class="mt-5">
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
              <div class=" card-header">
                <div class=" row">
                   <div class="col"><h2 class="mr-auto">Nomination Report </h2></div> 
                   <div class="col"><p class="mb-0 text-right">
												<b>State Name:</b> 
												<span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; 
												
			  <b>District Name:</b> 
			  @if(isset($dist) && $dist)
			  <span class="badge badge-info">{{$dist->DIST_NAME}}</span> @endif &nbsp;&nbsp; <b></b> 									  </p></div>
										</div><!-- end row-->
	              </div><!-- end card-header-->

  <div class="loader" style="display:none;"></div>
  <section class="dashboard-header section-padding">
	<div class="container-fluid">
	    <form id="generate_report_id" class="row" method="post" action="#">
      <input type="hidden" name="st_code" id="st_code" value="{{$user_data->st_code }}">
      <input type="hidden" name="ac_no" id="ac_no" value="{{$user_data->ac_no }}">
      {{ csrf_field() }} 
		<div class="form-group col-md-6">
         <label>Datewise Filter</label> &nbsp; 
         <input value="" id="date_range" name="date_range" type="text" class="ranges form-control" placeholder="Date Range" />
		</div>
         <div class="form-group col-md-6"> <label> </label> 
         <p class="mb-0 text-right"><span class="report-btn" id="export-btn">
		 <!--<a class="btn btn-primary" href="<?php echo url('/') ?>/ropc/form3apdfview/'+btoa(dtStr)+'/'+btoa(caseType1)+'" title="Download to PDF" target="_blank">Export PDF</a>-->
		 </span>
         </p></div>
        </form> 
	</div>
</section>
                
   
<div class="card-body">  
  <div class="table-responsive" id='showrecord'>
      <table id="examplesss" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <tr>
          <th>AC Number</th> 
          <th>AC Name</th>
          <th>Total Nominations</th> 
        </tr>
        </thead>
        <tbody id="oneTimetab">	
        <?php $count = 1;  $i=0;    $totalrg=0; $totalwg=0; $totalaccg=0;  $totalg=0; ?>
        @if(!empty($allAcList))
         @foreach($allAcList as $acList)
          <?php
              $ac=getacbyacno($acList->st_code,$acList->ac_no);
              $totalg=$totalg+$acList->totalnomination;  
            ?>  
       <tr>
            <td>{{$ac->AC_NO }}</td>  
            <td ><a target="" href="{{url('/acdeo/candidatelist-ac/'.$ac->AC_NO.'/')}}">{{ $ac->AC_NAME }}</a></td>
            <td><a target="" href="{{url('/acdeo/candidatelist-ac/'.$ac->AC_NO.'/')}}">@if(!empty($acList->totalnomination)){{ $acList->totalnomination }} @endif</a></td>
          </tr>
          <?php //$count++ ?>
          @endforeach
               <tr> 
                  <td>Total:- </td>
                  <td> </td> 
                  <td>{{$totalg}}</td>
                 </tr>
                 @endif 
              </tbody>
            </table>
           </div> <!-- end responcive-->
          </div> <!-- end card-body-->
        </div>
      </div>
     </div>
   	</div>
  </section>
	</main>
  @section('script') 
@endsection



<script type="text/javascript">
jQuery.noConflict();
jQuery(document).ready(function() {
    jQuery('#date_range').daterangepicker({
			startDate: moment('<?php echo $sched->LDT_IS_NOM; ?>'),
			endDate: moment(),
       ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          //  'Last 14 Days': [moment().subtract(13, 'days'), moment()] ,          
          //  'This Month': [moment().startOf('month'), moment().endOf('month')],
          //  'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        maxDate: new Date()
});
});  
   
jQuery(document).on('click', '.ranges, .applyBtn', function(e){
    var val=  jQuery('#date_range').val();
      //alert(val);
      getDataByTimeInterval(val);
    });
 
 function getDataByTimeInterval(val){   
   // alert(timeInterval);
    var getTimeName='';  
    // var constituency = $('#constituency').val();
    var timeInterval= val.split('-'); 
    if(timeInterval[0]!='' && timeInterval[1]!=''){
        var from_date = moment(timeInterval[0]).format('DD-MM-YYYY');
        var to_date = moment(timeInterval[1]).format('DD-MM-YYYY');
        // alert(to_date);
        if(from_date!='' && to_date!=''){  
            if(from_date==to_date) {
              var dtStr = from_date+'~'+to_date;
              getTimeName ='Report of: '+from_date;
            }else {
              var dtStr = from_date+'~'+to_date;
              getTimeName ='Report From: '+from_date+' To: '+to_date;
            }
       }
    }
     var st_code =  jQuery('input[name="st_code"]').val();
     var ac_no =   jQuery('input[name="ac_no"]').val();
     //alert(st_code); alert(ac_no);   
     //jQuery(".loader").show();
    // alert(to_date);    
    if(from_date!='' && to_date!=''){ //alert(from_date);  alert(to_date); 
        jQuery.ajax({
            type:'POST',
            url:'<?php echo url('/') ?>/acdeo/datewisenominationreport', //Make sure your URL is correct
            data:{'_token':'<?php echo csrf_token() ?>', 'from_date': from_date,'to_date': to_date,'st_code': st_code,'ac_no': ac_no},
            success:function(data){         
                //alert('FinalResponse->'+data); 
                //alert(getTimeName);
               // var exportpdfbtn = '<span class="report-btn" id="export-btn"><a class="btn btn-primary" href="<?php echo url('/') ?>/ropc/form3apdfview/'+btoa(dtStr)+'/'+btoa(pc_no)+'" title="Download to PDF" target="_blank">Export PDF</a></span>';
               // var exportcsvbtn = '<span class="report-btn" id="export-csv-btn"><a class="btn btn-primary" href="<?php echo url('/') ?>/ropc/reportexcelview/'+btoa(dtStr)+'/'+btoa(caseType1)+'" title="Download to PDF" target="_blank">Export Excel</a></span>';
               //  console.log(data);
               // jQuery('#export-btn').html(exportpdfbtn);
               // jQuery('#export-csv-btn').html(exportcsvbtn);
                jQuery('#oneTimetab').html(data);
               // jQuery('#timeRange').html(getTimeName);
               // jQuery('#caseType').html(getcaseType);
                //jQuery(".loader").hide();  
            },error:function(data){ 
               // console.log(data);  
               return 'Error';    
                //alert('Error !!');
            }
    });
    
    }else{alert('Please select time interval.')}
} 

  </script>
  
@endsection