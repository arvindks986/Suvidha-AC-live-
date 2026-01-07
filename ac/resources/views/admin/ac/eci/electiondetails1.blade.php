@extends('admin.layouts.theme')
@section('content')
@include('admin.includes.ecimultipleselectscript') 
<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
       @if(Session::has('success_admin'))
             <div class="alert alert-success">
                <strong> {{ nl2br(Session::get('success_admin')) }}</strong> 
              </div>
          @endif   
          @if(Session::has('unsuccess_admin'))
             <div class="alert alert-danger">
                <strong> {{ nl2br(Session::get('unsuccess_admin')) }}</strong> 
              </div>
          @endif  
   <div class="nw-crte-usr">
         <div class="head-title">
          <h3><i><img src="{{ asset('theme/images/icons/tab-icon-010.png')}}" /></i>Create / Udate Election Details</h3>
         </div>
        
            <form class="form-horizontal" id="election_form" method="POST"  action="{{url('eci/assigndetails') }}" >
                {{ csrf_field() }}   

            <div class="col-sm-12" style="border: 1px solid #CCDDFF;" > 
              <div class="col-sm-3 {{ $errors->has('schedule') ? ' has-error' : '' }}" >
                     <label class="control-label" for="">Schedule No:</label> 
                     <select name="schedule" id="schedule" style="width:120px;"  >
                            <option value="{{$schedule}}">{{$schedule}}</option>
                     </select> 
                      
                  </div><!-- End Of form-group Div -->
            <div class="col-sm-3 {{ $errors->has('phaseno')?'has-error' : '' }}" >
                <label class="control-label">Phase No:</label>
                    <select name="phaseno" id="phaseno" style="width:80px;"  >
                           <option value="{{$list_sec->SCHEDULENO}}">{{$list_sec->SCHEDULENO}}</option>
                     </select> 
                
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
                  
                   
                  <div class="col-sm-3 {{ $errors->has('electionid') ? ' has-error' : '' }}" >
                     <label class="control-label" for="">Election:</label>&nbsp;&nbsp;
                     <select name="electionid" id="electionid" style="width:120px;"  >
                         @foreach($list_election as $list)
                            @if($list_sec->ELECTION_ID==$list->election_id)
                              <option value="{{ $list->election_id}}"  selected='selected'>{{$list->election_sort_name."-".$list->election_type}}</option>
                            @endif
                              <!--<option value="{{ $list->election_id}}" 
                                @if($list_sec->ELECTION_ID==$list->election_id)
                                selected='selected'
                                @endif
                                >{{$list->election_sort_name."-".$list->election_type}}</option>-->
                            @endforeach
                     </select>   
                       
                  </div><!-- End Of form-group Div -->
                   <div class="col-sm-3 {{$errors->has('delimitation') ? ' has-error' : '' }}" >
                     <label class="control-label" for="">Delimitation type:</label>&nbsp;&nbsp;
                     <select name="delimitation" id="delimitation" style="width:80px;"  >
                        <option value="">Select</option><option value="pre">Pre</option><option value="post" selected="selected">Post</option>
                           
                     </select>   
                       
                  </div><!-- End Of form-group Div -->
                  <div class="col-sm-6 {{$errors->has('delimitation') ? ' has-error' : '' }}" >
                     <label class="control-label" for="">Election Date:</label>&nbsp;&nbsp;    
                    <input type="text" name="eledate" readonly="readonly" value="{{date("d-m-Y",strtotime($list_sec->DATE_POLL))}}" style="width:180px;" >
                  </div><!-- End Of form-group Div -->
                </div>
                <div class="row">
                  <div class="col-sm-6 {{ $errors->has('stphaseno') ? ' has-error':'' }}" >
                     <label class="control-label" for="">Select State Phase No:</label> 
                     <select name="stphaseno" id="stphaseno" style="width:80px;" >
                            <option value="">Phase no</option><option value="1">1</option><option value="2">2</option>
                            <option value="3">3</option><option value="4">4</option><option value="5">5</option>
                            <option value="6">6</option><option value="7">7</option><option value="8">8</option>
                            <option value="9">9</option><option value="10">10</option>
                     </select> 
                @if ($errors->has('stphaseno'))
                                <span class="help-block"> <strong>{{ $errors->first('stphaseno') }}</strong></span>
                           @endif
            </div><!-- End Of form-group Div -->
          </div>
        <div class="row">

            <table style='width:700px;' border="0"> <tr>
              <td style='width:300px; height:400px;text-align:center;'>
                  <b>Unassigned AC:</b><br/>
                 <select name="lstBox1[]" multiple="multiple" id='lstBox1' style='width:300px; height:400px;'>

                  @if(!empty($list_ac))  
                    @foreach($list_ac as $ac)  
                        <?php $va=0; ?>
                            @foreach($assignac as $lac)
                              @if($lac->CONST_NO==$ac->AC_NO)  
                                <?php $va=1; break; ?>
                              else 
                                  <?php $va=0; ?>
                              @endif
                            @endforeach
                             @if($va==0)
                              <option value="{{$ac->AC_NO}}">{{$ac->AC_NO}}-{{$ac->AC_NAME}}</option>
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
                   if($list->election_sort_name=="AC") { 
                            $tcon=\app(App\adminmodel\AcMaster::class)->where(['ST_CODE' =>$lac->ST_CODE])->where(['AC_NO' =>$lac->CONST_NO])->first();
                            $name=$tcon->AC_NAME;
                        }
                        elseif($list->election_sort_name=="PC") {
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
        <!--<div class="col-sm-6 {{ $errors->has('mscons') ? ' has-error':'' }}" >
            <label class="control-label" for="">Select Constituencies:</label>
        
            <select id="ms"  name="ms[]" multiple="multiple">
                  
                  @if(!empty($list_ac))
                    @foreach($list_ac as $ac)
                              <option value="{{ $ac->AC_NO }}">{{$ac->AC_NAME}}</option>
                     @endforeach
                  @endif
            </select>
         </div>-->
                
                    <div class="row">
                        <div class="btns-actn">
                           <input type="submit" value="Assigned Constituencies"> 
                        </div> 
                    </div>
            </form>  

    
          </div><!-- End Of nw-crte-usr Div -->
   
       <!--    Listing -->

<div class="page-contant">
      <div class="head-title">
              <h3><i><img src="{{ asset('theme/images/icons/tab-icon-002.png')}}" /></i>Election Details </h3>
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
                <tr><th>ID</th><th>State</th><th>Month</th><th>Year</th><th>DelmTypen</th> 
                 <th>Constituency Type</th><th>Total Constituency Type</th><th>Assigned Constituency</th><th>Unassigned Constituency</th>  <th>Details</th> 
                 </tr>
               </thead>
               @foreach($list_record as $list)
                  <?php   $totalconst=0; $assignconst=0; $unassignconst=0;
                          $s=\app(App\adminmodel\StateMaster::class)->where(['ST_CODE' =>$list->ST_CODE])->first(); 
                         // $ele=\app(App\adminmodel\ElectionMaster::class)->where(['election_id' =>$list->ELECTION_ID])->first(); 
                         $assignconst=\app(App\adminmodel\ElectiondetailsMaster::class)->where(['ST_CODE' =>$list->ST_CODE])->where(['ScheduleID' =>$list->ScheduleID])->get()->count();
                        if($list->ConstType=="AC") {
                            $totalconst=\app(App\adminmodel\AcMaster::class)->where(['ST_CODE' =>$list->ST_CODE])->get()->count();
                           
                          }
                        elseif($list->ConstType=="PC") {
                            $totalconst=\app(App\adminmodel\PCMaster::class)->where(['ST_CODE' =>$list->ST_CODE])->get()->count();

                         } 
                       $unassignconst=$totalconst-$assignconst;
                        ?> 
               <tr><td>{{ $list->ID }} </td><td>{{ $s->ST_NAME }} </td><td>{{ $list->MONTH }} </td><td>{{ $list->YEAR }} </td><td>{{ $list->DelmType }} </td> 
               <td>{{ $list->ConstType }} - {{ $list->ElecType }}</td><td>{{ $totalconst }} </td><td> {{$assignconst}} </td><td>{{$unassignconst}}  </td> <td><a href="{{ url('/eci/view-election-details') }}/{{$list-> ST_CODE}}/{{$list->ScheduleID}}" onclick="return confirm('Are you Show Details?');">View</a></td> 
                
              
               </tr>
                @endforeach
              </table>
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
        alert($(selectedOpts).val());
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
 