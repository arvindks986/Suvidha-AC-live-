@extends('admin.layouts.ac.theme')
@section('title', 'Candidate & Counting')
@section('bradcome', 'Round Wise Results Publish')
@section('content')
 <?php  $url = URL::to("/"); ?>
 
<main role="main" class="inner cover mb-3">
 <section>
  <div class="container mt-5">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
               <div class="col"> <h4 class="mr-auto">Round Wise Results Publish</h4>  </div>   
          <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> 
                        <span class="badge badge-info">{{$st_name}}</span> &nbsp;&nbsp; <b class="bolt">AC Name:</b> 
                        <span class="badge badge-info">{{$ac_name}}</span>&nbsp;&nbsp;  </p></div>
                 </div>
                </div>
       
    <div class="card-body">
	<div class="col-sm-10">
          @if (session('success_mes'))
                  <div class="alert alert-success"> {{session('success_mes') }}</div>
              @endif
              @if (session('error_mes'))
                  <div class="alert alert-danger"> {{session('error_mes') }}</div>
              @endif
            @if (session('error_mes1'))
                  <div class="alert alert-danger"> {{session('error_mes1') }}</div>
              @endif
            @if(!empty($errors->first()))
              <div class="alert alert-danger"> <span>{{ $errors->first() }}</span> </div>
             @endif
          
         @if(Session::has('success_admin'))
             <div class="alert alert-success">
                <strong> {{ nl2br(Session::get('success_admin')) }}</strong> 
              </div>
          @endif

         
    </div>
     <input type="button" value="Back" class="btn btn-primary mr-auto pull-right" onclick="location.href = '{{$url}}/roac/counting/polling-station-wisevote-entry';"> 
   @if(!empty($results))     
  <table   class="table table-striped table-bordered" style="width:100%">
        <thead><tr>
                <th>Sr. No</th>
                <th>Round Number</th>
                <th>Results</th> 
                <th>Details</th>
                </tr>
        </thead>
      <tbody>
          <?php  $j=0;   ?>
             @foreach($results as $list)  
            <?php $j++;  $encround=encrypt_string($list->round_id);  $eround=base64_encode($list->round_id); ?>
              <tr><td>{{$j}}</td> 
                  <td>{{$list->round_id}} </td> 
                  <td>@if($list->results==0)  <input type="button" value="Preview & Publish " placeholder="" class="btn btn-success submit-button" onclick="location.href ='{{$url}}/roac/counting/result-publish?round_id={{$encround}}';">

                   <!--  <input type="button" value="Results Not Publish" placeholder="" class="btn btn-success submit-button" onclick="location.href ='{{$url}}/roac/counting/round-wise-calculate-vote?round_id={{$encround}}';">  -->@endif 
                      @if($list->results==1)  Results Published  @endif
                  </td> 
                  <td> <input type="button" value="Details of Report" placeholder="" class="btn btn-success submit-button" onclick="location.href ='{{$url}}/roac/counting/tabulating-trend-results?round_id={{$eround}}';">  </td>
              </tr>  
            @endforeach  
      </tbody> 
  </table> 
   @else
                 <div class="norecords"><i class="fa fa-ban"></i><h4>No Records Found</h4></div>
        @endif  
             
 </div>
</div>
</div>
</div>
</section>
</main>
 
@endsection

 
