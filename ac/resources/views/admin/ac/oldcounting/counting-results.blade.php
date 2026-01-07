@extends('admin.layouts.ac.theme')
@section('title', 'Candidate & Counting')
@section('bradcome', 'Results Declaration Process')
@section('content')
 <?php  $st=getstatebystatecode($ele_details->ST_CODE);  
         $ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO); 
         
    ?>
 
<main role="main" class="inner cover mb-3">
 <section>
  <div class="container mt-5">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"> @if($winn_data->status==0)<h6 class="mr-auto">Results Declaration Process</h6> @else
                  <h6 class="mr-auto">Results   Declared</h6> @endif
				  </div> 
                  <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> 
                        <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b class="bolt">AC Name:</b> 
                        <span class="badge badge-info">{{$ac->AC_NAME}}</span>&nbsp;&nbsp;  </p></div>
                 </div>
                </div>
       
    <div class="card-body"> 
    @if (session('success_mes'))
                  <div class="alert alert-success"> {{session('success_mes') }}</div>
              @endif
              @if (session('error_mes'))
                  <div class="alert alert-danger"> {{session('error_mes') }}</div>
              @endif 
  <table   class="table table-striped table-bordered" style="width:100%">
        <thead><tr><th>Sr. No</th><th>Candidate Name</th><th>Party</th> <th>Evm Votes</th>
                 <th>Postal Votes</th><th>Total Votes</th> </tr>
        </thead>
      <tbody>
           <?php $j=0;   ?>
              @if(!empty($master_data))
            @foreach($master_data as $md)  
            <?php $j++;  ?>
          <tr><td>{{$j}}</td> <td>{{$md->candidate_name}}</td> 
                <td>{{$md->party_name}}</td> <td>{{$md->total_vote-$md->postalballot_vote}}</td><td>{{ $md->postalballot_vote}}</td>
                 <td>{{$md->total_vote}}  </td> </tr>

            @endforeach 
            @endif 
             </tbody> 
            </table> 
             <form class="form-horizontal" id="election_form" method="POST"  action="{{url('/roac/counting/results-declaration') }}" > 
              <input type="hidden" name="leading_id" readonly="readonly" value="@if(isset($winn_data)) {{$winn_data->leading_id}} @endif">
            {{ csrf_field() }}    
                 <?php  $url = URL::to("/");  ?>
              <div class="form-group float-right">  
                @if($val==0)
               @if(isset($winn_data)) @if($winn_data->status==0)
                 <input type="submit" value="Results Declaration " class="btn btn-primary">
                @endif  @endif 
                @endif 
                 <input type="button" value="Back" class="btn btn-primary" onclick="location.href = '{{$url}}/roac/counting/prepare-counting';">
              </div>
             
      </form>
 </div>
</div>
</div></div>
</section>
</main>
 
@endsection
 