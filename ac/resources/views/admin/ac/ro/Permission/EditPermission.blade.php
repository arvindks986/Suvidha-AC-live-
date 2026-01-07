@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content') 

<main role="main" class="inner cover mb-3 mb-auto">
    @if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
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
                                <h2>Update Permission</h2>
                            </div>
                            <div class="card-body getpermission">


                                @if(!empty($getpermsndetails))
                                @foreach($getpermsndetails as $data)
                                <form class="form-horizontal" method="POST" action="{{url('/roac/permission/editpermsn')}}" enctype="multipart/form-data">
                                    {{csrf_field()}}
                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Permission Name <sup>*</sup></label>
                                        <div class="col-sm-8">
                                            <input type="hidden" name="p_id" value="{{$data->p_id}}" />
                                            <input type="text" class="form-control" name="pname" value="{{$data->pname}}">
                                            <span class="text-danger">{{ $errors->error->first('pname') }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Approval Required from Authority <sup>*</sup></label>
                                        @if(!empty($data->authority_type_id))
                                        @php 
                                          $auth_name=explode(',',$data->authority_type_id);
                                        @endphp
                                        <div class="col-sm-8">
                                            @for($i=0;$i<=count($auth_name);$i++)
                                            @if(!empty($auth_name[$i]) && $auth_name[$i] != '')
                                            @if($auth_name[$i] == '1')
                                             <label class="checkbox-inline mr-3">
                                                <input id="inlineCheckbox1" type="checkbox" name="police" value="1" checked="checked"> Police
                                                <span class="text-danger">{{ $errors->error->first('police') }}</span>
                                            </label>
                                             @elseif($auth_name[$i] == 2)
                                             <label class="checkbox-inline mr-3">
                                                <input id="inlineCheckbox2" type="checkbox" name="fd" value="2" checked="checked"> Fire Departement
                                            </label>
                                             @elseif($auth_name[$i] == 3)
                                             <label class="checkbox-inline mr-3">
                                                <input id="inlineCheckbox3" type="checkbox" name="rd" value="3" checked="checked"> Revenue Department 
                                            </label>
                                             @elseif($auth_name[$i] == 4)
                                             <label class="checkbox-inline">
                                                <input id="inlineCheckbox3" type="checkbox" name="pwd" value="4" checked="checked"> PWD
                                            </label>
                                             @endif
                                             
                                             @endif
                                             @endfor
                                             
                                             @if(in_array('1',$auth_name)== false)
                                             <label class="checkbox-inline mr-3">
                                                <input id="inlineCheckbox1" type="checkbox" name="police" value="1"> Police
                                                <span class="text-danger">{{ $errors->error->first('police') }}</span>
                                            </label>
                                             @endif
                                             @if(in_array('2',$auth_name)== false)
                                              <label class="checkbox-inline mr-3">
                                                <input id="inlineCheckbox2" type="checkbox" name="fd" value="2" > Fire Departement
                                            </label>
                                             @endif
                                             @if(in_array('3',$auth_name)== false)
                                             <label class="checkbox-inline mr-3">
                                                <input id="inlineCheckbox3" type="checkbox" name="rd" value="3" > Revenue Department 
                                            </label>
                                             @endif
                                             @if(in_array('4',$auth_name)== false)
                                              <label class="checkbox-inline">
                                                <input id="inlineCheckbox3" type="checkbox" name="pwd" value="4" > PWD
                                            </label>
                                             @endif
                                             
                                            
                                        </div>
                                        @endif
                                    </div>
                                     @endforeach
                                     @endif
                                    @php $d=0; @endphp
                                    
                                    <div class="form-inline" id="dynamic_field">
                                        @if(!empty($getpermsndocdetails))
                                        @foreach($getpermsndocdetails as $docdata)
                                        <input type="hidden" name="doc[{{$d}}][doc_id]" value="{{$docdata->doc_id}}" >
                                        <label class="sr-only" for="inlineFormInputName2">Document Name</label>
                                        <input type="text" name="doc[{{$d}}][Dname]" value="{{$docdata->doc_name}}" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2" placeholder="Document Name">

                                        <label class="sr-only" for="inlineFormInputGroupUsername2">Size (in KB)</label>
                                        <div class="input-group mb-2 mr-sm-2">								
                                            <input type="text" name="doc[{{$d}}][fsize]" value="{{$docdata->doc_size}}" class="form-control" id="inlineFormInputGroupUsername2" placeholder="Size (in KB)">
                                        </div>

                                        <div class="form-check mb-2 mr-sm-2">
                                            @if($docdata->required_status == 1)
                                            <input class="form-check-input" type="checkbox" name="doc[{{$d}}][chck]" value="1" id="inlineFormCheck" checked>
                                            @else
                                            <input class="form-check-input" type="checkbox" name="doc[{{$d}}][chck]" value="1" id="inlineFormCheck" >
                                            @endif
                                            <label class="form-check-label" for="inlineFormCheck">
                                                Mandatory
                                            </label>
                                        </div>
                                        <div class="mr-sm-3">
                                            <div class="custom-file browsebtn  mb-3">
                                                <input type="file" name="doc[{{$d}}][format]" class="custom-file-input" id="customFile" name="filename">
                                                <label class="custom-file-label" for="customFile">Choose file</label>
                                            </div>
                                        </div>
                                        @php $d++; @endphp
                                    @endforeach
                                    @endif
                                        </div>
                                    
                                        <button type="button" class="btn btn-primary btn-submit mb-2 text-right" id="add">Add New</button>
                                    	

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
                                        <button class="btn btn-success float-right" name="UpdatePermission" value="update">UPDATE</button>
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
        var i=<?php echo $d; ?>;  
      $('#add').click(function(){ 
       $('#dynamic_field').append('<div class="form-inline dynamic-added" id="row'+i+'">\n\
   <label class="sr-only" for="inlineFormInputName2">Document Name</label>\n\
  <input type="text" name="doc['+i+'][Dname]" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2" placeholder="Document Name">\n\
<label class="sr-only" for="inlineFormInputGroupUsername2">Size (in KB)</label>\n\
<div class="input-group mb-2 mr-sm-2"><input type="text" name="doc['+i+'][fsize]" class="form-control" id="inlineFormInputGroupUsername2" placeholder="Size (in KB)"></div>\n\
<div class="form-check mb-2 mr-sm-2"> <input class="form-check-input" type="checkbox" name="doc['+i+'][chck]" value="1" id="inlineFormCheck"><label class="form-check-label" for="inlineFormCheck">Mandatory</label></div>\n\
<div class="mr-sm-3"><div class="custom-file browsebtn  mb-3"><input type="file" name="doc['+i+'][format]" class="custom-file-input" id="customFile" name="filename"><label class="custom-file-label" for="customFile">Choose file</label></div></div>\n\
<button type="button" name="remove" class="btn btn-warning btn-submit mb-2 btn_remove" id="'+i+'">Remove</button></div>\n\
   '); 
    i++;
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