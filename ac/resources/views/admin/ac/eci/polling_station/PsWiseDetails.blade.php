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
<style type="text/css">
  .loader {
   position: fixed;
   left: 50%;
   right: 50%;
   border: 16px solid #f3f3f3; /* Light grey */
   border-top: 16px solid #3498db; /* Blue */
   border-radius: 50%;
   width: 120px;
   height: 120px;
   animation: spin 2s linear infinite;
   z-index: 99999;
  }
      @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
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
<span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download Excel" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
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
            <?php $but = explode(':',$button); ?>
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
  
        
    <form class="row"  method="get" action="{{$action}}" id="pswisedataform">


           <!---STATE FILTER-->
          
         <div class="form-group col-md-3"> <label>State </label>
           <select id="state" name="state" class="form-control" onchange ="get_ac_list();">
                <option value="">--- Select State ---</option> 
                <@foreach (getallstate() as $statelist)

                 @if ($state == $statelist->ST_CODE)
                      <option value="{{ $statelist->ST_CODE }}" selected="selected">{{ $statelist->ST_NAME }}</option>
                @else
                      <option value="{{ $statelist->ST_CODE }}">{{ $statelist->ST_NAME  }}</option>
                @endif

                @endforeach
                </select>
                @if ($errors->has('state'))
                    <span class="help-block">
                        <strong>{{ $errors->first('state') }}</strong>
                    </span>
                @endif
         </div>
    
           <!---AC FILTER-->
          <div class="form-group col-md-3"> <label>AC Constituency </label> 
          
            <select id="ac_id" name="ac_id" class="form-control">
              <option value="">--- Select Assembly ---</option> 
            </select>
          </div>

          
         

      <div class="form-group col-md-3">
        <label class="col" for="">&nbsp;</label>
         <input type="submit" value="Submit" class="btn btn-primary">
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
      
            <table id="data_table_table" class="table table-striped table-bordered" style="width:100%"><thead>

      <tr><th colspan="13" class="text-center">{!! $heading_title !!}</th></tr>


       <tr>
        <th>Serial No</th>
          <th>PS No</th>
          <th>PS Name</th> 
          <th>PS Type</th> 
          <th>Electors Male</th> 
          <th>Electors Female</th> 
          <th>Electors Other</th> 
          <th>Electors Total</th> 
          <th>Voter Male</th> 
          <th>Voter Female</th> 
          <th>Voter Other</th> 
          <th>Voter Total</th> 
        <!--   <th>Action</th> -->
         
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

         $TotalElectorMale   +=$listdata->electors_male;
         $TotalElectorFeMale +=$listdata->electors_female;
         $TotalElectorOther  +=$listdata->electors_other;
         $TotalElector       +=$listdata->electors_total;
         $TotalVoterMale     +=$listdata->voter_male;
         $TotalVoterFeMale     +=$listdata->voter_female;
         $TotalVoterOther     +=$listdata->voter_other;
         $TotalVoter          +=$listdata->voter_total;
        

         @endphp


          <tr>
             <td>{{ $count }}</td>
            <td>{{$listdata->PS_NO }}</td>
            <td>{{$listdata->PS_NAME_EN }}</td>
            <td>{{$listdata->PS_TYPE }}</td>
            <td>{{$listdata->electors_male }}</td>
            <td>{{$listdata->electors_female }}</td>
            <td>{{$listdata->electors_other }}</td>
            <td>{{$listdata->electors_total }}</td>
            <td>{{$listdata->voter_male }}</td>
            <td>{{$listdata->voter_female }}</td>
            <td>{{$listdata->voter_other }}</td>
            <td>{{$listdata->voter_total }}</td>
            <!--<td><button type="button" class="btn btn-primary PsWiseDetailspopup" data-toggle="modal" data-target="#myModal" data-emale="{{$listdata->electors_male }}" data-efemale="{{$listdata->electors_female }}" data-eother="{{$listdata->electors_other }}" data-etotal="{{$listdata->electors_total }}" data-vmale="{{$listdata->voter_male }}" data-vfemale="{{$listdata->voter_female }}" data-vother="{{$listdata->voter_other }}" data-vtotal="{{$listdata->voter_total }}" data-psname="{{$listdata->PS_NAME_EN }}" data-psno="{{$listdata->PS_NO }}">Edit</button></td>-->
         
          </tr>
       
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td class="text-center" colspan="13">No Data Found For Polling Station</td>                 
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
        
       </tbody></table>

         </div><!-- End Of  table responsive -->  
      </div><!-- End Of intra-table Div -->   
        
         
      </div><!-- End Of random-area Div -->
      
    </div><!-- End OF page-contant Div -->
    </div>      
  </div><!-- End Of parent-wrap Div -->
  </div> 

@endsection

@section('script')
<!--**********FORM VALIDATION STARTS**********-->
<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>


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
$("#pswisedataform").validate({
    rules: {
              state: { required: true},
              ac_id: { required: true,number:true},
            },
  messages: { 
                state: {
                      required: "State is required.",
                  },
                  ac_id: {
                      required: "AC required.",
                      number: "AC should be numbers only.",
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

//ASSEMBY ONCHAGE FUNCTION STARTS
    function get_ac_list() {
        var default_ac = "<?php echo $ac_id; ?>";
        var state = jQuery('#state').val();
     
        jQuery.ajax({
             //url: "/eci/turnout/get_ac_list/",
			 url: "{!! url('eci/turnout/get_ac_list') !!}",
             type: 'GET',
             data:  "state="+state,
             success: function(response) {
             if(response.status == 200 && response.error==false && response.data!=''){ 

                       var output = [];
                       $.each(response.data, function(key, value)
                       {
                         if(default_ac == value['AC_NO']){
                         output.push('<option value="'+ value['AC_NO'] +'" selected="selected">'+ $.trim(value['AC_NAME']) +'</option>');
                        }else{
                          output.push('<option value="'+ value['AC_NO'] +'">'+ $.trim(value['AC_NAME']) +'</option>');
                        }
                       });
                       $('#ac_id').html(output.join(''));
                   } else {
                       /*alert('Enternal Server Error!');*/
                   }
           }
        });
        
    }

    <?php if(isset($ac_id) && $ac_id){ ?>
      get_ac_list();
    <?php } ?>
//ASSEMBY ONCHANGE FUNCTION ENDS 


</script>
@endsection