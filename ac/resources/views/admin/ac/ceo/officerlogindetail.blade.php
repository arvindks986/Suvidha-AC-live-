@extends('admin.layouts.ac.report-theme')
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
                 <div class="col"><h2 class="mr-auto">Officer Details Report</h2></div> 
             <div class="col"><p class="mb-0 text-right">
              <b>State Name:</b> 
              <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; 
              <b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
              <b><a href="{{url('pcceo/login-detail-pdf')}}" class="btn btn-info" role="button">Export Pdf</a> &nbsp;&nbsp;</b> <span class="badge badge-info"></span>
              &nbsp;&nbsp; 
              <b><a href="{{url('pcceo/login-detail-excel')}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;</b> <span class="badge badge-info"></span>
              </p></div>
            </div>
            </div>
<div class="card-body">  
  <div class="table-responsive">
  <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <tr>
         <th>Serial No</th>
           <th>User Id</th>
           <th>Desigation</th> 
           <th>Officer Name</th> <th>Officer Level</th>
           <th>Mobile Number</th><th>E-Mail</th>
           <th>AC</th>
           <th>PC</th>
          <th>Action</th>
        </tr>
        </thead>
        <tbody>
            <?php $j=0;  ?>
              @if(!empty($officerDetails))
            @foreach($officerDetails as $officerDetailsList) 

              <?php
        $pcDetails=getpcbypcno($officerDetailsList->st_code,$officerDetailsList->pc_no); 
         $acDetails =getacbyacno($officerDetailsList->st_code,$officerDetailsList->ac_no);
         $st=getstatebystatecode($officerDetailsList->st_code);
               $j++; 
                 ?>
            
              <tr>
               <td>{{$j}}</td>  
            <td >{{ $officerDetailsList->officername}}</td>
            <td >{{ $officerDetailsList->designation }}</td>
            <td >{{ $officerDetailsList->name }}</td>
            <td >{{ $officerDetailsList->officerlevel}}</td>
            <td >{{ $officerDetailsList->Phone_no}}</td><td >{{ $officerDetailsList->email}}</td>
            <td >@if(isset($pcDetails)) {{$officerDetailsList->pc_no.'-'.$pcDetails->PC_NAME}}@endif</td>
            <td >@if(isset($acDetails)) {{$officerDetailsList->ac_no.'-'.$acDetails->AC_NAME}}@endif</td>
            <td > <a class="btn btn-info btn-lg" href="{{ url('/pcceo/edituser') }}/{{ $officerDetailsList->id  }}">Edit</a></td>
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