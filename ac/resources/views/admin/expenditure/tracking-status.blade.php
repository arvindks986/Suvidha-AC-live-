@extends('admin.layouts.ac.theme')
@section('content')
<main role="main" class="inner cover mb-1">
    <div class="card-header pt-2" id="expenditure_section">
        <div class="container-fluid">
            <div class="row text-center">
                <div class="col-sm-12"><h4><b> ECRP</b></h4></div>
				
            </div> 
        </div>
    </div>
    <section class="breadcrumb-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <ul id="breadcrumb" class="pt-1">
                        <li><a href="#">ECRP Election Details</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>	
	<section class="mt-5">
		<div class="container-fluid">
		    <div class="row">
				
				<div class="card w-100">
					<!-- -->					
					<div class="mt-5"></div>
                    <div class="clearfix"></div>
					<p class="text-center h5 pb-5 Orange_text"><strong>Tracking Module For Expenditure (PC)</strong></p>
					<div class="clearfix"></div>
                    <div class="col-sm-12 col-md-12 side-content">
                        <div class="bs-vertical-wizard">
                            <ul>
                                <li class="complete">
                                    <a href="#">
									<i class="ico ico-green">RO</i> 									
									<span>
										<div class="contentBox">
											<div class="date h6 text-success"><strong>Date: 23/05/2019</strong></div>
											<p class="graySquire"> Yet not received any Report from Expenditure Officer</p>
											<p class="greenSquire">Sent 80% Report to CEO with Date</p>
											<p class="yellowSquire">Received all Reports from Expenditure Officer and Successfully sent to CEO with Date</p>	
											<p class="graySquire"> Yet not received any Report from Expenditure Officer</p>
											<p class="greenSquire">Sent 80% Report to CEO with Date</p>
											<p class="yellowSquire">Received all Reports from Expenditure Officer and Successfully sent to CEO with Date</p>
										</div>							
									</span>
									</a>
									<p class="dateleft">0 - 38&nbspDays</p>									
									<div class="clearfix"></div>
                                   	
                                </li>

                                <li class="complete prev-step">
                                    <a href="#"> 
									<i class="ico ico-green">CEO</i>
                                        <span class="desc">	
										<div class="contentBox">
											<div class="date h6 text-success"><strong>Date: 23/05/2019</strong></div>
											<p class="graySquire"> Yet not received any Report from Expenditure Officer</p>
											<p class="greenSquire">Sent 80% Report to CEO with Date</p>
											<p class="yellowSquire">Received all Reports from Expenditure Officer and Successfully sent to CEO with Date</p>	
											<p class="graySquire"> Yet not received any Report from Expenditure Officer</p>
											<p class="greenSquire">Sent 80% Report to CEO with Date</p>
											<p class="yellowSquire">Received all Reports from Expenditure Officer and Successfully sent to CEO with Date</p>
										</div>
										</span>
                                    </a>
									<p class="dateleft">0 - 38&nbspDays</p>
                                </li>								
                                <li class="current">
                                    <a href="#">
									<i class="ico ico-green">ECI</i> 
                                        <span class="desc">										
											<div class="contentBox">
												<div class="date h6 text-warning"><strong>Date 23/05/2019</strong></div>
												<p class="graySquire"> Yet not received any Report from Expenditure Officer</p>
												<p class="greenSquire">Sent 80% Report to CEO with Date</p>
												<p class="yellowSquire">Received all Reports from Expenditure Officer and Successfully sent to CEO with Date</p>	
												<p class="graySquire"> Yet not received any Report from Expenditure Officer</p>
												<p class="greenSquire">Sent 80% Report to CEO with Date</p>
												<p class="yellowSquire">Received all Reports from Expenditure Officer and Successfully sent to CEO with Date</p>
										</div>								
										</span>										
                                    </a>
									<p class="dateleft">0 - 38&nbspDays</p>		
                                </li>
                                <li class="pending">
                                    <a href="#">
									<i class="ico ico-green">Action/<br>Processing </i>
                                        <span class="desc">										
											<div class="contentBox">
												<div class="date h6 text-secondary"><strong>Date 23/05/2019</strong></div>
												<p class="graySquire"> Yet not received any Report from Expenditure Officer</p>
												<p class="greenSquire">Sent 80% Report to CEO with Date</p>
												<p class="yellowSquire">Received all Reports from Expenditure Officer and Successfully sent to CEO with Date</p>	
												<p class="graySquire"> Yet not received any Report from Expenditure Officer</p>
												<p class="greenSquire">Sent 80% Report to CEO with Date</p>
												<p class="yellowSquire">Received all Reports from Expenditure Officer and Successfully sent to CEO with Date</p>
										</div>	
										</span>
                                    </a>
									<p class="dateleft">0 - 38&nbspDays </p>		
                                </li>
								<li class="pending">
                                    <a href="#">
									<i class="ico ico-green ptop">Finalized</i>
                                        <span class="desc">										
											<div class="contentBox">
												<div class="date h6 text-secondary"><strong>Date 23/05/2019</strong></div>
												<p class="graySquire"> Yet not received any Report from Expenditure Officer</p>
												<p class="greenSquire">Sent 80% Report to CEO with Date</p>
												<p class="yellowSquire">Received all Reports from Expenditure Officer and Successfully sent to CEO with Date</p>	
												<p class="graySquire"> Yet not received any Report from Expenditure Officer</p>
												<p class="greenSquire">Sent 80% Report to CEO with Date</p>
												<p class="yellowSquire">Received all Reports from Expenditure Officer and Successfully sent to CEO with Date</p>
										</div>	
										</span>
                                    </a>
									
                                </li>
<!--                               
							   <li class="locked">
                                    <a href="#">Locked <i class="ico fa fa-lock ico-muted"></i>
                                        <span class="desc">Lorem ipsum dolor sit amet, consectetur adipisicing elit. A, cumque.</span>
                                    </a>
                                </li>
                                <li class="locked">
                                    <a href="#">Images <i class="ico fa fa-lock ico-muted"></i>
                                        <span class="desc">Lorem ipsum dolor sit amet, consectetur adipisicing elit. A, cumque.</span>
                                    </a>
                                </li>
								-->
                            </ul>
                        </div>
                    </div>                  
			<!-- -->			

				</div>
			
			</div>
			
			
			
		</div>
	</section>
    
</main>

<!-- Validation  JavaScript -->

<!--**********FORM VALIDATION STARTS**********-->
<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<!--**********FORM VALIDATIONS SCRIPT**********-->
<script type="text/javascript">

jQuery('ul.tabs').each(function(){  
	  var $active, $content, $links = jQuery(this).find('a');
	  $active = jQuery($links.filter('[href="'+location.hash+'"]')[0] || $links[0]);
	  $active.addClass('active');
	  $content = jQuery($active[0].hash);
	  $links.not($active).each(function () {
		jQuery(this.hash).hide();
	  });
	  jQuery(this).on('click', 'a', function(e){   
		$active.removeClass('active');
		$content.hide();    
		$active = jQuery(this);
		$content = jQuery(this.hash);    
		$active.addClass('active');
		$content.show();   
		e.preventDefault();
	  });
	});


//*******************EXTRA VALIDATION METHODS STARTS********************//
//maxsize
$.validator.addMethod('maxSize', function (value, element, param) {
    return this.optional(element) || (element.files[0].size <= param)
});
//minsize
$.validator.addMethod('minSize', function (value, element, param) {
    return this.optional(element) || (element.files[0].size >= param)
});
//alphanumeric
$.validator.addMethod("alphnumericregex", function (value, element) {
    return this.optional(element) || /^[a-z0-9\._\s]+$/i.test(value);
});
//alphaonly
$.validator.addMethod("onlyalphregex", function (value, element) {
    return this.optional(element) || /^[a-z\.\s]+$/i.test(value);
});
//without space
$.validator.addMethod("noSpace", function (value, element) {
    return value.indexOf(" ") < 0 && value != "";
}, "No space please and don't leave it empty");
//*******************EXTRA VALIDATION METHODS ENDS********************//

//*******************ECI FILTER FORM VALIDATION STARTS********************//
$("#EciCustomReportFilter").validate({
    rules: {
        state: {required: true, noSpace: true},
        ScheduleList: {number: true},
    },
    messages: {
        state: {
            required: "Select state name.",
            noSpace: "State name must be without space.",
        },
        ScheduleList: {
            number: "Scedule ID should be numbers only.",
        },
    },
    errorElement: 'div',
    errorPlacement: function (error, element) {
        var placement = $(element).data('error');
        if (placement) {
            $(placement).append(error)
        } else {
            error.insertAfter(element);
        }
    }
});
//********************ECI FILTER FORM VALIDATION ENDS********************//

</script>
<!--graph implementation start here-Manoj-->
<script type="text/javascript">
//    $(document).ready(function () {
//        console.log("working Hurrah");
//        var id = 1;
//        $.ajax({
//            type: "get",
//            url: "{{url('/')}}/ropc/summary-graph/" + id,
//            dataType: "json",
//            success: function (response) {
//                console.log(response);
//            },
//            errors: function (errors) {
//                console.log(errors);
//            }
//        });
//    });
</script>
<script type="text/javascript">
    google.charts.load('current', {'packages': ['bar']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
        var id = 1;
        $.ajax({
            type: "get",
            url: "{{url('/')}}/ropc/summary-graph/" + id,
            dataType: "json",
            success: function (response) {
                var data = google.visualization.arrayToDataTable(response);
                var options = {
                    chart: {
                        title: 'Data Entry stated and Finalized',
                        subtitle: 'Summary',
                    },
                    bars: 'vertical' // Required for Material Bar Charts.
                };

                var chart = new google.charts.Bar(document.getElementById('barchart'));
                chart.draw(data, google.charts.Bar.convertOptions(options));

            },
            errors: function (errors) {
                console.log(errors);
            }
        });


    }
</script>
<script>
const steps = document.querySelectorAll('.stepper__row');
const stepsArray = Array.from(steps);
function clickHandler(target) {
  const currentStep = document.querySelector('.stepper__row--active');
  stateHandler(currentStep);
}

function stateHandler(step) {
  let nextStep;
  let currentStepIndex = stepsArray.indexOf(step);
  if (currentStepIndex < stepsArray.length - 1) {
    nextStep = stepsArray[currentStepIndex + 1];
    classHandler([nextStep, step])
  }
}
function classHandler(steps) {
  steps.forEach(step => {
    step.classList.toggle('stepper__row--disabled');
    step.classList.toggle('stepper__row--active');
  });
}
</script>
<!--graph implementation start here-Manoj
<!--**********FORM VALIDATION ENDS*************-->
@endsection
