@extends('admin.layouts.ac.dashboard-theme')
@section('content')
<style type="text/css">
  .heading th{
    text-transform: capitalize;
    text-align: left;
  }
  .complain-heading-main{
    text-transform: capitalize;
    text-align: center;
  }
</style>
<section class="dashboard-header pt-3 pb-3">
  <div class="container-fluid">
  
        
      <form id="generate_report_id" class="row" method="get" onsubmit="return false;">
  

          <div class="form-group col-md-3"> <label>State</label> 
          
            <select name="st_code" id="st_code" class="form-control" onchange ="filter('1')">
              <option value="">Select State</option>
            @foreach($states as $iterate_state)
              @if($st_code == $iterate_state['st_code'])
                <option value="{{$iterate_state['st_code']}}" selected="selected" >{{$iterate_state['st_name']}}</option> 
              @else 
                <option value="{{$iterate_state['st_code']}}">{{$iterate_state['st_name']}}</option> 
              @endif  
            @endforeach
        
            </select>
          </div>

          <div class="form-group col-md-3"> <label>AC </label> 
          
            <select name="ac_no" id="ac_no" class="form-control" onchange ="filter('0')">
            <option value="">Select AC</option>
            @foreach($acs as $result)
              @if($ac_no == $result['ac_no'])
                <option value="{{$result['ac_no']}}" selected="selected" >{{$result['ac_no']}}-{{$result['ac_name']}}</option> 
              @else 
                <option value="{{$result['ac_no']}}" >{{$result['ac_no']}}-{{$result['ac_name']}}</option> 
              @endif  
            @endforeach
        
            </select>
          </div>
         
        </form>   
  
    
  </div>
</section>

<main role="main" class="inner cover mb-3 mt-3">
<section>  

  <div class="container-fluid">
  <div class="row">   


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
      <div class="alert <?php echo $class; ?>">
        {{ Session::get('flash-message') }}
      </div>
    @endif  


<div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h4>De-Finalize Constituency</h4></div> 
                  <div class="col"><p class="mb-0 text-right">			
						<label class="mr-3"><b>Report: </b></label>
						<a href="{{url('eci-index/indexcard/de-finalize-acs/pdf')}}" target="_blank"><button type="button" class="btn btn-primary">Export PDF</button></a>
						<a href="{{url('eci-index/indexcard/de-finalize-acs/excel')}}" target="_blank"><button type="button" class="btn btn-success">Export CSV</button></a>
						</p>
				  </div>
                </div> <!-- end col-->
                </div><!-- end row-->
              
            <div class="card-body"> 

    

           <div class="table-responsive">
          <table class="table table-bordered " id="list-table">
            <tr>
              <th>State Name</th>
              <th>AC Name</th>
              <!-- <th>Required definalization</th>
              <th>Required ECI Approval</th>
              <th>Date</th> 
              <th>Complain</th>-->
       
              <th>Action</th>
       
            </tr>
          @if( count($results)>0)
            
            @foreach($results as $result)
              <tr>
                <td>{!! $result['st_name'] !!}</td>
                <td>{!! $result['ac_no'] !!}-{!! $result['ac_name'] !!}</td>
                
                
                <td>
				<?php /* @if (verifyreport(777) == '0') */ ?>
				
				
                  <button type="submit" class="btn btn-success" onclick="return finalize_indexcard('{{$result['st_code']}}','{{$result['ac_no']}}');">De-Finalize-Index Card</button>
				 &nbsp;
                  <button type="submit" class="btn btn-primary" onclick="return finalize_nomination('{{$result['st_code']}}','{{$result['ac_no']}}');">De-Finalize-Nomination</button>
				 &nbsp;				  
                  <button type="submit" class="btn btn-secondary" onclick="return finalize_counting('{{$result['st_code']}}','{{$result['ac_no']}}');">De-Finalize-Counting</button>				                  
                </td>          
              </tr>
            @endforeach
          @else
          <tbody>
          <tr>
            <td colspan="6" cellpadding='5' align="center">
              No Record Found.
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

<div class="modal fade" id="popup_indexcard" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" style="color:#fff; background: #f0587e;">
				<h5 class="modal-title">De-Finalize-IndexCard</h5>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					
				</div>
				<div class="modal-body">
				
					<form method="post" name="finalize_form_indexcard" action="{{$result['definalize_action']}}" enctype="multipart/form-data">
					
					<input type="hidden" name="_token" value="{{csrf_token()}}">
					<input type="hidden" name="ac_no" id="ac_no_i">
					<input type="hidden" name="st_code" id="st_code_i">
					
					
					<div class="form-group row form-check">
						<label for="Name" class="col-sm-12 col-form-label"><b>Reason of De-Finalize-IndexCard</b> </label>
						<div class="col-sm-12">
						  <textarea class="form-control" name ="reason" id="indexcard_reason" value=""></textarea>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<a href="" data-dismiss="modal" class="btn btn-secondary pad-one" title="Cancel download">Cancel</a>
					<button type="button" class="btn btn-success pad-one index_card_finalize." onclick="submitForm_indexcard();" title="">De-Finalize-IndexCard</button>	
				</div> 
				
				</form>
				
			</div>
		</div>
	</div>
	
	
	
	<div class="modal fade" id="popup_nomination" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" style="color:#fff; background: #f0587e;">
				<h5 class="modal-title">De-Finalize-Nomination for Index Card</h5>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					
				</div>
				<div class="modal-body">
				
					<form method="post" name="finalize_form_nomination" action="{{$definalize_action_nomination}}" enctype="multipart/form-data">
					
					<input type="hidden" name="_token" value="{{csrf_token()}}">
					<input type="hidden" name="ac_no" id="ac_no_n">
					<input type="hidden" name="st_code" id="st_code_n">
					
					
					<div class="form-group row form-check">
						<label for="Name" class="col-sm-12 col-form-label"><b>Reason of De-Finalize-Nomination</b> </label>
						<div class="col-sm-12">
						  <textarea class="form-control" name ="reason" id="nomination_reason" value=""></textarea>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<a href="" data-dismiss="modal" class="btn btn-secondary pad-one" title="Cancel download">Cancel</a>
					<button type="button" class="btn btn-success pad-one index_card_finalize." onclick="submitForm_nomination();" title="">De-Finalize-Nomination</button>	
				</div> 
				
				</form>
				
			</div>
		</div>
	</div>
	
	
	
	<div class="modal fade" id="popup_counting" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" style="color:#fff; background: #f0587e;">
				<h5 class="modal-title">De-Finalize-Counting for Index Card</h5>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					
				</div>
				<div class="modal-body">
				
					<form method="post" name="finalize_form_counting" action="{{$definalize_action_counting}}" enctype="multipart/form-data">
					
					<input type="hidden" name="_token" value="{{csrf_token()}}">
					<input type="hidden" name="ac_no" id="ac_no_c">
					<input type="hidden" name="st_code" id="st_code_c">
					
					
					<div class="form-group row form-check">
						<label for="Name" class="col-sm-12 col-form-label"><b>Reason of De-Finalize-Counting</b> </label>
						<div class="col-sm-12">
						  <textarea class="form-control" name ="reason" id="counting_reason" value=""></textarea>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<a href="" data-dismiss="modal" class="btn btn-secondary pad-one" title="Cancel download">Cancel</a>
					<button type="button" class="btn btn-success pad-one index_card_finalize." onclick="submitForm_counting();" title="">De-Finalize-Counting</button>	
				</div> 
				
				</form>
				
			</div>
		</div>
	</div>
	


<script type="text/javascript">

function filter(st){
  var url = "<?php echo $current_page ?>";
  var query = '';
    
    if($("#st_code").val() != ''){
      query += '&st_code='+$("#st_code").val();
    }
	
	if(st == '0'){
		if($("#ac_no").val() != ''){
		  query += '&ac_no='+$("#ac_no").val();
		}
	}
	
    window.location.href = url+'?'+query.substring(1);
}
</script>

<script type="text/javascript">

	function submitForm_indexcard(){
		$("#span_").remove();
		if($('#indexcard_reason').val()){
			document.finalize_form_indexcard.submit();
		}else{
			$('.form-check').after('<span class="err" id="span_">Please Enter Reason.</span>');      
            $('#indexcard_reason').css("border-color", "solid 1px red"); 
		}	
	}
	
	function submitForm_nomination(){
		$("#span_").remove();
		if($('#nomination_reason').val()){
			document.finalize_form_nomination.submit();
		}else{
			$('.form-check').after('<span class="err" id="span_">Please Enter Reason.</span>');      
            $('#nomination_reason').css("border-color", "solid 1px red"); 
		}	
	}
	
	function submitForm_counting(){
		$("#span_").remove();
		if($('#counting_reason').val()){
			document.finalize_form_counting.submit();
		}else{
			$('.form-check').after('<span class="err" id="span_">Please Enter Reason.</span>');      
            $('#counting_reason').css("border-color", "solid 1px red"); 
		}	
	}
	
	function finalize_indexcard(st_code,ac_no){
		$('#st_code_i').val(st_code);
		$('#ac_no_i').val(ac_no);
		$('#popup_indexcard').modal('show');
	}
	
	function finalize_nomination(st_code,ac_no){
		$('#st_code_n').val(st_code);
		$('#ac_no_n').val(ac_no);
		$('#popup_nomination').modal('show');
	}
	
	function finalize_counting(st_code,ac_no){
		$('#st_code_c').val(st_code);
		$('#ac_no_c').val(ac_no);
		$('#popup_counting').modal('show');
	}
	
</script>
@endsection