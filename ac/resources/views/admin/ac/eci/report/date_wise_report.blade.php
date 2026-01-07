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
  <div class="col-md-9 pull-left">
   <h4>{!! $heading_title !!}</h4>
  </div>

   <div class="col-md-3  pull-right  text-right">
      @if(count($results)>0)
      <span class="report-btn" id="export-csv-btn"><a class="btn btn-primary" href="{{ $downlaod_to_excel }}" title="Download Excel" target="_blank">Export Excel</a></span>
      <span class="report-btn" id="export-pdf-btn"><a class="btn btn-primary" href="{{ $downlaod_to_pdf }}" title="Download PDF" target="_blank">Export PDF</a></span>
      @endif

      @if(isset($back_href) && $back_href != '')
      <span class="report-btn" id="back-button"><a class="btn btn-primary" href="{{ $back_href }}" title="Back">Back</a></span>
      @endif
    
    </div> 

  </div>
</div>  
</section>


@if(isset($filter_buttons) && count($filter_buttons)>0)
<section class="statistics pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        @foreach($filter_buttons as $button)
            <?php $but = explode(':',$button); ?>
            <span class="pull-right" style="margin-right: 10px;">
            <span><b>{!! $but[0] !!}:</b></span>
            <span class="badge badge-info">{!! $but[1] !!}</span>

            </span>
            
        @endforeach
      </div>
    </div>
  </div>
</section>
@endif

<section class="dashboard-header section-padding">
  <div class="container-fluid">
  
        
    <form id="generate_report_id" class="row" method="get" onsubmit="return false;">
      <div class="form-group col-md-6">
           <label>Datewise Filter</label> &nbsp; <input value="" id="date_range" name="date_range" type="text" class="ranges form-control" placeholder="Date Range" autocomplete="off"/>
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

          <div class="form-group col-md-3"> <label>State </label> 
          
            <select name="state" id="state" class="form-control" onchange ="filter()">
            <option value="">Select State</option>
            @foreach($states as $result)
              @if($state== base64_decode($result['code']))
                <option value="{{$result['code']}}" selected="selected">{{$result['name']}}</option> 
              @else 
                <option value="{{$result['code']}}" >{{$result['name']}}</option> 
              @endif  
            @endforeach
        
            </select>
          </div>


        </form>   
  
    
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

   <div class="table-responsive">
      <table class="table table-bordered ">
           <thead>
            <tr> 

              <th rowspan="2">Constituency Name</th>
              <th colspan="2">Before Scrutiny</th>
              <th colspan="5">After Scrutiny</th> 
            
            </tr>
            <tr>  
               
              <th>Total <br>Nomination Applied</th>
               <th>Nomiantion <br>Without Affidavit</th>
              <th>Accepted <br>Nominations</th> 
              <th>Rejected <br>Nominations</th>
              <th>Withdrawn <br>Nominations</th>
              <!-- <th>Validly <br>Nominated Candidates</th> -->
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


<!---->




                <td>
                @if($result['total_without_affidavit_nomination']>0)
                <a href="<?php echo $action.'/detail/withoutaffidavit?'.$result['filter'] ?>">
                {{$result['total_without_affidavit_nomination']}}
                </a>
                @else
                {{$result['total_without_affidavit_nomination']}}
                @endif

                </td>
                
<!---->

                
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
                
                <?php /*?>
                <td>
                @if($result['total_validated']>0)
                <a href="<?php echo $action.'/detail/validated?'.$result['filter'] ?>">
                {{$result['total_validated']}}</a>
                @else
                {{$result['total_validated']}}
                @endif

                </td>
                <?php */?>

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
                  <td>{{$totals['total_without_affidavit_nomination']}}</td>

                
                <td>{{$totals['total_accepted']}}</td>
                <td>{{$totals['total_rejected']}}</td>
                <td>{{$totals['total_withdraw']}}</td>
                <?php /*?><td>{{$totals['total_validated']}}</td> <?php */?>
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

   
$(document).ready(function(e){
  $('#date_range').change(function(e){
    filter();
  });
  if($('#date_range').val()!=''){
    var clear_date_html = "<button id='clear_date'>Clear Date</button>";
    $('#clear_date').remove();
    $('#date_range').after(clear_date_html);
  }
  $('#clear_date').click(function(e){
    $('#date_range').val('');
    filter();
  });
});




function filter(){
  var url = "<?php echo $action ?>";
  var query = '';
    if($("#state").val() != ''){
      query += '&state='+$("#state").val();
    }
    
    if($("#phase").val() != ''){
      query += '&phase='+$("#phase").val();
    }

    var val=  $('#date_range').val();
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