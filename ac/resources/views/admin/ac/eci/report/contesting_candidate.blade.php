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
   <h4>Contesting Candidate Report</h4>
  </div>

   <div class="col-md-3  pull-right text-right">
   
      <a href="{{url('/eci/contestingNominationcand_pdf')}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
   <!-- <a href="{{url('/eci/contestingNominationcand_excel')}}" class="btn btn-info" role="button">Excel Download</a> &nbsp;&nbsp; -->
    
  
   
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
 <div class="card-header">
      <div class="row">
          <div class="col-sm-2">
              
            <form method="post" action="{{url('/eci/contestingNominationcandform')}}" id="contestingNominationcandform">
                 {{ csrf_field() }}

                 <!--PHASE LIST DROPDOWN STARTS-->
                  
                   <select name="phaseid" id="phaseid" class="form-control"  >
            <option value="" class=>-- All Phases --</option>
              @foreach($getphase as $rowph)   
                   @if($rowph->PHASE_NO!=10)
              <option <?php if(isset($_POST['phaseid']) && $_POST['phaseid']==$rowph->PHASE_NO ) echo "selected" ?> value="{{$rowph->PHASE_NO}}">Phase-{{$rowph->PHASE_NO}}</option>
              @endif
              @endforeach
          </select> 
                  </div>
                   <div class="col-md-3">
                  
                   <!--PHASE LIST DROPDOWN ENDS-->

                  <input type="submit" value="Filter" class="btn btn-primary report-btn">
                  <!-- <input type="reset" value="Reset Filter" name="Cancel" class="btn"> -->
                    </div> 
              </form>
               

          


            
      </div>
</div>

 <!--FILTER ENDS HERE-->

           <div class="table-responsive">
      <table class="table table-bordered ">
           <thead>
            <tr> 
              <th  rowspan="2" style="text-align: center;">SL NO </th>
              <th  rowspan="2" style="text-align: center;">State </th>
              <!-- <th  style="text-align: center;">Phase </th> -->
              <th   rowspan="2" style="text-align: center;">Contesting Candidate</th>
              <th colspan="3" style="text-align: center;">Age Groups</th>
              <th rowspan="2"  style="text-align: center;">Male</th>
              <th rowspan="2"  style="text-align: center;">Female</th>
<?php if($tgcountis > 0 ) { ?>

              <th rowspan="2"  style="text-align: center;">TG</th>
            <?php } ?>
              <th  rowspan="2"  style="text-align: center;">ST/SC</br></th>
              <th rowspan="2"  style="text-align: center;">Criminal <br>Antecedents</br></th>
              <!-- <th rowspan="2" style="text-align: center;">Percentage</th> -->
              
            
            </tr>
            <tr>
              <th>25-40</th>
              <th>41-60</th>
              <th>61 To Above</th>
             
               
            </tr>
             
          </thead>
          <tbody id="oneTimetab">
          <?php $i=1; ?>   
              @foreach($results as $result)
                 
              
              <tr>
                <td style="text-align: center;"><b>{{ $i++ }}</b></td>
                <td style="text-align: left;">{{$result['label']}} </td>
                <!-- <td style="text-align: right;">{{$result['phase']}} </td> -->
                
                <td style="text-align:right"><b>
               {{count($result['nomination'])}}
                </b>
                </td>
                   <td style="text-align:right">
               {{$result['Agefrom25']}}
                
                </td>
                   <td style="text-align:right">
               {{  $result['Agefrom40']}}
                
                </td>
                   <td style="text-align:right">
               {{ $result['Agefrom60']}}
                
                </td>
                <td style="text-align:right"><b>
               {{count($result['male'])}}
                </b>
                </td>
                <td style="text-align:right"><b>
               {{count($result['female'])}}
                </b>
                </td>
                <?php if($tgcountis > 0 ) { ?>
                <td style="text-align:right"><b>
               {{count($result['tg'])}}
                </b>
                </td>
              <?php } ?>
                <td style="text-align:right"><b>
               {{count($result['category'])}}
                </b>
                </td>
                 <td style="text-align:right"><b>
               {{count($result['cadetail'])}}
                </b>
                </td>
               
                
<!---->

 
              </tr>
              @endforeach
              <tr class="totalClass">
           
            <td></td>
 <td style="text-align:center"><b>Total</b></td>
            
            <td style="text-align:right">{{$TotalContesting}}</td>
            <td style="text-align:right">{{$TotalAge_from_25}}</td>
            <td style="text-align:right">{{$TotalAge_from_40}}</td>
            <td style="text-align:right">{{$TotalAge_from_60}}</td>
            <td style="text-align:right">{{$TotalMale}}</td>
            <td style="text-align:right">{{$TotalFemale}}</td>
            <?php if($tgcountis > 0 ) { ?>
            <td style="text-align:right">{{$TotalTg}}</td>
          <?php } ?>
            <td style="text-align:right">{{$TotalCategory}}</td>
            <td style="text-align:right">{{$TotalCA}}</td>
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