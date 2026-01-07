@extends('admin.layouts.ac.theme')
@section('bradcome')
	<li><span class="icon icon-beaker"> </span> List of Nominated Candidate</li>
@endsection
@section('content')
<main role="main" class="inner cover mb-3">

<section class="statistics color-grey pt-4 pb-2">
<div class="container-fluid">
  <div class="row">
  <div class="col-md-7 pull-left">
   <h4>{!! $heading_title !!}</h4>
  </div>

   <div class="col-md-5  pull-right text-right">

@foreach($buttons as $button)
<span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download Excel" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
@endforeach
      
    </div> 

  </div>
</div>  
</section>

@if(isset($filter_buttons) && count($filter_buttons)>0)
<section class="statistics pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        @foreach($filter_buttons as $button)
            <?php $but = explode(':',$button); ?>
            <span class="pull-right" style="margin-right: 10px;">
            <span><b>{!! $but[0] !!}:</b></span>
            <span class="badge badge-info">{!! $but[1] !!}</span>

            </span>
            
        @endforeach
      </div>
    </div>
  </div>
</section>
@endif

@include('admin/common/form-filter')





    
<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
     <div class="page-contant">
     <div class="random-area">
  <br>

    

           <div class="table-responsive">
      <table class="table table-bordered " id="my-list-table">
           <thead>
            <tr>  
              <th>S.No</th>
              <th>Distrcit</th>
           <th>AC No</th>
              <th>AC Name</th>
              <th>Candidate Name</th>
              <th>Gender</th>
              <th>Total Nomination</th>
			  <th>Party</th> 
			  <th>Symbol</th> 
        <th>Is Criminal</th> 
              <th>All Status</th>
              <th>Final Status</th> 
            </tr> 
          </thead>
          <tbody id="oneTimetab">   
            <?php $i=1; ?>
              @foreach($results as $result)
              <tr>
                <td>{!! $i !!}</td>
                <td>{!! $result['dist_name'] !!}</td>
                 <td>{!! $result['ac_no'] !!}</td>
                <td>{!! $result['ac_name'] !!}</td>
                <td>{!! $result['name'] !!}</td>
                <td>{!! $result['gender'] !!}</td>
                <td>{!! $result['total_nomination'] !!}</td>
				<td>{!! $result['party'] !!}</td>
				<td>{!! $result['symbol'] !!}</td>
        <td>{!! $result['criminal_inced']==1?"Yes":"NO" !!}</td>
                <td>{!! $result['status'] !!}</td>
                <td>{!! $result['final_status'] !!}</td>
                
              </tr>
              <?php $i++; ?>
              @endforeach



            
          </tbody>
           </table>
         </div><!-- End Of  table responsive -->  
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