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
	<?php $style=""; ?>
	<main class="pt-3 pb-5 pl-5 pr-5" style="margin-bottom: 106px;">
	  <div class="card-header d-flex align-items-center">
             <h4>Draft Nomination</h4> &nbsp;&nbsp;&nbsp;&nbsp;
             <div style="margin-left: 52.66em;">
				<a href="{{ 'schedule-appointment' }}">
				<span id="app" style="cursor: pointer; background: gray; padding: 3px; border-radius: 2px; color: white; margin-left: 15px; opacity: .5;">Submitted</span>
				</a>
				&nbsp;&nbsp;&nbsp;
				<a href="{{ 'my-nominations-draft' }}"> 
				<span id="inc" style="cursor: pointer; background: #bb4292; padding: 3px; border-radius: 2px; color: white;margin-left: 15px;">Draft</span>
				</a>
		</div>
	   </div>	
	
	 <div class="container-fluid" id='zero'>	
		 <?php $i=0; if(count($results)>0){ ?>
		 <div class="card-body p-0">
			<div class="nomin-list">
			  <div class="owl-carousel owl-theme">
				<?php  if(count($results)>0){  ?>
                @foreach($results as $result)
				 @if($result['is_finalize'] == 0)
				<?php 
					$i++;
					$ddd= 'NA';
					$exp = '';
					$nid=encrypt_string($result['id']);
					if(isset($result['updated_at'])){
					$exp = explode(" ", $result['updated_at']);	
						$time = strtotime($result['updated_at']);
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
					$scrutiny='';	
					$st='';
					if($result['is_finalize']==1){
					 $st="Submitted";	
					}
					if($result['is_finalize']==0){
					 $st="Not Submitted";	
					}
					?>		
					<div class="item">
					<div class="appnt-detail list-detail">
					  <span class="semi-circle-left"></span> 
					   <span class="semi-circle-right"></span> 
					   <h4 class="text-center">{{$result['nomination_no']}}</h4>
					   <?php 
					   if(!empty($result['updated_at'])){
						$exp = '';
						$exp = explode(" ", $result['updated_at']);
						$yrdata= strtotime($result['updated_at']);
						echo date('d M Y', $yrdata).' '.$exp[1];
					   }
						?>
					<ul> 
						<li><strong>Name</strong> <span>{{$result['name']}}</span></li>
						<li><strong>AC No. &amp; Name</strong> <span>{{$result['ac_name']}}</span></li>
						<li><strong>Status</strong><span> {{$st}}</span></li>
						<li><strong>Election</strong> <span>{{$result['election_name']}}</span></li>
						<li><strong>Party</strong> <span>{{$result['party_name']}}</span></li>
					</ul> 
					<div class="row p-3">
					  <div class="col-md-4 col-12"><strong>Action</strong></div>  
					  <div class="col-md-8 col-12 text-right">
						 <div class="apt-btn">
						    <a href="{{$send}}" class="btn sm-btn dark-pink-btn">Edit</a>  
							@if($result['step']>=2)
						    <a href="{{$result['download_href']}}" class="btn sm-btn dark-purple-btn">Download</a>  
							@endif
						</div> 
					  </div>  
					</div>
					<div class="col-md-6 col-12" id="<?php echo $i; ?>" style="display:none;">{{$result['prescrutiny_comment']}}</div> 
					<div class="nomin-foot">
					<div class="custom-control custom-checkbox">	
					<?php if($result['is_finalize']==1){ ?>
					<input type="checkbox"  class="custom-control-input cls_{{$result['id']}}" name="is_scrutiny_completed" value="{{$result['id']}}" id="{{$result['id']}}" >
					     <label class="custom-control-label" for="customCheck-01">BOOK APPOINTMENT</label>
					<?php } ?>	 
					<?php if($result['is_finalize']==0){ ?>
					 Not Submitted
					<?php } ?>	
					  </div> 
					</div>
				   </div><!-- End Of appnt-detail Div --> 
				  </div> 
				  <?php $i++;  ?>
					@endif
					@endforeach   
				   <?php  }   ?>
			  </div>
		    </div>
		 </div>  
		 <?php 
		 $style="style='color: red; position: absolute; margin-top: -20em;'";
		 } else { 
		 $style="style='color: red; position: absolute; margin-top: 10px;'";
		} ?>
        
		@if($i==0)
		  <span <?php echo $style; ?>>Nomination not found</span>
		 @endif
	   </div>
	  <div class="col" style="margin-top: 33px; float: right;">
	  <a href="{{url('dashboard-nomination-new')}}" id="" class="btn btn-secondary float-right">Back to dashboard</a>
	</div>  
	 </main> 
   
   
   
   

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
  function showApproved(status){
	if(status==0){ 
		$("#one").hide();
		$("#two").hide();		
		$("#three").hide();		
		$("#inc").css("background", "#bb4292");
		$("#inc").css("opacity", "1");
		$("#Appointment").css("background", "gray");
		$("#Appointment").css("opacity", ".5");	
		$("#app").css("background", "gray");
		$("#app").css("opacity", ".5");
		$("#pre").css("background", "gray");
		$("#pre").css("opacity", ".5");
		$("#zero").show();	
		
	}
	if(status==1){ 
		$("#one").show();
		$("#zero").hide();
		$("#two").hide();		
		$("#three").hide();		
		$("#app").css("background", "#bb4292");
		$("#app").css("opacity", "1");
		$("#Appointment").css("background", "gray");
		$("#Appointment").css("opacity", ".5");	
		$("#inc").css("background", "gray");
		$("#inc").css("opacity", ".5");
		$("#pre").css("background", "gray");
		$("#pre").css("opacity", ".5");
	}	
  }	

  function showQuery(id){
	$("#"+id).toggle();  
  }
  function setRadio(v){
	$("#selectRadioButton").val(v);  
  }	
  function PreScrutiny(){ 
	var id =  $("#selectRadioButton").val();  
	if(id==''){
		alert("Please select nomination");
		return false;
	}
	var r = confirm("Are you sure to apply for pre scrutiny?");
	if(r == true){
	 document.app.submit();
	}
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




<script type="text/javascript">
function checkaff(id){ 
	 var checkaff = $("#affidavit_"+id).val();	
		if(checkaff==''){
		 $("#checkafferror_"+id).html("Please select affidavit");
		  $("#affidavit_"+id).focus();	
		 return false;
		}
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
  
	function uploadPdf(id){	
      $('#form-upload').remove();
      $('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" value="" /></form>');
      $('#form-upload input[name=\'file\']').trigger('click');
      if (typeof timer != 'undefined') {
        clearInterval(timer);
      }
      timer = setInterval(function() {
        if ($('#form-upload input[name=\'file\']').val() != '') {
		    var nid=id;                      
           // alert("<?php echo url('/'); ?>/Nomination/upload-affidavit-final?_token=<?php echo csrf_token(); ?>&nid="+nid);                 	
            clearInterval(timer);
            $.ajax({
            url: "<?php echo url('/'); ?>/nomination/upload-affidavit-final?_token=<?php echo csrf_token(); ?>&nid="+nid,
            type: 'POST',
            dataType: 'json',
            data: new FormData($('#form-upload')[0]),
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
              $('.file-frame').removeClass("file-frame-error");
              $('.file i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
              $('.file').prop('disabled', true);
              $('.text-danger').remove();
            },
            complete: function() {
              $('.file i').replaceWith('<i class="fa fa-upload"></i>');
              $('.file').prop('disabled', false);
            },
            success: function(json) {   console.log(json);
              if(json['success'] == false) {
				$("#checkafferror_"+nid).html("Please upload only PDF format");  
                //$('.file-frame').after("<span class='text-danger'>"+json['errors']+"</span>");
                //$('.file-frame').addClass("file-frame-error");
              }
              if (json['success'] == true) {
				$("#"+nid).show();  
				
				//alert('.affidavit-preview_'+nid+ ' iframe');
				
                $('.file-frame').find('.affidavit_'+nid).val(json['path']);
                $('.affidavit-preview_'+nid+ ' iframe').attr("src","<?php echo url('/'); ?>/"+json['path']);
              }
            },
            error: function(xhr, ajaxOptions, thrownError) {
				console.log( xhr.responseText);
				console.log(xhr);
				
				
              alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
          });
        }
      }, 500);
	  
	}
</script>	
	
	
	
	
<script>

	  function hello(id){
		 $("#queryshow").toggle();
		 $.get('prescootiny/'+id, {}, function(data){  
		   $("#queryshow").html(data);
		});
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