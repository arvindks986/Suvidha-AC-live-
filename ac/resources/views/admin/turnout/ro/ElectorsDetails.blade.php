@extends('admin.layouts.ac.dashboard-theme')
@section('title', 'Suvidha-Ac')
@section('bradcome', 'Electors Details')
@section('content')
<main role="main" class="inner cover mb-3">

  <section>
    <div class="container-fluid">
      <div class="row">
        <div class="card text-left mt-3" style="width:100%; margin:0 auto;">
          <div class=" card-header">
            <div class=" row d-flex align-items-center">
              <div class="col">
                <h4>Electors Details</h4>
              </div>
              <div class="col">
                <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st_name}}</span>&nbsp;&nbsp; <b class="bolt">AC Name:</b> <span class="badge badge-info">{{$ac_no}}-{{$ac_name}}</span>&nbsp;&nbsp;
                </p>
              </div>
            </div>
          </div>
          @if($errors->any())
          <div class="alert alert-info">{{$errors->first()}}</div>
          @endif

          @if (session('error'))
          <div class="alert alert-info">{{ session('error') }}</div>
          @endif
          @if (session('success'))
          <div class="alert alert-success"> {{session('success') }}</div>
          @endif
          @if(count($results) > 0 && count($results) == $totalPsElectoralDataFinalized)
          <div class="alert alert-info"><strong>All Polling station elector data is finalized by RO due to which Electors Details is now disabled for editing</strong></div>
          @endif
          <div class="card-body">
            @if(isset($ElectorsDetails))
            <table class="table table-striped table-bordered table-hover" style="width:100%">
              <thead>
                <tr>
                  <th>General Electors Male</th>
                  <th>General Electors Female</th>
                  <th>General Electors Others</th>
                  <th>Total General Electors</th>
                  <th>Service Electors</th>
                  <th>Grand Total</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>

                <?php //dd($ElectorsDetails); 
                $gtotal = $ElectorsDetails->electors_total + $ElectorsDetails->electors_service
                ?>
                <tr>
                  <td>{{$ElectorsDetails->electors_male }}</td>
                  <td>{{$ElectorsDetails->electors_female }}</td>
                  <td>{{$ElectorsDetails->electors_other }}</td>
                  <td>{{$ElectorsDetails->electors_total }}</td>
                  <td>{{$ElectorsDetails->electors_service }}</td>
                  <td>{{$gtotal}}</td>
                  <td>
                    @if(false)
                    @if(count($results) > 0 && count($results) == $totalPsElectoralDataFinalized)
                    <button type="button" class="btn btn-default disabled">Editing is Disabled</button>
                    @else
                    <button type="button" class="btn btn-primary electrolpopup" data-toggle="modal" data-target="#myModal" data-male="{{$ElectorsDetails->electors_male }}" data-female="{{$ElectorsDetails->electors_female }}" data-other="{{$ElectorsDetails->electors_other }}" data-total="{{$ElectorsDetails->electors_total }}" data-service="{{$ElectorsDetails->electors_service }}" data-gtotal="{{$gtotal }}">Edit</button>
                    @endif
                    @endif
                  </td>
                </tr>


              </tbody>
            </table>
            @else
            <div class="norecords"><i class="fa fa-ban"></i>
              <h4>No Records Found</h4>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<!--EDIT POP UP STARTS-->
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Update General Electors Data</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="{{url('roac/turnout/ElectorsDetailsUpdate')}}" id="ElectorsDetailsUpdate">

          {{ csrf_field() }}


          <div class="form-group row">
            <label class="col-sm-4 form-control-label">General Electors Male<sup>*</sup></label>
            <div class="col-sm-8">
              <input type="text" id="electors_male" maxsize="6" minsize="1" class="form-control" name="electors_male" value="">
              <span class="text-danger"></span>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-4 form-control-label">General Electors Female<sup>*</sup></label>
            <div class="col-sm-8">
              <input type="text" id="electors_female" maxsize="6" minsize="1" class="form-control" name="electors_female" value="">
              <span class="text-danger"></span>
            </div>
          </div>


          <div class="form-group row">
            <label class="col-sm-4 form-control-label">General Electors Others<sup>*</sup></label>
            <div class="col-sm-8">
              <input type="text" id="electors_other" maxsize="6" minsize="1" class="form-control" name="electors_other" value="">
              <span class="text-danger"></span>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-4 form-control-label">Total General Electors<sup>*</sup></label>
            <div class="col-sm-8">
              <input type="text" id="electors_total" maxsize="6" minsize="1" class="form-control" name="electors_total" value="">
              <span class="text-danger"></span>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 form-control-label">Services Electors<sup>*</sup></label>
            <div class="col-sm-8">
              <input type="text" id="electors_services" maxsize="6" minsize="1" class="form-control" name="electors_services" value="">
              <span class="text-danger"></span>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-4 form-control-label">Grand Total <sup>*</sup></label>
            <div class="col-sm-8">
              <input type="text" id="electors_gtotal" maxsize="6" minsize="1" class="form-control" name="electors_gtotal" value="" readonly>
              <span class="text-danger"></span>
            </div>
          </div>
          <div class="form-group float-right">
            <input type="submit" name="Update" class="btn btn-primary custombtn">
          </div>
        </form>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
<!--EDIT POP UP ENDS-->

<!-- Validation  JavaScript -->

<!--**********FORM VALIDATION STARTS**********-->
<script src="{{ asset('theme/vendor/jquery/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>

<!--**********FORM VALIDATIONS SCRIPT**********-->
<script type="text/javascript">
  $(document).on("click", ".electrolpopup", function() {

    male = $(this).attr('data-male');
    female = $(this).attr('data-female');
    other = $(this).attr('data-other');
    total = $(this).attr('data-total');
    sevice = $(this).attr('data-service');
    gtotal = $(this).attr('data-gtotal');

    $('#electors_male').val(male);
    $('#electors_female').val(female);
    $('#electors_other').val(other);
    $('#electors_total').val(total);
    $('#electors_services').val(sevice);
    $('#electors_gtotal').val(gtotal);
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
  $("#ElectorsDetailsUpdate").validate({
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
      electors_services: {
        required: true,
        number: true,
        noSpace: true,
        minlength: 1,
        maxlength: 7,
      },
      electors_gtotal: {
        required: true,
        number: true,
        noSpace: true,
        minlength: 1,
        maxlength: 7,
      },
    },
    messages: {
      electors_male: {
        required: "Male Numbers required.",
        number: "Male should be numbers only.",
        noSpace: "Enter Male without space.",
        minlength: "Minlength length of Male should be 1 characters.",
        maxlength: "Maximum length of Male should be 7 characters.",
      },
      electors_female: {
        required: "Female Numbers required.",
        number: "Female should be numbers only.",
        noSpace: "Enter Female without space.",
        minlength: "Minlength length of Female should be 1 characters.",
        maxlength: "Maximum length of Female should be 7 characters.",
      },
      electors_other: {
        required: "Other Numbers required.",
        number: "Other should be numbers only.",
        noSpace: "Enter Other without space.",
        minlength: "Minlength length of Other should be 1 characters.",
        maxlength: "Maximum length of Other should be 7 characters.",
      },
      electors_total: {
        required: "Total Numbers required.",
        number: "Total should be numbers only.",
        noSpace: "Enter Total without space.",
        minlength: "Minlength length of Total should be 1 characters.",
        maxlength: "Maximum length of Total should be 7 characters.",
      },
      electors_services: {
        required: "Total Numbers required.",
        number: "Total should be numbers only.",
        noSpace: "Enter Total without space.",
        minlength: "Minlength length of Total should be 1 characters.",
        maxlength: "Maximum length of Total should be 7 characters.",
      },
      electors_gtotal: {
        required: "Total Numbers required.",
        number: "Total should be numbers only.",
        noSpace: "Enter Total without space.",
        minlength: "Minlength length of Total should be 1 characters.",
        maxlength: "Maximum length of Total should be 7 characters.",
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

  $('#ElectorsDetailsUpdate input').on('keyup', function() {
    var electors_total = parseInt($('#electors_total').val());
    var electors_services = parseInt($('#electors_services').val());
    $('#electors_gtotal').val(electors_total + electors_services);
  })
</script>

<!--**********FORM VALIDATION ENDS*************-->

@endsection