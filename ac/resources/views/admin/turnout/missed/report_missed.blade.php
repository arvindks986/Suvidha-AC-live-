@extends('admin.layouts.ac.theme')
@section('content')
<style type="text/css">
  .loader {
    position: fixed;
    left: 50%;
    right: 50%;
    border: 16px solid #f3f3f3;
    /* Light grey */
    border-top: 16px solid #3498db;
    /* Blue */
    border-radius: 50%;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
    z-index: 99999;
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
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
        <span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download Excel" <?php if ($button['target']) { ?> target='_blank' <?php } ?>>{{ $button['name'] }}</a></span>
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
        <?php $but = explode(':', $button); ?>
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

      <div class="form-group col-md-3"> <label>Election Type</label>

        <select name="election_type" id="election_type" class="form-control" onchange="filter()">
          <option value="" {{ (Request::get('election_type')=='') ? 'selected' : '' }}>All Election Type</option>
          <option value="3" {{ (Request::get('election_type')=='3') ? 'selected' : '' }}>AC-GENERAL</option>
          <option value="4" {{ (Request::get('election_type')=='4') ? 'selected' : '' }}>AC-BYE</option>

        </select>
      </div>

      <div class="form-group col-md-3"> <label>State </label>
        <select name="state" id="state" class="form-control" onchange="filter()">
          <option value="" {{ ($state == '') ? ' selected="selected"' : '' }}>All States</option>
          @foreach($states as $result)
          @if(base64_encode($state) == $result['code'])
          <option value="{{$result['code']}}" selected="selected">{{$result['name']}}</option>
          @else
          <option value="{{$result['code']}}">{{$result['name']}}</option>
          @endif
          @endforeach

        </select>
      </div>


      <?php if (isset($phases) && count($phases) > 0) { ?>
        <div class="form-group col-md-3"> <label>Election Phase</label>

          <select name="phase" id="phase" class="form-control" onchange="filter()">
            @foreach($phases as $result)
            @if($phase==$result->PHASE_NO)
            <option value="{{$result->PHASE_NO}}" selected="selected">{{$result->PHASE_NO}}-Phase</option>
            @else
            <option value="{{$result->PHASE_NO}}">{{$result->PHASE_NO}}-Phase</option>
            @endif
            @endforeach

          </select>
        </div>
      <?php } else { ?>
        <input type="hidden" id="phase" name="phase" value="{!! $phase !!}">
      <?php } ?>

      <div class="form-group col-md-3"> <label>Round </label>
        <?php $rounds = [
          [
            'id' => 1,
            'name' => '9 AM',
          ],
          [
            'id' => 2,
            'name' => '11 AM',
          ],
          [
            'id' => 3,
            'name' => '1 PM',
          ],
          [
            'id' => 4,
            'name' => '3 PM',
          ],
          [
            'id' => 5,
            'name' => '5 PM',
          ],
          [
            'id' => 6,
            'name' => 'Close Of Poll',
          ],
        ];
        ?>
        <select name="round" id="round" class="form-control" onchange="filter()">
          <option value="all">Select Round</option>
          @foreach($rounds as $result)
          @if($round == $result['id'])
          <option value="{{$result['id']}}" selected="selected">Round{{$result['id']}}-{{$result['name']}}</option>
          @else
          <option value="{{$result['id']}}">Round{{$result['id']}}-{{$result['name']}}</option>
          @endif
          @endforeach

        </select>
      </div>


      <!-- 
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
          </div> -->

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

            <table id="data_table_table" class="table table-striped table-bordered" style="width:100%">
              <thead>

                <tr>
                  <th colspan="12" class="text-center">{!! $heading_title_with_all !!}</th>
                </tr>


                <tr>
                  <th colspan="3"> State </th>
                  <th> AC No & Name </th>
                  <th> RO Name </th>
                  <th> RO Mobile No </th>
                </tr>


              </thead>
              <tbody>
                @foreach($results as $result)
                <tr>
                  <td colspan="3">

                    <span>{!! $result['label'] !!}</span>
                  </td>

                  <td>
                    {{$result['ac_no'] }}-{{$result['ac_name'] }}
                  </td>

                  <td>{{$result['name'] }}</td>

                  <td>{{$result['Phone_no'] }}</td>

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


<script type="text/javascript">
  function filter() {
    var url = "<?php echo $action ?>";
    var state = '<?= $state_encrypted ?>';
    var query = '';
    if (jQuery("#election_type").val() != '' && jQuery("#election_type").val() != 'undefined') {
      query += '&election_type=' + jQuery("#election_type").val();
    }
    if (jQuery("#state").val() != '' && jQuery("#state").val() != 'undefined') {
      query += '&state=' + jQuery("#state").val();
    }

    if (state != jQuery("#state").val()) {
      query += '&phase=all';
    } else {
      if (jQuery("#phase").val() != '' && jQuery("#phase").val() != 'undefined') {
        query += '&phase=' + jQuery("#phase").val();
      }
    }
    if (jQuery("#round").val() != '' && jQuery("#round").val() != 'undefined') {
      query += '&round=' + jQuery("#round").val();
    }
    window.location.href = url + '?' + query.substring(1);
  }

  setTimeout(function(e) {
    referesh_page();
  }, 300000);

  function referesh_page() {
    location.reload();
  }
</script>
@endsection