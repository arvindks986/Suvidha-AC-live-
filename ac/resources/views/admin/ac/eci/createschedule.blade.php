@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Create Election Schedule')
@section('content')
 
 <style type="text/css">
      th, td { white-space: nowrap;}
        <!-- .dataTables_wrapper .row:nth-child(2) .col-sm-12 { overflow: scroll;} -->
        
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
  </style>
  <main role="main" class="inner cover mb-3">
  <section class="mt-3">
  <div class="container">
  <div class="row">
           
  <div class="card mt-3" style="width:100%; margin:0 auto;">
                <div class="card-header">
                <div class="row">
                 <div class="col"> <h4>Create Election Schedule</h4> </div> 
               </div>
                </div>
   <div class="row">
    <div class="col">
      @if(Session::has('success_admin'))
              <div class="alert alert-success">{{session('success_admin') }} </div> 
        @endif  
        @if(Session::has('success_admin'))
              <div class="alert alert-danger">{{session('unsuccess_insert') }} </div> 
        @endif   
       <div class="btns-actn"> <center>
                 <input type="submit" name="btnadd" value="Add Schedule" onClick="showadd();" id="btnadd" class="btns-actn">
                 <input type="submit" name="btncancel" value="Cancel Schedule" onClick="canceladd();" id="btncancel" style="display:none;" class="btns-actn"></center>
          </div>   
    </div>
    </div>
      
       
    <div class="card-border" id="add_menu" @if(count($errors)<=0) style="display:none;" @else style="display:block;" @endif> 
     
       <form class="form-horizontal" id="election_form" method="post" action="{{url('eci/createschedule') }}"  enctype="multipart/form-data" autocomplete='off'>
          {{csrf_field()}}
    <input type="hidden" name="affidavit_name" value="Counter"/>
     <div class="row d-flex align-items-center ">
             
         <div class="col"> 
              <label for="candidate_id" class="col-form-label">Press Announcement Date <span class="errorred">*</span></label>
                <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{ old('pressdate') }}">
                   @if ($errors->has('pressdate'))
                        <span style="color:red;">{{ $errors->first('pressdate') }}</span>
                   @endif 
                  <span id="errmsg1" class="text-danger"></span>
          </div>              
        
       
            <div class="col"> 
              <label for="candidate_id" class="col-form-label">Start Date of Nominations <span class="errorred">*</span></label>
                <input type="text" class="form-control" name="st_date_nom" id="st_date_nom" value="{{ old('st_date_nom') }}">
                   @if ($errors->has('st_date_nom'))
                        <span style="color:red;">{{ $errors->first('st_date_nom') }}</span>
                   @endif 
                  <span id="errmsg2" class="text-danger"></span>
          </div>
        </div> 
       <div class="row d-flex align-items-center ">  
        <div class="col"> 
              <label for="candidate_id" class="col-form-label">Last Date of Nominations <span class="errorred">*</span></label>
                <input type="text" class="form-control" name="lt_date_nom" id="lt_date_nom" value="{{ old('lt_date_nom') }}">
                   @if ($errors->has('lt_date_nom'))
                        <span style="color:red;">{{ $errors->first('lt_date_nom') }}</span>
                   @endif 
                  <span id="errmsg3" class="text-danger"></span>
          </div>  
        
            <div class="col"> 
              <label for="candidate_id" class="col-form-label">Scrutiny of Nomination Date <span class="errorred">*</span></label>
                <input type="text" class="form-control" name="scut_date_nom" id="scut_date_nom" value="{{ old('scut_date_nom') }}">
                   @if ($errors->has('scut_date_nom'))
                        <span style="color:red;">{{ $errors->first('scut_date_nom') }}</span>
                   @endif 
                  <span id="errmsg4" class="text-danger"></span>
          </div>
          </div> 
       <div class="row d-flex align-items-center ">  
        <div class="col"> 
              <label for="candidate_id" class="col-form-label">Last Date of Withdrawal <span class="errorred">*</span></label>
                <input type="text" class="form-control" name="with_date_nom" id="with_date_nom" value="{{ old('with_date_nom') }}">
                   @if ($errors->has('with_date_nom'))
                        <span style="color:red;">{{ $errors->first('with_date_nom') }}</span>
                   @endif 
                  <span id="errmsg5" class="text-danger"></span>
          </div>                
        
       
          <div class="col"> 
              <label for="candidate_id" class="col-form-label">Date Of Poll <span class="errorred">*</span></label>
                <input type="text" class="form-control" name="date_ofpoll" id="date_ofpoll" value="{{ old('date_ofpoll') }}">
                   @if ($errors->has('date_ofpoll'))
                        <span style="color:red;">{{ $errors->first('date_ofpoll') }}</span>
                   @endif 
                  <span id="errmsg6" class="text-danger"></span>
          </div> 
        </div> 
       <div class="row d-flex align-items-center "> 
        <div class="col"> 
              <label for="candidate_id" class="col-form-label">Date of Counting <span class="errorred">*</span></label>
                <input type="text" class="form-control" name="date_of_counting" id="date_of_counting" value="{{ old('date_of_counting') }}">
                   @if ($errors->has('date_of_counting'))
                        <span style="color:red;">{{ $errors->first('date_of_counting') }}</span>
                   @endif 
                  <span id="errmsg7" class="text-danger"></span>
          </div>             
        
       
            <div class="col"> 
              <label for="candidate_id" class="col-form-label">Election Completion Date <span class="errorred">*</span></label>
                <input type="text" class="form-control" name="completion_date" id="completion_date" value="{{ old('completion_date') }}">
                   @if ($errors->has('completion_date'))
                        <span style="color:red;">{{ $errors->first('completion_date') }}</span>
                   @endif 
                  <span id="errmsg8" class="text-danger"></span>
          </div>  
                      
        
       </div> 
      <div class="col-md-1 p-0 m-0 float-right">
        <button type="submit" id="candnomination" class="btn btn-primary custombtn">Upload</button>  
      </div>
           
       
    </form>   
    </div>
     
    </div>
  
  
  </div>
  </div>
  </section>
   <!-- end entry section-->
   <section class="mt-3">
    <div class="container">
    <div class="row">
              
    <div class="card mt-3" style="width:100%; margin:0 auto;">
            <div class="card-header">
                  <div class="row"> 
                        <div class="col"> <h4>Details of Election Schedule</h4> </div> 
                 </div>
            </div>
    <div class="card-border"> 
     @if(!empty($list_schedule))
         <div class="table-responsive">
        <table   class="table table-striped table-bordered table-hover" style="width:100%">
          <thead> 
            <tr><th>Action</th><th>Sl. No</th><th>Date of Press Announcement</th><th>Notification Date</th><th>Last Date of Nomination</th><th>Date for Scrutiny</th>
            <th>Last Date of Withdrawal </th><th>Date of Poll</th><th>Date of Counting</th>
            <th>Election Completed Date</th> </tr> 
         </thead>
        <tbody>
            @foreach($list_schedule as $list) 
            <tr><td><a href="{{url('eci/update-election-schedule/'.$list->SCHEDULEID) }}">Edit</a></td>
              <td>{{ $list->SCHEDULENO }}</td><td>{{ date("d-m-Y",strtotime($list->DT_PRESS_ANNC)) }}</td> 
              <td>{{ date("d-m-Y",strtotime($list->DT_ISS_NOM)) }}</td>
              <td>{{ date("d-m-Y",strtotime($list->LDT_IS_NOM)) }}</td>
              <td>{{ date("d-m-Y",strtotime($list->DT_SCR_NOM)) }}</td>
              <td>{{ date("d-m-Y",strtotime($list->LDT_WD_CAN)) }}</td>
              <td>{{ date("d-m-Y",strtotime($list->DATE_POLL)) }}</td>
              <td>{{ date("d-m-Y",strtotime($list->DATE_COUNT)) }}</td>
              <td>{{ date("d-m-Y",strtotime($list->DTB_EL_COM)) }}</td> 
            </tr>
          @endforeach 
            
        </tbody>
      </table>
    </div>
      @endif 
      </div></div>
    </div>
    </div>
  </section>
  </main>
 
@endsection
@section('script')
<script language="javascript">
  
jQuery(document).ready(function() { 
         
    jQuery('#pressdate').datetimepicker({
          format: 'DD-MM-YYYY',
          useCurrent: false,
          maxDate: new Date()
      });
    jQuery('#st_date_nom').datetimepicker({
          format: 'DD-MM-YYYY',
          useCurrent: false,
          maxDate: new Date()
      });
    jQuery('#lt_date_nom').datetimepicker({
          format: 'DD-MM-YYYY',
          useCurrent: false,
          maxDate: new Date()
      })
    jQuery('#scut_date_nom').datetimepicker({
          format: 'DD-MM-YYYY',
          useCurrent: false,
          maxDate: new Date()
      });
    jQuery('#with_date_nom').datetimepicker({
          format: 'DD-MM-YYYY',
          useCurrent: false,
          maxDate: new Date()
      });
    jQuery('#date_ofpoll').datetimepicker({
          format: 'DD-MM-YYYY',
          useCurrent: false,
          maxDate: new Date()
      });
    jQuery('#date_of_counting').datetimepicker({
          format: 'DD-MM-YYYY',
          useCurrent: false,
          maxDate: new Date()
      });
    jQuery('#completion_date').datetimepicker({
          format: 'DD-MM-YYYY',
          useCurrent: false,
          maxDate: new Date()
      });
});

</script>       
<script language="javascript">
function showadd()
  {
  document.getElementById('add_menu').style.display="block";
  document.getElementById('btnadd').style.display="none";
  document.getElementById('btncancel').style.display="block";
  }
function canceladd()
  { 
  document.getElementById('add_menu').style.display="none";
  document.getElementById('btncancel').style.display="none";
  document.getElementById('btnadd').style.display="block";
  }
 </script>
 

@endsection