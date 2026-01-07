@extends('admin.central.common.theme')
@section('title', 'Candidate and Counting Section')
@section('bradcome')
  <?php 
  $breadcrumbs = [];
  $breadcrumbs[] = [
    'href' => url('/mparty/ceo/partywise-reports'),
    'name' => 'List Of All Political Parties'
  ]; 
  ?>
@endsection
@section('content') 
 <?php $i=1; $url = URL::to("/");   ?>
<main role="main" class="inner cover mb-3">
   
<section>
  <div class="container">
     
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4>List Of All Parties</h4></div> 
            <div class="col-md-8">
               <p class="mb-0 text-right"><b>State Name:</b> 
                <span class="badge badge-info">{{$st_name}}</span> &nbsp;&nbsp; 
                  <b>State vernacular Language:</b> 
                  <span class="badge badge-info">{{$state_language}}</span>
               </p>
              </div>
            </div>
      </div>
  <div class=" card-header">
      <div class=" row">
   
  <div class="col-md-2 pull-left"><label>Select Party Type</label> </div>
  <div class="col-md-2 pull-left">
        <form name="frmparty" id="frmparty" method="POST"  action="" >
           {{ csrf_field() }}  
        <select name="party_type" id="party_type" onchange="this.form.submit();">
                  @foreach($mpartytype as $iterate)
                     @if($party_type==$iterate['id'])
                     <option value="{{$iterate['id']}}" selected="selected">{{$iterate['name']}}</option>
                     @else
                      <option value="{{$iterate['id']}}">{{$iterate['name']}}</option>
                     
                     @endif
                @endforeach
                </select>
              </form>
        </div>
    <div class="col-md-5  pull-right text-right">

@foreach($buttons as $button)
<span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download Excel" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
@endforeach
      
    </div> 
  </div>
</div>    
 <div class="table-responsive card-body">
      <div class="row">
      @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
        @endif
        @if (session('error_mes'))
           <div class="alert alert-danger"> {{session('error_mes') }}</div>
        @endif
         
  </div>
    @if(isset($record))  
    <table id="example1" class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
            <tr>
              <th>Sl. No.</th> 
              <th>Party Abbree</th> 
              <th>Party Name</th> 
              <th>Party vernacular</th>
            </tr>
        </thead>
        <tbody>
        <?php $i=1; $url = URL::to("/");   //dd($record);?>
      
      @foreach ($record as $key=>$list)
          <tr>
            <td>{{$i}}</td>
            <td>{{$list->party_abbre}}</td>
            <td>{{$list->party_name}}</td>
            <td>{{$list->party_vname}}</td>
          </tr>
           <?php $i++;?>
          @endforeach
        </tbody>
    </table>

  </div>
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
