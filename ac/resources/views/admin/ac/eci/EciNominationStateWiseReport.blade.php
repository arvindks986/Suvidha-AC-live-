@extends('admin.layouts.ac.theme')
@section('content')
<main role="main" class="inner cover mb-3">
<?php 

if(\Auth::user()->role_id == 4){
	$url ='acceo';
}else{
	$url ='eci';
}

?>
  <section class="dashboard-header section-padding">
  <div class="container-fluid">
  
        
    <form id="generate_report_id" class="row" method="get" onsubmit="return false;">
 

		<div class="form-group col-md-3"> <label>Election Type</label> 
          
            <select name="election_type" id="election_type" class="form-control" onchange ="filter()">
				<option value="" {{ (Request::get('election_type')=='') ? 'selected' : '' }}>All Election Type</option>
				<option value="3" {{ (Request::get('election_type')=='3') ? 'selected' : '' }}>AC-GENERAL</option>
				<option value="4" {{ (Request::get('election_type')=='4') ? 'selected' : '' }}>AC-BYE</option>
        
            </select>
          </div> 
          
        
          <div class="form-group col-md-3"> <label>Election Phase</label> 
          
            <select name="phase" id="phase" class="form-control" onchange ="filter()">
              <option value="">All</option>
            @foreach($phases as $result)
              @if($phase==$result->PHASE_NO)
                <option value="{{$result->PHASE_NO}}" selected="selected" >{{$result->PHASE_NO}}-Phase</option> 
              @else 
                <option value="{{$result->PHASE_NO}}" >{{$result->PHASE_NO}}-Phase</option> 
              @endif  
            @endforeach
        
            </select>
          </div>
		  
		  
		   <div class="form-group col-md-3"> <label>State </label> 
          
            <select name="state" id="state" class="form-control" onchange ="filter()">
            <option value="">Select State</option>
            @foreach($states as $result)
              @if($state== base64_decode($result['code']))
                <option value="{{$result['code']}}" selected="selected">{{$result['name']}}</option> 
              @else 
                <option value="{{$result['code']}}" >{{$result['name']}}</option> 
              @endif  
            @endforeach
        
            </select>
          </div>
		  
		  
		  
		  
		  <div class="form-group col-md-3">
           <label>Datewise Filter</label> &nbsp; <input value="" id="date_range" name="date_range" type="text" class="ranges form-control" placeholder="Date Range" autocomplete="off" />
      </div>
       
   
         



        </form>   
  
    
  </div>
</section>
 <!--FILTER ENDS HERE-->
   
<section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> List Of Nominations</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp; 
              @foreach($buttons as $raw)			  
				<a href="{{$raw['href']}}" class="btn btn-info" role="button">{{$raw['name']}}</a> &nbsp;&nbsp;			  
			  @endforeach
              </p>
              </div>
            </div>
      </div>
   
 <div class="card-body">  
    <table class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
         <tr>
        
          <th>AC No</th> 
          <th>AC Name</th> 
          <th>Total Nomination</th> 
        <th>Affidavit Uploaded</th>
        </tr>
        </thead>
        <tbody>
      @php  $count = 1; $total_nom = $total_aff = 0; @endphp
         @forelse ($EciNominationStateWiseReport as $key=>$listdata)
		 
		   @php   $total_nom += $listdata->totalnomination;
		  $total_aff += $listdata->affidavit_count; @endphp
		 
          <tr>
           
            <td><a href="{{url('/'.$url.'/EciNominationAcWiseReport?acno='.base64_encode($listdata->AC_NO).'&phase='.$phase.'&state=')}}{{base64_encode($listdata->ST_CODE)}}">{{ $listdata->AC_NO }}</a></td>
            <td><a href="{{url('/'.$url.'/EciNominationAcWiseReport?acno='.base64_encode($listdata->AC_NO).'&phase='.$phase.'&state=')}}{{base64_encode($listdata->ST_CODE)}}">{{ $listdata->AC_NAME }}</a></td>
            <td> @if($listdata->totalnomination =='' )     0  @else  {{ $listdata->totalnomination }} @endif</td>
           <td> @if($listdata->affidavit_count =='' )   0  @else {{ $listdata->affidavit_count }} @endif</td>
          </tr>
      
           @empty
                <tr>
                  <td colspan="4">No Data Found For Nominations</td>                 
              </tr>
          @endforelse
		  
		  	<tr>
             <td colspan="2" style="text-align:center;"><b>Total</b></td>
            <td> <b>{{$total_nom}}</b></td>
            <td> <b>{{$total_aff}}</b></td>
          </tr>
		  
        </tbody>
    </table>
    </div>
    </div>
  </div>
  </div>
  </section>
  </main>

<!-- Validation  JavaScript -->

<!--**********FORM VALIDATION STARTS**********-->
<script src="{{ asset('theme/vendor/jquery/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>

<script type="text/javascript">
$(document).ready(function() {  
  $('#date_range').daterangepicker({
    <?php if(isset($from) && isset($to)){ ?>
      startDate: moment('<?php echo $from ?>'),
      endDate: moment('<?php echo $to ?>'),
    <?php } ?>
      ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          //  'Last 14 Days': [moment().subtract(13, 'days'), moment()] ,          
          //  'This Month': [moment().startOf('month'), moment().endOf('month')],
          //  'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      maxDate: new Date()
  });
}); 


<?php if(!isset($from) && !isset($to)){ ?>
$(document).ready(function(e){
    $('#date_range').val('');
});
<?php } ?>


   
$(document).ready(function(e){
  $('#date_range').change(function(e){
    filter();
  });
  if($('#date_range').val()!=''){
    var clear_date_html = "<button id='clear_date'>Clear Date</button>";
    $('#clear_date').remove();
    $('#date_range').after(clear_date_html);
  }
  $('#clear_date').click(function(e){
    $('#date_range').val('');
    filter();
  });
});


function filter(){
  var url = "<?php echo $action ?>";
  var query = '';
  if(jQuery("#election_type").val() != '' && jQuery("#election_type").val() != 'undefined'){
      query += '&election_type='+jQuery("#election_type").val();
    }
  if(jQuery("#phase").val() != ''){
      query += '&phase='+jQuery("#phase").val();
    }
	
	if(jQuery("#state").val() != ''){
      query += '&state='+jQuery("#state").val();
    }
	
	 var val=  $('#date_range').val();
    var timeInterval= val.split('-'); 
    if(timeInterval[0] !='' && timeInterval[1] != ''){
      var from = moment(timeInterval[0]).format('DD-MM-YYYY');
      var to = moment(timeInterval[1]).format('DD-MM-YYYY');
      query += "&from="+from+'&to='+to;
    }
  window.location.href = url+'?'+query.substring(1);
}

setTimeout(function(e){
    referesh_page();
},300000);

function referesh_page(){
    location.reload();
}


</script>

@endsection


