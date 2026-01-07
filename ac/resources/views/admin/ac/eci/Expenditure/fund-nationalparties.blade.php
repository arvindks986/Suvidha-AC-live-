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

    <style type="text/css">
    	.mt-5, .my-5{margin-top: 1rem!important;}
    </style>
<main role="main" class="inner cover mb-3">
	<section class="mt-5">
  <div class="container-fluid">
  <div class="row">
  	<div class="col-sm-12 mt-3">
              <!--FILTER STARTS FROM HERE--
              <form method="get" action="{{url('/eci-expenditure/getPartyWiseExpenditure')}}" id="EcidashboardFilter">           
                       <div class="row justify-content-center">
                      <!--STATE LIST DROPDOWN STARTS--

                      <div class="col-sm-3">
                        <label for="" class="mr-3">Select State</label>    
                        <select name="state" id="state" class="form-control">
                      <?php if($stateName=='ALL') {  ?> <option value="">All States</option> <?php } ?>
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
                          <!--STATE LIST DROPDOWN ENDS--
                  <div class="col-sm-3">
                        <label for="" class="mr-3">Select PC</label>    
                        <select name="pc" id="pc" class="consttype form-control" >
                <option value="">-- All PC --</option>
                @if (!empty($all_pc))
                @foreach($all_pc as $getPc)

                @if ($pc ==  $getPc->PC_NO)
                <option value="{{ $getPc->PC_NO }}" selected>{{$getPc->PC_NO }} - {{$getPc->PC_NAME }}- {{$getPc->PC_NAME_HI}}</option>
                @else
                 <option value="{{ $getPc->PC_NO }}" <?php if(!empty($_GET['pc']) && $getPc->PC_NO==$_GET['pc']){ echo "selected";} ?>>{{$getPc->PC_NO }} - {{$getPc->PC_NAME }} - {{$getPc->PC_NAME_HI}}</option>
                 @endif

                @endforeach 
                @endif
              </select>
              @if ($errors->has('pc'))
                        <span style="color:red;">{{ $errors->first('pc') }}</span>
                    @endif
                     
              <div class="acerrormsg errormsg errorred"></div>
                        </div>
                        <div class="col-sm-3">
                        <label for="" class="mr-3">Select Party</label>    
                        <select name="party" id="party" class="form-control">
                      <option value="">All Party</option>
                     @php $patrylist = getallpartylist(); @endphp
                      @foreach ($patrylist as $party_List ))
                      <option value="{{ $party_List->CCODE }}" <?php if(!empty($_GET['party']) && $party_List->CCODE==$_GET['party']){ echo "selected";} ?>>{{$party_List->PARTYABBRE}} - {{$party_List->PARTYNAME}}</option>
                      @endforeach

                      @if ($errors->has('party'))
                      <span class="help-block">
                          <strong class="user">{{ $errors->first('party') }}</strong>
                      </span>
                      @endif
                      <div class="stateerrormsg errormsg errorred"></div>
                  </select> 
                        </div>

                          <!--STATE LIST DROPDOWN ENDS--
					       	
					  	<div class="col-sm-2 mt-2">
							<p class="mt-4 text-left">
							<!-- <button type="button" id="Back" class="btn btn-primary">Filter</button> --
						  <input type="submit" value="Filter" id="Filter" class="btn btn-primary">
             <!--  <a href="{{url('/eci-expenditure/getPartyWiseExpenditure')}}"><input type="button" value="Clear Filter" id="Filter" class="btn btn-primary"></a> --
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
                 <div class="col-sm-5"><h2 class="mr-auto">Fund given by National Parties to their Candidate</h2></div> 
                   <div class="col-sm-7"><p class="mb-0 text-right">
												<b>State Name:</b> 
												<span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
												<b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
												<b>AC:</b> <span class="badge badge-info">{{ $acName}}</span>
                        <span class="badge badge-info"></span>&nbsp;&nbsp;
            <a href="{{url('/eci-expenditure/fund-nationalparties')}}?party={{$party}}&ac={{$ac}}&state={{$st_code}}&pdf=yes" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
             <!-- <a href="{{url('/eci-expenditure/getPartyWiseExpenditure')}}?party={{$party}}&ac={{$ac}}&state={{$st_code}}&exl=yes" class="btn btn-info" role="button">Export Excel</a>--> 
             <a href="{{url('/eci-expenditure/fund-nationalparties-graph')}}" class="btn btn-info" role="button">Graph</a>
									  </p>
                  </div>
										</div><!-- end row-->
	              </div><!-- end card-header-->
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
      <table id="example" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
          <th>S.no</th>
          <th>Party</th>
		  <th>Average funds given to a candidate by national parties(Rs.)</th>
          <th>Total funds given by National Parties(Rs.)</th>
		  <th>Total funds given by Other Sources(Rs.)</th>
		  <th>No. of candidate to whome National Parties gave funds</th>
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
<td align="right"> {{!empty($partylists['totalpartyexpen'])? $partylists['totalcandidates']:0}}</td>

</tr>
@endforeach  
@endif
<tfoot>
  <tr>
    <td colspan="2"><b>Total Expenditure(Rs.)</b></td>
	 <td align="right"><b>{{$grandTotalAvgParty}}</b></td>
	 <td align="right"><b>{{$grandTotalParty}}</b></td>
	 <td align="right"><b>{{$grandTotalOtherSources}}</b></td>
	<td></td>
  </tr>
</tfoot>
            </table>
           </div> <!-- end responcive-->
          </div> <!-- end card-body-->
        </div>
      </div>
     </div>
   	</div>
  </section>
	
	</main>
 <!-- Modal -->
<div class="modal fade" id="ModalProfile" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <div class="col"><center><h4>Candidate Status</h4></center></div>
                <br>
                <div class="profileData"></div>
            </div>

            <!--            <button id='cmd' ids="">generate PDF</button>-->
        </div>

    </div>
</div>
<!-- ProfileRO-->
 
<!-- end pop up -->

<!-- Validation  JavaScript -->

<!--**********FORM VALIDATION STARTS**********-->
<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>

 <!-- Modal -->
    <div class="modal fade" id="myModalcheck" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="myModalLabel" style="text-align: -webkit-center;">Are you sure give permission to update scrutiny report?</h6>
                </div>
                <div class="modal-footer mb-2">
                	 <input type="hidden" value="" id="definalizedreport">
                	 <input type="button" value="Ok" id="definalized" class="btn btn-primary mt-2" data-dismiss="modal">
                    <input type="button" value="Cancel" id="" class="btn btn-default mt-2" data-dismiss="modal">
                   <!--  <input type="button" value="" id="definalizedreport"  class="btn btn-primary btncl mt-2" data-dismiss="modal"> -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="myModaldefi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="myModalLabel"><center>Scrutiny Report is definalized.</center></h6>
                </div>
                <div class="modal-footer mb-2">
                    <input type="button" value="Ok" id="" class="btn btn-primary mt-2" data-dismiss="modal">
                </div>
            </div>
        </div>
    </div>

<script type="text/javascript">
  jQuery(document).ready(function(){ 
  jQuery("select[name='state']").change(function(){
    var state = jQuery(this).val();  
   // alert(state);
        jQuery.ajax({ 
          url: '<?php echo url('/') ?>/eci-expenditure/getpcbystate',
            type: 'GET',
            data: {state:state},
         
            success: function(result){  
              console.log(result); 
                var stateselect = jQuery('form select[name=pc]');
                stateselect.empty();
                var pchtml = '';
                pchtml = pchtml + '<option value="">-- All PC --</option> ';
                jQuery.each(result,function(key, value) { 
                    pchtml = pchtml + '<option value="'+value.PC_NO+'">'+value.PC_NO+' - '+value.PC_NAME + ' - ' +value.PC_NAME_HI+'</option>';
                    jQuery("select[name='pc']").html(pchtml);
                });
                var pchtml_end = '';
                jQuery("select[name='pc']").append(pchtml_end)
            }
        });
    });
     });


</script>
<!--graph implementation start here-Manoj -->
@endsection
