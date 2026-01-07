@extends('admin.layouts.ac.theme')
@section('content')

<section class="statistics color-grey pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-7 pull-left">
        <h4>{!! $heading_title !!}</h4>
      </div>

      <div class="col-md-5  pull-right text-right">

        @foreach($buttons as $button)
        <span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="{{$button['name']}}" <?php if ($button['target']) { ?> target='_blank' <?php } ?>>{{ $button['name'] }}</a></span>
        @endforeach
        <span class="report-btn"><a class="btn btn-primary" href="{{ url('eci/turnout/voter-turnout-after-round-percentage-change')}}" title="Voter Turnout After Round Percentage Change">Voter Turnout After Round Percentage Change</a></span>
        
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
      <div class="form-group col-md-2"> <label>Election Type</label>
        <select name="election_type" id="election_type" class="form-control" onchange="filter()">
          <option value="" {{ (Request::get('election_type')=='') ? 'selected' : '' }}>All Election Type</option>
          <option value="3" {{ (Request::get('election_type')=='3') ? 'selected' : '' }}>AC-GENERAL</option>
          <option value="4" {{ (Request::get('election_type')=='4') ? 'selected' : '' }}>AC-BYE</option>
        </select>
      </div>

      <div class="form-group col-md-3"> <label>State </label>
        <select name="state" id="state" class="form-control" onchange="filter()">
          <option value="">Select State</option>
          @foreach($states as $result)
          @if($state== base64_decode($result['code']))
          <option value="{{$result['code']}}" selected="selected">{{$result['name']}}</option>
          @else
          <option value="{{$result['code']}}">{{$result['name']}}</option>
          @endif
          @endforeach

        </select>
      </div>

      <?php if (isset($phases) && count($phases) > 0) { ?>
        <div class="form-group col-md-2"> <label>Election Phase</label>
          <select name="phase" id="phase" class="form-control" onchange="filter()">
            <option value="all">All Phase</option>
            @foreach($phases as $key => $result)
            @if($phase==$result->PHASE_NO)
            <option value="{{$result->PHASE_NO}}" selected="selected">{{$result->statePHASE_NO}}-Phase</option>
            @else
            <option value="{{$result->PHASE_NO}}">{{$result->statePHASE_NO}}-Phase</option>
            @endif
            @endforeach
          </select>
        </div>
      <?php  } else { ?>
        <input type="hidden" id="phase" name="phase" value="{!! $phase !!}">
      <?php } ?>
      
      <div class="form-group col-md-2"> <label>Round</label>
          <select name="round" id="round" class="form-control" onchange="filter()">
            <option value="all">All Round</option>
            @foreach([1,2,3,4,5,6] as $key => $result)
              @if($round==$result)
              <option value="{{$result}}" selected="selected">{{$result}} Round</option>
              @else
              <option value="{{$result}}">{{$result}} Round</option>
              @endif
            @endforeach
          </select>
        </div>

      <?php if (isset($acs) && count($acs) > 0) { ?>
        <div class="form-group col-md-2"> <label>AC</label>
          <select name="ac" id="ac" class="form-control" onchange="filter()">
            <option value="all">All AC</option>
            @foreach($acs as $key => $result)
              @if($ac==$result->AC_NO)
              <option value="{{$result->AC_NO}}" selected="selected">{{$result->AC_NO}} - {{$result->AC_NAME}}</option>
              @else
              <option value="{{$result->AC_NO}}">{{$result->AC_NO}} - {{$result->AC_NAME}}</option>
              @endif
            @endforeach
          </select>
        </div>
      <?php  }?>
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
                  <th colspan="14" class="text-center">{!! $heading_title_with_all !!}</th>
                </tr>
                <tr>
                  <th> State </th>
                  <th> Phase </th>
                  <th> AC No. </th>
                  <th> AC Name </th>
                  <th> Round </th>
                  <th> Round Percentage </th>
                  <th> State Percentage </th>
                  <th> Updated By </th>
                  <th> Date Time </th>
                </tr>
              </thead>
              <tbody>
                @foreach($results as $result)
                <tr>
                  <td>{{$result['st_code'] }} - {{$result['state']['ST_NAME'] }}</td>
                  <td>Phase {{$result['phase']['StatePHASE_NO']}}</td>
                  <td>{{$result['ac_no'] }}</td>
                  <td>{{$result['ac']['AC_NAME'] }}</td>
                  <td>{{$result['round'] }}</td>
                  <td>{{$result['percentage'] }}</td>
                  <td>{{$result['state_percentage'] }}</td>
                  <td>{{$result['updatedby'] }}</td>
                  <td>{{$result['created_at'] }}</td>
                </tr>
                @endforeach
                @if(count($results) == 0)
                <tr>
                  <td colspan="9" align="center">No data found</td>
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


<script type="text/javascript">
  function filter() {
    var url = "<?=$action ?>";
    var state = "<?=$state_encoded ?>";
    var query = '';

    if (jQuery("#election_type").val() != '' && jQuery("#election_type").val() != undefined) {
      query += '&election_type=' + jQuery("#election_type").val();
    }
    if (jQuery("#state").val() != '' && jQuery("#state").val() != undefined) {
      query += "&state=" + jQuery("#state").val();
    }
    if(state == jQuery("#state").val()){
      if (jQuery("#phase").val() != '' && jQuery("#phase").val() != undefined) {
        query += '&phase=' + jQuery("#phase").val();
      }
      if (jQuery("#round").val() != '' && jQuery("#round").val() != undefined) {
        query += '&round=' + jQuery("#round").val();
      }
      if (jQuery("#ac").val() != undefined) {
        query += '&ac=' + jQuery("#ac").val();
      }
    }

    
    window.location.href = url + '?' + query.substring(1);
  }
</script>
@endsection