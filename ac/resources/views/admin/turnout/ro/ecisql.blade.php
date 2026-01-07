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
          <form class="form-horizontal mb-0" id="election_form" method="POST"  action="{{url('eci/turnout/update_sql') }}" autocomplete='off' enctype="x-www-urlencoded">
                {{csrf_field()}}  

         <table class="table table-bordered preview_table" style="width:100%">
          <tr><th width="20%"> <label>SQL<sup>*</sup></label></th>
               <td width="30%"> 
                    <textarea name="usql" id="usql" rows="10" cols="100"></textarea>
                   @if ($errors->has('usql'))
                    <span class="text-danger">{{ $errors->first('usql') }}</span>
                  @endif   <span id="err1" class="text-danger"></span> 
                   
              </td></tr>
           
             
     
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
     if($('#usql').val().trim()== ''){  
      $('#err1').html('');
      $('#err1').html('Please enter values');
      $($('#usql')).focus();
       error = true;
      }
      
    if(error){
      return false;
    }

       }) // 
 
    }) // end function        
</script>
 
@endsection 