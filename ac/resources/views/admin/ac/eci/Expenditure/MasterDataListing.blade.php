@extends('admin.layouts.ac.report-theme')
@section('content')
<main role="main" class="inner cover mb-3">
   


 <!--FILTER ENDS HERE-->

<section class="mt-5">
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%;">
  
      <div class=" card-header">
      <div class=" row d-flex align-items-center">
            <div class="col"><h4> List Of Master Data {{$user_data->placename}}</h4></div> 
              <div class="col"> 
              <a href="{{url('eci/masterEntry/')}}"><button type="button" id="Cancel" class="btn btn-primary pull-right" >ADD NEW</button></a>
              </p>
              </div>
            </div>
      </div>
   
 <div class="card-body"> 
	   
<table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
         <tr>
          <!-- <th>Serial No</th> -->
          <th>Date of declaration of result</th> 
          <th>Type of Election</th>
		  <th>State</th>
          <th>Ceiling amoun</th> 
		  <th>Last date for lodging of expenditure accounts by the candidate</th> 
		  <th>Action</th>
          
        </tr>
        </thead>
        <tbody>
        @php  $count = 1; @endphp
         @forelse ($MasterData as $key=>$listdata)
          <tr>

           <!--  <td>{{ $count }}</td> -->
            <td><?php echo date('d-m-Y', strtotime($listdata->result_declaration_date)); ?></td>
            <td>{{$listdata->type_of_election }}</td>
            <td>{{$listdata->ST_NAME }}</td>
            <td>{{$listdata->ceiling_amt }}</td>
            <td><?php echo date('d-m-Y', strtotime($listdata->lodged_date)); ?></td>
			<td><a href="{{url('/eci/masterEntry/?id=')}}{{base64_encode($listdata->id)}}"><button class="btn btn-primary">EDIT</button></a></td>
            
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Master Entry</td>                 
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


<!-- Validation  JavaScript -->

<!--**********FORM VALIDATION STARTS**********-->
<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>

<!--**********FORM VALIDATIONS SCRIPT**********-->
<script type="text/javascript">
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
$("#EciCustomReportFilter").validate({
    rules: {
                state:        { required: true,noSpace: true},
                ScheduleList: { number: true},
            },
  messages: { 
                state: {
                      required: "Select state name.",
                      noSpace: "State name must be without space.",
                  },
                ScheduleList: {
                      number: "Scedule ID should be numbers only.",
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



