@extends('admin.layouts.ac.theme')
@section('title', 'Candidate & Counting')
@section('bradcome', 'Finalize EVM Rounds')
@section('content')
 <?php  $st=getstatebystatecode($ele_details->ST_CODE);  
         $ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO); 
         
    ?>
 
<main role="main" class="inner cover mb-3">

    <section class="mt-5">
  <div class="container">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
               
          <div class="col form-inline"><h6 class="mr-auto">Finalize EVM Rounds</h6><p class="mb-0 text-right"><b class="bolt">State Name:</b> 
            <span class="badge badge-info">{{$st->ST_NAME}}</span>  &nbsp;&nbsp; <b class="bolt">AC Name:</b> 
            <span class="badge badge-info">{{$ac->AC_NAME}}</span></p></div>
                </div>
                </div>
                <div class="card-body">  
 
  <div class="table-responsive">
  <table   class="table table-striped table-bordered table-hover" style="width:100%">
        <thead><tr><th>Sr. No</th><th>Candidate Name</th><th>Party</th>
                @for($k=1; $k<=$round_details->scheduled_round; $k++)
                  <th>Round{{$k}}</th>
                @endfor
                <th>Total Votes</th> </tr>
        </thead>
        <tbody>  
            <?php $j=0;  ?>
              @if(!empty($master_data))
            @foreach($master_data as $md)  
              <?php $j++;   
               
              ?>
             
              <tr><td>{{$j}}</td> <td>{{$md->candidate_name}} <br>{{$md->candidate_hname}}<b>Demo</b>   @if($md->nom_id==$winn_data->nomination_id) <b>(Winning) </b>@endif @if($md->nom_id==$winn_data->trail_nomination_id) <b>(Trailing)</b>  @endif</td>   
        <td>{{$md->party_name}} <br>{{$md->party_hname}}<b>Demo</b>  </td>
                 @for($k=1; $k<=$round_details->scheduled_round; $k++) 
                  <?php $field="round".$k ?>
                  <td>{{$md->$field}}</td>
                @endfor 
                
                <td>{{$md->total_vote}} </td> </tr>

            @endforeach 
            @endif 
             </tbody>
     
    </table>
    </div> <!-- end reponcive-->
    <form class="form-horizontal" id="election_form" method="POST"  action="{{url('roac/counting/finalize_evm_rounds') }}" >
            {{ csrf_field() }} 
       <input type="hidden" name="new_table" value="{{$new_table}}">
        
                 <?php  $url = URL::to("/");  ?>
              <div class="form-group float-right">  
                 <input type="submit" value="Finalize " class="btn btn-primary" onclick="return confirm('Do you really want to Finalize?');">
                 
                 <input type="button" value="Cancel" class="btn btn-primary" onclick="location.href = '{{$url}}/roac/counting/counting-data-entry';">
              </div>
             
      </form>
                </div>
              </div>
  
  
  </div>
  </div>
  </section>
  </main>

@endsection