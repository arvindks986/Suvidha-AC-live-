  @extends('layouts.theme')
  @section('title', 'Nomination')
  @section('content')
  <style type="text/css">
    .error{
      font-size: 12px; 
      color: red;
    }
  </style>
    <link rel="stylesheet" href="{{ asset('admintheme/css/nomination.css') }}" id="theme-stylesheet">
    <link rel="stylesheet" href="{{ asset('admintheme/css/jquery-ui.css') }}" id="theme-stylesheet">
	<link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom-profile.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom-dark.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/font-awesome.min.css') }} " type="text/css">
	
  
  <main role="main" class="inner cover mb-3 mt-3">
	<section>
	@if(count($errors->all())>0)
		  <div class="container">
          <div class="alert alert-danger">
            <ul>
              @foreach($errors->all() as $iterate_error)
              <li><p class="text-left">{!! $iterate_error !!}</p></li>
              @endforeach
            </ul>
          </div>
		  </div>    	
    @endif
		  
	@if (session('flash-message'))
		<div class="container">
			<div class="row">
           @if (session('flash-message'))
           <div class="alert alert-success"> {{session('flash-message') }}</div>
           @endif
		</div>    
    @endif
    </section>	
	
	<main class="pt-3 pb-5 pl-5 pr-5">
	 <div class="container-fluid">	    
		  <div class="card-header d-flex align-items-center">
             <h4>My Nominations</h4> &nbsp;&nbsp;&nbsp;&nbsp;
			  <div style="margin-left: 48.9em;">
				<a href="{{ 'schedule-appointment' }}">
				<span id="app" style="cursor: pointer; background: #bb4292; padding: 3px; border-radius: 2px; color: white;margin-left: 15px" onclick="return showApproved(1);">Submitted</span>
				</a>
				&nbsp;&nbsp;&nbsp;
				<a href="{{ 'my-nominations-draft' }}">
				<span id="inc" style="cursor: pointer; background: gray; padding: 3px; border-radius: 2px; color: white; margin-left: 15px; opacity: .5;" onclick="return showApproved(0);">Draft</span>
				</a>
			</div>
	   </div>	
		 
	   <div class="card">
		 
		 <?php $i=0; if(count($results)>0){ ?>
		 <div class="card-body p-0">
			<div class="nomin-list">
			  <div class="owl-carousel owl-theme">
			  
				<?php $i=0; if(count($results)>0){ //  or $result['prescrutiny_status'] == 2 ?>
                @foreach($results as $result)
				@if($result['is_finalize'] == 1)
				<?php 
					$ddd= 'NA';
					$exp = '';
					$cng = 0;
					$nid=encrypt_string($result['id']);
					if(isset($result['appoinment_scheduled_datetime']) && ($result['appoinment_scheduled_datetime']!=='0000-00-00 00:00:00')){
					$exp = explode(" ", $result['appoinment_scheduled_datetime']);	
						$time = strtotime($result['appoinment_scheduled_datetime']);
						$ddd =  date("d M Y", $time).' '.$exp[1];
					}
					$send='';
					if($result['step']==1 || empty($result['step'])){
						$send=$result['edit_href'];
					}
					if($result['step']==2){
						$send='apply-nomination-step-3?nid='.$nid;
					}
					if($result['step']==3){
						$send='apply-nomination-step-4?nid='.$nid;
					}
					if($result['step']==4){
						$send='apply-nomination-step-5?nid='.$nid;
					}
					if($result['step']==5){
						$send='apply-nomination-finalize?nid='.$nid;
					}
					$scrutiny='';	$yes=0; $background=''; $raise='';
					
					if($result['is_appoinment_scheduled']==1 && $result['appoinment_status']!=2){
						$scrutiny='Appointment Scheduled';
						$background="background:pink";
						$yes=1;
					}
					if($result['is_appoinment_scheduled']==1 && $result['appoinment_status']==2){
						$scrutiny='Appointment Cancelled';
						$yes=3;
						$background="background:red;color: white; padding: 2px;";
					}
					if($result['is_appoinment_scheduled']==2 && $result['is_appoinment_scheduled']!=2){ 
						$scrutiny='Pending';
					}
					if($result['prescrutiny_status']==2){ 
						$raise='Query Raised';
						$background="background:yellow;";
						$yes=2;
					}
					if($result['prescrutiny_status']==1 && $yes==0){ 
						$background="background:green;color:white;padding:2px;";
				    }
					
					if($result['is_appoinment_scheduled']==1 && $result['appoinment_status']=='' && $result['prescrutiny_status']==2){
						$raise='Appointment Scheduled with query';
						$background="background:pink";
						$yes=2;
						$cng=1;
					}
					/////////////////////////
					
					
					$n=0;
					$st='';
					if($result['is_apply_prescrutiny']==1  && $result['prescrutiny_status']!=1  && $result['prescrutiny_status']!=2 
					&& $result['is_appoinment_scheduled']!=1 && $result['appoinment_status']!=1 && $result['appoinment_status']!=2   ){ 
					$st="Pre scrutiny submitted";	
					$n=1;
					}
					if($result['is_apply_prescrutiny']==1  && $result['prescrutiny_status']==1  && $result['prescrutiny_status']!=2 
					&& $result['is_appoinment_scheduled']!=1 && $result['appoinment_status']!=1 && $result['appoinment_status']!=2   ){ 
					$st="Pre scrutiny cleared";	
					$n=2;
					}
					if($result['is_apply_prescrutiny']==1  && $result['prescrutiny_status']!=1  && $result['prescrutiny_status']==2 
					&& $result['is_appoinment_scheduled']!=1 && $result['appoinment_status']!=1 && $result['appoinment_status']!=2   ){ 
					$st="Pre scrutiny Defect";	
					$n=3;
					}
					if($result['is_apply_prescrutiny']==1 && $result['is_appoinment_scheduled']==1 && $result['appoinment_status']!=1 && $result['appoinment_status']!=2   ){ 
					$st="Appointment Scheduled";	
					$n=4;
					}
					if($result['is_apply_prescrutiny']==1 && $result['is_appoinment_scheduled']==1 && $result['appoinment_status']==1   ){ 
					$st="Appointment cleared";	
					$n=5;
					}
					if($result['is_apply_prescrutiny']==1 && $result['is_appoinment_scheduled']==1 && $result['appoinment_status']==2   ){ 
					$st="Appointment not cleared";	
					$n=6;
					}
					if($st==''){
						$st='Not Submitted';
						$n=7;
					}
					?>
			  
				   <div class="item" style="@if($yes==1) opacity: 1;  @endif">
				    <div class="appnt-detail list-detail">
					<span class="semi-circle-left"></span> 
					 <span class="semi-circle-right"></span> 
					<h4 class="text-center">{{$result['nomination_no']}}</h4>
					<?php 
					if(!empty($result['prescrutiny_date'])){
					$exp = '';
					$exp = explode(" ", $result['prescrutiny_date']);
					$yrdata= strtotime($result['prescrutiny_date']);
					echo date('d M Y', $yrdata).' '.$exp[1];
					}
					?>
					
					
					<ul>
						<li><strong>Name</strong> <span>{{$result['name']}}</span></li>
						<li><strong>AC No. &amp; Name</strong> <span>{{$result['ac_name']}}</span></li>
						<li><strong>Status</strong> 
						<span>
							{{$st}}
						</span>
						</li>
						<!--<li><strong>Appointment Date</strong> <span>{{$ddd}}</span></li>-->
						<li><strong>Election</strong> <span>{{$result['election_name']}}</span></li>
						<li><strong>Party</strong> <span>{{$result['party_name']}}</span></li>
					</ul> 
					<div class="row p-3">
					  <div class="col-md-4 col-12"><strong>Action</strong></div>  
					  <div class="col-md-8 col-12 text-right">
						 <div class="apt-btn">
						  @if($result['is_finalize'] == 0)
							<a href="{{$send}}" class="btn sm-btn dark-pink-btn">Edit</a>
							@else	
						    <a href="{{$result['view_href']}}" class="btn sm-btn dark-pink-btn">view</a>  
						   @endif
						  <a href="{{$result['download_href']}}" class="btn sm-btn dark-purple-btn">Download</a>  
						</div> 
					  </div>  
					</div>
					<div class="col-md-6 col-12" id="<?php echo $i; ?>" style="display:none;">{{$result['prescrutiny_comment']}}</div> 	
					@if($n==2 or $n==3)		
					<div class="nomin-foot">
					<div class="custom-control custom-checkbox">	
					
					<input type="checkbox"  class="custom-control-input cls_{{$result['id']}}" name="is_scrutiny_completed" value="{{$result['id']}}" id="{{$result['id']}}" onclick="return setRadio('<?php echo $result['id']; ?>', '<?php echo $result['st_code']; ?>', '<?php echo $result['ac_no']; ?>');">
					     <label class="custom-control-label" for="customCheck-01">BOOK APPOINTMENT</label>
					  </div> 
					</div>
					@elseif($n==0)
					<div class="nomin-foot">
					<div class="custom-control custom-checkbox">	
					{{$st}}
					<!--
					<input type="checkbox"  class="custom-control-input cls_{{$result['id']}}" name="is_scrutiny_completed" value="{{$result['id']}}" id="{{$result['id']}}" onclick="return setRadio('<?php echo $result['id']; ?>', '<?php echo $result['st_code']; ?>', '<?php echo $result['ac_no']; ?>');">
					     <label class="custom-control-label" for="customCheck-01">BOOK APPOINTMENT</label>
						--> 
						 
						 
					  </div> 
					</div>
					@else
					<div class="nomin-foot"> 
					<div class="custom-control"> {{$st}}
					<!--<a style="color:white;" href="{!! url('nomination/confirm-schedule-appointment') !!}?query=eyJpdiI6InpaYVpGeG5IajV0c2syS1RVb1B1K3c9PSIsInZhbHVlIjoiU3RKVnRMOUgyeTltRXU3OVFzd2Q3QT09IiwibWFjIjoiNWVjZjY5Y2ZlMWU1OTBjZTJmNWQyYjJlMTI3OGVjY2RkNDhlMmJmYmZmMjgzOGQyODQ2MWFjMzQ3ZjcyMzRjMyJ9&id=<?php echo $result['id']; ?>&data=eyJpdiI6InpaYVpGeG5IajV0c2syS1RVb1B1K3c9PSIsInZhbHVlIjoiU3RKVnRMOUgyeTltRXU3OVFzd2Q3QT09IiwibWFjIjoiNWVjZjY5Y2ZlMWU1OTBjZTJmNWQyYjJlMTI3OGVjY2RkNDhlMmJmYmZmMjgzOGQyODQ2MWFjMzQ3ZjcyMzRjMyJ9">SEE DETAILS</a> -->
			      </div> 
					</div>
					@endif
				   </div><!-- End Of appnt-detail Div --> 
				</div>	
				
				  <?php $i++;  ?>
					@endif
                    @endforeach   
				   <?php  }   ?>					
					 				
				<?php ?>				
			  </div>
		    </div>
		 </div>  
		 <?php } if( $i > 0 ){ ?>
		 <div class="card-footer">
		   <div class="apt-btn text-right">
			 <a href="#" class="btn btn-lg font-big dark-purple-btn"  onclick="return bookAnAppointment();">BOOK SELECTED APPOINTMENT</a>  
			</div> 
			<div id="queryshow"> </div>
		 </div> 
        <?php } ?>		 
		 @if($i==0)
		 <div style="color: red; padding: 20px; position: absolute; margin-top: 48px;">Nomination not found</div>
		 @endif
	   </div>
	 </div>
   </main> 
   <!-- Pre Scrutiny Submitted from Preview Page -->	
		<div class="modal fade modal-confirm" id="is_sub_id">
		<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
		  <div class="modal-content">
		   <div class="pop-header pt-3 pb-1">
			  <div class="animte-tick"><span>&#10003;</span></div>	
			  <h5 class="modal-title"></h5>
			<div class="header-caption">
			  <p>Your online nomination application has been successfully submitted for online pre-scrutiny </p>	
			  <ul class="list-inline">
				<li class="list-inline-item mr-4"></li>
			  </ul>
			</div>		
			</div>
			 <div class="confirm-footer">
			  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">Ok</button>
			</div>
		  </div>
		</div>
		</div>
   
   
   

<form name="app" id="election_form" method="POST"  action="{{url('/nomination/schedule-appointment/post') }}" autocomplete='off' enctype="x-www-urlencoded">
{{ csrf_field() }}
<input name="st_code" id="st_code" type="hidden" value="U05">
<input name="ac" id="ac" type="hidden" value="1">
<input name="reason" type="hidden" value="reason">
<input name="name" type="hidden" value="name">
<input name="email" type="hidden" value="email">
<input name="mobile" type="hidden" value="9988776655">
<input name="date" type="hidden" value="2020-04-11">
<input name="time" type="hidden" value="10 to 11">
<input type="hidden" name="selectRadioButton" id="selectRadioButton">
</form>
</main>
@endsection
@section('script')
    <script type="text/javascript" src="{{ asset('admintheme/js/jquery-ui.js') }}"></script>
	<script src="{{ asset('appoinment/js/jQuery.min.v3.4.1.js') }}" type="text/javascript"></script>
	<script src="{{ asset('appoinment/js/week-scheduale.js') }}" type="text/javascript"></script>
	<script src="{{ asset('appoinment/js/bootstrap.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('appoinment/js/owl.carousel.js') }}"></script>  
<script>
	
	<?php if(session('is_sub')!==null){ 
	if(session('is_sub') == 'yes'){ ?>
	$('#is_sub_id').modal('show');
	<?php } } ?> 	
	
	
	  function hello(id){
		 $("#queryshow").toggle();
		 $.get('prescootiny/'+id, {}, function(data){  
		   $("#queryshow").html(data);
		});
	  }	
	
	 $(document).ready(function() {
              var owl = $('.owl-carousel');
              owl.owlCarousel({
                margin: 2,
                nav: true,
                loop: true,
                responsive: {
                  0: {
                    items: 1
                  },
                  600: {
                    items: 2
                  },
                  1000: {
                    items: itmCount
                  }
                }
              });
		  });
      	
	  
		 var itmCount = $('.item').length;
		
		if(itmCount == 1){
			$('.list-detail').addClass('one-appoint');
		}else if(itmCount == 2){
		   $('.list-detail').removeClass('one-appoint').addClass('two-appoint');
		}else if(itmCount == 3){
			$('.list-detail').removeClass('one-appoint, two-appoint').addClass('three-appoint');
		}else if(itmCount >= 4){
			$('.list-detail').removeClass('one-appoint, two-appoint, three-appoint');
			itmCount = 4;
		}
	
	
    function showQuery(id){
	$("#"+id).toggle();  
   }
   function setRadio(id, state, ac){ 
   
	$.ajax({
		type: "POST",
		url: "<?php echo url('/'); ?>/nomination/get-nomination-start-end-date", 
		data: {
			"_token": "{{ csrf_token() }}",
			"sId": state,
			"ac": ac
			},
		dataType: "html",
		success: function(msg){ 
		  if(msg==0){
			$(".cls_"+id).prop('checked', false);  
			alert("Nomination yet not started");
			return false;	
		  } 
		},
		error: function(error){
			console.log("Error"+error);
			console.log(error.responseText);				
			var obj =  $.parseJSON(error.responseText);
		}
	});
	
	$("#selectRadioButton").val(id);  
	$("#st_code").val(state);  
	$("#ac").val(ac);  
	
	
	
   }
   function bookAnAppointment(){ 
   
   
	var selectedTab = $("input[name=is_scrutiny_completed]:checked").map(
    function () {return this.value;}).get().join(","); 
	
	
	if(selectedTab==''){
	 alert("Please select nomination");	
	 return false;
	}
	
	
	var id 		=  $("#selectRadioButton").val();  
	var st_code =  $("#st_code").val();  
	var ac 		=  $("#ac").val();  
	
	if(id==''){
		alert("Please select nomination");
		return false;
	}
	$.ajax({
		type: "POST",
		url: "<?php echo url('/'); ?>/nomination/get-nomination-start-end-date", 
		data: {
			"_token": "{{ csrf_token() }}",
			"sId": st_code,
			"ac": ac
			},
		dataType: "html",
		success: function(msg){ 
		  if(msg==0){
			alert("Nomination yet not started");
			return false;	
		  } else {
			window.location.href = "{!! url('nomination/confirm-schedule-appointment') !!}?query=eyJpdiI6InpaYVpGeG5IajV0c2syS1RVb1B1K3c9PSIsInZhbHVlIjoiU3RKVnRMOUgyeTltRXU3OVFzd2Q3QT09IiwibWFjIjoiNWVjZjY5Y2ZlMWU1OTBjZTJmNWQyYjJlMTI3OGVjY2RkNDhlMmJmYmZmMjgzOGQyODQ2MWFjMzQ3ZjcyMzRjMyJ9&id="+selectedTab+'&data=eyJpdiI6InpaYVpGeG5IajV0c2syS1RVb1B1K3c9PSIsInZhbHVlIjoiU3RKVnRMOUgyeTltRXU3OVFzd2Q3QT09IiwibWFjIjoiNWVjZjY5Y2ZlMWU1OTBjZTJmNWQyYjJlMTI3OGVjY2RkNDhlMmJmYmZmMjgzOGQyODQ2MWFjMzQ3ZjcyMzRjMyJ9';  
		  }
		},
		error: function(error){
			console.log("Error"+error);
			console.log(error.responseText);				
			var obj =  $.parseJSON(error.responseText);
		}
	});
  }
  
  
  
  
  function RebookAnAppointment(id, st_code, ac){ 
	if(id==''){
		alert("Please select nomination");
		return false;
	}
	$.ajax({
		type: "POST",
		url: "<?php echo url('/'); ?>/nomination/get-nomination-start-end-date", 
		data: {
			"_token": "{{ csrf_token() }}",
			"sId": st_code,
			"ac": ac
			},
		dataType: "html",
		success: function(msg){ 
		  if(msg==0){
			alert("Nomination yet not started");
			return false;	
		  } else {
			var r = confirm("Are you sure to rescheduled appointment?");
			if(r == true){ 
			 window.location.href = "{!! url('nomination/confirm-schedule-appointment') !!}?query=eyJpdiI6InpaYVpGeG5IajV0c2syS1RVb1B1K3c9PSIsInZhbHVlIjoiU3RKVnRMOUgyeTltRXU3OVFzd2Q3QT09IiwibWFjIjoiNWVjZjY5Y2ZlMWU1OTBjZTJmNWQyYjJlMTI3OGVjY2RkNDhlMmJmYmZmMjgzOGQyODQ2MWFjMzQ3ZjcyMzRjMyJ9&id="+id+'&data=eyJpdiI6InpaYVpGeG5IajV0c2syS1RVb1B1K3c9PSIsInZhbHVlIjoiU3RKVnRMOUgyeTltRXU3OVFzd2Q3QT09IiwibWFjIjoiNWVjZjY5Y2ZlMWU1OTBjZTJmNWQyYjJlMTI3OGVjY2RkNDhlMmJmYmZmMjgzOGQyODQ2MWFjMzQ3ZjcyMzRjMyJ9';
			}
		  }
		},
		error: function(error){
			console.log("Error"+error);
			console.log(error.responseText);				
			var obj =  $.parseJSON(error.responseText);
		}
	  });
    }
	

    $(document).ready(function(){  
     if($('#breadcrumb').length){
       var breadcrumb = '';
       $.each({!! json_encode($breadcrumbs) !!},function(index, object){
        breadcrumb += "<li><a href='"+object.href+"'>"+object.name+"</a></li>";
       });
       $('#breadcrumb').html(breadcrumb);
      }
    });

  $(document).ready(function(e){
      let scanner = '';
      $('#open_webcam').click(function(e){
        $('.parent_qr_code').removeClass("display_none");
        scanner = new Instascan.Scanner({ 
          backgroundScan: false,
          video: document.getElementById('preview') 
        });
        scanner.addListener('scan', function (content) {
          window.location.href = "{!! url('nomination/detail') !!}"+'/'+content;
        });

        Instascan.Camera.getCameras().then(function (cameras) {
          if (cameras.length > 0) {
            scanner.start(cameras[0]);
          } else {
            console.error('No cameras found.');
          }
        }).catch(function (e) {
          console.error(e);
        });
      });

      $('#close_webcam').click(function(e){
        scanner.stop().then(function () {

        });
        $('.parent_qr_code').addClass("display_none");
      });

     });
</script>
@endsection