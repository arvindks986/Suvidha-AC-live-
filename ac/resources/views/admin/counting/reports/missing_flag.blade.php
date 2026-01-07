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
  <div class="col-md-7 pull-left">
   <h4>{!! $heading_title !!}</h4>
  </div>

   <div class="col-md-5  pull-right text-right">

@foreach($buttons as $button)
<span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download Excel" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
@endforeach
      
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
          <div class="form-group col-md-3"> <label>State </label> 
          
            <select name="state" id="state" class="form-control" onchange ="filter()">
            <option value="">Select State</option>
            @foreach($states as $result)
              @if(base64_encode($state) == $result['code'])
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
      
            <table id="acViewBody" class="table table-striped table-bordered" style="width:100%">
              <thead>

              <tr><th colspan="11" class="text-center">{!! $heading_title_with_all !!}</th></tr>
       <tr>
          <th align="left">State</th>
          <th align="left">Const No - Name</th>
          <th align="left">EVM Finalized</th>
          <th align="left">Postal Finalized</th>      
       </tr>


    </thead>
        <tbody>
      @forelse($results as $result)
        <tr>
        <td align="left"><span>{!! $result['label'] !!}</span></td> 
         <td align="left">{{$result['const_no'] }} - {{$result['const_name'] }} </td>

         @php if($result['evm_finalized'] == 'Yes'){  @endphp
          <td style="color:#008000;">{{$result['evm_finalized'] }}</td>
           @php }else{ @endphp
          <td style="color:#FF0000;">{{$result['evm_finalized'] }}</td>
          @php } @endphp

          @php if($result['postal_finalize'] == 'Yes'){  @endphp
          <td style="color:#008000;">{{$result['postal_finalize'] }}</td>
           @php }else{ @endphp
          <td style="color:#FF0000;">{{$result['postal_finalize'] }}</td>
          @php } @endphp


        </tr>
       @empty
                <tr>
                  <td colspan="4">No Data Found For Missing Flags</td>                 
              </tr>
          @endforelse
        <tr>
        
        <tfoot>
        </tfoot>
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
  if(jQuery("#state").val() != '' && jQuery("#state").val() != 'undefined'){
        query += "&state="+jQuery("#state").val();
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