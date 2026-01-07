@extends('admin.layouts.theme')
@section('content')
@include('admin.includes.ecimultipleselectscript') 
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
              <h3><i><img src="{{ asset('admintheme/images/icons/tab-icon-002.png')}}" /></i>Re-assign Constituency </h3>
      </div>
            
      <!-- Start Of Page Sub Setion Div --> 
       <div class="page-sub-setion"> 
      <!-- Start Of Intra section Div -->
          <div class="intra-section">
          
          <div class="row">
         
                <form class="form-horizontal" id="election_form" method="POST"  action="{{url('eci/update-assign') }}" >
                {{ csrf_field() }}  
                 
                 <input type="hidden" name="schedule" value="{{$schedule}}">
                 <input type="hidden" name="electionid" value="{{$list_sec->ELECTION_ID}}">
                 <input type="hidden" name="electiontypeid" value="{{$celectionbystate->ELECTION_TYPEID}}">
                 <input type="hidden" name="delimitation" value="{{$celectionbystate->DelmType}}">
                 
          <div class="col-sm-12">
                  <div class="col-sm-3" >
                     <label class="control-label" for="">Schedule No:</label>
                     <label class="control-label" for="">{{$schedule}}</label> 
                  </div><!-- End Of form-group Div -->
                  <div class="col-sm-3" >
                     <label class="control-label" for="">Election:</label>
                     <label class="control-label" for="">@foreach($list_election as $list)
                                @if($celectionbystate->ELECTION_TYPEID==$list->election_id)
                                 {{$list->election_sort_name."-".$list->election_type}}
                                @endif
                        @endforeach</label> 
                  </div><!-- End Of form-group Div -->
                  <div class="col-sm-3" >
                     <label class="control-label" for="">Election Date:</label>
                     <label class="control-label" for="">{{date("d-m-Y",strtotime($list_sec->DATE_POLL))}}</label> 
                  </div><!-- End Of form-group Div -->
                  <div class="col-sm-3" >
                     <label class="control-label" for="">Delimitation Type:</label>
                     <label class="control-label" for="">{{$celectionbystate->DelmType}} </label> 
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
                        <?php $va=0; if($cons_type=="AC"){ $c_no=$ac->AC_NO; $c_name=$ac->AC_NAME; } else { $c_no=$ac->PC_NO; $c_name=$ac->PC_NAME; } ?>
                            @foreach($newassignac as $lac)
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
           @foreach($newassignac as $lac)
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
      <div class="row">
          <div class="btns-actn">
                           <input type="submit" value="Re-Assign Constituencies"> 
              <a href="{{url('eci/election-details') }}">Back</a>
                     
          </div> 
       </div>
  </form> 

          
 <!-- start of Selected Div -->
          </div>  
        </div><!-- End Of intra-section Div -->   
        </div><!-- End Of page-sub-setion Div -->
    </div><!-- End OF page-contant Div --> 
      
       
    
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
 