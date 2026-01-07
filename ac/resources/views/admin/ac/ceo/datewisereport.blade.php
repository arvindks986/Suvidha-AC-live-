@extends('admin.layouts.report-theme')
@section('content')
  <link href="{{ asset('css/daterangepicker.css')}}" rel="stylesheet"/>
 <div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
     <div class="page-contant">
     <div class="random-area">
    <div class="intra-table">
        <div class="head-title">
          <h3><i><img src="{{ asset('admintheme/images/icons/tab-icon-002.png')}}" /></i>Datewise All Constituency Application Reports</h3>
        </div>

  <div class="row">

      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> 
         
        <form id="generate_report_id" method="post" action="#">
         {{ csrf_field() }} 
          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"> <lable>Datewise Filter</lable> </div>
         <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"> 
                <input value="" id="date_range" name="date_range" type="text" class="form-control" placeholder="Date Range" />
         </div>
         <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"> <lable>Constituency </lable> </div> 
         <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">            
            <select name="constituency" id="constituency"  style="width:150px" onchange="getDataByconst(this.value);">
              <option value="" selected="" >All</option>  
            @if(isset($list_const))  
            @foreach($list_const as $a)
                <?Php if($a->CONST_TYPE=='AC') {
                          $const=\app(App\adminmodel\AcMaster::class)->where(['ST_CODE' =>$a->ST_CODE])->where(['AC_NO' =>$a->CONST_NO])->first();  
                          $const_name=$const->AC_NAME;
                        }
                      if($a->CONST_TYPE=='PC') {
                          $const=\app(App\adminmodel\AcMaster::class)->where(['ST_CODE' =>$a->ST_CODE])->where(['PC_NO' =>$a->CONST_NO])->first();
                          $const_name=$const->PC_NAME;
                        } 
                ?>
                <option value="{{ $a->CONST_NO}}-{{$const_name}}" >{{ $a->CONST_NO}}-{{$const_name}}</option> 
            @endforeach
            @endif  
            </select>
          </div>
        </form>   
        
      </div>
    
      <h3> 
        <span id="stateNameSpn" style="font-size:18px;"><b>All Constituency Report</b></span>
        <span id="timeRange" style="padding-left:30px; font-size:18px;"><b>Report of: </b> {{ date('d-m-Y')}} </span>
        <span id="caseType" style="padding-left:30px; font-size:18px;"></span>
 
      </h3>
    </div> <br>

      <?php $i=0;   $totalag=0;  $totalvg=0; $totalrecg=0; $totalwg=0; $totalaccg=0; $totalrg=0; $totalg=0; ?>
       
           <div class="table-responsive">
           <table class="table table-bordered">
           <thead>
            <tr> <th rowspan="2">Constituency Name</th><th colspan="3">Before Scrutiny</th><th colspan="3">After Scrutiny</th> <th rowspan="2">Total</th> </tr>
            <tr>  <th>Applied</th><th>Verified</th><th>Receipt Print</th><th>Withdrawn</th><th>Rejected</th><th>Accepted</th>  </tr>
           </thead>
          <tbody id="oneTimetab">   
      @foreach($allTypeCountArr as $list)
         <?php 
              $totalag=$totalag+$list['totala'];  $totalvg=$totalvg+$list['totalv']; $totalrecg=$totalrecg+$list['totalrec']; 
              $totalwg=$totalwg+$list['totalw']; $totalrg=$totalrg+$list['totalr']; 
              $totalaccg=$totalaccg+$list['totalacc']; $totalg=$totalg+$list['total'];          
       ?>  
               <tr><td>{{$list['const_no']}}-{{$list['const_name']}} </td><td>{{$list['totala']}}</td> <td>{{$list['totalv']}}</td><td>{{$list['totalrec']}}</td><td>{{$list['totalw']}}</td><td>{{$list['totalr']}}</td><td>{{$list['totalacc']}}</td><td>{{$list['total']}}</td> </tr>
                @endforeach
                <tr><td>Total:- </td><td>{{$totalag}}</td> <td>{{$totalvg}}</td><td>{{$totalrecg}}</td><td>{{$totalwg}}</td><td>{{$totalrg}}</td><td>{{$totalaccg}}</td><td>{{$totalg}}</td> </tr>  
            
          </tbody>   
           </table>
         </div><!-- End Of  table responsive -->  
      </div><!-- End Of intra-table Div -->   
        
         
      </div><!-- End Of random-area Div -->
      
    </div><!-- End OF page-contant Div -->
    </div>      
  </div><!-- End Of parent-wrap Div -->
  </div> 
 
@endsection
<script src="{{ asset('js/jquery.js') }} "></script>
  
<script type="text/javascript" src="{{ asset('js/moment.min.js') }}"></script>
<script src="{{ asset('js/daterangepicker.js') }} "></script>
<script type="text/javascript">
  
jQuery(document).ready(function() { 
        jQuery.noConflict();
    jQuery('#date_range').daterangepicker({
       ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 14 Days': [moment().subtract(13, 'days'), moment()]           
           //'This Month': [moment().startOf('month'), moment().endOf('month')],
           //'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        maxDate: new Date()
});
});
   
jQuery(document).on('click', '.ranges', function(e){
    var val=  jQuery('#date_range').val();
     // alert(val);
      getDataByTimeInterval(val);
   
    });
 
 function getDataByTimeInterval(val){   
   // alert(timeInterval);
    var getTimeName='';  
   var timeInterval= val.split('-'); 
    if(timeInterval[0]!='' && timeInterval[1]!=''){
        var from_date = moment(timeInterval[0]).format('DD-MM-YYYY');
        var to_date = moment(timeInterval[1]).format('DD-MM-YYYY');
        if(from_date!='' && to_date!=''){  
            var dtStr = from_date+'~'+to_date;
          getTimeName ='<b>Report From: </b>'+from_date+'<b> To: </b>'+to_date;
       }
    }
   var caseType = jQuery("#constituency option:selected").val();

  
     if(caseType!=''){
      var caset= caseType.split('-'); 
      var caseType1 = caset[0];
      var caseType2 = caset[1];
      getcaseType= '<b>Constituency: </b>'+ caseType2;
     }else{
        getcaseType= '<b>Constituency: </b> All';
     }  
     
    // alert(caseType);    
    if(from_date!='' && to_date!=''){   
        jQuery.ajax({
            type:'POST',
            url:'<?php echo url('/') ?>/ceo/range-datewisereport', //Make sure your URL is correct
            data:{'_token':'<?php echo csrf_token() ?>', 'from_date': from_date,'to_date': to_date,'const': caseType1},
            success:function(data){         
                //alert('FinalResponse->'+data); 
                //alert(getTimeName);
                
                jQuery('#oneTimetab').html(data);
                jQuery('#timeRange').html(getTimeName); 
                jQuery('#caseType').html(getcaseType);    
            },error:function(data){ 
               // console.log(data);  
               return 'Error';    
                //alert('Error !!');
            }
    });
    
    }else{alert('Please select time interval.')}
} 

function getDataByconst(caseType){
     // alert(caseType);
     var getTimeName=''; var getcaseType='';
     var selectedTimeName = jQuery('#date_range').val();
     
    var timeInterval= selectedTimeName.split('-'); 
    if(timeInterval[0]!='' && timeInterval[1]!=''){
        var from_date = moment(timeInterval[0]).format('DD-MM-YYYY');
        var to_date = moment(timeInterval[1]).format('DD-MM-YYYY');
        if(from_date!='' && to_date!=''){  
            var dtStr = from_date+'~'+to_date;
          getTimeName ='<b>Report From: </b>'+from_date+'<b> To: </b>'+to_date;
       }
    }    
  
     if(caseType!=''){
      var caset= caseType.split('-'); 
      var caseType1 = caset[0];
      var caseType2 = caset[1];
      getcaseType= '<b>Constituency: </b>'+ caseType2;
     }else{
        getcaseType= '<b>Constituency: </b> All';
     }  
     
       
    if(caseType!=''){   
        jQuery.ajax({
            type:'POST',
            url:'<?php echo url('/') ?>/ceo/range-datewisereport', //Make sure your URL is correct
            data:{'_token':'<?php echo csrf_token() ?>', 'from_date': from_date,'to_date': to_date, 'const': caseType1},
             success:function(data){         
                //alert('FinalResponse->'+data); 
                //alert(getTimeName);
                
                jQuery('#oneTimetab').html(data);
                jQuery('#timeRange').html(getTimeName); 
                jQuery('#caseType').html(getcaseType);    
            },error:function(data){ 
               // console.log(data);  
               return 'Error';    
                //alert('Error !!');
            }
    });
    
    }else{alert('Please select Constituency.')}
}      
 

 
</script>