@extends('admin.layouts.ac.dashboard-theme')
@section('content')
<main role="main" class="inner cover mb-3">
   
<section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> List Of Party</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp; <a href="{{url('/eci/EciPartyDataPdf')}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
              <a href="{{url('/eci/EciPartyDataExcel')}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
			   <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>
              </p>
              </div>
            </div>
      </div>
   
 <div class="card-body">  
    <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
         <tr>
          <th>Serial No</th>
          <th>Party Abbreviation</th> 
          <th>Party Name</th>
          <th>Party Type</th> 
        </tr>
        </thead>
        <tbody>
          @php $count = 1; @endphp 
         @forelse ($AllPartyList as $key=>$listdata)
          <tr>
            <td>{{$count}}</td>  
            <td>{{ $listdata->PARTYABBRE }}</td>
            <td> {{ $listdata->PARTYNAME }}</td>
            <td> {{ $listdata->PARTYTYPE }}</td>
          </tr>
         <?php $count++; ?>  
           @empty
                <tr>
                  <td colspan="4">No Data Found For Party</td>                 
              </tr>
          @endforelse
        </tbody>
    </table>
    </div>
    </div>
  </div>
  </div>
  </section>
  </main>

@endsection


