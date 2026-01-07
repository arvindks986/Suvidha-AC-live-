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
  <div class="row pl-5" style="font-size:16px;"><strong style="color:blue;">Disclaimer : </strong><span>* This is approximate trend as data from some Polling Stations(PS) takes time. Final data for each PS is shared in Form 17C with all Polling Agents.</span></div>
  <div class="row text-center">
    <div class="col-lg-12">
      <button type="button" onclick="referesh_page()" class="btn btn-primary pull-right" style="font-size: 30px; padding: 15px 10px;">Refresh Page</button>
    </div>
  </div>

  <div class="row text-center mb-3">
    <div class="col">
      <span class="">
        <span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;">
          @if(Request::get('round')==1)
          {{$number_of_voting1}}%
          @elseif(Request::get('round')==2)
          {{$number_of_voting2}}%
          @elseif(Request::get('round')==3)
          {{$number_of_voting3}}%
          @elseif(Request::get('round')==4)
          {{$number_of_voting4}}%
          @elseif(Request::get('round')==5)
          {{$number_of_voting5}}%
          @elseif(Request::get('round')==6)
          {{$number_of_voting6}}%
          @else
          {{$number_of_voting}}%
          @endif
        </span>
        <br />
        <span type="text" style="color: #28a745;  text-transform: uppercase;  letter-spacing: 3px;" class=" ">Voter Turn Out</span></span>
    </div>
  </div>




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


      <div class="form-group col-md-2"> <label>Election Type</label>

        <select name="election_type" id="election_type" class="form-control" onchange="filter()">
          <option value="" {{ (Request::get('election_type')=='') ? 'selected' : '' }}>All Election Type</option>
          <option value="3" {{ (Request::get('election_type')=='3') ? 'selected' : '' }}>AC-GENERAL</option>
          <option value="4" {{ (Request::get('election_type')=='4') ? 'selected' : '' }}>AC-BYE</option>

        </select>
      </div>

      <?php if (isset($phases) && count($phases) > 0) { ?>
        <div class="form-group col-md-2"> <label>Election Phase</label>

          <select name="phase" id="phase" class="form-control" onchange="filter()">
            <option value="all">All Phase</option>
            @foreach($phases as $result)
            @if($phase==$result->PHASE_NO)
            <option value="{{$result->PHASE_NO}}" selected="selected">{{$result->PHASE_NO}}-Phase</option>
            @else
            <option value="{{$result->PHASE_NO}}">{{$result->PHASE_NO}}-Phase</option>
            @endif
            @endforeach

          </select>
        </div>
      <?php  } else { ?>
        <input type="hidden" id="phase" name="phase" value="{!! $phase !!}">
      <?php } ?>


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

      <div class="form-group col-md-2"> <label>Round </label>
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
                  <th> District No & Name </th>
                  @if(Request::get('round')==1)
                  <th colspan="1">Round1 %<br>(Poll Start to 9:00 AM)</th>
                  @elseif(Request::get('round')==2)
                  <th colspan="1">Round2 %<br>(Poll Start to 11:00 AM)</th>
                  @elseif(Request::get('round')==3)
                  <th colspan="1">Round3 %<br>(Poll Start to 1:00 PM)</th>
                  @elseif(Request::get('round')==4)
                  <th colspan="1">Round4 %<br>(Poll Start to 3:00 PM)</th>
                  @elseif(Request::get('round')==5)
                  <th colspan="1">Round5 %<br>(Poll Start to 5:00 PM)</th>
                  @elseif(Request::get('round')==6)
                  <th colspan="1">Close Of Poll %</th>
                  @else
                  <th colspan="1">Round1 %<br>(Poll Start to 9:00 AM)</th>
                  <th colspan="1">Round2 %<br>(Poll Start to 11:00 AM)</th>
                  <th colspan="1">Round3 %<br>(Poll Start to 1:00 PM)</th>
                  <th colspan="1">Round4 %<br>(Poll Start to 3:00 PM)</th>
                  <th colspan="1">Round5 %<br>(Poll Start to 5:00 PM)</th>
                  <th colspan="1">Close Of Poll %</th>
                  <th colspan="1">Latest Updated Poll %</th>
                  @endif
                  <!--<th colspan="1">Change from 2014</th>-->
                </tr>


              </thead>
              <tbody>
                @foreach($results as $result)
                <tr>
                  <td>{!! $result['label'] !!}
                  </td>

                  <td>
                    {{$result['dist_no'] }}-{{$result['dist'] }}
                  </td>

                  @if(Request::get('round')==1)
                  <td>
                    {{ $result['est_total_round1'] }}
                  </td>
                  @elseif(Request::get('round')==2)
                  <td>
                    {{$result['est_total_round2'] }}
                  </td>
                  @elseif(Request::get('round')==3)
                  <td>
                    {{$result['est_total_round3'] }}
                  </td>
                  @elseif(Request::get('round')==4)
                  <td>
                    {{$result['est_total_round4'] }}
                  </td>
                  @elseif(Request::get('round')==5)
                  <td>
                    {{$result['est_total_round5'] }}
                  </td>
                  @elseif(Request::get('round')==6)
                  <td>
                    {{$result['close_of_poll'] }}
                  </td>
                  @else
                  <td>
                    {{ $result['est_total_round1'] }}
                  </td>
                  <td>
                    {{$result['est_total_round2'] }}
                  </td>
                  <td>
                    {{$result['est_total_round3'] }}
                  </td>
                  <td>
                    {{$result['est_total_round4'] }}
                  </td>

                  <td>
                    {{$result['est_total_round5'] }}
                  </td>
                  <td>
                    {{$result['close_of_poll'] }}
                  </td>
                  <td>
                    {{$result['est_total'] }}
                  </td>
                  @endif

                </tr>
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
    var query = '';

    if (jQuery("#election_type").val() != '' && jQuery("#election_type").val() != 'undefined') {
      query += '&election_type=' + jQuery("#election_type").val();
    }
    if (jQuery("#phase").val() != '' && jQuery("#phase").val() != 'undefined') {
      query += '&phase=' + jQuery("#phase").val();
    }
    if (jQuery("#state").val() != '' && jQuery("#state").val() != 'undefined') {
      query += "&state=" + jQuery("#state").val();
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