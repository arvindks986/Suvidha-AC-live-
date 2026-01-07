@extends('admin.layouts.ac.theme')
@section('bradcome', 'Estimate Turnout Open')
@section('content')
  
  <main role="main" class="inner cover mb-3">
    <section class="mt-3">
      <div class="container-fluid">
      <div class="row">
  
     
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"> <h4 class="mr-auto">Master Estimated Turnout </h4>  </div>  
                 
                 
                </div>
                </div>

   <div class="row">
    <div class="col">
          @if (session('success_mes'))
                  <div class="alert alert-success"> {{session('success_mes') }}</div>
              @endif
              @if (session('error_mes'))
                  <div class="alert alert-danger"> {{session('error_mes') }}</div>
              @endif
            @if (session('error_mes1'))
                  <div class="alert alert-danger"> {{session('error_mes1') }}</div>
              @endif
            @if(!empty($errors->first()))
              <div class="alert alert-danger"> <span>{{ $errors->first() }}</span> </div>
             @endif
          
         @if(Session::has('success_admin'))
             <div class="alert alert-success">
                <strong> {{ nl2br(Session::get('success_admin')) }}</strong> 
              </div>
          @endif

         
    </div>
    </div>
   
       
    <div class="card-body">
          <form class="form-horizontal mb-0" id="election_form" method="POST"  action="{{url('eci/turnout/update_turnout') }}" autocomplete='off' enctype="x-www-urlencoded">
                {{csrf_field()}}  
                  
          <input type="hidden" name="id" value="{{$master->id}}">
         <table class="table table-bordered preview_table" style="width:100%">
          <tr><th width="20%"> <label>Election ID<sup>*</sup></label></th>
               <td width="30%"> 
                   <select name="election_id" id="election_id" class="form-control">
                     <option value=""> -- Select Election-- </option>
                      @if(isset($election))
                        @foreach($election as $val)
                          <option value="{{$val}}" 
                          @if($master->election_id==$val) selected="selected" @endif>{{$val}}</option>
                        @endforeach 
                         @endif
                   </select>
                   @if ($errors->has('election_id'))
                    <span class="text-danger">{{ $errors->first('election_id') }}</span>
                  @endif   <span id="err1" class="text-danger"></span> 
                   
              </td></tr>
           
          <tr><th width="20%"> <label>Phase Number<sup>*</sup></label></th>
               <td width="30%"> 
                   <select name="phase_no" id="phase_no" class="form-control">
                     <option value=""> -- Select phase-- </option>
                      @if(isset($phase))
                        @foreach($phase as $val)
                          <option value="{{$val}}" @if($master->phase_no==$val) selected="selected" @endif>{{$val}}</option>
                        @endforeach 
                         @endif
                   </select>
                   @if ($errors->has('phase_no'))
                    <span class="text-danger">{{ $errors->first('phase_no') }}</span>
                  @endif   <span id="err2" class="text-danger"></span> 
                   
              </td> </tr>
          <tr><th width="20%"> <label>Schedule ID<sup>*</sup></label></th>
               <td width="30%"> 
                   <select name="sechudle_id" id="sechudle_id" class="form-control">
                     <option value=""> -- Select Schedule-- </option>
                      @if(isset($schedule))
                        @foreach($schedule as $val)
                          <option value="{{$val}}" @if($master->sechudle_id==$val) selected="selected" @endif>{{$val}}</option>
                        @endforeach 
                         @endif
                   </select>
                   @if ($errors->has('sechudle_id'))
                    <span class="text-danger">{{ $errors->first('sechudle_id') }}</span>
                  @endif   <span id="err3" class="text-danger"></span> 
                   
              </td> </tr>  
            <tr><th width="20%"> <label>Poll Date<sup>*</sup></label></th>
               <td width="30%"> 
                   <select name="poll_date" id="poll_date" class="form-control">
                     <option value=""> -- Select Polldate-- </option>
                      @if(isset($polldate))
                        @foreach($polldate as $val)
                          <option value="{{$val}}" @if($master->poll_date==$val) selected="selected" @endif>{{$val}}</option>
                        @endforeach 
                         @endif
                   </select>
                   @if ($errors->has('poll_date'))
                    <span class="text-danger">{{ $errors->first('poll_date') }}</span>
                  @endif   <span id="err4" class="text-danger"></span> 
                   
              </td> </tr>    
     
    </table>
        <div class="form-group float-right"> 
        <input type="submit" name="Update" id="saverec" class="btn btn-primary custombtn">
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
<script type="text/javascript">
   $(document).ready(function(){   

    $('#saverec').click(function(){
       error = false;
     if($('#election_id').val().trim()== ''){  
      $('#err1').html('');
      $('#err1').html('Please select Election ID');
      $($('#election_id')).focus();
       error = true;
      }
     if($('#phase_no').val().trim()== ''){  
      $('#err2').html('');
      $('#err2').html('Please select Phase');
      $($('#phase_no')).focus();
       error = true;
      }
    if($('#sechudle_id').val().trim()== ''){  
      $('#err3').html('');
      $('#err3').html('Please select Schedule');
      $($('#sechudle_id')).focus();
       error = true;
      }
    if($('#poll_date').val().trim()== ''){  
      $('#err4').html('');
      $('#err4').html('Please select Poll Date');
      $($('#poll_date')).focus();
       error = true;
      }
    if(error){
      return false;
    }

       }) // 
 
    }) // end function        
</script>
 
@endsection 