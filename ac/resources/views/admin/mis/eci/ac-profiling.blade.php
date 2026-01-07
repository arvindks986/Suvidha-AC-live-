@extends('admin.central.common.theme')
@section('title', 'State Profile')
<?php
$breadcrumbs = [];
$breadcrumbs[] = [
    'href' => Common::generate_url('mis/state-profile-dashboard'),
    'name' => 'State Profile'
];
?>
@section('content') 
@section('style')

<style type="text/css">
    .bordernone{border:none;}
    /* Always set the map height explicitly to define the size of the div
        * element that contains the map. */
    #map_in {
        min-height: 95%;
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
    .loader113 {
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
    }
    .inner-dtl{
        background-color: #f8f8f8;
        border: 1px solid #dcdcdc;
        padding: 5px 10px;
        border-radius: 5px;
    }
    .inner-dtl .col-sm-6:first-child{
        border-right: 1px dashed #f0587e;
    }
    .mapshow{
        font-weight:normal;
    }


    .leaflet-popup-content {
        margin: 10px 7px;  
        line-height: 1.1;
        width: 230px;

    }
    .pop-sec{
        height: 180px;
        overflow: auto;
    }
    .pop-sec p{
        margin: 7px 0;
        font-size: 14px;
    }
    .pop-sec .nav-link {
        padding: .4rem .8rem;
    }
    .leaflet-container a.leaflet-popup-close-button{
        color: #f0587e;    
    }    
    #map{
        min-height: 500px!important;
    }

</style>
@endsection
@include('admin.mis.map-common.left-map')
<!-- Leaflet-KMZ -->
<link rel="stylesheet" href="{{asset('js/leaflet/leaflet.css')}}" />
<link rel="stylesheet" type="text/css" href="{{asset('js/leaflet/esri-leaflet-geocoder.css')}}">
<link href="{{asset('js/leaflet/leaflet.fullscreen.css')}}" rel='stylesheet' />

<link rel="stylesheet" href="{{ asset('css/custom-profile.css') }}">
<link rel="stylesheet" href="{{asset('js/leaflet/leaflet-routing-machine.css')}}" />
<script src="{{asset('js/leaflet/leaflet-routing-machine.js')}}"></script>
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.6.2/css/bootstrap-slider.min.css" rel="stylesheet"/>

<main role="main" class="inner cover position-relative mb-3">
    <div class="tab-b">
        <input type="checkbox" id="toggle-event" checked data-toggle="toggle" data-on="Map View" data-off="Tabular View" data-onstyle="primary" data-offstyle="info">
        <a href="{{url('/eci/mis/compare-state-profile')}}"><button class="btn btn-success">Compare</button></a>
		@if(!empty($st_code))
		<a href="{{url('/eci/mis/print-state-profile/'.$st_code)}}"><button class="btn btn-warning">PDF<i class="fa fa-download"></i></button></a>
		@endif
    </div>
    <section>
        <main class="main-wrap tab1" style="display:none;">
            <div class="col-sm-12">
               
                <div class="row">
                    <div class="col-sm-3 pr-md-0 pr-sm-3">
                        <div class="search-field mt-2">
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
                        <div class="map-inner">
                            <div class="snip-inner" id="snip-inner" >
                                <section class="statistics mt-2" id="eo-g">

                                </section>                      
                            </div>
                        </div>  
                         <div class="detls-map mb-1" id="textnew" style="display: none;">
                            <div class="col-sm-12 offset-sm-0 inner-dtl">
                                <div class="row">
                                    <div class="col-sm-12" id="textnew">
                                        <p class="mb-0 mr-1 py-2" >
                                            <span><strong class="text-white" >State: </strong></span><span id="sttext" class="text-white"> : aaa</span>                          
                                            <span style="display:none;" id="distText">
                                                <strong class="text-white" style="display:none;" id="distText2">District: </strong>
                                                <span style="display:none;" id="dstext" class="text-white"> : </span>
                                            </span>                    
                                            <span style="display:none;" id="acText">
                                                <strong class="text-white" style="display:none;" id="acText2">AC: </strong>
                                                <span style="display:none;" id="act" class="text-white"> : </span>
                                            </span>
                                            <span style="display:none;" id="psText">
                                                <strong class="text-white" style="display:none;" id="psText2">PS: </strong>
                                                <span style="display:none;" id="pct" class="text-white"> : </span>
                                            </span> 
                                            <span id="t_pscount"><strong class="text-white">Total P.S</strong><span class="text-white"> :</span></span>&nbsp;&nbsp; 
                                            <span id="e_count"><strong class="text-white">Electors Count</strong><span class="text-white"> : </span></span>&nbsp;&nbsp; 
                                            <span id="p_count"><strong class="text-white">PWD Count</strong><span class="text-white"> :</span></span>&nbsp;&nbsp;
                                            <span id="pwd_facility"><strong class="text-white" style="display:none">PWD facility available</strong><span class="text-white"></span></span>&nbsp;&nbsp;
                                        </p>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 offset-sm-0 inner-dtl">
                        <div class="row">
                            <div class="col-sm-12" id="textnew">
                                <p class="mb-0 mr-1" >
                                    <span class="text-info"> 
                                      <select name="state" class="form-control" id="state">
                                        <option value="0">Select State Name</option>
                                        @foreach($state as $val) 
                                        <option value="{{$val->ST_CODE}}">{{$val->ST_NAME}}</option>
                                        @endforeach     
                                    </select>
                                    </span>    
                                    <span class="text-info"> 
                                       <select name="dist" class="form-control" id="dist">
                                        <option value="0">Select District Name</option>
                                    </select>
                                    </span>     
                                    <span class="text-info"> 
                                       <select name="ac_search_header" id="ac">
                                            <option value="0">Select AC Name</option>
                                        </select>
                                    </span>
                                    <span class="text-info"> 
                                       <select name="ps_search_header" id="ps">
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
                        <div class="map mt-2" id="map"></div>
                        <div class="text-center mt-3 ftr-buttons"  style="text-align:center">
    <!--                   <input id="clear_shapes" value="clear shapes"  class="d-inline" 
                         type="button">
                        <input id="save_encoded" class="d-inline" value="save encoded(IO.IN(shapes,true))" type="hidden">
                        <input id="save_raw" value="Save Map" class="d-inline" type="button" >-->
                        </div>
                        <div class="map" id="map_out"></div>
                        <div class="map-color float-right" style="bottom: -15px;">
                            <ul class="list-inline">
                                <li class="list-inline-item"><strong></strong> </li>                             
                                <li class="list-inline-item"><i class="fa fa-circle text-success"></i> Main</li>
                                <li class="list-inline-item"><i class="fa fa-circle text-dark" style="color:#000"></i> Modal</li>
                                <li class="list-inline-item"><i class="fa fa-circle text-red" style="color:red"></i> Critical</li>
                                <li class="list-inline-item"><i class="fa fa-circle text-pink" style="color:#d0028a"></i> Valnerable</li>
                                <li class="list-inline-item"><i class="fa fa-circle text-blue" style="color:#04195e"></i> Auxiliary</li>  
                                <!--<li class="list-inline-item"><a type="button" href="#" class="act"><i class="fa fa-download"></i> Get Report</a></li>-->
                            </ul>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <div id="loader" style="display:block;">
                <div class="loader113"><figure><img src="{{ asset('img/loading-img.gif')}}" alt=""/></figure></div>
            </div>
            <input type="hidden" id="psdataval">
        </main>
        <!-- Tabular View start here -->
        <main class="main-wrap tab2" style="display:block;">
            <aside class="lft-aside">
                <div class="filter-area">
                    <form id='state_frm'>
                        <select class="form-control" id="st_code_val" name="st_code">
                            <option value="0">Select State Name</option>
                            @foreach($state as $val) 
                            <option value="{{$val->ST_CODE}}" @if($st_code==$val->ST_CODE)selected @endif>{{$val->ST_NAME}}</option>
                            @endforeach     
                        </select>
                    </form>
                    @if($state_details)
                    <div class="circle-box">{{$state_details->ST_NAME}}</div>
                    @else
                    <div class="circle-box">State <span>Profile</span></div>
                    @endif
                    
                    <div class="filter-list">
                        <h4 class="filter-title">
                            Filter <span class="pull-right"><i class="fa fa-filter"></i></span>
                        </h4>
                        <div class="p-3 inner-scroll">
                            <form>
                                <!--<div class="form-group">
                                  <input type="checkbox" id="select_all" checked>
                                  <label for="select_all">Select All</label>
                                </div>-->
                                <div class="form-group">
                                    <input type="checkbox" id="module1" checked class="checkbox cscheck" data-id="1">
                                    <label for="module1">State Map</label>
                                </div>
                                <div class="form-group">
                                    <input type="checkbox" id="module2" checked class="checkbox cscheck" data-id="2">
                                    <label for="module2">Officer Details</label>
                                </div>
                                <div class="form-group">
                                    <input type="checkbox" id="module3" checked class="checkbox cscheck" data-id="3">
                                    <label for="module3">Demographic Details</label>
                                </div>
                                <div class="form-group">
                                    <input type="checkbox" id="module4" checked class="checkbox cscheck" data-id="4">
                                    <label for="module4">Population & Polling Stations</label>
                                </div>
                                <div class="form-group">
                                    <input type="checkbox" id="module5" checked class="checkbox cscheck" data-id="5">
                                    <label for="module5">General Electrols</label>
                                </div> 
                                <div class="form-group">
                                    <input type="checkbox" id="module6" checked class="checkbox cscheck" data-id="6">
                                    <label for="module6">Service Electrols</label>
                                </div> 
                                <div class="form-group">
                                    <input type="checkbox" id="module7" checked class="checkbox cscheck" data-id="7">
                                    <label for="module7">Person With Disabilities Status</label>
                                </div> 
                                <div class="form-group">
                                    <input type="checkbox" id="module8" checked class="checkbox cscheck" data-id="8">
                                    <label for="module8">Asurred Minimum Facilities At PS</label>
                                </div> 
                                <div class="form-group">
                                    <input type="checkbox" id="module9" checked class="checkbox cscheck" data-id="9">
                                    <label for="module9">ERONET Forms</label>
                                </div> 
                                <div class="form-group">
                                    <input type="checkbox" id="module10" checked class="checkbox cscheck" data-id="10">
                                    <label for="module10">NGSP Complaints</label>
                                </div> 
                                <div class="form-group">
                                    <input type="checkbox" id="module11" checked class="checkbox cscheck" data-id="11">
                                    <label for="module11">EVM/VVPAT</label>
                                </div> 
                                <div class="form-group">
                                    <input type="checkbox" id="module12" checked class="checkbox cscheck" data-id="12">
                                    <label for="module12">Candidate & Nomination Details</label>
                                </div> 
                                <div class="form-group">
                                    <input type="checkbox" id="module13" checked class="checkbox cscheck" data-id="13">
                                    <label for="module13">Vul-rability/Senditive Area Mapping</label>
                                </div> 
                                <div class="form-group">
                                    <input type="checkbox" id="module14" checked class="checkbox cscheck" data-id="14">
                                    <label for="module14">Election Schedule</label>
                                </div> 
                                <div class="form-group">
                                    <input type="checkbox" id="module15" checked class="checkbox cscheck" data-id="15">
                                    <label for="module15">Webcasting Details</label>
                                </div> 
                                <div class="form-group">
                                    <input type="checkbox" id="module16" checked class="checkbox cscheck" data-id="16">
                                    <label for="module16">Election Results</label>
                                </div>   
                            </form>
                        </div>  
                    </div> 
                </div>
            </aside>
            <section class="rght-section mt-2">
                <div class="container-fluid">
                    <div class="row mt-3">
                        <div class="col-md-5 col-12">
                            <div class="module1 commoncheck">
                                <h4>State Map</h4> 
                                <div class="card card-shadow mb-4">
                                    @php 
                                        $no_class = 'no-record';
                                        $height = '364px;';
                                        if($get_state_entry){
                                            if($get_state_entry->statemap){
                                                $no_class = '';
                                                $height = '';
                                            }
                                        }
                                    @endphp
                                    <div class="card-body p-0 {{$no_class}}" style="height: {{$height}}">
                                        <map>@if($get_state_entry)<img src="{{ asset($get_state_entry->statemap) }}" alt="">@endif</map>
                                    </div>
                                </div>
                            </div>			  
                        </div>	
                        <div class="col-md-7 col-12">
                            <div class="module2 commoncheck">
                                <h4>Officer Details</h4> 
                                <div class="card card-shadow mb-4">
                                    <div class="card-body p-0 @if(!$officer_count) no-record @endif">
                                        <div class="max-hght">
                                            <!--<div class="spinner-grow text-primary" role="status">
                                                    <span class="sr-only">Loading...</span>
                                        </div>-->
                                            <ul class="p-3 ul-list">
												@php $i=1;@endphp
                                                @if($officer_count)
													@foreach($officer_count as $k=>$v)
														<li>{{$i}}. {{$v->designation}} <span class="val-box">{{$v->total_officer}}</span></li>
													@php $i++;@endphp
													@endforeach
												@else
													<li>1. CEO <span class="val-box">-</span></li>
													<li>2. DEO <span class="val-box">-</span></li>
													<li>3. ROAC <span class="val-box">-</span></li>
													@php $i=4;@endphp
												@endif
												
												@php 
													$j=$i;
												@endphp
												@if($get_observer_data)
													@foreach($get_observer_data as $val)
														@if($val->OBSERVER_Type !='NotDefine')
															<li>{{$j}}. {{ucfirst(strtolower($val->OBSERVER_Type))}} Observer <span class="val-box">{{$val->TotalOBS}}</span></li>
														@php $j++; @endphp
														@endif
														
													@endforeach
											    @else
													<li>4. General Observer <span class="val-box">-</span></li>
													<li>5. Police Observer <span class="val-box">-</span></li>
													<li>6. Expenditure Observer <span class="val-box">-</span></li>
													<li>7. Awareness Observer <span class="val-box">-</span></li>
											    @endif
                                            </ul>

                                        </div>	
                                    </div>
                                </div>
                            </div> 	
                        </div>	
                    </div> 
                    <div class="mt-3">
                        <div class="module3 commoncheck">
                            <h4>Demographic Details</h4>   
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="card card-shadow mb-4">
                                        <div class="card-body p-0">
                                            <table class="table custom-table text-center">
                                                <thead>
                                                    <tr>
                                                        <th>Revenue Districts</th>
                                                        <th>Sub Divisions</th>
                                                        <th>Tehsils/Talukas</th>
                                                    </tr>  
                                                </thead> 
                                                <tbody>
                                                    <tr>
                                                        <td>@if($get_state_entry){{$get_state_entry->revenue_district}}@else - @endif</td>
                                                        <td>@if($get_state_entry){{$get_state_entry->sub_division}}@else - @endif</td>
                                                        <td>@if($get_state_entry){{$get_state_entry->tehsil_talkuas}}@else - @endif</td>
                                                    </tr>  
                                                </tbody>  
                                            </table>
                                        </div>
                                    </div>   
                                </div>	
                                <div class="col-md-6 col-12">
                                    <div class="card card-shadow mb-4">
                                        <div class="card-body p-0">
                                            <table class="table custom-table text-center">
                                                <thead>
                                                    <tr>
                                                        <th>Gram Panchayats</th>
                                                        <th>Villages</th>
                                                        <th>Municipal Corporation</th>
                                                    </tr>  
                                                </thead> 
                                                <tbody>
                                                    <tr>
                                                        <td>@if($get_state_entry){{$get_state_entry->gram_panchayat}}@else - @endif</td>
                                                        <td>@if($get_state_entry){{$get_state_entry->gram_panchayat}}@else - @endif</td>
                                                        <td>@if($get_state_entry){{$get_state_entry->municipal_corporations}}@else - @endif</td>
                                                    </tr>  
                                                </tbody>  
                                            </table>
                                        </div>
                                    </div>
                                </div>	
                            </div>
                            <div class="row">	  
                                <div class="col-md-12 col-12">
                                    <div class="card card-shadow mb-4">
                                        <div class="card-body p-0">
                                            <table class="table custom-table text-center">
                                                <thead>
                                                    <tr>
                                                        <th>Municipalities</th>
                                                        <th>Post Offices</th>
                                                        <th>Police Stations</th>
                                                        <th>Electoral Office</th>
                                                        <th>Total ACs</th>
                                                        <th>Total PCs</th>   
                                                    </tr>  
                                                </thead> 
                                                <tbody>
                                                    <tr>
                                                        <td>@if($get_state_entry){{$get_state_entry->municipalities}}@else - @endif</td>
                                                        <td>@if($get_state_entry){{$get_state_entry->post_offices}}@else - @endif</td>
                                                        <td>@if($get_state_entry){{$get_state_entry->police_stations}}@else - @endif</td>
                                                        <td>@if($get_state_entry){{$total_dist}}@else - @endif</td>
                                                        <td>@if($get_state_entry){{$total_acs}}@else - @endif</td>
                                                        <td>@if($get_state_entry){{$total_pcs}}@else - @endif</td>  
                                                    </tr>  
                                                </tbody>  
                                            </table>
                                        </div>
                                    </div>   
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- Parallex Effect Section Starts Hare -->
                    <section class="parallex-effect mt-3 mb-3 module4 commoncheck">
                        <div class="row">
                            <div class="col-md-6 col-12 pt-5 pl-5 pr-5">
                                <h4 class="mb-3">Popullation Details</h4>  
                                @php 
                                    $non_class = 'no-record';
                                    if($get_ac_entry){
                                        if($get_ac_entry->census_population >0 || $get_ac_entry->projected_population >0 || $get_ac_entry->ep_ratio >0 ){
                                            $non_class = '';
                                        }
                                    }
                                    @endphp
                                <div class="parallex-content list">
                                    <div class="{{$non_class}}">
                                    <ul class="para-list">
                                        <li><i class="fa fa-angle-double-right"></i> 2011 Census Popullation <span>@if($get_ac_entry){{$get_ac_entry->census_population}}@else - @endif</span></li>
                                        <li><i class="fa fa-angle-double-right"></i> Projected Popullation <span>@if($get_ac_entry){{$get_ac_entry->projected_population}}@else - @endif</span></li>
                                        <li><i class="fa fa-angle-double-right"></i> EP Ratio <span>@if($get_ac_entry){{$get_ac_entry->ep_ratio}}@else - @endif</span></li>
                                    </ul> 
                                    </div>
                                </div>  
                            </div> 
                            <div class="col-md-6 col-12 pt-5 pl-5 pr-5">
                                <h4 class="text-right mb-3">Polling Stations</h4>  
                                <div class="parallex-content list" id="polling_stations_dashboard">
                                    <div class="mis-loader"><img src="{{ asset('img/loading-img.gif')}}" alt=""/></div>
                                </div> 
                            </div>		 
                        </div> 

                    </section>	
                    <section class="mt-4">
                        <div class="row">
                            <div class="col-md-8 col-12">
                                <div class="module5 commoncheck">
                                    <h4>General Electors</h4>  
                                    <div class="card card-shadow mb-4">
                                        <div class="card-body p-0 no-record">
                                            <div class="min-hght-md">
                                                
                                                <!--<canvas id="myChart"></canvas>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>		 
                            <div class="col-md-4 col-12">
                                <div class="module6 commoncheck">
                                    <h4 class="text-right">Service Electors</h4>  
                                    <div class="card card-shadow mb-4" id="service_electors_dashboard">
                                        <div class="card-body p-0">
                                            <div class="mis-loader"><img src="{{ asset('img/loading-img.gif')}}" alt=""/></div>
                                        </div>
                                    </div>
                                </div>
                            </div>		 
                        </div>	
                    </section>	
                    <section class="mt-3">
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="module7 commoncheck">
                                    <h4>Persons with Disability Status (PWD) </h4> 
                                    @php 
                                    $non_class = 'no-record';
                                    if($get_ac_entry){
                                        if($get_ac_entry->total_visially_impaired >0 || $get_ac_entry->total_speech_hearig >0 || $get_ac_entry->total_locomotor_disabled >0 || $get_ac_entry->total_Other_disability >0 || $get_ac_entry->total_pwds >0){
                                            $non_class = '';
                                        }
                                    }
                                    @endphp
                                    <div class="card card-shadow mb-4">
                                        <div class="card-body p-0 {{$non_class}}">
                                            <ul class="p-3 ul-list">
                                                <li>1. Total Visually Impaired <span class="val-box">@if($get_ac_entry){{$get_ac_entry->total_visially_impaired}}@else - @endif</span></li>
                                                <li>2. Total Speech/ Hearig Disabled <span class="val-box">@if($get_ac_entry){{$get_ac_entry->total_speech_hearig}}@else - @endif</span></li>
                                                <li>3. Total Locomotor Disabled <span class="val-box">@if($get_ac_entry){{$get_ac_entry->total_locomotor_disabled}}@else - @endif</span></li>
                                                <li>4. Total Other Disability <span class="val-box">@if($get_ac_entry){{$get_ac_entry->total_Other_disability}}@else - @endif</span></li>
                                                <li>5. Total PwDs <span class="val-box">@if($get_ac_entry){{$get_ac_entry->total_pwds}}@else - @endif</span></li>
                                            </ul>  
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="module8 commoncheck">
                                    <h4 class="text-right">Assured Minimum Facilities At PS</h4>  
                                    <div class="card card-shadow mb-4" id="assured_minimum_facilities_dashboard">
                                        <div class="mis-loader"><img src="{{ asset('img/loading-img.gif')}}" alt=""/></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="mt-3">
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="module9 commoncheck">
                                    <h4>ERONET Forms </h4>  
                                    <div class="card card-shadow mb-4" id="eronet_forms_dashboard">
                                        <div class="mis-loader"><img src="{{ asset('img/loading-img.gif')}}" alt=""/></div>  
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="module10 commoncheck">
                                    <h4 class="text-right">NGSP Complaints</h4>  
                                    <div class="card card-shadow mb-4">
                                        <div class="card-body p-0" id="ngsp_complaints_dashboard">
                                            <div class="mis-loader"><img src="{{ asset('img/loading-img.gif')}}" alt=""/></div>
                                        </div>	
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>	
                    <section>
                        <div class="module11 commoncheck">
                            <h3>EVM/ VVPAT</h3>
                            <div class="row" id="evm_vvpat_dashboard">
                                <div class="col-md-12 col-12">
                                    <div class="card card-shadow">	
                                        <div class="mis-loader"><img src="{{ asset('img/loading-img.gif')}}" alt=""/></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>		
                    <section class="mt-3">
                        <div class="row module12 commoncheck">
                            <div class="col-md-6 col-12">
                                <h4>Candidate Details </h4>  
                                <div class="card card-shadow mb-2">
                                    <div class="card-body p-0 @if($count_applied==0)no-record @endif">
                                        <ul class="p-3 ul-list">
                                            <li>Total Nominations applied <span class="val-box">@if($count_applied>0){{$count_applied}}@else - @endif</span></li>
                                            <li>Contesting Candidates <span class="val-box">@if($count_contested>0){{$count_contested}}@else - @endif</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <h4 class="text-right">Nomination</h4> 	  
                                <div class="card card-shadow mb-4">
                                    <div class="card-body p-0 @if($count_accepted==0)no-record @endif">
                                        <table class="table custom-table text-center">
                                            <thead>
                                                <tr>
                                                    <th>Accepted</th>
                                                    <th>Rejected</th>
                                                    <th>Withdraw</th>
                                                </tr>  
                                            </thead> 
                                            <tbody>
                                                <tr>
                                                    <td>@if($count_accepted>0){{$count_accepted}}@else - @endif</td>
                                                    <td>@if($count_rejected>0){{$count_rejected}}@else - @endif</td>
                                                    <td>@if($count_withdraw>0){{$count_withdraw}}@else - @endif</td>
                                                </tr>  
                                            </tbody>  
                                        </table>  
                                       
                                    </div>	
                                </div>
                            </div>
                        </div>
                        <div class="module13 commoncheck">
                            <h4>Vulnarability/ Sensitive area mapping</h4>  
                            @php 
                            $non_class = 'no-record';
                            if($get_ac_entry){
                                if($get_ac_entry->vulnerable_area_pockets >0 || $get_ac_entry->critical_ps >0 || $get_ac_entry->expenditure_sensitive_constituencies >0){
                                    $non_class = '';
                                }
                            }
                            @endphp
                            <div class="card card-shadow mb-4">
                                <div class="card-body p-0 {{$non_class}}">
                                    <ul class="p-3 ul-list ul-li-ptb">
                                        <li>Vulnerable Area/ Pockets <span class="val-box">@if($get_ac_entry){{$get_ac_entry->vulnerable_area_pockets}}@else - @endif</span></li>
                                        <li>Critical PS <span class="val-box">@if($get_ac_entry){{$get_ac_entry->critical_ps}}@else - @endif</span></li>
                                        <li>Expenditure sensitive Constituencies <span class="val-box">@if($get_ac_entry){{$get_ac_entry->expenditure_sensitive_constituencies}}@else - @endif</span></li>
                                    </ul>  
                                </div>
                            </div>
                        </div>
                    </section>	
                    <div class="mt-3">
                        <div class="module14 commoncheck">
                            <h4>Election Schedule</h4>

                            <div class="card card-shadow mb-4">
                                <div class="card-body p-0">
                                    <table class="table custom-table text-center">
                                        <thead>
                                            <tr>
                                                @if(count($schdule)>1)<th>Phase</th>@endif
                                                <th>Total ACs for elections</th>
                                                <th>Notification Date</th>
                                                <th>Nomination Start Date</th>
                                                <th>Nomination Last Date</th>
                                                <th>Scrutiny Date</th> 
                                            </tr>  
                                        </thead> 
                                        <tbody>
                                            <?php $phase = 1;
                                            if ($schdule) {
                                                foreach ($schdule as $k => $v) { ?>
                                                    <tr>
                                                        @if(count($schdule)>1)<td>{{$phase}}</td>@endif
                                                        <td>{{$v->total_acs}}</td>
                                                        <td>{{date('d M Y',strtotime($v->DT_PRESS_ANNC))}}</td>
                                                        <td>{{date('d M Y',strtotime($v->DT_ISS_NOM))}}</td>
                                                        <td>{{date('d M Y',strtotime($v->LDT_IS_NOM))}}</td>
                                                        <td>{{date('d M Y',strtotime($v->DT_SCR_NOM))}}</td>
                                                    </tr>  

                                                    <?php $phase++;
                                                }
                                            } else { ?>
                                                <tr><td style="font-weight:normal;font-size:12px;" colspan="5">No record found</td></tr>
<?php } ?>
                                        </tbody>  
                                    </table>
                                </div>
                            </div> 
                            <div class="card card-shadow mb-4">
                                <div class="card-body p-0">
                                    <table class="table custom-table text-center">
                                        <thead>

                                            <tr>
                                                <th>Withdraw Date</th>
                                                <th>Poll date</th>
                                                <th>Date of Counting</th>
                                                <th>Date of Completion of elections</th>
                                            </tr>  

                                        </thead> 
                                        <tbody>
<?php $phase = 1;
if ($schdule) {
    foreach ($schdule as $k => $v) { ?>
                                                    <tr>
                                                        <td>{{date('d M Y',strtotime($v->LDT_WD_CAN))}}</td> 
                                                        <td>{{date('d M Y',strtotime($v->DATE_POLL))}}</td>
                                                        <td>{{date('d M Y',strtotime($v->DATE_COUNT))}}</td>
                                                        <td>{{date('d M Y',strtotime($v->DTB_EL_COM))}}</td>   
                                                    </tr>  
        <?php $phase++;
    }
} else { ?>
                                                <tr><td style="font-weight:normal;font-size:12px;" colspan="4">No record found</td></tr>
<?php } ?>
                                        </tbody>  
                                    </table>
                                </div>
                            </div>   
                        </div>
                    </div>	
                    <section class="mt-3">
                        <div class="module15 commoncheck">
                            <h4>Webcasting details</h4>  	
                            <div class="row">
                                <div class="col-md-12 col-12">
                                    <div class="card card-shadow mb-4">
                                        @php 
                                        $non_class = 'no-record';
                                        if($get_ac_entry){
                                            if($get_ac_entry->no_of_ps_webcasting >0 || $get_ac_entry->details_of_webcasting >0){
                                                $non_class = '';
                                            }
                                        }
                                        @endphp
                                        <div class="card-body p-0 {{$non_class}}">
                                            <ul class="pt-3 pb-0 pl-3 pr-3 ul-list">
                                                <li>No. of PS for webcasting <span class="val-box">@if($get_ac_entry){{$get_ac_entry->no_of_ps_webcasting}}@else - @endif</span></li>
                                                <li>Details of Webcasting <span class="val-box">@if($get_ac_entry){{$get_ac_entry->details_of_webcasting}}@else - @endif</span></li>
                                            </ul>  
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="mt-3 mb-4">
                        <div class="module16 commoncheck">
                            <h4>Election Results</h4>  
                            <div class="card card-shadow mb-4">
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table result-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Party</th>
                                                    <th class="text-right">Seat Won</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i = 1;
                                                $total_seats = 0;
                                                if (count($leadWinCount) > 0) {
                                                    foreach ($leadWinCount as $k => $val) {
                                                        $total_seats += $val->win;
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $i; ?></td>
                                                            <td><?php echo $val->lead_cand_party; ?></td>
                                                            <td class="text-right"><?php echo $val->win; ?></td>
                                                        </tr>
        <?php $i++;
    }
} else { ?>
                                                    <tr>
                                                        <td colspan="2">No record found</td>
                                                    </tr>
<?php } ?>
                                            </tbody>
                                            <thead class="tfoot">
                                            <th>#</th>
                                            <th>Total Seats</th>
                                            <th class="text-right"><?php echo $total_seats; ?></th>  
                                            </thead>  
                                        </table>	
                                    </div>	
                                </div>
                            </div>	
                        </div>
                    </section>		
                </div><!-- End Of Container-fluid Div -->
            </section>  
        </main>
        <!-- Tabular views ends here -->

    </section>
</main>


@endsection
@section('script')
<script type="text/javascript" src="{{ asset('js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/Chart.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/utils-chart.js') }}"></script>
<script type="text/javascript">
    function imgpathFunc(){
    return "http://10.199.104.241:82/";
    }

    var state_code = '<?php echo $st_code ?>';
	
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
    url: "getelectorbypsid",
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
    $("#snip").click(function(){
    $('#snip-inner').slideToggle();
    $("i", this).toggleClass("fa fa-bars fa fa-times");
    });
//Geo Location MAP
    $(function () {
    $("#st_code_val").change(function(){
        if($(this).val() !=''){
           $("#state_frm").submit();
        } 
    });
    var psdata = <?php print_r(json_encode($allpsdata)) ?>;
    var pscnt = psdata.length;
    var acpscnt = 0;
    //Default Map	
    var mymap = L.map('map', {
    center: [20.594, 78.962],
            zoom: 5,
            zoomSnap: 0.25,
            zoomDelta: 0.25,
            minZoom: 3.25,
            maxZoom: 6,
            zoomControl: true
            });
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(mymap);
    mymap.zoomControl.setPosition('topright');
    L.DomUtil.setOpacity(mymap.zoomControl.getContainer(), 0.5);
    $('select#state').change(function () {
    $("#loader").show();
    $('#amf').val('0');
    $('#emf').val('0');
    $('#pstype').val('0');
    $('#psphoto').val('0');
    $('#psfilter').val('0');
    var state_code = $(this).val();
    //alert(ac_value);
    var alldata = state_code.split('#');
    var ac_no = alldata[0];
    $.ajax({
    url: "statedetails",
            type: 'POST',
            data: {_token:'{{csrf_token()}}', state: state_code},
            success: function (result) {
            $.ajax({
            url: "getDistByState",
                    type: 'POST',
                    data: {_token:'{{csrf_token()}}', state: state_code},
                    success: function(response) {
                    var jsonText = $.parseJSON(response);
                    var text = [];
                    text.push('<option value="0">Select District</option>');
                    for (i = 0; i < jsonText.id.length; i++) {
                    text.push('<option value=' + jsonText.id[i] + '>' + jsonText.val[i] + '</option>');
                    }
                    $('#dist').html(text);
                    $("#loader").hide();
                    },
                    error: function(error) {
                    console.log(error.responseText);
                    }
            });
            var container = L.DomUtil.get('map');
            if (container != null){ container._leaflet_id = null; }

            var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    osmAttrib = '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    osm = L.tileLayer(osmUrl, { maxZoom: 18, attribution: osmAttrib }),
                    //map id
                    map = new L.Map('map', { center: new L.LatLng(28.6337379, 77.1972581), zoom: 13, draggable: 'true', fullscreenControl: {
                    pseudoFullscreen: false
                    }, }),
                    //Drawman Set
                    drawnItems = L.featureGroup().addTo(map);
            L.control.layers({
            'osm': osm.addTo(map),
                    "google": L.tileLayer('http://www.google.cn/maps/vt?lyrs=s@189&gl=cn&x={x}&y={y}&z={z}', {
                    attribution: 'google'
                    })
            }, { 'drawlayer': drawnItems }, {fullscreenControl: true}, { position: 'topleft', collapsed: false }).addTo(map);
            setTimeout(function(){ map.invalidateSize(true)}, 0);
            //marker.dragging.enable();
            // run kml on leaflet
            //var dist_no = '8';
            //var st_code = "U05";
            var urllink = "<?php echo url('/'); ?>/kmlmap/" + state_code + "/" + state_code + ".kml";
            //var urllink = "<?php echo url('/'); ?>/kmlmap/" + st_code + "/District/" + dist_no + ".kml";
            var runLayer = omnivore.kml(urllink, {async: true, })
                    .on('ready', function() {
                    map.fitBounds(runLayer.getBounds());
                    })
                    .addTo(map);
            var iconImage = L.icon({
            iconUrl: '<?php echo asset('img/garuda/marker-icons/force.png') ?>',
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
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
            $('#distText').hide();
            $('#distText2').hide();
            $('#acText').hide();
            $('#dstext').hide();
            $('#acText2').hide();
            $('#act').hide();
            $('#psText2').hide();
            $('#psText').hide();
            $('#pct').hide();
            $('#sttext').html($("#state option:selected").text());
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
            $('#e_count span').text(' : Not Available');
            }
            if (result[2][0].p_total != '' && result[2][0].p_total != null)
            {
            $('#p_count strong').text('PWD Count');
            $('#p_count span').text(' : ' + result[2][0].p_total);
            }
            else
            {
            $('#p_count strong').text('PWD Count');
            $('#p_count span').text(' : Not Available');
            }
            }
            $.each(result[0], function (index, value) {
            var rsdata = getElectorByPSID(value.id);
            var lat = value.lattitude;
            var lng = value.longitude;
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
            var acnod = window.btoa(value.ac_no);
            var psidd = window.btoa(value.id);
            var psno = window.btoa(value.ps_no);
            var customOptions =
            {
            'maxWidth': '300',
                    'className' : 'custom'
            }
            if (value.ps_address != null && value.ps_address != '')
            {
            var psaddress = value.ps_address;
            }
            else
            {
            var psaddress = 'Not available';
            }
            var psid = value.id;
            var imgpath = imgpathFunc();
            var psdatas = $("#psdataval").val();
            var eleceach = psdatas.split("***");
            if (eleceach[0] != 'NA' && eleceach[0] != undefined && eleceach[0] != undefined){
            var ps_id = eleceach[0];
            } else {
            var ps_id = "NA";
            }
            if (eleceach[1] != 'NA' && eleceach[1] != undefined && eleceach[1] != undefined){
            var electors_total = eleceach[1];
            } else {
            var electors_total = "NA";
            }
            if (eleceach[2] != 'NA' && eleceach[2] != undefined && eleceach[2] != undefined){
            var electors_male = eleceach[2];
            } else {
            var electors_male = "NA";
            }
            if (eleceach[3] != 'NA' && eleceach[3] != undefined && eleceach[3] != undefined){
            var electors_female = eleceach[3];
            } else {
            var electors_female = "NA";
            }
            if (eleceach[4] != 'NA' && eleceach[4] != undefined && eleceach[4] != undefined){
            var electors_other = eleceach[4];
            } else {
            var electors_other = "NA";
            }
            if (eleceach[5] != 'NA' && eleceach[5] != undefined && eleceach[5] != undefined){
            var no_of_pwd_voters = eleceach[5];
            } else {
            var no_of_pwd_voters = "NA";
            }
            if (eleceach[6] != 'NA' && eleceach[6] != undefined && eleceach[6] != undefined){
            var dd = eleceach[6];
            i = '<img src=' + '"' + imgpath + eleceach[6] + '"' + ' class="img-fluid"/>';
            } else {
            var dd = "NA";
            i = '<img src="{{asset('img / pro - img.jpg')}}" class="img-fluid"/>';
            }
            var contentString = '<div class="pop-sec"><ul class="nav pb-2 nav-tabs" role="tablist">' +
                    '<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#detailss' + psid + '">Basic Details</a></li>' +
                    '<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#facilitys' + psid + '">Facility</a></li></ul>' +
                    '<div class="tab-content">' +
                    '<div id="detailss' + psid + '" class="mt-2 container tab-pane active"><p class="text-bold">' + value.ps_name_en + '</p>\n\
                   <p>' + psaddress + '</p><p>No. of electrols : ' + electors_total + '</p><p>No. of pwd electrols : ' + no_of_pwd_voters + '</p>\n\
                   <p><a href="#">Navigate to here</a></p></div>' +
                    '<div id="facilitys' + psid + '" class="container tab-pane fade">\n\
                   <p>Water <i class="fa fa-check text-success"></i></p>\n\
                   <p>Electricity <i class="fa fa-close text-danger"></i></p>\n\
                   <p>Toilet <i class="fa fa-check text-success"></i></p>\n\
                   <p>Parking <i class="fa fa-close text-danger"></i></p>\n\
                   <p>PWD <i class="fa fa-check text-success"></i></p>\n\
                   <p><a href="{{url('psdetailsmarker')}}/' + psidd + '/' + acnod + '/' + psno + '">More facility Details</a></p></div>' +
                    '</div>' +
                    '</div>';
            var newpopup = L.popup({
            closeOnClick: false,
                    autoClose: true
            }).setContent(contentString);
            var marker = L.marker([lat, lng], {bounceOnAdd: false, icon: iconImage});
            marker.addTo(map);
            marker.bindPopup(newpopup);
            //marker.openPopup();
            //End Marker   
            });
            $('#ps').empty();
            var ps = '<option value="">Select Polling Station</option>';
            $.each(result[1], function (index, value) {
            ps = ps + '<option value="' + value.id + '#' + value.lattitude + '#' + value.longitude + '#' + value.ps_type_id + '" >' + value.ps_no + '-' + value.ps_name_en + '</option>';
            });
            $('#ps').append(ps);
            }
            else
            {
            alert('No polling station available');
            }
            }, error: function(error) {
    console.log(error.responseText);
    }
    });
    });
    //end onchange state
    //start onchange district
    $('select#dist').change(function () {
    $("#loader").show();
    $('#amf').val('0');
    $('#emf').val('0');
    $('#pstype').val('0');
    $('#psphoto').val('0');
    $('#psfilter').val('0');
    var dist = $(this).val();
    var state = $('#state').val();
    if (state == '0'){
    alert("Please select state");
    return false;
    }
    var alldata = dist.split('#');
    var ac_no = alldata[0];
    var latitude = parseFloat(alldata[1]);
    var long = parseFloat(alldata[2]);
    $.ajax({
    url: "distdetails",
            type: 'POST',
            data: {_token:'{{csrf_token()}}', state: state, dist:dist},
            success: function (result) {
            $.ajax({
            url: "getAcByDistState",
                    type: 'POST',
                    data: {_token:'{{csrf_token()}}', state: state, dist:dist},
                    success: function(res) {
                    //alert(res);
                    //console.log(res);
                    var jsonTextac = $.parseJSON(res);
                    var textac = [];
                    textac.push('<option value="0">Select AC</option>');
                    if (jsonTextac.id){
                    for (i = 0; i < jsonTextac.id.length; i++) {
                    textac.push('<option value=' + jsonTextac.id[i] + '>' + jsonTextac.val[i] + '</option>');
                    }
                    }
                    $('#ac').html(textac);
                    $("#loader").hide();
                    },
                    error: function(error) {
                    console.log(error.responseText);
                    }
            });
            var container = L.DomUtil.get('map');
            if (container != null){ container._leaflet_id = null; }
            var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    osmAttrib = '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    osm = L.tileLayer(osmUrl, { maxZoom: 18, attribution: osmAttrib }),
                    //map id
                    map = new L.Map('map', { center: new L.LatLng(28.6337379, 77.1972581), zoom: 13, fullscreenControl: {
                    pseudoFullscreen: false
                    }, }),
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
            var urllink = "<?php echo url('/'); ?>/kmlmap/" + state + "/District/" + dist + ".kml";
            var runLayer = omnivore.kml(urllink, {async: true, })
                    .on('ready', function() {
                    map.fitBounds(runLayer.getBounds());
                    })
                    .addTo(map);
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/polling_station/ps.png')}}",
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
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
            $('#sttext').html($("#state option:selected").text());
            $('#t_pscount strong').text('Total P.S');
            $('#t_pscount span').text(' : ' + acpscnt);
            $('#pwd_facility strong').css('display', 'none');
            $('#pwd_facility span').css('display', 'none');
            $('#textnew').show();
            $('#distText').show();
            $('#distText2').show();
            $('#acText').hide();
            $('#acText2').hide();
            $('#sttext').html($("#state option:selected").text());
            $('#dstext').show().html($("#dist option:selected").text());
            $('#act').hide();
            $('#psText2').hide();
            $('#psText').hide();
            $('#pct').hide();
            $('#p_count').show();
            if (result[2][0].e_total != '' && result[2][0].e_total != null)
            {
            $('#e_count strong').text('Electors Count');
            $('#e_count span').text(' : ' + result[2][0].e_total);
            }
            else
            {
            $('#e_count strong').text('Electors Count');
            $('#e_count span').text(' : Not Available');
            }
            if (result[2][0].p_total != '' && result[2][0].p_total != null)
            {
            $('#p_count strong').text('PWD Count');
            $('#p_count span').text(' : ' + result[2][0].p_total);
            }
            else
            {
            $('#p_count strong').text('PWD Count');
            $('#p_count span').text(' : Not Available');
            }
            }
            $.each(result[0], function (index, value) {
            var rsdata = getElectorByPSID(value.id);
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
            var psaddress = 'Not available';
            }
            var psid = value.id;
            var imgpath = imgpathFunc();
            var psdatas = $("#psdataval").val();
            var eleceach = psdatas.split("***");
            if (eleceach[0] != 'NA' && eleceach[0] != undefined && eleceach[0] != undefined){
            var ps_id = eleceach[0];
            } else {
            var ps_id = "NA";
            }
            if (eleceach[1] != 'NA' && eleceach[1] != undefined && eleceach[1] != undefined){
            var electors_total = eleceach[1];
            } else {
            var electors_total = "NA";
            }
            if (eleceach[2] != 'NA' && eleceach[2] != undefined && eleceach[2] != undefined){
            var electors_male = eleceach[2];
            } else {
            var electors_male = "NA";
            }
            if (eleceach[3] != 'NA' && eleceach[3] != undefined && eleceach[3] != undefined){
            var electors_female = eleceach[3];
            } else {
            var electors_female = "NA";
            }
            if (eleceach[4] != 'NA' && eleceach[4] != undefined && eleceach[4] != undefined){
            var electors_other = eleceach[4];
            } else {
            var electors_other = "NA";
            }
            if (eleceach[5] != 'NA' && eleceach[5] != undefined && eleceach[5] != undefined){
            var no_of_pwd_voters = eleceach[5];
            } else {
            var no_of_pwd_voters = "NA";
            }

            if (eleceach[6] != 'NA' && eleceach[6] != undefined && eleceach[6] != undefined){
            var dd = eleceach[6];
            i = '<img src=' + '"' + imgpath + eleceach[6] + '"' + ' class="img-fluid"/>';
            } else {
            var dd = "NA";
            i = '<img src="{{asset('img / pro - img.jpg')}}" class="img-fluid"/>';
            }
            var contentString = '<div class="pop-sec"><ul class="nav pb-2 nav-tabs" role="tablist">' +
                    '<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#detailsd' + psid + '">Basic Details</a></li>' +
                    '<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#facilityd' + psid + '">Facility</a></li></ul>' +
                    '<div class="tab-content">' +
                    '<div id="detailsd' + psid + '" class="mt-2 container tab-pane active"><p class="text-bold">' + value.ps_name_en + '</p>\n\
                   <p>' + psaddress + '</p><p>No. of electrols : ' + electors_total + '</p><p>No. of pwd electrols : ' + no_of_pwd_voters + '</p>\n\
                   <p><a href="#">Navigate to here</a></p></div>' +
                    '<div id="facilityd' + psid + '" class="container tab-pane fade">\n\
                   <p>Water <i class="fa fa-check text-success"></i></p>\n\
                   <p>Electricity <i class="fa fa-close text-danger"></i></p>\n\
                   <p>Toilet <i class="fa fa-check text-success"></i></p>\n\
                   <p>Parking <i class="fa fa-close text-danger"></i></p>\n\
                   <p>PWD <i class="fa fa-check text-success"></i></p></div>' +
                    '</div>' +
                    '</div>';
            var newpopup = L.popup({
            closeOnClick: false,
                    autoClose: true
            }).setContent(contentString);
            var marker = L.marker([value.lattitude, value.longitude], {bounceOnAdd: false, icon: iconImage});
            marker.addTo(map);
            marker.bindPopup(newpopup);
//            marker.openPopup();
            });
            $('#ps').empty();
            var ps = '<option value="">Select Polling Station</option>';
            $.each(result[1], function (index, value) {
            ps = ps + '<option value="' + value.id + '#' + value.lattitude + '#' + value.longitude + '#' + value.ps_type_id + '" >' + value.ps_no + '-' + value.ps_name_en + '</option>';
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
    //end onchange district
    //start onchange ac
    $('select#ac').change(function () {
    //resetDropdown('ac');  
    $("#loader").show();
    $('#amf').val('0');
    $('#emf').val('0');
    $('#pstype').val('0');
    $('#psphoto').val('0');
    $('#psfilter').val('0');
    var ac = $(this).val();
    var acname = $('select#ac option:selected').text();
    var dist = $('#dist').val();
    var state = $('#state').val();
    if (state == '0'){
    alert("Please select state");
    return false;
    }
    var alldata = ac.split('#');
    var ac_no = alldata[0];
    var latitude = parseFloat(alldata[1]);
    var long = parseFloat(alldata[2]);
    $.ajax({
    url: "acpsdetails",
            type: 'POST',
            data: {_token:'{{csrf_token()}}', state: state, dist: dist, ac: ac_no},
            success: function (result) {
            var container = L.DomUtil.get('map');
            if (container != null){ container._leaflet_id = null; }
            var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    osmAttrib = '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    osm = L.tileLayer(osmUrl, { maxZoom: 18, attribution: osmAttrib }),
                    //map id
                    map = new L.Map('map', { center: new L.LatLng(28.6337379, 77.1972581), zoom: 13, fullscreenControl: {
                    pseudoFullscreen: false
                    }, }),
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
            var urllink = "<?php echo url('/'); ?>/kmlmap/" + state + "/AC/" + acname + ".kml";
            var runLayer = omnivore.kml(urllink, {async: true, })
                    .on('ready', function() {
                    map.fitBounds(runLayer.getBounds());
                    })
                    .addTo(map);
            var iconImage = L.icon({
            iconUrl: "{{asset('img/garuda/polling_station/ps.png')}}",
                    iconSize: [40, 60], // size of the icon
                    popupAnchor: [0, - 15]
            });
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
            $('#e_count span').text(' : Not Available');
            }
            if (result[2][0].p_total != '' && result[2][0].p_total != null)
            {
            $('#p_count strong').text('PWD Count');
            $('#p_count span').text(' : ' + result[2][0].p_total);
            }
            else
            {
            $('#p_count strong').text('PWD Count');
            $('#p_count span').text(' : Not Available');
            }
            }
            $.each(result[0], function (index, value) {
            var rsdata = getElectorByPSID(value.id);
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
            var psaddress = 'Not available';
            }
            var psid = value.id;
            var imgpath = imgpathFunc();
            var psdatas = $("#psdataval").val();
            var eleceach = psdatas.split("***");
            if (eleceach[0] != 'NA' && eleceach[0] != undefined && eleceach[0] != undefined){
            var ps_id = eleceach[0];
            } else {
            var ps_id = "NA";
            }
            if (eleceach[1] != 'NA' && eleceach[1] != undefined && eleceach[1] != undefined){
            var electors_total = eleceach[1];
            } else {
            var electors_total = "NA";
            }
            if (eleceach[2] != 'NA' && eleceach[2] != undefined && eleceach[2] != undefined){
            var electors_male = eleceach[2];
            } else {
            var electors_male = "NA";
            }
            if (eleceach[3] != 'NA' && eleceach[3] != undefined && eleceach[3] != undefined){
            var electors_female = eleceach[3];
            } else {
            var electors_female = "NA";
            }
            if (eleceach[4] != 'NA' && eleceach[4] != undefined && eleceach[4] != undefined){
            var electors_other = eleceach[4];
            } else {
            var electors_other = "NA";
            }
            if (eleceach[5] != 'NA' && eleceach[5] != undefined && eleceach[5] != undefined){
            var no_of_pwd_voters = eleceach[5];
            } else {
            var no_of_pwd_voters = "NA";
            }

            if (eleceach[6] != 'NA' && eleceach[6] != undefined && eleceach[6] != undefined){
            var dd = eleceach[6];
            i = '<img src=' + '"' + imgpath + eleceach[6] + '"' + ' class="img-fluid"/>';
            } else {
            var dd = "NA";
            i = '<img src="{{asset('img / pro - img.jpg')}}" class="img-fluid"/>';
            }
            var contentString = '<div class="pop-sec"><ul class="nav pb-2 nav-tabs" role="tablist">' +
                    '<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#detailsac' + psid + '">Basic Details</a></li>' +
                    '<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#facilityac' + psid + '">Facility</a></li></ul>' +
                    '<div class="tab-content">' +
                    '<div id="detailsac' + psid + '" class="mt-2 container tab-pane active"><p class="text-bold">' + value.ps_name_en + '</p>\n\
                   <p>' + psaddress + '</p><p>No. of electrols : ' + electors_total + '</p><p>No. of pwd electrols : ' + no_of_pwd_voters + '</p>\n\
                   <p><a href="#">Navigate to here</a></p></div>' +
                    '<div id="facilityac' + psid + '" class="container tab-pane fade">\n\
                   <p>Water <i class="fa fa-check text-success"></i></p>\n\
                   <p>Electricity <i class="fa fa-close text-danger"></i></p>\n\
                   <p>Toilet <i class="fa fa-check text-success"></i></p>\n\
                   <p>Parking <i class="fa fa-close text-danger"></i></p>\n\
                   <p>PWD <i class="fa fa-check text-success"></i></p></div>' +
                    '</div>' +
                    '</div>';
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
    //end onchange ac
    //start ps onchange
    $('select#ps').change(function () {
    var acname = $('select#ac option:selected').text();
    var state = $('select#state').val();
    resetDropdown('ps');
    var container = L.DomUtil.get('map');
    if (container != null){ container._leaflet_id = null; }
    var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            osmAttrib = '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            osm = L.tileLayer(osmUrl, { maxZoom: 18, attribution: osmAttrib }),
            //map id
            map = new L.Map('map', { center: new L.LatLng(28.6337379, 77.1972581), zoom: 13, draggable: true, fullscreenControl: {
            pseudoFullscreen: false
            }, }),
            //map.dragging.enable();
            //map.touchZoom.enable();
            //map.doubleClickZoom.enable();
//map.scrollWheelZoom.enable();
//map.boxZoom.enable();
//map.keyboard.enable();
//if (map.tap) map.tap.enable();
//document.getElementById('map').style.cursor='grab';
            //map.dragging.enable();

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
    var urllink = "<?php echo url('/'); ?>/kmlmap/" + state + "/AC/" + acname + ".kml";
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
                    /*polygon: {
                     allowIntersection: false,
                     showArea: true
                     }*/
            }
    }));
    //End Control Icon - search and other icon
    var state = $("#state :selected").val();
    if (state == '0'){
    alert("Please select state");
    return false;
    }
    $("#loader").show();
    var dist = $("#dist :selected").val();
    var acno = $("#ac :selected").val();
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
//console.log(error.status);
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
            var rsdata = getElectorByPSID(result[0][0]['id']);
            $("#loader").hide();
            $('#t_pscount strong').text('PS Name');
            $('#t_pscount span').text(' : ' + result[0][0].ps_name_en);
            $('#e_count strong').text('Electors Count');
            if (result[1][0].e_total != null){
            $('#e_count span').text(' : ' + result[1][0].e_total);
            } else {
            $('#e_count span').text(' : Not Available');
            }

            $('#p_count').show();
            $('#p_count strong').text('PWD Count');
            if (result[1][0].p_total != null){
            $('#p_count span').text(' : ' + result[1][0].p_total);
            } else {
            $('#p_count span').text(' : Not Available');
            }

            $('#textnew').show();
            $('#psText').show();
            $('#psText2').show();
            $('#pst').show();
            $('#pct').show().html($("#ps option:selected").text());
            $('#pwd_facility strong').css('display', 'none');
            $('#pwd_facility span').css('display', 'none');
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
            var imgpath = imgpathFunc();
            var psdatas = $("#psdataval").val();
            var eleceach = psdatas.split("***");
            if (eleceach[0] != 'NA' && eleceach[0] != undefined && eleceach[0] != undefined){
            var ps_id = eleceach[0];
            } else {
            var ps_id = "NA";
            }
            if (eleceach[1] != 'NA' && eleceach[1] != undefined && eleceach[1] != undefined){
            var electors_total = eleceach[1];
            } else {
            var electors_total = "NA";
            }
            if (eleceach[2] != 'NA' && eleceach[2] != undefined && eleceach[2] != undefined){
            var electors_male = eleceach[2];
            } else {
            var electors_male = "NA";
            }
            if (eleceach[3] != 'NA' && eleceach[3] != undefined && eleceach[3] != undefined){
            var electors_female = eleceach[3];
            } else {
            var electors_female = "NA";
            }
            if (eleceach[4] != 'NA' && eleceach[4] != undefined && eleceach[4] != undefined){
            var electors_other = eleceach[4];
            } else {
            var electors_other = "NA";
            }
            if (eleceach[5] != 'NA' && eleceach[5] != undefined && eleceach[5] != undefined){
            var no_of_pwd_voters = eleceach[5];
            } else {
            var no_of_pwd_voters = "NA";
            }

            if (eleceach[6] != 'NA' && eleceach[6] != undefined && eleceach[6] != undefined){
            var dd = eleceach[6];
            i = '<img src=' + '"' + imgpath + eleceach[6] + '"' + ' class="img-fluid"/>';
            } else {
            var dd = "NA";
            i = '<img src="{{asset('img / pro - img.jpg')}}" class="img-fluid"/>';
            }

            var contentString = '<div class="pop-sec"><ul class="nav pb-2 nav-tabs" role="tablist">' +
                    '<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#detailsps' + psid + '">Basic Details</a></li>' +
                    '<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#facilityps' + psid + '">Facility</a></li></ul>' +
                    '<div class="tab-content">' +
                    '<div id="detailsps' + psid + '" class="mt-2 container tab-pane active"><p class="text-bold">' + result[0][0]['ps_name_en'] + '</p>\n\
                   <p>' + psaddress + '</p><p>No. of electrols : ' + electors_total + '</p><p>No. of pwd electrols : ' + no_of_pwd_voters + '</p>\n\
                   <p><a href="#">Navigate to here</a></p></div>' +
                    '<div id="facilityps' + psid + '" class="container tab-pane fade">\n\
                   <p>Water <i class="fa fa-check text-success"></i></p>\n\
                   <p>Electricity <i class="fa fa-close text-danger"></i></p>\n\
                   <p>Toilet <i class="fa fa-check text-success"></i></p>\n\
                   <p>Parking <i class="fa fa-close text-danger"></i></p>\n\
                   <p>PWD <i class="fa fa-check text-success"></i></p></div>' +
                    '</div>' +
                    '</div>';
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
    resetDropdown('pstype');
    var acname = $('select#ac option:selected').text();
    var state = $('select#state').val();
    var ps_type_id = $(this).val();
    //alert(ps_type_id);            
    $("#numberCount").show();
    var state = $('#state').val();
    if (state == '0'){
    alert("Please select state");
    return false;
    }

    var dist = 0;
    var ac = 0;
    var acno = 0;
    var dist = $('#dist').val();
    var ac = $('#ac').val();
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
                    osm = L.tileLayer(osmUrl, { maxZoom: 18, attribution: osmAttrib }),
                    //map id
                    map = new L.Map('map', { center: new L.LatLng(28.6337379, 77.1972581), zoom: 13, fullscreenControl: {
                    pseudoFullscreen: false
                    }, }),
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
            var urllink = "<?php echo url('/'); ?>/kmlmap/" + state + "/AC/" + acname + ".kml";
            var runLayer = omnivore.kml(urllink, {async: true, })
                    .on('ready', function() {
                    map.fitBounds(runLayer.getBounds());
                    })
                    .addTo(map);
            $('#psText2').hide();
            $('#psText').hide();
            $('#pct').hide();
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
            $('#e_count span').text(' : Not Available');
            }
            if (result[1][0].p_total != '' && result[1][0].p_total != null)
            {
            $('#p_count strong').text('PWD Count');
            $('#p_count span').text(' : ' + result[1][0].p_total);
            }
            else
            {
            $('#p_count strong').text('PWD Count');
            $('#p_count span').text(' : Not Available');
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
            var psaddress = 'Not available';
            }

            var rsdata = getElectorByPSID(value.id);
            var imgpath = imgpathFunc();
            var psdatas = $("#psdataval").val();
            var eleceach = psdatas.split("***");
            if (eleceach[0] != 'NA' && eleceach[0] != undefined && eleceach[0] != undefined){
            var ps_id = eleceach[0];
            } else {
            var ps_id = "NA";
            }
            if (eleceach[1] != 'NA' && eleceach[1] != undefined && eleceach[1] != undefined){
            var electors_total = eleceach[1];
            } else {
            var electors_total = "NA";
            }
            if (eleceach[2] != 'NA' && eleceach[2] != undefined && eleceach[2] != undefined){
            var electors_male = eleceach[2];
            } else {
            var electors_male = "NA";
            }
            if (eleceach[3] != 'NA' && eleceach[3] != undefined && eleceach[3] != undefined){
            var electors_female = eleceach[3];
            } else {
            var electors_female = "NA";
            }
            if (eleceach[4] != 'NA' && eleceach[4] != undefined && eleceach[4] != undefined){
            var electors_other = eleceach[4];
            } else {
            var electors_other = "NA";
            }
            if (eleceach[5] != 'NA' && eleceach[5] != undefined && eleceach[5] != undefined){
            var no_of_pwd_voters = eleceach[5];
            } else {
            var no_of_pwd_voters = "NA";
            }

            if (eleceach[6] != 'NA' && eleceach[6] != undefined && eleceach[6] != undefined){
            var dd = eleceach[6];
            i = '<img src=' + '"' + imgpath + eleceach[6] + '"' + ' class="img-fluid"/>';
            } else {
            var dd = "NA";
            i = '<img src="{{asset('img / pro - img.jpg')}}" class="img-fluid"/>';
            }


            var contentString = '<div class="pop-sec"><ul class="nav pb-2 nav-tabs" role="tablist">' +
                    '<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#detailspstype' + psid + '">Basic Details</a></li>' +
                    '<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#facility">Facility</a></li></ul>' +
                    '<div class="tab-content">' +
                    '<div id="detailspstype' + psid + '" class="mt-2 container tab-pane active"><p class="text-bold">' + value.ps_name_en + '</p>\n\
                   <p>' + psaddress + '</p><p>No. of electrols : ' + electors_total + '</p><p>No. of pwd electrols : ' + no_of_pwd_voters + '</p>\n\
                   <p><a href="#">Navigate to here</a></p></div>' +
                    '<div id="facility" class="container tab-pane fade">\n\
                   <p>Water <i class="fa fa-check text-success"></i></p>\n\
                   <p>Electricity <i class="fa fa-close text-danger"></i></p>\n\
                   <p>Toilet <i class="fa fa-check text-success"></i></p>\n\
                   <p>Parking <i class="fa fa-close text-danger"></i></p>\n\
                   <p>PWD <i class="fa fa-check text-success"></i></p></div>' +
                    '</div>' +
                    '</div>';
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
            alert('No polling station available');
            }

            },
            error: function(error) {
            //console.log(error.responseText);
            //console.log(error);
            alert(error.responseText);
            }
    });
    });
    //end pstype onchange 
    //start electors filter onchange 
    //Onclick Electors range bar filter
    $('div#findElectorsByRangeBar').click(function () {
    resetDropdown('findElectorsByRangeBar');
    $("#numberCount").show();
    var state = $('#state').val();
    if (state == '0'){
    alert("Please select state");
    return false;
    }
    var acname = $('select#ac option:selected').text();
    var state = $('select#state').val();
    var dist = 0;
    var ac = 0;
    var acno = 0;
    var dist = $('#dist').val();
    var ac = $("#ac :selected").val();
    var electorrange = $('#electorrange').val();
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
            osm = L.tileLayer(osmUrl, { maxZoom: 18, attribution: osmAttrib }),
            //map id
            map = new L.Map('map', { center: new L.LatLng(28.6337379, 77.1972581), zoom: 13, fullscreenControl: {
            pseudoFullscreen: false
            }, }),
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
    var urllink = "<?php echo url('/'); ?>/kmlmap/" + state + "/AC/" + acname + ".kml";
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
            var psaddress = 'Not available';
            }

            var rsdata = getElectorByPSID(value.psid);
            var imgpath = imgpathFunc();
            var psdatas = $("#psdataval").val();
            var eleceach = psdatas.split("***");
            if (eleceach[0] != 'NA' && eleceach[0] != undefined && eleceach[0] != undefined){
            var ps_id = eleceach[0];
            } else {
            var ps_id = "NA";
            }
            if (eleceach[1] != 'NA' && eleceach[1] != undefined && eleceach[1] != undefined){
            var electors_total = eleceach[1];
            } else {
            var electors_total = "NA";
            }
            if (eleceach[2] != 'NA' && eleceach[2] != undefined && eleceach[2] != undefined){
            var electors_male = eleceach[2];
            } else {
            var electors_male = "NA";
            }
            if (eleceach[3] != 'NA' && eleceach[3] != undefined && eleceach[3] != undefined){
            var electors_female = eleceach[3];
            } else {
            var electors_female = "NA";
            }
            if (eleceach[4] != 'NA' && eleceach[4] != undefined && eleceach[4] != undefined){
            var electors_other = eleceach[4];
            } else {
            var electors_other = "NA";
            }
            if (eleceach[5] != 'NA' && eleceach[5] != undefined && eleceach[5] != undefined){
            var no_of_pwd_voters = eleceach[5];
            } else {
            var no_of_pwd_voters = "NA";
            }
            if (eleceach[6] != 'NA' && eleceach[6] != undefined && eleceach[6] != undefined){
            var dd = eleceach[6];
            i = '<img src=' + '"' + imgpath + eleceach[6] + '"' + ' class="img-fluid"/>';
            } else {
            var dd = "NA";
            i = '<img src="{{asset('img / pro - img.jpg')}}" class="img-fluid"/>';
            }

            var contentString = '<div class="pop-sec"><ul class="nav pb-2 nav-tabs" role="tablist">' +
                    '<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#details' + psid + '">Basic Details</a></li>' +
                    '<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#facility">Facility</a></li></ul>' +
                    '<div class="tab-content">' +
                    '<div id="details' + psid + '" class="mt-2 container tab-pane active"><p class="text-bold">' + value.ps_name_en + '</p>\n\
                   <p>' + psaddress + '</p><p>No. of electrols : ' + electors_total + '</p><p>No. of pwd electrols : ' + no_of_pwd_voters + '</p>\n\
                   <p><a href="#">Navigate to here</a></p></div>' +
                    '<div id="facility" class="container tab-pane fade">\n\
                   <p>Water <i class="fa fa-check text-success"></i></p>\n\
                   <p>Electricity <i class="fa fa-close text-danger"></i></p>\n\
                   <p>Toilet <i class="fa fa-check text-success"></i></p>\n\
                   <p>Parking <i class="fa fa-close text-danger"></i></p>\n\
                   <p>PWD <i class="fa fa-check text-success"></i></p></div>' +
                    '</div>' +
                    '</div>';
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
//                    else
//                    {
//                       alert('This facility is not available')
//                    }
            },
            error: function(error) {
            console.log(error.responseText);
            }
    });
    });
    //end electors filter onchange 
    //start pwd facility onchange
    $('select#pwdfacility').change(function () {
    resetDropdown('pwdfacility');
    $("#numberCount").show();
    $('#amf').val('0');
    $('#pwd_facility strong').css('display', 'none');
    $('#pwd_facility span').css('display', 'none');
    var state = $('#state').val();
    if (state == '0'){
    alert("Please select state");
    return false;
    }
    $("#loader").show();
    var dist = 0;
    var ac = 0;
    var acno = 0;
    var dist = $('#dist').val();
    var ac = $('#ac').val();
    var acname = $('select#ac option:selected').text();
    var pwdfacility = $(this).val();
    var acno = $("#ac :selected").val();
    var container = L.DomUtil.get('map');
    if (container != null){ container._leaflet_id = null; }
    var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            osmAttrib = '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            osm = L.tileLayer(osmUrl, { maxZoom: 18, attribution: osmAttrib }),
            //map id
            map = new L.Map('map', { center: new L.LatLng(28.6337379, 77.1972581), zoom: 13, fullscreenControl: {
            pseudoFullscreen: false
            }, }),
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
    var urllink = "<?php echo url('/'); ?>/kmlmap/" + state + "/AC/" + acname + ".kml";
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
            var psaddress = 'Not available';
            }

            var imgpath = imgpathFunc();
            var psdatas = $("#psdataval").val();
            var eleceach = psdatas.split("***");
            if (eleceach[0] != 'NA' && eleceach[0] != undefined && eleceach[0] != undefined){
            var ps_id = eleceach[0];
            } else {
            var ps_id = "NA";
            }
            if (eleceach[1] != 'NA' && eleceach[1] != undefined && eleceach[1] != undefined){
            var electors_total = eleceach[1];
            } else {
            var electors_total = "NA";
            }
            if (eleceach[2] != 'NA' && eleceach[2] != undefined && eleceach[2] != undefined){
            var electors_male = eleceach[2];
            } else {
            var electors_male = "NA";
            }
            if (eleceach[3] != 'NA' && eleceach[3] != undefined && eleceach[3] != undefined){
            var electors_female = eleceach[3];
            } else {
            var electors_female = "NA";
            }
            if (eleceach[4] != 'NA' && eleceach[4] != undefined && eleceach[4] != undefined){
            var electors_other = eleceach[4];
            } else {
            var electors_other = "NA";
            }
            if (eleceach[5] != 'NA' && eleceach[5] != undefined && eleceach[5] != undefined){
            var no_of_pwd_voters = eleceach[5];
            } else {
            var no_of_pwd_voters = "NA";
            }
            //alert(imgpath+eleceach[6]);
            if (eleceach[6] != 'NA' && eleceach[6] != undefined && eleceach[6] != undefined){
            var dd = eleceach[6];
            i = '<img src=' + '"' + imgpath + eleceach[6] + '"' + ' class="img-fluid"/>';
            } else {
            var dd = "NA";
            i = '<img src="{{asset('img / pro - img.jpg')}}" class="img-fluid"/>';
            }
            var contentString = '<div class="pop-sec"><ul class="nav pb-2 nav-tabs" role="tablist">' +
                    '<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#detailspwdfacility' + psid + '">Basic Details</a></li>' +
                    '<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#facility">Facility</a></li></ul>' +
                    '<div class="tab-content">' +
                    '<div id="detailspwdfacility' + psid + '" class="mt-2 container tab-pane active"><p class="text-bold">' + value.ps_name_en + '</p>\n\
                   <p>' + psaddress + '</p><p>No. of electrols : ' + electors_total + '</p><p>No. of pwd electrols : ' + no_of_pwd_voters + '</p>\n\
                   <p><a href="#">Navigate to here</a></p></div>' +
                    '<div id="facility" class="container tab-pane fade">\n\
                   <p>Water <i class="fa fa-check text-success"></i></p>\n\
                   <p>Electricity <i class="fa fa-close text-danger"></i></p>\n\
                   <p>Toilet <i class="fa fa-check text-success"></i></p>\n\
                   <p>Parking <i class="fa fa-close text-danger"></i></p>\n\
                   <p>PWD <i class="fa fa-check text-success"></i></p></div>' +
                    '</div>' +
                    '</div>';
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
            alert('This facility is not available in this polling staion');
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
            },
            error: function(error) {
            console.log(error.responseText);
            }
    });
    });
    });
    //chart 1
    $(function() {
    $('#toggle-event').change(function() {
    //alert($(this).prop('checked'));
    if ($(this).prop('checked') === true){
    $(".tab2").show();
    $(".tab1").hide();
    } else{

    $(".tab2").hide();
    $(".tab1").show();
    }
    });
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
    type: 'bar',
            data: {
            labels: ['Male', 'Female', 'Third gender', 'Total Electors'],
                    datasets: [{
                    label: ['Elector Trends'],
                            data: [1424433, 436461, 1999, 52612561],
                            backgroundColor: [
                                    'rgba(255, 99, 132, 0.2)',
                                    'rgba(54, 162, 235, 0.2)',
                                    'rgba(255, 206, 86, 0.2)',
                                    'rgba(75, 192, 192, 0.2)'
                            ],
                            borderColor: [
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(75, 192, 192, 1)'
                            ],
                            borderWidth: 1
                    }]
            },
            options: {
            scales: {
            yAxes: [{
            ticks: {
            beginAtZero: true
            }
            }]
            }
            }
    });
    });
    var role_id = '<?php echo $user_data->role_id ?>';
    var prefix = 'acdeo';
    function deleteNfd(){
    //alert();
    }
    $(document).ready(function(){
    $('#select_all').on('click', function(){
    if (this.checked){
    $('.checkbox').each(function(){
    this.checked = true;
    });
    $(".commoncheck").show();
    $(".norecord").hide();
    } else{
    $('.checkbox').each(function(){
    this.checked = false;
    });
    $(".commoncheck").hide();
    $(".norecord").show();
    }
    });
    $('.checkbox').on('click', function(){
    var dataid = $(this).attr("data-id");
    if ($('.checkbox:checked').length == $('.checkbox').length){
    $('#select_all').prop('checked', true);
    $('.module' + dataid).show();
    } else{
    $('#select_all').prop('checked', false);
    $('.module' + dataid).hide();
    }

    if ($('.checkbox').length == '17'){
    $(".norecord").show();
    } else{
    $(".norecord").hide();
    }
    });
    $('.cscheck').on('click', function(){
    var dataid = $(this).attr("data-id");
    if (this.checked){
    $('.module' + dataid).show();
    } else{
    $('.module' + dataid).hide();
    }

    var checked_len = 0;
    var checked_len = $('.checkbox:not(:checked)').length;
    //alert(checked_len);
    if ($('.checkbox:checked').length == '17'){
    $(".norecord").show();
    } else{
    $(".norecord").hide();
    }
    });
    });
// This function for filter Fixed 	
    var jQ = jQuery.noConflict();
    jQ(window).scroll(function(){
    if (jQ(this).scrollTop() >= 100){
    jQ('.filter-area').addClass('filter-sticky');
    } else { jQ('.filter-area').removeClass('filter-sticky'); }

    });

</script>
<script type="text/javascript" src="{{ asset('js/ac-profiling-custom.js') }}"></script>
@endsection