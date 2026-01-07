@extends('admin.layouts.ac.dashboard-theme')
@section('title', 'Suvidha-Ac')
@section('bradcome', 'Electors Details')
@section('content')

 
@if($errors->any())
        <div class="alert alert-info">{{$errors->first()}}</div>
  @endif

@if (session('error'))
           <div class="alert alert-info">{{ session('error') }}</div>
@endif
 @if (session('success'))
           <div class="alert alert-info">{{ session('success') }}</div>
@endif
<main role="main" class="inner cover mb-3">
  
<section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left mt-3" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row d-flex align-items-center">
            <div class="col"><h4>Electors Details {{$user_data->placename}}</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp;  
              </p>
              </div>
            </div>
      </div>
   
 <div class="card-body">  
    <table class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
         <tr>
          <!-- <th>Serial No</th> -->
          <th>Male</th> 
          <th>Female</th> 
          <th>Other</th> 
          <th>Total</th> 
          <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @php  
        $count = 1;

        @endphp

         @forelse ($ElectorsDetails as $key=>$listdata)


          <tr>
            <!-- <td>{{ $count }}</td>-->
          
            <td>{{$listdata->electors_male }}</td>
            <td>{{$listdata->electors_female }}</td>
            <td>{{$listdata->electors_other }}</td>
            <td>{{$listdata->electors_total }}</td>

            <td >
            
              <button type="button" class="btn btn-primary electrolpopup" data-toggle="modal" data-target="#myModal" data-male="{{$listdata->electors_male }}" data-female="{{$listdata->electors_female }}" data-other="{{$listdata->electors_other }}" data-total="{{$listdata->electors_total }}">Edit</button>
              
            </td>
         
          </tr>
       
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="5">No Data Found For Election Nomination Data</td>                 
              </tr>
          @endforelse         
        </tbody>
    </table>
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
        <h4 class="modal-title">Update Electors Data</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
       <form class="form-horizontal" method="POST" action="{{url('roac/ElectorsDetailsUpdate')}}" id="ElectorsDetailsUpdate">

         {{ csrf_field() }}
                         
  
         <div class="form-group row">
          <label class="col-sm-4 form-control-label">Male <sup>*</sup></label>
          <div class="col-sm-8">
           <input type="text" id="electors_male"  maxsize="6" minsize="1" class="form-control" name="electors_male" value="">
           <span class="text-danger"></span>
          </div>
        </div>

          <div class="form-group row">
          <label class="col-sm-4 form-control-label">Female <sup>*</sup></label>
          <div class="col-sm-8">
          <input type="text" id="electors_female"  maxsize="6" minsize="1" class="form-control" name="electors_female" value="">
          <span class="text-danger"></span>
          </div>
        </div>
        

    <div class="form-group row">
          <label class="col-sm-4 form-control-label">Other <sup>*</sup></label>
          <div class="col-sm-8">
           <input type="text" id="electors_other"  maxsize="6" minsize="1" class="form-control" name="electors_other" value="">
           <span class="text-danger"></span>
          </div>
        </div>  
        

    <div class="form-group row">
          <label class="col-sm-4 form-control-label">Total <sup>*</sup></label>
          <div class="col-sm-8">
              <input type="text" id="electors_total" maxsize="6" minsize="1" class="form-control" name="electors_total" value="">
           <span class="text-danger"></span>
          </div>
        </div>

        <input type="submit" name="Update">
              
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

$(document).on("click", ".electrolpopup", function () {


       male = $(this).attr('data-male');
       female = $(this).attr('data-female');
       other = $(this).attr('data-other');
       total = $(this).attr('data-total');
    
       
       $('#electors_male').val(male);
       $('#electors_female').val(female);
       $('#electors_other').val(other);
       $('#electors_total').val(total);
      

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
              electors_male: { required: true,number:true,noSpace: true,minlength:1, maxlength: 7,},
              electors_female: { required: true,number:true,noSpace: true,minlength:1, maxlength: 7,},
              electors_other: { required: true,number:true,noSpace: true,minlength:1, maxlength: 7,},
              electors_total: { required: true,number:true,noSpace: true,minlength:1, maxlength: 7,},
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
            },
        errorElement: 'div',
          errorPlacement: function (error, element) {
              var placement = $(element).data('error');
              if (placement) {
                  $(placement).append(error)
              } else {
                  error.insertAfter(element);
              }
          }
});
//********************ECI FILTER FORM VALIDATION ENDS********************//

</script>

<!--**********FORM VALIDATION ENDS*************-->

@endsection 