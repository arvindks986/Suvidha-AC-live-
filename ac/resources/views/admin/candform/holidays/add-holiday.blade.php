@extends('admin.layouts.ac.theme')
@section('content')
@section('bradcome', 'Add Holidays')
<link rel="stylesheet" href="{{ asset('theme/css/prenom.css')}}" />
<link rel="stylesheet" href="{{ asset('theme/css/vaca-cal.css')}}">
<link rel="stylesheet" href="{{ asset('theme/css/fullcalendar.css') }}">
<link rel="stylesheet" href="{{ asset('theme/css/datepicker.css') }}">

<style type="text/css">
  .wrap-section {
    height: 485px;
    overflow: hidden;
}
.scroll-section {
    overflow-y: auto;
    height: 100%;
}
.datepickers-container{z-index: 9999;}
</style>
<main role="main" class="inner cover">
	<section>
		<div class="container-fluid HolidayPage">

			<div class="row d-flex">

				<div class="col-md-5 card mt-3 p-0">
					<div class="card-header">
						<ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" id="one-tab" data-toggle="tab" href="#one" role="tab"
									aria-controls="One" aria-selected="true">Holidays</a>
							</li>
							{{-- <li class="nav-item">
								<a class="nav-link" id="two-tab" data-toggle="tab" href="#two" role="tab"
									aria-controls="Two" aria-selected="false">Important Dates</a>
							</li> --}}

							<li class="pull-right ml-auto"><a href="{{ url('acceo/print-holidays-pdf') }}"
									class="active-holiday btn btn-outline-primary  pull-right" target="_blank">Print
									Holiday Pdf</a></li>
						</ul>
					</div>

					<div class="card-body pt-3">
						<div class="wrap-section">
                           <div class="scroll-section">
							<div class="tab-content" id="myTabContent">
								<div class="tab-pane fade show active" id="one" role="tabpanel" aria-labelledby="one-tab">
									<h2 id="holdiay_title" class="display-8 text-center">List of Holidays</h2>
									<table id="holiday_data" class="table table-bordered">
										<?php
											$filter_array = array_filter($final_data, function($final_data) {
												return $final_data['className'] == 'fc-bg-blue';
											});
										?>
										@foreach ($filter_array as $each_data)
										<tr>
											<td>{{ $each_data['title'] }}</td>
											<td><i class="grey">{{ $each_data['datetoshow'] }}</i></td>
										</tr>
										@endforeach
									</table>
								</div>
							</div>
						   </div>
						</div>

					</div>
				</div>
				<div class="col-md-7 card mt-3">
					<div class="card-body p-0">
						<div class="row no-gutters">
							<div class="holiday-box-grp">
								<div class="rght-calender">
									<div id="calendar" class="calendartep"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- calendar modal -->
		<div id="modal-view-event" class="modal modal-top fade calendar-modal">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<h4 class="modal-title"><span class="event-icon"></span><span class="event-title"></span></h4>
						<div class="event-body"></div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>

		<div id="modal-view-event-add" class="modal modal-top fade calendar-modal">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<form id="add-event" method="post" action="">
						{{ csrf_field() }}
						<div class="modal-body">
							<h4>Add Holiday Detail</h4>
							<div class="row form-group">
							<input type="hidden" class="event_id" name="event_id" value="">
								<div class="form-group col-md-6">
									<label>Holyday Type</label>
									<select class="form-control ecolor" name="ecolor">
										{{-- <option value="fc-bg-lightgreen">Important Dates</option> --}}
										<option value="fc-bg-blue" selected>Holidays</option>
									</select>
								</div>
								<div class="form-group col-md-6">
									<label>Event Date</label>
									<input type='text' class="form-control edate" class="edate" name="edate">
								</div>
							</div>
							<div class="form-group">
								<label>Event name</label>
								<input type="text" class="form-control ename" name="ename">
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" id="event_submit" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- partial -->
	</section>
</main>
@endsection

@section('script')

@if (session('success_mes'))
<script type="text/javascript">
	success_messages("{{session('success_mes') }}");
</script>
@endif
@if (session('error_mes'))
<script type="text/javascript">
	error_messages("{{session('error_mes') }}");
</script>
@endif

<script src='{{ asset('theme/js/fullcalendar.js') }}'></script>
<script src='{{ asset('theme/js/datepicker.js') }}'></script>
<script src='{{ asset('theme/js/datepicker.en.js') }}'></script>
<script>
	$(".holiday-toggle").click(function(){
    $(".holiday-togg").fadeToggle(300);
});
</script>
<script>
	jQuery(document).ready(function(){

	jQuery('.edate').datepicker({
		dateFormat: "dd-mm-yyyy",
		timepicker: false,
		language: 'en',
		range: true,
		multipleDates: true,
		multipleDatesSeparator: " - "
	});

	var all_data = @json($final_data);
	$('#two-tab').click(function(e) {
			var filter_data =	all_data.filter(function(obj) {
					return (obj.className == 'fc-bg-lightgreen');
				});
				var myvar = '';
			if(filter_data.length>0){
				$.each(filter_data, function(key,obj) {
				myvar += '<tr><td>'+obj.title+'</td><td><i class="grey">'+obj.datetoshow+'</i></td></tr>';
				});
			}else{
				myvar = '<tr><td class="text-center" colspan="2">No Data Available</td></tr>';
			}
			$('#holiday_data').html(myvar);
			$('#holdiay_title').text("Important Dates");
	});

	$('#one-tab').click(function(e) {
			var filter_data =	all_data.filter(function(obj) {
					return (obj.className == 'fc-bg-blue');
				});
				var myvar = '';
			if(filter_data.length>0){
				$.each(filter_data, function(key,obj) {
				myvar += '<tr><td>'+obj.title+'</td><td><i class="grey">'+obj.datetoshow+'</i></td></tr>';
				});
			}else{
				myvar = '<tr><td class="text-center" colspan="2">No Data Available</td></tr>';
			}
			$('#holiday_data').html(myvar);
			$('#holdiay_title').text("List of Holidays");
	});

  $('#event_submit').click(function(){
       $.ajax({
       url: "{{ url('/acceo/submit_add_holiday') }}",
       type: 'POST',
       data: $('#modal-view-event-add form').serialize(),
       dataType: 'json',
       beforeSend: function() {
         $('#modal-view-event-add .text-danger').remove();
         $('#modal-view-event-add input').removeClass('input-error');
         $('#event_submit').prop('disabled',true);
         $('#event_submit').text("Validating...");
         $('#event_submit').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
       },
       complete: function() {
       },
       success: function(json) {
         if(json['success'] == true){
			location.reload();
         }
         if(json['success'] == false){
           if(json['errors']['warning']){
             alert(json['errors']['warning']);
           }
           if(json['errors']['edate']){
             $("#add-event .edate").addClass("input-error");
             $("#add-event .edate").after("<span class='text-error text-danger text-right pull-right'>"+json['errors']['edate'][0]+"</span>");
           }
		   if(json['errors']['ecolor']){
             $("#add-event .ecolor").addClass("input-error");
             $("#add-event .ecolor").after("<span class='text-error text-danger text-right pull-right'>"+json['errors']['edate'][0]+"</span>");
           }
           if(json['errors']['ename']){
             $("#add-event .ename").addClass("input-error");
             $("#add-event .ename").after("<span class='text-error text-danger text-right pull-right'>"+json['errors']['ename'][0]+"</span>");
           }
         }
         $('#event_submit').prop('disabled',false);
         $('#event_submit').text("Submit");
         $('.loading_spinner').remove();
       },
       error: function(data) {
         var errors = data.responseJSON;
         $('#event_submit').prop('disabled',false);
         $('#event_submit').text("Submit");
         $('.loading_spinner').remove();
       }
     });
    });
});

(function () {
    'use strict';
    // ------------------------------------------------------- //
    // Calendar
    // ------------------------------------------------------ //

		// page is ready
		var holiday_array = [];
		var final_data = <?php echo json_encode($final_data) ?>;
		$.each(final_data, function(index, object){
			if(object.start == object.end){

			}
			holiday_array.push({
				'id'		: object.id,
				'title'		: object.title,
				'start'		: object.start,
			 	'end'		: object.end,
				'className'	: object.className
			});
		});
		jQuery('#calendar').fullCalendar({
			themeSystem: 'bootstrap4',
			// emphasizes business hours
			businessHours: false,
			displayEventTime : false,
			defaultView: 'month',
			// event dragging & resizing
			editable: false,
			// header
			header: {
				center: 'title',
				left: 	'month',
				right: 	'today prev,next'
			},
			events: holiday_array,
			eventRender: function(event, element) {
				if(event.icon){
					element.find(".fc-title").prepend("<i class='fa fa-"+event.icon+"'></i>");
				}
			  },
			dayClick: function() {
				jQuery('.edate').val(new moment($(this).attr('data-date'), 'YYYY-MM-DD').format('DD-MM-YYYY'));
				jQuery('#modal-view-event-add').modal();
			},
			eventClick: function(event, jsEvent, view) {
					jQuery('.event-icon').html("<i class='fa fa-"+event.icon+"'></i>");
					jQuery('.event_id').val(event.id);
					jQuery('.ename').val(event.title);
					jQuery('.ecolor').val(event.className[0]);
					jQuery('.edate').val(new moment(event.start._i, 'YYYY-MM-DD').format('DD-MM-YYYY'));
					if(event.start._i != event.end._i){
						var start = new moment(event.start._i, 'YYYY-MM-DD').format('DD-MM-YYYY');
						var end   = new moment(event.end._i, 'YYYY-MM-DD').subtract(1, "days").format('DD-MM-YYYY');
						jQuery('.edate').val(start + ' - ' + end);
					}
					jQuery('#modal-view-event-add').modal();
			},
	});
})(jQuery);
</script>

<script>
$(document).ready(function() {

	$.ajaxSetup({
			headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
	});

});
function getnotivalue(noti_id,refr_id){

  var st_code = "<?php echo $user_data['st_code']; ?>";

  $.ajax({
  url : "{{ url('acceo/ep/noti_read_confirm') }}",
  type: "POST",
  data : {
            'notification_id': noti_id,
            'st_code':st_code
          },

  success: function(json)

  {
    var myvarnoticount = '';
    var myvarnoti = '';
    if (json['notification_data'].length > 0) {
        $.each(json['notification_data'], function(key, value) {
            myvarnoti += '<li class="dropdown-item notif_mark" data_refr_id="'+value['reference_id']+'" onclick="getnotivalue('+value['id']+')" data_noti_id="'+value['id']+'">' +
                '<a style="text-decoration: none;" href="Javascript:;">'+'<div>'+value['notification_text']+'</div>'+'</a>'+'</li>'+
                '<hr/>';
        });
    } else {
        myvarnoti = '<small class="uppercase"> No Notification Available <br/></small>' +
            '<hr/>';
    }
    $('#notifi_row').html(myvarnoti);

    if(json['notification_count'] > 0){
      var myvarnoticount = '<i class="fa fa-bell"></i>'+'<span class="badge_new" style="color:red;">'+ json['notification_count'] +'</span>'
    }

    $('#notif_count_value').html(myvarnoticount);



  },
  error: function ()
  {

  }
});
}

</script>
@endsection
