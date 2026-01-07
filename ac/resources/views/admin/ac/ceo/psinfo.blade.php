@extends('admin.layouts.pc.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'PS Details')
@section('description', '')
@section('content') 
 <?php  
  $st=getstatebystatecode($user_data->st_code); 
	//dd($jsonResult);
    ?>
<main role="main" class="inner cover mb-3">
<?php if(!empty($all_dist)) {?> 
<section>	 
	 <form enctype="multipart/form-data" id="election_form" method="POST"  action="{{url('pcceo/psinfo') }}" >
	  {{ csrf_field() }}
  <div class="container">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h4>PS Details</h4></div> 
                  <div class="col"><p class="mb-0 text-right"><b>State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; 
									<b></b> 
                   <span class="badge badge-info"></span>&nbsp;&nbsp;  </p></div>
                </div> <!-- end col-->
                </div><!-- end row-->
							
						<div class="card-body"> 
 		       <div class="row">
		    <div class="col-sm-2"><label for="district">District <sup>*</sup></label></div>
					<div class="col"><div class="" style="width:100%;">
						<select name="district" class="form-control" >
							<option value="">-- Select Ditricts --</option>
							@foreach($all_dist as $district)
								<option value="{{$district->DIST_NO}}" > 
									{{$district->DIST_NO}} - {{$district->DIST_NAME }} - {{$district->DIST_NAME_HI}}
								</option>
							@endforeach  
						</select>
						@if ($errors->has('district'))
                  		  <span style="color:red;">{{ $errors->first('district') }}</span>
               			@endif 
						<div class="districterrormsg errormsg errorred"></div>
				  	</div>
				  </div> 

					<div class="col-sm-2"><label for="ac">AC <sup>*</sup></label></div>
					 <div class="col">
						<div class="" style="width:100%;">
							<select name="ac" class="consttype form-control" >
								<option value="">-- Select AC --</option>
								@foreach($all_ac as $getAc)
									<option value="{{ $getAc->AC_NO }}" > 
									{{$getAc->AC_NO }} - {{$getAc->AC_NAME }} - {{$getAc->AC_NAME_HI}}
									</option>
								@endforeach 
							</select>
					    @if ($errors->has('ac'))
                  		  <span style="color:red;">{{ $errors->first('ac') }}</span>
               			@endif
							<div class="acerrormsg errormsg errorred"></div>
							<input type="hidden" name="st_code" value="{{ $user_data->st_code }}">
						</div>
					</div> 

					<div class="form-group row float-right">       
					  <div class="col">
						<button type="submit" id="psinfo" name="psinfo" class="btn btn-primary">Submit</button>
					  </div>
				 </div>	
	         </div><!-- end row-->
	         </div><!-- end card-body-->
           </form>
					</div>
				</div>
				</div>
			</div>
		</div>	  
	  </section>
		<?php } else {
			 $stname=getstatebystatecode($st_code); 
			 $distname=getdistrictbydistrictno($st_code,$dist_no);
		   $acdetails=getacbyacno($st_code,$ac_no); 

			?> 
	<section class="mt-5">
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h2 class="mr-auto">PS Details</h2></div> 
                   <div class="col"><p class="mb-0 text-right">
												<b>State Name:</b> 
												<span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; 
												<b>District:</b><span class="badge badge-info">{{$distname->DIST_NAME}}</span>&nbsp;&nbsp; 
												<b>AC:</b> <span class="badge badge-info">{{ $acdetails->AC_NAME}}</span>
												<b></b> <button type="submit" class="btn btn-primary"><a href="{{url('/pcceo/psinfo')}}"><font color="black">Back</font></a></button>
									  </p></div>
										</div><!-- end row-->
	              </div><!-- end card-header-->
<div class="card-body">  
  <div class="table-responsive">
      <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <tr>
          <th>PART No</th>
          <th>PART NAME</th>
          <th>RAMP PROVIDED</th>
          <th>DRINKING WATER</th>
          <th>FURNITURE IN BUILDING</th>
					<th>LIGHTING IN BUILDING</th>
					<th>HELP DESK</th>
					<th>PPS LATLONG</th>
        </tr>
        </thead>
<?php $j=0;  ?>
		@if(!empty($jsonResult))
		@foreach($jsonResult as $jsonResultListData)  
			<?php
			// dd( $jsonResultListData);
				$j++; 
				?>
<tr>
<td>@if(!empty($jsonResultListData->PART_NO)) {{$jsonResultListData->PART_NO}} @endif</td>
<td>@if(!empty($jsonResultListData->PART_NAME)) {{$jsonResultListData->PART_NAME}} @endif</td>
<td style="background-color:white;"><font color="black">@if(!empty($jsonResultListData->IS_RAMP_PROVIDED)) {{$jsonResultListData->IS_RAMP_PROVIDED}} @endif</font>
</td>
<td>@if(!empty($jsonResultListData->IS_DRINKING_WATER_FACILITY_AVAILABLE)) {{$jsonResultListData->IS_DRINKING_WATER_FACILITY_AVAILABLE}} @endif</td>
<td>@if(!empty($jsonResultListData->IS_ADEQUATE_FURNITURE_AVAILABLE_IN_BUILDING)) {{$jsonResultListData->IS_ADEQUATE_FURNITURE_AVAILABLE_IN_BUILDING}} @endif</td>
<td>@if(!empty($jsonResultListData->IS_PROPER_LIGHTING_AVAILABLE_IN_BUILDING)) {{$jsonResultListData->IS_PROPER_LIGHTING_AVAILABLE_IN_BUILDING}} @endif</td>
<td>@if(!empty($jsonResultListData->HELP_DESK)) {{$jsonResultListData->HELP_DESK}} @endif</td>
<td>@if(!empty($jsonResultListData->PPS_LATLONG)) {{$jsonResultListData->PPS_LATLONG}} @endif</td>
</tr>
@endforeach 
@endif 
<tbody>
             </tbody>
            </table>
           </div> <!-- end responcive-->
          </div> <!-- end card-body-->
        </div>
      </div>
     </div>
   	</div>
  </section>
	<?php } ?> 
	</main>
@endsection

@section('script')

<script>
jQuery(document).ready(function(){ 

	jQuery("select[name='district']").change(function(){
		var district = jQuery(this).val();  
        jQuery.ajax({ 
        	url: '<?php echo url('/') ?>/pcceo/getallac',
            type: 'GET',
            data: {district:district},
         
            success: function(result){  
							//alert(result); 
                var distselect = jQuery('form select[name=ac]');
                distselect.empty();
                var achtml = '';
                achtml = achtml + '<option value="">-- Select AC --</option> ';
                jQuery.each(result,function(key, value) { 
                    achtml = achtml + '<option value="'+value.AC_NO+'">'+value.AC_NO+' - '+value.AC_NAME + ' - ' +value.AC_NAME_HI+'</option>';
                    jQuery("select[name='ac']").html(achtml);
                });
                var achtml_end = '';
                jQuery("select[name='ac']").append(achtml_end)
            }
        });
    });
	
	//Check Validation
    jQuery('#psinfo').click(function(){
		var distt = jQuery('select[name="district"]').val();
		var acname = jQuery('select[name="ac"]').val();
		
		if(distt == ''){
			jQuery('.errormsg').html('');
			jQuery('.districterrormsg').html('Please select district');
			jQuery( "input[name='district']" ).focus();
			return false;
		}
		if(acname == ''){
            jQuery('.errormsg').html('');
			jQuery('.acerrormsg').html('Please select ac');
			jQuery( "input[name='ac']" ).focus();
			return false;
		}
	});
	
});

</script>
@endsection