@extends('admin.layouts.ac.report-theme')
@section('content')
<main role="main" class="inner cover mb-3">

   <!--FILTER STARTS FROM HERE-->
 <div class=" card-header">
      <div class=" row">
            <div class="col">
               @if(Session::has('ScheduleList'))
              <form method="post" action="{{url('/eci/EciNominationAcPhaseFilter')}}" id="EciNominationAcPhaseFilter">
                 {{ csrf_field() }}
                 
                 <input type="hidden" name="stcode" value="{{base64_encode($stcode)}}">
                 <!--PHASE LIST DROPDOWN STARTS-->
                  <select name="ScheduleList" id="ScheduleList">
                  <option value="">Select Phase</option>
                  @php  $i = 1; @endphp
                  @foreach (Session::get('ScheduleList') as $Schedule_List ))

                  @if (old('ScheduleList') == $Schedule_List->SCHEDULEID)
                        <option value="{{ $Schedule_List->SCHEDULEID }}" selected>Phase {{$i}}</option>
                  @else
                        <option value="{{ $Schedule_List->SCHEDULEID }}">Phase {{$i}}</option>
                  @endif
                  
                   @php  $i++;  @endphp
                  @endforeach                 

                  @if ($errors->has('ScheduleList'))
                  <span class="help-block">
                      <strong class="user">{{ $errors->first('ScheduleList') }}</strong>
                  </span>
                  @endif
                  
                  </select>
                   <!--PHASE LIST DROPDOWN ENDS-->

                  <input type="submit" value="Filter" class="btn btn-primary">
                  <input type="reset" value="Reset Filter" name="Cancel" class="btn">
              </form>
               @endif
            </div> 
            <div class="col">
              @if($stcode != '') <h4> State @php $statelist = getstatebystatecode($stcode); @endphp {{$statelist->ST_NAME}} &nbsp;&nbsp; @else  @endif 
              @if($phase != '')  States In Election Phase &nbsp; {{$phase}}</h4>  @else  @endif
            </div>
      </div>
</div>

 <!--FILTER ENDS HERE-->
   
<section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> List Of Nominations</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp; 
              <a href="{{url('/eci/EciNominationAcPhaseFilterExcel')}}/{{base64_encode($stcode)}}/{{base64_encode($phase)}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
              </p>
              </div>
            </div>
      </div>
   
 <div class="card-body">  
    <table class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
         <tr>
        
          <th>AC No</th> 
          <th>AC Name</th> 
          <th>Total Nomination</th> 
        
        </tr>
        </thead>
        <tbody>
      
         @forelse ($EciNominationAcPhaseFilter as $key=>$listdata)
          <tr>
           
            <td><a href="{{url('/eci/EciNominationAcWiseReport')}}/{{base64_encode($listdata->ST_CODE)}}/{{base64_encode($listdata->AC_NO)}}">{{ $listdata->AC_NO }}</a></td>
            <td><a href="{{url('/eci/EciNominationAcWiseReport')}}/{{base64_encode($listdata->ST_CODE)}}/{{base64_encode($listdata->AC_NO)}}">{{ $listdata->AC_NAME }}</a></td>
            <td> @if($listdata->totalnomination =='' )     0  @else  {{ $listdata->totalnomination }} @endif</td>
           
          </tr>
      
           @empty
                <tr>
                  <td colspan="4">No Data Found For Nominations</td>                 
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
<script src="{{ asset('theme/vendor/jquery/jquery.min.js') }}"></script>
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
$("#EciNominationAcPhaseFilter").validate({
    rules: {
              ScheduleList: { required: true,number: true},
            },
  messages: { 
                ScheduleList: {
                      required: "Phase required.",
                      number: "Phase should be numbers only.",
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


