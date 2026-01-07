@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content') 
@include('admin.ac.deo.Permission.permission-master-menu')
<main role="main" class="inner cover mb-3 mb-auto">

<section class="mt-5" id="wrapper">
<div class="container">
<div class="row">
<div class="col-lg-12 p-0">
<div class="sidebar__inner">
              <div class="card"><!--  style="max-width:700px; margin:0 auto;" -->
                <div class="card-header d-flex align-items-center">
                  <h2>Update permission Cell Incharge</h2>
                </div>
                   @if (Session::has('message'))
                    <div class="alert alert-success">
                        {{ session()->get('message') }}
                    </div>
                   @endif
                              @if (session('chckmessage'))
    <div class="alert alert-danger">
        {{ session('chckmessage') }}
    </div>
    @endif
             <div class="card-body getpermission">
			
			 
			@if(!empty($getAllPCIData))
                        @foreach($getAllPCIData as $data)
                      <form class="form-horizontal" method="POST" action="{{url('/acdeo/editpci')}}">
                          {{csrf_field()}}
                        <div class="form-group row">
                            <label class="col-sm-4 form-control-label">Select Role <sup>*</sup></label>
                            <div class="col-sm-8">
                            <select name="pname" class="form-control" >
                                <option value="{{$data->role_id}}" selected="selected">{{$data->role_name}}</option>
                                @if(!empty($getPCIrole))
                                @foreach($getPCIrole as $pdata)
                                @if($data->role_id != $pdata->role_id)
                                <option value="{{$pdata->role_id}}" {{ (collect(old('pname'))->contains($pdata->role_id)) ? 'selected':'' }}>{{$pdata->role_name}}</option>
                                @endif
                                @endforeach
                                @endif
                            </select>
                            <span class="text-danger">{{ $errors->error->first('pname') }}</span>
                        </div>
                        </div>
						
			<div class="form-group row">
                          <label class="col-sm-4 form-control-label">Incahrge Name <sup>*</sup></label>
                          <div class="col-sm-8">
                            <input type="hidden" class="form-control" name="pci_id" value="{{$data->id}}">
                            <input type="hidden" class="form-control" name="pci_uname" value="{{$data->username}}">
                           <input type="text" class="form-control" placeholder="Enter Name" name="name" value="{{$data->name}}">
                           <span class="text-danger">{{ $errors->error->first('name') }}</span>
                          </div>
                        </div>
						
			
						
						<div class="form-group row">
                          <label class="col-sm-4 form-control-label">Incahrge Designation <sup>*</sup></label>
                          <div class="col-sm-8">
                           <input type="text" class="form-control" placeholder="Enter Designation" name="desig" value="{{$data->designation}}">
                           <span class="text-danger">{{ $errors->error->first('desig') }}</span>
                          </div>
                        </div>
						
						<div class="form-group row">
                          <label class="col-sm-4 form-control-label">Incahrge Mobile No <sup>*</sup></label>
                          <div class="col-sm-8">
                           <input type="text" class="form-control" placeholder="Enter Mobile Number" name="mb" value="{{$data->mobile}}">
                           <span class="text-danger">{{ $errors->error->first('mb') }}</span>
                          </div>
                        </div>						
						
						
						<div class="form-group row">
                          <label class="col-sm-4 form-control-label">Incahrge Email Id <sup>*</sup></label>
                          <div class="col-sm-8">
                           <input type="email" class="form-control" placeholder="Enter Email ID" name="email" value="{{$data->email}}">
                           <span class="text-danger">{{ $errors->error->first('email') }}</span>
                          </div>
                        </div>
                          <div class="form-group row">
                          <label class="col-sm-4 form-control-label">Department <sup>*</sup></label>
                          <div class="col-sm-8">
                           <input type="text" class="form-control" placeholder="Enter Department" name="dept" value="{{$data->department}}">
                           <span class="text-danger">{{ $errors->error->first('dept') }}</span>
                          </div>
                        </div>
                          
                          			<div class="form-group row">
                          <label class="col-sm-4 form-control-label">Address <sup>*</sup></label>
                          <div class="col-sm-8">
                          <textarea name="addr" class="form-control" placeholder="Add Address Here" id="" cols="3" rows="4">{{$data->address}}</textarea>
                          <span class="text-danger">{{ $errors->error->first('addr') }}</span>
                          </div>
                        </div>
							
					  
                      
                    </div>
					<div class="card-footer">
						     <div class="form-group row">
                         
                          <div class="col">
                           <button class="btn btn-success float-right" name="editpci" value="Update">UPDATE</button>
                          </div>
                        </div>
					</div>
                   </form>
                        @endforeach
                        @endif
					
              </div>
           </div>
            </div>
              
			  
			        
			  
			  
            </div>
</div>

</section>

</main>

@endsection