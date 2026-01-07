@extends('admin.layouts.theme')
@section('content')
@include('admin.includes.ecimultipleselectscript') 
@include('admin.includes.list_script')
<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
       @if(Session::has('success_admin'))
                <script type="text/javascript" >
                    alert(' {{Session::get('success_admin')}}');
                  </script>
             <div class="alert alert-success">
                <strong> {{ nl2br(Session::get('success_admin')) }}</strong> 
              </div>
          @endif   
          @if(Session::has('unsuccess_admin'))
             <div class="alert alert-danger">
                <strong> {{ nl2br(Session::get('unsuccess_admin')) }}</strong> 
              </div>
          @endif 
    <div class="page-contant">
      <div class="head-title">
              <h3><i><img src="{{ asset('admintheme/images/icons/tab-icon-002.png')}}" /></i>Election Schedule </h3>
      </div>
            
      <!-- Start Of Page Sub Setion Div --> 
       <div class="page-sub-setion"> 
      <!-- Start Of Intra section Div -->
          <div class="intra-section">
          
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
           <!-- Start Table Here -->  
            <div class="table-responsive">
              <table class="table table-bordered">
               <thead>
                 <tr><th>Phase No</th><th>Date of Press Announcement</th><th>St. Date of Nomination</th><th>Last Date of Nomination</th> 
                 <th>Date for Scrutiny</th><th>Last Date of Withdrawal </th><th>Date of Poll</th><th>Date of Counting</th><th>Assign State Phase</th> 
                 </tr>
               </thead>
               @foreach($list_schedule as $list)
              
               <tr><td>{{ $list->SCHEDULENO }}</td><td>{{ date("d-m-Y",strtotime($list->DT_PRESS_ANNC)) }}</td> 
                  <td>{{ date("d-m-Y",strtotime($list->DT_ISS_NOM)) }}</td><td>{{ date("d-m-Y",strtotime($list->LDT_IS_NOM)) }}</td>
                  <td>{{ date("d-m-Y",strtotime($list->DT_SCR_NOM)) }}</td><td>{{ date("d-m-Y",strtotime($list->LDT_WD_CAN)) }}</td>
                  <td>{{ date("d-m-Y",strtotime($list->DATE_POLL)) }}</td><td>{{ date("d-m-Y",strtotime($list->DATE_COUNT)) }}</td>
                   </td> <td><a href="{{url('eci/election-details/'.$list->SCHEDULEID) }}">Assign Phase</a></td> 
              
               </tr>
              @endforeach
              </table>
            </div><!-- End Of table-responsive Div -->
          </div>
         <?php if($type=='assign') { ?>  
          <form class="form-horizontal" id="election_form" method="POST"  action="{{url('eci/election-details') }}" >
                {{ csrf_field() }}  
                <input type="hidden" name="scheduleid" value="{{$list_sec->SCHEDULEID}}">   
            <div class="col-sm-12"  > 
              <div class="col-sm-3 {{ $errors->has('stphaseno') ? ' has-error' : '' }}" >
                     <label class="control-label" for="">State Phase No:</label> 
                     <select name="stphaseno" id="stphaseno" style="width:120px;"  >
                            <option value="">Phase No</option>
                            <option value="1">1</option><option value="2">2</option>
                            <option value="3">3</option><option value="4">4</option><option value="5">5</option>
                            <option value="6">6</option><option value="7">7</option><option value="8">8</option>
                            <option value="9">9</option><option value="10">10</option>
                     </select> 
                      @if ($errors->has('stphaseno'))
                                <span class="help-block" <strong>{{ $errors->first('stphaseno') }}</strong></span>
                           @endif
                  </div><!-- End Of form-group Div -->
           
               
                 
                <div class="col-sm-3 {{ $errors->has('state') ? ' has-error' : '' }}" >
                     <label class="control-label" for="">State:</label> 
                     <select name="state" id="state" style="width:150px;"  >
                            <option value=""> Select State</option>
                            @foreach($list_state as $s)
                              <option value="{{ $s->ST_CODE }}"@if(old('state')==$s->ST_CODE) selected @endif>{{ $s->ST_NAME }}</option>
                            @endforeach 
                     </select> 
                      @if ($errors->has('state'))
                                <span class="help-block" <strong>{{ $errors->first('state') }}</strong></span>
                           @endif
                  </div><!-- End Of form-group Div -->
                  
                 
                   <div class="col-sm-3 {{$errors->has('delimitation') ? ' has-error' : '' }}" >
                     <label class="control-label" for="">Delimitation type:</label>&nbsp;&nbsp;
                     <select name="delimitation" id="delimitation" style="width:80px;"  >
                        <option value="">Select</option><option value="pre">Pre</option><option value="post" selected="selected">Post</option>
                           
                     </select>   
                       @if ($errors->has('delimitation'))
                                <span class="help-block" <strong>{{ $errors->first('delimitation') }}</strong></span>
                           @endif
                  </div><!-- End Of form-group Div -->
               
                  <div class="col-sm-3 {{$errors->has('election') ? ' has-error' : '' }}" >
                     <label class="control-label" for="">Election type:</label>&nbsp;&nbsp;
                     <select name="election" id="election" >
                          <option value="">Select Election</option>
                        @foreach($list_election as $list)
                          <option value="{{ $list->election_id }}">{{ $list->election_sort_name."-".$list->election_type }}</option>
                    @endforeach
                           
                     </select>   
                       @if ($errors->has('election'))
                                <span class="help-block" <strong>{{ $errors->first('election') }}</strong></span>
                           @endif
                  </div><!-- End Of form-group Div -->
                </div>
    
                    <div class="row">
                        <div class="btns-actn">
                           <input type="submit" value="Show Records"> 
                        </div> 
                    </div>
            </form> 
            <?php } if($type=="show") { ?> 
                <form class="form-horizontal" id="election_form" method="POST"  action="{{url('eci/assigndetails') }}" >
                {{ csrf_field() }}  
                 
                 <input type="hidden" name="schedule" value="{{$schedule}}">
                 <input type="hidden" name="electionid" value="{{$list_sec->ELECTION_ID}}">
                 <input type="hidden" name="electiontypeid" value="{{$electiontypeid}}">
                 <input type="hidden" name="delimitation" value="{{$delimitation}}">
                 
          <div class="col-sm-12">
                  <div class="col-sm-3" >
                     <label class="control-label" for="">Phase No:</label>
                     <label class="control-label" for="">{{$list_sec->SCHEDULENO}}</label> 
                  </div><!-- End Of form-group Div -->
                  <div class="col-sm-3" >
                     <label class="control-label" for="">Election:</label>
                     <label class="control-label" for=""> @foreach($list_election as $list)
                        @if($electiontypeid==$list->election_id)
                          <option value="{{ $list->election_id }}">{{ $list->election_sort_name."-".$list->election_type }}</option>
                        @endif
                    @endforeach </label> 
                  </div><!-- End Of form-group Div -->
                  <div class="col-sm-3" >
                     <label class="control-label" for="">Election Date:</label>
                     <label class="control-label" for="">{{date("d-m-Y",strtotime($list_sec->DATE_POLL))}}</label> 
                  </div><!-- End Of form-group Div -->
                  <div class="col-sm-3" >
                     <label class="control-label" for="">Delimitation Type:</label>
                     <label class="control-label" for="">{{$delimitation}}</label> 
                  </div><!-- End Of form-group Div -->
          </div> 
           <div class="col-sm-12"> 
              <div class="col-sm-3 {{ $errors->has('stphaseno') ? ' has-error' : '' }}" >
                     <label class="control-label" for="">State Phase No:</label> 
                     <select name="stphaseno" id="stphaseno" style="width:120px;"  >
                            <option value="">Phase No</option>
                               <option value="1"@if($stphaseno=='1') selected @endif>1</option>
                               <option value="2"@if($stphaseno=='2') selected @endif>2</option>
                            <option value="3" @if($stphaseno=='3') selected @endif>3</option>
                            <option value="4" @if($stphaseno=='4') selected @endif>4</option>
                            <option value="5" @if($stphaseno=='5') selected @endif>5</option>
                            <option value="6" @if($stphaseno=='6') selected @endif>6</option>
                            <option value="7" @if($stphaseno=='7') selected @endif>7</option>
                            <option value="8" @if($stphaseno=='8') selected @endif>8</option>
                            <option value="9" @if($stphaseno=='9') selected @endif>9</option>
                            <option value="10" @if($stphaseno=='10') selected @endif>10</option>
                            
                     </select> 
                      @if ($errors->has('stphaseno'))
                                <span class="help-block" <strong>{{ $errors->first('stphaseno') }}</strong></span>
                           @endif
                  </div><!-- End Of form-group Div -->
               <div class="col-sm-3 {{ $errors->has('state') ? ' has-error' : '' }}" >
                     <label class="control-label" for="">State:</label> 
                     <select name="state" id="state" style="width:150px;"   >
                       
                            @foreach($list_state as $s)
                            @if($st_code==$s->ST_CODE)
                              <option value="{{ $s->ST_CODE }}"  selected='selected'>{{ $s->ST_NAME }}</option>
                            @endif
                             <!-- <option value="{{ $s->ST_CODE }}"@if($st_code==$s->ST_CODE) selected @endif>{{ $s->ST_NAME }}</option>-->
                            @endforeach 
                     </select> 
                     
                  </div><!-- End Of form-group Div -->
                  
                   
                   
                  
                </div>
                 
        <div class="row">

            <table style='width:700px;' border="0"> <tr>
              <td style='width:300px; height:400px;text-align:center;'>
                  <b>Unassigned AC:</b><br/>
                 <select name="lstBox1[]" multiple="multiple" id='lstBox1' style='width:300px; height:400px;'>
                 @if(!empty($list_ac))  
                    @foreach($list_ac as $ac)  
                        <?php $va=0; if($const_type=="AC"){ $c_no=$ac->AC_NO; $c_name=$ac->AC_NAME; } else { $c_no=$ac->PC_NO; $c_name=$ac->PC_NAME; } ?>
                            @foreach($assignac as $lac)
                              @if($lac->CONST_NO==$c_no)  
                                <?php $va=1; break; ?>
                              else 
                                  <?php $va=0; ?>
                              @endif
                            @endforeach
                             @if($va==0)
                              <option value="{{$c_no}}">{{$c_no}}-{{$c_name}}</option>
                             @endif 
                     @endforeach
                  @endif
              </select>
          </td>
    <td style='width:100px;text-align:center;vertical-align:middle;'>
      <!--  <input type='button' id='btnRight' value ='  >  '/>
        <br/><input type='button' id='btnLeft' value ='  <  '/>-->
    </td>
    <td style='width:300px;text-align:center;'>
        <b>Assigned AC: </b><br/>
        <select name="lstBox2[]" multiple="multiple" id='lstBox2' style='width:300px; height:400px;'>
           @foreach($assignac as $lac)
           <?php    
                   if($const_type=="AC") { 
                          $tcon=\app(App\adminmodel\AcMaster::class)->where(['ST_CODE' =>$lac->ST_CODE])->where(['AC_NO' =>$lac->CONST_NO])->first();
                          $name=$tcon->AC_NAME;
                        }
                        elseif($const_type=="PC") {
                            $tcon=\app(App\adminmodel\PCMaster::class)->where(['ST_CODE' =>$lac->ST_CODE])->where(['PC_NO' =>$lac->CONST_NO])->first();
                            $name=$tcon->PC_NAME;
                          }
              
                        ?> 
               <option value="{{$lac->CONST_NO}}">{{$lac->CONST_NO}}-{{$name}}-St. Sch-{{$lac->StatePHASE_NO}} </option>
                               
           @endforeach
                             
        </select>
    </td>
</tr>
</table>
</div>
                       
                    <div class="row">
                        <div class="btns-actn">
                           <input type="submit" value="Assign Constituencies"> 
                        </div> 
                    </div>
            </form> 

            <?php } ?><!-- end if-- show -->
 <!-- start of Selected Div -->
          </div>  
        </div><!-- End Of intra-section Div -->   
        </div><!-- End Of page-sub-setion Div -->
    </div><!-- End OF page-contant Div --> 
      
       <!--    Listing -->

<div class="page-contant">
      <div class="head-title">
              <h3><i><img src="{{ asset('admintheme/images/icons/tab-icon-002.png')}}" /></i>Election Details </h3>
      </div>
            
      <!-- Start Of Page Sub Setion Div --> 
       <div class="page-sub-setion"> 
      <!-- Start Of Intra section Div -->
          <div class="intra-section">
          
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
           <!-- Start Table Here -->  
            <div class="table-responsive">
             <table id="example" class="table table-striped table-bordered" style="width:100%">
               <thead> <tr><th>ID</th><th>State</th><th>Phase No.</th> 
                 <th>Const. Type</th><th>Total Const.</th><th>Assigned Const.</th><th>Unassigned Const.</th> 
                 <th>Re Assign/ Update AC</th> <th>Details</th><th>Delete</th></tr>
               </thead><tbody>
                
                
               </thead> <?php $i=0; ?>

               @foreach($list_record as $list)
                  <?php $i++;  $totalconst=0; $assignconst=0; $unassignconst=0;
                          $s=\app(App\adminmodel\StateMaster::class)->where(['ST_CODE' =>$list->ST_CODE])->first();
                          $curele=\app(App\adminmodel\Electioncurrentelection::class)->where(['ST_CODE' =>$list->ST_CODE])->first();
                        
                        if($curele->ConstType=="AC") {
                            $totalconst=\app(App\adminmodel\AcMaster::class)->where(['ST_CODE' =>$list->ST_CODE])->get()->count();
                           
                          }
                        elseif($curele->ConstType=="PC") {
                            $totalconst=\app(App\adminmodel\PCMaster::class)->where(['ST_CODE' =>$list->ST_CODE])->get()->count();

                         } 
                       $unassignconst=$totalconst-$list->total;
                        ?> 
               <tr><td>{{ $i }} </td><td>{{ $s->ST_CODE }}-{{ $s->ST_NAME }} </td><td>Phase:-{{ $list->StatePHASE_NO }} </td> 
               <td>{{$list->CONST_TYPE}}-{{$list->ELECTION_TYPE}}  </td><td>{{ $totalconst }} </td><td> {{$list->total}} </td><td>{{$unassignconst}}  </td> <td><a href="{{url('/eci/update-election-details') }}/{{$list->ST_CODE}}/{{$curele->ScheduleID}}/{{$list->StatePHASE_NO}}/{{$list->CONST_TYPE}}">Reassign/ update </a></td>
               <td><a href="{{ url('/eci/view-election-details') }}/{{$list-> ST_CODE}}/{{$list->ScheduleID}}/{{$list->StatePHASE_NO}}/{{$list->CONST_TYPE}}">View</a></td> 
               <td><a href="{{ url('/eci/delete-election')}}/{{$list-> ST_CODE}}/{{$list->CONST_TYPE}}" onclick="return confirm('Are you Deleted?');">Delete</a></td>
               </tr>
                @endforeach
               </tbody>
              <tfoot> <tr><th>ID</th><th>State</th><th>Phase No.</th> 
                 <th>Const. Type</th><th>Total Const.</th><th>Assigned Const.</th><th>Unassigned Const.</th> 
                 <th>Re Assign/ Update AC</th> <th>Details</th><th>Delete</th>   </tr>
            </tfoot></table>
            </div><!-- End Of table-responsive Div -->
          </div>
           
          </div>  
        </div><!-- End Of intra-section Div -->   
        </div><!-- End Of page-sub-setion Div -->
      
    </div><!-- End OF page-contant Div -->


       <!-- end list-->
    
    </div> <!-- End Of child-area Div -->     
  </div><!-- End Of parent-wrap Div -->
  </div> 
<script type="text/javascript">
  $(document).ready(function() {
    $('#btnRight').click(function(e) {
        var selectedOpts = $('#lstBox1 option:selected');
        if (selectedOpts.length == 0) {
            alert("Nothing to move.");
            e.preventDefault();
        }

        $('#lstBox2').append($(selectedOpts).clone());
        $(selectedOpts).remove();
        e.preventDefault();
    });

    $('#btnLeft').click(function(e) {
        var selectedOpts = $('#lstBox2 option:selected');
        if (selectedOpts.length == 0) {
            alert("Nothing to move.");
            e.preventDefault();
        }

        $('#lstBox1').append($(selectedOpts).clone());
        $(selectedOpts).remove();
        e.preventDefault();
    });
});

</script>
@endsection
 