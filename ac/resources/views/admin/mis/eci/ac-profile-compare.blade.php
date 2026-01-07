@extends('admin.central.common.theme')
@section('title', 'Compare State Profile')
  <?php 
  $breadcrumbs = [];
  $breadcrumbs[] = [
    'href' => Common::generate_url('mis/descriptive-non-election-period'),
    'name' => 'Compare State Profile'
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
<!-- Leaflet-KMZ -->
<link rel="stylesheet" href="{{ asset('css/custom-profile.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.6.2/css/bootstrap-slider.min.css" rel="stylesheet"/>

<main class="main-wrap">
	 <aside class="lft-aside compare-area">
	   <nav class="filter-area effct-slide move-lft" style="">
		  <div class="filter-list">
		    <h4 class="filter-title">
			  Compare <span class="pull-right"><i class="fa fa-sliders" aria-hidden="true"></i></span>
			</h4>
			<p id="errorMsg" class="red" style="font-size:12px;"></p>
			<div class="pl-2 parnt-area">
			  <div class="chld-scroll">
			  <form id="compare_form" method="POST">
				{{ csrf_field() }}
				@if($state)
				@foreach($state as $val)
				<div class="form-group">
				  <input type="checkbox" class="checkbox" id="st_code{{$val->ST_CODE}}" <?php if($selected_state){ if(in_array($val->ST_CODE,$selected_state)){?> checked <?php }}?> name="st_code[]" value="{{$val->ST_CODE}}">
				  <label for="st_code{{$val->ST_CODE}}">{{$val->ST_NAME}}</label>
				</div>
				@endforeach
				@endif
				<div class="frm-actn-btn pt-3">
				  
				  <button type="submit" class="btn btn-primary" id="compare_submit">Compare</button>
				  <input type="reset" value="Clear">	
				  
				</div><!-- End Of frm-actn-btn Div -->		   
			  </form>
			  
			  
			</div><!-- End Of chld-scroll Div -->	
			  
			</div>  
		</div> 
	   </nav>
	 </aside>
	 <section class="rght-section effct-slide compare-rslt">
	    <div class="container-fluid">
	    <div class="my-3">
	     <h3>State Profiles <span class="pull-right"> <small>Compare <i class="fa fa-sliders comp-btn" aria-hidden="true"></i></small></span></h3>
			<div class="table-responsive shadow-sm mt-3">
			  <table class="table table-bordered table-hover compr-table-odd text-center mb-0">
				<thead class="first-th">
					<tr>
						<th>Demographic Details</th>
						@if(@get_state_entry)
							@foreach($get_state_entry as $k=>$v)
								<th><?php echo app(App\Http\Controllers\Admin\Mis\Eci\EciAcProfilingController::class)->getStateName($v->st_code); ?></th>
							@endforeach
					    @endif
					</tr>
				</thead>
				<tbody class="bg-tbody">
					
					<tr>
						<td>Revenue Districts</td>
						@if(@get_state_entry)
							@foreach($get_state_entry as $k=>$v)
								<td>{{$v->revenue_district}}</td>
							@endforeach
						@endif
					</tr>
					
					<tr>
						<td>Sub divisions</td>
						@if(@get_state_entry)
							@foreach($get_state_entry as $k=>$v)
								<td>{{$v->sub_division}}</td>
							@endforeach
						@endif
					</tr>
					<tr>
						<td>Tehsils/ Talukas</td>
						@if(@get_state_entry)
							@foreach($get_state_entry as $k=>$v)
								<td>{{$v->tehsil_talkuas}}</td>
							@endforeach
						@endif
					</tr>
					<tr>
						<td>Gram panchayats</td>
						@if(@get_state_entry)
							@foreach($get_state_entry as $k=>$v)
								<td>{{$v->gram_panchayat}}</td>
							@endforeach
						@endif
					</tr>
					<tr>
						<td>Villages</td>
						@if(@get_state_entry)
							@foreach($get_state_entry as $k=>$v)
								<td>{{$v->villages}}</td>
							@endforeach
						@endif
					</tr>
					<tr>
						<td>Municipal Corporations</td>
						@if(@get_state_entry)
							@foreach($get_state_entry as $k=>$v)
									<td>{{$v->municipal_corporations}}</td>
							@endforeach
						@endif
					</tr>
					<tr>
						<td>Municipalities</td>
						@if(@get_state_entry)
							@foreach($get_state_entry as $k=>$v)
									<td>{{$v->municipalities}}</td>
							@endforeach
						@endif
					</tr>
					<tr>
						<td>Post offices</td>
						@if(@get_state_entry)
							@foreach($get_state_entry as $k=>$v)
									<td>{{$v->post_offices}}</td>
							@endforeach
						@endif
					</tr>
					<tr>
						<td>Police Stations</td>
						@if(@get_state_entry)
							@foreach($get_state_entry as $k=>$v)
									<td>{{$v->police_stations}}</td>
							@endforeach
						@endif
					</tr>
					
				</tbody>
			  </table>
			</div><!-- End Of Table-responsive Div -->	
				
			<div class="table-responsive shadow-sm my-4">
			  <table class="table table-bordered table-hover compr-table-odd text-center mb-0">
				<thead class="first-th">
					<tr>
						<th>Population</th>
						@if(@get_state_entry)
							@foreach($get_state_entry as $k=>$v)
								<th><?php echo app(App\Http\Controllers\Admin\Mis\Eci\EciAcProfilingController::class)->getStateName($v->st_code); ?></th>
							@endforeach
					    @endif
					</tr>
				</thead>
				<tbody class="bg-tbody">
					
					<tr>
						<td>2011 census population</td>
						@if(@get_state_entry)
							@foreach($get_state_entry as $k=>$v)
									<td>{{$v->census_population}}</td>
							@endforeach
						@endif
					</tr>
					<tr>
						<td>Projected population</td>
						@if(@get_state_entry)
							@foreach($get_state_entry as $k=>$v)
									<td>{{$v->projected_population}}</td>
							@endforeach
						@endif
					</tr>
					<tr>
						<td>EP Ratio</td>
						@if(@get_state_entry)
							@foreach($get_state_entry as $k=>$v)
									<td>{{$v->ep_ratio}}</td>
							@endforeach
						@endif
					</tr>
				</tbody>
			  </table>
			</div><!-- End Of Table-responsive Div -->
			<div class="table-responsive shadow-sm my-4">
			  <table class="table table-bordered table-hover compr-table-odd text-center mb-0">
				<thead class="first-th">
					<tr>
						<th>Webcasting Details</th>
						@if(@get_state_entry)
							@foreach($get_state_entry as $k=>$v)
								<th><?php echo app(App\Http\Controllers\Admin\Mis\Eci\EciAcProfilingController::class)->getStateName($v->st_code); ?></th>
							@endforeach
					    @endif
					</tr>
				</thead>
				<tbody class="bg-tbody">
					
					<tr>
						<td>No. of PS for Webcasting</td>
						@if(@get_state_entry)
							@foreach($get_state_entry as $k=>$v)
									<td>{{$v->expenditure_sensitive_constituencies}}</td>
							@endforeach
						@endif
					</tr>
					<tr>
						<td>Details of Webcasting</td>
						@if(@get_state_entry)
							@foreach($get_state_entry as $k=>$v)
									<td>{{$v->no_of_ps_webcasting}}</td>
							@endforeach
						@endif
					</tr>
					
				</tbody>
			  </table>
			</div><!-- End Of Table-responsive Div -->
			<div class="table-responsive shadow-sm my-4">
			  <table class="table table-bordered table-hover compr-table-odd text-center mb-0">
				<thead class="first-th">
					<tr style="width:200px;">
						<th>Vulnarability/ Sensitive Area Mapping</th>
						@if(@get_state_entry)
							@foreach($get_state_entry as $k=>$v)
								<th><?php echo app(App\Http\Controllers\Admin\Mis\Eci\EciAcProfilingController::class)->getStateName($v->st_code); ?></th>
							@endforeach
					    @endif
					</tr>
				</thead>
				<tbody class="bg-tbody">
					
					<tr style="width:200px;">
						<td>Vulnerable Area/ Pockets</td>
						@if(@get_state_entry)
							@foreach($get_state_entry as $k=>$v)
									<td>{{$v->vulnerable_area_pockets}}</td>
							@endforeach
						@endif
					</tr>
					<tr style="width:200px;">
						<td>Critical PS</td>
						@if(@get_state_entry)
							@foreach($get_state_entry as $k=>$v)
									<td>{{$v->critical_ps}}</td>
							@endforeach
						@endif
					</tr>
					<tr style="width:200px;">
						<td>Expenditure sensitive Constituencies</td>
						@if(@get_state_entry)
							@foreach($get_state_entry as $k=>$v)
									<td>{{$v->expenditure_sensitive_constituencies}}</td>
							@endforeach
						@endif
					</tr>
					
				</tbody>
			  </table>
			</div><!-- End Of Table-responsive Div -->
		 </div>		
		</div>			
     </section>  
   </main>
   
@endsection
<script type="text/javascript" src="{{ asset('js/jquery.min.js') }}"></script>
 <script type="text/javascript">
	
	
    var role_id = '<?php echo $user_data->role_id ?>';
	
	

	$(document).ready(function(){
		$('#compare_submit').on('click',function(){
			$("#errorMsg").html("");
			if($('.checkbox:checked').length ==0){
				$("#errorMsg").html('Please select at least one state to compare.');
				return false;
			}else{
				$("#compare_form").submit();
			}
		});
		$('#select_all').on('click',function(){
			if(this.checked){
				$('.checkbox').each(function(){
					this.checked = true;
				});
				$(".commoncheck").show();
				$(".norecord").hide();
			}else{
				 $('.checkbox').each(function(){
					this.checked = false;
				});
				$(".commoncheck").hide();
				$(".norecord").show();
			}
		});
		
		$('.checkbox').on('click',function(){
			var dataid = $(this).attr("data-id");
			if($('.checkbox:checked').length == $('.checkbox').length){
				$('#select_all').prop('checked',true);
				$('.module'+dataid).show();
			}else{
				$('#select_all').prop('checked',false);
				$('.module'+dataid).hide();
			}
			
			if($('.checkbox').length=='17'){
				$(".norecord").show();
			}else{
				$(".norecord").hide();
			}
		});
		$('.cscheck').on('click',function(){
			var dataid = $(this).attr("data-id");
			if(this.checked){
				$('.module'+dataid).show();
			}else{
				$('.module'+dataid).hide();
			}

			var checked_len = 0;
			var checked_len = $('.checkbox:not(:checked)').length;
			//alert(checked_len);
			if($('.checkbox:checked').length=='17'){
				$(".norecord").show();
			}else{
				$(".norecord").hide();
			}
		});
		
		jQ('.comp-btn').click(function(){
			//alert('hi');
			jQ('.lft-aside').toggleClass('compare-area');
			 jQ('.filter-area').toggleClass('move-lft');
			  jQ('.rght-section').toggleClass('compare-rslt');
			//jQ(this).removeClass('compare-area, compare-rslt');
		});	 
	});
	
 // This function for filter Fixed 	
 var jQ = jQuery.noConflict();
  jQ(window).scroll(function(){
     if(jQ(this).scrollTop()>=100){ 
	   jQ('.filter-area').addClass('filter-sticky');
	 }else { jQ('.filter-area').removeClass('filter-sticky');}
	 
  });
	
</script>