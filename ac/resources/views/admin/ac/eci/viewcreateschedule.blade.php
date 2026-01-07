@extends('admin.layouts.theme')
@section('title', 'Create Schedule')
@section('content') 
@include('admin.includes.script')
<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
   <div class="nw-crte-usr">
         <div class="head-title">
          <h3><i><img src="{{ asset('theme/images/icons/tab-icon-010.png')}}" /></i>Create Election Schedule</h3>
         </div>
         @if(Session::has('success_admin'))
             <div class="alert alert-success">
                <strong> {{ nl2br(Session::get('success_admin')) }}</strong> 
              </div>
          @endif   
          @if(Session::has('unsuccess_insert'))
             <div class="alert alert-danger">
                <strong> {{ nl2br(Session::get('unsuccess_insert')) }}</strong> 
              </div>
          @endif  
        <?php 
             $ev=\app(App\adminmodel\MELECMaster::class)->where(['ELEC_ID'=>$sdata->ELECTION_ID])->first(); 
            
        ?>    
            <form class="form-horizontal" id="election_form" method="POST"  action="{{url('eci/updateschedule') }}" >
                {{ csrf_field() }}   
                 <input type="hidden" name="SCHEDULEID" value="<?=$sdata->SCHEDULEID;?>">
            <div class="col-sm-12" > 
              <div class="form-group col-sm-6 {{ $errors->has('totalschedule') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Schedule / Phase of Election :</label>
                    <div class="col-sm-6">
                           <!--{{ Form::select('size', array('L' => 'Large', 'S' => 'Small')) }} -->
                      <select name="totalschedule" id="totalschedule" >
                       <option value=""> Select Schedule</option><option value="1" @if($sdata->SCHEDULENO=='1')  selected @endif>1</option>
                       <option value="2" @if($sdata->SCHEDULENO=='2')  selected @endif>2</option>
                       <option value="3" @if($sdata->SCHEDULENO=='3')  selected @endif>3</option>
                       <option value="4" @if($sdata->SCHEDULENO=='4')  selected @endif>4</option>
                       <option value="5" @if($sdata->SCHEDULENO=='5')  selected @endif>5</option>
                       <option value="6" @if($sdata->SCHEDULENO=='6')  selected @endif>6</option>
                      <option value="7" @if($sdata->SCHEDULENO=='7')  selected @endif>7</option>
                      <option value="8" @if($sdata->SCHEDULENO=='8')  selected @endif>8</option>
                      <option value="9" @if($sdata->SCHEDULENO=='9')  selected @endif>9</option>
                      <option value="10" @if($sdata->SCHEDULENO=='10')  selected @endif>10</option>
                      </select> 
                           @if ($errors->has('totalschedule'))
                                <span class="help-block" <strong>{{ $errors->first('totalschedule') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
               
                 
                <div class="form-group col-sm-6 {{ $errors->has('pressdate') ? ' has-error' : '' }}">
                     <label class="control-label col-sm-6" for="">Press Announcement Date:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="pressdate" id="pressdate" value="{ old('pressdate') }}{{date("m-d-Y",strtotime($sdata->DT_PRESS_ANNC))}} ">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                </div>
                <div class="col-sm-12" > 
               <div class="form-group col-sm-6 {{ $errors->has('st_date_nom') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Start Date of Nominations:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="st_date_nom" id="st_date_nom" value="{{old('st_date_nom')}}{{date("m-d-Y",strtotime($sdata->DT_ISS_NOM))}} ">
                           @if ($errors->has('st_date_nom'))
                                <span class="help-block" <strong>{{ $errors->first('st_date_nom') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->


                  <div class="form-group col-sm-6 {{ $errors->has('lt_date_nom') ? ' has-error' : '' }}">
                     <label class="control-label col-sm-6" for="">Last Date of Nominations:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="lt_date_nom" id="lt_date_nom" value="{{ old('lt_date_nom') }}{{date("m-d-Y",strtotime($sdata->LDT_IS_NOM))}} ">
                           @if ($errors->has('lt_date_nom'))
                                <span class="help-block" <strong>{{ $errors->first('lt_date_nom') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                </div><div class="col-sm-12" > 
               <div class="form-group col-sm-6 {{ $errors->has('scut_date_nom') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Scrutiny of Nomination Date:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="scut_date_nom" id="scut_date_nom" value="{{ old('scut_date_nom') }}{{date("m-d-Y",strtotime($sdata->DT_SCR_NOM))}} ">
                           @if ($errors->has('scut_date_nom'))
                                <span class="help-block" <strong>{{ $errors->first('scut_date_nom') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->


                  <div class="form-group col-sm-6 {{ $errors->has('with_date_nom') ? ' has-error' : '' }}">
                     <label class="control-label col-sm-6" for="">Last Date of Withdrawal:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="with_date_nom" id="with_date_nom" value="{{ old('with_date_nom') }}{{date("m-d-Y",strtotime($sdata->LDT_WD_CAN))}} ">
                           @if ($errors->has('with_date_nom'))
                                <span class="help-block" <strong>{{ $errors->first('with_date_nom') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                </div><div class="col-sm-12" > 
               <div class="form-group col-sm-6 {{ $errors->has('date_ofpoll') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Date Of Poll:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="date_ofpoll" id="date_ofpoll" value="{{ old('date_ofpoll') }}{{date("m-d-Y",strtotime($sdata->DATE_POLL))}} ">
                           @if ($errors->has('date_ofpoll'))
                                <span class="help-block" <strong>{{ $errors->first('date_ofpoll') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->


                  <div class="form-group col-sm-6 {{ $errors->has('date_of_counting') ? ' has-error' : '' }}">
                     <label class="control-label col-sm-6" for="">Counting Of Votes Date:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="date_of_counting" id="date_of_counting" value="{{ old('date_of_counting') }}{{date("m-d-Y",strtotime($sdata->DATE_COUNT))}} ">
                           @if ($errors->has('date_of_counting'))
                                <span class="help-block" <strong>{{ $errors->first('date_of_counting') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                </div><div class="col-sm-12" > 
               <div class="form-group col-sm-6 {{ $errors->has('completion_date') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Election Completion Date:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control"  name="completion_date" id="completion_date"value="{{ old('completion_date') }}{{date("m-d-Y",strtotime($sdata->DTB_EL_COM))}} ">
                           @if ($errors->has('completion_date'))
                                <span class="help-block" <strong>{{ $errors->first('completion_date') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                  
               </div>
              <div class="btns-actn">
                 <input type="submit" value="Update Schedule">
              </div>
            </form>  
          </div><!-- End Of nw-crte-usr Div -->
   
       <!--    Listing -->

<div class="page-contant">
      <div class="head-title">
              <h3><i><img src="{{ asset('theme/images/icons/tab-icon-002.png')}}" /></i>Election Schedule </h3>
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
                 <tr><th>Action</th><th>Phase No</th><th>Date of Press Announcement</th><th>St. Date of Nomination</th><th>Last Date of Nomination</th> 
                 <th>Date for Scrutiny</th><th>Last Date of Withdrawal </th><th>Date of Poll</th><th>Date of Counting</th>
                  <th>Election Completed Date</th> 
                 </tr>
               </thead>
               @foreach($list_schedule as $list)
                   
               <tr><td><a href="{{url('eci/updateschedule/'.$list->SCHEDULEID) }}">Edit</a></td>
                <td>{{ $list->SCHEDULENO }}</td><td>{{ date("d-m-Y",strtotime($list->DT_PRESS_ANNC)) }}</td> 
               <td>{{ date("d-m-Y",strtotime($list->DT_ISS_NOM)) }}</td><td>{{ date("d-m-Y",strtotime($list->LDT_IS_NOM)) }}</td><td>{{ date("d-m-Y",strtotime($list->DT_SCR_NOM)) }}</td><td>{{ date("d-m-Y",strtotime($list->LDT_WD_CAN)) }}</td>
                <td>{{ date("d-m-Y",strtotime($list->DATE_POLL)) }}</td><td>{{ date("d-m-Y",strtotime($list->DATE_COUNT)) }}</td><td>{{ date("d-m-Y",strtotime($list->DTB_EL_COM)) }}</td> 
                
              
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

@endsection