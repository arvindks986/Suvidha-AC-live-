@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')
 <?php 
 //echo "<pre>";
 //print_r($perm);
 //exit;
 ?>
<main role="main" class="inner cover mb-3 mb-auto">
	<br/>
    <section id="details">
	
<div class="container-fluid">
<form name ="report" method="post"  action="{{url('/acceo/reportdates')}}"> 
{{csrf_field()}}
<div class="row">

<div class="col-sm-3  row">
<label for="state" class="col-sm-4 col-form-label">Select PC</label>
<div class="col-sm-8 distt">
<select name="pc" id="pc" class="form-control">
<option value="0">-- Select PC --</option>
@foreach($distvalue as $dist)
<option value="{{$dist->PC_NO }}"> 
{{$dist->PC_NAME }}
</option>
@endforeach 
</select>
</div>
</div>
<div class="col-sm-4  row">
<label for="ac" class="col-sm-4 col-form-label">Select AC</label>
<div class="col-sm-8 distt">
<select name="ac" id="ac" class="form-control">
<option value="">-- Select AC --</option>
</select>
</div>
</div>

<div class="col-sm-3  row">
<label for="ac" class="col-sm-2 col-form-label">Date</label>
<div class="col-sm-8 distt">
<div class='input-group date datetimepicker1' id=''>
<input type="text" autocomplete = "off" id="demo" placeholder='Search via Date' name="datefilter" class="form-control" >

</div>
</div>
</div>
<div class="col-sm-2  row">
<input type="submit"  value="Export Excel" name="submit" class="btn btn-primary getdata">
</div>
</div>
</form>

        </div>
    </section>

</main>
@endsection
@section('script')
<script>
$(document).ready(function() {
var base_url = $("#base_url").val();
var token = $('meta[name="csrf-token"]').attr('content');

jQuery("select[name='pc']").change(function()
{
var pc = jQuery(this).val();
//alert(pc);
jQuery.ajax({
	url:base_url+'/acceo/getDistrictsval',
	type: 'GET',
	data: {pc:pc},
	success: function(data){
		//alert(data);
		if(data != ''){
			var distselect = jQuery('form select[name=ac]');
			distselect.empty();
			var statehtml = '';
			statehtml = statehtml + '<option value=""> -- Select AC --</option> ';
			jQuery.each(data,function(key, value) {
				statehtml = statehtml + '<option value="'+value.AC_NO+'">'+value.AC_NAME+'</option>'; 
				jQuery("select[name='ac']").html(statehtml);
			});
			var statehtml_end = '';
			jQuery("select[name='ac']").append(statehtml_end);
		}else{
			//alert('test');
			jQuery("select[name='ac']").html('<option value=""> -- Select AC --</option>');
		}
		
	}
});
});
});
</script>

<link href="{{url('/admintheme/css/daterangepicker.css')}}" rel="stylesheet" id="bootstrap-css">
<script type="text/javascript" src="{{url('/admintheme/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{url('admintheme/js/daterangepicker.js')}}"></script>
<script>
var selectdate = "";
var today = new Date();
var dd = today.getDate();
var mm = today.getMonth()+1; //January is 0!
var yyyy = today.getFullYear();

if(dd<10) {
    dd = '0'+dd
} 

if(mm<10) {
    mm = '0'+mm
} 

today = yyyy + '-' + mm + '-' + dd;
var start_date = today;
var end_date = today;
  $('#demo').daterangepicker({
	    "ranges": {
              'Today': [moment(), moment()],
              'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
              'Last 7 Days': [moment().subtract(6, 'days'), moment()],
              'Last 30 Days': [moment().subtract(29, 'days'), moment()],
              'This Month': [moment().startOf('month'), moment().endOf('month')],
              'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]      
    },
    "startDate": Date.now(),
    "endDate": Date.now(),
   autoUpdateInput: false,
      locale: {
          cancelLabel: 'Clear',
		  format: 'YYYY-MM-DD'
      }
}, function(start, end, label) {
  //alert("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
  //alert(start.format('YYYY-MM-DD'));
  start_date = start.format('YYYY-MM-DD');
  end_date = end.format('YYYY-MM-DD');

});
  $('#demo').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('YYYY-MM-DD') + '~' + picker.endDate.format('YYYY-MM-DD'));
	   //alert(picker.startDate.format('YYYY-MM-DD') + '~' + picker.endDate.format('YYYY-MM-DD'));
	 
	
  });

  $('#demo').on('cancel.daterangepicker', function(ev, picker) {
      $(this).val('');
  });
</script>
    @endsection
