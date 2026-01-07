@extends('admin.layouts.ac.dashboard-theme') 
@section('content')
<style type="text/css">
    .loader {
        position: fixed;
        left: 50%;
        right: 50%;
        border: 16px solid #f3f3f3;
        /* Light grey */
        border-top: 16px solid #3498db;
        /* Blue */
        border-radius: 50%;
        width: 120px;
        height: 120px;
        animation: spin 2s linear infinite;
        z-index: 99999;
    }
    
    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
    
    #acViewBody a {
        text-decoration: none !important;
        color: #000 !important;
        cursor: default !important;
    }
    
    #acViewBody a:hover {
        text-decoration: none !important;
        color: #000 !important;
        cursor: default !important;
    }
</style>
<div class="loader" style="display:none;"></div>
<section class="statistics color-grey pt-4 pb-2">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-9 pull-left">
                <h4>{!! $heading_title !!}</h4>
            </div> 	
        </div>
    </div>
</section>
									
<div class="container-fluid">
    <!-- Start parent-wrap div -->
    <div class="parent-wrap">
        <!-- Start child-area Div -->
        <div class="child-area">
            <div class="page-contant">
                <div class="random-area">
                    <br>
                    <div class="table-responsive">
                        <table  class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th> S.No </th>
                                    <th> State </th>
									<th> Total AC</th>
									<th> Total Upload</th>
									<th> Total Pending Upload</th>
                                </tr>
                            </thead>
                            <tbody>	
 							@php $totalAC=0;
							     $totalUploadAC=0;
								 $totalNotUploadAC=0;
							@endphp
							@if(count($result)>0)
							 @php $i=1; @endphp
							 @foreach($result as $data)	
                              <?php $uplodedTotal=uploadedACTotal($data->STATE);
									$notUploadedTotal=notUploadedACTotal($data->STATE);
                              ?>							 
								<tr>
                                    <td><span>{{$i}}</span></td>
                                    <td><span>{{getstatebystatecode($data->STATE)->ST_NAME}}</span></td>
									<td><span>{{$data->TOTALAC}}</span></td>
									<td><span>{{$uplodedTotal}}</span> </td>
									<td><span>{{$data->TOTALAC-$uplodedTotal}}</span> </td>
								</tr>
								<?php $totalAC=$totalAC+$data->TOTALAC;
								      $totalUploadAC=$totalUploadAC+$uplodedTotal;
								?>
								@php $i++ @endphp
								@endforeach
								<tr>
                                    <td colspan="2"><span><strong>TOTAL</strong></span></td>
                                    <td><span><strong><?php echo $totalAC ?><strong></span> </td>
									<td><span><strong><?php echo $totalUploadAC; ?><strong></span> </td>
									<td><span><strong><?php echo $totalAC-$totalUploadAC; ?><strong></span> </td>
								</tr>
								@else
								<tr>
                                    <td colspan="8" style="text-align:center">--No Record Found--</td>
                                </tr>
								@endif
                            </tbody>
                        </table>
						
                    </div>
                    <!-- End Of  table responsive -->
                </div>
            </div>
            <!-- End Of random-area Div -->

        </div>
        <!-- End OF page-contant Div -->
    </div>
</div>
<!-- End Of parent-wrap Div -->
</div>	
@endsection