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
   <h4>State Wise Nomination Count</h4>
  </div>

   <div class="col-md-3  pull-right text-right">
   
      <a href="{{url('/eci/nomination_count_pdf')}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
   <!-- <a href="{{url('/eci/nomination_count_excel')}}" class="btn btn-info" role="button">Excel Download</a> &nbsp;&nbsp; -->
    

   
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

    <div class="row">
          <div class="col-sm-2">
              
             <form method="post" action="{{url('/eci/countnominationform')}}" id="countnominationform">
                 {{ csrf_field() }}

                 <!--PHASE LIST DROPDOWN STARTS-->
                  
                   <select name="phaseid" id="phaseid" class="form-control"  >
            <option value="" class=>-- All Phases --</option>
              @foreach($getphase as $rowph)   
               @if($rowph->PHASE_NO!=10)
              <option <?php if(isset($phaseid) && $phaseid==$rowph->PHASE_NO ) echo "selected" ?> value="{{$rowph->PHASE_NO}}">Phase-{{$rowph->PHASE_NO}}</option>
              @endif
              @endforeach
          </select> 
        </div>
                   <div class="col-md-3">
                  
                  
                  <input type="submit" value="Filter" class="btn btn-primary report-btn">
                  </div> 
                   
              </form>
               
       
      </div>

    <br></br>


           <div class="table-responsive">
      <table class="table table-bordered ">
           <thead>
            <tr> 
              <th rowspan="2" style="text-align: center;">SL NO </th>
              <th rowspan="2" style="text-align: center;">State </th>
               <th rowspan="2" style="text-align: center;">Number of <br>AC(s)</br> </th>
              <th rowspan="2" style="text-align: center;">Last Date<br>of Nomination</br> </th>
              <th colspan="2" style="text-align: center;">Nomination</th>
            
              
            
            </tr>
            <tr>  
               
              <th style="text-align: center;">Online</th>
              <th style="text-align: center;">Offline</th>
              
              <!-- <th>Validly <br>Nominated Candidates</th> -->
                 
            </tr> 
          </thead>
          <tbody id="oneTimetab">
          <?php $i=1; $tipslastmonth=0;?>   
              @foreach($results as $result)
                 
              <?php $cals=0;  

               $onliecount=count($result['online_nomination']);
               $totalcount=count($result['online_nomination']) + count($result['offline_nomination']);
               //$onliecount=150;
              // $tipslastmonth == 0 ? 0 : round( (float)$onliecount/$totalcount, 4);
               if(!empty($totalcount)){
              $cal=round( (float)$onliecount/$totalcount, 4)*100;
          }else{

            $cal=0;
          }
              //$newDateFormat = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $result['LDT_IS_NOM'])->format('d-m-Y');
              //$calmultiple=$cals * 100;
              //dd($calmultiple);
             // $cal= number_format((float)$calmultiple, 2, '.', '');
    //dd($tipslastmonth);  
                    //$cals=$cal/100;
              ?>
              <tr>
                <td style="text-align: center;"><b>{{ $i++ }}</b></td>
                <td style="text-align: left;"><b>{{$result['label']}}</b> </td>
                <td style="text-align: right;"><b>{{$result['ac_count']}}</b> </td>
                  <td style="text-align: right;">{{$result['LDT_IS_NOM']}} </td>
                
                <td style="text-align:right"><b>
               {{count($result['online_nomination'])}}
                </b>
                </td>
                <td style="text-align:right"><b>
                {{count($result['offline_nomination'])}}</b>
                </td>
               
                
<!---->

 
              </tr>
              @endforeach
              <tr class="totalClass">
                <td></td>
            <td style="text-align:center"><b>Total</b></td>
            
             <td style="text-align:right">{{$TotalAC}}</td>
            <td></td>
            <td style="text-align:right">{{$onlineCount}}</td>
            <td style="text-align:right">{{$offlineCount}}</td>
            </tr>

            <?php $total_nomination= $onlineCount + $offlineCount; ?>
            <tr class="">
            <td colspan="6" style="text-align:right"><b><h6>Total Nominations:   {{ $total_nomination }}</h6></b></td>
            
            </tr>

  
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