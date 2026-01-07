@extends('admin.layouts.pc.theme')
@section('title', 'Officer Login Report')
@section('content')
 <?php  $st=getstatebystatecode($user_data->st_code); 
        $pc=getpcbypcno($user_data->st_code,$user_data->pc_no); 
        // dd($pc);
    ?>
  <style type="text/css">
      th, td { white-space: nowrap;}
        <!-- .dataTables_wrapper .row:nth-child(2) .col-sm-12 { overflow: scroll;} -->
        
        html {
              overflow: scroll;
              overflow-x: hidden;
             }
              ::-webkit-scrollbar {    width: 0px; 
              background: transparent;  /* optional: just make scrollbar invisible */
              }

              ::-webkit-scrollbar-thumb {
                background: #ff9800;
                }
              div.dataTables_wrapper {margin:0 auto;} 
  </style>
<main role="main" class="inner cover mb-3">
<section class="mt-5">
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h2 class="mr-auto">Officer Detail Reports</h2></div> 
             <div class="col-sm-8"><p class="mb-0 text-right">
              <b>State Name:</b> 
              <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; 
              <b>PC Name:</b><span class="badge badge-info">{{$pc->PC_NAME}}</span>&nbsp;&nbsp; 
              <b></b> <span class="badge badge-info"></span>&nbsp;&nbsp; 
              <b><a href="{{url('ropc/login-detail-pdf')}}" class="btn btn-info" role="button">Export Pdf</a> &nbsp;&nbsp;</b> <span class="badge badge-info"></span>
              
              <b><a href="{{url('ropc/pcrologin-detail-excel')}}" class="btn btn-info" role="button">Export excel</a> &nbsp;&nbsp;</b> <span class="badge badge-info"></span>
              </p></div>
            </div>
            </div>
<div class="card-body">  
  <div class="table-responsive">
  <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <tr>
          <th>Sr. No</th>
          <th>Officer Name</th>
          <th>Designation</th>
          <th>User Id</th>
          <th>Password</th>
        </tr>
        </thead>
        <tbody>
            <?php $j=0;  ?>
              @if(!empty($officerDetails))
            @foreach($officerDetails as $officerDetailsList)  
              <?php
               $j++; 
                 ?>
            
              <tr>
               <td>{{$j}}</td> 
               <td>@if(!empty($officerDetailsList->name)) {{$officerDetailsList->name}} @endif</td>
               <td>@if(!empty($officerDetailsList->designation)) {{$officerDetailsList->designation}} @endif</td>
               <td>@if(!empty($officerDetailsList->officername)) {{$officerDetailsList->officername}} @endif</td>
               <td style="background-color:white;"><font color="black">@if(!empty($officerDetailsList->officername)) {{'demo@123'}} @endif</font>
</td>
              </tr>
            @endforeach 
            @endif 
             </tbody>
            </table>
           </div> <!-- end reponcive-->
          </div>
        </div>
  </div>
  </div>
  </section>
  </main>

@endsection