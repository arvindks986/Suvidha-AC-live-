@extends('admin.layouts.ac.theme')
@section('content')
<main role="main" class="inner cover mb-3">





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
          
            <select name="state" id="state" class="form-control" onchange ="filter_pcs(this.value)">
            <option value="">Select State</option>
            @foreach($states as $result)
              @if($state== base64_decode($result['code']))
                <option value="{!! base64_decode($result['code']) !!}" data-state-value="{{$result['code']}}" selected="selected">{{$result['name']}}</option> 
              @else 
                <option value="{!! base64_decode($result['code']) !!}" data-state-value="{{$result['code']}}">{{$result['name']}}</option> 
              @endif  
            @endforeach
        
            </select>
          </div>

          <div class="form-group col-md-3"> <label>AC </label> 
          
            <select name="ac_no" id="ac_no" class="form-control" onchange ="filter()">
            <option value="">Select AC</option>
            @foreach($acs as $ac)
              @if($ac_no == $ac['ac_no'] && $state == $ac['st_code'])
                <option value="{{$ac['ac_no']}}" class="ac_no_option {{$ac['st_code']}}" selected="selected">{{$ac['ac_no']}}-{{$ac['ac_name']}}</option> 
              @else 
                <option value="{{$ac['ac_no']}}" class="ac_no_option {{$ac['st_code']}}" >{{$ac['ac_no']}}-{{$ac['ac_name']}}</option> 
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
      <table class="table table-bordered " id="example1">
           <thead>
            <tr>  
              <th>S.No</th>
              
              <th>Candidate Name</th>
              <th>Gender</th>
              <th>Total Nomination</th>
              <th>All Status</th>
              <th>Final Status</th> 
            </tr> 
          </thead>
          <tbody id="oneTimetab">   
            <?php $i=1; ?>
              @foreach($results as $result)
              <tr>
                <td>{!! $i !!}</td>
                
                <td>{!! $result['name'] !!}</td>
                <td>{!! $result['gender'] !!}</td>
                <td>{!! $result['total_nomination'] !!}</td>
                <td>{!! $result['status'] !!}</td>
                <td>{!! $result['final_status'] !!}</td>
              </tr>
              <?php $i++; ?>
              @endforeach



            
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

@section('script')

<script type="text/javascript">

function filter(){
    var url = "<?php echo $action ?>";
    var query = '';
    
    if($("#state").val() != ''){
      query += '&state='+ $("#state").children(":selected").attr("data-state-value");
    }

    if($("#ac_no").val() != ''){
      query += '&ac_no='+ $("#ac_no").val();
    }

    window.location.href = url+'?'+query.substring(1);
}

function filter_pcs(st_code){
  $('#ac_no').val('');
  $('.ac_no_option').css('display','none');
  $('.'+st_code).css('display','block');
}

$(document).ready(function(e){
  if($("#state").val() != ''){
    $('.ac_no_option').css('display','none');
    $('.'+$("#state").val()).css('display','block');
  }
});


  </script>
@endsection