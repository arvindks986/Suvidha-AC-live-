@extends('admin.layouts.ac.theme')
@section('title', 'Candidate and Counting Section')
@section('bradcome', 'List of candidate finalize')
@section('content') 
 <main role="main" class="inner cover mb-3">
   
<section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> All Nomination Finalize</h4></div> 
              <div class="col">
                <p class="mb-0 text-right"><b>State Name:</b> 
                      <span class="badge badge-info">{{$st_name}}</span> &nbsp;&nbsp;  
                  <b>Dist Name:</b> 
                      <span class="badge badge-info">{{$dist_name}}</span> &nbsp;&nbsp;  
                </p>
              </div>
            </div>
      </div>
  
 <div class="card-body">
         
    <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
            <tr>
              <th>Sl. No.</th>
              <th>Constituency Name</th>
              <th>List of Contesting Candidates </th>
              <th>CONST. Type</th>
              <th>Finalized</th>
              <th>Date</th></tr>
        </thead>
        <tbody>
        <?php $i=1; $url = URL::to("/");    ?>
      
      @foreach ($lists as $key=>$list)
        
        
          <tr><td>{{$i}}</td>
           <td>{{$list->const_no}}-{{$list->AC_NAME}}</td>
           <td> 
            <button type="button" class="btn btn-danger" onclick="location.href = '{{$url}}/acdeo/download-form7a-english?st_code={{$list->st_code}}&ac_no={{$list->const_no}}';">Download Form7A in English</button> 
        
            <!--<button type="button" class="btn btn-danger" onclick="location.href = '{{$url}}/acdeo/download-form7a-vernacular?st_code={{$list->st_code}}&ac_no={{$list->const_no}}';">Download Form7A in Vernacular</button> 
			<button type="button" class="btn btn-danger" onclick="location.href = '{{$url}}/acdeo/download-form7a-bilingual?st_code={{$list->st_code}}&ac_no={{$list->const_no}}';">Download Form7A in Bilingual</button> -->

            <!-- <button type="button" class="btn btn-danger" onclick="location.href = '{{$url}}/acdeo/download-contesting-candidate/{{encrypt_string($list->const_no)}}';">Download & Verify </button> -->  </td>
           <td>{{$list->const_type}}</td>
           <td>@if($list->finalized_ac==1) Yes @else NO @endif</td>
           
           <td>@if($list->finalize_date!='') Finalize D:- {{date("d-m-Y",strtotime($list->finalize_date))}} @endif <br>
            @if($list->definalize_date!='') Definalize D:- {{date("d-m-Y",strtotime($list->definalize_date))}} @endif</td> 
             
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
 