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
        @if($lists->end_of_poll_finalize==1)

        @else
        @if($is_finalize_ro =='1')
        @if($is_finalize == '1')
        @if($is_finalize_ceo =='0')
        <span class="report-btn psfinalize btn btn-warning btn-lg">Request Sent To CEO For Approval</span>
        @else
        <span class="report-btn btn btn-success btn-lg">CEO Approval Received</span>
        @endif
        @else
        <span class="report-btn psdefinalize btn btn-warning btn-lg">Definalize RO PS Data</span>
        <span class="report-btn psfinalize btn btn-success btn-lg">Approve RO Data</span>
        @endif
        @else
        <span class="report-btn btn btn-warning btn-lg">Waiting For RO Approval</span>
        @endif
        @endif
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
                  @if($listdata->ro_ps_finalize == 0 && $listdata->deo_ps_finalize == 0 &&  $listdata->ps_finalize == 0)
                    Waiting For RO Approval
                  @elseif($listdata->ro_ps_finalize == 1 && $listdata->deo_ps_finalize == 0 &&  $listdata->ps_finalize == 0)
                    Waiting For DEO Approval
                  @elseif($listdata->ro_ps_finalize == 1 && $listdata->deo_ps_finalize == 1 &&  $listdata->ps_finalize == 0)
                    Waiting For CEO Approval
                  @elseif($listdata->ro_ps_finalize == 1 && $listdata->deo_ps_finalize == 1 &&  $listdata->ps_finalize == 1)
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

@endsection

@section('script')


<script type="text/javascript">
  $(document).on("click", ".psdefinalize", function() {
    var ac_no = $('#ac_id').val();

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "<?php echo url('/acdeo/turnout/DeoPsDefinalizeUpdate') ?>",
      type: "POST",
      //data:'ac_no='+ac_no+'_token=<?php echo csrf_token() ?>',
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
      url: "<?php echo url('/acdeo/turnout/DeoPsFinalizeUpdate') ?>",
      type: "POST",
      //data:'ac_no='+ac_no+'_token=<?php echo csrf_token() ?>',
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