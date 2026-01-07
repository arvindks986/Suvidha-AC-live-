@extends('admin.layouts.ac.theme')
@section('content')
<style type="text/css">
#my-list-table{
  font-size: 12px !important;
}
.sticky{
    position: sticky;
    top: 0px;
  }
</style>

<div class="loader" style="display:none;"></div>

@include('admin/common/form-filter')
<section class="statistics color-grey pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-9 pull-left">
        <h4>{!! $heading_title !!}</h4>
      </div>

      <div class="col-md-3 pull-right text-right">

        @foreach($buttons as $button)
        <span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
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

@if(Session::has('flash-message'))
@if(Session::has('status'))
<?php
$status = Session::get('status');
if($status==1){
  $class = 'alert-success';
}
else{
  $class = 'alert-danger';
}
?>
@endif
<div class="alert <?php echo $class; ?> fade in">
  <a href="#" class="close" data-dismiss="alert">&times;</a>
  {{ Session::get('flash-message') }}
</div>
@endif


<div class="container-fluid">
  <!-- Start parent-wrap div -->  
  <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
      <div class="page-contant">
        <div class="random-area">


          <div class="table-responsive">
            <table class="table table-bordered " id="my-list-table">
              <thead class="sticky">
                <tr>
                  <th rowspan="2">State</th>
                  <th rowspan="2">AC No. & Name</th>
                  <th rowspan="2">PS No. & Name</th>
                  <th colspan="2" class="text-center">Poll Time</th>
                  <th colspan="4" class="text-center">Turnout</th>
                  <th colspan="3" class="text-center">Gender By Votes</th>
                  <th colspan="5" class="text-center">Total Scan</th>
                   <th colspan="7" class="text-center">PRO Diary</th>
                </tr>
                <tr>

                  <th>Start Date/Time</th>
                  <th>End Date/Time</th>

                  <th>Total Electors</th>
                  <th>PRO Turnout</th>
                  <th>BLO Turnout</th>
                  <th>Total Turnout</th>
                  <th>Male Turnout</th>
                  <th>Fe-male Turnout</th>
                  <th>Other Turnout</th>

                  <th>QR Scan</th>
                  <th>Serial No.</th>
                  <th>Epic No</th>
                  <th>Name</th>
                  <th>Mobile</th>
                  
                  <th>Total Vote</th>
                  <th>Total EVM VOte</th>
                  <th>Total Agent</th>
                  <th>Total EDC</th>
                  <th>Total Overseas</th>
                  <th>Total Proxy</th>
                  <th>Total Tendered</th>
                </tr>
              </thead>
              <tbody>  
                @if(count($results)>0)
                @foreach($results as $result)
                <tr>
                  <td>{{$result['st_name']}}</td>
                  <td>{{$result['ac_no']}}-{{$result['ac_name']}}</td>
                  <td>{{$result['ps_no']}}-{{$result['ps_name']}}</td>
                  <td>{{ $result['poll_start_datetime'] }} </td>
                    <td>{{ $result['electors'] }} </td>
                    <td>{{ $result['pro_turn_out'] }} </td>
                    <td>{{ $result['blo_turn_out'] }} </td>
                    <td>{{ $result['total_turn_out'] }} </td>
                    <td>{{ $result['total_male_turn_out'] }} </td>
                    <td>{{ $result['total_female_turn_out'] }} </td>
                    <td>{{ $result['total_other_turn_out'] }} </td>
                    <td>{{ $result['scan_qr'] }} </td>
                    <td>{{ $result['scan_srno'] }} </td>
                    <td>{{ $result['scan_epicno'] }} </td>
                    <td>{{ $result['scan_name'] }} </td>
                    <td>{{ $result['scan_mobile'] }} </td>
                    <td>{{ $result['poll_end_datetime'] }} </td>
                    <td>{{ $result['no_of_vote'] }} </td>
                    <td>{{ $result['no_of_vote_evm'] }} </td>
                    <td>{{ $result['no_of_agent'] }} </td>
                    <td>{{ $result['no_of_edc'] }} </td>
                    <td>{{ $result['no_of_overseas'] }} </td>
                    <td>{{ $result['no_of_proxy'] }} </td>
                    <td>{{ $result['no_of_tendered'] }}</td>
                </tr>
                @endforeach
                @else
                <tr>
                  <td colspan="3">
                    No Record Found.
                  </td>
                </tr>
                @endif
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
  $(document).ready(function () {
    if($('#my-list-table').length>0){
      $('#my-list-table').DataTable({
        "pageLength": 2000,
        "aaSorting": []
      });
    }
  });
</script>
@endsection