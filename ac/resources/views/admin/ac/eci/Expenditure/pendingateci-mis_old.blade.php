@extends('admin.layouts.pc.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
@section('description', '')
@section('content')
@php
  $st_code=!empty($st_code) ? $st_code : '0';
  $cons_no=!empty($cons_no) ? $cons_no : '0';
  $st=getstatebystatecode($st_code);
  $distname=getdistrictbydistrictno($st_code,$user_data->dist_no);
  $pcdetails=getpcbypcno($st_code, $cons_no); 
  $pcName=!empty($pcdetails->PC_NAME) ? $pcdetails->PC_NAME : 'ALL';
  $stateName=!empty($st->ST_NAME) ? $st->ST_NAME : 'ALL';
// echo $st_code.'cons_no=>'.$cons_no;
@endphp 
<main role="main" class="inner cover mb-1">     
    <section class="breadcrumb-section">
        <div class="container-fluid">
            <div class=" row">
                <div class="col"><h2 class="mr-auto">Candidate List Pending At ECI : {{$count}}</h2></div> 
                <div class="col"><p class="mb-0 text-right">
                        <b>State Name:</b> 
                        <span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
                        <b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
                        <b>PC:</b> <span class="badge badge-info">{{ $pcName}}</span>
                        <a href="{{url('/eci/pendingateciPDF')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
                        <a href="{{url('/eci/pendingateciEXL')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;

                        <b></b><a href="{{url('/eci/mis-officer/')}}"> <button type="button" id="Back" class="btn btn-primary">Back</button></a>

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
                                        <th>PC No & Name</th>
                                        <th>Candidate Name</th>
                                        <th>Party Name</th>
                                        <th>Last Date of Submission</th>
          <th>Date of Scrutiny Report Submission</th>
         <th>Date of Lodging A/C By Candidate</th>
         <th>Date of Sending to the CEO</th>
         <th>Date of Receipt By CEO</th>
    <th>Action</th>
                                    </tr>
                                </thead>
                                <?php $j = 0; ?>
                                @if(!empty($pendingateciCandlist))
                                @foreach($pendingateciCandlist as $candDetails)  
                                <?php
                                $pc = getpcbypcno($candDetails->ST_CODE, $candDetails->constituency_no);
                                $date = new DateTime($candDetails->created_at);
                                //echo $date->format('d.m.Y'); // 31.07.2012
                                $lodgingDate = $date->format('d-m-Y'); // 31-07-2012
                                $j++;
                                ?>
                                <tr>
                                    <td>@if(!empty($pc->PC_NO))  {{ $pc->PC_NAME}} @endif</td>
                                    <td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
                                    <td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
                                    <td>22-06-2019</td>
<td>@if(!empty($candDetails->finalized_date)) {{ date('d-m-Y',strtotime($candDetails->finalized_date))}}  @else {{ 'N/A'}} @endif</td>
<td>@if(!empty($candDetails->date_orginal_acct)) {{ date('d-m-Y',strtotime($candDetails->date_orginal_acct))}} @else {{ 'N/A'}} @endif</td>
<td>@if(!empty($candDetails->date_of_sending_deo)) {{  date('d-m-Y',strtotime($candDetails->date_of_sending_deo))}} @else {{ 'N/A'}} @endif</td>
<td>@if(!empty($candDetails->date_of_receipt) && ($candDetails->date_of_receipt !='0000-00-00')) {{ date('d-m-Y',strtotime($candDetails->date_of_receipt))}}  @else {{ 'N/A'}} @endif</td>

                                    <td>  @if(!empty($candDetails->cand_name))
                <a href="{{url('/')}}/eci/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary btn-sm width-75" target="_blank">Report</a> 
                @endif</td>
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
            </div>
        </div>
    </section>
</main>

@endsection


