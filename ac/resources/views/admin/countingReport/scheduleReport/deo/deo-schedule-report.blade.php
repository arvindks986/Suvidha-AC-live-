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
            <div class="col-md-3  pull-right text-right">
                <span class="report-btn" id="export-csv-btn"><a class="btn btn-primary" href="{{url('acdeo/schedule-report-excel')}}/{{$urlexcel}}" title="Download Excel" target="_blank">Export Excel</a></span>
                <span class="report-btn" id="export-pdf-btn"><a class="btn btn-primary" href="{{url('acdeo/schedule-report-pdf')}}/{{$urlpdf}}" title="Download PDF" target="_blank">Export PDF</a></span>
            </div>

        </div>
    </div>
</section>
<section class="statistics pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
	        <span class="pull-right" style="margin-right: 10px;">
            <span><b>AC:</b></span>
            <span class="badge badge-info">@if($ac_no!=0) {{getacbyacno($state,$ac_no)->AC_NAME}} @else All Selected @endif</span>
            </span>
      </div>
    </div>
  </div>
</section>

<section class="dashboard-header section-padding">
    <div class="container-fluid">
        <form  action="{{url('acdeo/schedule-report')}}" class="row" method="POST" >
		{{csrf_field()}}
			<input type="hidden" name="state_code" id="state_code" value="{{$state}}"> 
            <div class="form-group col-md-3">
                <label>Select AC <span style="color:red">*</span></label>
                <select name="ac_no" id="ac_no" class="form-control" required>
                    <option value="">Select AC</option>
					<option value="0" @if(isset($ac_no) && $ac_no==0) selected @endif>Select All</option>
					@foreach($m_ac as $data)
                    <option value="{{$data->AC_NO}}" @if(isset($ac_no) && $ac_no==$data->AC_NO) selected @endif>{{$data->AC_NO}} -{{$data->AC_NAME}} </option>
					@endforeach
                </select>
            </div>
			 <div class="form-group col-md-3">
                <button type="submit" name="search" id="submit-report" class="btn btn-success" style="margin-top:31px;">Search</button>
            </div>
        </form>
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
                        <table id="example" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th> S.No </th>
                                    <th> AC No & Name</th>
                                    <th> Total Scheduled Rounds</th>
                                    <th> Total Completed Rounds</th>
                                    <th> Total Pending Rounds</th>
                                </tr>
                            </thead>
                            <tbody>
							@if(count($result)>0)
							 @php $i=1; @endphp
							 @foreach($result as $data)
							<?php 
 							 $totalScheduled=$data->S_ROUND; 
							 $completedRound=completeRound($data->STATE,$data->AC_NO)
							 ?>
								<tr>
                                    <td><span>{{$i}}</span></td>
                                    <td><span>{{$data->AC_NO}} -{{getacbyacno($data->STATE,$data->AC_NO)->AC_NAME}}</span></td>
									<td><span>{{$totalScheduled}}</span></td>
									<td><span>{{$completedRound}}</span></td>
                                    <td><span><?php echo $totalScheduled-$completedRound; ?></span></td>
                                </tr>
								@php $i++ @endphp
								@endforeach
								@else
								<tr >
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