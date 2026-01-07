@extends('admin.layouts.ac.theme')
@section('title', 'Candidate CA Report')
@section('bradcome', 'Candidate CA Report')
@section('content')
 <?php   
         $url = URL::to("/"); $j=0;
    ?>
 <style type="text/css">
     html {
              overflow: scroll;
              overflow-x: hidden;
             }
              ::-webkit-scrollbar {    width: 0px; 
              background: transparent;  /* optional: just make scrollbar invisible */
              }

              ::-webkit-scrollbar-thumb {
                background: #ff9800;
                }
              div.dataTables_wrapper {margin:0 auto;} 
              .file-upload{width: 80%;}
  </style>
  
  <main role="main" class="inner cover mb-3">
  <section class="mt-3">
  <div class="container">
<div class="row">
          
  <div class="card text-left" style="width:100%; margin:0 auto;">
    <div class=" card-header">
      <div class=" row">
        <div class="col"> <h4>Candidates  CA Report </h4> </div>          
      </div>
    </div>
  
       
    <div class="card-border">  
       <form class="form-horizontal" id="" method="post" action="{{url('eci/ca-candidate-list')}}">
      {{csrf_field()}}     
      <div class="row">
        <div class="col-md-12">
          <div class="row d-flex align-items-center ">
            <div class="col">
                <label for="phase" class="col-form-label">Phase </label> &nbsp; &nbsp;
                <select name="phase" id="phase" class="form-control"  onchange="javascript:get_state()" >
                  <option value="" class=>-- All Phases --</option>
                    @foreach($phase_list as $rowph)    
                    <option <?php if(isset($_POST['phase']) && $_POST['phase']==$rowph->StatePHASE_NO ) echo "selected" ?> value="{{$rowph->StatePHASE_NO}}">Phase-{{$rowph->StatePHASE_NO}}</option>
                    @endforeach
                </select>
                @if ($errors->has('state_id'))
                        <span style="color:red;">{{ $errors->first('state_id') }}</span>
                 @endif
                        <span id="errmsg" class="text-danger"></span> 
                </div> 

              <div class="col">
                <label for="state_id" class="col-form-label">State </label> &nbsp; &nbsp;
                <select name="state_id" id="state_id" class="form-control"  onchange="javascript:get_district()" >
                  <option value="" class=>-- All States --</option>
                    @foreach($state_list as $rows)    
                    <option <?php if(isset($_POST['state_id']) && $_POST['state_id']==$rows->ST_CODE ) echo "selected" ?> value="{{$rows->ST_CODE}}">{{$rows->ST_CODE}}-{{$rows->ST_NAME}}</option>
                    @endforeach
                </select>
                @if ($errors->has('state_id'))
                        <span style="color:red;">{{ $errors->first('state_id') }}</span>
                 @endif
                        <span id="errmsg" class="text-danger"></span> 
                </div> 
                <div class="col">
                <label for="district" class="col-form-label">District </label> &nbsp; &nbsp;
                <select name="district" id="district" class="form-control"  onchange="javascript:get_ac()" >
                  <option value="" class=>-- All Districts --</option>
                    @foreach($district_list as $rowd)    
                    <option <?php if(isset($_POST['district']) && $_POST['district']==$rowd->DIST_NO) echo "selected" ?> value="{{$rowd->DIST_NO}}">{{$rowd->DIST_NO}}-{{$rowd->DIST_NAME}}</option>
                    @endforeach
                </select>
                @if ($errors->has('district'))
                        <span style="color:red;">{{ $errors->first('district') }}</span>
                 @endif
                        <span id="errmsg" class="text-danger"></span> 
                </div> 
                <div class="col">
                <label for="state_id" class="col-form-label">AC </label> &nbsp; &nbsp;
                <select name="ac_id" id="ac_id" class="form-control">
                  <option value="" class=>-- All AC --</option>
                    @if($ac_list)
                      @foreach($ac_list as $rowac)    
                      <option <?php if(isset($_POST['ac_id']) && $_POST['ac_id']==$rowac->AC_NO ) echo "selected" ?> value="{{$rowac->AC_NO}}">{{$rowac->AC_NO}}-{{$rowac->AC_NAME}}</option>
                      @endforeach
                    @endif
                </select>
                @if ($errors->has('ac_id'))
                        <span style="color:red;">{{ $errors->first('ac_id') }}</span>
                 @endif
                        <span id="errmsg" class="text-danger"></span> 
                </div> 
                <div class="col">
                <label for="party_id" class="col-form-label">Party </label> &nbsp; &nbsp;
                <select name="party_id" id="party_id" class="form-control">
                  <option value="" class=>-- All Parties --</option>
                    @foreach($party_list as $rowp)    
                    <option <?php if(isset($_POST['party_id']) && $_POST['party_id']==$rowp->CCODE ) echo "selected" ?> value="{{$rowp->CCODE}}">{{$rowp->PARTYABBRE}}-{{$rowp->PARTYNAME}}</option>
                    @endforeach
                </select>
                @if ($errors->has('party_id'))
                        <span style="color:red;">{{ $errors->first('party_id') }}</span>
                 @endif
                        <span id="errmsg" class="text-danger"></span> 
                </div> 
                <div class="col">
                <label for="cand_type" class="col-form-label">Candidate Type </label> &nbsp; &nbsp;
                <select name="cand_type" id="cand_type" class="form-control">
                  <option value="">-- Select --</option>
                  <option value="1" <?php if(isset($_POST['cand_type']) && $_POST['cand_type']==1) echo "selected" ?> >With Criminal Antecedents</option>
                  <option value="2" <?php if(isset($_POST['cand_type']) && $_POST['cand_type']==2) echo "selected" ?> >Without Criminal Antecedents</option>
                </select>
                @if ($errors->has('cand_type'))
                        <span style="color:red;">{{ $errors->first('cand_type') }}</span>
                 @endif
                          <span id="errmsg" class="text-danger"></span> 
                </div>                     
            </div>          
          </div>

          </div>
          <div class="row">
        <div class="col-md-4">
          <div class="row d-flex align-items-center ">
            <div class="col">
                <label for="app_status" class="col-form-label">Status </label> &nbsp; &nbsp;
                <select name="app_status" id="app_status" class="form-control" style="text-transform: uppercase;">
                  <option value="">-- Select --</option>
                    @foreach($status_list as $rowst)    
                    <option <?php if(isset($_POST['app_status']) && $_POST['app_status']==$rowst->id ) echo "selected" ?> value="{{$rowst->id}}" >{{$rowst->status}}</option>
                    @endforeach
                    <option <?php if(isset($_POST['app_status']) && $_POST['app_status']==12 ) echo "selected" ?> value="12">Contesting</option>
                </select>
                @if ($errors->has('app_status'))
                        <span style="color:red;">{{ $errors->first('app_status') }}</span>
                 @endif
                          <span id="errmsg" class="text-danger"></span> 
                </div>  

              <div class="col">
                <label for="symbol_search" class="col-form-label">Symbol</label> &nbsp; &nbsp;
                <select name="symbol_search" id="symbol_search" class="form-control">
                  <option value="">-- Select --</option>
                  <option  <?php if(isset($_POST['symbol_search']) && $_POST['symbol_search']==1 ) echo "selected" ?> value="1">Allotted</option>
                  <option <?php if(isset($_POST['symbol_search']) && $_POST['symbol_search']==2 ) echo "selected" ?> value="2">Not Allotted</option>
                   <!-- <option value="3">Both</option> -->
                 
                </select>
               <!--  @if ($errors->has('cand_type'))
                        <span style="color:red;">{{ $errors->first('cand_type') }}</span>
                 @endif
                          <span id="errmsg" class="text-danger"></span>  -->
                </div>   














                <div class="col-md-1 p-0 m-0">
                  <button type="submit" id="candnomination" class="btn btn-primary custombtn">Search</button>
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
   
  <section class="mt-3">
  <div class="container">
  <div class="row">
    <div class="col-md-8"></div>
    <div class="col-md-1">
      <!-- Export PDF Start -->
       <form class="form-horizontal" id="" method="post" action="{{url('eci/ca-candidate-list-pdf')}}">
        {{csrf_field()}} 
        <select name="phase" id="phase" class="form-control"  style="display:none;">
            <option value="" class=>-- All Phases --</option>
              @foreach($phase_list as $rowph)   
               
              <option <?php if(isset($_POST['phase']) && $_POST['phase']==$rowph->StatePHASE_NO ) echo "selected" ?> value="{{$rowph->StatePHASE_NO}}">Phase-{{$rowph->StatePHASE_NO}}</option>
              @endforeach
          </select> 
        <select name="state_id" id="state_id" class="form-control" style="display:none;">
          <option value="" class=>-- All States --</option>
            @foreach($state_list as $rows)    
            <option <?php if(isset($_POST['state_id']) && $_POST['state_id']==$rows->ST_CODE ) echo "selected" ?> value="{{$rows->ST_CODE}}">{{$rows->ST_CODE}}-{{$rows->ST_NAME}}</option>
            @endforeach
        </select>
        <select name="district" id="district" class="form-control"  style="display:none;">
          <option value="" class=>-- All Districts --</option>
            @foreach($district_list as $rowd)    
            <option <?php if(isset($_POST['district']) && $_POST['district']==$rowd->DIST_NO) echo "selected" ?> value="{{$rowd->DIST_NO}}">{{$rowd->DIST_NO}}-{{$rowd->DIST_NAME}}</option>
            @endforeach
        </select>
        <select name="ac_id_report" id="ac_id_report" class="form-control"  style="display:none;">
          <option value="" class=>-- All AC --</option>
            @if($ac_list)
              @foreach($ac_list as $rowac)    
              <option <?php if(isset($_POST['ac_id']) && $_POST['ac_id']==$rowac->AC_NO ) echo "selected" ?> value="{{$rowac->AC_NO}}">{{$rowac->AC_NO}}-{{$rowac->AC_NAME}}</option>
              @endforeach
            @endif
        </select>
        <select name="party_id" id="party_id" class="form-control"  style="display:none;">
          <option value="" class=>-- All Parties --</option>
            @foreach($party_list as $rowp)    
            <option <?php if(isset($_POST['party_id']) && $_POST['party_id']==$rowp->CCODE ) echo "selected" ?> value="{{$rowp->CCODE}}">{{$rowp->PARTYABBRE}}-{{$rowp->PARTYNAME}}</option>
            @endforeach
        </select>
        <select name="cand_type" id="cand_type" class="form-control"  style="display:none;">
            <option value="">-- Select --</option>
            <option value="1" <?php if(isset($_POST['cand_type']) && $_POST['cand_type']==1) echo "selected" ?> >With Criminal Antecedents</option>
            <option value="2" <?php if(isset($_POST['cand_type']) && $_POST['cand_type']==2) echo "selected" ?> >Without Criminal Antecedents</option>
          </select>
          <select name="app_status" id="app_status" class="form-control" style="display:none;">
              <option value="">-- Select --</option>
                @foreach($status_list as $rowst)    
                <option <?php if(isset($_POST['app_status']) && $_POST['app_status']==$rowst->id ) echo "selected" ?> value="{{$rowst->id}}" >{{$rowst->status}}</option>
                @endforeach
                <option <?php if(isset($_POST['app_status']) && $_POST['app_status']==12 ) echo "selected" ?> value="12">Contesting</option>
            </select>


               <select name="symbol_search" id="symbol_search" class="form-control" style="display:none;">
                  <option value="">-- Select --</option>
                  <option  <?php if(isset($_POST['symbol_search']) && $_POST['symbol_search']==1 ) echo "selected" ?> value="1">Allotted</option>
                  <option <?php if(isset($_POST['symbol_search']) && $_POST['symbol_search']==2 ) echo "selected" ?> value="2">Not Allotted</option>
                   <!-- <option value="3">Both</option> -->
                 
                </select>










          <button type="submit" id="candnomination" class="btn btn-primary report-btn">Export PDF</button>
      </form>
      <!-- Export PDF End -->      
    </div>

    <div class="col-md-2">
      <!-- Export PDF Start -->
   <!--   <form class="form-horizontal" id="" method="post" action="{{url('eci/ca-candidate-list-summary-pdf')}}">
        {{csrf_field()}}  
         <select name="phase" id="phase" class="form-control"  style="display:none;">
            <option value="" class=>-- All Phases --</option>
              @foreach($phase_list as $rowph)    
              <option <?php if(isset($_POST['phase']) && $_POST['phase']==$rowph->StatePHASE_NO ) echo "selected" ?> value="{{$rowph->StatePHASE_NO}}">Phase-{{$rowph->StatePHASE_NO}}</option>
              @endforeach
          </select> 
        <select name="state_id" id="state_id" class="form-control"  onchange="javascript:get_ac()"  style="display:none;">
          <option value="" class=>-- All States --</option>
            @foreach($state_list as $rows)    
            <option <?php if(isset($_POST['state_id']) && $_POST['state_id']==$rows->ST_CODE ) echo "selected" ?> value="{{$rows->ST_CODE}}">{{$rows->ST_CODE}}-{{$rows->ST_NAME}}</option>
            @endforeach
        </select>
        <select name="app_status" id="app_status" class="form-control" style="display:none;">
              <option value="">-- Select --</option>
                @foreach($status_list as $rowst)    
                <option <?php if(isset($_POST['app_status']) && $_POST['app_status']==$rowst->id ) echo "selected" ?> value="{{$rowst->id}}" >{{$rowst->status}}</option>
                @endforeach
                <option <?php if(isset($_POST['app_status']) && $_POST['app_status']==12 ) echo "selected" ?> value="12">Contesting</option>
            </select>
         <button type="submit" id="candnomination" class="btn btn-primary report-btn">Export Partywise Summary PDF</button>
       </form> -->
       <!-- Export PDF End -->
    <!-- </div> -->
  <!--   <br>
    <br> -->
    <!-- <div class="col-md-8"></div> 
    <div class="col-md-1">-->
      <!-- Export Excel Start -->
      <form class="form-horizontal" id="" method="post" action="{{url('eci/ca-candidate-list-excel')}}">
        {{csrf_field()}} 
        <select name="phase" id="phase" class="form-control"  style="display:none;">
            <option value="" class=>-- All Phases --</option>
              @foreach($phase_list as $rowph)    
              <option <?php if(isset($_POST['phase']) && $_POST['phase']==$rowph->StatePHASE_NO ) echo "selected" ?> value="{{$rowph->StatePHASE_NO}}">Phase-{{$rowph->StatePHASE_NO}}</option>
              @endforeach
          </select> 
        <select name="state_id" id="state_id" class="form-control" style="display:none;">
          <option value="" class=>-- All States --</option>
            @foreach($state_list as $rows)    
            <option <?php if(isset($_POST['state_id']) && $_POST['state_id']==$rows->ST_CODE ) echo "selected" ?> value="{{$rows->ST_CODE}}">{{$rows->ST_CODE}}-{{$rows->ST_NAME}}</option>
            @endforeach
        </select>
        <select name="district" id="district" class="form-control"  style="display:none;">
          <option value="" class=>-- All Districts --</option>
            @foreach($district_list as $rowd)    
            <option <?php if(isset($_POST['district']) && $_POST['district']==$rowd->DIST_NO) echo "selected" ?> value="{{$rowd->DIST_NO}}">{{$rowd->DIST_NO}}-{{$rowd->DIST_NAME}}</option>
            @endforeach
        </select>
        <select name="ac_id_report" id="ac_id_report" class="form-control"  style="display:none;">
          <option value="" class=>-- All AC --</option>
            @if($ac_list)
              @foreach($ac_list as $rowac)    
              <option <?php if(isset($_POST['ac_id']) && $_POST['ac_id']==$rowac->AC_NO ) echo "selected" ?> value="{{$rowac->AC_NO}}">{{$rowac->AC_NO}}-{{$rowac->AC_NAME}}</option>
              @endforeach
            @endif
        </select>
        <select name="party_id" id="party_id" class="form-control"  style="display:none;">
          <option value="" class=>-- All Parties --</option>
            @foreach($party_list as $rowp)    
            <option <?php if(isset($_POST['party_id']) && $_POST['party_id']==$rowp->CCODE ) echo "selected" ?> value="{{$rowp->CCODE}}">{{$rowp->PARTYABBRE}}-{{$rowp->PARTYNAME}}</option>
            @endforeach
        </select>
        <select name="cand_type" id="cand_type" class="form-control"  style="display:none;">
            <option value="">-- Select --</option>
            <option value="1" <?php if(isset($_POST['cand_type']) && $_POST['cand_type']==1) echo "selected" ?> >With Criminal Antecedents</option>
            <option value="2" <?php if(isset($_POST['cand_type']) && $_POST['cand_type']==2) echo "selected" ?> >Without Criminal Antecedents</option>
          </select>
          <select name="app_status" id="app_status" class="form-control" style="display:none;">
              <option value="">-- Select --</option>
                @foreach($status_list as $rowst)    
                <option <?php if(isset($_POST['app_status']) && $_POST['app_status']==$rowst->id ) echo "selected" ?> value="{{$rowst->id}}" >{{$rowst->status}}</option>
                @endforeach
                 <option <?php if(isset($_POST['app_status']) && $_POST['app_status']==12 ) echo "selected" ?> value="12">Contesting</option>
            </select>

                <select name="symbol_search" id="symbol_search" class="form-control" style="display:none;">
                  <option value="">-- Select --</option>
                  <option  <?php if(isset($_POST['symbol_search']) && $_POST['symbol_search']==1 ) echo "selected" ?> value="1">Allotted</option>
                  <option <?php if(isset($_POST['symbol_search']) && $_POST['symbol_search']==2 ) echo "selected" ?> value="2">Not Allotted</option>
                   <!-- <option value="3">Both</option> -->
                 
                </select>








          <button type="submit" id="candnomination" class="btn btn-primary report-btn">Export Excel</button>
      </form> 
      <!-- Export Excel End -->
    </div>

    <div class="col-md-2">
             <!-- Export Excel Start -->
     <!--  <form class="form-horizontal" id="" method="post" action="{{url('eci/ca-candidate-list-summary-excel')}}">
        {{csrf_field()}}  
         <select name="phase" id="phase" class="form-control"  style="display:none;">
            <option value="" class=>-- All Phases --</option>
              @foreach($phase_list as $rowph)    
              <option <?php if(isset($_POST['phase']) && $_POST['phase']==$rowph->StatePHASE_NO ) echo "selected" ?> value="{{$rowph->StatePHASE_NO}}">Phase-{{$rowph->StatePHASE_NO}}</option>
              @endforeach
          </select> 
        <select name="state_id" id="state_id" class="form-control"  onchange="javascript:get_ac()"  style="display:none;">
          <option value="" class=>-- All States --</option>
            @foreach($state_list as $rows)    
            <option <?php if(isset($_POST['state_id']) && $_POST['state_id']==$rows->ST_CODE ) echo "selected" ?> value="{{$rows->ST_CODE}}">{{$rows->ST_CODE}}-{{$rows->ST_NAME}}</option>
            @endforeach
        </select>
        <select name="app_status" id="app_status" class="form-control" style="display:none;">
            <option value="">-- Select --</option>
              @foreach($status_list as $rowst)    
              <option <?php if(isset($_POST['app_status']) && $_POST['app_status']==$rowst->id ) echo "selected" ?> value="{{$rowst->id}}" >{{$rowst->status}}</option>
              @endforeach
              <option <?php if(isset($_POST['app_status']) && $_POST['app_status']==12 ) echo "selected" ?> value="12">Contesting</option>
          </select>
         <button type="submit" id="candnomination" class="btn btn-primary report-btn" >Export Partywise Summary Excel</button>
       </form> -->
       <!-- Export Excel End -->
    </div>
    <br><br>
    <div class="col-md-8"></div>
    <div class="col-md-2">
      <!-- Export Excel Start -->
     <!-- <form class="form-horizontal" id="" method="post" action="{{url('eci/ca-candidate-list-acwisesummary-excel')}}">
        {{csrf_field()}}  
         <select name="phase" id="phase" class="form-control"  style="display:none;">
            <option value="" class=>-- All Phases --</option>
              @foreach($phase_list as $rowph)    
              <option <?php if(isset($_POST['phase']) && $_POST['phase']==$rowph->StatePHASE_NO ) echo "selected" ?> value="{{$rowph->StatePHASE_NO}}">Phase-{{$rowph->StatePHASE_NO}}</option>
              @endforeach
          </select> 
        <select name="state_id" id="state_id" class="form-control"  onchange="javascript:get_ac()"  style="display:none;">
          <option value="" class=>-- All States --</option>
            @foreach($state_list as $rows)    
            <option <?php if(isset($_POST['state_id']) && $_POST['state_id']==$rows->ST_CODE ) echo "selected" ?> value="{{$rows->ST_CODE}}">{{$rows->ST_CODE}}-{{$rows->ST_NAME}}</option>
            @endforeach
        </select>
        <select name="app_status" id="app_status" class="form-control" style="display:none;">
            <option value="">-- Select --</option>
              @foreach($status_list as $rowst)    
              <option <?php if(isset($_POST['app_status']) && $_POST['app_status']==$rowst->id ) echo "selected" ?> value="{{$rowst->id}}" >{{$rowst->status}}</option>
              @endforeach
               <option <?php if(isset($_POST['app_status']) && $_POST['app_status']==12 ) echo "selected" ?> value="12">Contesting</option>
          </select>
         <button type="submit" id="candnomination" class="btn btn-primary report-btn" >Export ACwise Excel</button>
       </form> -->
      <!-- Export Excel End -->
    </div>

    <div class="col-md-2">
             <!-- Export Excel Start -->
      <!-- <form class="form-horizontal" id="" method="post" action="{{url('eci/ca-candidate-list-acwisesummary-pdf')}}">
        {{csrf_field()}}  
         <select name="phase" id="phase" class="form-control"  style="display:none;">
            <option value="" class=>-- All Phases --</option>
              @foreach($phase_list as $rowph)    
              <option <?php if(isset($_POST['phase']) && $_POST['phase']==$rowph->StatePHASE_NO ) echo "selected" ?> value="{{$rowph->StatePHASE_NO}}">Phase-{{$rowph->StatePHASE_NO}}</option>
              @endforeach
          </select> 
        <select name="state_id" id="state_id" class="form-control"  onchange="javascript:get_ac()"  style="display:none;">
          <option value="" class=>-- All States --</option>
            @foreach($state_list as $rows)    
            <option <?php if(isset($_POST['state_id']) && $_POST['state_id']==$rows->ST_CODE ) echo "selected" ?> value="{{$rows->ST_CODE}}">{{$rows->ST_CODE}}-{{$rows->ST_NAME}}</option>
            @endforeach
        </select>
        <select name="app_status" id="app_status" class="form-control" style="display:none;">
            <option value="">-- Select --</option>
              @foreach($status_list as $rowst)    
              <option <?php if(isset($_POST['app_status']) && $_POST['app_status']==$rowst->id ) echo "selected" ?> value="{{$rowst->id}}" >{{$rowst->status}}</option>
              @endforeach
               <option <?php if(isset($_POST['app_status']) && $_POST['app_status']==12 ) echo "selected" ?> value="12">Contesting</option>
          </select>
         <button type="submit" id="candnomination" class="btn btn-primary report-btn">Export ACwise PDF</button>
       </form>
-->
       <!-- Export Excel End -->
    </div> 
    <br><br>
       <table id="list-table" class="table table-striped table-bordered table-hover" style="font-size:12px">
        <thead> 
          <tr> 
            <th>Sl. No.</th> 
            <th>Phase</th>
            <th>Candidate Name</th>
            <th>State</th>  
            <th>District</th>  
            <th>AC</th>  
            <th>Party</th> 
            <th>Is Criminal</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @if(!empty($data))
            @foreach($data as $list) 
            <?php $j++;  ?>      
        
        <tr>
          <td>{{$j}}</td>
          <td>Phase-{{$list->StatePHASE_NO}}</td>
          <td>Nom. Id- {{$list->nom_id}}-{{$list->cand_name}} -S/O or H/O:-{{$list->candidate_father_name}}</td> 
          <td>{{$list->ST_NAME}}</td>
          <td>{{$list->DIST_NAME}}</td>
          <td>{{$list->AC_NAME}}</td>
          <td>{{$list->PARTYABBRE}}-{{$list->PARTYNAME}}</td>
          <td>@if($list->is_criminal==1) <span class="text-danger">Yes</span> @else <span class="text-success">No</span> @endif</td>
          <td>{{$list->application_status}}</td>
        </tr>
 
           
            @endforeach 
            @endif 
        </tbody>
     
    </table>
    </div>
  </div>
  </section>
  </main>
 
@endsection
@section('script')
<script>    
    function get_ac(){
        var district_id = $("#district").val();
        var schedule_id = $("#phase").val();
        var state_id = $("#state_id").val();
            $.ajax({
                url: "{{ url('eci/get-ac') }}",
                type: 'GET',
                data: {district_id:district_id, state_id:state_id, schedule_id:schedule_id},         
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success:function(data){                   
                    data = JSON.parse(data);
                    var count = Object.keys(data).length;
                    var all = '<option value="">-- All AC --</option>';
                    for (var i = 0; i < count; i++) { 
                        if(data[i].id!=10)
                        {
                            all += '<option value="'+ data[i].id +'">'+data[i].id+' - '+ data[i].name +'</option>'; 
                        }
                        }
                    $("#ac_id").html(all);
                }
            });
        }
        function get_state(){
        var phase_id = $("#phase").val();

            $("#district").html('<option value="">-- All Districts --</option>');
            $("#ac_id").html('<option value="">-- All AC --</option>');

        if(phase_id)
        {
              $.ajax({
                  url: "{{ url('eci/get-state') }}",
                  type: 'GET',
                  data: {id:phase_id},            
                  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                  success:function(data){                   
                      data = JSON.parse(data);
                      var count = Object.keys(data).length;
                      var all = '<option value="">-- All States --</option>';
                      for (var i = 0; i < count; i++) { 
                          if(data[i].id!=10)
                          {
                              all += '<option value="'+ data[i].id +'">'+data[i].id+' - '+ data[i].name +'</option>'; 
                          }
                          }
                      $("#state_id").html(all);
                  }
              });
          }
          else
          {
            $.ajax({
                  url: "{{ url('eci/get-state') }}",
                  type: 'GET',
                  data: {id:0},            
                  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                  success:function(data){                   
                      data = JSON.parse(data);
                      var count = Object.keys(data).length;
                      var all = '<option value="">-- All States --</option>';
                      for (var i = 0; i < count; i++) { 
                          if(data[i].id!=10)
                          {
                              all += '<option value="'+ data[i].id +'">'+data[i].id+' - '+ data[i].name +'</option>'; 
                          }
                          }
                      $("#state_id").html(all);
                  }
              });
          }
        }
        function get_district()
        {
          var state_id = $("#state_id").val();
          var schedule_id = $("#phase").val();
              $.ajax({
                  url: "{{ url('eci/get-district') }}",
                  type: 'GET',
                  data: {id:state_id, schedule_id:schedule_id},            
                  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                  success:function(data){                   
                      data = JSON.parse(data);
                      var count = Object.keys(data).length;
                      var all = '<option value="">-- All Districts --</option>';
                      for (var i = 0; i < count; i++) { 
                          if(data[i].id!=10)
                          {
                              all += '<option value="'+ data[i].id +'">'+data[i].id+' - '+ data[i].name +'</option>'; 
                          }
                          }
                      $("#district").html(all);
                  }
              });
        }
</script>
@endsection