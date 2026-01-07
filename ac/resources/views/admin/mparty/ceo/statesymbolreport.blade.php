@extends('admin.central.common.theme')
@section('title', 'Candidate and Counting Section')
@section('bradcome')
  <?php 
  $breadcrumbs = [];
  $breadcrumbs[] = [
    'href' => url('/mparty/ceo/symbol-reports'),
    'name' => 'List Of Symbol'
  ]; 
  ?>
@endsection
@section('content') 
    
 <main role="main" class="inner cover mb-3">
   
<section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> List of Symbol</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>State Name:</b> 
                              <span class="badge badge-info">{{$st_name}}</span> &nbsp;&nbsp; 
                              <b>State vernacular Language:</b> 
                              <span class="badge badge-info">{{$state_language}}</span>
               </p>
              </div>
            </div>
    <div class=" row">
    <div class="col  pull-right text-right">
      @foreach($buttons as $button)
      <span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download Excel" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
      @endforeach
    </div>  
    </div> 
      </div>
  
 <div class="table-responsive card-body">
       
    <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
            <tr>
              <th>Sl. No.</th> 
              <th>Symbol  Name in English</th> 
              <!-- <th>Symbol  Name in Hindi</th> -->
              <th>Symbol  Name in vernacular</th>
            </tr>
        </thead>
        <tbody>
        <?php $i=1; $url = URL::to("/");   //dd($record);?>
      
      @foreach ($record as $key=>$list)
         
          <tr><td>{{$i}}</td>
            <td>{{$list->symbol_name}}   </td>
            <!-- <td>{{$list->symbol_hname}}   </td> -->
            <td>{{$list->symbol_vname}}</td>
          </tr>
           <?php $i++;?>
          @endforeach
        </tbody>
    </table>
    </div>
    </div>
  </div>
  </div>
  </section>
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

@endsection
 