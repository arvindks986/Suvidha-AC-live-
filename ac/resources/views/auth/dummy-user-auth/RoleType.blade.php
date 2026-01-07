@extends('layouts.login')

@section('content')
<main>
   <section class="main-box">
    <div class="container-fluid">
     <div class="circle peach-gradient">
            <img src="{{ asset('theme/img/vendor/background.png') }}" alt=""></div>
    <!--LOG OUT BUTTON STARTS-->
      <a href="{{url('/candidatelogout')}}" class="btn btn-primary nav-link logout float-right mt-3"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a>
    <!--LOG OUT BUTTON ENDS-->
    <div class="row d-flex flex-column flex-md-row align-items-center" style="height:100vh;">
  
  
   <div class="col-md-6 login-page "> 
  <figure class="evm-logo">
  <span style="margin: auto;"><img class="eci-logo" src="{{ asset('theme/img/logo/eci-logo.png') }}"> <p>Election Commission of India</p> </span></figure> </div>
    <div class="col-md-6">
    <div class="login-right">
    
   
                <div class="d-flex align-items-center mb-3">
                  <h4>Select<span> Applicant</span> Type</h4>
                </div>
               
<form method="post" action="{{url('/candidate-permissionrole')}}">
   {{ csrf_field() }}
<div class="form-group">       
<select name="role_id" id="role_id"  class="form-control">
<option value="">Select Applicant Type</option>
@if(count($role_type)>0)
@foreach ($role_type as $key=>$police_list)
<option value="{{$police_list->role_id}}">{{$police_list->role_name}}</option>
@endforeach
@endif
</select>
<span class="text-danger">{{ $errors->first('role_id') }}</span>

</div>

<div class="form-group">       
<select name="party_id" id="role_id"  class="form-control">
<option value="">Select Political Party / Independent </option>
@if(count($role)>0)
@foreach ($role as $role)
<option value="{{$role->CCODE}}">{{$role->PARTYNAME}}</option>
@endforeach
@endif
</select>
<span class="text-danger">{{ $errors->first('party_id') }}</span>

</div>

<div class="form-group float-right">       
<input type="submit" class="btn btn-primary" name="submit">
<!--  <input type="submit" value="Login" >   -->                
</div>
</form>
               
              
    
    </div>    
    </div>    
    </div>
        </div>
   </section>
    <footer class="main-footer">
        <div class="container-fluid">
          <div class="row">
      <div class="col"></div>
            <div class="col">
              <figure class="foot-lft"><img src="{{ asset('theme/img/vendor/footer-img.png')}}"></figure>
            </div>
            <div class="col text-right">

				<a style="color:#bbb;float:right;" href="https://eci.gov.in/divisions-of-eci/ict-apps/" target="_blank">Made In ECI</a>
   
            </div>
          </div>
        </div>
      </footer>  
  </main>
@endsection
