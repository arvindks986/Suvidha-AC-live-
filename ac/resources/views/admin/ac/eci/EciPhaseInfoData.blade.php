@extends('admin.layouts.ac.report-theme')
@section('content')
<main role="main" class="inner cover mb-3">

<!--FILTER STARTS FROM HERE-->
 <div class=" card-header">
      <div class=" row">
            <div class="col">
               @if(Session::has('ScheduleList'))
              <form method="post" action="{{url('/eci/EciPhaseInfoDataCandWiseForm')}}" id="EciPhaseInfoDataCandWiseForm">
                 {{ csrf_field() }}

                 <!--PHASE LIST DROPDOWN STARTS-->
                  <select name="phaseid" id="phaseid">
                  <option value="">Select Phase</option>
                  @php  $i = 1; @endphp
                  @foreach (Session::get('ScheduleList') as $Schedule_List ))

                  @if (old('ScheduleList') == $Schedule_List->StatePHASE_NO)
                        <option value="{{ $Schedule_List->StatePHASE_NO }}" selected>Phase- {{$i}}</option>
                  @else
                        <option value="{{ $Schedule_List->StatePHASE_NO }}">Phase- {{$i}}</option>
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


            
      </div>
</div>

 <!--FILTER ENDS HERE-->
   
<section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> List Of Election Valid Nomination {{$user_data->placename}}</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp; 
			  <a href="{{url('/eci/EciPhaseInfoDataPdf')}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
			   <a href="{{url('/eci/EciPhaseInfoDataExcel')}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;

              <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>
             
              </p>
              </div>
            </div>
      </div>
   
 <div class="card-body">  
    <table class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
         <tr>
          <!-- <th>Serial No</th> -->
         <th>Serial No</th>
         
          <th>State/Uts</th>
          <th>  Phase </th>
          <th>Total Nominations Filed</th> 
          <th>National Parties</th> 
          <th>State Parties</th> 
          <th>Other Parties</th> 
          <th>Independent</th> 
          <th>Male</th> 
          <th>Female</th> 
		  <th>Others</th> 
          <th>Total Valid Nominations</th> 
        </tr>
        </thead>
        <tbody>
         @php  
        $count = 1; 

        $TotalNomination = 0; 
        $TotalNational = 0;
        $TotalState = 0;
        $TotalOther= 0;
        $TotalIndependent = 0;
        $TotalMale = 0;
        $TotalFemale = 0;
		$TotalOthers = 0;
        $TotalValidNomination=0;


        @endphp

         @forelse ($EciPhaseInfoData as $key=>$listdata)

         @php 

         $TotalNomination             +=   $listdata->TOTAL_NOMINATION;
         $TotalNational               +=   $listdata->NATIONAL;
         $TotalState                  +=   $listdata->STATE;
         $TotalOther                  +=   $listdata->OTHER;
         $TotalIndependent            +=   $listdata->INDEPENDENT;
         $TotalMale                   +=   $listdata->male;
         $TotalFemale                 +=   $listdata->female;
		 $TotalOthers                 +=   $listdata->others;
         $TotalValidNomination        +=   $listdata->total;

        @endphp


          <tr>
             <td>{{ $count }}</td>
            <td><a href="{{url('/eci/EciNominationStateWiseReport?state=')}}{{base64_encode($listdata->ST_CODE)}}">{{$listdata->ST_NAME }}</a></td> 
            <td>Phase-{{$listdata->StatePHASE_NO}}</td>
            <td>{{$listdata->TOTAL_NOMINATION }}</td>
            <td>{{$listdata->NATIONAL }}</td>
            <td>{{$listdata->STATE }}</td>
            <td>{{$listdata->OTHER }}</td>
            <td>{{$listdata->INDEPENDENT }}</td>
            <td>{{$listdata->male }}</td>
            <td>{{$listdata->female }}</td>
			<td>{{$listdata->others }}</td>
            <td><b>{{$listdata->total }}</b></td>
          </tr>
       
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Valid Nomination Data</td>                 
              </tr>
          @endforelse
           <tr class="totalClass">
            <td ><b>Total</b></td>
            <td></td>
            <td></td>
            <td><b>{{$TotalNomination}}</b></td>
            <td><b>{{$TotalNational}}</b></td>
            <td><b>{{$TotalState}}</b></td>
            <td><b>{{$TotalOther}}</b></td>
            <td><b>{{$TotalIndependent}}</b></td>
            <td><b>{{$TotalMale}}</b></td>
            <td><b>{{$TotalFemale}}</b></td>
			<td><b>{{$TotalOthers}}</b></td>
            <td><b>{{$TotalValidNomination}}</b></td>
            
          </tr>
        </tbody>
    </table>
    </div>
    </div>
  </div>
  </div>
  </section>
  </main>

@endsection



