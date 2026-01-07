@extends('admin.layouts.ac.theme')
@section('content')

 <main class="mb-auto">
     
      <!--main content start-->
       
 <main role="main" class="inner cover mb-3">


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

<section class="mt-4">
  <div class="container-fluid">
  
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
     <div class=" card-header">
    <div class=" row">
      <div class="col-md-4"><h4>{!! $heading_title !!}</h4></div> 
      <div class="col"><p class="mb-0 text-right">
      </p><div class="" style="width:100%; margin:0 auto;"></div>
      &nbsp;&nbsp;  
      <p></p>
      </div><!--end col-->
    </div> <!--end row-->
    </div><!--end card-header -->
      
    <div class="card-body">  
      




      <table id="list-table1" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <tr>
          <th>State</th>
          <th>Username</th> 
          <th>Designation</th> 
          <th>Email</th> 
          <th>Mobile</th>
          <th>Action</th> 
        </tr>
        </thead>
        <tbody id="oneTimetab"> 
          @if(!empty($results))
           <?php $i = 1; ?>
           @foreach($results as $result)
            <tr id="row_{{$i}}">
              <td>{{$result['state_name']}}</td>
              <td>{{$result['officername']}}</td>
              <td>{{$result['designation']}}</td>
              <td>{{$result['email']}}</td>
              <td>{{$result['mobile']}}</td>
              <td>
       
                <a class="btn btn-primary reset_password_button" href="{!! url('eci/officer/de-finalize-ballot/'.$result['hash_id']) !!}">De-finalize Ballot</a>
              
                <a class="btn btn-primary reset_password_button" href="{!! url('eci/officer/de-finalize-result/'.$result['hash_id']) !!}">De-finalize Result</a>

              </td>
            </tr>
            <?php $i++; ?>
            @endforeach
   
            @endif 
        </tbody>
        </table>










      </div><!-- end row-->
          </div> <!-- end COL-->
        </div>

    
    
  </div>
</section>

</main>
      <!--main content end-->
   
 </main>





@endsection

@section('script')

@if (session('success_mes'))
<script type="text/javascript">
 success_messages("{{session('success_mes') }}");
 </script>
@endif
@if (session('error_mes'))
  <script type="text/javascript">
  error_messages("{{session('error_mes') }}");
</script>
@endif
</script>
@endsection