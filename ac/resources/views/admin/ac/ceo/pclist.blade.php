@extends('admin.layouts.ac.report-theme')
@section('title', 'Candidate and Counting Section')
@section('bradcome', 'List Of PC With Candidate Details')
@section('content') 
  <?php  $st=getstatebystatecode($user_data->st_code);  
  ?> 
<style type="text/css">
      th, td { white-space: nowrap;}
        .dataTables_wrapper .row:nth-child(2) .col-sm-12 { overflow: scroll;}
        
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
   
<section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> List Of PC With Candidate Details</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp; 
              <a href="{{url('pcceo/ceo-pclist-pdf')}}" class="btn btn-info" role="button">Export PDF</a> &nbsp;&nbsp;
              <a href="{{url('pcceo/ceo-pclist-excel')}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
              <!--<a href="{{url('pcceo/ceo-candidate-summary')}}" class="btn btn-info" role="button">Export Summary</a> &nbsp;&nbsp;</p>-->
              </p>
              </div>
            </div>
      </div>
    <?php $i=0;    $totalrg=0; $totalwg=0; $totalaccg=0;  $totalg=0; ?>
 <div class="card-body">  
    <table   class="table table-striped table-bordered" style="width:100%">
         <thead>
        <tr>
          <th>Serial No</th> 
          <th>PC Number&Name</th>
          <th>Accepted</th> 
          <th>Rejected</th>  
          <th>Withdrawn</th> 
          <!--<th>PC Name Hindi</th> -->
          <th>Total</th> 
        </tr>
        </thead>
        <tbody>
        <?php $count = 1; ?>
         @foreach($allPcList as $pcList)
          <?php  
               $totalwg=$totalwg+$pcList['Withdrawn']; 
               $totalrg=$totalrg+$pcList['rejected']; 
               $totalaccg=$totalaccg+$pcList['accepted'];
               $totalg=$totalg+$pcList['total'];          
       ?>  
          <tr>
            <td>{{$pcList['srno']}}</td>  
            <td ><a target="" href="{{url('/pcceo/candidatelist/'.$pcList['PC_NO'].'/')}}">{{ $pcList['pc_name']}}</a></td>
            <td> {{$pcList['accepted']}}</td>
            <td> {{$pcList['rejected']}}</td>
            <td> {{$pcList['Withdrawn']}}</td>
            <td>{{$pcList['total']}}
            </td>
          </tr>
          <?php $count++ ?>
          @endforeach
          <tr> 
                  <td>Total:- </td>
                  <td> </td> 
                  <td>{{$totalaccg}}</td>
                  <td>{{$totalrg}}</td>
                  <td>{{$totalwg}}</td>
                  <td>{{$totalg}}</td> </tr>
        </tbody>
    </table>
    </div>
    </div>
  </div>
  </div>
  </section>
  </main>
 
@endsection
