 @extends('layouts.theme')
@section('title', 'Permission')

@section('content')
<style type="text/css">
.checkedreciept {
  text-align: center;
    margin: 5px auto 10px auto!important;
    text-transform: uppercase;
    color: green!important;
  }
  .checkedreciept .fa {
    font-size: 50px;
    margin: 0;
    border: 2px solid green;
    border-radius: 50%;
    padding: 14px;
    color: green;
}
</style>
  <main role="main" class="inner cover mb-3 mb-auto">
  <div class="container">
  <div class="row">
  
  </div>
  </div>

<section class="mt-5" id="wrapper">
<div class="container">
   
        <div class="well reciept">
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-header d-flex align-items-center">
                  <h4><img src="{{ asset('theme/img/logo/eci-logo.png')}}" alt="" class="mr-3" style=" max-width: 40px;" />Election Commission Of India</h4>
                </div>
                <div class="card-body getpermission">
          				<p class="checkedreciept"><i class="fa fa-check"></i> <br />
          				Submission Successful
          				</p>
				          <small class="text-center" style="display: block;">Thankyou for submitting your application to the CEO,ECI <br />Your Application Details as Follows</small>
				          <br />
				          <hr class="row" />
				          <br />
                  @forelse ($detaildata as $user)
                  <div class="form-horizontal">
				              <div class="form-group row">
                          <label class="col-sm-4 form-control-label">Permission Request Id</label>
                          <div class="col-sm-8">
                            <p>{{$user->id}}</p>
                          </div>
                      </div> 
				   
				              <div class="form-group row">
                          <label class="col-sm-4 form-control-label">Name</label>
                          <div class="col-sm-8">
                            <p>{{ $user->name }}</p>
                          </div>
                      </div>  
				   
				              <div class="form-group row">
                          <label class="col-sm-4 form-control-label">Email</label>
                          <div class="col-sm-8">
                            <p>{{ $user->email }}</p>
                          </div>
                      </div> 
				   
				              <div class="form-group row">
                          <label class="col-sm-4 form-control-label">Mobile Number</label>
                          <div class="col-sm-8">
                            <p>{{ $user->mobileno }}</p>
                          </div>
                      </div>
				   
				              <div class="form-group row">
                          <label class="col-sm-4 form-control-label">Gender</label>
                          <div class="col-sm-8">
                            <p>{{ $user->gender }}</p>
                          </div>
                      </div> 
				              <div class="form-group row">
                          <label class="col-sm-4 form-control-label">Date Of Birth</label>
                          <div class="col-sm-8">
                            <p>{{ $user->dob }}</p>
                          </div>
                      </div>
				   
				              <div class="form-group row">
                          <label class="col-sm-4 form-control-label">State</label>
                          <div class="col-sm-8">
                            <p>{{ $user->ST_NAME }}</p>
                          </div>
                      </div> 

                      <div class="form-group row">
                          <label class="col-sm-4 form-control-label">District</label>
                          <div class="col-sm-8">
                            <p>{{ $user->DIST_NAME }}</p>
                          </div>
                      </div>

                      <div class="form-group row">
                          <label class="col-sm-4 form-control-label">Assembly Constituency</label>
                          <div class="col-sm-8">
                            <p>{{ $user->AC_NAME }}</p>
                          </div>
                      </div>

                      <div class="form-group row">
                          <label class="col-sm-4 form-control-label">Permission Type</label>
                          <div class="col-sm-8">
                            <p>{{ $user->permission_name }}</p>
                          </div>
                      </div>
                      <div class="form-group row">
                          <label class="col-sm-4 form-control-label">Event Place</label>
                          <div class="col-sm-8">
                            @if($user->location_name)
                            <p>{{ $user->location_name }}</p>
                            @else
                            <p>{{ $user->Other_location }}</p>
                            @endif
                          </div>
                      </div>
                      
                      <div class="form-group row">
                          <label class="col-sm-4 form-control-label">Permission Start Date & Time </label>
                          <div class="col-sm-8">
                            <p>{{ $user->date_time_start }}</p>
                          </div>
                      </div>
                      <div class="form-group row">
                          <label class="col-sm-4 form-control-label">Permission End Date & Time </label>
                          <div class="col-sm-8">
                            <p>{{ $user->date_time_end }}</p>
                          </div>
                      </div>
                    @empty
                        <p>No users</p>
                    @endforelse 
				          </div>
                </div>
              </div>
            </div>
          </div>
        </div>
</section>

</main>
@endsection