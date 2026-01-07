@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'List of All online MLC Nomination')
@section('content')
<?php   $url = URL::to("/");  ?>
<main role="main" class="inner cover mb-3">
 
<section class="statistics color-grey pt-4 pb-2">
<div class="container-fluid">
  <div class="row">
  <div class="col-md-7 pull-left">
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
        @if(isset($results) and ($results)) 
      <div class="table-responsive">
      <table class="table table-bordered " id="my-list-table">
           <thead>
            <tr>  
              <th>Sl.No</th>
              <th>Nomination No.</th>
              <th>Name</th>
              <th>Name In Hindi</th>
              <th>Father's Name</th>
              <th>Date of Apply</th>
              <th>Mobile</th>
              <th>Category</th>
              <th>Pan Number</th>
              <th>Party</th>
              <th>Status</th>
              <th>Action</th>
            </tr> 
          </thead>
          <tbody id="oneTimetab">   
            <?php $i=1;    //dd($results); ?>
              @foreach($results as $result)
              <?php $party=getpartybyid($result['party_id']); ?>
              <tr>  
        
                <td>{!! $i !!}</td>
                <td>{{$result['nomination_no']}}</td> <td>{{$result['name']}}</td> 
                <td>{{$result['hname']}}</td> 
                <td>{{$result['father_name']}}</td> 
                <td>{{date("d-M-Y",strtotime($result['apply_date']))}}</td> 
                <td>{{$result['mobile']}}</td> 
                <td>{{$result['category']}}</td> 
                <td>{{$result['pan_number']}} </td>
                <td>@if(isset($party)){{$party->PARTYNAME}} @endif</td>
                <td>@if($result['finalize']==0) In-Progress @else Finalize by Candidate @endif</td>
                <td>
                   <!-- <a href=" {{$url.'/mlc/ro/candidateinformation?nom_id='.encrypt_string($result['nomination_no'])}}" class="btn btn-primary">View Details</a> <br> -->
                   @if($result['affidavit']!='')
                   <a href="{{$url.'/'.$result['affidavit']}}" download="download" class="btn button btn-primary">Download Affidavit</a> 
                   @endif
                   <br>
                   @if($result['application_path']!='')
                   <a href="{{$url.'/'.$result['application_path']}}" download="download" class="btn button btn-primary">Download Application</a> 
                   @endif
                </td>
              </tr>
              <?php $i++; ?>
              @endforeach



            
          </tbody>
           </table>
         </div><!-- End Of  table responsive -->  
            @else
               <div class="norecords"><i class="fa fa-ban"></i><h4>No Records Found</h4></div>
            @endif
      </div><!-- End Of intra-table Div -->   
        
         
      </div><!-- End Of random-area Div -->
      
    </div><!-- End OF page-contant Div -->
    </div>      
  </div><!-- End Of parent-wrap Div -->
  </div> 
   
@endsection

@section("script")
<script type="text/javascript">
  $(document).ready(function () {
    if($('#my-list-table').length>0){
      $('#my-list-table').DataTable({
        "pageLength": 500,
        "aaSorting": []
      });
    }
  });
</script>
@endsection