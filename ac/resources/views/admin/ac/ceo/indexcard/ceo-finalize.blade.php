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


  @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
          @endif
          @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
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
                   <span class="badge badge-info"></span>&nbsp;&nbsp;  </p></div>
                </div> <!-- end col-->
                </div><!-- end row-->
              
            <div class="card-body"> 

    

           <div class="table-responsive">
          <table class="table table-bordered ">
           <thead class="capatlize">


            <tr> 
              
             <th>AC No - Name</th>
              <th>Finalized By RO</th>
              <th>Finalized By CEO</th>
            </tr>

          </thead>
          @if(count($results)>0)

          <tbody>   
            <?php $i = 0; ?>
            @foreach($results as $result)
              <tr id="{{$result['id']}}" data-id="{!! $result['id'] !!}">
                <td>{!! $result['ac_no'] !!}-{!! $result['ac_name'] !!}</td>
     
                <td> 
                  <?php if($result['finalize_by_ro']){ ?>
                    Yes 
                    <?php if(!$result['finalize_by_ceo']){ ?>
                    <button class="btn btn-success ceo-definalize" style="margin: 10px 0 10px 20px;">De-finalize </button>
                    <?php } ?>
                  <?php }else{ ?>
                    No
                  <?php } ?>
                </td>
                <td>
                  <?php if($result['finalize_by_ceo']){ ?>
                    Yes
                  <?php }else if($result['finalize_by_ro']){ ?>
                    <button class="btn btn-success finalize_by_ro" data-toggle="modal" data-target="#popup" style="margin: 10px 0 10px 20px;">Send To ECI Office</button>
                  <?php }else{ ?>
                    <small class="alert aleart-warning">Finalize button will appear once RO will finalize the indexcard.</small>
                  <?php } ?> 
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

<div class="modal fade" id="popup" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" style="color:#fff; background: #f0587e;">
				<h5 class="modal-title">Certificate of Correctness for Index Card</h5>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					
				</div>
				<div class="modal-body">
					<div>
						<ol id="ceo_certificate">
							<li><b>{{Auth::user()->name}}</b>, Certify that data entered/updated for Index Card is verified and correct.</li>
							<li>I understand that upon pressing the <b>'Finalize and Send Report to ECI'</b> button the data will become non editable.</li>
						</ol>
					</div>
					<div class="form-group row">
						<label for="Name" class="col-sm-4 col-form-label"><b>Enter CEO Name:</b> </label>
						<div class="col-sm-7">
						  <input type="text" class="form-control-plaintext" id="ceo_name" required>
						  <input type="hidden" id="id">
						</div>
					</div>
					<div class="w-100 text-right">
						<h6>{{Auth::user()->name}}</h6>
						<h6>Chief Electoral Officer</h6>
						<h6>{{date('d-m-Y h:i A')}}</h6>
					</div>
				</div>
				<div class="modal-footer">
					<a href="" data-dismiss="modal" class="btn btn-secondary pad-one" title="Cancel download">Cancel</a>
					<a href="" class="btn btn-success pad-one ceo-finalize" data-action="selectFile" title="Agree to Finalize and Send Report to ECI">Finalize and Send Report to ECI</a>	
				</div> 
			</div>
		</div>
	</div>


@endsection

@section('script')

<script type="text/javascript">
function filter(){
  var url = "<?php echo $action ?>";
  var query = '';
    if($("#ac_no").val() != ''){
      query += '&ac_no='+$("#ac_no").val();
    }
    if($("#year").val() != ''){
      query += '&year='+$("#year").val();
    }
    window.location.href = url+'?'+query.substring(1);
}
$(document).ready(function(e){
	
	
	
	
  $('.finalize_by_ro').click(function(e){	  
	var id = $(this).parent('td').parent('tr').attr('data-id');	 
	$('#id').val(id);	 
  });
  
  
  $('.ceo-finalize').click(function(e){
    //if(confirm("Are you sure you want to finalize.")){
		var id = $('#id').val();
	  
		var ceo_name = $("#ceo_name").val();
		
		if (!$("#ceo_name").val()) {
		  return alert('Please Enter the Ceo Name.');
		}
		
		var ceo_certificate = $("#ceo_certificate").html();
		
		var ceo_certificate = $(ceo_certificate).text();
	  
	  
        $.ajax({
          url: "{!! url('/acceo/indexcard/finalize/post') !!}",
          type: 'POST',
          //data: '_token={!! csrf_token() !!}&finalized=1&id='+id+'&year='+$('#year').val(),
		  data: '_token={!! csrf_token() !!}&ceo_name='+ceo_name+'&ceo_certificate='+ceo_certificate+'&finalized=1&id='+id+'&year='+$('#year').val(),
          dataType: 'json', 
          beforeSend: function() {
            $('#'+id+' button').prop('disabled',true);
            $('#'+id+' button').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
          },  
          complete: function() {
            $('.loading_spinner').remove();
            $('#'+id+' button').prop('disabled',false);
          },        
          success: function(json) {
			   
            if(json['status'] == true){
              location.reload();
            }
            if(json['status'] == false){
              error_messages(json['message']);
            }
            $('.loading_spinner').remove();
            $('#'+id+' button').prop('disabled',false);
          },
          error: function(data) {
            var errors = data.responseJSON;
            $('.loading_spinner').remove();
            $('#'+id+' button').prop('disabled',false);
          }
        });
      //} 
  });


  $('.ceo-definalize').click(function(e){
    if(confirm("Are you sure you want to De-finalize.")){
      id = $(this).parent('td').parent('tr').attr('data-id');
        $.ajax({
          url: "{!! url('/acceo/indexcard/finalize/post') !!}",
          type: 'POST',
          data: '_token={!! csrf_token() !!}&finalized=0&id='+id+'&year='+$('#year').val(),
          dataType: 'json', 
          beforeSend: function() {
            $('#'+id+' button').prop('disabled',true);
            $('#'+id+' button').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
          },  
          complete: function() {
            $('.loading_spinner').remove();
            $('#'+id+' button').prop('disabled',false);
          },        
          success: function(json) {
            if(json['status'] == true){
              location.reload();
            }
            if(json['status'] == false){
              error_messages(json['message']);
            }
            $('.loading_spinner').remove();
            $('#'+id+' button').prop('disabled',false);
          },
          error: function(data) {
            var errors = data.responseJSON;
            $('.loading_spinner').remove();
            $('#'+id+' button').prop('disabled',false);
          }
        });
      } 
  });


  
});
</script>
@endsection