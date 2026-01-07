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

<div class="alert alert-info updated" style="display: none;">Polling Station Electoral Data Updated Successfully</div>

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


<section class="statistics pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
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
    <div class="row">
      <div class="form-group col-md-3"> <label>Phase</label>
        <select name="phase" id="phase" class="form-control" onchange="filter()">
          <option value="">Select Phase</option>
          @if(isset($phases))
          @foreach($phases as $key => $a)
          @if($phase==$a->SCHEDULENO)
          <option value="{{$a->SCHEDULENO}}" selected="{{$phase}}">Phase {{$key+1}}</option>
          @else
          <option value="{{$a->SCHEDULENO}}">Phase {{$key+1}}</option>
          @endif
          @endforeach
          @endif
        </select>
      </div>

      <!---AC FILTER-->
      <div class="form-group col-md-3"> <label>AC Constituency </label>

        <select name="ac_id" id="ac_id" class="form-control" onchange="filter()">
          <option value="all">All AC</option>
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
    </div>
  </div>
</section>

@if(count($results) > 0)
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

      @if($ac_id != 'all' && $ac_id != '')
      <div class="card text-left" style="width:100%; margin:0 auto;">

        <table class="table table-striped table-bordered" style="width:100%">
          <thead>
            <tr>
              <th colspan="4" align="center">Electors</th>
            </tr>
            <tr>
              <th>Male</th>
              <th>Female</th>
              <th>Other</th>
              <th>total</th>
            </tr>
          </thead>
          <tr>
            <td>@if(isset($ac_data)) {{$ac_data->electors_male}} @endif</td>
            <td>@if(isset($ac_data)) {{$ac_data->electors_female}} @endif</td>
            <td>@if(isset($ac_data)) {{$ac_data->electors_other}} @endif</td>
            <td>@if(isset($ac_data)) {{$ac_data->electors_total}} @endif</td>
          </tr>
        </table>
      </div>
      @endif


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
                  <th>S.No</th>
                  <th>AC No</th>
                  <th>AC Name</th>
                  <th>PS No</th>
                  <th>PS Name</th>
                  <th>Location Type</th>
                  <th>PS Type</th>
                  <th>Electors Male</th>
                  <th>Electors Female</th>
                  <th>Electors Other</th>
                  <th>Electors Total</th>
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
                @endphp


                @forelse ($results as $key=>$listdata)

                @php

                $TotalElectorMale +=$listdata->electors_male;
                $TotalElectorFeMale +=$listdata->electors_female;
                $TotalElectorOther +=$listdata->electors_other;
                $TotalElector +=$listdata->electors_total;
                @endphp


                <tr>
                  <td>{{$count++}}</td>
                  <td>{{$listdata->acno }}</td>
                  <td>{{$listdata->acn }}</td>
                  <td>{{$listdata->PS_NO }}</td>
                  <td>{{$listdata->PS_NAME_EN }}</td>
                  <td>{{$listdata->LOCN_TYPE }}</td>
                  <td>{{$listdata->PS_TYPE }}</td>
                  <td>{{$listdata->electors_male }}</td>
                  <td>{{$listdata->electors_female }}</td>
                  <td>{{$listdata->electors_other }}</td>
                  <td>{{$listdata->electors_total }}</td>
                  <td>
                    @if($listdata->deo_ps_finalize == '0' && $listdata->ro_ps_finalize == '0')
                    <button type="button" class="btn btn-primary PsWiseDetailspopup" data-toggle="modal" data-target="#myModal" data-emale="{{$listdata->electors_male }}" data-efemale="{{$listdata->electors_female }}" data-eother="{{$listdata->electors_other }}" data-etotal="{{$listdata->electors_total }}" data-psname="{{$listdata->PS_NAME_EN }}" data-psno="{{$listdata->PS_NO }}" data-ccode="{{$listdata->CCODE }}">Edit</button>
                    @else
                    Finalized
                    @endif
                  </td>

                </tr>
                @empty
                <tr>
                  <td class="text-center" colspan="11">No Data Found For Polling Station</td>
                </tr>
                @endforelse




              </tbody>
              <tfoot>
                <tr>
                  <td><b>Grand Total</b></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><b>{{$TotalElectorMale}}</b></td>
                  <td><b>{{$TotalElectorFeMale}}</b></td>
                  <td><b>{{$TotalElectorOther}}</b></td>
                  <td><b>{{$TotalElector }}</b></td>
                  <td></td>
                </tr>
              </tfoot>
            </table>

          </div><!-- End Of  table responsive -->
        </div><!-- End Of intra-table Div -->
      </div><!-- End Of random-area Div -->
    </div><!-- End OF page-contant Div -->
  </div>
</div><!-- End Of parent-wrap Div -->
@else
<div class="alert alert-info updated">No Data Found Please change filters</div>

@endif
<!--EDIT POP UP STARTS-->
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Polling Station <span id="psnameid"></span>-<span id="psnoid"></span></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <form class="form-horizontal" method="POST" action="{{url('acceo/turnout/CeoPsWiseDetailsUpdate')}}" id="CeoPsWiseDetailsUpdate">
        <div class="modal-body">

          {{ csrf_field() }}

          <input type="hidden" name="isElectroal" id="isElectroal" value="1">
          <input type="hidden" name="psnoinput" id="psnoinput" value="">
          <input type="hidden" name="psccode" id="psccode" value="">
          <input type="hidden" name="ac_no" id="ac_no" value="{{$ac_id}}">

          <div class="form-group row">
            <label class="col-sm-4 form-control-label">Electors Male <sup>*</sup></label>
            <div class="col-sm-8">
              <input type="text" id="electors_male" maxsize="6" minsize="1" class="form-control" name="electors_male" value="">
              <span class="text-danger"></span>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-4 form-control-label">Electors Female <sup>*</sup></label>
            <div class="col-sm-8">
              <input type="text" id="electors_female" maxsize="6" minsize="1" class="form-control" name="electors_female" value="">
              <span class="text-danger"></span>
            </div>
          </div>


          <div class="form-group row">
            <label class="col-sm-4 form-control-label">Electors Other <sup>*</sup></label>
            <div class="col-sm-8">
              <input type="text" id="electors_other" maxsize="6" minsize="1" class="form-control" name="electors_other" value="">
              <span class="text-danger"></span>
            </div>
          </div>


          <div class="form-group row">
            <label class="col-sm-4 form-control-label">Electors Total <sup>*</sup></label>
            <div class="col-sm-8">
              <input type="text" id="electors_total" maxsize="6" minsize="1" class="form-control" name="electors_total" value="">
              <span class="text-danger"></span>
            </div>
          </div>
        </div>

        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-info">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!--EDIT POP UP ENDS-->

@endsection

@section('script')

<script type="text/javascript">
  $(document).on("click", ".PsWiseDetailspopup", function() {
    emale = $(this).attr('data-emale');
    efemale = $(this).attr('data-efemale');
    eother = $(this).attr('data-eother');
    etotal = $(this).attr('data-etotal');
    psname = $(this).attr('data-psname');
    psno = $(this).attr('data-psno');
    ccode = $(this).attr('data-ccode');

    $('#electors_male').val(emale);
    $('#electors_female').val(efemale);
    $('#electors_other').val(eother);
    $('#electors_total').val(etotal);
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

  //*******************ECI FILTER FORM VALIDATION STARTS********************//
  $("#pswisedataform").validate({
    rules: {
      ac_id: {
        required: true,
        number: true
      },
    },
    messages: {
      ac_id: {
        required: "AC required.",
        number: "AC should be numbers only.",
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
  //********************ECI FILTER FORM VALIDATION ENDS********************//  

  //ASSEMBY ONCHAGE FUNCTION STARTS
  function get_ac_list_by_st_pc() {
    var default_ac = "<?php echo $ac_id; ?>";
    var state = jQuery('#state').val();
    var ac_id = jQuery('#pc_id').val();


    jQuery.ajax({
      //url: APP_URL+"/get_ac_list_by_st_pc/"+state+'/'+pc_id,
      url: "{!! url('get_ac_list_by_st_pc') !!}" + state + '/' + pc_id,
      type: 'GET',
      data: "state=" + state + "pc_id=" + pc_id,
      success: function(response) {
        if (response.status == 200 && response.error == false && response.acdata != '') {

          var output = [];
          $.each(response.acdata, function(key, value) {
            if (default_ac == value['AC_NO']) {
              output.push('<option value="' + value['AC_NO'] + '" selected="selected">' + $.trim(value['AC_NAME']) + '</option>');
            } else {
              output.push('<option value="' + value['AC_NO'] + '">' + $.trim(value['AC_NAME']) + '</option>');
            }
          });
          $('#ac_id').html(output.join(''));
        } else {
          /*alert('Enternal Server Error!');*/
        }
      }
    });

  }

  //ASSEMBY ONCHANGE FUNCTION ENDS 


  //*******************POLLIN STATION FORM VALIDATION STARTS********************//
  $("#CeoPsWiseDetailsUpdate").validate({
    rules: {
      electors_male: {
        required: true,
        number: true,
        noSpace: true,
        minlength: 1,
        maxlength: 7,
      },
      electors_female: {
        required: true,
        number: true,
        noSpace: true,
        minlength: 1,
        maxlength: 7,
      },
      electors_other: {
        required: true,
        number: true,
        noSpace: true,
        minlength: 1,
        maxlength: 7,
      },
      electors_total: {
        required: true,
        number: true,
        noSpace: true,
        minlength: 1,
        maxlength: 7,
      },

    },
    messages: {
      electors_male: {
        required: "Electors Male Numbers required.",
        number: "Electors Male should be numbers only.",
        noSpace: "Enter Electors Male without space.",
        minlength: "Minlength length of Electors Male should be 1 characters.",
        maxlength: "Maximum length of Electors Male should be 7 characters.",
      },
      electors_female: {
        required: "Electors Female Numbers required.",
        number: "Electors Female should be numbers only.",
        noSpace: "Enter Electors Female without space.",
        minlength: "Minlength length of Electors Female should be 1 characters.",
        maxlength: "Maximum length of Electors Female should be 7 characters.",
      },
      electors_other: {
        required: "Electors Other Numbers required.",
        number: "Electors Other should be numbers only.",
        noSpace: "Enter Electors Other without space.",
        minlength: "Minlength length of Electors Other should be 1 characters.",
        maxlength: "Maximum length of Electors Other should be 7 characters.",
      },
      electors_total: {
        required: "Electors Total Numbers required.",
        number: "Electors Total should be numbers only.",
        noSpace: "Enter Electors Total without space.",
        minlength: "Minlength length of Electors Total should be 1 characters.",
        maxlength: "Maximum length of Electors Total should be 7 characters.",
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

  function filter() {
    var url = "<?= $action ?>";
    var currentPhase = "<?= $phase ?>";
    var query = '';
    if (jQuery("#phase").val() != '' && jQuery("#phase").val() != 'undefined') {
      query += '&phase=' + jQuery("#phase").val();
    }
    if (jQuery("#ac_id").val() != '' && jQuery("#ac_id").val() != 'undefined') {
      query += '&ac_id=' + jQuery("#ac_id").val();
    }
    // if (currentPhase == jQuery("#phase").val()) {
    // }
    window.location.href = url + '?' + query.substring(1);
  }
  $(document).ready(function() {
    $('#data_table_table').DataTable({
      "order": [
        [0, "asc"]
      ],
    });
  });
</script>
@endsection