@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Party Wise Expenditure')
@section('description', '')
@section('content') 
@php 
$ac = !empty($_GET['ac'])?$_GET['ac']:"";
$st_code=!empty($st_code) ? $st_code : '0';
$cons_no=!empty($cons_no) ? $cons_no : $ac;
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


    <style type="text/css">
    	.mt-5, .my-5{margin-top: 1rem!important;}
    </style>
<main role="main" class="inner cover mb-3">
	<section class="mt-5">
  <div class="container-fluid">
  <div class="row">
  	<div class="col-sm-12 mt-3">
              <!--FILTER STARTS FROM HERE-->
              <form method="get" action="{{url('/eci-expenditure/getPartyWiseExpenditure')}}" id="EcidashboardFilter">           
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
                          <span style="color:red;">{{ $errors->first('ac') }}</span>
                        @endif
                     
                            <div class="acerrormsg errormsg errorred"></div>
                        </div>
                        <div class="col-sm-3">
                        <label for="" class="mr-3">Select Party</label>    
                        <select name="party" id="party" class="form-control">
                      <option value="">All Party</option>
                     @php $patrylist = getallpartylist(); @endphp
                      @foreach ($patrylist as $party_List ))
                      <option value="{{ $party_List->CCODE }}" <?php if(!empty($_GET['party']) && $party_List->CCODE==$_GET['party']){ echo "selected";} ?>>{{$party_List->PARTYNAME}}-{{$party_List->PARTYABBRE}}</option>
                      @endforeach

                      @if ($errors->has('party'))
                      <span class="help-block">
                          <strong class="user">{{ $errors->first('party') }}</strong>
                      </span>
                      @endif
                      <div class="stateerrormsg errormsg errorred"></div>
                  </select> 
                        </div>




                          <!--STATE LIST DROPDOWN ENDS-->
					       	
					  	<div class="col-sm-2 mt-2">
							<p class="mt-4 text-left">
							<!-- <button type="button" id="Back" class="btn btn-primary">Filter</button> -->
						  <input type="submit" value="Filter" id="Filter" class="btn btn-primary">
              <!-- <a href="{{url('/eci-expenditure/getPartyWiseExpenditure')}}"><input type="button" value="Clear Filter" id="Filter" class="btn btn-primary"></a> -->
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
                 <div class="col-sm-5"><h2 class="mr-auto">Party Wise Expenditure</h2></div> 
                   <div class="col-sm-7"><p class="mb-0 text-right">
												<b>State Name:</b> 
												<span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
												<b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
												<b>AC:</b> <span class="badge badge-info">{{ $acName}}</span>
                        <span class="badge badge-info"></span>&nbsp;&nbsp;
            <a href="{{url('/eci-expenditure/getPartyWiseExpenditure')}}?party={{$party}}&ac={{$ac}}&state={{$st_code}}&pdf=yes" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
              <a href="{{url('/eci-expenditure/getPartyWiseExpenditure')}}?party={{$party}}&ac={{$ac}}&state={{$st_code}}&exl=yes" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
									  </p>
                  </div>
										</div><!-- end row-->
	              </div><!-- end card-header-->
<div class="card-body"> 
     <?php
                    $j = 1;
                    $allPartylist = [];
                    $grandTotal = 0;
                    
                    ?>

                    @if(!empty($partylist))
                    @foreach($partylist as $partylists)  

                    @php
                    $totalexpen=\app(App\models\Expenditure\ExpenditureModel::class)->getpartytotalexpenditure($partylists->CCODE,$st_code,$ac);
                    $grandTotal += $totalexpen; 

                    $allPartylist[]=[
                    'CCODE'=>$partylists->CCODE,
                    'PARTYABBRE'=>$partylists->PARTYABBRE,
                    'PARTYNAME'=>$partylists->PARTYNAME,
                    'totalexpen'=>$totalexpen
                    ]; @endphp
                    @endforeach  
                    @endif
                    <?php
                    $amount = array_column($allPartylist, 'totalexpen');
                    array_multisort($amount, SORT_DESC, $allPartylist);
                       $noData=  empty($allPartylist)?'No Data Available Graph':'';
                    ?>
                     <div class="row">

                            <div class="col-lg-6 col-md-6 col-sm-6" >

  <div class="table-responsive">
      <table id="example1" class="table table-bordered" style="width:100%">
        <thead>
        <tr>
          <th>S.no</th>
          <th>Party Name</th>
          <th>Total Expenditure</th>
        </tr>
        </thead>
        <tbody>
    <?php $j=1;
    $grandTotal=0;  ?>
		@if(!empty($allPartylist))
		@foreach($allPartylist as $partylists)  

     @php

        $totalexpen=\app(App\models\Expenditure\ExpenditureModel::class)->getpartytotalexpenditure($partylists['CCODE'],$st_code,$ac);

        $grandTotal += $totalexpen; 
     @endphp
<tr>
<td><?php echo $j++; ?></td>
<td>{{$partylists['PARTYABBRE']}} - {{$partylists['PARTYNAME']}}</td>
<td>Rs. {{$totalexpen}}</td>
</tr>
@endforeach  
@else
No data found
@endif
</tbody>

<tfoot>
  <tr>
    <td colspan="2"><b>Total Expenditure</b></td>
    <td><b>Rs. {{$grandTotal}}</b></td>
  </tr>
</tfoot>
            </table>


           </div> <!-- end responcive-->
            </div>
               <div class="col-lg-6 col-md-6 col-sm-6" >
                                <div class="card text-left">
                                    <div class="text-center mt-3 graph1" style="display:none;">
                                        <button class="btn btn-primary" type="button" disabled>
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            Loading...
                                        </button> 
                                    </div>
                                     @if(!empty($allPartylist))
                                    <div class="card-body"  class="collapse show">

                                        <div id="piechart" style="width: 600px; height: 500px;"></div>

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

$(document).ready(function() {
    $('#example1').DataTable();
 });

</script>
<?php
 
if (!empty($allPartylist)) {
    $toptenrecords = array_slice($allPartylist, 0, 9);
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
                        [<?php echo '"' . $item['PARTYNAME'] . '(' . $item['PARTYABBRE'] . ')",', $item['totalexpen'] ?>],
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
