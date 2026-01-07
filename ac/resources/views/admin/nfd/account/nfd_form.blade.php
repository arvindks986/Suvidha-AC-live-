@extends('admin.layouts.ac.theme')
@section('content')
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
textarea {
  resize: none;
}
</style>

<div class="loader" style="display:none;"></div>

<section class="statistics color-grey pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col pull-left">
       <h4>{!! $heading_title !!}</h4>
     </div>

     <div class="col  pull-right  text-right">
	 
	 @if(isset($filter_buttons) && count($filter_buttons)>0)

        @foreach($filter_buttons as $button)
        <?php $but = explode(':',$button); ?>
        <span class="" style="margin-right: 10px;">
          <span><b>{!! $but[0] !!}:</b></span>
          <span class="badge badge-info">{!! $but[1] !!}</span>

        </span>

        @endforeach 
		
@endif	  

@if(isset($buttons) && count($buttons)>0)
      @foreach($buttons as $button)
      <span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="{{ $button['name'] }}" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
      @endforeach
      @endif  
    </div>
  </div>
</div>  
</section>







<div class="container-fluid">

  <!-- Start parent-wrap div --> 
  <!-- Start parent-wrap div -->  
  <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
      @if(Session::has('flash-message'))
@if(Session::has('status'))
<?php
$status = Session::get('status');
if($status==1){
 $class = 'alert-success';
}
else{
  $class = 'alert-danger';
}
?>
@endif
<div class="alert <?php echo $class; ?> in">
  <a href="#" class="close" data-dismiss="alert">&times;</a>
  {{ Session::get('flash-message') }}
</div>
@endif
     <div class="page-contant card">
      <div class="random-area card-body">
        <br>

        <form class="form-horizontal" method="post" action="{!! $action !!}" autocomplete="off">

          <input type="hidden" value="{!! csrf_token() !!}" name="_token">
          <?php if(isset($encrpt_id)){ ?>
            <input type="hidden" value="{!! $encrpt_id !!}" name="id">
          <?php } ?>


          <div class="form-group mb-1">
            <label class="col-md-3 pull-left">Name</label>
            <input type="text" class="form-control col-md-9 " name="name" id="name" value="{{$name}}" placeholder="Name">
            @if(isset($errors))
            <span class="text-error text-danger text-right pull-right">{!! $errors->first('name') !!}</span>
            @endif 

          </div>


          <div class="form-group mb-1">
            <label class="col-md-3 pull-left">Mobile</label>
            <input type="text" class="form-control col-md-9 " name="mobile" id="name" value="{{$mobile}}" placeholder="Mobile" size="10" maxlength="10">
            @if(isset($errors))
            <span class="text-error text-danger text-right pull-right">{!! $errors->first('mobile') !!}</span>
            @endif 
          </div>

          <div class="form-group mb-1">
            <label class="col-md-3  pull-left">Address</label>
            <textarea class="form-control col-md-9" name="address" id="address" value="{{$address}}" placeholder="Address" rows="1">{{$address}}</textarea>
            
            <input type="hidden" name="lat" id="lat" value="{{$lat}}">
            <input type="hidden" name="lng" id="lng" value="{{$lng}}">
            @if(isset($errors))
            <span class="text-error text-danger text-right pull-right">{!! $errors->first('address') !!}</span>
            @endif 

          </div>

          <div class="form-group mb-1">
            <label class="col-md-3 pull-left">Password</label>
            <input type="password" class="form-control col-md-9 " name="password" id="password" placeholder="Password">
            @if(isset($errors))
            <span class="text-error text-danger text-right pull-right">{!! $errors->first('password') !!}</span>
            @endif 
          </div>

          <div class="form-group mb-1">
            <label class="col-md-3 pull-left">Confirm Password</label>
            <input type="password" class="form-control col-md-9 " name="password_confirmation" id="password_confirmation" placeholder="Confirm Password">
            @if(isset($errors))
            <span class="text-error text-danger text-right pull-right">{!! $errors->first('password_confirmation') !!}</span>
            @endif 
          </div>

          <div class="form-group mb-1">
            <label class="col-md-3 pull-left">2 Step Pin</label>
            <input type="password" class="form-control col-md-9 " name="pin" id="pin" placeholder="4 digit Pin"  size="4" maxlength="4">
            @if(isset($errors))
            <span class="text-error text-danger text-right pull-right">{!! $errors->first('pin') !!}</span>
            @endif 
          </div>

@if(config('public_config.google_map_api'))
<div class="form-group mb-1">
  <label class="col-md-3  pull-left" style="visibility: hidden;">Address</label>
  <div id="map" class="col-md-9" style="height: 300px;"></div>
</div>
@endif


   



        

          <div class="form-group mb-1">
            <label class="col-md-3 pull-left" style="visibility: hidden;"></label>
            <button type="submit" class="btn btn-large btn-primary">Submit</button>
          </div>

        </form>
      </div>
    </div>
  </div> 
</div>

</div><!-- End Of parent-wrap Div -->
</div> 
@endsection

@section('script')

@if(config('public_config.google_map_api'))
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo config('public_config.google_map_api'); ?>&libraries=places&callback=initAutocomplete"
         async defer></script>

 <script>
      function initAutocomplete() {

        var map = new google.maps.Map(document.getElementById('map'), {
          fullscreenControl: false,
          zoom: 7,
          zoomControl: true,
          mapTypeControlOptions: {
            mapTypeIds: [google.maps.MapTypeId.ROADMAP]
          }
        });

        var geocoder = new google.maps.Geocoder();
        var location = "India";
        geocoder.geocode( { 'address': location }, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                map.setCenter(results[0].geometry.location);
            } else {
                alert("Could not find location: " + location);
            }
        });

        // Create the search box and link it to the UI element.
        var input = document.getElementById('address');
        var searchBox = new google.maps.places.SearchBox(input);


        // Bias the SearchBox results towards current map's viewport.
        map.addListener('bounds_changed', function() {
          searchBox.setBounds(map.getBounds());
        });

        var markers = [];
        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener('places_changed', function() {
          var places = searchBox.getPlaces();

          if (places.length == 0) {
            return;
          }

          // Clear out the old markers.
          markers.forEach(function(marker) {
            marker.setMap(null);
          });
          markers = [];

          // For each place, get the icon, name and location.
          var bounds = new google.maps.LatLngBounds();
          places.forEach(function(place) {
            if (!place.geometry) {
              console.log("Returned place contains no geometry");
              return;
            }
            var icon = {
              url: place.icon,
              size: new google.maps.Size(71, 71),
              origin: new google.maps.Point(0, 0),
              anchor: new google.maps.Point(17, 34),
              scaledSize: new google.maps.Size(25, 25)
            };

         
            if(!place.geometry.location){
              return;
            }

            $('#lat').val(place.geometry.location.lat);
            $('#lng').val(place.geometry.location.lng);

        
            // Create a marker for each place.
            markers.push(new google.maps.Marker({
              map: map,
              icon: icon,
              title: place.name,
              position: place.geometry.location
            }));

            if (place.geometry.viewport) {
              // Only geocodes have viewport.
              bounds.union(place.geometry.viewport);
            } else {
              bounds.extend(place.geometry.location);
            }
          });
          map.fitBounds(bounds);
        });
      }

    </script>
@endif
@endsection
