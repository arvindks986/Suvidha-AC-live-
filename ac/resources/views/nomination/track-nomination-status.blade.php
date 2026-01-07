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
	

   <section>
    <div class="container p-0">
      <div class="row">
		
		 <div style="background: lightgray; margin-bottom: 23px; margin-left: 18px; padding: 10px; width: 1135px;">
		<span><a href="apply-nomination-step-2" target="_blank">Apply New Nomination</a></span>&nbsp;|&nbsp;
		<span><a href="my-nominations" >Saved Nominations</a></span>&nbsp;|&nbsp;
		<span><a href="submit-for-pre-scrutiny">Submit for Pre-Scrutiny</a></span>&nbsp;|&nbsp;
		<span><a href="schedule-appointment">Schedule Appointment</a></span>&nbsp;|&nbsp;
		<span><a href="track-nomination-status" style="background: #f0587e;color: white;padding:3px;">Track Nomination Status</a></span>
		 </div>
		
        <div class="col-md-12">
          <div class="card">
           <div class="card-header d-flex align-items-center">
             <h4>Track Nomination Status</h4> 
           </div>
           <div class="card-body">
             <div class="row">
             	
			  <table class="table"  id='three'>
                <thead>
                  <tr>
					<th>Nomination No.</th>
					<th>Nomination Status</th>
                    <th>Name</th>
                    <th>AC No & Name</th>
                    <th>Election</th>
                    <th>Appointment Date</th>
                    <th align="center" class="text-center">Action</th>
                  </tr>
                </thead>
              
				<tbody>
                 <?php $i=0; if(count($results)>0){ //echo "<pre>"; print_r($results); ?>
                  @foreach($results as $result)
				    @if($result['is_finalize'] == 1 && $result['prescrutiny_status'] == 1 && $result['is_appoinment_scheduled'] == 1)
					<?php 
					$ddd= 'NA';
					$exp = '';
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
					$scrutiny='';	$yes=0;
					
					
					if($result['is_appoinment_scheduled']==1){
						$scrutiny='Scheduled';
						$yes=1;
					}					
					if($result['is_appoinment_scheduled']==2){
						$scrutiny='Pending';
					}
					$appoinment_status='NA';
					if($result['appoinment_status']==0 or $result['appoinment_status']==''){
						$appoinment_status='Pending';
						$yes=1;
					}
					if($result['appoinment_status']==1){
						$appoinment_status='Scheduled';
						$yes=1;
					}
					if($result['appoinment_status']==2){
						$appoinment_status='Rejected';
						$yes=1;
					}
					if($result['appoinment_status']==3){
						$appoinment_status='Accepted';
						$yes=1;
					}
					
					?>
					<tr>  
                      <td>{{$result['nomination_no']}}</td>
                      <td>{{$appoinment_status}}</td>
                      <td>{{$result['name']}}</td>
                      <td>{{$result['ac_name']}}</td>
                      <td>{{$result['election_name']}}</td>
					  <td>{{$ddd}}</td>
                      <td align="center"  class="text-center">
                        @if($result['is_finalize'] == 0)
                        <a href="{{$send}}" class="btn button btn-primary">Edit</a>
                        @else
                        <a href="{{$result['view_href']}}"  class="btn button btn-primary">View</a> 
                        @endif
                        <a href="{{$result['download_href']}}" target="_blank" class="btn button btn-primary">Download Application</a>
                       </td>
                      </tr>
					  <tr> 
                      <td colspan="6" id="<?php echo $i; ?>" style="display:none;"> {{$result['prescrutiny_comment']}}</td>
                      </tr>
					  
					  <?php $i++;  ?>
					  @endif
                    @endforeach   
				   <?php  } ?>
					 
			   	 </tbody>
				 @if($i==0)
					<tr>
					  <td style="color:red;">Nomination not found</td>
					</tr>	
				@endif	
			  </table>	
			 </div>
		   </div>
         </div>
       </div>
     </div>
   </div>    
 </section>


<form name="app" id="election_form" method="POST"  action="{{url('/nomination/schedule-appointment/post') }}" autocomplete='off' enctype="x-www-urlencoded">
{{ csrf_field() }}
<input name="st_code" type="hidden" value="U05">
<input name="ac" type="hidden" value="1">
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

<script>
  function showQuery(id){
	$("#"+id).toggle();  
  }
  function setRadio(v){
	$("#selectRadioButton").val(v);  
  }	
  function bookAnAppointment(){ 
	var id =  $("#selectRadioButton").val();  
	if(id==''){
		alert("Please select nomination");
		return false;
	}
	var r = confirm("Are you sure to scheduled appointment?");
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
@endsection