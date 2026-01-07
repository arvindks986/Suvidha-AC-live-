@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate Wise Expenditure')
@section('description', '')
@section('content') 
 
 @php 
 $ac = !empty($_GET['ac'])?$_GET['ac']:"";
$st_code=!empty($st_code) ? $st_code : '0';
$cons_no=!empty($cons_no) ? $cons_no :  $ac;
$party = !empty($_GET['party'])?$_GET['party']:"";
 

$st=getstatebystatecode($st_code);
$acdetails=getacbyacno($st_code,$ac); 
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$acName=!empty($acdetails) ? $acdetails->AC_NAME : 'ALL';
$all_ac=getacbystate($st_code);
$graphText='';
if(!empty($st->ST_NAME)){
$graphText.=$st->ST_NAME;
}
if(!empty($acdetails->AC_NAME)){
$graphText.=' '.$acdetails->AC_NAME.'(AC)';
}
if(!empty($party)){
$partydetails=getpartybyid($party);
$partyName=!empty($partydetails->PARTYNAME)?$partydetails->PARTYNAME:'';
$graphText.=' '.$partyName.'(Party)';
}
if(empty($graphText)){
  $graphText='All States';
}


 $noData='';
@endphp
 <?php 
	// //$st=getstatebystatecode($user_data->st_code);
	// $distname=getdistrictbydistrictno($user_data->st_code,$user_data->dist_no);
	// $pcdetails=getpcbypcno($user_data->st_code,$user_data->ac_no); 
 //  $stcode = !empty($_GET['state'])?$_GET['state']:$st_code;
 //    $ac = !empty($_GET['ac'])?$_GET['ac']:"";
 //    $all_ac=getacbystate($stcode);

 //  $st=getstatebystatecode($stcode);
 //  $acdetails=getacbyacno($stcode, $ac); 
 //  $acName=!empty($acdetails->AC_NAME) ? $acdetails->AC_NAME : 'ALL';
 //  $stateName=!empty($st->ST_NAME) ? $st->ST_NAME : 'ALL';

    ?>
    <style type="text/css">
    	.mt-5, .my-5{margin-top: 1rem!important;}
    </style>
<main role="main" class="inner cover mb-3">
	<section class="mt-5">
  <div class="container-fluid">
  <div class="row">
  	<div class="col-sm-12 mt-3">
              <!--FILTER STARTS FROM HERE-->
              <form method="get" action="{{url('/eci-expenditure/candidate_wise_expenditure')}}" id="EcidashboardFilter">           
                       <div class="row justify-content-center">
                      <!--STATE LIST DROPDOWN STARTS-->
                        <div class="col-sm-3">
                        <label for="" class="mr-3">Select State</label>    
                        <select name="state" id="state" class="form-control">
                        <?php if($stateName=='ALL') { ?> <option value="">All States</option> <?php } ?>
                      @foreach ($statelist as $state_List ))
                      <option value="{{ $state_List->ST_CODE }}" <?php if(!empty($_GET['state']) && $state_List->ST_CODE==$_GET['state']){ echo "selected";} ?>>{{$state_List->ST_NAME}}</option>
                      @endforeach

                      @if ($errors->has('state'))
                      <span class="help-block">
                          <strong class="user">{{ $errors->first('state') }}</strong>
                      </span>
                      @endif
                      <div class="stateerrormsg errormsg errorred"></div>
                  </select> 
                        </div>
                          <!--STATE LIST DROPDOWN ENDS-->
					       	<div class="col-sm-3">
                        <label for="" class="mr-3">Select AC</label>    
                        <select name="ac" id="ac" class="consttype form-control" >
                                <option value="">-- All AC --</option>
                @if (!empty($all_ac))
                @foreach($all_ac as $getAc)

                @if ($ac ==  $getAc->AC_NO)
                <option value="{{ $getAc->AC_NO }}" selected>{{$getAc->AC_NO }} - {{$getAc->AC_NAME }}</option>
                @else
                 <option value="{{ $getAc->AC_NO }}" <?php if(!empty($_GET['pc']) && $getAc->AC_NO==$_GET['pc']){ echo "selected";} ?>>{{$getAc->AC_NO }} - {{$getAc->AC_NAME }}</option>
                 @endif

                @endforeach 
                @endif
                            </select>
					    @if ($errors->has('ac'))
                  		  <span style="color:red;">{{ $errors->first('pc') }}</span>
               			@endif
                     
							<div class="acerrormsg errormsg errorred"></div>
                        </div>
					  	<div class="col-sm-1 mt-2">
							<p class="mt-4 text-left">
							<!-- <button type="button" id="Back" class="btn btn-primary">Filter</button> -->
						  <input type="submit" value="Filter" id="Filter" class="btn btn-primary">
               
 <!-- <a href="{{url('/eci-expenditure/candidate_wise_expenditure')}}"><input type="button" value="Cleard Filter" id="Filter" class="btn btn-primary"></a> -->
            	
              </p>
                        </div>
                    </div>
                </form> 
                 <!--FILTER ENDS HERE-->
				</div> 
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                   @if (Session::has('message'))
                        <div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>{{ Session::get('message') }} </div> 
                        @php Session::forget('message'); @endphp
                        @elseif (Session::has('error'))
                        <div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            {{ Session::get('error') }} <br/>

                        </div>
                        @php Session::forget('error'); @endphp
                        @endif
                <div class=" row">
                 <div class="col"><h2 class="mr-auto">Candidate Wise Expenditure</h2></div> 
                   <div class="col"><p class="mb-0 text-right">
												<b>State Name:</b> 
												<span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
												<b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
												<b>AC:</b> <span class="badge badge-info">{{$acName}}</span>
                        <span class="badge badge-info"></span>&nbsp;&nbsp;
            <a href="{{url('/eci-expenditure/candidate_wise_expenditure')}}?ac={{$ac}}&state={{$st_code}}&pdf=yes" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
              <a href="{{url('/eci-expenditure/candidate_wise_expenditure')}}?ac={{$ac}}&state={{$st_code}}&exl=yes" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
									  </p></div>
										</div><!-- end row-->
	              </div><!-- end card-header-->
<div class="card-body">  
    <div class="row">
                            <div class="col-lg-7 col-md-7 col-sm-7">

                                <?php
                                $allPartylist = [];
                                if (!empty($candList)) {
                                    foreach ($candList as $candDetails) {
 
                                        $totalamount = !empty($candDetails->grand_total_election_exp_by_cadidate)? $candDetails->grand_total_election_exp_by_cadidate : 0; 
                                        $allPartylist[] = [
                                            'st_code' => $candDetails->st_code,
                                            'ac_no' => $candDetails->ac_no,
                                            'YEAR' => $candDetails->YEAR,
                                            'ELECTION_TYPE' => $candDetails->ELECTION_TYPE, 
                                            'candidate_id' => $candDetails->candidate_id,                                            
                                            'cand_name' => $candDetails->cand_name,
                                            'grand_total_election_exp_by_cadidate' => $totalamount
                                            
                                        ];
                                    }
                                }

                                $amount = array_column($allPartylist, 'grand_total_election_exp_by_cadidate');
                                array_multisort($amount, SORT_DESC, $allPartylist);
                                   $noData=  empty($allPartylist)?'No Data Available Graph':'';
                                ?>
  <div class="table-responsive">
      <table id="example1" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
          <th>Candidate Name</th>
          <th>State</th>
          <th>AC No & AC Name</th>
          <th>Election Year</th>
		  <th>Election Type</th>
  		  <th>Total Expenditure Declared By Candidate(Rs.)</th>
        </tr>
        </thead>
<?php $j=0; $grandTotal=0;  ?>
		@if(!empty($allPartylist))
		@foreach($allPartylist as $candDetails)  
			<?php
				$acdetails=getacbyacno($candDetails['st_code'],$candDetails['ac_no']); 
				$st=getstatebystatecode($candDetails['st_code']);
				$j++; 
        ?>
        @php
         $totalamount = !empty($candDetails['grand_total_election_exp_by_cadidate'])? $candDetails['grand_total_election_exp_by_cadidate'] : 0; 
        $grandTotal += $totalamount;
        @endphp
<tr>
<td>@if(!empty($candDetails['cand_name'])) {{$candDetails['cand_name']}} @endif </td>
<td>{{$st->ST_NAME}}</td>
<td>{{$acdetails->AC_NO}} - {{$acdetails->AC_NAME}}</td>
<td>@if(!empty($candDetails['YEAR'])) {{$candDetails['YEAR']}} @endif</td>
<td>@if(!empty($candDetails['ELECTION_TYPE'])) {{$candDetails['ELECTION_TYPE']}} @endif</td>
<td align="right">{{$totalamount}}</td>
</td>

</tr>
@endforeach 
@endif 
<tfoot>
  <tr>
    <td colspan="5"><b>Total Expenditure(Rs.)</b></td>
    <td><b>Rs. {{$grandTotal}}</b></td>
  </tr>
</tfoot>
            </table>
           </div> <!-- end responcive-->
           </div> 
             <div class="col-sm-5" >
                            <div class="card text-left">
                                <div class="text-center mt-3 graph1" style="display:none;">
                                    <button class="btn btn-primary" type="button" disabled>
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Loading...
                                    </button> 
                                </div>
                                            @if(!empty($allPartylist))
                                <div class="card-body"  class="collapse show">

                                    <div id="piechart" style="width: 530px; height: 500px;"></div>

                                </div>
                                  @else
                                      <div class="card-body"  class="collapse">
                                         {{$noData}}
                                          </div>
                                         @endif
                            </div>
                        </div> 
          </div> <!-- end card-body-->
        </div>
      </div>
     </div>
   	</div>
  </section>
	
	</main>
 

<!--**********FORM VALIDATION STARTS**********-->
<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>
 

 



<script type="text/javascript">
jQuery(document).ready(function(){ 
    jQuery("select[name='state']").change(function(){
        var state = jQuery(this).val();  
       // alert(state);
        jQuery.ajax({ 
            url: '<?php echo url('/') ?>/eci-expenditure/getacbystate',
            type: 'GET',
            data: {state:state},
         
            success: function(result){  
                //console.log(result); 
                var stateselect = jQuery('form select[name=ac]');
                stateselect.empty();
                var achtml = '';
                achtml = achtml + '<option value="">-- All AC --</option> ';
                jQuery.each(result,function(key, value) { 
                    achtml = achtml + '<option value="'+value.AC_NO+'">'+value.AC_NO+' - '+value.AC_NAME + '</option>';
                    jQuery("select[name='ac']").html(achtml);
                });
                var achtml_end = '';
                jQuery("select[name='ac']").append(achtml_end)
            }
        });
    });

    });

  jQuery(document).ready(function(){ 
   $('#example1').DataTable();
 }); 

</script>

<?php
 
if (!empty($allPartylist)) {
    $toptenrecords = array_slice($allPartylist, 0, 9);
    $toptenrecords2=[];
    foreach($toptenrecords as $item2){
        $toptenrecords2[]=[
            'cand_name'=>$item2['cand_name'],
             'totalexpen'=>$item2['grand_total_election_exp_by_cadidate']
        ];
        
    }
    ?>
    
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script> 

    <script type="text/javascript">
    google.charts.load("current", {packages: ["corechart"]});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
        var options = {
            title: '<?php echo $graphText; ?>',
            is3D: true,
        };
        var data = google.visualization.arrayToDataTable(
                [
                    ['<?php echo $graphText; ?>', '<?php echo $graphText; ?>'],
    <?php
    foreach ($toptenrecords as $item) {
        ?>
                        [<?php echo '"' . $item['cand_name'] . '",', $item['grand_total_election_exp_by_cadidate'] ?>],
    <?php }
    ?>
                ]);
        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(data, options);

    }
    </script>
    <?php
}
else{
   $noData='No Data Available Graph'; 
}
?>
@endsection
