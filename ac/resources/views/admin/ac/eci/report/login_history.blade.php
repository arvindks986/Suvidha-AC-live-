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
    table th td tr{
      text-align: center;
    }
  </style>

  <div class="loader" style="display:none;"></div>


<section class="statistics color-grey pt-4 pb-2">
<div class="container-fluid">
  <div class="row">
  <div class="col-md-9 pull-left">
   <h5>RO Officer Login History  <span style="color:blue"><?php echo date('d-m-Y') ; ?></span></h5>
  </div>

   <div class="col-md-3  pull-right text-right">
   
      <a href="{{url('/eci/loginrecord_pdf')}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
   <a href="{{url('/eci/loginrecord_excel')}}" class="btn btn-info" role="button">Excel Download</a> &nbsp;&nbsp;
    

   
    @if(isset($back_href) && $back_href != '')
    <span class="report-btn" id="back-button"><a class="btn btn-primary" href="{{ $back_href }}" title="Back">Back</a></span>
    @endif
     </div> 

  </div>
</div>  
</section>

@if(isset($filter_buttons) && count($filter_buttons)>0)
<section class="statistics pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
      
      </div>
    </div>
  </div>
</section>
@endif

<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
     <div class="page-contant">
     <div class="random-area">
  <br>

    

           <div class="table-responsive">
      <table class="table table-bordered ">
           <thead>
            <tr> 
              <th style="text-align: center;">SL NO </th>
              <th style="text-align: center;">State </th>
              <th  style="text-align: center;">Total Login</th>
              <th  style="text-align: center;">Nomination Received</th>
              <!-- <th rowspan="2" style="text-align: center;">Percentage</th> -->
              
            
            </tr>
            
          </thead>
          <tbody id="oneTimetab">
          <?php $i=1; ?>   
              @foreach($results as $result)
              <tr>
                <td style="text-align: center;"><b>{{ $i++ }}</b></td>
                <td><b>{{$result['label']}}</b> </td>
                
                <td style="text-align:right"><b>
               {{$result['login_history']}}
                </b>
                </td>
                <td style="text-align:right"><b>
               {{$result['nomination_recv']}}
                </b>
                </td>
                
                
<!---->

 
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



@endsection