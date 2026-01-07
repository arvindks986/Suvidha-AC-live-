@extends('admin.layouts.pc.dashboard-theme')
@section('content')
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

  <div class="loader" style="display:none;"></div>
<section class="statistics color-grey pt-4 pb-2">
<div class="container-fluid">
	<div class="row">
	<div class="col">
	 <h4>Nomination Reports</h4>
	</div>
	</div>
</div>	
</section>
<section class="dashboard-header section-padding">
	<div class="container-fluid">
	
				
	    <form id="generate_report_id" class="row" method="post" action="#">
		<div class="form-group col-md-6">
		 {{ csrf_field() }} 
         <label>Datewise Filter</label> &nbsp; <input value="" id="date_range" name="date_range" type="text" class="ranges form-control" placeholder="Date Range" />
		</div>
        
         
         <div class="form-group col-md-6"> <label>Constituency </label> 
          
            <select name="constituency" id="constituency" class="form-control" onchange="getDataByconst(this.value);">
              <option value="" selected="" >All</option>
            @if(isset($list_const))  
            @foreach($list_const as $a)
                <option value="{{$a->CONST_NO}}-{{$a->PC_NAME_EN}}" >{{$a->PC_NAME_EN}}</option>
            @endforeach
            @endif  
            </select>
          </div>
        </form> 	
	
		
	</div>
</section>
<section>
	<div class="container-fluid">
		<div class="row">
			<div class="col">
				 <h5 class="text-center">
        <span id="timeRange">Total Report</span> &nbsp;&nbsp;&nbsp;&nbsp;
        <span id="caseType"></span>
      </h3>
			</div>
		</div>
	</div>
</section>
 <div class="container">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
     <div class="page-contant">
     <div class="random-area">
  <br>

      <?php $i=0;   $totalag=0;  $totalvg=0; $totalrecg=0; $totalwg=0; $totalaccg=0; $totalrg=0; $totalg=0; ?>
	  
	<div class="row mb-3">
	<div class="col text-right">
	  <span class="report-btn" id="export-btn"><a class="btn btn-primary" href="{{ url('/pcdeo/reportspdfview/'.base64_encode('all').'/'.base64_encode('all')) }}" title="Download PDF" target="_blank">Export PDF</a></span>
              <span class="report-btn" id="export-csv-btn"><a class="btn btn-primary" href="{{ url('/pcdeo/reportexcelview/'.base64_encode('all').'/'.base64_encode('all')) }}" title="Download Excel" target="_blank">Export Excel</a></span>
	  </div>
	  </div>
           <div class="table-responsive">
		  <table class="table table-bordered ">
           <thead>
            <tr> <th rowspan="2">Constituency Name</th><th colspan="1">Before Scrutiny</th><th colspan="3">After Scrutiny</th> <th rowspan="2">Total</th> </tr>
            <tr>  <th>Applied</th><th>Withdrawn</th><th>Rejected</th><th>Accepted</th>  </tr>
           </thead>
          <tbody id="oneTimetab">   
      @foreach($allTypeCountArr as $list)
         <?php 
              $totalag=$totalag+$list['totala'];  $totalvg=$totalvg+$list['totalv']; $totalrecg=$totalrecg+$list['totalrec']; 
              $totalwg=$totalwg+$list['totalw']; $totalrg=$totalrg+$list['totalr']; 
              $totalaccg=$totalaccg+$list['totalacc']; $totalg=$totalg+$list['total'];          
       ?>  
               <tr><td>{{$list['const_name']}} </td><td>{{$list['totala']}}</td><td>{{$list['totalw']}}</td><td>{{$list['totalr']}}</td><td>{{$list['totalacc']}}</td><td>{{$list['total']}}</td> </tr>
                @endforeach
                <tr><td>Total:- </td><td>{{$totalag}}</td><td>{{$totalwg}}</td><td>{{$totalrg}}</td><td>{{$totalaccg}}</td><td>{{$totalg}}</td> </tr>  
            
          </tbody>
           </table>
         </div><!-- End Of  table responsive -->  
      </div><!-- End Of intra-table Div -->   
        
         
      </div><!-- End Of random-area Div -->
      
    </div><!-- End OF page-contant Div -->
    </div>      
  </div><!-- End Of parent-wrap Div -->
  </div> 
<script type="text/javascript">
    jQuery.noConflict();
jQuery(document).ready(function() {
        
    jQuery('#date_range').daterangepicker({
			startDate: moment().subtract(2, 'month'),
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
     // alert(val);
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
   var caseType = jQuery("#constituency option:selected").val();

  
     if(caseType!=''){
      var caset= caseType.split('-'); 
      var caseType1 = caset[0];
      var caseType2 = caset[1];
      getcaseType= 'Constituency: '+ caseType2;
     }else{
        getcaseType= '';
     }  
     jQuery(".loader").show();
    // alert(caseType);    
    if(from_date!='' && to_date!=''){   
        jQuery.ajax({
            type:'POST',
            url:'<?php echo url('/') ?>/pcdeo/range-datewisereport', //Make sure your URL is correct
            data:{'_token':'<?php echo csrf_token() ?>', 'from_date': from_date,'to_date': to_date,'const': caseType1},
            success:function(data){         
                //alert('FinalResponse->'+data); 
                //alert(getTimeName);
                var exportpdfbtn = '<span class="report-btn" id="export-btn"><a class="btn btn-primary" href="<?php echo url('/') ?>/pcdeo/reportspdfview/'+btoa(dtStr)+'/'+btoa(caseType1)+'" title="Download to PDF" target="_blank">Export PDF</a></span>';
                var exportcsvbtn = '<span class="report-btn" id="export-csv-btn"><a class="btn btn-primary" href="<?php echo url('/') ?>/pcdeo/reportexcelview/'+btoa(dtStr)+'/'+btoa(caseType1)+'" title="Download to PDF" target="_blank">Export Excel</a></span>';
                // console.log(data);
                jQuery('#export-btn').html(exportpdfbtn);
                jQuery('#export-csv-btn').html(exportcsvbtn);
                jQuery('#oneTimetab').html(data);
                jQuery('#timeRange').html(getTimeName);
                jQuery('#caseType').html(getcaseType);
                jQuery(".loader").hide();  
            },error:function(data){ 
               // console.log(data);  
               return 'Error';    
                //alert('Error !!');
            }
    });
    
    }else{alert('Please select time interval.')}
} 

function getDataByconst(caseType){
    //  alert(caseType);
     var getTimeName=''; var getcaseType='';
    //  var constituency = $('#constituency').val();
     var stateId =  '<?php echo $user_data->st_code ?>';
    var dist =  '<?php echo $user_data->dist_no ?>';
     var selectedTimeName = jQuery('#date_range').val();
     
    var timeInterval= selectedTimeName.split('-'); 
    if(timeInterval[0]!='' && timeInterval[1]!=''){
        var from_date = moment(timeInterval[0]).format('DD-MM-YYYY');
        var to_date = moment(timeInterval[1]).format('DD-MM-YYYY');
        if(from_date!='' && to_date!=''){  
            if(from_date==to_date) {
              var dtStr = from_date+'~'+to_date;
              getTimeName ='Report of:'+from_date;
            }else {
              var dtStr = from_date+'~'+to_date;
              getTimeName ='Report From: '+from_date+'To:'+to_date;
            }
       }
    }    
  
     if(caseType!=''){
      var caset= caseType.split('-'); 
      var caseType1 = caset[0];
      var caseType2 = caset[1];
      getcaseType= 'Constituency: '+ caseType2;
     }else{
        getcaseType= 'Constituency:  All';
     }  
     
     jQuery(".loader").show();
    if(caseType!=''){   
        jQuery.ajax({
            type:'POST',
            url:'<?php echo url('/') ?>/pcdeo/range-datewisereport', //Make sure your URL is correct
            data:{'_token':'<?php echo csrf_token() ?>', 'from_date': from_date,'to_date': to_date, 'const': caseType1},
             success:function(data){ 
                var exportpdfbtn = '<span class="report-btn" id="export-btn"><a class="btn btn-primary" href="<?php echo url('/') ?>/pcdeo/reportspdfview/'+btoa(dtStr)+'/'+btoa(caseType1)+'" title="Download to PDF" target="_blank">Export PDF</a></span>';
                var exportcsvbtn = '<span class="report-btn" id="export-csv-btn"><a class="btn btn-primary" href="<?php echo url('/') ?>/pcdeo/reportexcelview/'+btoa(dtStr)+'/'+btoa(caseType1)+'" title="Download to PDF" target="_blank">Export Excel</a></span>';
                // console.log(data);
                jQuery('#export-btn').html(exportpdfbtn);
                jQuery('#export-csv-btn').html(exportcsvbtn);
                jQuery('#oneTimetab').html(data);
                jQuery('#timeRange').html(getTimeName); 
                jQuery('#caseType').html(getcaseType); 
                jQuery(".loader").hide();   
            },error:function(data){ 
               // console.log(data);  
               return 'Error';    
                //alert('Error !!');
            }
    });
    
    }else{
      jQuery.ajax({
            type:'POST',
            url:'<?php echo url('/') ?>/pcdeo/range-datewisereport', //Make sure your URL is correct
            data:{'_token':'<?php echo csrf_token() ?>', 'from_date': from_date,'to_date': to_date, 'const': caseType1},
             success:function(data){         
                //alert('FinalResponse->'+data); 
                //alert(getTimeName);
                var exportpdfbtn = '<span class="report-btn" id="export-btn"><a class="btn btn-primary" href="<?php echo url('/') ?>/pcdeo/reportspdfview/'+btoa(dtStr)+'/'+btoa(caseType1)+'" title="Download to PDF" target="_blank">Export PDF</a></span>';
                var exportcsvbtn = '<span class="report-btn" id="export-csv-btn"><a class="btn btn-primary" href="<?php echo url('/') ?>/pcdeo/reportexcelview/'+btoa(dtStr)+'/'+btoa(caseType1)+'" title="Download to PDF" target="_blank">Export Excel</a></span>';
                // console.log(data);
                jQuery('#export-btn').html(exportpdfbtn);
                jQuery('#export-csv-btn').html(exportcsvbtn);
                jQuery('#oneTimetab').html(data);
                jQuery('#timeRange').html(getTimeName); 
                jQuery('#caseType').html(getcaseType); 
                jQuery(".loader").hide();   
            },error:function(data){ 
               // console.log(data);  
               return 'Error';    
                //alert('Error !!');
            }
    });
      // location.reload();
    }
}  
  </script>
@endsection