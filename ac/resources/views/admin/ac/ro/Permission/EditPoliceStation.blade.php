@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content') 

  <section class="statistics">
        <div class="container-fluid mt-5 mb-5">
          <div class="row d-flex">
            <div class="col pl-0">
              <!-- Income-->
              <div class="card income text-center">
                <!-- <div class="icon"><i class="icon-line-chart"></i></div> -->
				<div class="card-body">
                <div class="text-success"><b>Police Station</b> &nbsp; <div class="btn-group float-right">
		<a type="button" href="{{url('/roac/permission/viewps')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/roac/permission/addps')}}" class="btn btn-sm btn-primary">Add</a>
				  </div></div></div>
              </div>
            </div>
<!--          <div class="col-lg-3 ">
               Income
              <div class="card income text-center">
                 <div class="icon"><i class="icon-line-chart"></i></div> 
                <div class="text-info"><b>Permission</b> &nbsp; <div class="btn-group float-right mt-2">
		<a type="button" href="{{url('/roac/permission/viewpermsn')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/roac/permission/addpermission')}}" class="btn btn-sm btn-primary">Add</a>
				  </div></div>
              </div>
            </div> -->
			<div class="col">
              <!-- Income-->
              <div class="card income">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
			   <div class="card-body">
                <div class="text-info"><b>Nodal</b> &nbsp; <div class="btn-group float-right">
		<a type="button" href="{{url('/roac/permission/viewauthority')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/roac/permission/addauthority')}}" class="btn btn-sm btn-primary">Add</a>
				  </div>				</div></div>
              </div>
            </div>
          <div class="col pr-0">
              <!-- Income-->
              <div class="card income">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
			   <div class="card-body">
                <div class="text-warning"><b>Location</b> &nbsp; <div class="btn-group float-right">
		<a type="button" href="{{url('/roac/permission/viewaddlocation')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/roac/permission/addlocation')}}" class="btn btn-sm btn-primary">Add</a>
				  </div></div> </div>   
              </div>
            </div>
          </div>
        </div>
      </section>



<section>
<div class="container-fluid">
<div class="row">
<div class="col-lg-12 p-0">
              <div class="card" style="max-width:700px; margin:0 auto;">
                <div class="card-header d-flex align-items-center">
                  <h5>Update Police Station</h5>
                </div>
                  @if (session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
               @endif
               @if (session('chckmessage'))
    <div class="alert alert-danger">
        {{ session('chckmessage') }}
    </div>
    @endif
                <div class="card-body">
                   @if(!empty($getpsdetails))
                   @foreach($getpsdetails as $data)
                  <form class="form-horizontal" action="{{url('/roac/permission/editps')}}" method="POST">
                      {{csrf_field()}}
                      <input type="hidden" name="psid"  value="{{$data->id}}">
                    <div class="form-group row">
                      <label class="col-sm-4 form-control-label">Police Station Name <sup>*</sup></label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" value="{{$data->police_st_name}}" name="ps_name">
                        <span class="text-danger">{{ $errors->error->first('ps_name') }}</span>
                      </div>
                    </div>
                    <div class="line"></div>
                    <div class="form-group row">
                      <label class="col-sm-4 form-control-label">Police Station Address <sup>*</sup></label>
                      <div class="col-sm-8">
			<textarea name="ps_addr" id="" cols="3" rows="2" class="form-control" name="ps_addr">{{$data->police_station_address}}</textarea>
                         <span class="text-danger">{{ $errors->error->first('ps_addr') }}</span>
                      </div>
                    </div>
                    <div class="line"></div>
                     <div class="form-group row">
					
                      <label class="col-sm-4 form-control-label">Incharge Name<sup>*</sup></label>
                      <div class="col-sm-8">
                          <input type="text" class="form-control" name="uname" value="{{$data->incharge_name}}">
                        <span class="text-danger">{{ $errors->first('uname') }}</span>
                      </div>
                     </div>
                    <div class="line"></div>
                    <div class="form-group row">
					
                      <label class="col-sm-4 form-control-label">Police Station Incharge Mobile No<sup>*</sup></label>
                      <div class="col-sm-8">
					<input type="tel" class="form-control" value="{{$data->police_st_incharge_no}}" name="ps_imb">
                        <span class="text-danger">{{ $errors->error->first('ps_imb') }}</span>
                      </div>
                     </div>
                 
					  <div class="form-group row">
                      <label class="col-sm-4 form-control-label">Police Station Mobile No<sup>*</sup></label>
                      <div class="col-sm-8">
			<input type="tel" class="form-control" name="ps_smb" value="{{$data->police_station_no}}">
                        <span class="text-danger">{{ $errors->error->first('ps_smb') }}</span>
                      </div>
                      </div>
                    
                 
                   
                   
                 
                    <div class="line"></div>
                   <div class="form-group row float-right">
                      <div class="col">
<!--                        <button type="submit" class="btn btn-secondary">Cancel</button>-->
                        <button type="submit" class="btn btn-primary" name="UpdatePS" value="Save">UPDATE</button>
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


@endsection