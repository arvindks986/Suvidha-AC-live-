@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content') 
<style>
 .statistics div[class*=col-] .card {
    padding: 20px 7px;
    }
    .card.income b {
    font-size: 15px;
</style>
 <section class="statistics">
        <div class="container-fluid mt-5 mb-5">
          <div class="row d-flex">
	<div class="col-lg-3">
              <!-- Income-->
              <div class="card income text-center">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Authority Type</b> &nbsp; <div class="btn-group float-right">
		  <a type="button" href="{{url('/acceo/viewauthority')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/acceo/addauthority')}}" class="btn btn-sm btn-primary">Add</a>
				  </div>				</div>
              </div>
            </div>
          <div class="col-lg-3">
              <!-- Income-->
              <div class="card income text-center">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Permission</b> &nbsp; <div class="btn-group float-right">
<!--					<button type="button" class="btn btn-sm btn-outline-primary">View</button>-->
                  <a type="button" href="{{url('/acceo/viewpermsn')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/acceo/addpermission')}}" class="btn btn-sm btn-primary">Add</a>
				  </div></div>
              </div>
            </div>
              
              <div class="col-lg-3">
              <!-- Income-->
              <div class="card income text-center">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Authority</b> &nbsp; <div class="btn-group float-right">
		  <a type="button" href="{{url('/acceo/viewnodals')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/acceo/addnodals')}}" class="btn btn-sm btn-primary">Add</a>
				  </div>				</div>
              </div>
            </div>
              
              <div class="col-lg-3">
              <!-- Income-->
              <div class="card income text-center">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Permission Date Restriction</b> &nbsp; <div class="btn-group float-right">
<!--					<button type="button" class="btn btn-sm btn-outline-primary">View</button>-->
                  <a type="button" href="{{url('/acceo/EditRestriction')}}" class="btn btn-sm btn-outline-primary">View</a>
				  </div></div>
              </div>
            </div> 
		
          </div>
        </div>
      </section>
    @if (session('chckmessage'))
    <div class="alert alert-danger">
        {{ session('chckmessage') }}
    </div>
    @endif
<section class="mt-5" id="wrapper">
<div class="container">
<div class="row">
<div class="col-lg-12 p-0">
<div class="sidebar__inner">
              <div class="card"><!--  style="max-width:700px; margin:0 auto;" -->
                <div class="card-header d-flex align-items-center">
                  <h2>ADD Authority</h2>
                </div>
                   @if (Session::has('message'))
                    <div class="alert alert-success">
                        {{ session()->get('message') }}
                    </div>
                   @endif
             <div class="card-body getpermission">
			
			 
			 
                      <form class="form-horizontal" method="POST" action="{{url('/acceo/addnodalsdata')}}">
                          {{csrf_field()}}
                        <div class="form-group row">
                          <label class="col-sm-4 form-control-label">Select Approving Authority<sup>*</sup> <br/><span class="text-danger">(Authority type will be added by CEO)</span> </label>
                          
                          <div class="col-sm-8">
                          <select class="form-control" name="authid">
                          <option value="0">Select Approving Authority</option>
                          @if(!empty($authority))
                            @foreach($authority as $data)
                            <option  value="{{$data->id}}" >
                                @if (!empty($data->name)) 
                                 {{$data->name}}
                                @endif
                            </option>
                            @endforeach
                            @endif
                         </select>
                         <span class="text-danger">{{ $errors->error->first('authid') }}</span>
                          </div>
                        </div> 
                         <div class="form-group row">
                          <label class="col-sm-4 form-control-label">Department <sup>*</sup></label>
                          <div class="col-sm-8">
                           <input type="text" class="form-control" placeholder="Enter Department" name="dept" value="{{ old('dept') }}">
                           <span class="text-danger">{{ $errors->error->first('dept') }}</span>
                          </div>
                        </div>
                          <div class="form-group row">
                          <label class="col-sm-4 form-control-label">Address <sup>*</sup></label>
                          <div class="col-sm-8">
                          <textarea name="addr" class="form-control" placeholder="Add Address Here" id="" cols="3" rows="4">{{old('addr')}}</textarea>
                          <span class="text-danger">{{ $errors->error->first('addr') }}</span>
                          </div>
                        </div>
						
			<div class="form-group row">
                          <label class="col-sm-4 form-control-label">Incharge Name <sup>*</sup></label>
                          <div class="col-sm-8">
                           <input type="text" class="form-control" placeholder="Enter Name" name="name" value="{{ old('name') }}">
                           <span class="text-danger">{{ $errors->error->first('name') }}</span>
                          </div>
                        </div>	
						
						<div class="form-group row">
                          <label class="col-sm-4 form-control-label">Incharge Designation <sup>*</sup></label>
                          <div class="col-sm-8">
                              <input type="text" class="form-control" placeholder="Enter Designation" name="desig" value="{{old('desig')}}">
                           <span class="text-danger">{{ $errors->error->first('desig') }}</span>
                          </div>
                        </div>
						
						<div class="form-group row">
                          <label class="col-sm-4 form-control-label">Incharge Mobile No <sup>*</sup></label>
                          <div class="col-sm-8">
                              <input type="text" class="form-control" placeholder="Enter Mobile Number" name="mb" value="{{old('mb')}}">
                           <span class="text-danger">{{ $errors->error->first('mb') }}</span>
                          </div>
                        </div>						
						
						
						<div class="form-group row">
                          <label class="col-sm-4 form-control-label">Incharge Email Id <sup>*</sup></label>
                          <div class="col-sm-8">
                              <input type="email" class="form-control" placeholder="Enter Email ID" name="email" value="{{old('email')}}">
                           <span class="text-danger">{{ $errors->error->first('email') }}</span>
                          </div>
                        </div>
						
						
						
<!--						<div class="form-group row">
                          <label class="col-sm-4 form-control-label">Epic No <sup>*</sup></label>
                          <div class="col-sm-8">
                              <input type="text" class="form-control" placeholder="Enter Epic Number" name="eno" value="{{old('eno')}}">
                           <span class="text-danger">{{ $errors->error->first('eno') }}</span>
                          </div>
                        </div>-->
						
					
							
							
							
					  
                      
                    </div>
					<div class="card-footer">
						     <div class="form-group row">
                         
                          <div class="col">
                           <button class="btn btn-success float-right" name="submit" value="ADD">ADD</button>
                          </div>
                        </div>
					</div>
                   </form>
					
              </div>
           </div>
            </div>
              
			  
			        
			  
			  
            </div>
</div>

</section>

@endsection