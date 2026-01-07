@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'National Party Wise Expenditure')
@section('description', '')
@section('content') 
@php 
$st_code=!empty($st_code) ? $st_code : '0';
$cons_no=!empty($cons_no) ? $cons_no : '0';
$party = !empty($_GET['party'])?$_GET['party']:"";
$ac = !empty($_GET['ac'])?$_GET['ac']:""; 

$st=getstatebystatecode($st_code);
$acdetails=getacbyacno($st_code,$cons_no); 
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$acName=!empty($acdetails) ? $acdetails->AC_NAME : 'ALL';
 // echo $st_code.'cons_no'.$cons_no; die;
@endphp
<?php
$namePrefix = \Route::current()->action['prefix'];
if($namePrefix=="/acdeo"){
    $urlback="/acdeo/fund-nationalparties"; 
 }
 if($namePrefix=="/acceo"){
  $urlback="/acceo/fund-nationalparties";
 }
 if($namePrefix=="/eci-expenditure"){
    $urlback="/eci-expenditure/fund-nationalparties";
}
?>
    <style type="text/css">
    	.mt-5, .my-5{margin-top: 1rem!important;}
    </style>
<main role="main" class="inner cover mb-3">
	<section class="mt-5">
  <div class="container-fluid">
  <div class="row">
  	    <div class="card">
                    <div class=" card-header">

                        <div class=" row">
                            <div class="col-sm-5"><h2 class="mr-auto">Fund given by National Parties to their Candidate</h2></div> 
                            <div class="col-sm-7">
                                <p class="mb-0 text-right">
                                     <a href="javascript:window.print()"><i class="fa fa-print"></i></a>
                                     &nbsp; &nbsp; &nbsp;
                                    <a href="{{url('/')}}{{$urlback}}"> <button type="button" id="Back" class="btn btn-primary">Back</button></a>
                               
                                </p>
                           
                            </div>
                        </div><!-- end row-->
                    </div>
                
<div class="card-body"> 
<?php $j=1; 
 $allPartylist=[];
    
 $grandTotalParty=0;  
 $grandTotalOtherSources=0;  
 $grandTotalAvgParty =0;  
 $grandTotalOverall  =0;  

	?>

		@if(!empty($partylist))
		@foreach($partylist as $partylists)  

     @php
         $totalcandidates=\app(App\models\Expenditure\ExpenditureModel::class)->getcandidatesbyparties($partylists->CCODE,$st_code,$ac);
		 $countPartywiseCandidate = count(explode(',',$totalcandidates));
		 
		 $totalpartyexpen=\app(App\models\Expenditure\ExpenditureModel::class)->getPoliticalpartyExp($totalcandidates);
         $grandTotalParty += $totalpartyexpen; 
		 
		 $avgpartyexpencandidatewise= round($totalpartyexpen/$countPartywiseCandidate,2);
		 $grandTotalAvgParty += $avgpartyexpencandidatewise; 
			
		 $totalothersexpen=\app(App\models\Expenditure\ExpenditureModel::class)->getOtherSourcesExp($totalcandidates);
         $grandTotalOtherSources += $totalothersexpen; 
		 
		 $totaloverallexpen=\app(App\models\Expenditure\ExpenditureModel::class)->getGrandTotalExp($totalcandidates);
         $grandTotalOverall += $totaloverallexpen; 
    
     $allPartylist[]=[
     'PARTYABBRE'=>$partylists->PARTYABBRE,
     'PARTYNAME'=>$partylists->PARTYNAME,
     'avgpartyexpencandidatewise'=> $avgpartyexpencandidatewise,
	 'totalpartyexpen'=>$totalpartyexpen,
	 'totalothersexpen'=>$totalothersexpen,
	 'totaloverallexpen'=>$totaloverallexpen,
	 'totalcandidates'=>$countPartywiseCandidate
     ]; @endphp
     @endforeach  
@endif
<?php 
$amount=array_column($allPartylist,'totalpartyexpen');
array_multisort($amount, SORT_DESC,$allPartylist);
?>
  <div class="table-responsive">
      <table   class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
          <th style="color: #080808;">S.no</th>
          <th style="color: #080808;">Party</th>
		  <th style="color: #080808;">Average funds given to a candidate by national parties(Rs.)</th>
          <th style="color: #080808;">Total funds given by National Parties(Rs.)</th>
		  <th style="color: #080808;">Total funds given by Other Sources(Rs.)</th>
		  <th style="color: #080808;">No. of candidate to whome National Parties gave funds</th>
        </tr>
        </thead>
        @if(!empty($partylist))
		@foreach($allPartylist as $partylists) 
<tr>
<td><?php echo $j++; ?></td>
<td>{{ $partylists['PARTYABBRE'] }} - {{$partylists['PARTYNAME']}}</td>
<td align="right">{{$partylists['avgpartyexpencandidatewise']}}</td>
<td align="right">{{$partylists['totalpartyexpen']}}</td>
<td align="right">{{$partylists['totalothersexpen']}}</td>
<td align="right"> {{ !empty($partylists['totalpartyexpen'])? $partylists['totalcandidates']:0}}</td>

</tr>
@endforeach  
@endif
<tfoot>
  <tr>
    <td colspan="2"><b>Total Expenditure</b></td>
	 <td align="right"><b>{{$grandTotalAvgParty}}</b></td>
	 <td align="right"><b>{{$grandTotalParty}}</b></td>
	 <td align="right"><b>{{$grandTotalOtherSources}}</b></td>
	<td></td>
  </tr>
</tfoot>
            </table>
           </div> <!-- end responcive-->
          </div> <!-- end card-body-->
           <div class="card-body"> 
                        <div class="row">

                            <div class="col-lg-12 col-md-12 col-sm-12" >
                                <div class="card text-left">
                                    <div class="text-center mt-3 graph1" style="display:none;">
                                        <button class="btn btn-primary" type="button" disabled>
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            Loading...
                                        </button> 
                                    </div>
                                    <div class="card-body"  class="collapse show">

                                        <div id="barchart"></div>

                                    </div>
                                </div>
                            </div> 

                            <div class="col-lg-12 col-md-12 col-sm-12 ">

                                <div class="card text-left">
                                    <div class="text-center mt-3 graph2" style="display:none;">
                                        <button class="btn btn-primary" type="button" disabled>
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            Loading...
                                        </button> 
                                    </div>
                                    <div class="card-body"  class="collapse show">

                                        <div id="barchart2"></div>

                                    </div>
                                </div>
                            </div>
                            <div class=" row">
                             
                            <div class="col-sm-12">
                                <p>
                                   Note:
                                     *M=Million,  *B=Billion,  *K=Thousand
                               
                                </p>
                           
                            </div>
                        </div><!-- end row-->
                        </div>
                    </div>
        </div>
      </div>
     </div>
   	</div>
  </section>
	
	</main>
  

  
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script> 

<script type="text/javascript">
google.charts.load('current', {'packages': ['bar']});
google.charts.setOnLoadCallback(drawChart);
function drawChart() {

    $.ajax({
        type: "get",
        beforeSend: function () {
            // Show image container
            $(".graph1").css('display', 'block');
        },
        url: "{{url('/')}}/eci-expenditure/fund-nationalpartiesnationgraph",
        dataType: "json",
        success: function (response) {
            $(".graph1").css('display', 'none');
            var data = google.visualization.arrayToDataTable(response);
            var options = {
                chart: {

                    title: 'No. of candidate to Whom National Parties gave funds',
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
    $.ajax({
        type: "get",
        beforeSend: function () {
            // Show image container
            $(".graph2").css('display', 'block');
        },
        url: "{{url('/')}}/eci-expenditure/fund-nationalpartiesavggraph",
        dataType: "json",
        success: function (response) {
            $(".graph2").css('display', 'none');
            var data = google.visualization.arrayToDataTable(response);
            var options = {

                chart: {
                    title: 'Average funds given to a candidate by National parties',
                },
                bars: 'vertical' // Required for Material Bar Charts.
            };

            var chart = new google.charts.Bar(document.getElementById('barchart2'));
            chart.draw(data, google.charts.Bar.convertOptions(options));

        },
        errors: function (errors) {
            console.log(errors);
        }
    });


}
</script>
<!--graph implementation start here-Manoj -->
 
@endsection
