@extends('admin.layouts.ac.theme')
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
   <h4>Scrutiny Reports</h4>
  </div>
  </div>
</div>  
</section>
<section class="dashboard-header section-padding">
  <div class="container-fluid">
  
        
      <form id="generate_report_id" class="row" method="get" onsubmit="return false;">
    <div class="form-group col-md-6">
         <label>Datewise Filter</label> &nbsp; <input value="" id="date_range" name="date_range" type="text" class="ranges form-control" placeholder="Date Range" />
    </div>


    

          <div class="form-group col-md-3"> <label>Phases </label> 
          
            <select name="phase" id="phase" class="form-control" onchange ="filter()">
            <option value="">Select Phase</option>
            @foreach($phases as $result)
              @if($phase==$result->SCHEDULEID)
                <option value="{{$result->SCHEDULEID}}" selected="selected" >Phase-{{$result->SCHEDULEID}}</option> 
              @else 
                <option value="{{$result->SCHEDULEID}}" >Phase-{{$result->SCHEDULEID}}</option> 
              @endif  
            @endforeach
        
            </select>
          </div>
         
         <div class="form-group col-md-3"> <label>Constituency </label> 
          
            <select name="constituency" id="constituency" class="form-control" onchange ="filter()">
              <option value="">Select Constituency</option>
            @if(isset($list_const))
            @foreach($list_const as $a)
              @if($constituency==$a->CONST_NO)
                <option value="{{$a->CONST_NO}}" selected="{{$constituency}}" >{{$a->AC_NAME}}</option> 
              @else 
                <option value="{{$a->CONST_NO}}" >{{$a->AC_NAME}}</option> 
              @endif   
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
        <span id="timeRange">{!! $heading_title !!}</span>
      </h5>
      </div>
    </div>
  </div>
</section>
<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
     <div class="page-contant">
     <div class="random-area">
  <br>

    
  <div class="row mb-3">
  <div class="col text-right">
    @if(count($results)>0)
    <span class="report-btn" id="export-csv-btn"><a class="btn btn-primary" href="{{ $downlaod_to_excel }}" title="Download Excel" target="_blank">Export Excel</a></span>
    <span class="report-btn" id="export-pdf-btn"><a class="btn btn-primary" href="{{ $downlaod_to_pdf }}" title="Download PDF" target="_blank">Export PDF</a></span>
    @endif
  </div>
    </div>
           <div class="table-responsive">
      <table class="table table-bordered ">
           <thead>
            <tr> 

              <th rowspan="2">Constituency Name</th>
              <th colspan="1">Before Scrutiny</th>
              <th colspan="4">After Scrutiny</th> 
           
            </tr>
            <tr>  
              <th>Total Nomination Applied</th>
              <th>Accepted</th> 
              <th>Rejected</th>
              <th>Withdrawn</th>
              <th>Contesting</th> 
            </tr>
          </thead>
          <tbody id="oneTimetab">   
              @foreach($results as $result)
              <tr>
                <td>{{$result['label']}} </td>
                <td>
                @if($result['total_applied']>0)
                <a href="<?php echo $action.'/detail/applied?'.$result['filter'] ?>">
                {{$result['total_applied']}}
                </a>
                @else
                {{$result['total_applied']}}
                @endif

                </td>
                
                <td>
                @if($result['total_accepted']>0)
                <a href="<?php echo $action.'/detail/accepted?'.$result['filter'] ?>">
                {{$result['total_accepted']}}</a>
                @else
                {{$result['total_accepted']}}
                @endif
                </td>

                <td>
                @if($result['total_rejected']>0)
                <a href="<?php echo $action.'/detail/rejected?'.$result['filter'] ?>">
                {{$result['total_rejected']}}</a>
                @else
                {{$result['total_rejected']}}
                @endif
                </td>

                <td>
                @if($result['total_withdraw']>0)
                <a href="<?php echo $action.'/detail/withdraw?'.$result['filter'] ?>">
                {{$result['total_withdraw']}}</a>
                @else
                {{$result['total_withdraw']}}
                @endif

                </td>
                
                <td>
                @if($result['total_contested']>0)
                <a href="<?php echo $action.'/detail/contested?'.$result['filter'] ?>">
                {{$result['total_contested']}}</a>
                @else
                {{$result['total_contested']}}
                @endif
                </td> 
              
                
              </tr>
              @endforeach

       








            
              <tr>
                <td>{{$totals['label']}} </td>
                <td>{{$totals['total_applied']}}</td>
                <td>{{$totals['total_accepted']}}</td>
                <td>{{$totals['total_rejected']}}</td>
                <td>{{$totals['total_withdraw']}}</td>  
                <td>{{$totals['total_contested']}}</td> 
            
                
              </tr>
           









            
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
$(document).ready(function() {  
  $('#date_range').daterangepicker({
    <?php if(isset($from) && isset($to)){ ?>
      startDate: moment('<?php echo $from ?>'),
      endDate: moment('<?php echo $to ?>'),
    <?php } ?>
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


<?php if(!isset($from) && !isset($to)){ ?>
$(document).ready(function(e){
    $('#date_range').val('');
});
<?php } ?>
</script>

<script type="text/javascript">
jQuery(document).ready(function(e){
  jQuery('#date_range').change(function(e){
    filter();
  });
});


function filter(){
  var url = "<?php echo $action ?>";
  var query = '';
    if(jQuery("#constituency").val() != ''){
      query += '&constituency='+jQuery("#constituency").val();
    }
    
    if(jQuery("#phase").val() != ''){
      query += '&phase='+jQuery("#phase").val();
    }

    var val=  jQuery('#date_range').val();
    var timeInterval= val.split('-'); 
    if(timeInterval[0] !='' && timeInterval[1] != ''){
      var from = moment(timeInterval[0]).format('DD-MM-YYYY');
      var to = moment(timeInterval[1]).format('DD-MM-YYYY');
      query += "&from="+from+'&to='+to;
    }
    window.location.href = url+'?'+query.substring(1);
}

  </script>
@endsection