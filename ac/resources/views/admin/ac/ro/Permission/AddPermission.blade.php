@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content') 

<main role="main" class="inner cover mb-3 mb-auto">
    @if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
    @endif
    @if(count($errors->error))
    <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.
            <br/>
            <ul>
                    @foreach($errors->error->all() as $erro)
                    <li>{{ $erro }}</li>
                    @endforeach
            </ul>
    </div>
@endif
    <section class="statistics">
        <div class="container-fluid mt-5 mb-5">
          <div class="row d-flex">
            <div class="col-lg-3 pl-0">
              <!-- Income-->
              <div class="card income text-center">
                <!-- <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-success"><b>Police Station</b> &nbsp; <div class="btn-group float-right">
		  <a type="button" href="{{url('/roac/permission/viewps')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/roac/permission/addps')}}" class="btn btn-sm btn-primary">Add</a>
				  </div></div>
              </div>
            </div>
          <div class="col-lg-3 ">
              <!-- Income-->
              <div class="card income text-center">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Permission</b> &nbsp; <div class="btn-group float-right mt-2">
		  <a type="button" href="{{url('/roac/permission/viewpermsn')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/roac/permission/addpermission')}}" class="btn btn-sm btn-primary">Add</a>
				  </div></div>
              </div>
            </div> 
			<div class="col-lg-3 ">
              <!-- Income-->
              <div class="card income text-center">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Authority</b> &nbsp; <div class="btn-group float-right mt-2">
	          <a type="button" href="{{url('/roac/permission/viewauthority')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/roac/permission/addauthority')}}" class="btn btn-sm btn-primary">Add</a>
				  </div>				</div>
              </div>
            </div>
          <div class="col-lg-3 pr-0">
              <!-- Income-->
              <div class="card income text-center">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-warning"><b>Location</b> &nbsp; <div class="btn-group float-right mt-2">
		<a type="button" href="{{url('/roac/permission/viewaddlocation')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/roac/permission/addlocation')}}" class="btn btn-sm btn-primary">Add</a>
				  </div></div>   
              </div>
            </div>
          </div>
        </div>
      </section>
    <section class="mt-5" id="wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <div class="sidebar__inner">
                        <div class="card"><!--  style="max-width:700px; margin:0 auto;" -->
                            <div class="card-header d-flex align-items-center">
                                <h2>Add Permission</h2>
                            </div>
                            <div class="card-body getpermission">



                                <form class="form-horizontal" method="POST" action="{{url('/roac/permission/AddPermissionData')}}" enctype="multipart/form-data">
                                    {{csrf_field()}}
                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Permission Name <sup>*</sup></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="pname" value="{{old('pname')}}">
                                            <span class="text-danger">{{ $errors->error->first('pname') }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Approval Required from Authority <sup>*</sup></label>
                                        <div class="col-sm-8">
                                            <label class="checkbox-inline mr-3">
                                                <input id="inlineCheckbox1" type="checkbox" name="police" value="1" checked> Police
                                                <span class="text-danger">{{ $errors->error->first('police') }}</span>
                                            </label>
                                            <label class="checkbox-inline mr-3">
                                                <input id="inlineCheckbox2" type="checkbox" name="fd" value="2"> Fire Departement
                                                <span class="text-danger">{{ $errors->error->first('fd') }}</span>
                                            </label>
                                            <label class="checkbox-inline mr-3">
                                                <input id="inlineCheckbox3" type="checkbox" name="rd" value="3"> Revenue Department 
                                                <span class="text-danger">{{ $errors->error->first('rd') }}</span>
                                            </label>  
                                            <label class="checkbox-inline">
                                                <input id="inlineCheckbox3" type="checkbox" name="pwd" value="4"> PWD
                                                <span class="text-danger">{{ $errors->error->first('pwd') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div id="dynamic_field" >
                                    <div class="form-inline" >
                                        <label class="sr-only" for="inlineFormInputName2">Document Name</label>
                                        <input type="text" name="doc[0][Dname]" value="{{old('doc.0.Dname]')}}" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2" placeholder="Document Name">
                                        <span class="text-danger">{{ $errors->error->first('doc.0.Dname') }}</span>

                                        <label class="sr-only" for="inlineFormInputGroupUsername2">Size (in KB)</label>
                                        <div class="input-group mb-2 mr-sm-2">								
                                            <input type="text" name="doc[0][fsize]" class="form-control" id="inlineFormInputGroupUsername2" placeholder="Size (in KB)">
                                             <span class="text-danger">{{ $errors->error->first('doc.0.fsize') }}</span>
                                        </div>

	

                                        <div class="form-check mb-2 mr-sm-2">
                                            <input class="form-check-input" type="checkbox" name="doc[0][chck]" value="1" id="inlineFormCheck">
                                            <label class="form-check-label" for="inlineFormCheck">
                                                Mandatory
                                            </label>
                                        </div>
										
										<div class="file-upload">
											<div class="file-select">
											<div class="file-select-name" id="noFile">No file chosen...</div> 
											<input type="file" name="doc[0][format]" id="customFile" class="custom-file-input affidavit form-control mr-auto" accept=".pdf">
											<div class="file-select-button customchoose" id="fileName">Choose File</div>
											 <span class="text-danger">{{ $errors->error->first('doc.0.format') }}</span>
											</div>
										</div>
										
										
                                        <div class="mr-sm-3">
                                            <div class="custom-file browsebtn  mb-3">
                                                <input type="file" name="doc[0][format]" class="custom-file-input" id="customFile" name="filename">
                                                <label class="custom-file-label" for="customFile">Choose file</label>
                                                 <span class="text-danger">{{ $errors->error->first('doc.0.format') }}</span>
                                            </div>
                                        </div>

                                        <button type="button" class="btn btn-primary btn-submit mb-2" id="add">Add New</button>
                                    </div>	
                                    </div>	

<!--                                    <div class="form-inline">
                                        <label class="sr-only" for="inlineFormInputName2">Document Name</label>
                                        <input type="text" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2" placeholder="Document Name">

                                        <label class="sr-only" for="inlineFormInputGroupUsername2">Size (in KB)</label>
                                        <div class="input-group mb-2 mr-sm-2"><input type="text" class="form-control" id="inlineFormInputGroupUsername2" placeholder="Size (in KB)"></div>

                                        <div class="form-check mb-2 mr-sm-2">
                                            <input class="form-check-input" type="checkbox" id="inlineFormCheck">
                                            <label class="form-check-label" for="inlineFormCheck">
                                                Mandatory
                                            </label>
                                        </div>
                                        <div class="mr-sm-3">
                                            <div class="custom-file browsebtn  mb-3">
                                                <input type="file" class="custom-file-input" id="customFile" name="filename">
                                                <label class="custom-file-label" for="customFile">Choose file</label>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-warning btn-submit mb-2">Remove</button>
                                    </div>-->
                            </div>
                            <div class="card-footer">
                                <div class="form-group row">

                                    <div class="col">
                                        <button class="btn btn-success float-right">Submit</button>
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

</main>
@endsection
@section('script')
<script type="text/javascript">
    $(function(){
        var i=0;  
      $('#add').click(function(){ 
           i++;
       $('#dynamic_field').append('<div class="form-inline dynamic-added" id="row'+i+'">\n\
   <label class="sr-only" for="inlineFormInputName2">Document Name</label>\n\
  <input type="text" name="doc['+i+'][Dname]" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2" placeholder="Document Name">\n\
<label class="sr-only" for="inlineFormInputGroupUsername2">Size (in KB)</label>\n\
<div class="input-group mb-2 mr-sm-2"><input type="text" name="doc['+i+'][fsize]" class="form-control" id="inlineFormInputGroupUsername2" placeholder="Size (in KB)"></div>\n\
<div class="form-check mb-2 mr-sm-2"> <input class="form-check-input" type="checkbox" name="doc['+i+'][chck]" value="1" id="inlineFormCheck"><label class="form-check-label" for="inlineFormCheck">Mandatory</label></div>\n\
<div class="mr-sm-3"><div class="custom-file browsebtn  mb-3"><input type="file" name="doc['+i+'][format]" class="custom-file-input" id="customFile" name="filename"><label class="custom-file-label" for="customFile">Choose file</label></div></div>\n\
<button type="button" name="remove" class="btn btn-warning btn-submit mb-2 btn_remove" id="'+i+'">Remove</button></div>\n\
   '); 
      });  


      $(document).on('click', '.btn_remove', function(){  
           var button_id = $(this).attr("id");   
           $('#row'+button_id+'').remove();  
      }); 
       $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
    });
    
</script>
 
@endsection