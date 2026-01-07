@extends('admin.layouts.ac.theme')
@section('bradcome')
  <li><span class="icon icon-beaker"> </span> Finalize Indexcard Entry</li>
@endsection
@section('content')
<style type="text/css">
  .capatlize th{
    text-transform: capitalize;
    font-size: 12px;
    text-align: center;
  }
  .table th, .table td{
    padding: 3px !important;
  }
  .table td .form-control{
    font-size: 12px;
  }
  .small_text{
    font-size: 10px;
    line-height: 12px;
  }
  .form-control-plaintext{
	  border:solid #ddd 1px;
  }
  ol{
	text-align: justify;
  }
</style>
<section class="dashboard-header pt-3 pb-3">
  <div class="container-fluid">
  
        
      
  
    
  </div>
</section>

<main role="main" class="inner cover mb-3 mt-3">
<section>  

  <div class="container-fluid">
  <div class="row">   


   @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
        @endif
         @if (session('error_mes'))
          <div class="alert alert-danger"> {{session('error_mes') }}</div>
        @endif
         @if (\Session::has('success'))
			<div class="alert alert-success">
				<ul>
					<li>{!! \Session::get('success') !!}</li>
				</ul>
			</div>
		@endif




<div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h4>{!! $heading_title !!}</h4></div> 
                  <div class="col"><p class="mb-0 text-right">

                    @if(isset($filter_buttons) && count($filter_buttons)>0)
                            @foreach($filter_buttons as $button)
                                <?php $but = explode(':',$button); ?>
                                <b>{!! $but[0] !!}:</b>
                                <span class="badge badge-info">{!! $but[1] !!}</span>
                            @endforeach  
                    @endif
                



                    &nbsp;&nbsp; 
                  <b></b> 
                   <button class="btn btn-success" id="delbutton" disabled>Publish Selected</button></p></div>
                </div> <!-- end col-->
                </div><!-- end row-->
              
            <div class="card-body"> 

    

           <div class="table-responsive">
          <table class="table table-bordered">
           <thead class="capatlize">


            <tr> 
             <th><input type="checkbox" id="select_all"></th>
             <th>AC No - Name</th>
			 <th>RO Finalize Status</th>
			 <th>DEO Finalize Status</th>
			 <th>CEO Finalize Status</th>
             <th>Turnout Publish Status</th>
             <th>Action</th>
            </tr>

          </thead>
          @if(count($get_all_data)>0)

          <tbody>   
            <?php $i = 0; ?>
            @foreach($get_all_data as $result)
			@php
			$get_status = app(App\Http\Controllers\Admin\PollingStation\CeoPollingStationController::class)->getFinializeStatus($user_data->st_code,$result->ac_no);	
			
			$ro_finalize = '';
			$deo_finalize = '';
			$ceo_finalize = '';
			
			if(isset($get_status)){
				$ro_finalize = $get_status->ro_ps_finalize;
				$deo_finalize = $get_status->deo_ps_finalize;
				$ceo_finalize = $get_status->ps_finalize;
			}
			@endphp
			
              <tr>
				@if($result->end_of_poll_finalize=='0' && $ro_finalize == '1' && $deo_finalize =='1' && $ceo_finalize =='1')
					<td align="center"><input type="checkbox" name="finaldata[]" class="checkbox" value="{{$result->ac_no.'_'.$result->dist_no}}"></td>
				@else
					<td align="center"><input type="checkbox" name="finaldata[]" value="{{$result->ac_no.'_'.$result->dist_no}}" disabled></td>
				@endif
                <td>{!! $result->ac_no !!}-{!! $result->ac_name !!}</td>
				<td align="center"> 
                  <?php if($ro_finalize=='1'){ ?>
                    <label style="color:green";>Finalized </label>
                  <?php }else{ ?>
					<label style="color:red";>Not Finalized </label>
                    
                  <?php } ?>
                </td>
				<td align="center"> 
                  <?php if($deo_finalize=='1'){ ?>
                    <label style="color:green";>Finalized </label>
                  <?php }else{ ?>
					<label style="color:red";>Not Finalized </label>
                    
                  <?php } ?>
                </td>
				<td align="center"> 
                  <?php if($ceo_finalize=='1'){ ?>
                    <label style="color:green";>Finalized </label>
                  <?php }else{ ?>
					<label style="color:red";>Not Finalized </label>
                    
                  <?php } ?>
                </td>
                <td align="center"> 
                  <?php if($result->end_of_poll_finalize=='1'){ ?>
                    <label style="color:green";>Published </label>
                  <?php }else{ ?>
					<label style="color:red";>Not Published </label>
                    
                  <?php } ?>
                </td>
				<td align="center">
				<?php if($result->end_of_poll_finalize=='0' && $ro_finalize == '1' && $deo_finalize =='1' && $ceo_finalize =='1'){ ?>
				<button type="button" class="btn btn-success" onclick="return finalize('<?php echo $result->ac_no;?>','<?php echo $result->dist_no;?>');">Publish Turnout</button>
				<?php }else{?>
				<button type="button" class="btn btn-success" disabled>Publish Turnout</button>
				<?php }?>
				</td>
              </tr>
              <?php $i++; ?>
            @endforeach

          </tbody>

         
          @else
          <tbody>
          <tr>
            <td colspan="15" cellpadding='5' align="center">
              Please Select a AC.
            </td>
          </tr>
          </tbody>
          @endif

           </table>
         </div><!-- End Of  table responsive -->  
       </div>
     </div>
      </div><!-- End Of intra-table Div -->   
        
         
      </div><!-- End Of random-area Div -->
      
</section>
</main>

<div class="modal fade" id="confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">               					
				<h4 class="modal-title w-100">Are you sure you want to finalize End of Poll Voter turnout data?</h4>	
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <p><span style="color:red">After Publish, the Voter turnout percentage will be updated for public through Voter turnout app.<span></p>
            </div>
			<form method="post" action="<?php echo url('acceo/turnout/publish-turnout'); ?>" id="publishTurnoutFrm">
				{{ csrf_field() }}
				<input type="hidden" name="ac_no" id="ac_no" value="">
				<input type="hidden" name="dist_no" id="dist_no" value="">
			</form>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok confirm_button" onclick="submitForm();">Confirm</a>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirm1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">               					
				<h4 class="modal-title w-100">Are you sure you want to finalize End of Poll Voter turnout data?</h4>	
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <p><span style="color:red">After Publish, the Voter turnout percentage will be updated for public through Voter turnout app.<span></p>
            </div>
			<form method="post" action="<?php echo url('acceo/turnout/publish-all-turnout'); ?>" id="publishTurnoutFrm1">
				{{ csrf_field() }}
				<input type="hidden" name="all_ac_data" id="all_ac_data" value="">
			</form>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok confirm_button1" onclick="submitForm1();">Confirm</a>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

<script type="text/javascript">
var tmp = [];
$('.checkbox').change(function () {
	var checked = $(this).val();
	if ($(this).is(':checked')) {
		if (checked > 0) {
			tmp.push(checked);
		}
	} else {
		tmp.splice($.inArray(checked, tmp), 1);
	}
	if (false == $(this).prop("checked")) { //if this item is unchecked
		$("#select_all").prop('checked', false); //change "select all" checked status to false
	}
	if ($('.checkbox:checked').length == $('.checkbox').length) {
		$("#select_all").prop('checked', true);
	}
	
	if (($('.checkbox:checked').length) > 0) {
		$("#delbutton").removeAttr("disabled");
	} else {
		$("#delbutton").attr("disabled", "disabled");
	}
	
    });
	
	$("#select_all").change(function () {  //"select all" change 
        $(".checkbox").prop('checked', $(this).prop("checked")); //change all ".checkbox" checked status
        if (($('.checkbox:checked').length) > 0) {
            $("#delbutton").removeAttr("disabled");
			
        } else {
            $("#delbutton").attr("disabled", "disabled");
        }
    });
$("#delbutton").click(function(){
	var val_ids = new Array();
	$('input[type=checkbox][class=checkbox]').each(function () {
		var checked = $(this).val();
		if ($(this).is(':checked')) {
			if (checked != '') {
				val_ids.push(checked);
			}
		}
	});
	$("#all_ac_data").val(val_ids);
	$('#confirm1').modal('show');
});
function finalize(ac_no,dist_no){
	$("#ac_no").val(ac_no);
	$("#dist_no").val(dist_no);
	 $('#confirm').modal('show');
}

	
function submitForm(){
	//window.location.href = "<?php echo url('roac/turnout/publish-turnout'); ?>";
	document.getElementById("publishTurnoutFrm").submit();
}	
function submitForm1(){
	//window.location.href = "<?php echo url('roac/turnout/publish-turnout'); ?>";
	document.getElementById("publishTurnoutFrm1").submit();
}
</script>
@endsection