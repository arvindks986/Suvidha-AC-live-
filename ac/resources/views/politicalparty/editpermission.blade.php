@extends('layouts.profiletheme')
@section('title', 'Permission')
@include('admin.includes.list_script')
@section('content')
<style type="text/css">
h4{
	color:#ffffff !important;
	padding: 0.5em !important;
}
.dataTables_filter {
    display: none;
}
.odd{
	display: none;
}

</style>
<link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap.min.css') }}">
<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    @if (Session::has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
   @endif
    <div class="child-area">
		 
	<div class="nw-crte-usr">
            <div class="head-title">
               <h3>Apply Permission</h3>
             </div>   
             @if($errors->any())
                <div class="alert alert-info" style="color:red;">{{$errors->first()}}</div>
              @endif 
                    
            <form class="form-horizontal" method="post" action="{{url('/Applypermission')}}" enctype="multipart/form-data" autocomplete="off">
	              {{ csrf_field() }}
			        @if(count($detaildata)>0)
	                @foreach ($detaildata as $key=>$rosuper_list)
	            <div class="form-group col-sm-12">
	                <div class="form-group col-sm-6">
	                    <label class="col-sm-4">Name:<span>*</span></label>
	                    <div class="col-sm-8">
	                      <input type="hidden" class="form-control" value="{{$rosuper_list->user_id}}" name="userid" readonly>
	                      <input type="text" class="form-control" value="{{$rosuper_list->name}}" name="name" readonly>
	                    </div>
	                </div><!-- End Of form-group Div -->

			    	<div class="form-group col-sm-6">
	                    <label class="col-sm-4">Email:<span>*</span></label>
	                    <div class="col-sm-8">
	                      <input type="email" class="form-control" value="{{$rosuper_list->email}}" name="email" readonly>
	                    </div>
	                  </div><!-- End Of form-group Div -->
			    </div>
				<div class="form-group col-sm-12">
			    	<div class="form-group col-sm-6">
	                    <label class="col-sm-4">State:<span>*</span></label>
	                    <div class="col-sm-8">
	                        <select name="state" id="state" readonly>
	                           <option value="{{$rosuper_list->ST_CODE}}">{{$rosuper_list->ST_NAME}}</option>
	                        </select>                     
	                    </div>
	                </div><!-- End Of form-group Div -->
			    
			    	<div class="form-group col-sm-6">
	                    <label class="col-sm-4">District:<span>*</span></label>
	                    <div class="col-sm-8">
	                        <select name="district" readonly>
	                           <option value="{{$rosuper_list->DIST_NO}}">{{$rosuper_list->DIST_NAME_EN}}</option>
	                        </select>                     
	                    </div>
	                </div><!-- End Of form-group Div -->
				</div>
				<div class="form-group col-sm-12">
			    	<div class="form-group col-sm-6">
	                    <label class="col-sm-4">AC:<span>*</span></label>
	                    <div class="col-sm-8">
	                        <select name="ac" id="ac" readonly>
	                           <option value="{{$rosuper_list->ac_no}}">{{$rosuper_list->AC_NAME_EN}}</option>
	                        </select>                     
	                    </div>
	                </div><!-- End Of form-group Div -->
				
	                <div class="form-group col-sm-6">
	                    <label class="col-sm-4">Mobile No:<span>*</span></label>
	                    <div class="col-sm-8">
	                      <input type="text" class="form-control" value="{{$rosuper_list->mobileno}}" name="mobile" readonly>
	                    </div>
	                </div><!-- End Of form-group Div -->
				</div>
				<div class="form-group col-sm-12">
		            <div class="form-group col-sm-6">
		                <label class="col-sm-4">Permission Type<span>*</span></label>
			            <div class="col-sm-8">
			                <select name="permission_type" id="selectprmsn">
			                <option>Select Permission Type</option>
			                @if(count($permission_type)>0)
			                @foreach ($permission_type as $key=>$rosuper_list)
			                <option value="{{$rosuper_list->id}}">{{$rosuper_list->permission_name}}</option>
			                @endforeach
			                @endif
			                </select>                     
			            </div>
		            </div>
		            <div class="form-group col-sm-6">
					    <label class="col-sm-4">Police Station<span>*</span></label>
		                <div class="col-sm-8">
		                    <select name="police_station" >
		                    <option>Select Police Station</option>
		                        @if(count($user_details_police)>0)
		                           @foreach ($user_details_police as $key=>$police_list)
		                           <option value="{{$police_list->id}}">{{$police_list->police_station_address}}, {{$police_list->police_st_name}}</option>
		                           @endforeach
		                        @endif
		                    </select>                     
		                </div>
					</div>    
				</div>
	            <div class="form-group col-lg-12" style="display: none" id="permsn_doc">
	            </div>
	            <div class="form-group col-sm-12">
			        <div class="form-group col-sm-6">
		                <label class="col-sm-4">Start Date&Time:<span>*</span></label>
		                <div class="col-sm-8">
	                      <input type='text'  id="datetimepicker3" name="start" class="form-control" />
	                    </div>
	                </div><!-- End Of form-group Div -->
			        <div class="form-group col-sm-6">
	                    <label class="col-sm-4">End Date & Time:<span>*</span></label>
	                    <div class="col-sm-8">
	                      <input type='text' id="datetimepicker4" name="end" class="form-control" />
	                    </div>
	                </div><!-- End Of form-group Div -->       
				</div>
		  		<div class="form-group col-sm-12">
			        <div class="form-group col-sm-6">
	                    <label class="col-sm-4">Location:<span>*</span></label>
						<div class="col-sm-8">
	                        <select name="location" id="location">
	                        <option>Select Location</option>
	                        @if(count($user_details_location)>0)
	                        @foreach($user_details_location as $key=>$rosuper_list1)
	                           <option value="{{$rosuper_list1->id}}">{{$rosuper_list1->location_name}}</option>
	                        @endforeach
							<option value="">Other</option>
	                        @endif 
	                        </select>  
						</div>
	                </div>
					<div class="form-group col-sm-6">
					  <label class="col-sm-4"><span></span></label>
					  <div class="col-sm-8">
						{!! Form::text('other', null, ['placeholder' => 'Enter Location Here','class' => 'date form-control', 'id' => 'other', 'style'=>'display:none;']) !!}
						</div>
					</div>		
	            </div>
				<div class="form-group col-sm-12">
				<div class="form-group col-sm-3"></div>
	  	        <div id="dvMap"></div>	
	            </div>				 
				@endforeach
				@endif
	            <div class="btns-actn">
	                <input type="submit" name="submit" class="btn btn-info" value="Save/Next">
	            </div>
            </form>
            
          </div><!-- End Of nw-crte-usr Div -->
		  
      </div><!-- End Of child-area Div -->
	
   
    </div> <!-- End Of child-area Div -->   



	
  </div><!-- End Of parent-wrap Div -->

  </div>
  
  <input type="hidden" id="base_url" value="<?php echo url('/'); ?>">

<style>
/* Always set the map height explicitly to define the size of the div
* element that contains the map. */
#dvMap {
height: 300px;
width: 600px;
}
/* Optional: Makes the sample page fill the window. */
html, body {
height: 100%;
margin: 0;
padding: 0;
}
</style>
  
  
@endsection
@section('scripts')
<script type="text/javascript">

jQuery( document ).ready(function() {
	
	var stcode = jQuery("#state :selected").val();
	var district = jQuery("#district :selected").val();
	var ac = jQuery("#ac :selected").val();
	    if(ac != "")
		{
		$('#openmap').css('display', ($(this).val() != '') ? 'block' : 'none');
		}
		else
		{
	   $('#openmap').css('display', ($(this).val() != '') ? 'display' : 'none');
		}
		jQuery.ajax({
		url: "{{url('/politicalparty/getlocations')}}",
		type: 'GET',
		dataType: 'json',
		data: {stcode:stcode,ac:ac},
		success: function(result){
		var jsonObj = JSON.stringify(result);
		// alert(result[0]['name']);
		var acselect = jQuery('select[name="location"]');
		acselect.empty();

		var map = new google.maps.Map(document.getElementById('dvMap'), {
		zoom: 10,
		center: new google.maps.LatLng(result[0]['latitude'], result[0]['logitude']),
		mapTypeId: google.maps.MapTypeId.ROADMAP
		});

		var infowindow = new google.maps.InfoWindow();
		var marker, i;
        
		for (i = 0; i < result.length; i++) 
		{  
		marker = new google.maps.Marker
		({
		position: new google.maps.LatLng(result[i]['latitude'], result[i]['longitude']),
		map: map
		});

	    google.maps.event.addListener(marker, 'click', (function(marker, i) {
		return function() {
		infowindow.setContent(result[i]['location_name']);
		infowindow.open(map, marker);
		}
		})(marker, i)); 
		}
		var src = 'https://cvigil.eci.nic.in/GIS/'+stcode+'.kmz';
		var kmlLayer = new google.maps.KmlLayer(src, {
		suppressInfoWindows: true,
		preserveViewport: false,
		map: map
		}); 
		var achtml = '';
		var otherhtml = '';
		achtml = achtml + '<option value=""> Select Location</option>';
	    var achtmlother = '<option value=other>Other</option>';
		jQuery.each(result, function(key, value) 
		{ 
		achtml = achtml + '<option value="'+value.id+'">'+value.location_name+'</option>';
		});
		achtml = achtml + achtmlother;
		jQuery("select[name='location']").html(achtml);
		//alert(achtml);
	
		var achtmlend = '';
		jQuery("select[name='location']").append(achtmlend)
			}
		});

	
	jQuery("select[name='location']").change(function()
	{
		var stcode = jQuery("select[name='state']").val();
		var district = jQuery("select[name='district']").val();
		var ac = jQuery("select[name='ac']").val();
		var locationid = jQuery(this).val();
		if(locationid == "other")
		{
		// alert("divya");
		 $('#other').css('display', ($(this).val() == 'other') ? 'block' : 'none');
		}
		else
		{
		$('#other').css('display', ($(this).val() == 'other') ? 'display' : 'none');	
		}
		jQuery.ajax({
		url: "{{url('/politicalparty/getlatlong')}}",
		type: 'GET',
		//dataType:'json',
		data: {locationid:locationid},
		success: function(arr)
		{
		//alert(arr);
		var jsonObj = JSON.parse(arr);
		placenameslocation = jsonObj[0]['location_name'];
		stcodes =  jsonObj[0]['ST_CODE'];
		//alert(placenameslocation);
		$("#latitude").val(jsonObj[0]['latitude']);
		$("#longitude").val(jsonObj[0]['longitude']);
		$("#placenameslocation").val(jsonObj[0]['name']);
		LoadMap(jsonObj[0]['latitude'], jsonObj[0]['longitude']);			 
		}
		});
	});
	});
	
	
	function LoadMap( lat, lng) 
	{
		var src = 'https://cvigil.eci.nic.in/GIS/'+stcodes+'.kmz';
		var markers = [{
		"lat": lat,
		"lng": lng,
		},	
		];
		var mapOptions = {
		center: new google.maps.LatLng(markers[0].lat, markers[0].lng),
		zoom: 10,
		mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		var myContent = 'test';
		var map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
		//Create and open InfoWindow.
		//var infowindow = new google.maps.InfoWindow();
		var infowindow = new google.maps.InfoWindow({
		//content: '<div id="myInfoWinDiv">'+ myContent +'</div>'
		});
		/* google.maps.event.addListener(infowindow,'domready',function()
		{}); */
		var marker, i;	  
		marker = new google.maps.Marker
		({
		position: new google.maps.LatLng(markers[0].lat, markers[0].lng),
		map: map,
		icon:  'https://www.google.com/mapfiles/marker_black.png'
		});

	    google.maps.event.addListener(marker, 'click', (function(marker, i) {
		return function() {
		infowindow.setContent(placenameslocation);
		infowindow.open(map, marker);
		
		
		}
		})(marker, i)); 
		var src = 'https://cvigil.eci.nic.in/GIS/'+stcodes+'.kmz';
		var kmlLayer = new google.maps.KmlLayer(src, {
		suppressInfoWindows: true,
		preserveViewport: false,
		map: map
		});
	
	

	}
</script>
<script async defer
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA4mlo-kY0vyBDIdeXffR2igqE5igx3piE&callback=LoadMap">
</script>
<script>
jQuery(function(){

 jQuery('#datetimepicker3').datetimepicker({
   format: 'YYYY-MM-DD HH:mm',
   minDate: new Date()
 });
        jQuery('#datetimepicker4').datetimepicker({
            useCurrent: false,
      format: 'YYYY-MM-DD HH:mm',
        });
        jQuery("#datetimepicker3").on("dp.change", function (e) {
            jQuery('#datetimepicker4').data("DateTimePicker").minDate(e.date);
        });
        jQuery("#datetimepicker4").on("dp.change", function (e) {
            jQuery('#datetimepicker3').data("DateTimePicker").maxDate(e.date);
        });
        
        //neera
        $('select#selectprmsn').change(function(){
            var permsn_id=$(this).val();
            var base_url = $("#base_url").val();
            var token = $('meta[name="csrf-token"]').attr('content');
            //alert(permsn_id);
             $.ajax({
                url:base_url+'/getSelectDetails',
                type:'POST',
                data: {_token:token,permsn_id:permsn_id},
                success:function(response)
                {
                    //alert(response);exit;
                    var cnt = response.length;
                    var str='';
                    var required_status='';
                    //alert(cnt);
                     $('#permsn_doc').css('display','');
                     for (var i=0; i<cnt; i++) {
                        var doc_name=response[i]['doc_name']
                        var doc_size=response[i]['doc_size']
                        var status=response[i]['required_status']
                        if(status == 1)
                        {
                            required_status='Document is Required';
                        }
                        else
                        {
                            required_status='Document is Not Required';
                        }
                        var file_name=response[i]['file_name']
                        if(response!=0)
                        {
                         str += "<ul class='list-inline'><li>" + doc_name + "</li><li>" +doc_size+"</li><li>"+required_status+"</li><li><a href='{{asset('public/uploads/permission-document')}}/"+file_name+" ' download>"+file_name+"</a></li><li><input type='file' name='permsndoc["+i+"][p_doc]'></li></ul>";
                        }else
                        {
                             str += "<ul class='list-inline'><li>No Document Required.</li>";
                            
                        }
                       }
                       $('#permsn_doc').html(str);
                     
                }
            });
        });
        //end neera
        $('#state').change(function(){
        	// alert($(this).val());
        	var state_id = $(this).val();
        	$('#dis').empty(); 
        	if(state_id)
        	{
        		$.ajax({
	          	url: '<?php echo url('/district'); ?>/'+state_id,
	            type: "GET",
	            dataType: "json",
	            success:function(data) {
	            			// console.log(data);   
	                        $.each(data, function (index, data) {
					        $('#dis').append('<option value="'+data.DIST_NO+'">'+data.DIST_NAME_EN+'</option>');
					    }) 
	                } 
	        	}); 
        	}
        });
        $('#dis').change(function(){
        	var state_id = $('#state').val();
        	var dis_id = $('#dis').val();
        	// alert(state_id);
        	// alert(dis_id);
        	$('#ac').empty(); 
        	
        		$.ajax({
	          	url: '<?php echo url('/ac'); ?>/'+state_id+'/'+dis_id,
	            	type: "GET",
	            	dataType: "json",
	            	success:function(data) {
	            			// console.log(data); 
	            			$.each(data, function (index, data) {
					        $('#ac').append('<option value="'+data.AC_NO+'">'+data.AC_NAME+'</option>');
					    })   
	                } 
	        	}); 
        	
        });
        $('#ac').change(function(){
        	var state_id = $('#state').val();
        	var ac_id = $('#ac').val();
        	$('#ps').empty(); 
        	
        		$.ajax({
	          	url: '<?php echo url('/policestation'); ?>/'+state_id+'/'+ac_id,
	            	type: "GET",
	            	dataType: "json",
	            	success:function(data) {
	            			console.log(data); 
	            			$.each(data, function (index, data) {
					        $('#ps').append('<option value="'+data.id+'">'+data.police_station_address+', '+data.police_st_name+'</option>');
					    })   
	                } 
	        	}); 
        	
        });
        $('#ac').change(function(){
        	var state_id = $('#state').val();
        	var disrict_id = $('#dis').val();
        	var ac_id = $('#ac').val();
        	alert(state_id);
        	alert(disrict_id);
        	alert(ac_id);
        	$('#location').empty(); 
        		$.ajax({
	          	url: '<?php echo url('/location'); ?>/'+state_id+'/'+disrict_id+'/'+ac_id,
	            	type: "GET",
	            	dataType: "json",
	            	success:function(data) {
	            			console.log(data); 
	            			$.each(data, function (index, data) {
					        $('#location').append('<option value="'+data.id+'">'+data.location_name+'</option>');
					    })   
	                } 
	        	}); 
        	
        });
        
        
});
</script>



<script async defer
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA4mlo-kY0vyBDIdeXffR2igqE5igx3piE&callback=LoadMap">
</script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css" rel="stylesheet"/>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js" ></script>-->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.js" type="text/javascript" ></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
@endsection

