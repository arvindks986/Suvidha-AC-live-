@extends('admin.layouts.ac.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Analytical Dashboard')
@section('description', '')
@section('content')
<?php 

$st=getstatebystatecode($user_data->st_code);
//$pcdetails=getpcbypcno($user_data->st_code,$user_data->pc_no); 
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
//$pcName=!empty($pcdetails) ? $pcdetails->PC_NAME : 'ALL';
$distname=getdistrictbydistrictno($user_data->st_code,$user_data->dist_no);
//dd($distname);
?>
<main role="main" class="inner cover mb-1">     
    <section class="breadcrumb-section">
        <div class="container-fluid">
            <div class=" row">
                <div class="col"><h2 class="mr-auto">Pending Candidate List : {{$count}}</h2></div> 
                <div class="col"><p class="mb-0 text-right">
                        <b>State Name:</b> 
                        <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; 
                        <b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
                        <b>District Name:</b> <span class="badge badge-info">{{$distname->DIST_NAME}}</span>
                        <b></b> <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>

                    </p></div>
            </div> <!-- end row -->
        </div>

    </section>
    <section class="mt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card text-left" style="width:100%;">
                        <!--SELECT CANDIDATE-->
                        <div class="card-body" id="demo" class="collapse show">  
                            <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>AC No & Name</th>
                                        <th>Candidate Name</th>
                                        <th>Party Name</th>
                                         <th>Action</th>
                                    </tr>
                                </thead>
                                <?php $j = 0; ?>
                                @if(!empty($pendingCandList))
                                @foreach($pendingCandList as $candDetails)  
                                <?php
								//dd($candDetails);
                               $ac=getacbyacno($candDetails->st_code,$candDetails->ac_no);
                                $date = new DateTime($candDetails->created_at);
                                //echo $date->format('d.m.Y'); // 31.07.2012
                                $lodgingDate = $date->format('d-m-Y'); // 31-07-2012
                                $j++;
                                ?>
							<tr>
							   <td>@if(!empty($ac->AC_NO))  {{ $ac->AC_NO}}-{{ $ac->AC_NAME}} @endif</td>
								<td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
								<td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
								<td >                                       
							<a href="{{url('/')}}/acdeo/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary" target="_blank">Scrutiny Report</a> 
							</td>
							</tr>
							@endforeach 
							@endif 
							<tbody>
							</tbody>
					</table>
				</div>
               </div>
                    <!--END OF SELECT CANDIDATE-->
                </div>
                <!--<div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="card text-left" style="width:100%;">

                        <div class="card-body"  class="collapse show">
                            @if($count>0)
                            <div id="barchart"></div>
                            @else
                            No data for graph. 
                            @endif
                        </div>
                    </div>
                </div>-->
            </div>
        </div>
    </section>
</main>


@endsection


