  @extends('layouts.theme')
  @section('title', 'Nomination')
  @section('content')
  <style type="text/css">
    .error{
      font-size: 12px; 
      color: red;
    }
  </style>
  <link rel="stylesheet" href="{{ asset('theme/css/custom-dark.css')}}" />
  <style type="text/css">
  .fullwidth {
    width: 100%;
    float: left;
  }
  .button-next {
    margin-top: 30px;
  }
  .button-next button {
    float: right;
  }
  .affidavit-preview {
    min-height: 600px;
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
		<span><a href="submit-for-pre-scrutiny" style="background: #f0587e;color: white;padding:3px;">Submit for Pre-Scrutiny</a></span>&nbsp;|&nbsp;
		<span><a href="schedule-appointment">Schedule Appointment</a></span>&nbsp;|&nbsp;
		<span><a href="track-nomination-status">Track Nomination Status</a></span>
		 </div>
		
        <div class="col-md-12">
          <div class="card">
           <div class="card-header d-flex align-items-center">
             <h4>Submit for Pre Scrutiny</h4> &nbsp;&nbsp;&nbsp;&nbsp;
           </div>
           <div class="card-body">
             <div class="row">
			<table class="table" id='two'>
                <thead>
                  <tr>
					<th>Pre Scrutiny</th>
					<th>Pre Scrutiny Status</th>
                    <th>Nomination No.</th>
                    <th>Name</th>
                    <th>AC No & Name</th>
                    <th>Election</th>
                    <th>Pre Scrutiny Date</th>
                    <th align="center" class="text-center">Action</th>
                  </tr>
                </thead>
              
				<tbody>
                 <?php  $ttt=0; if(count($results)>0){ //echo "<pre>"; print_r($results); ?>
                  @foreach($results as $result)
				   @if(($result['is_finalize'] == 1) && ($result['is_apply_prescrutiny'] == 1 or $result['is_apply_prescrutiny']==null) && ($result['prescrutiny_status'] == 1 or $result['prescrutiny_status'] == 2 or $result['prescrutiny_status'] == ''))
					<?php 
					$ddd= 'NA';
					$exp = '';
					$nid=encrypt_string($result['id']);
					if(isset($result['prescrutiny_apply_datetime'])){
					$exp = explode(" ", $result['prescrutiny_apply_datetime']);	
						$time = strtotime($result['prescrutiny_apply_datetime']);
						$ddd =  date("d M Y", $time).' '.$exp[1];
					}
					$send='';
					if($result['step']==1 || empty($result['step']) || ($result['prescrutiny_status']==2)){
						$send=$result['edit_href'].'?nid='.$nid;;
					}
					/*if($result['step']==2){
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
					} */ 
					
					
					$scrutiny='';	$query=0;
					if($result['prescrutiny_status']=='' or $result['prescrutiny_status']=='0' or $result['is_apply_prescrutiny']==1){
						$scrutiny='In Progress';
					}
					if($result['is_apply_prescrutiny']!=1){
						$scrutiny='Not submitted';
					}
					if($result['prescrutiny_status']==1){
						$scrutiny='Cleared';
					}
					if($result['prescrutiny_status']==2){
						$scrutiny='Query Raised';
						$query=1;
					}
					
					?>
					<tr>  
					   <td>
						@if($result['is_apply_prescrutiny']==1)
						<span style="color:gray;">Submitted</span>
						@else
						<input type="radio" name='is_scrutiny_completed' value="{{$result['id']}}" onclick="return setRadio({{$result['id']}});">	 
						@endif
					  </td>	
                      <td>{{$scrutiny}}</td>
                      <td>{{$result['nomination_no']}}</td>
                      <td>{{$result['name']}}</td>
                      <td>{{$result['ac_name']}}</td>
                      <td>{{$result['election_name']}}</td>
                      <td>{{$ddd}}</td>
                      <td align="center"  class="text-center" style="width: 250px;">
                        @if($result['is_finalize'] == 0 or $result['prescrutiny_status']==2)
                        <a href="{{$send}}" class="btn button btn-primary">Edit</a>
                        @else
                        <a href="{{$result['view_href']}}"  class="btn button btn-primary">View</a> 
                        @endif
                        <a href="{{$result['download_href']}}" target="_blank" class="btn button btn-primary">Download Application</a>
                       </td>
                      </tr>
					  <tr> 
                      <td colspan="15" id="<?php echo $ttt; ?>" style="display:none;width:1000px;" >{{$result['prescrutiny_comment']}} </td>
                      </tr>
					   <?php $ttt++;  ?>
					  @endif
                    @endforeach   
				   <?php $ttt++; } else { ?>
					<tr>
					  <td style="color:red;">Nomination not found</td>
					</tr>		
				   <?php } ?>
			   	 </tbody>
					@if( $ttt== 1)
					<tr>
					  <td style="color:red;">Nomination not found</td>
					</tr>
				   @endif	
					@if( $ttt > 0)
					<tr> 
                      <td colspan="8" style="width: 250px;">
					  <div style="background: #bb4292; color: white; padding: 6px; cursor: pointer;border-radius: 5px;width: 168px;" 
					  onclick="return PreScrutiny();">Apply For Pre Scrutiny</div>
					  </td>
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


<form name="app" id="election_form" method="POST"  action="{{url('/nomination/apply_pre_scrutiny/post') }}" autocomplete='off' enctype="x-www-urlencoded">
{{ csrf_field() }}
<input name="st_code" type="text" value="U05">
<input name="ac" type="text" value="1">
<input type="text" name="selectRadioButton" id="selectRadioButton">
</form>
</main>
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('admintheme/js/jquery-ui.js') }}"></script>

<script>
 

  function showQuery(shd, id){ 
	$("#"+shd).toggle();  
	 $.get('prescootiny/'+id, {}, function(data){  
	   $("#"+shd).html(data);
    });
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
@endsection