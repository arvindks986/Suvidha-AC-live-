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
  <div class="row text-center mb-3">
    <div class="col">
      <span class="">
        <span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;">{{$number_of_voting}}%</span>
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
      <div class="col-lg-4">
        <select class="form-control" name="phase" id="election_phase">
          <option value="" {{ ($phase=='') ? 'selected' : '' }}>All Phase</option>
          @foreach ($phases as $each_data)
          <option value="{{ $each_data->SCHEDULENO }}" {{ ($phase==$each_data->SCHEDULENO) ? 'selected' : '' }}>{{ $each_data->statePHASE_NO.'-'.'Phase' }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-lg-8">
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
                  <th colspan="3"> State </th>
                  <th> AC No & Name </th>
                  <!--<th align="left">Turnout % (2014)</th>-->
                  <th colspan="1">Round1 %<br>(Poll Start to 9:00 AM)</th>
                  <th colspan="1">Round2 %<br>(Poll Start to 11:00 AM)</th>
                  <th colspan="1">Round3 %<br>(Poll Start to 1:00 PM)</th>
                  <th colspan="1">Round4 %<br>(Poll Start to 3:00 PM)</th>
                  <th colspan="1">Round5 %<br>(Poll Start to 5:00 PM)</th>
                  <th colspan="1">Close Of Poll %</th>
                  <th colspan="1">Latest Updated Poll %</th>
                  <!--<th colspan="1">Action</th>-->
                </tr>


              </thead>
              <tbody>
                @foreach($results as $result)
                <tr>
                  <td colspan="3"> <span>{!! $result['label'] !!}</span> </td>
                  <td> {{$result['const_no'] }}-{{$result['const'] }} </td>
                  <td> {{$result['est_total_round1'] }} </td>
                  <td> {{$result['est_total_round2'] }} </td>
                  <td> {{$result['est_total_round3'] }} </td>
                  <td> {{$result['est_total_round4'] }} </td>
                  <td> {{$result['est_total_round5'] }} </td>
                  <td> {{$result['close_of_poll'] }} </td>
                  <td> {{$result['est_total'] }} </td>

                </tr>
                @endforeach

                <?php if (isset($totals) && count($$total) > 0) { ?>
              <tfoot>
                <tr>
                  <td colspan="3"><span>{!! $totals['label'] !!}</span></td>
                  <td></td>
                  <td></td>
                  <td>{!! $totals['est_total_round1'] !!} </td>
                  <td>{!! $totals['est_total_round2'] !!} </td>
                  <td>{!! $totals['est_total_round3'] !!} </td>
                  <td>{!! $totals['est_total_round4'] !!} </td>
                  <td>{!! $totals['est_total_round5'] !!} </td>
                  <td>{!! $totals['close_of_poll'] !!} </td>
                  <td>{!! $totals['total_percentage'] !!} </td>


                </tr>
              </tfoot>
            <?php } ?>
            </tbody>
            </table>

          </div><!-- End Of  table responsive -->
        </div><!-- End Of intra-table Div -->


      </div><!-- End Of random-area Div -->

    </div><!-- End OF page-contant Div -->
  </div>
</div><!-- End Of parent-wrap Div -->
</div>
<!-- Modal -->
<div class="modal fade" id="changestatus" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <h4 class="modal-title" id="exampleModalLabel">Estimated Poll Turnout %</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table>
          <tr>
            <th>
              <p class="mb-0 text-left"><b>State Name:</b></p>
            </th>
            <th> <span class="badge badge-info">{{isset($states[0]['name']) ? $states[0]['name'] : ''}}</span> </th>
            <th>
              <p class="mb-0 text-left"><b>District Name:</b></p>
            </th>
            <th><span class="badge badge-info">{{isset($dist[0]['name']) ? $dist[0]['name'] : ''}}</span> </th>
          </tr>
          <tr>
            <th>
              <p class="mb-0 text-left"><b>AC Name:</b> </p>
            </th>
            <th><span class="badge badge-info" name="acname" id="acname">{{isset($consituencies[0]['ac_name']) ? $consituencies[0]['ac_name'] : ''}}</span></th>
          </tr>
        </table>

        <form class="form-horizontal" id="election_form" method="POST" action="{{url('acdeo/turnout/estimated-turnout-change') }}">
          {{ csrf_field() }}

          <input type="hidden" name="id" id="id" value="" readonly="readonly">
          <input type="hidden" name="acno" id="acno" value="" readonly="readonly">
          <input type="hidden" name="distno" id="distno" value="" readonly="readonly">

          <table>
            <tr>
              <th> <label for="PercenTage" class="mt-2">
                  <b>Select Rounds</b></label></th>
              <th>
                <select name="rounds" id="rounds" required="required">
                  <option value=""> Select One</option>
                  <option value="1"> Round1 (9:00 AM) </option>
                  <option value="2"> Round1 (11:00 AM) </option>
                  <option value="3"> Round1 (1:00 PM) </option>
                  <option value="4"> Round1 (3:00 PM) </option>
                  <option value="5"> Round1 (5:00 PM) </option>
                  <option value="6"> close of poll</option>

                </select>
                <span id="errmsg1" class="text-danger"></span>
              </th>
            </tr>
            <tr>
              <th> <label for="PercenTage" class="mt-2">
                  <b>Enter Total Percentage here</b></label>
              </th>
              <th>
                <input type="text" name="est_turnout" id="est_turnout" class="PoLLInput" placeholder="Estimated Poll Turnout % " value="" maxlength="5" />
                <span id="errmsg" class="text-danger"></span>
                @if ($errors->has('est_turnout'))
                <span style="color:red;">{{$errors->first('est_turnout_round1')}}</span>
                @endif
                <span id="err" class="text-danger"></span>
                <div class="invalid-feedback"> Please enter a turnout value.
                </div>
              </th>
            </tr>

          </table>



          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" id="saverec">Save changes</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>
<!-- Modal Content Ends Here -->

<script type="text/javascript">
  function filter() {
    var url = "<?php echo $action ?>";
    var query = '';
    if (jQuery("#phase").val() != '' && jQuery("#phase").val() != 'undefined') {
      query += '&phase=' + jQuery("#phase").val();
    }
    if (jQuery("#state").val() != '' && jQuery("#state").val() != 'undefined') {
      query += "&state=" + jQuery("#state").val();
    }

    window.location.href = url + '?' + query.substring(1);
  }

  $("#election_phase").change(function(e) {
    var val = $(this).val();
    var newurl = addParam('phase', val);
    window.location.href = newurl;
  });

  function addParam(key, val) {
    var currentUrl = "<?php echo url()->full(); ?>";
    var url = new URL(currentUrl);
    url.searchParams.set(key, val);
    return url.href;
  }

  setTimeout(function(e) {
    referesh_page();
  }, 300000);

  function referesh_page() {
    location.reload();
  }
</script>
@endsection
@section('script')

<script type="text/javascript">
  $(document).ready(function() {
    var ac_name_full = '';
    $('.getdata').click(function(e) {
      var ac_name_full = $(this).attr('ac_name');
      $('#acname').text(ac_name_full);
    });


    $("#est_turnout").keypress(function(e) {
      //if the letter is not digit then display error and don't type anything
      $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
      if ((e.which != 46 || $(this).val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg").html("Digits Only").show().fadeOut("slow");
        return false;
      }
    });


    $('#saverec').click(function() {
      var est = $('select[name="rounds"]').val();
      error = false;
      if (est.trim() == '') {
        $('#errmsg1').html('');
        $('#errmsg1').html('Please select Rounds');
        $("select[name='rounds']").focus();
        error = true;
      }



      var est = $('input[name="est_turnout"]').val();
      error = false;
      if (est.trim() == '') {
        $('#errmsg').html('');
        $('#errmsg').html('Please enter voters turnout');
        $("input[name='est_turnout']").focus();
        error = true;
      }

      if (error) {
        return false;
      }

    }) // 


  });

  $(document).on("click", ".getdata", function() {
    var id = $(this).attr('data-id');
    var acno = $(this).attr('data-acno');
    var distno = $(this).attr('data-distno');

    $("#id").val(id);
    $("#acno").val(acno);
    $("#distno").val(distno);

  });
</script>
@endsection