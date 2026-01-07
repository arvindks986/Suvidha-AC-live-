@extends('admin.layouts.ac.report-theme')
@section('content')
<main role="main" class="inner cover mb-3">

    <!--FILTER STARTS FROM HERE-->
 <div class=" card-header">
      <div class=" row">
            <div class="col">
               @if(Session::has('ScheduleList'))
              <form method="post" action="{{url('/eci/EciNominationFinalizedByPhaseIdForm')}}" id="EciNominationFinalizedByPhaseIdForm">
                 {{ csrf_field() }}

                 <!--PHASE LIST DROPDOWN STARTS-->
                  <select name="phaseid" id="phaseid">
                  <option value="">Select Phase</option>
                  @php  $i = 1; @endphp
                  @foreach (Session::get('ScheduleList') as $Schedule_List ))

                  @if (old('ScheduleList') == $Schedule_List->StatePHASE_NO)
                        <option value="{{ $Schedule_List->StatePHASE_NO }}" selected>Phase {{$i}}</option>
                  @else
                        <option value="{{ $Schedule_List->StatePHASE_NO }}">Phase {{$i}}</option>
                  @endif
                  
                   @php  $i++;  @endphp
                  @endforeach                 

                  @if ($errors->has('ScheduleList'))
                  <span class="help-block">
                      <strong class="user">{{ $errors->first('ScheduleList') }}</strong>
                  </span>
                  @endif
                  
                  </select>
                   <!--PHASE LIST DROPDOWN ENDS-->

                  <input type="submit" value="Filter" class="btn btn-primary">
                  <input type="reset" value="Reset Filter" name="Cancel" class="btn">
              </form>
               @endif

            </div> 

             <div class="col"> <h4> 
    @if($phaseid != '') Finalized ACs In Election Phase &nbsp; {{$phaseid}}</h4>  @else  @endif</h4> 
          </div>
            
      </div>
</div>

 <!--FILTER ENDS HERE-->


<section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> List Of Finalized ACs In States Phase Wise {{$user_data->placename}}</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp;<a href="{{url('/eci/EciNominationFinalizedByPhaseIdPdf')}}/{{base64_encode($phaseid)}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp; 
              <a href="{{url('/eci/EciNominationFinalizedByPhaseIdExcel')}}/{{base64_encode($phaseid)}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
              <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>
             
              </p>
              </div>
            </div>
      </div>
   
 <div class="card-body">  
    <table class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
         <tr>
          <th>Serial No</th>
          <th>State Name</th> 
          <th>No of Total ACs</th> 
          <th>Finalized ACs</th> 
        </tr>
        </thead>
        <tbody>
        @php  
        $count = 1; 
        $TotalAc = 0; 
        $TotalFinalized = 0;

        @endphp
         @forelse ($EciNominationFinalizedByPhaseId as $key=>$listdata)

          @php 

         $TotalAc += $listdata->total_ac;

         $TotalFinalized += $listdata->finalized_ac;

        @endphp
          <tr>
            <td>{{ $count }}</td>
            <td> <a href="{{url('/eci/EciNominationFinalizedByStatePhaseId')}}/{{base64_encode($listdata->sid)}}/{{base64_encode($listdata->ST_CODE)}}">{{$listdata->state_name }}</a></td>
            <td><a href="{{url('/eci/EciNominationFinalizedByStatePhaseId')}}/{{base64_encode($listdata->sid)}}/{{base64_encode($listdata->ST_CODE)}}">{{$listdata->total_ac }}</a></td>
            <td><a href="{{url('/eci/EciNominationFinalizedByStatePhaseId')}}/{{base64_encode($listdata->sid)}}/{{base64_encode($listdata->ST_CODE)}}">{{$listdata->finalized_ac }}</a></td>
          </tr>
       
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Finalized AC In This Phase</td>                 
              </tr>
          @endforelse
          <tr class="totalClass"><td><b>Total</b></td><td></td><td><b>{{ $TotalAc }}</b></td><td><b>{{ $TotalFinalized }}</b></td></tr>
        </tbody>
    </table>
    </div>
    </div>
  </div>
  </div>
  </section>
  </main>

@endsection



