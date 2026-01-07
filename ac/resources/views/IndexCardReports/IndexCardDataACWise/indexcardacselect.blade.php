@extends('IndexCardReports.layouts.IndexReportTheme')
@section('title', 'AC Wise Report')

@section('content')
<style>
    .reportsection {
        text-align: center;
    }

    td {
        font-size: 14px !important;
    }

    .headerreport h2 {
        background: #005aab;
        color: #ffff;
        padding: 10px;
        text-transform: capitalize;
        border-radius: 10px;
        font-size: 22px;
    }

    .bordertestreport {
        text-align: center;
        border: 1px solid #ddd;
        padding: 30px;
        background-image: url(../images/grid.png);
        background: #005aab08;
        background-repeat: repeat;
    }

    .headerreport h4 {
        text-transform: capitalize;
        font-size: 18px;
        font-family: 'poppinsregular';
    }

    table.table.table-bordered.table-responsive.tablecenterreport {
        text-align: center;
    }

    .headerreport {
        margin: auto;
    }

    .tablecenterreport td {
        font-size: 15px;
    }

    span.devil {
        float: right;
        padding: 11px;
        font-size: 17px;
        position: relative;
        right: 16%;
        border: 2px dotted #4da1ed;
        background: #4da1edab;
        color: #fff;
    }

    div#example_wrapper {
        position: relative;
        top: 40px;
    }

    .pagination {
        margin: 10px 0px;
    }

    div#example_paginate {
        top: -16px;
        position: relative;
    }

    .rightfl {
        display: inline-block;
        float: right;
        word-spacing: -1px;
    }

    .contituency {
        text-align: left;
        padding: 4px;
        font-size: 17px;
        font-weight: bold;
        display: inline-block;
        padding: 0px 60px;
    }

    th {
        background: #f0587e;
        color: #fff !important;
        text-align: center;
    }

    tr:nth-child(even) {
        background: #8e99ab29;
    }

    td.dev {
        text-align: center;
    }

    img.img-responsivesreport {
        height: 130px;
        margin: auto;
        position: relative;
        display: block;
    }

    .contituency span {
        padding: 6px 46px !important;
        background: #17a2b8;
        color: #fff;
        text-decoration: none !important;
    }
</style>
<?php //$st=getstatebystatecode($user_data->st_code);   ?>
<section class="">
    <div class="container">
        <div class="row">
            <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                    <div class=" row">
                        <div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(AC Wise Reports)</h4></div> 

                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <!-- Content goes Here -->
                        <div class="col-sm-12 text-center">
                            <h5>Select AC</h5><br><br>
                        </div>
						
	<form id="generate_report_id"  method="post" action="getindexcarddata">
  @csrf

          <div class="form-group col-md-12 text-center"> 
          
            <select name="ac_no" id="ac_no" class="form-control col-md-6">
            <option value="">Select AC</option>
                 @foreach ($acList as $acs)					
					<option value="{{ $acs->AC_NO }}">{{ $acs->AC_NO }} - {{ $acs->AC_NAME }} </option> 
				@endforeach
                    
            </select>
          </div>
		  
		  <div class="form-group col-md-12 text-center"> 
			<input type="submit" name="submit" class="btn btn-primary getdata">
            
          </div>
         
        </form><br><br>
                       
                        <!-- Content ends Here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection