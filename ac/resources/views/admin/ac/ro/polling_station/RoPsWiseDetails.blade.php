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

@if (session('success_mes'))
<div class="alert alert-success"> {{session('success_mes') }}</div>
@endif

@if (session('error_mes'))
<div class="alert alert-danger"> {{session('error_mes') }}</div>
@endif

@if (\Session::has('success'))
<div class="alert alert-success"> {{\Session::get('success')}} </div>
@endif

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
        @if($showFinalizeBtn)
        <span class="report-btn psfinalize btn btn-warning btn-lg" onclick="psfinalize(this)" data-ac="{{$user_data->ac_no}}" data-statecode="{{$user_data->st_code}}">Finalize PS Data</span>
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


<section class="mt-3">
  <div class="container">
    <div class="row">
      @if($lists->end_of_poll_finalize==1)
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
                  <th colspan=" {{($showTableColumns) ? 13 : 8 }} " class="text-center">{!! $heading_title !!}</th>
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
                  @if($showTableColumns)
                  <th>Voter Male</th>
                  <th>Voter Female</th>
                  <th>Voter Other</th>
                  <th>Voter Total</th>
                  <th>Action</th>
                  @endif

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
                  <!--    <td>{{ $count }}</td> -->
                  <td>{{$listdata->PS_NO }}</td>
                  <td>{{$listdata->PS_NAME_EN }}</td>
                  <td>{{$listdata->LOCN_TYPE }}</td>
                  <td>{{$listdata->PS_TYPE }}</td>
                  <td>{{$listdata->electors_male }}</td>
                  <td>{{$listdata->electors_female }}</td>
                  <td>{{$listdata->electors_other }}</td>
                  <td>{{$listdata->electors_total }}</td>
                  @if($showTableColumns)
                  <td>{{$listdata->voter_male }}</td>
                  <td>{{$listdata->voter_female }}</td>
                  <td>{{$listdata->voter_other }}</td>
                  <td>{{$listdata->voter_total }}</td>
                  <td>
                    @if($listdata->ro_ps_finalize == 0 && $listdata->deo_ps_finalize == 0 &&  $listdata->ps_finalize == 0)
                    <button type="button" class="btn btn-primary PsWiseDetailspopup" data-toggle="modal" data-target="#myModal" data-psname="{{$listdata->PS_NAME_EN }}" data-emale="{{$listdata->electors_male }}" data-efemale="{{$listdata->electors_female }}" data-eother="{{$listdata->electors_other }}" data-etotal="{{$listdata->electors_total }}" data-vmale="{{$listdata->voter_male }}" data-vfemale="{{$listdata->voter_female }}" data-vother="{{$listdata->voter_other }}" data-vtotal="{{$listdata->voter_total }}" data-psname="{{$listdata->PS_NAME_EN }}" data-psno="{{$listdata->PS_NO }}" data-ccode="{{$listdata->CCODE }}">Edit</button>
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
                  @endif


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
                  @if($showTableColumns)
                  <td><b>{{$TotalVoterMale}}</b></td>
                  <td><b>{{$TotalVoterFeMale}}</b></td>
                  <td><b>{{$TotalVoterOther}}</b></td>
                  <td><b>{{$TotalVoter}}</b></td>
                  <td></td>
                  @endif
                </tr>

              </tbody>
            </table>

          </div><!-- End Of  table responsive -->
        </div><!-- End Of intra-table Div -->


      </div><!-- End Of random-area Div -->

    </div><!-- End OF page-contant Div -->
  </div>
</div><!-- End Of parent-wrap Div -->
</div>

<!--EDIT POP UP STARTS-->
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Polling Station <span id="psnameid"></span>-<span id="psnoid"></span></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form class="form-horizontal" method="POST" action="{{url('roac/turnout/RoPsWiseDetailsUpdate')}}" id="RoPsWiseDetailsUpdate">
        <!-- Modal body -->
        <div class="modal-body">
          {{ csrf_field() }}
          <input type="hidden" name="psnoinput" id="psnoinput" value="">
          <input type="hidden" name="psccode" id="psccode" value="">
          <input type="hidden" name="ac_no" id="ac_no" value="{{$ac_id}}">
          <div class="form-group row">
            <label class="col-sm-4 form-control-label">Voter Male <sup>*</sup></label>
            <div class="col-sm-8">
              <input type="text" id="voter_male" maxsize="6" minsize="1" class="form-control" name="voter_male" value="">
              <span class="text-danger"></span>
            </div>
          </div>


          <div class="form-group row">
            <label class="col-sm-4 form-control-label">Voter Female <sup>*</sup></label>
            <div class="col-sm-8">
              <input type="text" id="voter_female" maxsize="6" minsize="1" class="form-control" name="voter_female" value="">
              <span class="text-danger"></span>
            </div>
          </div>


          <div class="form-group row">
            <label class="col-sm-4 form-control-label">Voter Other <sup>*</sup></label>
            <div class="col-sm-8">
              <input type="text" id="voter_other" maxsize="6" minsize="1" class="form-control" name="voter_other" value="">
              <span class="text-danger"></span>
            </div>
          </div>


          <div class="form-group row">
            <label class="col-sm-4 form-control-label">Voter Total <sup>*</sup></label>
            <div class="col-sm-8">
              <input type="text" id="voter_total" maxsize="6" minsize="1" class="form-control" name="voter_total" value="">
              <span class="text-danger"></span>
            </div>
          </div>


        </div>

        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          <input type="submit" class="btn btn-success" name="Update">
        </div>
      </form>

    </div>
  </div>
</div>
<!--EDIT POP UP ENDS-->





@endsection

@section('script')
<!--**********FORM VALIDATION STARTS**********-->

<script type="text/javascript">
  function finalize() {
    $('#confirm').modal('show');
  }

  function submitForm() {
    window.location.href = "<?php echo url('roac/turnout/publish-turnout'); ?>";
    //document.preview.submit();
  }
</script>

<script type="text/javascript">
  function psfinalize(clicked_object) {
    var ac_no = clicked_object.getAttribute('data-ac');
    var statecode = clicked_object.getAttribute('data-statecode');

    $.ajax({
      url: "<?php echo url('/roac/turnout/RoPsFinalizeUpdate') ?>",
      type: "POST",
      cache: false,
      data: '_token=<?php echo csrf_token() ?>',
      success: function() {
        location.reload();
        $(".updated").css("display", "block");
      }
    });
  }

  $(document).on("click", ".PsWiseDetailspopup", function() {


    vmale = $(this).attr('data-vmale');
    vfemale = $(this).attr('data-vfemale');
    vother = $(this).attr('data-vother');
    vtotal = $(this).attr('data-vtotal');
    psname = $(this).attr('data-psname');
    psno = $(this).attr('data-psno');
    ccode = $(this).attr('data-ccode');

    $('#voter_male').val(vmale);
    $('#voter_female').val(vfemale);
    $('#voter_other').val(vother);
    $('#voter_total').val(vtotal);
    $('#psnameid').text(psname);
    $('#psnoid').text(psno);
    $('#psnoinput').val(psno);
    $('#psccode').val(ccode);




  });
  //*******************EXTRA VALIDATION METHODS STARTS********************//
  //maxsize
  $.validator.addMethod('maxSize', function(value, element, param) {
    return this.optional(element) || (element.files[0].size <= param)
  });
  //minsize
  $.validator.addMethod('minSize', function(value, element, param) {
    return this.optional(element) || (element.files[0].size >= param)
  });
  //alphanumeric
  $.validator.addMethod("alphnumericregex", function(value, element) {
    return this.optional(element) || /^[a-z0-9\._\s]+$/i.test(value);
  });
  //alphaonly
  $.validator.addMethod("onlyalphregex", function(value, element) {
    return this.optional(element) || /^[a-z\.\s]+$/i.test(value);
  });
  //without space
  $.validator.addMethod("noSpace", function(value, element) {
    return value.indexOf(" ") < 0 && value != "";
  }, "No space please and don't leave it empty");
  //*******************EXTRA VALIDATION METHODS ENDS********************//


  //*******************POLLIN STATION FORM VALIDATION STARTS********************//
  $("#RoPsWiseDetailsUpdate").validate({
    rules: {
      voter_male: {
        required: true,
        number: true,
        noSpace: true,
        minlength: 1,
        maxlength: 7,
      },
      voter_female: {
        required: true,
        number: true,
        noSpace: true,
        minlength: 1,
        maxlength: 7,
      },
      voter_other: {
        required: true,
        number: true,
        noSpace: true,
        minlength: 1,
        maxlength: 7,
      },
      voter_total: {
        required: true,
        number: true,
        noSpace: true,
        minlength: 1,
        maxlength: 7,
      },
    },
    messages: {
      voter_male: {
        required: "Voter Male Numbers required.",
        number: "Voter Male should be numbers only.",
        noSpace: "Voter Enter Male without space.",
        minlength: "Minlength length of Voter Male should be 1 characters.",
        maxlength: "Maximum length of Voter Male should be 7 characters.",
      },
      voter_female: {
        required: "Voter Female Numbers required.",
        number: "Voter Female should be numbers only.",
        noSpace: "Enter Female without space.",
        minlength: "Minlength length of Voter Female should be 1 characters.",
        maxlength: "Maximum length of Voter Female should be 7 characters.",
      },
      voter_other: {
        required: "Voter Other Numbers required.",
        number: "Voter Other should be numbers only.",
        noSpace: "Enter Other without space.",
        minlength: "Minlength length of Voter Other should be 1 characters.",
        maxlength: "Maximum length of Voter Other should be 7 characters.",
      },
      voter_total: {
        required: "Voter Total Numbers required.",
        number: "Voter Total should be numbers only.",
        noSpace: "Enter Voter Total without space.",
        minlength: "Minlength length of Voter Total should be 1 characters.",
        maxlength: "Maximum length of Voter Total should be 7 characters.",
      },
    },
    errorElement: 'div',
    errorPlacement: function(error, element) {
      var placement = $(element).data('error');
      if (placement) {
        $(placement).append(error)
      } else {
        error.insertAfter(element);
      }
    }
  });
  //********************POLLIN STATION FORM VALIDATION ENDS********************//
</script>
@endsection