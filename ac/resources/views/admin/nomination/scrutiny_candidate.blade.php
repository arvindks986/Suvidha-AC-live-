@extends('admin.layouts.ac.theme')
@section('content')
<style type="text/css">
  .loader {
   position: fixed;
   left: 50%;
   right: 50%;
   border: 16px solid #f3f3f3; /* Light grey */
   border-top: 16px solid #3498db; /* Blue */
   border-radius: 50%;
   width: 120px;
   height: 120px;
   animation: spin 2s linear infinite;
   z-index: 99999;
  }
      @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
    }
  </style>

  <div class="loader" style="display:none;"></div>
<section class="statistics color-grey pt-4 pb-2">
<div class="container-fluid">
  <div class="row">
  <div class="col">
   <div class="col-md-9 pull-left">
   <h4>{!! $heading_title !!}</h4>
 </div>
 <div class="col-md-3  pull-right">
         
              <button type="button" id="Cancel" class="btn btn-primary pull-right" onclick="window.history.back();">Back</button>
       </div> 
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


    
           <div class="table-responsive">



      <table class="table table-bordered " id="list-example">
           <thead>
            <tr> 
              
              <th>PC Number&Name</th>
              <th>S.No</th>
              <th>Candidate Name</th>
             
              <th>Party Name</th>
              <th>Party Symbol</th>
              <th>Status</th>
            </tr>

          </thead>
          <tbody id="oneTimetab">   
              @foreach($results as $result)
              <tr>
               
                <td>{{$result['pc_no_name']}}</td>
                <td>{{$result['index']}}</td>
                <td><a href="{!! $result['href'] !!}">{{$result['name']}}</a> </td>
                <td>{{$result['party_name']}}</td>
                <td>{{$result['party_symbol']}}</td>
                <td style="text-transform: capitalize;">
                {{$result['status']}}
                </td>
              </tr>

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

<script type="text/javascript">
  function go_to_href(href){
    window.location.href = href;
  }
</script>

@endsection