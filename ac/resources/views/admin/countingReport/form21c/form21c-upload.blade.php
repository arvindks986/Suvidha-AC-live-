@extends('admin.layouts.ac.theme')
@section('title', 'Suvidha AC')
@section('bradcome', 'Form 21')
@section('content')
<style type="text/css">
.Pdf-container {width:800px; background: #fff; margin: 0 auto;}
section.pdfDoc table { font-family: serif;}
section.pdfDoc table.table td{padding:5px;border: 1px solid #000;border-top: 0;}
section.pdfDoc table td {padding: 4px 0;font-size: 18px;}
section.pdfDoc table h1{font-size: 36px;  font-weight: 700;}
section.pdfDoc table h2 {font-size: 24px;  font-weight: 800;   color: #000;}
section.pdfDoc table th {font-size: 20px;  font-weight: 800;}
.showname {font-size: 18px; margin-left: 10px; margin-right:10px; font-weight:bold;    text-decoration-style: solid; border-bottom: 1px solid #000;}
</style>
<script type="text/javascript" src="{{ asset('public/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('public/js/FileSaver.js') }}"></script>
<script type="text/javascript" src="{{ asset('public/js/jquery.wordexport.js') }}"></script>
<div class="line"></div>
<section>
<div class="container-fluid">
<div class="row">
<div class="col-lg-12 p-0">
              <div class="card" style="max-width:700px; margin:0 auto;">
                <div class="card-header d-flex align-items-center">
                  <h5>Form 21</h5>
                </div>
                    <div class="card-body">
                    <form id="uploadForm" class="form-horizontal" action="{{url('/roac/form-21-report-upload')}}" method="POST" enctype="multipart/form-data">
					{{ csrf_field() }}
					
                    <div class="form-group row">
						<label class="col-sm-4 form-control-label">Select Form Type <sup>*</sup></label>
					   <div class="col-sm-8">
                        <select name="form_type" id="form_type" class="form-control">
                        	<option value="">Select form type</option>
                                <option value="21c" selected="">Form 21 C</option>
                        	<option value="21d">Form 21 D</option>
                        </select>
                                               <span class="text-danger error1" style="font-size:15px;">&nbsp;</span>
                      </div>
                      <label class="col-sm-4 form-control-label">Select File <sup>*</sup></label>
                      <div class="col-sm-8">
                        <input type="file" name="form21" id="form21" accept=".pdf"><br>
                        <span class="text-danger error2" style="font-size:15px;">{!! Session::has('emsg') ? Session::get("emsg") : '' !!}</span>
						<span class="text-success" style="font-size:15px;">{!! Session::has('smsg') ? Session::get("smsg") : '' !!}</span>
						@if ($errors->has('form21'))
						<span class="text-danger" style="font-size:15px;">
							{{ $errors->first('form21') }}
						</span>
						@endif
                      </div>
                    </div>
                    <div class="line"></div>
                   <div class="form-group row float-right">
                      <div class="col">
<!--                        <button type="submit" class="btn btn-secondary">Cancel</button>-->
                        <button type="submit" class="btn btn-primary" name="UpdatePS" value="Save">UPLOAD</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
</div>
</div>

</section>

</div> 
<script>
$(document).ready(function($) {
  setTimeout(function(){$(".text-success").text("");}, 3000);

  $('#uploadForm').submit(function(e) {
	  var form_type = $("#form_type").val();
	  if(form_type==''){
		  $(".error1").text("Please select form type.");
		  return false;
	  }else{
		  $(".error1").text("");
	  }
	  var form21 = $("#form21").val();
	  if(form21==''){
		  $(".error2").text("Please select a file.");
		  return false;
	  }
	  var fileext = form21.split('.').pop();
	  fileext = fileext.toLowerCase();
	  if(fileext != 'pdf'){
		$(".error2").text("Please select only pdf file.");
		return false;  
	  }else{
		  $(".error2").text("");  
	  }
	  var file_type = document.getElementById("form21").files[0].type;
	  
	  if(file_type != 'application/pdf'){
		$(".error2").text("Please select valid pdf file.");
		return false;    
	  }else{
		  $(".error2").text("");  
	  }
	  var file_size = document.getElementById("form21").files[0].size;
	  file_size = bytesToSize(file_size).split(' ');
	  
	  if(file_size[0] >2 && file_size[1]=='MB'){
		$(".error2").text("Please select file size less than or equal to 2 MB.");
		return false;    
	  }else{
		  $(".error2").text("");  
	  }
	  
	  //return false;
  });
});

function bytesToSize(bytes) {
   var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
   if (bytes == 0) return '0 Byte';
   var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
   return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
};

</script>
@endsection
