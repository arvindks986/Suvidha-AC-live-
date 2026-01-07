@extends('admin.layouts.ac.theme')
@section('bradcome', 'Print Receipt')
@section('content')  
<?php   
          $url = URL::to("/"); $j=0;
        if($caddata->cand_name == $caddata->nomination_submittedby){
          $applied_by = '(candidate)';
        }else {
          $applied_by = '(proposer)';
        }  
    ?>

<link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('theme/css/custom.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('theme/css/custom-dark.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('appoinment/css/font-awesome.min.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('appoinment/fonts.css') }} " type="text/css">

  <div class="container">
    <div class="step-wrap mt-4 text-center">
      <ul>
        <li class="step-success"><b>&#10004;</b><span>Verify Nomination Details</span></li>
        <li class="step-success"><b>&#10004;</b><span>Decision by RO (Part IV)</span></li>
        <li class="step-success"><b>&#10004;</b><span>Genrate Receipt (Part VI)</span></li>
        <li class="step-current"><b>&#10004;</b><span>Print Receipt</span></li>
      </div>
    </div>

  <main role="main" class="inner cover mb-3">
  <section class="mt-3">
  <div class="container">
  <div class="row">
            
  <div class="card mt-3">
                <div class="card-header">
                <div class="row">
                 <div class="col"> <h3>Print Receipt </h3> </div> 
          <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st_name}}</span> &nbsp;&nbsp; <b class="bolt">AC Name:</b> 
            <span class="badge badge-info">{{$ac_name}}</span>&nbsp;&nbsp;  
            </p></div>
         
                </div>
                </div>
    
    <div class="card-border">  
  <div class="nomination-fieldset">
            
              <div class="nomination-form-heading text-center">
                  <span class="fillupbold">PART VI </span><br/>
                  <b>Receipt for Nomination Paper and Notice of Scrutiny </b> <br>
                  (To be handed over to the person presenting the Nomination Paper) 
                </div>
              <div class="nomination-parts box recognised">
              <div class="nomination-detail m-4" style="font-size:15px;">
               <div class="one-param">
                 <p>Serial No. of nomination paper <span class="fillupbold dashed">{{$caddata->nomination_papersrno}}</span></p>    
                  <p> The nomination paper of  <span class="fillupbold dashed">{{strtoupper($caddata->cand_name)}} </span> a candidate for election from the 
                    @if(!empty($ac))  <span class="fillupbold dashed">{{strtoupper($ac->AC_NAME) }} </span>  Assembly constituency @endif 
                    was delivered to me at my office  at  <span class="fillupbold dashed">{{$caddata->rosubmit_time}} </span>
                     (hour) on  <span class="fillupbold dashed">{{ date('d-m-Y', strtotime($caddata->rosubmit_date)) }} </span>  (date) by <span class="dashed">{{ $caddata->nomination_submittedby }}</span> {{$applied_by}}. All nomination papers will be taken up for scrutiny at  <span class="fillupbold dashed">{{$caddata->scrutiny_time}} </span>   (hour) on  <span class="fillupbold dashed">{{$caddata->scrutiny_date}} </span> (date) at  
                     <span class="fillupbold dashed">{{strtoupper($caddata->place)}} </span>  Place.  </p>
               </div>

                 
                </div><!--Nomination Details-->
                 
                 
                 <div class="btns-actn p-3" style="border-top: 1px solid #d7d7d7;">
                  <div class="row">
                    <div class="col">
                      <a class="btn btn-secondary" href="{{ url('roac/finalreceipt?nom_id='.encrypt_string($caddata->nom_id)) }}">Back</a>
                     </div>
                    <div class="col text-right">
                      <strong>Receipt</strong>
                      <i class="fa fa-download" aria-hidden="true"><a  href="{{url('/roac/nomination-receipt-print?nom_id='.$caddata->nom_id)}}" download="download" title="Download PDF">English</a></i> /
                      <i class="fa fa-download" aria-hidden="true"><a  href="{{url('/roac/nomination-receipt-print/Hindi?nom_id='.$caddata->nom_id)}}"  download="download" title="Download PDF">Hindi</a></i>
                    <a href="{{ url('roac/listallapplicant') }}" class="btn dark-purple-btn">Proceed For Other Phycial Verifiction</a>
                        </div>
                  </div>
                  </div>
              </div><!--Nomination Parts-->
            </div>
    </div>
  
  
  </div>
  </div>
</div>
  </section>
  </main>
 
@endsection
 