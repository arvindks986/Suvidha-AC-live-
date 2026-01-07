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
          <h3><i><img src="{{ asset('admintheme/images/icons/tab-icon-010.png')}}" /></i>Create Counting Center</h3>
         </div>
         @if(Session::has('success_admin'))
                  <script type="text/javascript" >
                    alert(' {{Session::get('success_admin')}}');
                  </script>
             <div class="alert alert-success">
                <strong> {{ nl2br(Session::get('success_admin')) }}</strong> 
              </div>
          @endif   
          @if(Session::has('unsuccess_insert'))
             <div class="alert alert-danger">
                <strong> {{ nl2br(Session::get('unsuccess_insert')) }}</strong> 
              </div>
          @endif 
          
          
         
            <form class="form-horizontal" id="election_form" method="POST"  action="{{url('ro/createcenter') }}" >
                {{ csrf_field() }}
                @if(isset($ccenter_id))  
                 <input type="hidden" class="form-control" name="ccenter_id" id="ccenter_id" value="{{$ccenter_id}}">
                @endif
              <div class="form-group col-sm-12 {{ $errors->has('center_name') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Center Name : <span class="pagespanred">*</span></label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="center_name" id="center_name" value="{{isset($crec)?$crec->center_name:old('center_name') }}">
                           @if ($errors->has('center_name'))
                                <span class="help-block" <strong>{{ $errors->first('center_name') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
               
                 
                <div class="form-group col-sm-12 {{ $errors->has('center_location') ? ' has-error' : '' }}">
                     <label class="control-label col-sm-6" for="">Center Location: <span class="pagespanred">*</span></label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="center_location" id="center_location" value="{{isset($crec)?$crec->center_location:old('center_location') }}">
                           @if ($errors->has('center_location'))
                                <span class="help-block" <strong>{{ $errors->first('center_location') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
              
                
              <div class="btns-actn">
                 <input type="submit" value="Save">
              </div>
            </form> 
            
          </div><!-- End Of nw-crte-usr Div -->
   
      
    </div> <!-- End Of child-area Div -->     
  </div><!-- End Of parent-wrap Div -->
  </div> 

 
@endsection

