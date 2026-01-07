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
#acViewBody a{
    color: #000 !important;
    text-decoration: none !important;
    cursor: default !important;
}
#acViewBody a:hover{
    color: #000 !important;
    text-decoration: none !important;
    cursor: default !important;
}
  </style>

  <div class="loader" style="display:none;"></div>


<section class="statistics color-grey pt-4 pb-2">

<div class="row text-center">
<div class="col-lg-12">
<button type="button" onclick="referesh_page()" class="btn btn-primary pull-right" style="font-size: 30px; padding: 15px 10px;">Refresh Page</button>
</div>
</div>

<div class="row text-center mb-3">
   <div class="col">
   <span class="">
   <span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;">{{$number_of_voting}}%</span>
   <br />
                 <span type="text" style="color: #28a745;  text-transform: uppercase;  letter-spacing: 3px;" class=" ">Voter Turn Out</span></span>
  </div></div>
 



<div class="container-fluid">
  <div class="row">
  <div class="col-md-9 pull-left">
   <h4>{!! $heading_title !!}</h4>
  </div>

   <div class="col-md-3  pull-right text-right">

    @if(count($results)>0)
      <?php if(isset($downlaod_to_excel)){ ?>
      <span class="report-btn" id="export-csv-btn"><a class="btn btn-primary" href="{{ $downlaod_to_excel }}" title="Download Excel" target="_blank">Export Excel</a></span>
    <?php } ?>
      <?php if(isset($downlaod_to_excel)){ ?>
      <span class="report-btn" id="export-pdf-btn"><a class="btn btn-primary" href="{{ $downlaod_to_pdf }}" title="Download PDF" target="_blank">Export PDF</a></span>
    <?php } ?>
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
          

        <?php /*
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
          </div>*/?>

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



<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
     <div class="page-contant">
     <div class="random-area">
  <br>

    

           <div class="table-responsive">
      
            <table id="acViewBody" class="table table-striped table-bordered" style="width:100%"><thead>
       <tr>
        <th colspan="1" rowspan="2"> S.No </th>
        <th colspan="2" rowspan="2"> AC Name </th>
        <th colspan="4">Total Elector </th>
        <th colspan="4">Latest Updated Value </th>
        <th colspan="4">Round1 (Poll Start to 9:00 AM)</th>
        <th colspan="4">Round2 (Poll Start to 11:00 AM)</th>
        <th colspan="4">Round3 (Poll Start to 1:00 PM)</th>
        <th colspan="4">Round4 (Poll Start to 3:00 PM)</th>
        <th colspan="4">Round5 (Poll Start to 5:00 PM)</th>
        <th colspan="4">End of Poll(Poll Start to End)</th>
       </tr>

        <tr>

        <th size="2">Male</th>
        <th size="2">Female</th>
        <th size="2">Other</th>
        <th size="2">Total</th>

        <th size="2">Male</th>
        <th size="2">Female</th>
        <th size="2">Other</th>
        <th size="2">Total</th>

        <th size="2">Male</th>
        <th size="2">Female</th>
        <th size="2">Other</th>
        <th size="2">Total</th>

        <th size="2">Male</th>
        <th size="2">Female</th>
        <th size="2">Other</th>
        <th size="2">Total</th>

        <th size="2">Male</th>
        <th size="2">Female</th>
        <th size="2">Other</th>
        <th size="2">Total</th>

        <th size="2">Male</th>
        <th size="2">Female</th>
        <th size="2">Other</th>
        <th size="2">Total</th>

        <th size="2">Male</th>
        <th size="2">Female</th>
        <th size="2">Other</th>
        <th size="2">Total</th>

         <th size="2">Male</th>
        <th size="2">Female</th>
        <th size="2">Other</th>
        <th size="2">Total</th>



        </tr>
    </thead>
        <tbody>
      @foreach($results as $result)
        <tr>
            <td >
         
          <span>{!! $result['const_no'] !!}</span>
        </td> 
        <td colspan="2">
          <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
          <span>{!! $result['label'] !!}</span>
        </a>
        </td> 
       
        <td>
        @if($result['gen_m']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['gen_m'] }}
        </a>
        @else
        {{$result['gen_m'] }}
        @endif
         </td>
         <td>
        @if($result['gen_f']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['gen_f'] }}
        </a>
        @else
        {{$result['gen_f'] }}
        @endif
         </td>
         <td>
        @if($result['gen_o']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['gen_o'] }}
        </a>
        @else
        {{$result['gen_o'] }}
        @endif
         </td>
         <td>
        @if($result['gen_t']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['gen_t'] }}
        </a>
        @else
        {{$result['gen_t'] }}
        @endif
         </td>

          <td>
        @if($result['total_male']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['total_male'] }}
        </a>
        @else
        {{$result['total_male'] }}
        @endif
         </td>
         <td>
        @if($result['total_female']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['total_female'] }}
        </a>
        @else
        {{$result['total_female'] }}
        @endif
         </td>
         <td>
        @if($result['total_other']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['total_other'] }}
        </a>
        @else
        {{$result['total_other'] }}
        @endif
         </td>
         <td>
        @if($result['total']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['total'] }}
        </a>
        @else
        {{$result['total'] }}
        @endif
         </td>



        <td>
        @if($result['round_1_m']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['round_1_m'] }}
        </a>
        @else
        {{$result['round_1_m'] }}
        @endif
         </td>

         <td>
         @if($result['round_1_f']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['round_1_f'] }}
        </a>
        @else
        {{$result['round_1_f'] }}
        @endif
         </td>

         <td>
         @if($result['round_1_o']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['round_1_o'] }}
        </a>
        @else
        {{$result['round_1_o'] }}
        @endif
         </td>

         <td>
         @if($result['round_1_t']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['round_1_t'] }}
        </a>
        @else
        {{$result['round_1_t'] }}
        @endif
         </td>

      
            <td>
        @if($result['round_2_m']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['round_2_m'] }}
        </a>
        @else
        {{$result['round_2_m'] }}
        @endif
         </td>

         <td>
         @if($result['round_2_f']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['round_2_f'] }}
        </a>
        @else
        {{$result['round_2_f'] }}
        @endif
         </td>

         <td>
         @if($result['round_2_o']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['round_2_o'] }}
        </a>
        @else
        {{$result['round_2_o'] }}
        @endif
         </td>

         <td>
         @if($result['round_2_t']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['round_2_t'] }}
        </a>
        @else
        {{$result['round_2_t'] }}
        @endif
         </td>


            <td>
        @if($result['round_3_m']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['round_3_m'] }}
        </a>
        @else
        {{$result['round_3_m'] }}
        @endif
         </td>

         <td>
         @if($result['round_3_f']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['round_3_f'] }}
        </a>
        @else
        {{$result['round_3_f'] }}
        @endif
         </td>

         <td>
         @if($result['round_3_o']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['round_3_o'] }}
        </a>
        @else
        {{$result['round_3_o'] }}
        @endif
         </td>

         <td>
         @if($result['round_3_t']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['round_3_t'] }}
        </a>
        @else
        {{$result['round_3_t'] }}
        @endif
         </td>


            <td>
        @if($result['round_4_m']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['round_4_m'] }}
        </a>
        @else
        {{$result['round_4_m'] }}
        @endif
         </td>

         <td>
         @if($result['round_4_f']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['round_4_f'] }}
        </a>
        @else
        {{$result['round_4_f'] }}
        @endif
         </td>

         <td>
         @if($result['round_4_o']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['round_4_o'] }}
        </a>
        @else
        {{$result['round_4_o'] }}
        @endif
         </td>

         <td>
         @if($result['round_4_t']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['round_4_t'] }}
        </a>
        @else
        {{$result['round_4_t'] }}
        @endif
         </td>

            <td>
        @if($result['round_5_m']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['round_5_m'] }}
        </a>
        @else
        {{$result['round_5_m'] }}
        @endif
         </td>

         <td>
         @if($result['round_5_f']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['round_5_f'] }}
        </a>
        @else
        {{$result['round_5_f'] }}
        @endif
         </td>

         <td>
         @if($result['round_5_o']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['round_5_o'] }}
        </a>
        @else
        {{$result['round_5_o'] }}
        @endif
         </td>

         <td>
         @if($result['round_5_t']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['round_5_t'] }}
        </a>
        @else
        {{$result['round_5_t'] }}
        @endif
         </td>

            <td>
        @if($result['round_end_m']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['round_end_m'] }}
        </a>
        @else
        {{$result['round_end_m'] }}
        @endif
         </td>

         <td>
         @if($result['round_end_f']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['round_end_f'] }}
        </a>
        @else
        {{$result['round_end_f'] }}
        @endif
         </td>

         <td>
         @if($result['round_end_o']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['round_end_o'] }}
        </a>
        @else
        {{$result['round_end_o'] }}
        @endif
         </td>

         <td>
         @if($result['round_end_t']>0)
        <a href="<?php echo $result['href'].'?'.$result['filter'] ?>">
        {{ $result['round_end_t'] }}
        </a>
        @else
        {{$result['round_end_t'] }}
        @endif
         </td>




         </tr>
        @endforeach
        <?php /*
        <tfoot>
         <tr>
        <td colspan="3"><span>{!! $totals['label'] !!}</span></td> 
       
        
        <td>
        {{$totals['gen_m'] }}
        </td> 
        <td>{!! $totals['gen_f'] !!} </td>         
        <td>{!! $totals['gen_o'] !!} </td>       
        <td>{!! $totals['gen_t'] !!} </td> 

        <td>{!! $totals['ser_m'] !!} </td> 
        <td>{!! $totals['ser_f'] !!} </td>           
        <td>{!! $totals['ser_o'] !!} </td> 
        <td>{!! $totals['ser_t'] !!} </td> 
        
        <td> {!! $totals['polling_reg'] !!}</td> 
        <td> {!! $totals['polling_auxillary'] !!}</td> 
        <td> {!! $totals['polling_total'] !!}</td> 
         </tr>
        </tfoot>
        */?>
       </tbody></table>


         </div><!-- End Of  table responsive -->  
      </div><!-- End Of intra-table Div -->   
        
         
      </div><!-- End Of random-area Div -->
      
    </div><!-- End OF page-contant Div -->
    </div>      
  </div><!-- End Of parent-wrap Div -->
  </div> 


<script type="text/javascript">

function filter(){
    var url = "<?php echo $action ?>";
    var query = '';
    if(jQuery("#constituency").val() != ''){
      query += '&constituency='+jQuery("#constituency").val();
    }
    if(jQuery("#phase").val() != ''){
      query += '&phase='+jQuery("#phase").val();
    }
    var state = "<?php echo $state; ?>";
    if(state != ''){
        query += "&state=<?php echo base64_encode($state); ?>";
    }
    window.location.href = url+'?'+query.substring(1);
}

setTimeout(function(e){
    referesh_page();
},300000);

function referesh_page(){
    location.reload();
}

</script>
@endsection