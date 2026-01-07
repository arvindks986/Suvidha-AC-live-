<?php 
$userId = $userid; 
//print_r($userId);
//print_r("hello");exit;
?>

@extends('layouts.profiletheme')

@section('title', 'Candidate Profile')

@section('content-heading', 'Candidate Profile')

@section('content')

  <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #dvMap {
          height: 500px;
          width: 900px;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>




<div class="container-fluid">
<!-- Start parent-wrap div -->  
<div class="parent-wrap">
<!-- Start child-area Div --> 
<div class="child-area">
<div class="row"> 
<div class="nw-crte-usr">
<div class="head-title">
<h3><i><img src="{{ asset('theme/images/icons/tab-icon-002.png')}}" /></i>Candidate Profile</h3>
</div>
<div class="card-body">

@if($errors)
@foreach($errors->all() as $error)
<div class="alert alert-danger" role="alert">
{{ $error }}
</div>
@endforeach
@endif

@if(Session::has('message'))
<p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
@endif

<form method="post" action="saveprofile">
{{ csrf_field() }}
<input type="hidden" name="userId" value="{{$userId}}"/>

<div class="col-md-12 form-group row">

<div class="col-sm-4  row">
<label for="state" class="col-sm-4 col-form-label">State</label>
<div class="col-sm-8">
<select name="state" id="state" class="form-control" >
<option>-- Select States --</option>
@foreach($getStates as $State)
<option value="{{ $State->ST_CODE }}"> {{ $State->ST_NAME }}</option>
@endforeach
</select>
</div>
</div>

<div class="col-sm-4  row">
<label for="district" class="col-sm-4 col-form-label">District</label>
<div class="col-sm-8 distt">
<select name="district" id="district" class="form-control">
<option>-- Select Districts --</option>
@foreach($getDistricts as $District)
<option value="{{ $District['DIST_NO'] }}"> 
{{$District->DIST_NAME }}
</option>
@endforeach 
</select>
</div>
</div>
<div class="col-sm-4  row">
<label for="ac" class="col-sm-4 col-form-label">AC</label>
<div class="col-sm-8 disttac">
<select name="ac" id="ac" class="form-control">
<option value="0">-- Select AC --</option>
@foreach($getAclist as $getAc)
<option value="{{ $getAc->AC_NO }}"> 
{{$getAc->AC_NAME }}
</option>
@endforeach 
</select>
</div>
</div>
</div>

<div class="col-md-12 form-group row">
<div class="col-sm-4 row">
<label for="ac" class="col-sm-4 col-form-label">Permission</label>
<div class="col-sm-8">
<?php //print_r($permissiondata); exit;  ?>	
<select name="permission" class="form-control">
<option>-- Select Permission --</option>
@foreach($permissiondata as $permdata)
<option value="{{$permdata->id }}"> 
{{$permdata->name }}
</option>
@endforeach 
</select>
</div>
</div>
<div class="col-sm-4 row">
<label for="ac" class="col-sm-4 col-form-label">Location</label>
<div class="col-sm-8">
<select name="location" id="location" class="form-control">
<option>-- Select Location --</option>
@foreach($locationdata as $locdata)
<option value=""> 
{{$locdata->name }}
</option>
@endforeach 
<option value="other">Other</option>
</select>
</div>
</div>
<input type="hidden" name="longitude"  id="longitude"/>
<input type="hidden" name="latitude" id="latitude"/>
<input type="hidden" name="placename" id="placename"/>
<div class="othertextbox">
<div class="col-sm-4 row">
<label for="other" class="col-sm-4 col-form-label"></label>
<div class="col-sm-8">
{!! Form::text('other', null, ['placeholder' => 'Enter Location Here','class' => 'date form-control', 'id' => 'other', 'style'=>'display:none;']) !!}
<div class="doberrormsg errormsg"></div>
</div>
</div>
</div>
</div>
<span id="openmap" style="display:none">
<div class="col-md-12 form-group row">

<div class="col-sm-12 row">
<div id="dvMap"></div>
</div>

</div>
</span>
<div class="btns-actn">
{!! Form::submit('Save as Draft', ['class' => 'btn btn-primary', 'id'=>'candprofile']) !!}
</div>

</form>

</div>
</div>
</div>
</div>
</div>



@endsection
@section('scripts')


<script type="text/javascript">

jQuery( document ).ready(function() {
	jQuery("select[name='state']").change(function()
	{
		var stcode = jQuery(this).val();
		//alert("hello");
		jQuery.ajax({
		url: "{{url('/permission/getDistricts')}}",
		type: 'GET',
		dataType: 'json',
		data: {stcode:stcode},
		success: function(result){
		var distselect = jQuery('form select[name=district]');
		distselect.empty();
		var statehtml = '';
		statehtml = statehtml + '<option value=""> Select District</option> ';
		jQuery.each(result,function(key, value) {
		//distselect.append('<option value=' + value.DIST_NO + '>' + value.DIST_NAME + '</option>');
		statehtml = statehtml + '<option value="'+value.DIST_NO+'">'+value.DIST_NAME+'</option>'; 
		jQuery("select[name='district']").html(statehtml);
		});
		var statehtml_end = '';
		jQuery("select[name='district']").append(statehtml_end)
		}
		});
	});

	jQuery("#district").change(function()
	{
		//alert('test');
		var stcode = jQuery("select[name='state']").val();
		var district = jQuery(this).val();
		
		//alert(stcode);
		jQuery.ajax({
		url: "{{url('/permission/getAcs')}}",
		type: 'GET',
		dataType: 'json',
		data: {stcode:stcode,district:district},
		success: function(result){
		//alert(result);
		var acselect = jQuery('select[name="ac"]');
		acselect.empty();
		var achtml = '';
		achtml = achtml + '<option value=""> Select AC</option> ';
		jQuery.each(result,function(key, value) {
		achtml = achtml + '<option value=' + value.AC_NO + '>' + value.AC_NAME + '</option>';
		jQuery("select[name='ac']").html(achtml);		
		});
		var achtmlend = '';
		jQuery("select[name='ac']").append(achtmlend)
		}
		});
	});
	var lat ;
	var lng;
	var stcodeval;
	var stid ;
	var stname ;
	var latitude ;
	var longitude;
	var latac;
	var lngac;
	var mapname;
	var placenames;
	var stcodes = new Array();
    var lat = new Array();
	var lng = new Array();
	var title = new Array();
	jQuery("#ac").change(function()
	{
		var stcode = jQuery("select[name='state']").val();
		var district = jQuery("select[name='district']").val();
		var ac = jQuery(this).val();
        if(ac != "")
		{
		$('#openmap').css('display', ($(this).val() != '') ? 'block' : 'none');
		}
		else
		{
	  $('#openmap').css('display', ($(this).val() != '') ? 'display' : 'none');
		}
		jQuery.ajax({
		url: "{{url('/permission/getlocations')}}",
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
		infowindow.setContent(result[i]['name']);
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
		achtml = achtml + '<option value="'+value.id+'">'+value.name+'</option>';
		});
		achtml = achtml + achtmlother;
		jQuery("select[name='location']").html(achtml);
		//alert(achtml);
	
		var achtmlend = '';
		jQuery("select[name='location']").append(achtmlend)
			}
		});

    });	

	jQuery("#location").change(function()
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
		url: "{{url('/permission/getlatlong')}}",
		type: 'GET',
		// dataType:'json',
		data: {locationid:locationid},
		success: function(arr)
		{
		var jsonObj = JSON.parse(arr);
		placenames = jsonObj[0]['name'];
		stcodes =  jsonObj[0]['ST_CODE'];
		//alert(stcodes);
		$("#latitude").val(jsonObj[0]['latitude']);
		$("#longitude").val(jsonObj[0]['longitude']);
		$("#placename").val(jsonObj[0]['name']);
		LoadMap(jsonObj[0]['latitude'], jsonObj[0]['longitude']);			 
		}
		});

	});
  
/* 	$(function() {

}); */
	
	function LoadMap( lat, lng) 
	{
		var src = 'https://cvigil.eci.nic.in/GIS/'+stcodes+'.kmz';
		var markers = [{
		"lat": lat,
		"lng": lng,
		"description": '<div class="popupmap"><form name="frm" class="form-horizontal" id="frm"><h4>'+placenames+'</h4><br/><strong>Status</Strong>:Yet to be added <br/><div class="row"><input class="col-md-6 inputOne" type="text"  id="datetimepicker3" name="datetimepicker3" class="form-control" /><input class="col-md-6 inputOne" type="text" id="datetimepicker4" name="datetimepicker4" class="form-control" /></div><input type="hidden" value="<?php echo $userId; ?>"  name="userid" id="userid"/><input type="button" value="Submit" onclick="formcalsubmit();" id="submit" name="submit"></form></div>'
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

		google.maps.event.addListener(infowindow,'domready',function()
		{
		   jQuery(function(){
		   jQuery('#datetimepicker3').datetimepicker({
	       format: 'MM-DD-YYYY HH:mm',
	       minDate: new Date()
           });
           jQuery('#datetimepicker4').datetimepicker({
            useCurrent: false,
			format: 'MM-DD-YYYY HH:mm',
           });
           jQuery("#datetimepicker3").on("dp.change", function (e) {
            jQuery('#datetimepicker4').data("DateTimePicker").minDate(e.date);
           });
           jQuery("#datetimepicker4").on("dp.change", function (e) {
            jQuery('#datetimepicker3').data("DateTimePicker").maxDate(e.date);
           });
		   });
			
		 	jQuery('#submit').click(function() 
			{	
			alert("hello");
			var startval = document.getElementById("datetimepicker3").value;
			var endval = document.getElementById("datetimepicker4").value;
			var userid = document.getElementById("userid").value;
			alert(startval);
			alert(endval);
			alert(userid);
			jQuery.ajax({
			url: "{{url('/permission/savecalendarvalue')}}",
			type: 'get',
			//dataType: 'json',
			data: {dateval:dateval,userid:userid},
			success: function(result)
			{
			alert(result);
			}
			}); 
			});
			 
	
		});
		 
		var marker, i;

		  
		marker = new google.maps.Marker
		({
		position: new google.maps.LatLng(markers[0].lat, markers[0].lng),
		map: map,
		icon:  'https://www.google.com/mapfiles/marker_black.png'
		});

	    google.maps.event.addListener(marker, 'click', (function(marker, i) {
		return function() {
		infowindow.setContent(markers[0].description);
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
	

}); 

</script>
<script async defer
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA4mlo-kY0vyBDIdeXffR2igqE5igx3piE&callback=LoadMap">
</script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css" rel="stylesheet"/>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js" ></script>--->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.js" type="text/javascript" ></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>


<script type="text/javascript">
/* jQuery('#submit').click(function() 
{
//alert("hello");
var startDate = document.getElementById("datetimepicker3").value;
var endDate = document.getElementById("datetimepicker4").value;
alert(startDate);
alert(endDate);
}); */
</script>

<script>
/* jQuery(function(){

 jQuery('#datetimepicker3').datetimepicker({
	 format: 'MM-DD-YYYY HH:mm',
	 minDate: new Date()
 });
        jQuery('#datetimepicker4').datetimepicker({
            useCurrent: false,
			format: 'MM-DD-YYYY HH:mm',
        });
        jQuery("#datetimepicker3").on("dp.change", function (e) {
            jQuery('#datetimepicker4').data("DateTimePicker").minDate(e.date);
        });
        jQuery("#datetimepicker4").on("dp.change", function (e) {
            jQuery('#datetimepicker3').data("DateTimePicker").maxDate(e.date);
        }); 
}); */
</script>


@endsection