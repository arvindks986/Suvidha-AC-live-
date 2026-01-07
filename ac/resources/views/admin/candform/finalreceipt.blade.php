@extends('admin.layouts.ac.theme')
@section('bradcome', 'Print Receipt')
@section('content')
<?php    $url = URL::to("/"); $j=0;
    if($caddata->scrutiny_time!='') $scrutiny_time=$caddata->scrutiny_time;
              elseif(old('scrutiny_time')!='')  $scrutiny_time=old('scrutiny_time');
              else $scrutiny_time='23:59:59';
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
      <li class="step-current"><b>&#10004;</b><span>Genrate Receipt(Part VI)</span></li>
      <li class=""><b>&#10004;</b><span>Print Receipt</span></li>
  </div>
</div>
<main role="main" class="inner cover mb-3">
  <section class="mt-3">
    <div class="container">
      <div class="row">

        <div class="card mt-3">
          <div class=" card-header">
            <div class=" row align-items-center">
              <div class="col">
                <h3>Genrate Receipt (Part VI)</h3>
              </div>
              <div class="col">
                <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span
                    class="badge badge-info">{{$st_name}}</span> &nbsp;&nbsp; <b class="bolt">AC Name:</b>
                  <span class="badge badge-info">{{$ac_name}}</span>&nbsp;&nbsp;
                </p>
              </div>

            </div>
          </div>

          @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
          @endif
          @if (session('error_mes'))
          <div class="alert alert-danger"> {{session('error_mes') }}</div>
          @endif
          @if (session('success'))
          <div class="alert alert-success"> {{session('success') }}</div>
          @endif
          @if(!empty($errors->first()))
          <div class="alert alert-danger"> <span>{{ $errors->first() }}</span> </div>
          @endif


          <div class="card-border">

            <form method="POST" action="{{url('roac/print-receipt') }}" onsubmit="return ">
              {{ csrf_field() }}
              <input type="hidden" name="candidate_id" value="{{$caddata->candidate_id}}">
              <input type="hidden" name="nom_id" value="{{$caddata->nom_id}}">
              <div class="nomination-fieldset">

                <div class="nomination-form-heading text-center">
                  <span class="fillupbold">PART VI </span><br />
                  <b>Receipt for Nomination Paper and Notice of Scrutiny </b> <br>
                  (To be handed over to the person presenting the Nomination Paper)
                </div>

                <div class="nomination-parts box recognised">
                  <div class="nomination-detail m-4" style="font-size:15px;">
                    <div class="one-param">
                      <p>
                        Serial No. of nomination paper <span
                          class="fillupbold dashed">{{$caddata->nomination_papersrno}} </span>
                      </p>
                      <p>
                        The nomination paper of <span class="fillupbold dashed">{{$caddata->cand_name}} </span> a
                        candidate for election from the <span class="fillupbold dashed">{{$ac_name}} </span> Assembly
                        constituency.

                        was delivered to me at my office at <span class="fillupbold dashed">{{$caddata->rosubmit_time}}
                        </span> (hour) on <span
                          class="fillupbold dashed">{{ date('d-m-Y', strtotime($caddata->rosubmit_date)) }} </span>
                        (date) by <span class="dashed">{{ $caddata->nomination_submittedby }}</span> {{$applied_by}}.
                        All nomination papers will be taken up for scrutiny at
                        <span><input type="text" name="scrutiny_time" class="nomination-field-1 form-control dashed"
                            id="scrutiny_time" value="{{$scrutiny_time}}" /> </span>
                        @if ($errors->has('scrutiny_time'))
                        <span style="color:red;"><strong>{{ $errors->first('scrutiny_time') }}</strong></span>
                        @endif

                        (hour) on <input type="text" name="scrutiny_date" id="scrutiny_date"
                          class="nomination-field-2 form-control dashed"
                          value="{{ date('d-m-Y', strtotime($scrutiny_date)) }}" placeholder="scrutiny Date"
                          readonly="readonly" />
                        @if ($errors->has('scrutiny_date'))
                        <span style="color:red;"><strong>{{ $errors->first('scrutiny_date') }}</strong></span>
                        @endif

                        (date) at <input type="text" name="place" class="nomination-field-2 form-control dashed"
                          readonly="readonly" value="{{$ac_name}}" /> Place.
                      </p>
                    </div>
                  </div>
                  <!--Nomination Details-->
                  <input type='hidden' name="fdate" class="nomination-field-4" value="{{ date('d-m-Y') }}"
                    readonly="readonly" />

                  <div class="btns-actn p-3" style="border-top: 1px solid #d7d7d7;">
                    <div class="row">
                      <div class="col"><a class="btn btn-secondary font-big"
                          href="{{ url('roac/decisionbyro?nom_id='.encrypt_string($caddata->nom_id)) }}">Back</a>
                      </div>
                      <div class="col text-right"> <button class="btn dark-purple-btn font-big" type="submit">Save &
                          Print Receipt</button></div>
                    </div>

                  </div>
                </div>

              </div>
            </form>
          </div>
        </div>


      </div>
    </div>
  </section>
</main>

@endsection
@section('script')
<script type="text/javascript" src="{{ asset('admintheme/js/jquery-ui.js') }}"></script>

<script>
  $(document).ready(function(){  
   
  jQuery('#scrutiny_time').datetimepicker({
           format:'HH:mm:ss',
          //  minDate: new Date()
          });
});
</script>
@endsection