<link rel="stylesheet" href="{{asset('js/leaflet/leaflet.css')}}" />
<link rel="stylesheet" type="text/css" href="{{asset('js/leaflet/esri-leaflet-geocoder.css')}}">
<link href="{{asset('js/leaflet/leaflet.fullscreen.css')}}" rel='stylesheet' />
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.6.2/css/bootstrap-slider.min.css" rel="stylesheet"/>
<style>
    /* Always set the map height explicitly to define the size of the div
     * element that contains the map. */
     .facility p i
     {
        right:30%;
     }
    #map_in {
        min-height: 95%;
    }
    .details p {
    font-weight: 600;
    font-size: 11px;
    }
    .details p span {
    right: 0%;
}
	.leaflet-routing-error {
	  width: 320px;
	  background-color: rgb(238, 153, 164);
	  padding-top: 4px;
	  transition: all 0.2s ease;
	  box-sizing: border-box;
	}
    /* Optional: Makes the sample page fill the window. */
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
    }
    .slider-selection {
        background-image: -webkit-linear-gradient(top,#f9f9f9,#0c0c0c 100%);
    }
    .loader {
        position: fixed;
        z-index: 99999;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: block;
        margin: auto;
        background-color: #fff;
        opacity: 0.7;
        padding-left: 56%;
        padding-top: 15%;
    }
    .detls-map{
        padding: 15px 20px;
        background-color: #fff;
            padding-bottom: 0px;
    }
    .inner-dtl{
        background-color: #eee;
        border: 1px solid #dcdcdc;
        padding: 5px 10px;
        border-radius: 0;
    }
    .inner-dtl p select{
        padding: 5px;
        font-size: 14px;
    }
    .inner-dtl p span{margin-right: 2px;}
    .inner-dtl .col-sm-6:first-child{
        border-right: 1px dashed #f0587e;
    }
    .mapshow{
        font-weight:normal;
    }
    .leaflet-container a.leaflet-popup-close-button{
        color: #f0587e;    
    }    
    #map{
        min-height: 450px!important;
    }
    .compare{
        background-color: #f0587e;
        padding: 7px 5px;
        font-size: 12px;
        color: #fff;
    }
     .compare:hover{
        color: #fff;
    }
    .details_ac{
        margin: 0 auto;
        border: 1px solid #004085;
        box-shadow: 1px 2px 7px 2px #f1f1f1;
        max-height: 440px;
        overflow-y: auto;
        }   
</style>
@include('ATLAS.common-css')
<link rel="stylesheet" href="{{asset('js/leaflet/leaflet-routing-machine.css')}}" />
<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">

<main role="main" class="inner cover mb-3 mb-auto">
	
    <section id="wrapper">      
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-3 pr-md-0 pr-sm-3" style="margin-top: 0px;">
                    <div class="search-field mt-1">
                        @if (session('error'))
                        <div class="alert alert-success">
                            {{ session('error') }}
                        </div>
                        @endif
                        @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                        @endif
                        <div class="form-sec" style="position:relative">    
                            <div class="form-group">
                                <label>PWD Facility:</label>
                                <select name="" class="form-control" id="pwdfacility">
                                    <option value="00">--Select PWD Facility--</option>
                                    <option value="1">Available</option>
                                    <option value="0">Not Available</option>
                                </select>
                            </div>
                            <div class="form-group" style="width: 105%;margin-bottom: 26px;">
                                <label>Electors : <input type="text" id="amount" style="font-size: 15px; width: 95%; border: none; height: 2px; background: none; color: rgb(0, 121, 130); margin-top: 2px;">
                                    <input type="hidden" id="electorrange">
                                    <div id="slider-range"></div>
                                </label> 
                                <div style="float: right; margin-right: 36px; font-size: 13px; margin-bottom: 13px;cursor: pointer;" id="findElectorsByRangeBar">
                                    <img src="{{asset('img/search.jpg')}}" height="20" width="20"> Apply
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Polling Station Type:</label>
                                <select name="booth_type" class="form-control" id="pstype">
                                    <option value="0">--Select Booth Type--</option>
                                    @if(!empty($ps_type))
                                    @foreach($ps_type as $data)
                                    <option value="{{$data->id}}">{{$data->ps_type}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-9" style="height:544px;">
                    <div class="mb-1" id="textnew" style="display:none;">
            <div class=" inner-dtl">
                <div class="row">
                    <div class="col-sm-12">
    					<p class="mb-0 mr-1" style="text-align:center;">
                            <span><strong class="text-info" >State: </strong></span><span id="sttext" class="text-info"> : aaa</span>                          
                            <span style="display:none;" id="distText">
                                <strong class="text-info" style="display:none;" id="distText2">District: </strong>
                                <span style="display:none;" id="dstext" class="text-info"> : </span>
                            </span>                    
                            <span style="display:none;" id="acText">
                                <strong class="text-info" style="display:none;" id="acText2">AC: </strong>
                                <span style="display:none;" id="act" class="text-info"> : </span>
                            </span>
                            <span style="display:none;" id="psText">
                                <strong class="text-info" style="display:none;" id="psText2">PS: </strong>
                                <span style="display:none;" id="pct" class="text-info"> : </span>
                            </span> 
                            <span id="t_pscount"><strong class="text-info">Total P.S</strong><span class="text-info"> :</span></span>&nbsp;&nbsp; 
                            <span id="e_count"><strong class="text-info">Electors Count</strong><span class="text-info"> : </span></span>&nbsp;&nbsp; 
                            <span id="p_count"><strong class="text-info">PWD Count</strong><span class="text-info"> :</span></span>&nbsp;&nbsp;
                            <span id="pwd_facility"><strong class="text-info" style="display:none">PWD facility available</strong><span class="text-info"></span></span>&nbsp;&nbsp;
                        </p>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
                     <div class="col-sm-12 offset-sm-0 inner-dtl">
                        <div class="row">
                            <div class="col-sm-12" id="textnew">
                                <p class="mb-0 mr-1" style="text-align:center;">
                                    <span class="text-info"> 
                                       <select name="state_search_header" id="state">
                                           <option value="{{$user_data->st_code}}" selected="selected">{{getstatebystatecode($user_data->st_code)->ST_NAME}}</option>
                                        </select>
                                    </span>    
                                    <span class="text-info"> 
                                        <select name="dist_search_header" id="dist">
                                            <option value="{{$user_data->dist_no}}" selected="selected">{{getdistrictbydistrictno($user_data->st_code,$user_data->dist_no)->DIST_NAME}}</option>  
                                        </select>
                                    </span>     
                                    <span class="text-info"> 
                                       <select name="ac_search_header"  id="ac" >
                                            <option value="{{$user_data->ac_no}}" selected>{{getacname($user_data->st_code,$user_data->ac_no)->AC_NAME}}</option>
                                        </select>
                                    </span>
                                    <span class="text-info"> 
                                       <select name="ps_search_header"  id="ps" >
                                            <option value="">Select PS Name</option>
                                        </select>
                                    </span> 
                                   <!-- <span class="text-info"> 
                                        <a href="http://localhost:8000/compareac" class="compare">Compare AC</a>
                                    </span>-->
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="map-inner">
                        <div class="snip-inner" id="snip-inner" >
                            <section class="statistics mt-2" id="eo-g">
                                <div class="inv-area">
                                    <ul class="mt-4 mb-2 pl-0">
                                        <li><a href="{{route('inventory')}}" class="mt-2">Inventory</a></li>
                                        <li><a href="{{route('security.arrangement')}}" class="mt-2">Security Arrangement</a></li>
                                        <li><a href="{{route('others')}}" class="mt-2">Others</a></li>
                                    </ul>
                                </div>
                            </section>                      
                        </div>
                    </div>  
                    <div class="map mt-3" id="map"></div>
                    <div class="text-center mt-3 ftr-buttons"  style="text-align:center">
                    </div>
                    <div class="map" id="map_out"></div>
                    <div class="map-color float-right" style="bottom: -15px;">
                        <ul class="list-inline">
                            <li class="list-inline-item"><strong></strong> </li>
                            <li class="list-inline-item"><i class="fa fa-circle text-success"></i> Main</li>
                            <li class="list-inline-item"><i class="fa fa-circle text-dark" style="color:#000"></i> Model</li>
                            <li class="list-inline-item"><i class="fa fa-circle text-red" style="color:red"></i> Critical</li>
                            <li class="list-inline-item"><i class="fa fa-circle text-pink" style="color:#d0028a"></i> Vulnerable</li>
                            <li class="list-inline-item"><i class="fa fa-circle text-blue" style="color:#04195e"></i> Auxiliary</li>
                        </ul>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </section>
</main>
<div id="loader" style="display:none;">
    <div class="loader"><figure><img src="{{ asset('images/icons/loading-img.gif')}}" alt=""/></figure></div>
</div>
<input type="hidden" id="psdataval">
<input type="hidden" id="psfacility">
<!--modal-->
<!-- Modal -->
<div id="notification" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Notification</h4>
      </div>
      <div class="modal-body notify text-center">
          <img src="{{ asset('img/oops.png')}}" alt=""/>
          <p>This facility is not available in this polling station</p>
          
      </div>
      
    </div>

  </div>
</div>
@section('script')
@include('ATLAS.left-map')

<script src="{{asset('js/leaflet/leaflet-routing-machine.js')}}"></script>

<script src="//code.jquery.com/jquery-1.9.1.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script>
var j = jQuery.noConflict();
j(function() {
j("#slider-range").slider({
range: true,
        min: 0,
        max: 3000,
        values: [ 0, 3000 ],
        slide: function(event, ui) {
        j("#electorrange").val(ui.values[ 0 ] + "***" + ui.values[ 1 ]);
        j("#amount").val("Min " + ui.values[ 0 ] + " - Max " + ui.values[ 1 ]);
        }
});
j("#amount").val("Min " + j("#slider-range").slider("values", 0) +
        " - Max " + j("#slider-range").slider("values", 1));
});</script>
<script>
    function imgpathFunc(){
    return "http://10.199.104.241:82/";
    }
    function resetDropdown(txt){
    if (txt != 'amf'){
    $('#amf').val('0');
    }
    if (txt != 'emf'){
    $('#emf').val('0');
    }
    if (txt != 'pstype'){
    $('#pstype').val('0');
    }
    if (txt != 'psphoto'){
    $('#psphoto').val('0');
    }
    if (txt != 'psfilter'){
    $('#psfilter').val('0');
    }
    if (txt != 'ps'){
    $('#ps').val('');
    }
    if (txt != 'wstype'){
    $('#wstype').val('0');
    }
    if (txt != 'pwdfacility'){
    $('#pwdfacility').val('00');
    }
    }
    function getElectorByPSID(callback){
    jQuery.ajax({
    url: '{{route('getelectorbypsid')}}',
            type: 'POST',
            async:false,
            dataType: 'json',
            data: {_token:'{{csrf_token()}}', psid: callback},
            success:function(data)
            {
            $("#psdataval").val(data);
            },
            error: function(error) {
            console.log(error.responseText);
            }
    });
    }
    function getFacilityPSID(psid){
            jQuery.ajax({
                    url: '{{route('getfacilitylist')}}',
                    type: 'POST',
                    async:false,
                    dataType: 'json',
                    data: {_token:'{{csrf_token()}}', psid: psid},
                    success:function(data)
                    {
                    var jsonString = JSON.stringify(data);
                    //alert(jsonString);
                    //console.log(data);
                    //var jsonText = $.parseJSON(data);
                    $("#psfacility").val(jsonString);
                    },
                    error: function(error) {
                    console.log(error.responseText);
                    }
            });
    }
    $("#snip").click(function(){
    $('#snip-inner').slideToggle();
    $("i", this).toggleClass("fa fa-bars fa fa-times");
    });</script>
<script type="text/javascript">
//Geo Location MAP
var map;
    $(function () {

    $("#loader").show();
    //resetDropdown('ac');  
    $('#pstype').val('0');
    $('#psfilter').val('0');
    var ac = $('#ac').val();
    var acname = $('select#ac option:selected').text();
    var statename = $('select#state option:selected').text();
    var distname = $('select#dist option:selected').text();
    var dist = $('#dist').val();
    var state = $('#state').val();
   
         $.ajax({
    url: "{{route('acpsdetails')}}",
            type: 'POST',
            data: {_token:'{{csrf_token()}}', state: state, dist: dist, ac: ac},
            success: function (result) {
            
            var container = L.DomUtil.get('map');
            if (container != null){ container._leaflet_id = null; }
            var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    osmAttrib = '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    osm = L.tileLayer(osmUrl, { maxZoom: 18, attribution: osmAttrib }),
                    //map id
                    map = new L.Map('map', { center: new L.LatLng(28.6337379, 77.1972581), zoom: 13, fullscreenControl: {
                    pseudoFullscreen: false
                    }, })
                    //Drawman Set
                    drawnItems = L.featureGroup().addTo(map);
            L.control.layers({
            'osm': osm.addTo(map),
                    "google": L.tileLayer('http://www.google.cn/maps/vt?lyrs=s@189&gl=cn&x={x}&y={y}&z={z}', {
                    attribution: 'google'
                    })
            }, { 'drawlayer': drawnItems }, {fullscreenControl: true}, { position: 'topleft', collapsed: false }).addTo(map);
            setTimeout(function(){ map.invalidateSize(true)}, 0);
            // run kml on leaflet
            var urllink = "<?php echo url('/'); ?>/kmlmap/" + state + "/AC/" + ac + ".kml";
            var runLayer = omnivore.kml(urllink, {async: true, })
                    .on('ready', function() {
                    map.fitBounds(runLayer.getBounds());
                    })
                    .addTo(map);
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/marker-icons/ac-icon.png')}}",
                    iconSize: [40, 50], // size of the icon
                    popupAnchor: [0, - 15]
            });
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();
            today = yyyy+ '-' + dd + '-' + mm;
            var candidate_list = '';
            var poll_date='<br/>';
            var total_candidate=0;
            if(result[7][0]!=undefined && result[7].length>0 && result[7][0]['poll_date']!=null){
                if(result[7][0]['poll_date']>=today){
                var poll_date='<p><b>Date of Election : <span class="text-blue">'+result[7][0]['poll_date']+'</span></b></p>';
                //if poll date annouce
                if(result[8]!=undefined){

                var total_candidate=result[8].length;

                candidate_list=' <div class="list-Candidate">\n\
                                            <h4 class="p-2">Candidate List</h4>\n\
                                            <ul class="pl-0 mb-0">';
                $.each(result[8], function (index, value) {
                    //var candidate_name = value.cand_name;
                    candidate_list += '<li><p><i class="fa fa-long-arrow-right text-blue"></i>'+value.cand_name+'</p></li>';
                //fruits.push(value.cand_name);
                });
                candidate_list+=' <p class="more-f mt-3 mb-0"><a href="#">View More</a></p>\n\
                                            </ul>\n\
                                        </div>';
                }
            }
            }            
            //console.log(total_candidate);
            //Ac Details
            //console.log(result[5][0]['electors_male']);
            if(result[5][0]['male']==null || result[5][0]['electors_male']==""){
                 var male="To be updated";
            }else{
                 var male=result[5][0]['male'];
            }
            if(result[5][0]['female']==null || result[5][0]['electors_female']==""){
                 var female="To be updated";
            }else{
                 var female=result[5][0]['female'];
            }
            if(result[5][0]['others']==null || result[5][0]['third_gender']==""){
                 var third_gender="To be updated";
            }else{
                 var third_gender=result[5][0]['others'];
            }
            if((result[5][0]['male_ratio']==null || result[5][0]['male_ratio']=="") && (result[5][0]['female_ratio']==null || result[5][0]['female_ratio']=="")){
                 var gender_male=0;
                 var gender_female=0;

            }else{
                 var gender_male=parseFloat(result[5][0]['male_ratio']).toFixed(2);
                 var gender_female=parseFloat(result[5][0]['female_ratio']).toFixed(2);
            }
            if(result[5][0]['total']==null || result[5][0]['total']==""){
                 var total_elector="To be Updated";
            }else{
                 var total_elector=result[5][0]['total'];
            }
            if(result[5][0]['pwd']==null || result[5][0]['pwd']==""){
                 var total_pwd="To be Updated";
            }else{
                 var total_pwd=result[5][0]['pwd'];
            }

            var jsonText = $.parseJSON(result[4]);
            //console.log(jsonText.data.length);
            if (jsonText.data.length!= undefined) {
                        //console.log('NO');
                        //variable is undefined or null
                        var ac1_arm_force_form2=jsonText.data[0].Form2;
                        var ac1_arm_force_form2A=jsonText.data[0].Form2A;
                        var ac1_arm_force_form3=jsonText.data[0].Form3;         
                    } else{
                        var ac1_arm_force_form2="To be Updated";
                        var ac1_arm_force_form2A="To be Updated";
                        var ac1_arm_force_form3="To be Updated";
                    }
            if (result[6][0]!=undefined) {
                        var census_population =result[6][0]['census_population'];
                        var projected_population =result[6][0]['projected_population'];
                        var ep_ratio =result[6][0]['ep_ratio'];
                        var revenue_district =result[6][0]['revenue_district'];
                        var tehsil_talkuas =result[6][0]['tehsil_talkuas'];
                        var gram_panchayat =result[6][0]['gram_panchayat'];
                        var villages =result[6][0]['villages'];
                        var municipal_corporations =result[6][0]['municipal_corporations'];
                        var municipalities =result[6][0]['municipalities'];
                        var post_offices =result[6][0]['post_offices'];
                        var police_stations =result[6][0]['police_stations'];
                        var district_no =result[6][0]['district_no'];

                    } else{
                        var census_population ='To be Updated';
                        var projected_population ='To be Updated';
                        var ep_ratio ='To be Updated';
                        var revenue_district ='To be Updated';
                        var tehsil_talkuas ='To be Updated';
                        var gram_panchayat ='To be Updated';
                        var villages ='To be Updated';
                        var municipal_corporations ='To be Updated';
                        var municipalities ='To be Updated';
                        var post_offices ='To be Updated';
                        var police_stations ='To be Updated';
                        var district_no ='To be Updated';
                    }

            //console.log(ac1_arm_force_form2);
            //var svoter=$result[4];
            var comparelink ='{!! url("compareac") !!}?state='+state+'&dist='+dist+'&ac='+ac;
            var contentString1 ='<div class="container">\n\
                                <div class="snip-pop mt-3">\n\
                                <div class="pro-head position-relative pt-2 px-3">\n\
                                    <h4 class="text-blue brb"> '+acname+'</h4>\n\
									<div class="comp more-f"><a href="'+comparelink+'" class="font-weight-bold" style="color:#ffffff" target="_blank">Compare AC</a></div>\n\
                                    <p class="mb-1"><b>State :</b> '+statename+'</p>\n\
                                    <p class="mb-1"><b>DIstrict :</b> '+distname+'</p>\n\
                                    '+poll_date+'\n\
                                </div>\n\
                                <div class="pop-sec pb-3">\n\
                                    <ul class="nav pb-3 nav-tabs" role="tablist">\n\
                                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#detailsacno1">Service Voter</a></li>\n\
                                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#electorsac1"> Electors</a></li>\n\
										<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#populationac1">Population</a></li>\n\
										<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#officersac1">Officers</a></li>\n\
										<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#demographicsac1">Demogrphic</a></li>\n\
                                    </ul>\n\
                                <div class="tab-content">\n\
                                    <div id="detailsacno1" class="details mt-2 container tab-pane active">\n\
                                         <p class="pt-1">Armed Force(Form2) <span class="text-blue" id="ac2_arm_force_form2">'+ac1_arm_force_form2+'</span></p>\n\
                                             <p class="pt-1">Armed Police Force(Form2A) <span class="text-blue" id="ac2_arm_force_form2A">'+ac1_arm_force_form2A+'</span></p>\n\
                                            <p class="pt-1">Govt Officials Serving Abroad.(Form3) <span class="text-blue" id="ac2_arm_force_form3">'+ac1_arm_force_form3+'</span></p>\n\
                                    </div>\n\
                                    <div id="electorsac1" class="details mt-2 container tab-pane fade">\n\
                                            <p class="pt-1">Total Electors <span class="text-blue">'+total_elector+'</span></p>\n\
                                            <p class="pt-1">Male <span class="text-blue">'+male+'</span></p>\n\
                                            <p class="pt-1">Female<span class="text-blue">'+female+'</span></p>\n\
                                            <p class="pt-1">Third gender <span class="text-blue">'+third_gender+'</span></p>\n\
                                            <p class="pt-1">Gender Ratio(Male/Female)  <span class="text-blue">'+gender_male+':'+gender_female+'</span></p>\n\
                                            <p class="pt-1">Total PwDs  <span class="text-blue">'+total_pwd+'</span></p>\n\
                                    </div>\n\
                                    <div id="populationac1" class="details mt-2 container tab-pane fade">\n\
                                         <p class="pt-1">2011 Census Population  <span class="text-blue"> '+census_population+'</span></p>\n\
                                            <p class="pt-1">Projected Population<span class="text-blue"> '+projected_population+'</span></p>\n\
                                            <p class="pt-1">EP Ratio <span class="text-blue"> '+ep_ratio+'</span></p>\n\
                                    </div>\n\
                                    <div id="officersac1" class="details mt-2 container tab-pane fade">\n\
                                        <p class="pt-1"> CEO/ DEO/ RO/ ERO/ Observer  <span class="text-blue"> 0</span></p>\n\
                                            <p class="pt-1">General Observer<span class="text-blue"> 0</span></p>\n\
                                            <p class="pt-1">Police Observer <span class="text-blue"> 0</span></p>\n\
                                            <p class="pt-1">Expenditure Observer<span class="text-blue"> 0</span></p>\n\
                                            <p class="pt-1">Awareness Observer <span class="text-blue"> 0</span></p>\n\
                                    </div>\n\
                                    <div id="demographicsac1" class="details mt-2 container tab-pane fade">\n\
                                         <p class="pt-1">Revenue Districts  <span class="text-blue"> '+revenue_district+'</span></p>\n\
                                            <p class="pt-1">Tehsils/ Talukas<span class="text-blue"> '+tehsil_talkuas+'</span></p>\n\
                                            <p class="pt-1">Gram panchayats <span class="text-blue"> '+gram_panchayat+'</span></p>\n\
                                            <p class="pt-1">Villages<span class="text-blue"> '+villages+'</span></p>\n\
                                            <p class="pt-1">Municipal Corporations <span class="text-blue"> '+municipal_corporations+'</span></p>\n\
                                            <p class="pt-1">Municipalities <span class="text-blue"> '+municipalities+'</span></p>\n\
                                            <p class="pt-1">Post offices<span class="text-blue"> '+post_offices+'</span></p>\n\
                                            <p class="pt-1">Police Stations <span class="text-blue"> '+police_stations+'</span></p>\n\
                                            <p class="pt-1">Eletoral Districts<span class="text-blue"> '+district_no+'</span></p>\n\
                                    </div>\n\
                                </div>\n\
                                </div>\n\
                                </div>\n\
                                </div>';
            var iconImage1 = L.icon({
            iconUrl: "{{asset('img/garuda/marker-icons/ac-icon.png')}}",
                    iconSize: [40, 40], // size of the icon
                    popupAnchor: [0, - 15]
            });
//            console.log(result[3]);
            if(result[3]!=null) { 
                var lattitude1=result[3][1];
                var longitude1=result[3][0];
                var newpopup1 = L.popup({
                        closeOnClick: false,
                        autoClose: false
                }).setContent(contentString1);
                var marker1 = L.marker([lattitude1, longitude1], {bounceOnAdd: false, icon: iconImage1});
                marker1.addTo(map);
                marker1.bindPopup(newpopup1);
            }
            $("#loader").hide();
            if (result != '')
            {
            acpscnt = result[0].length;
            $('#t_pscount strong').text('Total P.S');
            $('#t_pscount span').text(' : ' + acpscnt);
            $('#pwd_facility strong').css('display', 'none');
            $('#pwd_facility span').css('display', 'none');
            if (result[2] != '')
            {
            acpscnt = result[0].length;
            $('#textnew').show();
            $('#distText').show();
            $('#distText2').show();
            $('#acText').show();
            $('#acText2').show();
            $('#sttext').html($("#state option:selected").text());
            $('#dstext').show().html($("#dist option:selected").text());
            $('#dstext').show().html($("#dist option:selected").text());
            $('#act').show().html($("#ac option:selected").text());
            $('#t_pscount strong').text('Total P.S');
            $('#t_pscount span').text(' : ' + acpscnt);
            $('#pwd_facility strong').css('display', 'none');
            $('#pwd_facility span').css('display', 'none');
            $('#p_count').show();
            if (result[2][0].e_total != '' && result[2][0].e_total != null)
            {
            $('#e_count strong').text('Electors Count');
            $('#e_count span').text(' : ' + result[2][0].e_total);
            }
            else
            {
            $('#e_count strong').text('Electors Count');
            $('#e_count span').text(' : N/A');
            }
            if (result[2][0].p_total != '' && result[2][0].p_total != null)
            {
            $('#p_count strong').text('PWD Count');
            $('#p_count span').text(' : ' + result[2][0].p_total);
            }
            else
            {
            $('#p_count strong').text('PWD Count');
            $('#p_count span').text(' : N/A');
            }
            }
           // console.log(result[0]);
            $.each(result[0], function (index, value) {
            var rsdata = getElectorByPSID(value.id);
            var fmdata=getFacilityPSID(value.id);
            //console.log(value.id);
           // console.log(stringify(fmdata));
            var data = {lat: parseFloat(value.lattitude), lng: parseFloat(value.longitude)};
            if (value.ps_type_id == 1)
            {
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/polling_station/ps.blue.png')}}",
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
            }
            else if (value.ps_type_id == 2)
            {
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/polling_station/ps.pink.png')}}",
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
            }
            else if (value.ps_type_id == 3)
            {
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/polling_station/ps.red.png')}}",
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
            }
            else if (value.ps_type_id == 4)
            {
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/polling_station/ps-black.png')}}",
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
            }
            else if (value.ps_type_id == 5)
            {
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/polling_station/ps.green.png')}}",
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
            }
            var psidd = window.btoa(value.id);
            var psno = window.btoa(value.ps_no);
            var acno = window.btoa(value.ac_no);
            if (value.ps_address != null && value.ps_address != '')
            {
            var psaddress = value.ps_address;
            }
            else
            {
            var psaddress = 'To be Updated';
            }
            var psid = value.id;
            var imgpath = imgpathFunc();
            
            var psfacility = $("#psfacility").val();
            var array_psfacility=$.parseJSON(psfacility);
            //console.log(array_psfacility);
            if(array_psfacility.length!=0 && array_psfacility[0]!=undefined && array_psfacility[0]['facility_master_id']==10){
                var electricity='<i class="fa fa-check text-success"></i>';
            }else{
                var electricity='<i class="fa fa-close text-danger"></i>';
            }
            if(array_psfacility.length!=0 && array_psfacility[1]!=undefined && array_psfacility[1]['facility_master_id']==13){
                var road_connectivity='<i class="fa fa-check text-success"></i>';
            }else{
                var road_connectivity='<i class="fa fa-close text-danger"></i>';
            }
            if(array_psfacility.length!=0 && array_psfacility[2]!=undefined && array_psfacility[2]['facility_master_id']==17){
                var internet='<i class="fa fa-check text-success"></i>';
            }else{
                var internet='<i class="fa fa-close text-danger"></i>';
            }
            if(array_psfacility.length!=0 && array_psfacility[3]!=undefined && array_psfacility[3]['facility_master_id']==22){
                var water='<i class="fa fa-check text-success"></i>';
            }else{
                var water='<i class="fa fa-close text-danger"></i>';
            }
            if(array_psfacility.length!=0 && array_psfacility[4]!=undefined && array_psfacility[4]['facility_master_id']==25){
                var toilet='<i class="fa fa-check text-success"></i>';
            }else{
                var toilet='<i class="fa fa-close text-danger"></i>';
            }
            //console.log(water);
            var psdatas = $("#psdataval").val();
            var eleceach = psdatas.split("***");
            //console.log(eleceach);
            if (eleceach[0] != 'NA' && eleceach[0] != undefined){
            var ps_id = eleceach[0];
            } else {
            var ps_id = "NA";
            }
            if (eleceach[1] != 'NA' && eleceach[1] != undefined){
            var electors_total = eleceach[1];
            } else {
            var electors_total = "To be updated";
            }
            if (eleceach[2] != 'NA' && eleceach[2] != undefined){
            var electors_male = eleceach[2];
            } else {
            var electors_male = "To be updated";
            }
            if (eleceach[3] != 'NA' && eleceach[3] != undefined){
            var electors_female = eleceach[3];
            } else {
            var electors_female = "To be updated";
            }
            if (eleceach[4] != 'NA' && eleceach[4] != undefined){
            var electors_other = eleceach[4];
            } else {
            var electors_other = "To be updated";
            }
            if (eleceach[5] != 'NA' && eleceach[5] != undefined){
            var no_of_pwd_voters = eleceach[5];
            } else {
            var no_of_pwd_voters = "To be updated";
            }
            if (eleceach[6] != 'NA' && eleceach[6] != undefined){
            var dd = eleceach[6];
            i = '<img src=' + '"' + imgpath + eleceach[6] + '"' + ' class="img-fluid"/>';
            } else {
            var dd = "NA";
            i = '<img src="{{asset('img / pro - img.jpg')}}" class="img-fluid"/>';
            }

            if (eleceach[0] != 'NA' && eleceach[0] != undefined){
            var ps_id = eleceach[0];
            } else {
            var ps_id = "NA";
            }
            var psdtlink ='{!! url("psdetailsmarker") !!}/'+psidd+'/'+acno+'/'+psno;
            var contentString ='<div class="container">\n\
                                <div class="snip-pop mt-3">\n\
                                <div class="pro-head">\n\
                                   <h4 class="text-blue brb">' + value.ps_name_en + '</h4>\n\
                                   <p class="mb-1"><b>PS Type : <span class="text-blue">' + value.ps_type+ '</span></b>\n\
                                   </p><p class="mb-1"><b>Address :</b> ' + psaddress + '</p>\n\
                                   '+poll_date+'\n\
                                </div>\n\
                                <div class="pop-sec pb-3">\n\
                                    <ul class="nav pb-3 nav-tabs" role="tablist">\n\
                                        <li class="nav-item"><a class="nav-link active mr-2" data-toggle="tab" href="#detailsac'+ psid +'">Basic Details</a></li>\n\
                                        <li class="nav-item"><a class="nav-link mr-2" data-toggle="tab" href="#facilityac'+ psid +'">Facility</a></li>\n\
                                        <li class="nav-item"><a class="nav-link mr-2" data-toggle="tab" href="#acc'+ psid +'">AC Details</a></li>\n\
                                    </ul>\n\
                                <div class="tab-content">\n\
                                    <div id="detailsac'+ psid +'" class="details mt-2 container tab-pane active">\n\
                                        <p class="pt-1">No. of electors  <span class="text-blue">: ' + electors_total + '</span></p>\n\
                                        <p>No. of pwd electors <span class="text-blue">: ' + no_of_pwd_voters + '</span></p>\n\
                                        <p>No. of electors male <span class="text-blue">: ' + electors_male + '</span></p>\n\
                                        <p>No. of electors female <span class="text-blue">: ' + electors_female + '</span></p>\n\
                                        <p>No. of electors third gender <span class="text-blue">: ' + electors_other + '</span></p>\n\
                                        <div class="share mt-3 mb-4">\n\
                                            <ul class="share-icon">\n\
                                            <li><span>Share With :</span></li>\n\
                                            <li class="ml-2"><a href="https://api.whatsapp.com/send?text='+psdtlink+'" target="_blank"><i class="fa fa-whatsapp"></i></a></li>\n\
                                            <li><a href="https://twitter.com/intent/tweet?text='+psdtlink+'"  target="_blank"><i class="fa fa-twitter"></i></a></li>\n\
                                            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>\n\
                                            <li><a href="#"><i class="fa fa-instagram"></i></a></li>\n\
                                            </ul>\n\
                                        </div>\n\
                                    </div>\n\
                                    <div id="facilityac'+ psid +'" class="container facility tab-pane fade">\n\
                                        <p class="mt-2"><img src="{{asset('images/icons/water.png')}}" class="img-fluid"/> Water '+water+'</p>\n\
                                        <p><img src="{{asset('images/icons/eletricity.png')}}" class="img-fluid"/> Electricity '+electricity+'</p>\n\
                                        <p><img src="{{asset('images/icons/toilate.png')}}" class="img-fluid"/> Toilet '+toilet+'</p>\n\
                                        <p><img src="{{asset('images/icons/parking.png')}}" class="img-fluid"/> Internet '+internet+'</p>\n\
                                        <p><img src="{{asset('images/icons/parking.png')}}" class="mg-fluid"/> Road Connectivity '+road_connectivity+'</p>\n\
                                        <p class="more-f text-center"><a href="{{url('psdetailsmarker')}}/'+psidd+'/'+acno+'/'+psno+'">More facility Details</a></p>\n\
                                    </div>\n\
                                    <div id="acc'+ psid +'" class="ac container tab-pane fade">\n\
                                        <div class="row pt-2 pb-2">\n\
                                            <div class="col-sm-7">\n\
                                                <p>AC Name </p>\n\
                                                <p>District Name </p>\n\
                                                <p>Total Number of Candidate </p>\n\
                                            </div>\n\
                                            <div class="col-sm-5">\n\
                                                <p>: <span>'+acname+'</span></p>\n\
                                                <p>: <span>'+distname+'</span></p>\n\
                                                <p>: <span>'+total_candidate+'</span></p>\n\
                                            </div>\n\
                                        </div>\n\
                                        '+candidate_list+'\n\
                                    </div>\n\
                                </div>\n\
                                </div>\n\
                                </div>\n\
                                </div>';
             var newpopup = L.popup({
            closeOnClick: false,
                    autoClose: false
            }).setContent(contentString);
            var marker = L.marker([value.lattitude, value.longitude], {bounceOnAdd: false, icon: iconImage});
            marker.addTo(map);
            marker.bindPopup(newpopup);
            
            });
            $('#ps').empty();
            var ps = '<option value="">Select Polling Station</option>';
            $.each(result[1], function (index, value) {
            var pwd = value.is_pwd_facility_available;
            if (pwd == 0)
            {
            ps = ps + '<option value="' + value.id + '#' + value.lattitude + '#' + value.longitude + '#' + value.ps_type_id + '" >' + value.ps_no + '-' + value.ps_name_en + '</option>';
            }
            else
            {
            ps = ps + '<option  value="' + value.id + '#' + value.lattitude + '#' + value.longitude + '#' + value.ps_type_id + '" >' + value.ps_no + '-' + value.ps_name_en + '<i>&#xf193;</i> </option>';
            }
            });
            $('#ps').append(ps);
            }
            else
            {
            alert('No polling station available');
            }
            }
    });
        });
    //start ps onchange
    $('select#ps').change(function () {
    if(map != '' && map != null)
        {
            map.remove();
        }
    var acname = $('select#ac option:selected').text();
    var state = $('select#state').val();
    var ac = $('select#ac option:selected').val();
    resetDropdown('ps');
    var container = L.DomUtil.get('map');
    if (container != null){ container._leaflet_id = null; }
    var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            osmAttrib = '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            osm = L.tileLayer(osmUrl, { maxZoom: 18, attribution: osmAttrib });
            //map id
            map = new L.Map('map', { center: new L.LatLng(28.6337379, 77.1972581), zoom: 13, draggable: true, fullscreenControl: {
            pseudoFullscreen: false
            }, });
            //Drawman Set
            var drawnItems = L.featureGroup().addTo(map);
    L.control.layers({
    'osm': osm.addTo(map),
            "google": L.tileLayer('http://www.google.cn/maps/vt?lyrs=s@189&gl=cn&x={x}&y={y}&z={z}', {
            attribution: 'google'
            })
    }, { 'drawlayer': drawnItems }, {fullscreenControl: true}, { position: 'topleft', collapsed: false }).addTo(map);
    setTimeout(function(){ map.invalidateSize(true)}, 0);
    // run kml on leaflet
    var urllink = "<?php echo url('/'); ?>/kmlmap/" + state + "/AC/" + ac + ".kml";
    var runLayer = omnivore.kml(urllink, {async: true, })
            .on('ready', function() {
            map.fitBounds(runLayer.getBounds());
            })
            .addTo(map);
    //Start Control Icon - search and other icon
    map.addControl(new L.Control.Draw({
    edit: {
    featureGroup: drawnItems,
            poly: {
            allowIntersection: false
            }
    },
            draw: {
            position : 'topleft',
                    polyline : true,
                    polygon: false,
                    rectangle : false,
                    circle : false,
                    circlemarker: false,
                    marker: {
                    // marker options here
                    },
            }
    }));
    //End Control Icon - search and other icon
    var state = $("#state :selected").val();
    var dist = $("#dist :selected").val();
    var distname = $("#dist :selected").text();
    var acno = $("#ac :selected").val();
     if (state == '0'){
        $(this).prop('selectedIndex',0);
        $('#notification .modal-body p').text('Please select state first');
    $('#notification').modal('toggle');
    return false;
    }
    if (dist == '0'){
        $(this).prop('selectedIndex',0);
        $('#notification .modal-body p').text('Please select district first');
    $('#notification').modal('toggle');
    return false;
    }
    if (ac == '0'){
        $(this).prop('selectedIndex',0);
        $('#notification .modal-body p').text('Please select ac first');
    $('#notification').modal('toggle');
    return false;
    }
    
    $("#loader").show();
    var alldata = $(this).val();
    var psdata = alldata.split('#');
    var latitude = parseFloat(psdata[1]);
    var long = parseFloat(psdata[2]);
    var ps_type_id = parseFloat(psdata[3]);
    var psid = psdata[0];
    var data = {lat: latitude, lng: long};
    var layerscontrol;
    var markercontrol;
    //search
    var searchControl = new L.esri.Controls.Geosearch().addTo(map);
    var results = new L.LayerGroup().addTo(map);
    searchControl.on('results', function(data){
//          results.clearLayers();
//          map.removeLayer(results);
    if (layerscontrol != '' && layerscontrol != undefined)
    {
    map.removeControl(layerscontrol);
    layerscontrol = null;
    }
    if (markercontrol != '' && markercontrol != undefined)
    {
    map.removeControl(markercontrol);
    markercontrol = null;
    }
    for (var i = data.results.length - 1; i >= 0; i--) {
    markercontrol = L.marker(data.results[i].latlng);
    results.addLayer(markercontrol);
    var lat = data.results[i].latlng.lat;
    var lng = data.results[i].latlng.lng;
	setTimeout(function(){ map.invalidateSize(true)}, 0);
    layerscontrol = L.Routing.control({
    waypoints: [
            L.latLng(lat, lng),
            L.latLng(latitude, long)
    ]
    }).addTo(map);
    }
    });
    //search end
    if (ps_type_id == 1)
    {
    var iconImage = L.icon({
    iconUrl: "{{asset('img/garuda/polling_station/ps.blue.png')}}",
            iconSize: [40, 60], // size of the icon
            popupAnchor: [0, - 15]
    });
    }
    else if (ps_type_id == 2)
    {
    var iconImage = L.icon({
    iconUrl: "{{asset('img/garuda/polling_station/ps.pink.png')}}",
            iconSize: [40, 60], // size of the icon
            popupAnchor: [0, - 15]
    });
    }
    else if (ps_type_id == 3)
    {
    var iconImage = L.icon({
    iconUrl: "{{asset('img/garuda/polling_station/ps.red.png')}}",
            iconSize: [40, 60], // size of the icon
            popupAnchor: [0, - 15]
    });
    }
    else if (ps_type_id == 4)
    {
    var iconImage = L.icon({
    iconUrl: "{{asset('img/garuda/polling_station/ps-black.png')}}",
            iconSize: [40, 60], // size of the icon
            popupAnchor: [0, - 15]
    });
    }
    else if (ps_type_id == 5)
    {
    var iconImage = L.icon({
    iconUrl: "{{asset('img/garuda/polling_station/ps.green.png')}}",
            iconSize: [40, 60], // size of the icon
            popupAnchor: [0, - 15]
    });
    }

    $.ajax({
    url: "{{url('pollingdetails')}}",
            type: 'POST',
            data: {_token:'{{csrf_token()}}', ps_id: psid, onchange :1, acno:acno, state: state, dist: dist},
            success: function (result) {
            if(result[0].length>0)
            {
            var rsdata = getElectorByPSID(result[0][0]['id']);
            var fmdata=getFacilityPSID(result[0][0]['id']);
        }
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();
            today = yyyy+ '-' + dd + '-' + mm;
            var candidate_list = '';
            var poll_date='<br/>';
            var total_candidate=0;
            if(result[2][0]!=undefined && result[2].length>0 && result[2][0]['poll_date']!=null){
                if(result[2][0]['poll_date']>=today){
                var poll_date='<p><b>Date of Election : <span class="text-blue">'+result[2][0]['poll_date']+'</span></b></p>';
                //if poll date annouce
                if(result[3]!=undefined){
                var total_candidate=result[3].length;
                candidate_list=' <div class="list-Candidate">\n\
                                            <h4 class="p-2">Candidate List</h4>\n\
                                            <ul class="pl-0 mb-0">';
                $.each(result[3], function (index, value) {
                    //var candidate_name = value.cand_name;
                    candidate_list += '<li><p><i class="fa fa-long-arrow-right text-blue"></i>'+value.cand_name+'</p></li>';
                //fruits.push(value.cand_name);
                });
                candidate_list+=' <p class="more-f mt-3 mb-0"><a href="#">View More</a></p>\n\
                                            </ul>\n\
                                        </div>';
                }
                } 
            }
            //console.log(candidate_list);
            $("#loader").hide();
            //$('#t_pscount strong').text('PS Name');
            //$('#t_pscount span').text(' : ' + result[0][0].ps_name_en);
            $('#e_count strong').text('Electors Count');
            if (result[1][0].e_total != null){
            $('#e_count span').text(' : ' + result[1][0].e_total);
            } else {
            $('#e_count span').text(' : To be updated');
            }
            $('#p_count').show();
            $('#p_count strong').text('PWD Count');
            if (result[1][0].p_total != null){
            $('#p_count span').text(' : ' + result[1][0].p_total);
            } else {
            $('#p_count span').text(' : To be updated');
            }
            $('#textnew').show();
            $('#psText').show();
            $('#psText2').show();
            $('#pst').show();
            $('#pct').show().html($("#ps option:selected").text());
            $('#pwd_facility strong').css('display', 'none');
            $('#pwd_facility span').css('display', 'none');
             if(result[0].length>0)
            {
                var pslink = window.btoa(result[0][0]['id']);
                var psno = window.btoa(result[0][0]['ps_no']);
                var acno = window.btoa(result[0][0]['ac_no']);
                if (result[0][0]['ps_address'] != null && result[0][0]['ps_address'] != '')
                {
                var psaddress = result[0][0]['ps_address'];
                }
                else
                {
                var psaddress = 'Not available';
                }
                var psid = result[0][0]['id'];
            }
            var imgpath = imgpathFunc();
            var psdatas = $("#psdataval").val();
            var eleceach = psdatas.split("***");
            if (eleceach[0] != 'NA' && eleceach[0] != undefined){
            var ps_id = eleceach[0];
            } else {
            var ps_id = "NA";
            }
            if (eleceach[1] != 'NA' && eleceach[1] != undefined){
            var electors_total = eleceach[1];
            } else {
            var electors_total = "To be updated";
            }
            if (eleceach[2] != 'NA' && eleceach[2] != undefined){
            var electors_male = eleceach[2];
            } else {
            var electors_male = "To be updated";
            }
            if (eleceach[3] != 'NA' && eleceach[3] != undefined){
            var electors_female = eleceach[3];
            } else {
            var electors_female = "To be updated";
            }
            if (eleceach[4] != 'NA' && eleceach[4] != undefined){
            var electors_other = eleceach[4];
            } else {
            var electors_other = "To be updated";
            }
            if (eleceach[5] != 'NA' && eleceach[5] != undefined){
            var no_of_pwd_voters = eleceach[5];
            } else {
            var no_of_pwd_voters = "To be updated";
            }
            if (eleceach[6] != 'NA' && eleceach[6] != undefined){
            var dd = eleceach[6];
            i = '<img src=' + '"' + imgpath + eleceach[6] + '"' + ' class="img-fluid"/>';
            } else {
            var dd = "NA";
            i = '<img src="{{asset('img / pro - img.jpg')}}" class="img-fluid"/>';
            }


            var psfacility = $("#psfacility").val();
            var array_psfacility=$.parseJSON(psfacility);
            //console.log(array_psfacility);
            if(array_psfacility.length!=0 && array_psfacility[0]!=undefined && array_psfacility[0]['facility_master_id']==10){
                var electricity='<i class="fa fa-check text-success"></i>';
            }else{
                var electricity='<i class="fa fa-close text-danger"></i>';
            }
            if(array_psfacility.length!=0 && array_psfacility[1]!=undefined && array_psfacility[1]['facility_master_id']==13){
                var road_connectivity='<i class="fa fa-check text-success"></i>';
            }else{
                var road_connectivity='<i class="fa fa-close text-danger"></i>';
            }
            if(array_psfacility.length!=0 && array_psfacility[2]!=undefined && array_psfacility[2]['facility_master_id']==17){
                var internet='<i class="fa fa-check text-success"></i>';
            }else{
                var internet='<i class="fa fa-close text-danger"></i>';
            }
            if(array_psfacility.length!=0 && array_psfacility[3]!=undefined && array_psfacility[3]['facility_master_id']==22){
                var water='<i class="fa fa-check text-success"></i>';
            }else{
                var water='<i class="fa fa-close text-danger"></i>';
            }
            if(array_psfacility.length!=0 && array_psfacility[4]!=undefined && array_psfacility[4]['facility_master_id']==25){
                var toilet='<i class="fa fa-check text-success"></i>';
            }else{
                var toilet='<i class="fa fa-close text-danger"></i>';
            }
            //console.log(water);
            var psdtlink ='{!! url("psdetailsmarker") !!}/'+pslink+'/'+acno+'/'+psno;
            var contentString ='<div class="container">\n\
                                <div class="snip-pop mt-3">\n\
                                <div class="pro-head">\n\
                                   <h4 class="text-blue brb">' + result[0][0]['ps_name_en'] + '</h4>\n\
                                   <p class="mb-1"><b>PS Type : <span class="text-blue">' + result[0][0]['ps_type']+ '</span></b>\n\
                                   </p><p class="mb-1"><b>Address :</b> ' + psaddress + '</p>\n\
                                  '+poll_date+'\n\
                                </div>\n\
                                <div class="pop-sec pb-3">\n\
                                    <ul class="nav pb-3 nav-tabs" role="tablist">\n\
                                        <li class="nav-item"><a class="nav-link mr-2 active" data-toggle="tab" href="#detailsps'+ result[0][0]['id'] +'">Basic Details</a></li>\n\
                                        <li class="nav-item"><a class="nav-link mr-2" data-toggle="tab" href="#facilityps'+ result[0][0]['id'] +'">Facility</a></li>\n\
                                        <li class="nav-item"><a class="nav-link mr-2" data-toggle="tab" href="#acps'+ result[0][0]['id'] +'">AC Details</a></li>\n\
                                    </ul>\n\
                                <div class="tab-content">\n\
                                    <div id="detailsps'+ result[0][0]['id'] +'" class="details mt-2 container tab-pane active">\n\
                                        <p class="pt-1">No. of electors  <span class="text-blue">: ' + electors_total + '</span></p>\n\
                                        <p>No. of pwd electors <span class="text-blue">: ' + no_of_pwd_voters + '</span></p>\n\
                                        <p>No. of electors male <span class="text-blue">: ' + electors_male + '</span></p>\n\
                                        <p>No. of electors female <span class="text-blue">: ' + electors_female + '</span></p>\n\
                                        <p>No. of electors third gender <span class="text-blue">: ' + electors_other + '</span></p>\n\
                                        <div class="share mt-3 mb-4">\n\
                                            <ul class="share-icon">\n\
                                            <li><span>Share With :</span></li>\n\
                                            <li class="ml-2"><a href="https://api.whatsapp.com/send?text='+psdtlink+'" target="_blank"><i class="fa fa-whatsapp"></i></a></li>\n\
                                            <li><a href="https://twitter.com/intent/tweet?text='+psdtlink+'"  target="_blank"><i class="fa fa-twitter"></i></a></li>\n\
                                            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>\n\
                                            <li><a href="#"><i class="fa fa-instagram"></i></a></li>\n\
                                            </ul>\n\
                                        </div>\n\
                                    </div>\n\
                                    <div id="facilityps'+ result[0][0]['id'] +'" class="container facility tab-pane fade">\n\
                                        <p class="mt-2"><img src="{{asset('images/icons/water.png')}}" class="img-fluid"/> Water '+water+'</p>\n\
                                        <p><img src="{{asset('images/icons/eletricity.png')}}" class="img-fluid"/> Electricity '+electricity+'</p>\n\
                                        <p><img src="{{asset('images/icons/toilate.png')}}" class="img-fluid"/> Toilet '+toilet+'</p>\n\
                                        <p><img src="{{asset('images/icons/parking.png')}}" class="img-fluid"/> Interet '+internet+'</p>\n\
                                        <p><img src="{{asset('images/icons/pwd.png')}}" class="mg-fluid"/> Road Connectivity '+road_connectivity+'</p>\n\
                                        <p class="more-f text-center"><a href="{{url('psdetailsmarker')}}/'+pslink+'/'+acno+'/'+psno+'">More facility Details</a></p>\n\
                                    </div>\n\
                                    <div id="acps'+ result[0][0]['id'] +'" class="ac container tab-pane fade">\n\
                                        <div class="row pt-2 pb-2">\n\
                                            <div class="col-sm-7">\n\
                                                <p>AC Name </p>\n\
                                                <p>District Name </p>\n\
                                                <p>Total number of Candidate </p>\n\
                                            </div>\n\
                                            <div class="col-sm-5">\n\
                                                <p>: <span>'+acname+'</span></p>\n\
                                                <p>: <span>'+distname+'</span></p>\n\
                                                <p>: <span>'+total_candidate+'</span></p>\n\
                                            </div>\n\
                                        </div>\n\
                                       '+candidate_list+'\n\
                                    </div>\n\
                                </div>\n\
                                </div>\n\
                                </div>\n\
                                </div>';
             var newpopup = L.popup({
            closeOnClick: false,
                    autoClose: true
            }).setContent(contentString);
            var marker = L.marker([latitude, long], {bounceOnAdd: false, icon: iconImage});
            marker.addTo(map);
            marker.bindPopup(newpopup);
            //marker.dragging.enable();
			L.Routing.errorControl(control, {
            header: 'Routing error',
            formatMessage(error) {
                if (error.status < 0) {
                    return 'Calculating the route caused an error. Technical description follows:  <code><pre>' +
                        error.message + '</pre></code';
						alert('ssss');
                } else {
                    return 'The route could not be calculated. ' +
                        error.message;
                }
            }
        }).addTo(map);
            }, error: function(error) {
    console.log(error.responseText);
    }
    });
    });
    //end ps onchange 
    //start pstype onchange 
    $('select#pstype').change(function () {
    if(map != '' && map != null)
        {
            map.remove();
        }
    resetDropdown('pstype');
    var acname = $('select#ac option:selected').text();
    var ac = $('select#ac option:selected').val();
    var distname = $('select#dist option:selected').text();
    var state = $('select#state').val();
    var ps_type_id = $(this).val();
    //alert(ps_type_id);            
    $("#numberCount").show();
    var state = $('#state').val();
    var dist = 0;
    var ac = 0;
    var acno = 0;
    var dist = $('#dist').val();
    var ac = $('#ac').val();
     if (state == '0'){
        $(this).prop('selectedIndex',0);
        $('#notification .modal-body p').text('Please select state first');
    $('#notification').modal('toggle');
    return false;
    }
    if (dist == '0'){
        $(this).prop('selectedIndex',0);
        $('#notification .modal-body p').text('Please select district first');
    $('#notification').modal('toggle');
    return false;
    }
    if (ac == '0'){
        $(this).prop('selectedIndex',0);
        $('#notification .modal-body p').text('Please select ac first');
    $('#notification').modal('toggle');
    return false;
    }
    $("#loader").show();
    var acno = $("#ac :selected").val();
    $.ajax({
    url: "{{url('pollingdetails')}}",
            type: 'POST',
            data: {_token:'{{csrf_token()}}', pstypeid: ps_type_id, onchange :2, state :state, dist:dist, acno :acno},
            success: function (result) {
//                    //console.log(result);exit;
            $("#loader").hide();
            var container = L.DomUtil.get('map');
            if (container != null){ container._leaflet_id = null; }
            var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    osmAttrib = '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    osm = L.tileLayer(osmUrl, { maxZoom: 18, attribution: osmAttrib });
                    //map id
                    map = new L.Map('map', { center: new L.LatLng(28.6337379, 77.1972581), zoom: 13, fullscreenControl: {
                    pseudoFullscreen: false
                    }, });
                    //Drawman Set
                   var drawnItems = L.featureGroup().addTo(map);
            L.control.layers({
            'osm': osm.addTo(map),
                    "google": L.tileLayer('http://www.google.cn/maps/vt?lyrs=s@189&gl=cn&x={x}&y={y}&z={z}', {
                    attribution: 'google'
                    })
            }, { 'drawlayer': drawnItems }, {fullscreenControl: true}, { position: 'topleft', collapsed: false }).addTo(map);
            setTimeout(function(){ map.invalidateSize(true)}, 0);
            // run kml on leaflet
            var urllink = "<?php echo url('/'); ?>/kmlmap/" + state + "/AC/" + ac + ".kml";
            var runLayer = omnivore.kml(urllink, {async: true, })
                    .on('ready', function() {
                    map.fitBounds(runLayer.getBounds());
                    })
                    .addTo(map);
            $('#psText2').hide();
            $('#psText').hide();
            $('#pct').hide();

            var fmdata=getFacilityPSID(result[0][0]['id']);
            //console.log(result[2]);
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();
            today = yyyy+ '-' + dd + '-' + mm;
//            console.log(today);

            var candidate_list = '';
            var poll_date='<br/>';
            var total_candidate=0;
            if(result[2][0]!=undefined && result[2].length>0 && result[2][0]['poll_date']!=null){
                if(result[2][0]['poll_date']>=today){
                var poll_date='<p><b>Date of Election : <span class="text-blue">'+result[2][0]['poll_date']+'</span></b></p>';
                //if poll date annouce
                if(result[3]!=undefined){
                var total_candidate=result[3].length;
                candidate_list=' <div class="list-Candidate">\n\
                                            <h4 class="p-2">Candidate List</h4>\n\
                                            <ul class="pl-0 mb-0">';
                $.each(result[3], function (index, value) {
                    //var candidate_name = value.cand_name;
                    candidate_list += '<li><p><i class="fa fa-long-arrow-right text-blue"></i>'+value.cand_name+'</p></li>';
                //fruits.push(value.cand_name);
                });
                candidate_list+=' <p class="more-f mt-3 mb-0"><a href="#">View More</a></p>\n\
                                            </ul>\n\
                                        </div>';
                }           
                }             
            }

            var psfacility = $("#psfacility").val();
            var array_psfacility=$.parseJSON(psfacility);
            //console.log(array_psfacility);
            if(array_psfacility.length!=0 && array_psfacility[0]!=undefined && array_psfacility[0]['facility_master_id']==10){
                var electricity='<i class="fa fa-check text-success"></i>';
            }else{
                var electricity='<i class="fa fa-close text-danger"></i>';
            }
            if(array_psfacility.length!=0 && array_psfacility[1]!=undefined && array_psfacility[1]['facility_master_id']==13){
                var road_connectivity='<i class="fa fa-check text-success"></i>';
            }else{
                var road_connectivity='<i class="fa fa-close text-danger"></i>';
            }
            if(array_psfacility.length!=0 && array_psfacility[2]!=undefined && array_psfacility[2]['facility_master_id']==17){
                var internet='<i class="fa fa-check text-success"></i>';
            }else{
                var internet='<i class="fa fa-close text-danger"></i>';
            }
            if(array_psfacility.length!=0 && array_psfacility[3]!=undefined && array_psfacility[3]['facility_master_id']==22){
                var water='<i class="fa fa-check text-success"></i>';
            }else{
                var water='<i class="fa fa-close text-danger"></i>';
            }
            if(array_psfacility.length!=0 && array_psfacility[4]!=undefined && array_psfacility[4]['facility_master_id']==25){
                var toilet='<i class="fa fa-check text-success"></i>';
            }else{
                var toilet='<i class="fa fa-close text-danger"></i>';
            }



            if (result != '')
            {
            var typecnt = result[0].length;
            $('#t_pscount strong').text('Total P.S');
            $('#t_pscount span').text(' : ' + typecnt);
            $('#pwd_facility strong').css('display', 'none');
            $('#pwd_facility span').css('display', 'none');
            if (result[1] != '')
            {
            $('#p_count').show();
            if (result[1][0].e_total != '' && result[1][0].e_total != null)
            {
            $('#e_count strong').text('Electors Count');
            $('#e_count span').text(' : ' + result[1][0].e_total);
            }
            else
            {
            $('#e_count strong').text('Electors Count');
            $('#e_count span').text(' : To be updated');
            }
            if (result[1][0].p_total != '' && result[1][0].p_total != null)
            {
            $('#p_count strong').text('PWD Count');
            $('#p_count span').text(' : ' + result[1][0].p_total);
            }
            else
            {
            $('#p_count strong').text('PWD Count');
            $('#p_count span').text(' : To be updated');
            }
            }
            $.each(result[0], function (index, value) {
            if (value.ps_type_id == 1)
            {
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/polling_station/ps.blue.png')}}",
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
            }
            else if (value.ps_type_id == 2)
            {
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/polling_station/ps.pink.png')}}",
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
            }
            else if (value.ps_type_id == 3)
            {
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/polling_station/ps.red.png')}}",
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
            }
            else if (value.ps_type_id == 4)
            {
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/polling_station/ps-black.png')}}",
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
            }
            else if (ps_type_id == 5)
            {
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/polling_station/green.png')}}",
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
            }
            var psid = window.btoa(value.id);
            var psno = window.btoa(value.ps_no);
            var acno = window.btoa(value.ac_no);
            if (value.ps_address != null && value.ps_address != '')
            {
            var psaddress = value.ps_address;
            }
            else
            {
            var psaddress = 'To be updated';
            }

            var rsdata = getElectorByPSID(value.id);
            var imgpath = imgpathFunc();
            var psdatas = $("#psdataval").val();
            var eleceach = psdatas.split("***");
            if (eleceach[0] != 'NA' && eleceach[0] != undefined){
            var ps_id = eleceach[0];
            } else {
            var ps_id = "NA";
            }
            if (eleceach[1] != 'NA' && eleceach[1] != undefined){
            var electors_total = eleceach[1];
            } else {
            var electors_total = "To be updated";
            }
            if (eleceach[2] != 'NA' && eleceach[2] != undefined){
            var electors_male = eleceach[2];
            } else {
            var electors_male = "To be updated";
            }
            if (eleceach[3] != 'NA' && eleceach[3] != undefined){
            var electors_female = eleceach[3];
            } else {
            var electors_female = "NA";
            }
            if (eleceach[4] != 'NA' && eleceach[4] != undefined){
            var electors_other = eleceach[4];
            } else {
            var electors_other = "To be updated";
            }
            if (eleceach[5] != 'NA' && eleceach[5] != undefined){
            var no_of_pwd_voters = eleceach[5];
            } else {
            var no_of_pwd_voters = "To be updated";
            }

            if (eleceach[6] != 'NA' && eleceach[6] != undefined){
            var dd = eleceach[6];
            i = '<img src=' + '"' + imgpath + eleceach[6] + '"' + ' class="img-fluid"/>';
            } else {
            var dd = "To be updated";
            i = '<img src="{{asset('img / pro - img.jpg')}}" class="img-fluid"/>';
            }
             var psdtlink ='{!! url("psdetailsmarker") !!}/'+psid+'/'+acno+'/'+psno;
            var contentString ='<div class="container">\n\
                                <div class="snip-pop mt-3">\n\
                                <div class="pro-head">\n\
                                   <h4 class="text-blue brb">' + value.ps_name_en + '</h4>\n\
                                   <p class="mb-1"><b>PS Type : <span class="text-blue">' + value.ps_type+ '</span></b>\n\
                                   </p><p class="mb-1"><b>Address :</b> ' + psaddress + '</p>\n\
                                  '+poll_date+'\n\
                                </div>\n\
                                <div class="pop-sec pb-3">\n\
                                    <ul class="nav pb-3 nav-tabs" role="tablist">\n\
                                        <li class="nav-item"><a class="nav-link mr-2 active" data-toggle="tab" href="#detailspstype'+ value.id +'">Basic Details</a></li>\n\
                                        <li class="nav-item"><a class="nav-link mr-2" data-toggle="tab" href="#facilitypstype'+ value.id +'">Facility</a></li>\n\
                                        <li class="nav-item"><a class="nav-link mr-2" data-toggle="tab" href="#acpstype'+ value.id +'">AC Details</a></li>\n\
                                    </ul>\n\
                                <div class="tab-content">\n\
                                    <div id="detailspstype'+ value.id +'" class="details mt-2 container tab-pane active">\n\
                                        <p class="pt-1">No. of electors  <span class="text-blue">: ' + electors_total + '</span></p>\n\
                                        <p>No. of pwd electors <span class="text-blue">: ' + no_of_pwd_voters + '</span></p>\n\
                                        <p>No. of electors male <span class="text-blue">: ' + electors_male + '</span></p>\n\
                                        <p>No. of electors female <span class="text-blue">: ' + electors_female + '</span></p>\n\
                                        <p>No. of electors third gender <span class="text-blue">: ' + electors_other + '</span></p>\n\
                                        <div class="share mt-3 mb-4">\n\
                                            <ul class="share-icon">\n\
                                            <li><span>Share With :</span></li>\n\
                                            <li class="ml-2"><a href="https://api.whatsapp.com/send?text='+psdtlink+'" target="_blank"><i class="fa fa-whatsapp"></i></a></li>\n\
                                            <li><a href="https://twitter.com/intent/tweet?text='+psdtlink+'"  target="_blank"><i class="fa fa-twitter"></i></a></li>\n\
                                            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>\n\
                                            <li><a href="#"><i class="fa fa-instagram"></i></a></li>\n\
                                            </ul>\n\
                                        </div>\n\
                                    </div>\n\
                                    <div id="facilitypstype'+ value.id +'" class="container facility tab-pane fade">\n\
                                        <p class="mt-2"><img src="{{asset('images/icons/water.png')}}" class="img-fluid"/> Water '+water+'</p>\n\
                                        <p><img src="{{asset('images/icons/eletricity.png')}}" class="img-fluid"/> Electricity '+electricity+'</p>\n\
                                        <p><img src="{{asset('images/icons/toilate.png')}}" class="img-fluid"/> Toilet '+toilet+'</p>\n\
                                        <p><img src="{{asset('images/icons/parking.png')}}" class="img-fluid"/> Interet '+internet+'</p>\n\
                                        <p><img src="{{asset('images/icons/pwd.png')}}" class="mg-fluid"/> Road Connectivity '+road_connectivity+'</p>\n\
                                        <p class="more-f text-center"><a href="{{url('psdetailsmarker')}}/'+psid+'/'+acno+'/'+psno+'">More facility Details</a></p>\n\
                                    </div>\n\
                                    <div id="acpstype'+ value.id +'" class="ac container tab-pane fade">\n\
                                        <div class="row pt-2 pb-2">\n\
                                            <div class="col-sm-7">\n\
                                                <p>AC Name </p>\n\
                                                <p>District Name </p>\n\
                                                <p>Total number of Candidate </p>\n\
                                            </div>\n\
                                            <div class="col-sm-5">\n\
                                                <p>: <span>'+acname+'</span></p>\n\
                                                <p>: <span>'+distname+'</span></p>\n\
                                                <p>: <span>'+total_candidate+'</span></p>\n\
                                            </div>\n\
                                        </div>\n\
                                        '+candidate_list+'\n\
                                    </div>\n\
                                </div>\n\
                                </div>\n\
                                </div>\n\
                                </div>';
            var newpopup = L.popup({
            closeOnClick: false,
                    autoClose: true
            }).setContent(contentString);
            var marker = L.marker([value.lattitude, value.longitude], {bounceOnAdd: false, icon: iconImage});
            marker.addTo(map);
            marker.bindPopup(newpopup);
            });
            }
            else
            {
//            alert('No polling station available');
              $('#notification .modal-body p').text('No polling station available on selected PS type');
                 $('#notification').modal('toggle');
            }
            },
            error: function(error) {
            //console.log(error.responseText);
            console.log(error);
//            alert(error.responseText);
            }
    });
    });
    //end pstype onchange 
    //start electors filter onchange 
    //Onclick Electors range bar filter
    $('div#findElectorsByRangeBar').click(function () {
        if(map != '' && map != null)
        {
            map.remove();
        }
    resetDropdown('findElectorsByRangeBar');
    $("#numberCount").show();
    
    var acname = $('select#ac option:selected').text();
    var ac = $('select#ac option:selected').val();
    var distname = $('select#dist option:selected').text();
    var state = $('select#state').val();
    var dist = 0;
    var ac = 0;
    var acno = 0;
    var dist = $('#dist').val();
    var ac = $("#ac :selected").val();
    var electorrange = $('#electorrange').val();
    if (state == '0'){
        $(this).prop('selectedIndex',0);
        $('#notification .modal-body p').text('Please select state first');
    $('#notification').modal('toggle');
    return false;
    }
    if (dist == '0'){
        $(this).prop('selectedIndex',0);
        $('#notification .modal-body p').text('Please select district first');
    $('#notification').modal('toggle');
    return false;
    }
    if (ac == '0'){
        $(this).prop('selectedIndex',0);
        $('#notification .modal-body p').text('Please select ac first');
    $('#notification').modal('toggle');
    return false;
    }
    var rangeelc = "";
    if (electorrange == ""){
    rangeelc = "0***3000"
    } else {
    rangeelc = electorrange;
    }
    // alert(state+'-'+ dist +'-'+ ac); 
    $("#loader").show();
    var container = L.DomUtil.get('map');
    if (container != null){ container._leaflet_id = null; }
    var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            osmAttrib = '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            osm = L.tileLayer(osmUrl, { maxZoom: 18, attribution: osmAttrib });
            //map id
            map = new L.Map('map', { center: new L.LatLng(28.6337379, 77.1972581), zoom: 13, fullscreenControl: {
            pseudoFullscreen: false
            }, });
            //Drawman Set
            var drawnItems = L.featureGroup().addTo(map);
    L.control.layers({
    'osm': osm.addTo(map),
            "google": L.tileLayer('http://www.google.cn/maps/vt?lyrs=s@189&gl=cn&x={x}&y={y}&z={z}', {
            attribution: 'google'
            })
    }, { 'drawlayer': drawnItems }, {fullscreenControl: true}, { position: 'topleft', collapsed: false }).addTo(map);
    setTimeout(function(){ map.invalidateSize(true)}, 0);
    // run kml on leaflet
    var urllink = "<?php echo url('/'); ?>/kmlmap/" + state + "/AC/" + ac + ".kml";
    var runLayer = omnivore.kml(urllink, {async: true, })
            .on('ready', function() {
            map.fitBounds(runLayer.getBounds());
            })
            .addTo(map);
    $.ajax({
    url: "{{url('findElectorsByRangeBar')}}",
            type: 'POST',
            data: {_token:'{{csrf_token()}}', electorrange: rangeelc, state: state, dist: dist, ac: ac},
            success: function (result) {
            
            $("#loader").hide();
            $('#psText2').hide();
            $('#psText').hide();
            $('#pct').hide();
            $('#t_pscount').show();
            $('#t_pscount strong').text('Total P.S');
            $('#t_pscount span').text(' : ' + result.length);
            $('#e_count strong').text('Elector Range');
            $('#e_count span').text(' : ' + rangeelc.replace('***', " to "));
            $("#p_count").hide();
            //$('#p_count strong').text('');
            //$('#p_count span').text('');
            //$('#p_count span').text('');
            //console.log(result[0]['psid']);
            if(result.length>0 && result[0]!=undefined){
                psid=result[0]['psid'];
            }else{
                psid='0';
            }


            var fmdata=getFacilityPSID(psid);
            //console.log(result[2]);
            var candidate_list = '';
            var poll_date='<br/>';
            var total_candidate=0;
            /*
            if(result[2][0]!=undefined && result[2].length>0 && result[2][0]['poll_date']!=null){
                var poll_date='<p><b>Date of Election : <span class="text-blue">'+result[2][0]['poll_date']+'</span></b></p>';
                //if poll date annouce
                if(result[3]!=undefined){
                var total_candidate=result[8].length;
                candidate_list=' <div class="list-Candidate">\n\
                                            <h4 class="p-2">Candidate List</h4>\n\
                                            <ul class="pl-0 mb-0">';
                $.each(result[3], function (index, value) {
                    //var candidate_name = value.cand_name;
                    candidate_list += '<li><p><i class="fa fa-long-arrow-right text-blue"></i>'+value.cand_name+'</p></li>';
                //fruits.push(value.cand_name);
                });
                candidate_list+=' <p class="more-f mt-3 mb-0"><a href="#">View More</a></p>\n\
                                            </ul>\n\
                                        </div>';
                }
            }*/
            var psfacility = $("#psfacility").val();
            var array_psfacility=$.parseJSON(psfacility);
            //console.log(array_psfacility);
            if(array_psfacility.length!=0 && array_psfacility[0]!=undefined && array_psfacility[0]['facility_master_id']==10){
                var electricity='<i class="fa fa-check text-success"></i>';
            }else{
                var electricity='<i class="fa fa-close text-danger"></i>';
            }
            if(array_psfacility.length!=0 && array_psfacility[1]!=undefined && array_psfacility[1]['facility_master_id']==13){
                var road_connectivity='<i class="fa fa-check text-success"></i>';
            }else{
                var road_connectivity='<i class="fa fa-close text-danger"></i>';
            }
            if(array_psfacility.length!=0 && array_psfacility[2]!=undefined && array_psfacility[2]['facility_master_id']==17){
                var internet='<i class="fa fa-check text-success"></i>';
            }else{
                var internet='<i class="fa fa-close text-danger"></i>';
            }
            if(array_psfacility.length!=0 && array_psfacility[3]!=undefined && array_psfacility[3]['facility_master_id']==22){
                var water='<i class="fa fa-check text-success"></i>';
            }else{
                var water='<i class="fa fa-close text-danger"></i>';
            }
            if(array_psfacility.length!=0 && array_psfacility[4]!=undefined && array_psfacility[4]['facility_master_id']==25){
                var toilet='<i class="fa fa-check text-success"></i>';
            }else{
                var toilet='<i class="fa fa-close text-danger"></i>';
            }
            if (result != '')
            {
            cnt = result.length;
            $.each(result, function (index, value) {
            if (value.pslat != null && value.pslong != null && value.pslat != '' && value.pslong != '')
            {
            if (value.ps_type_id == 1)
            {
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/polling_station/ps.blue.png')}}",
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
            }
            else if (value.ps_type_id == 2)
            {
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/polling_station/ps.pink.png')}}",
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
            }
            else if (value.ps_type_id == 3)
            {
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/polling_station/ps.red.png')}}",
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
            }
            else if (value.ps_type_id == 4)
            {
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/polling_station/ps-black.png')}}",
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
            }
            else if (value.ps_type_id == 5)
            {
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/polling_station/ps.green.png')}}",
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
            }
            else
            {
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/polling_station/ps.png')}}",
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
            }
            var psid = window.btoa(value.psid);
            var psno = window.btoa(value.ps_no);
            var acno = window.btoa(value.ac_no);
            var latlong = {lat: parseFloat(value.pslat), lng: parseFloat(value.pslong)};
            if (value.ps_address != null && value.ps_address != '')
            {
            var psaddress = value.ps_address;
            }
            else
            {
            var psaddress = 'To be updated';
            }
            var rsdata = getElectorByPSID(value.psid);
            var imgpath = imgpathFunc();
            var psdatas = $("#psdataval").val();
            var eleceach = psdatas.split("***");
            if (eleceach[0] != 'NA' && eleceach[0] != undefined){
            var ps_id = eleceach[0];
            } else {
            var ps_id = "NA";
            }
            if (eleceach[1] != 'NA' && eleceach[1] != undefined){
            var electors_total = eleceach[1];
            } else {
            var electors_total = "To be updated";
            }
            if (eleceach[2] != 'NA' && eleceach[2] != undefined){
            var electors_male = eleceach[2];
            } else {
            var electors_male = "To be updated";
            }
            if (eleceach[3] != 'NA' && eleceach[3] != undefined){
            var electors_female = eleceach[3];
            } else {
            var electors_female = "To be updated";
            }
            if (eleceach[4] != 'NA' && eleceach[4] != undefined){
            var electors_other = eleceach[4];
            } else {
            var electors_other = "To be updated";
            }
            if (eleceach[5] != 'NA' && eleceach[5] != undefined){
            var no_of_pwd_voters = eleceach[5];
            } else {
            var no_of_pwd_voters = "To be updated";
            }
            if (eleceach[6] != 'NA' && eleceach[6] != undefined){
            var dd = eleceach[6];
            i = '<img src=' + '"' + imgpath + eleceach[6] + '"' + ' class="img-fluid"/>';
            } else {
            var dd = "To be updated";
            i = '<img src="{{asset('img / pro - img.jpg')}}" class="img-fluid"/>';
            }
            var psdtlink ='{!! url("psdetailsmarker") !!}/'+psid+'/'+acno+'/'+psno;
            var contentString ='<div class="container">\n\
                                <div class="snip-pop mt-3">\n\
                                <div class="pro-head">\n\
                                   <h4 class="text-blue brb">' + value.ps_name_en + '</h4>\n\
                                   <p class="mb-1"><b>PS Type : <span class="text-blue">' + value.ps_type+ '</span></b>\n\
                                   </p><p class="mb-1"><b>Address :</b> ' + psaddress + '</p>\n\
                                 '+poll_date+'\n\
                                </div>\n\
                                <div class="pop-sec pb-3">\n\
                                    <ul class="nav pb-3 nav-tabs" role="tablist">\n\
                                        <li class="nav-item"><a class="nav-link mr-2 active" data-toggle="tab" href="#detailsRangeBar'+ value.psid +'">Basic Details</a></li>\n\
                                        <li class="nav-item"><a class="nav-link mr-2" data-toggle="tab" href="#facilityRangeBar'+ value.psid +'">Facility</a></li>\n\
                                        <li class="nav-item"><a class="nav-link mr-2" data-toggle="tab" href="#acRangeBar'+ value.psid +'">AC Details</a></li>\n\
                                    </ul>\n\
                                <div class="tab-content">\n\
                                    <div id="detailsRangeBar'+ value.psid +'" class="details mt-2 container tab-pane active">\n\
                                        <p class="pt-1">No. of electors  <span class="text-blue">: ' + electors_total + '</span></p>\n\
                                        <p>No. of pwd electors <span class="text-blue">: ' + no_of_pwd_voters + '</span></p>\n\
                                        <p>No. of electors male <span class="text-blue">: ' + electors_male + '</span></p>\n\
                                        <p>No. of electors female <span class="text-blue">: ' + electors_female + '</span></p>\n\
                                        <p>No. of electors third gender <span class="text-blue">: ' + electors_other + '</span></p>\n\
                                        <div class="share mt-3 mb-4">\n\
                                            <ul class="share-icon">\n\
                                            <li><span>Share With :</span></li>\n\
                                            <li class="ml-2"><a href="https://api.whatsapp.com/send?text='+psdtlink+'" target="_blank"><i class="fa fa-whatsapp"></i></a></li>\n\
                                            <li><a href="https://twitter.com/intent/tweet?text='+psdtlink+'"  target="_blank"><i class="fa fa-twitter"></i></a></li>\n\
                                            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>\n\
                                            <li><a href="#"><i class="fa fa-instagram"></i></a></li>\n\
                                            </ul>\n\
                                        </div>\n\
                                        <p class="more-f mt-3"><a href="#">Navigate to here</a></p>\n\
                                    </div>\n\
                                    <div id="facilityRangeBar'+ value.psid +'" class="container facility tab-pane fade">\n\
                                        <p class="mt-2"><img src="{{asset('images/icons/water.png')}}" class="img-fluid"/> Water '+water+'</p>\n\
                                        <p><img src="{{asset('images/icons/eletricity.png')}}" class="img-fluid"/> Electricity '+electricity+'</p>\n\
                                        <p><img src="{{asset('images/icons/toilate.png')}}" class="img-fluid"/> Toilet '+toilet+'</p>\n\
                                        <p><img src="{{asset('images/icons/parking.png')}}" class="img-fluid"/> Interet '+internet+'</p>\n\
                                        <p><img src="{{asset('images/icons/pwd.png')}}" class="mg-fluid"/> Road Connectivity '+road_connectivity+'</p>\n\
                                        <p class="more-f text-center"><a href="{{url('psdetailsmarker')}}/'+psid+'/'+acno+'/'+psno+'">More facility Details</a></p>\n\
                                    </div>\n\
                                    <div id="acRangeBar'+ value.psid +'" class="ac container tab-pane fade">\n\
                                        <div class="row pt-2 pb-2">\n\
                                            <div class="col-sm-7">\n\
                                                <p>AC Name </p>\n\
                                                <p>District Name </p>\n\
                                                <p>Total number of Candidate </p>\n\
                                            </div>\n\
                                            <div class="col-sm-5">\n\
                                                <p>: <span>'+acname+'</span></p>\n\
                                                <p>: <span>'+distname+'</span></p>\n\
                                                <p>: <span>'+total_candidate+'</span></p>\n\
                                            </div>\n\
                                        </div>\n\
                                        '+candidate_list+'\n\
                                    </div>\n\
                                </div>\n\
                                </div>\n\
                                </div>\n\
                                </div>';
            var newpopup = L.popup({
            closeOnClick: false,
                    autoClose: true
            }).setContent(contentString);
            var marker = L.marker([value.pslat, value.pslong], {bounceOnAdd: false, icon: iconImage});
            marker.addTo(map);
            marker.bindPopup(newpopup);
            }
            else
            {
            console.log('This facility is not available in this polling staion')
            }
            });
            }
            else
            {
                 $('#notification .modal-body p').text('No data available in this range');
                 $('#notification').modal('toggle');
            }
        },
            error: function(error) {
            console.log(error.responseText);
            }
    });
    });
    //end electors filter onchange 
    //start pwd facility onchange
    $('select#pwdfacility').change(function () {
        if(map != '' && map != null)
        {
            map.remove();
        }
    resetDropdown('pwdfacility');
    $("#numberCount").show();
    $('#amf').val('0');
    $('#pwd_facility strong').css('display', 'none');
    $('#pwd_facility span').css('display', 'none');
    var dist = 0;
    var ac = 0;
    var acno = 0;
    var statee = $('select#state option:selected').val();
    var state = $('#state').val();
     var dist = $('#dist :selected').val();
    var ac = $('#ac :selected').val();
    if (statee == '0'){
        $(this).prop('selectedIndex',0);
        $('#notification .modal-body p').text('Please select state first');
    $('#notification').modal('toggle');
    return false;
    }
    if (dist == '0'){
        $(this).prop('selectedIndex',0);
        $('#notification .modal-body p').text('Please select district first');
    $('#notification').modal('toggle');
    return false;
    }
    if (ac == '0'){
        $(this).prop('selectedIndex',0);
        $('#notification .modal-body p').text('Please select ac first');
    $('#notification').modal('toggle');
    return false;
    }
    $("#loader").show();
    var acname = $('select#ac option:selected').text();
    var distname = $('select#dist option:selected').text();
    var pwdfacility = $(this).val();
    var container = L.DomUtil.get('map');
    if (container != null){ container._leaflet_id = null; }
    var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            osmAttrib = '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            osm = L.tileLayer(osmUrl, { maxZoom: 18, attribution: osmAttrib });
            //map id
            map = new L.Map('map', { center: new L.LatLng(28.6337379, 77.1972581), zoom: 13, fullscreenControl: {
            pseudoFullscreen: false
            }, });
            //Drawman Set
            var drawnItems = L.featureGroup().addTo(map);
    L.control.layers({
    'osm': osm.addTo(map),
            "google": L.tileLayer('http://www.google.cn/maps/vt?lyrs=s@189&gl=cn&x={x}&y={y}&z={z}', {
            attribution: 'google'
            })
    }, { 'drawlayer': drawnItems }, {fullscreenControl: true}, { position: 'topleft', collapsed: false }).addTo(map);
    setTimeout(function(){ map.invalidateSize(true)}, 0);
    // run kml on leaflet
    var urllink = "<?php echo url('/'); ?>/kmlmap/" + state + "/AC/" + ac + ".kml";
    var runLayer = omnivore.kml(urllink, {async: true, })
            .on('ready', function() {
            map.fitBounds(runLayer.getBounds());
            })
            .addTo(map);
    $.ajax({
    url: "{{url('pwdfacilty')}}",
            type: 'POST',
            data: {_token:'{{csrf_token()}}', pwdfacility: pwdfacility, state: state, dist: dist, acno: acno},
            success: function (result) {
            $("#loader").hide();
            $('#psText2').hide();
            $('#psText').hide();
            $('#pct').hide();
            if(result.length>0 && result[0]!=undefined){
                psid=result[0]['psid'];
            }else{
                psid='0';
            }

            var candidate_list = '';
            var poll_date='<br/>';
            var total_candidate=0;

           /* if(result[2][0]!=undefined && result[2].length>0 && result[2][0]['poll_date']!=null){
                var poll_date='<p><b>Date of Election : <span class="text-blue">'+result[2][0]['poll_date']+'</span></b></p>';
                //if poll date annouce
                if(result[8]!=undefined){
                var total_candidate=result[8].length;
                candidate_list=' <div class="list-Candidate">\n\
                                            <h4 class="p-2">Candidate List</h4>\n\
                                            <ul class="pl-0 mb-0">';
                $.each(result[3], function (index, value) {
                    //var candidate_name = value.cand_name;
                    candidate_list += '<li><p><i class="fa fa-long-arrow-right text-blue"></i>'+value.cand_name+'</p></li>';
                //fruits.push(value.cand_name);
                });
                candidate_list+=' <p class="more-f mt-3 mb-0"><a href="#">View More</a></p>\n\
                                            </ul>\n\
                                        </div>';
                }
            }
            */
            var fmdata=getFacilityPSID(psid);
            var psfacility = $("#psfacility").val();
            var array_psfacility=$.parseJSON(psfacility);
            //console.log(array_psfacility);
            if(array_psfacility.length!=0 && array_psfacility[0]!=undefined && array_psfacility[0]['facility_master_id']==10){
                var electricity='<i class="fa fa-check text-success"></i>';
            }else{
                var electricity='<i class="fa fa-close text-danger"></i>';
            }
            if(array_psfacility.length!=0 && array_psfacility[1]!=undefined && array_psfacility[1]['facility_master_id']==13){
                var road_connectivity='<i class="fa fa-check text-success"></i>';
            }else{
                var road_connectivity='<i class="fa fa-close text-danger"></i>';
            }
            if(array_psfacility.length!=0 && array_psfacility[2]!=undefined && array_psfacility[2]['facility_master_id']==17){
                var internet='<i class="fa fa-check text-success"></i>';
            }else{
                var internet='<i class="fa fa-close text-danger"></i>';
            }
            if(array_psfacility.length!=0 && array_psfacility[3]!=undefined && array_psfacility[3]['facility_master_id']==22){
                var water='<i class="fa fa-check text-success"></i>';
            }else{
                var water='<i class="fa fa-close text-danger"></i>';
            }
            if(array_psfacility.length!=0 && array_psfacility[4]!=undefined && array_psfacility[4]['facility_master_id']==25){
                var toilet='<i class="fa fa-check text-success"></i>';
            }else{
                var toilet='<i class="fa fa-close text-danger"></i>';
            }

            if (result != '')
            {
            cnt = result.length;
            if (pwdfacility == 0)
            {
            $('#e_count strong').text('Facility Not Available');
            $('#e_count span').text(' : ' + result.length);
            $("#p_count").hide();
            $("#t_pscount").hide();
            }
            if (pwdfacility == 1)
            {
            $('#e_count strong').text('Facility Available');
            $('#e_count span').text(' : ' + result.length);
            $("#p_count").hide();
            $("#t_pscount").hide();
            }
            $.each(result, function (index, value) {
            var rsdata = getElectorByPSID(value.psid);
            if (value.psid)
            {
            if (value.ps_type_id == 1)
            {
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/polling_station/ps.blue.png')}}",
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
            }
            else if (value.ps_type_id == 2)
            {
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/polling_station/ps.pink.png')}}",
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
            }
            else if (value.ps_type_id == 3)
            {
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/polling_station/ps.red.png')}}",
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
            }
            else if (value.ps_type_id == 4)
            {
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/polling_station/ps-black.png')}}",
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
            }
            else if (value.ps_type_id == 5)
            {
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/polling_station/ps.green.png')}}",
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
            }
            else
            {
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/polling_station/ps.png')}}",
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
            }
            var psid = window.btoa(value.psid);
            var psno = window.btoa(value.ps_no);
            var acno = window.btoa(value.ac_no);
            if (value.ps_address != null && value.ps_address != '')
            {
            var psaddress = value.ps_address;
            }
            else
            {
            var psaddress = 'To be updated';
            }

            var imgpath = imgpathFunc();
            var psdatas = $("#psdataval").val();
            var eleceach = psdatas.split("***");
            if (eleceach[0] != 'NA' && eleceach[0] != undefined){
            var ps_id = eleceach[0];
            } else {
            var ps_id = "NA";
            }
            if (eleceach[1] != 'NA' && eleceach[1] != undefined){
            var electors_total = eleceach[1];
            } else {
            var electors_total = "To be updated";
            }
            if (eleceach[2] != 'NA' && eleceach[2] != undefined){
            var electors_male = eleceach[2];
            } else {
            var electors_male = "To be updated";
            }
            if (eleceach[3] != 'NA' && eleceach[3] != undefined){
            var electors_female = eleceach[3];
            } else {
            var electors_female = "To be updated";
            }
            if (eleceach[4] != 'NA' && eleceach[4] != undefined){
            var electors_other = eleceach[4];
            } else {
            var electors_other = "To be updated";
            }
            if (eleceach[5] != 'NA' && eleceach[5] != undefined){
            var no_of_pwd_voters = eleceach[5];
            } else {
            var no_of_pwd_voters = "To be updated";
            }
            //alert(imgpath+eleceach[6]);
            if (eleceach[6] != 'NA' && eleceach[6] != undefined){
            var dd = eleceach[6];
            i = '<img src=' + '"' + imgpath + eleceach[6] + '"' + ' class="img-fluid"/>';
            } else {
            var dd = "To be updated";
            i = '<img src="{{asset('img / pro - img.jpg')}}" class="img-fluid"/>';
            }
            var psdtlink ='{!! url("psdetailsmarker") !!}/'+psid+'/'+ac+'/'+psno;
            var contentString ='<div class="container">\n\
                                <div class="snip-pop mt-3">\n\
                                <div class="pro-head">\n\
                                   <h4 class="text-blue brb">' + value.ps_name_en + '</h4>\n\
                                   <p class="mb-1"><b>PS Type : <span class="text-blue">' + value.ps_type+ '</span></b>\n\
                                   </p><p class="mb-1"><b>Address :</b> ' + psaddress + '</p>\n\
                                  '+poll_date+'\n\
                                </div>\n\
                                <div class="pop-sec pb-3">\n\
                                    <ul class="nav pb-3 nav-tabs" role="tablist">\n\
                                        <li class="nav-item"><a class="nav-link mr-2 active" data-toggle="tab" href="#detailspwdfacility'+ value.psid +'">Basic Details</a></li>\n\
                                        <li class="nav-item"><a class="nav-link mr-2" data-toggle="tab" href="#facilitypwdfacility'+ value.psid +'">Facility</a></li>\n\
                                        <li class="nav-item"><a class="nav-link mr-2" data-toggle="tab" href="#acpwdfacility'+ value.psid +'">AC Details</a></li>\n\
                                    </ul>\n\
                                <div class="tab-content">\n\
                                    <div id="detailspwdfacility'+ value.psid +'" class="details mt-2 container tab-pane active">\n\
                                        <p class="pt-1">No. of electors  <span class="text-blue">: ' + electors_total + '</span></p>\n\
                                        <p>No. of pwd electors <span class="text-blue">: ' + no_of_pwd_voters + '</span></p>\n\
                                        <p>No. of electors male <span class="text-blue">: ' + electors_male + '</span></p>\n\
                                        <p>No. of electors female <span class="text-blue">: ' + electors_female + '</span></p>\n\
                                        <p>No. of electors third gender <span class="text-blue">: ' + electors_other + '</span></p>\n\
                                        <div class="share mt-3 mb-4">\n\
                                            <ul class="share-icon">\n\
                                            <li><span>Share With :</span></li>\n\
                                            <li class="ml-2"><a href="https://api.whatsapp.com/send?text='+psdtlink+'" target="_blank"><i class="fa fa-whatsapp"></i></a></li>\n\
                                            <li><a href="https://twitter.com/intent/tweet?text='+psdtlink+'"  target="_blank"><i class="fa fa-twitter"></i></a></li>\n\
                                            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>\n\
                                            <li><a href="#"><i class="fa fa-instagram"></i></a></li>\n\
                                            </ul>\n\
                                        </div>\n\
                                        <p class="more-f mt-3"><a href="#">Navigate to here</a></p>\n\
                                    </div>\n\
                                    <div id="facilitypwdfacility'+ value.psid +'" class="container facility tab-pane fade">\n\
                                        <p class="mt-2"><img src="{{asset('images/icons/water.png')}}" class="img-fluid"/> Water '+water+'</p>\n\
                                        <p><img src="{{asset('images/icons/eletricity.png')}}" class="img-fluid"/> Electricity '+electricity+'</p>\n\
                                        <p><img src="{{asset('images/icons/toilate.png')}}" class="img-fluid"/> Toilet '+toilet+'</p>\n\
                                        <p><img src="{{asset('images/icons/parking.png')}}" class="img-fluid"/> Interet '+internet+'</p>\n\
                                        <p><img src="{{asset('images/icons/pwd.png')}}" class="mg-fluid"/> Road Connectivity '+road_connectivity+'</p>\n\
                                        <p class="more-f text-center"><a href="{{url('psdetailsmarker')}}/'+psid+'/'+ac+'/'+psno+'">More facility Details</a></p>\n\
                                    </div>\n\
                                    <div id="acpwdfacility'+ value.psid +'" class="ac container tab-pane fade">\n\
                                        <div class="row pt-2 pb-2">\n\
                                            <div class="col-sm-7">\n\
                                                <p>AC Name </p>\n\
                                                <p>District Name </p>\n\
                                                <p>Total number of Candidate </p>\n\
                                            </div>\n\
                                            <div class="col-sm-5">\n\
                                                <p>: <span>'+acname+'</span></p>\n\
                                                <p>: <span>'+distname+'</span></p>\n\
                                                <p>: <span>'+total_candidate+'</span></p>\n\
                                            </div>\n\
                                        </div>\n\
                                       '+candidate_list+'\n\
                                    </div>\n\
                                </div>\n\
                                </div>\n\
                                </div>\n\
                                </div>';
            var newpopup = L.popup({
            closeOnClick: false,
                    autoClose: true
            }).setContent(contentString);
            var marker = L.marker([value.pslat, value.pslong], {bounceOnAdd: false, icon: iconImage});
            marker.addTo(map);
            marker.bindPopup(newpopup);
            }
            else
            {
            //alert('This facility is not available in this polling staion');
            if (pwdfacility == 0)
            {
            $('#e_count strong').text('Facility Not Available');
            $('#e_count span').text(' : 0');
            $("#p_count").hide();
            $("#t_pscount").hide();
            }
            if (pwdfacility == 1)
            {
            $('#e_count strong').text('Facility Available');
            $('#e_count span').text(' : 0');
            $("#p_count").hide();
            $("#t_pscount").hide();
            }
            $("#p_count").hide();
            $("#t_pscount").hide();
            }
            });
            }
            else
            {
            if (pwdfacility == 0)
            {
            $('#e_count strong').text('Facility Not Available');
            $('#e_count span').text(' : 0');
            $("#p_count").hide();
            $("#t_pscount").hide();
            }
            if (pwdfacility == 1)
            {
            $('#e_count strong').text('Facility Available');
            $('#e_count span').text(' : 0');
            $("#p_count").hide();
            $("#t_pscount").hide();
            }
            $("#p_count").hide();
            $("#t_pscount").hide();
             $('#notification .modal-body p').text('Selected PWD facility is not available');
                 $('#notification').modal('toggle');
            }
            },
            error: function(error) {
            console.log(error.responseText);
            }
    });
    });
    //end pwd facility  onchange 
    </script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.6.2/bootstrap-slider.min.js"></script>
	<script>
    	// Without JQuery
    	var slider = new Slider("#ex8", {
    	tooltip: 'always'
    	});
	</script>
	  <script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.0";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));</script>
@endsection