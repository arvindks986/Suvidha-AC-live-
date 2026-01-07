@extends('admin.layouts.ac.theme')
@section('title', 'Suvidha')
@section('bradcome', 'Poll Day Create Schedule')
@section('content')
 <?php   $st=getstatebystatecode($ele_details->ST_CODE);  
          $ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO); 
          $url = URL::to("/"); $j=0;
    ?>
 
 
 <main role="main" class="inner cover mb-3">
  <section class="mt-3">
  <div class="container">
<div class="row">
  				
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                <div class="col"> <h4>Poll Day Voting Schedule </h4> </div> 
				<div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b class="bolt">AC Name:</b> 
            <span class="badge badge-info">{{$ac->AC_NAME}}</span>&nbsp;&nbsp;  
            </p></div>
         
                </div>
                </div>
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
    </div>
   	  
       
    <div class="card-border">  
       <form class="form-horizontal" id="election_form" method="post" action="{{url('roac/voting/verify-mainschedule')}}" enctype="multipart/form-data" autocomplete='off'>
  {{csrf_field()}}
		<input type="hidden" name="pd_scheduleid" value="{{$pd_scheduleid}}" id='pd_scheduleid'/>
	   
			<div class="row">
				<div class="col-md-12">
				
				 
           <?php  if(old('stdate')){ $sttime = old('stdate');   } elseif(!empty($list)) { $sttime=$list->start_time; } else {$sttime='';} 
                  if(old('eddate')){ $edtime = old('eddate');   } elseif(!empty($list)) { $edtime=$list->end_time; }  else {$edtime='';}  ?>
					<div class="row d-flex align-items-center ">
						<div class="col">
								<label for="candidate_id" class="col-form-label">Voting Start time (Time 7:00 AM) <span class="errorred">*</span></label> &nbsp; &nbsp;
							<!--	<input name="stdate" type="text" id="stdate" class="form-control" placeholder="Time 7:00 AM" value="{{isset($list)?$list->start_time:old('eddate') }}"> -->
                <input name="stdate" type="text" id="stdate" class="form-control" placeholder="Time 7:00 AM" value="{{date("G:i",strtotime($sttime))}}">                                        
                                @if ($errors->has('stdate'))
				                        <span style="color:red;">{{ $errors->first('stdate') }}</span>
				                   @endif 
								 <span id="errmsg" class="text-danger"></span>	
								</div>	
								
											
		
					<div class="col">
					<label for="affidavit" class="col-form-label">Voting End time (Time 6:00 PM)<span class="errorred">*</span> </label>
					 <!-- <input name="eddate" type="text" id="eddate" class="form-control" placeholder="Time 5:00 PM" value="{{isset($list)?$list->end_time:old('eddate') }}">-->
          
          <input name="eddate" type="text" id="eddate" class="form-control" placeholder="Date &amp; time" value="{{date("G:i",strtotime($edtime))}}">                                              
                                @if ($errors->has('eddate'))
                        				<span style="color:red;">{{ $errors->first('eddate') }}</span>
                   					@endif 
								 <span id="errmsg1" class="text-danger"></span>	
								</div>	 
<div class="col-md-1 p-0 m-0">

<button type="submit" id="saverec" class="btn btn-primary custombtn">Save</button></div>
			
			</div>
					
					</div>
					</div>
					 
			 <input type="hidden" id="base_url" value="<?php echo url('/'); ?>">
		</form>   
  
        

    </div>
    </div>
  
  
  </div>
  </div>
  </section>
   
  </main>
 
@endsection
@section('script')
<script type="text/javascript">
   $(document).ready(function(){  

        var token = $('meta[name="csrf-token"]').attr('content');
        var base_url = $("#base_url").val();
        var d = new Date();
        var d1 = new Date();
    
     $('#stdate').datetimepicker({
                 format: ' hh A',
                 useCurrent: false,
              
         });
     $("#eddate").datetimepicker({
             format: ' hh A',
             useCurrent: false,
        });
    // $("#stdate").on("dp.change", function (e) {
    //         $('#eddate').data("DateTimePicker").minDate(e.date);
           
    //    });
    // $("#stdate").on("dp.change", function (e) {
           
    //        $('#eddate').data("DateTimePicker").maxDate(e.date);
    //    });
    // $("#eddate").on("dp.change", function (e) {
    //         $('#stdate').data("DateTimePicker").maxDate(e.date);
    //     }); 
    $('#saverec').click(function(){
		var stdate = $('input[name="stdate"]').val();
		var eddate = $('input[name="eddate"]').val();
		 
		error = false;
		if(stdate == ''){
            $('#errmsg').html('');
			     $('#errmsg').html('Please enter start  time');
			     $( "input[name='stdate']" ).focus();
			     error = true;
		}
        if(eddate == ''){
              $('#errmsg1').html('');
			        $('#errmsg1').html('Please enter End  time');
			        $( "input[name='eddate']" ).focus();
			         error = true;
		}
     if(error){
      return false;
    }
       }) // 
    }) // end function
         
</script>
 

@endsection 