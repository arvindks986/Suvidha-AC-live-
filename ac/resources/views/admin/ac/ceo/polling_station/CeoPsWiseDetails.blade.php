@extends('admin.layouts.ac.theme')
@section('title', 'Suvidha')
@section('bradcome', 'Polling Station Details')
@section('content')


@if($errors->any())
<div class="alert alert-info">{{$errors->first()}}</div>
@endif

@if (session('error'))
<div class="alert alert-info">{{ session('error') }}</div>
@endif

<div class="alert alert-info updated" style="display: none;">Polling Station Data Updated Successfully</div>

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
        @if($ac_id)
        @if($lists->end_of_poll_finalize==0)
        @if($is_finalize_deo=='1')
        <span class="report-btn psdefinalize btn btn-warning btn-lg">Definalize PS Data</span>
        @if($is_finalize=='0')
        <span class="report-btn psfinalize btn btn-success btn-lg">Finalize PS Data</span>
        @endif
        @else
        <span class="report-btn btn btn-warning btn-lg">Waiting For DEO Approval</span>
        @endif
        @endif
        @endif
        @if($is_finalize)

        @endif


      </div>

    </div>
  </div>
</section>


<section class="statistics pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <a href="{{url('acceo/turnout/turnout-publish-status-list')}}"><button class="btn btn-primary">AC Wise Turnout Publish Status</button></a>
        @if(isset($filter_buttons) && count($filter_buttons)>0)
        @foreach($filter_buttons as $button)
        <?php $but = explode(':', $button); ?>
        <span class="pull-right" style="margin-right: 10px;">
          <span><b>{!! $but[0] !!}:</b></span>
          <span class="badge badge-info">{!! $but[1] !!}</span>

        </span>

        @endforeach
        @endif
      </div>
    </div>
  </div>
</section>


<section class="dashboard-header section-padding">
  <div class="container-fluid">


    <form class="row" method="get" action="{{$action}}" id="pswisedataform">

      <input type="hidden" name="state" id="state" value="{{$state}}">
      <!---AC FILTER-->
      <div class="form-group col-md-3"> <label>AC Constituency </label>

        <select name="ac_id" id="ac_id" class="form-control">
          <option value="">Select AC</option>
          @if(isset($consituencies))
          @foreach($consituencies as $a)
          @if($ac_id==$a['ac_no'])
          <option value="{{$a['ac_no']}}" selected="{{$ac_id}}">{{$a['ac_no']}}-{{$a['ac_name']}}</option>
          @else
          <option value="{{$a['ac_no']}}">{{$a['ac_no']}}-{{$a['ac_name']}}</option>
          @endif
          @endforeach
          @endif
        </select>
      </div>



      <div class="form-group col-md-3">
        <label class="col" for="">&nbsp;</label>
        <input type="submit" value="Submit" class="btn btn-primary">
      </div>


    </form>


  </div>
</section>

<section class="mt-3">
  <div class="container">
    <div class="row">
      <div class="col">

        @if (session('success_mes'))
        <div class="alert alert-success"> {{session('success_mes') }}</div>
        @endif
        @if (session('error_mes'))
        <div class="alert alert-danger"> {{session('error_mes') }}</div>
        @endif
        @if (\Session::has('success'))
        <div class="alert alert-success">
          <ul>
            <li>{!! \Session::get('success') !!}</li>
          </ul>
        </div>
        @endif

      </div>

      @if($lists->end_of_poll_finalize == '1' && $ac_id <>'')
        <div class="col-md-12 text-center">
          <button type="button" class="btn btn-success btn-lg">AC Finalized & Turnout Successfully Published</button>
        </div>
        <div>&nbsp;</div>
        @endif
        <div class="card text-left" style="width:100%; margin:0 auto;">

          <table class="table table-striped table-bordered" style="width:100%">
            <thead>
              <tr>
                <th colspan="4" align="center">Electors</th>
                <th colspan="4" align="center">End of Poll Turnout</th>
                <th colspan="4" align="center">Turnout % </th>
              </tr>
              <tr>
                <th>Male</th>
                <th>female</th>
                <th>Other</th>
                <th>total</th>
                <th>Male</th>
                <th>female</th>
                <th>Other</th>
                <th>total</th>
                <th>Male</th>
                <th>female</th>
                <th>Other</th>
                <th>total</th>
              </tr>
            </thead>


            @php $maleturnout_per = $femaleturnout_per = $othersturnout_per = $totalturnout_per =0; @endphp
            @if(isset($ac_data))
            @if( $ac_data->electors_male >0)
            @php $maleturnout_per = round((($ac_data->voter_male/$ac_data->electors_male)*100),2); @endphp
            @endif

            @if($ac_data->electors_female)
            @php $femaleturnout_per = round((($ac_data->voter_female/$ac_data->electors_female)*100),2); @endphp
            @endif

            @if($ac_data->electors_other)
            @php $othersturnout_per = round((($ac_data->voter_other/$ac_data->electors_other)*100),2); @endphp
            @endif

            @if($ac_data->electors_total)
            @php $totalturnout_per = round((($ac_data->voter_total/$ac_data->electors_total)*100),2); @endphp
            @endif

            @endif

            <tr>
              <td>@if(isset($ac_data)) {{$ac_data->electors_male}} @endif</td>
              <td>@if(isset($ac_data)) {{$ac_data->electors_female}} @endif</td>
              <td>@if(isset($ac_data)) {{$ac_data->electors_other}} @endif</td>
              <td>@if(isset($ac_data)) {{$ac_data->electors_total}} @endif</td>
              <td>@if(isset($ac_data)) {{$ac_data->voter_male}} @endif</td>
              <td>@if(isset($ac_data)) {{$ac_data->voter_female}} @endif</td>
              <td>@if(isset($ac_data)) {{$ac_data->voter_other}} @endif</td>
              <td>@if(isset($ac_data)) {{$ac_data->voter_total}} @endif</td>
              <td>{{$maleturnout_per}}%</td>
              <td>{{$femaleturnout_per}}%</td>
              <td>{{$othersturnout_per}}%</td>
              <td>{{$totalturnout_per}}%</td>
            </tr>
          </table>

          @if($is_finalize == '1')

          @if($lists->end_of_poll_finalize==0)
          <div class="row">
            <div class="col-md-2 p-0 m-0" style="width: 100px;"></div>
            <div class="col-md-12 " style="margin-left:20px;">
              <label for="candidate_id" class="col-form-label">Editing of Voter Details will not be availaible after clicking on Publish Turnout Button</label>

              <button type="button" class="btn btn-primary custombtn" onclick="return finalize();">Publish Turnout</button>
            </div>
          </div>

          @endif
          @endif

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



          <div class="table-responsive">

            <table id="data_table_table" class="table table-striped table-bordered" style="width:100%">
              <thead>

                <tr>
                  <th colspan="14" class="text-center">{!! $heading_title !!}</th>
                </tr>


                <tr>
                  <!--  <th>Serial No</th> -->
                  <th>PS No</th>
                  <th>PS Name</th>
                  <th>Location Type</th>
                  <th>PS Type</th>
                  <th>Electors Male</th>
                  <th>Electors Female</th>
                  <th>Electors Other</th>
                  <th>Electors Total</th>
                  <th>Voter Male</th>
                  <th>Voter Female</th>
                  <th>Voter Other</th>
                  <th>Voter Total</th>
                  <th>Action</th>

                </tr>


              </thead>
              <tbody>
                @php
                $count = 1;

                $TotalElectorMale = 0;
                $TotalElectorFeMale = 0;
                $TotalElectorOther = 0;
                $TotalElector = 0;
                $TotalVoterMale = 0;
                $TotalVoterFeMale = 0;
                $TotalVoterOther = 0;
                $TotalVoter = 0;



                @endphp

                @forelse ($results as $key=>$listdata)

                @php

                $TotalElectorMale +=$listdata->electors_male;
                $TotalElectorFeMale +=$listdata->electors_female;
                $TotalElectorOther +=$listdata->electors_other;
                $TotalElector +=$listdata->electors_total;
                $TotalVoterMale +=$listdata->voter_male;
                $TotalVoterFeMale +=$listdata->voter_female;
                $TotalVoterOther +=$listdata->voter_other;
                $TotalVoter +=$listdata->voter_total;


                @endphp


                <tr>
                  <!--  <td>{{ $count }}</td> -->
                  <td>{{$listdata->PS_NO }}</td>
                  <td>{{$listdata->PS_NAME_EN }}</td>
                  <td>{{$listdata->LOCN_TYPE }}</td>
                  <td>{{$listdata->PS_TYPE }}</td>
                  <td>{{$listdata->electors_male }}</td>
                  <td>{{$listdata->electors_female }}</td>
                  <td>{{$listdata->electors_other }}</td>
                  <td>{{$listdata->electors_total }}</td>
                  <td>{{$listdata->voter_male }}</td>
                  <td>{{$listdata->voter_female }}</td>
                  <td>{{$listdata->voter_other }}</td>
                  <td>{{$listdata->voter_total }}</td>
                  <td>
                    @if($listdata->ro_ps_finalize == 0 && $listdata->deo_ps_finalize == 0 && $listdata->ps_finalize == 0)
                    Waiting For RO Approval
                    @elseif($listdata->ro_ps_finalize == 1 && $listdata->deo_ps_finalize == 0 && $listdata->ps_finalize == 0)
                    Waiting For DEO Approval
                    @elseif($listdata->ro_ps_finalize == 1 && $listdata->deo_ps_finalize == 1 && $listdata->ps_finalize == 0)
                    Waiting For CEO Approval
                    @elseif($listdata->ro_ps_finalize == 1 && $listdata->deo_ps_finalize == 1 && $listdata->ps_finalize == 1)
                    Finalized
                    @else
                    -
                    @endif
                  </td>

                </tr>

                @php $count++; @endphp
                @empty
                <tr>
                  <td class="text-center" colspan="14">No Data Found For Polling Station</td>
                </tr>
                @endforelse

                <tr>
                  <td><b>Total</b></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><b>{{$TotalElectorMale}}</b></td>
                  <td><b>{{$TotalElectorFeMale}}</b></td>
                  <td><b>{{$TotalElectorOther}}</b></td>
                  <td><b>{{$TotalElector }}</b></td>
                  <td><b>{{$TotalVoterMale}}</b></td>
                  <td><b>{{$TotalVoterFeMale}}</b></td>
                  <td><b>{{$TotalVoterOther}}</b></td>
                  <td><b>{{$TotalVoter}}</b></td>
                </tr>

              </tbody>
            </table>

          </div><!-- End Of  table responsive -->
        </div><!-- End Of intra-table Div -->


      </div><!-- End Of random-area Div -->

    </div><!-- End OF page-contant Div -->
  </div>
</div><!-- End Of parent-wrap Div -->

<!--EDIT POP UP ENDS-->
<div class="modal fade" id="confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title w-100">Are you sure you want to finalize End of Poll Voter turnout data?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body">
        <p><span style="color:red">After Publish, the Voter turnout percentage will be updated for public through Voter turnout app.<span></p>
      </div>
      <form method="post" action="<?php echo url('acceo/turnout/publish-turnout'); ?>" id="publishTurnoutFrm">
        {{ csrf_field() }}
        <input type="hidden" name="ac_no" value="{{$ac_id}}">
        <input type="hidden" name="dist_no" value="{{$dist_no}}">
      </form>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <a class="btn btn-danger btn-ok confirm_button" onclick="submitForm();">Confirm</a>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
  function finalize() {
    $('#confirm').modal('show');
  }

  function submitForm() {
    document.getElementById("publishTurnoutFrm").submit();
  }

  $(document).on("click", ".psdefinalize", function() {
    var ac_no = $('#ac_id').val();
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "<?php echo url('/acceo/turnout/CeoPsDefinalizeUpdate') ?>",
      type: "POST",
      data: {
        ac_no: ac_no
      },
      cache: false,
      success: function() {
        location.reload();
        $(".updated").css("display", "block");
      }
    });

  });

  $(document).on("click", ".psfinalize", function() {
    var ac_no = $('#ac_id').val();

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "<?php echo url('/acceo/turnout/CeoPsFinalizeUpdate') ?>",
      type: "POST",
      data: {
        ac_no: ac_no
      },
      cache: false,
      success: function() {
        location.reload();
        $(".updated").css("display", "block");
      }
    });

  });
</script>
@endsection