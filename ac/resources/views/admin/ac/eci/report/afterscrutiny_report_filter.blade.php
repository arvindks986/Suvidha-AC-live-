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
   <h4>After Scrutiny Nomination Status</h4>
  </div>

   <div class="col-md-3  pull-right text-right">
   
      <a href="{{url('/eci/afterscrutinyfilter_pdf')}}/{{base64_encode($phaseid)}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
   <a href="{{url('/eci/afterscrutinyfilter_excel')}}/{{base64_encode($phaseid)}}" class="btn btn-info" role="button">Excel Download</a> &nbsp;&nbsp;
    
  
   
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

<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
     <div class="page-contant">
     <div class="random-area">
  <br>

    <!--FILTER STARTS FROM HERE-->

      <div class="row">
          <div class="col-sm-2">
              
             <form method="post" action="{{url('/eci/afterscrutinyform')}}" id="afterscrutinyform">
                 {{ csrf_field() }}

                 <!--PHASE LIST DROPDOWN STARTS-->
                  
                   <select name="phaseid" id="phaseid" class="form-control"  >
            <option value="" class=>-- All Phases --</option>
              @foreach($getphase as $rowph)   
               
              <option <?php if(isset($phaseid) && $phaseid==$rowph->StatePHASE_NO ) echo "selected" ?> value="{{$rowph->StatePHASE_NO}}">Phase-{{$rowph->StatePHASE_NO}}</option>
              @endforeach
          </select> 
</div>
                   <div class="col-md-3">
                  
                  
                  <input type="submit" value="Filter" class="btn btn-primary report-btn">
                  </div> 
                   
              </form>
               

            


            
      </div>

           <div class="table-responsive">
      <table class="table table-bordered ">
           <thead>
            <tr> 
              <th rowspan="2"style="text-align: center;">Sl NO</th>
              <th rowspan="2"style="text-align: center;">State</th>
              <th rowspan="2"style="text-align: center;">Phase</th>
              <th rowspan="2" style="text-align: center;">Total Nomination</th>
               <th rowspan="2"style="text-align: center;">Online</th>
                <th rowspan="2"style="text-align: center;">Offline</th>
              <th colspan="5" style="text-align: center;">Payment Mode</th> 
            
            </tr>
            <tr>  
               
             
              <th style="text-align: center;">Online <br>Mode</th> 
              <th style="text-align: center;">Challan</th>
              <th style="text-align: center;">Cash </th>
              <!-- <th>Validly <br>Nominated Candidates</th> -->
              <th style="text-align: center;">Offline</th> 
            </tr> 
             
          </thead>
          <tbody id="oneTimetab">
          <?php $i=1; ?>   
              @foreach($results as $result)
                 
              <tr>
                <td style="text-align: center;"><b>{{ $i++ }}</b></td>
                <td style="text-align: left;">{{$result['label']}} </td>
                  <td style="text-align: right;">{{$result['phase']}} </td>
                
                <td style="text-align: right;">
               {{count($result['nomination'])}}
                
                </td>
                <td style="text-align: right;">
                {{count($result['online'])}}
                </td>
                <td style="text-align: right;">{{count($result['offline'])}}</td>
                <td style="text-align: right;">{{$result['payment_online']}}</td>
                <td style="text-align: right;">{{$result['payment_challan']}}</td>
                <td style="text-align: right;">{{$result['payment_cash']}}</td>
                <td style="text-align: right;">{{$result['offline_cash']}}</td>



                
                 
                


 
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