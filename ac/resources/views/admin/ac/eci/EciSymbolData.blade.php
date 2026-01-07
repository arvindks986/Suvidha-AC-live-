@extends('admin.layouts.ac.dashboard-theme')
@section('content')
<main role="main" class="inner cover mb-3">
   
<section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> List Of Symbol</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp; <a href="{{url('/eci/EciSymbolDataPdf')}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
              <a href="{{url('/eci/EciSymbolDataExcel')}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
			   <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>
              </p>
              </div>
            </div>
      </div>
   
 <div class="card-body">  
    <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
         <tr>
          <th>Symbol Number</th>
          <th>Symbol Name</th> 
        </tr>
        </thead>
        <tbody>
        
         @forelse ($AllSymbolList as $key=>$listdata)
          <tr>
            
            <td>{{ $listdata->SYMBOL_NO }}</td>
            <td> {{ $listdata->SYMBOL_DES }}</td>
            
          </tr>
         
           @empty
                <tr>
                  <td colspan="4">No Data Found For Symbol</td>                 
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


