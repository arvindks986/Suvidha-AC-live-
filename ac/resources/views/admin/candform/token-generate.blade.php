@extends('admin.layouts.ac.theme')
@section('title', 'Candidate & Counting')
@section('bradcome', 'Counting Users Creation')
@section('content')
 <?php  $url = URL::to("/");    ?>
 
<main role="main" class="inner cover mb-3">
 <section>
  <div class="container mt-5">
  <div class="row">
  @if(Session::has('success_mes'))
      <div class="alert alert-success mb-3"><strong> {{ nl2br(Session::get('success_mes')) }}</strong> </div>
    @endif 
     @if(Session::has('error_mes'))
     <div class="alert alert-danger mb-3"><strong> {{ nl2br(Session::get('error_mes')) }}</strong></div>
    @endif 
     
      @if (count($errors->all()) > 0)
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger mb-3"><strong> {{ $error }}</strong></div>
                        
                    @endforeach
              @endif      
    

  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
					   <div class="col"> 
							<h4 class="mr-auto">Candidate Token Management</h4>  
					   </div> 
					   
						<div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> 
							<span class="badge badge-info">{{$st_name}}</span> &nbsp;&nbsp; <b class="bolt">AC Name:</b> 
							<span class="badge badge-info">{{$ac_name}}</span>&nbsp;&nbsp;  </p>
						</div>
                 </div>
                </div>
       
    <div class="card-body"> 
   <div class="row mb-3">
	<div class="col">
	 
	  
       
	     <input type="button" id="btn" class="btn btn-success submit-button ml-auto" value="Add New Toten">
      
   
 </div></div>
             

      <div id="Create"  class="createForm" @if(count($errors->all()) <= 0) style="display:none" @endif>
      <form class="form-horizontal" id="election_form" method="POST"  action="{{url('roac/verify-token') }}" autocomplete='off' enctype="x-www-urlencoded">
                {{ csrf_field() }}
               
                 <div class="form-group row">
                        <label class="col-sm-4 form-control-label"> Form Number</label> 
                         <div class="col-sm-8">
                            <input type="text"  name="form_number" id="form_number" value="{{old('form_number')}}" class="form-control">
                                @if ($errors->has('form_number'))
                                      <span class="text-danger">{{ $errors->first('form_number') }}</span>
                                @endif 
                               <span class="text-danger" id="err"></span>
                        </div>
                  </div>
                  
                  <div class="form-group row">
                          <label class="col-sm-4 form-control-label">Nomination Paper Serial Number<sup>*</sup></label> 
                          <div class="col-sm-8">
                           <input type="text"   name="nomination_papersrno" id="nomination_papersrno" value="{{old('nomination_papersrno')}}" class="form-control">
                            
                            @if ($errors->has('nomination_papersrno'))
                                        <span class="text-danger">{{ $errors->first('nomination_papersrno') }}</span>
                                  @endif 
                             <span class="text-danger" id="err1"></span>
                          </div>
                    </div>

                  <div class="form-group row">
                          <label class="col-sm-4 form-control-label"> Time  </label> 
                           <div class="col-sm-8">
                           <input type="text"  name="rosubmit_time" id="rosubmit_time" value="{{old('rosubmit_time')}}" class="form-control">
                            
                            @if ($errors->has('rosubmit_time'))
                                        <span class="text-danger">{{ $errors->first('rosubmit_time') }}</span>
                                  @endif 
                            <span class="text-danger" id="err2"></span>
                          </div>
                    </div>

                     <div class="form-group row">
                         <label class="col-sm-4 form-control-label"> Date <sup>*</sup></label> 
                         <div class="col-sm-8">
                           <input type="text"  name="rosubmit_date" id="rosubmit_date" value="" class="form-control">
                            
                            @if ($errors->has('rosubmit_date'))
                                        <span class="text-danger">{{ $errors->first('rosubmit_date') }}</span>
                                  @endif 
                            <span class="text-danger" id="err3"></span>
                         </div>
                    </div>
            
                      <div class="form-group row">
                          <label class="col-sm-4 form-control-label">Paper Submited By (Candidate/ Proposer) <sup>*</sup></label>
                          <div class="col-sm-8"> 
                           <input type="text"    name="nomination_submittedby" id="nomination_submittedby" value="{{old('nomination_submittedby')}}" class="form-control">
                            
                            @if ($errors->has('nomination_submittedby'))
                                        <span class="text-danger">{{ $errors->first('nomination_submittedby') }}</span>
                                  @endif 
                             <span class="text-danger" id="err4"></span>  
                          </div>
                    </div>
                   <div class="form-group row">
                  <div class="col-sm-12 text-right">       
                      <input type="submit" value="Submit" placeholder="" class="btn btn-success submit-button">
				</div>
               </div>
                
                     
    </form>
     </div>  

   @if($results)
  <table   class="example table table-striped table-bordered mt-5" style="width:100%">
        <thead><tr>
                <th>Sr. No</th>
                <th>Form Number</th>
                <th>Nomination Paper Serial No.</th> 
                <th>Time</th>
                <th>Date</th>
                <th>Submit By</th> 
                 
                 
                </tr>
        </thead>
      <tbody>
          <?php  $j=0; $url = URL::to("/"); ?>
             @foreach($results as $list)  
            <?php $j++;    ?> 
              <tr><td>{{$j}}</td> 
                  <td>{{$list['form_number']}} </td> 
                  <td>{{$list['nomination_papersrno']}} </td> 
                  <td>{{$list['rosubmit_time']}} </td> 
                  <td>{{$list['rosubmit_date']}} </td> 
                  <td>{{$list['nomination_submittedby']}} </td> 

                   
              
            </td>
              </tr>  
            @endforeach  
      </tbody> 
  </table> 
   @else
              
             
 </div>

     <div class="norecords"><i class="fa fa-ban"></i><h4>No Records Added</h4></div>  
					
        @endif
	
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
        <h4 class="modal-title">Updating Counting User </h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
       <form class="form-horizontal" id="election_form1" method="POST"  action="{{url('roac/counting/update-counting-user') }}"   enctype="x-www-urlencoded">
                {{ csrf_field() }}
                 <input type="hidden"  name="off_id" id="off_id" value="" class="">
                <div class="form-group row">
                        <label class="col-sm-5 form-control-label"> Users Name<sup>*</sup></label> 
                         <div class="col-sm-7">
                            <input type="text"  name="name1" id="name1" value="{{old('name1')}}" class="form-control">
                                @if ($errors->has('name1'))
                                      <span class="text-danger">{{ $errors->first('name1') }}</span>
                                @endif 
                               <span class="text-danger" id="perr"></span>
                        </div>
                  </div>
                  
                  <div class="form-group row">
                          <label class="col-sm-5 form-control-label"> Users Mobile Number <sup>*</sup></label> 
                          <div class="col-sm-7">
                           <input type="text" maxlength="10"  name="Phone_no1" id="Phone_no1" value="{{old('Phone_no1')}}" class="form-control">
                            
                            @if ($errors->has('Phone_no1'))
                                        <span class="text-danger">{{ $errors->first('Phone_no1') }}</span>
                                  @endif 
                             <span class="text-danger" id="perr1"></span>
                          </div>
                    </div>

                  <div class="form-group row">
                          <label class="col-sm-5 form-control-label"> Users E-Mail  </label> 
                           <div class="col-sm-7">
                           <input type="text"  name="email1" id="email1" value="{{old('email1')}}" class="form-control">
                            
                            @if ($errors->has('email1'))
                                        <span class="text-danger">{{ $errors->first('email1') }}</span>
                                  @endif 
                            <span class="text-danger" id="perr2"></span>
                          </div>
                    </div>

                     <div class="form-group row">
                         <label class="col-sm-5 form-control-label"> Password <sup>*</sup></label> 
                         <div class="col-sm-7">
                           <input type="password"  name="password1" id="password1" value="" class="form-control">
                            
                            @if ($errors->has('password1'))
                                        <span class="text-danger">{{ $errors->first('password1') }}</span>
                                  @endif 
                            <span class="text-danger" id="perr3"></span>
                         </div>
                    </div>
            
                      <div class="form-group row">
                          <label class="col-sm-5 form-control-label"> Login Pin <sup>*</sup></label>
                          <div class="col-sm-7"> 
                           <input type="text" maxlength="4"  name="two_step_pin1" id="two_step_pin1" value="{{old('two_step_pin1')}}" class="form-control">
                            
                            @if ($errors->has('two_step_pin1'))
                                        <span class="text-danger">{{ $errors->first('two_step_pin1') }}</span>
                                  @endif 
                             <span class="text-danger" id="perr4"></span>  
                          </div>
                    </div>
                 
                  <div class="col-sm-7 float-right">       
                      <input type="submit" value="Update" placeholder="" class="btn btn-primary">

                   
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
@endsection
 @section('script')
<script type="text/javascript">
   
   $(document).ready(function () {  
    $(document).on("click", ".userpopup", function () {

       id = $(this).attr('data-id');
       name = $(this).attr('data-name');
       Phone_no = $(this).attr('data-Phone_no');
       email = $(this).attr('data-email');
       //alert(name);

       $('#name1').val(name);
       $('#Phone_no1').val(Phone_no);
       $('#email1').val(email);
       $('#off_id').val(id);
    });
   $("#btn").click(function () {
        $("#Create").toggle();
    });

    $("#Phone_no").keypress(function (e) {   
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
              $("#err1").html("Digits Only").show().fadeOut("slow");
              return false;
            }
    });
    
    $("#two_step_pin").keypress(function (e) {   
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
              $("#err4").html("Digits Only").show().fadeOut("slow");
              return false;
            }
    });
     $("#Phone_no1").keypress(function (e) {   
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
              $("#perr1").html("Digits Only").show().fadeOut("slow");
              return false;
            }
    });
    
    $("#two_step_pin1").keypress(function (e) {   
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
              $("#perr4").html("Digits Only").show().fadeOut("slow");
              return false;
            }
    });
  $("#election_form").submit(function(){
    var is_error = false;
     
      if($('#election_form #name').val()==''){
            $('#election_form #name').next('.text-danger').text("please enter users name.").show();
            is_error = true;
          }
       if($('#election_form #Phone_no').val()==''){
            $('#election_form #Phone_no').next('.text-danger').text("please enter mobile number.").show();
            is_error = true;
          }
        if(!$('#election_form #Phone_no').val().match('[0-9]{10}'))  {
              $('#election_form #Phone_no').next('.text-danger').text("please enter 10 digit mobile.").show();
              is_error = true;
            } 
        // if($('#election_form #email').val()==''){
        //     $('#election_form #email').next('.text-danger').text("please enter e-mail.").show();
        //     is_error = true;
        //   }
        if($('#election_form #email').val()!=''){
         if(IsEmail($('#election_form #email').val())==false){
            $('#election_form #email').next('.text-danger').text("please enter valid e-mail.").show();
            is_error = true;
          }
        }
        if($('#election_form #password').val()==''){
            $('#election_form #password').next('.text-danger').text("please enter valid password.").show();
            is_error = true;
          }
         // if(!$('#election_form #password').val().match('{8}'))  {
         //      $('#election_form #password').next('.text-danger').text("please enter 8 charecter password.").show();
         //      is_error = true;
         //    } 
        if($('#election_form #two_step_pin').val()==''){
            $('#election_form #two_step_pin').next('.text-danger').text("please enter two step pin.").show();
            is_error = true;
          }
        if(!$('#election_form #two_step_pin').val().match('[0-9]{4}'))  {
              $('#election_form #two_step_pin').next('.text-danger').text("please enter 4 digit two step pin.").show();
              is_error = true;
            }  
      if(is_error){
          return false;
          }
    });

  $("#election_form1").submit(function(){
    var is_error = false;
     
      if($('#election_form1 #name1').val()==''){
            $('#election_form1 #name1').next('.text-danger').text("please enter users name.").show();
            is_error = true;
          }
       if($('#election_form1 #Phone_no1').val()==''){
            $('#election_form1 #Phone_no1').next('.text-danger').text("please enter mobile number.").show();
            is_error = true;
          }
        if(!$('#election_form1 #Phone_no1').val().match('[0-9]{10}'))  {
              $('#election_form1 #Phone_no1').next('.text-danger').text("please enter 10 digit mobile.").show();
              is_error = true;
            } 
        // if($('#election_form1 #email1').val()==''){
        //     $('#election_form1 #email1').next('.text-danger').text("please enter e-mail.").show();
        //     is_error = true;
        //   }
        if($('#election_form1 #email1').val()!=''){
         if(IsEmail($('#election_form1 #email1').val())==false){
            $('#election_form1 #email1').next('.text-danger').text("please enter valid e-mail.").show();
            is_error = true;
          }
        }
        if($('#election_form1 #password1').val()==''){
            $('#election_form1 #password1').next('.text-danger').text("please enter valid password.").show();
            is_error = true;
          }
         // if(!$('#election_form #password').val().match('{8}'))  {
         //      $('#election_form #password').next('.text-danger').text("please enter 8 charecter password.").show();
         //      is_error = true;
         //    } 
        if($('#election_form1 #two_step_pin1').val()==''){
            $('#election_form1 #two_step_pin1').next('.text-danger').text("please enter two step pin.").show();
            is_error = true;
          }
        if(!$('#election_form1 #two_step_pin1').val().match('[0-9]{4}'))  {
              $('#election_form1 #two_step_pin1').next('.text-danger').text("please enter 4 digit two step pin.").show();
              is_error = true;
            }  
      if(is_error){
          return false;
          }
    });
});
   function IsEmail(email) {
  var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  if(!regex.test(email)) {
    return false;
  }else{
    return true;
  }
}
 </script>
 @endsection