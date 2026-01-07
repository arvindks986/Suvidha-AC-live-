 @extends('admin.layouts.ac.theme')
@section('title', 'Online Nomination Details')
@section('content')
  <?php   $url = URL::to("/"); $j=0; ?>
  <link href="{{ asset('theme/main.css') }}" rel="stylesheet">
   
  <main role="main" class="inner cover mb-3">
  <section class="bg-scroll">
<div class="container">
   <div class="row">
     <div class="col pr-0 pl-0">
                 <ul class="steps mb-0" id="progressbar">
                  <li class="step">Personal Details</li>
                  <li class="step active">Election Details</li>
                  <li class="step">Part I/II</li>
                  <li class="step">Part III<span></span></li>
                  <li class="step">Part IIIA<span></span></li>
                 <!-- <li class="step">Upload Affidavit<span></span></li>
                     <li class="step">Finalize Application<span></span></li> -->
                </ul> 
                 
    </div>
    </div>
</div>
</section>
<section class="mt-1">
  <div class="container">
       
  <div class="row">
            
  <div class="card mt-3" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h4>{!! $heading_title !!}</h4>
                 </div>  
                <div class="col">
                        <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st_name}} </span> &nbsp;&nbsp; <b class="bolt">AC Name:</b> 
                        <span class="badge badge-info"> {{$ac_name}}</span>&nbsp;&nbsp;  </p>
                </div>
         
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
        @if (session('success'))
           <div class="alert alert-success"> {{session('success') }}</div>
        @endif
            @if(!empty($errors->first()))
                <div class="alert alert-danger"> <span>{{ $errors->first() }}</span> </div>
            @endif 
         
    </div>
    </div>
   <div class="card-body">  

<!-- <div class="container-fluid">
   <div class="col-md-12 mt-3">
     <ul style="text-align:center;margin-bottom:40px;" class="arrow-steps clearfix">
      <li class="step step1">Personal Details</li>
      <li class="step step2 current first">Election Details</li>
       <li class="step step3">Part I/II</li>
       <li class="step step4">Part III<span></span></li>
       <li class="step step5">Part IIIA<span></span></li>
       <!-- <li class="step step4">Upload Affidavit<span></span></li>
       <li class="step step4">Finalize Application<span></span></li> -->
    <!--  </ul>
 </div>

</div> --> 


  <section>
    <div class="container p-0">
      <form id="election_form" method="POST"  action="{{$action}}" autocomplete='off' enctype="x-www-urlencoded">
        @if(isset($nomination_id) && $nomination_id)
        <input type="hidden" name="nomination_id" value="{{$nomination_id}}">
        <input type="hidden" name="candidate_id" value="{{$candidate_id}}">
        @endif
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <!-- <div class="card-header d-flex align-items-center">
                <div class="col-md-7">
                
                </div> -->
                <!-- @if(isset($reference_id) && isset($href_download_application))
                <div class="col-md-5">
                  <ul class="list-inline float-right">
                    <li class="list-inline-item text-right">Reference ID: <b style="text-decoration: underline;">{{$reference_id}}</b></li>
                    <li class="list-inline-item text-right"><a href="{!! $href_download_application !!}" class="btn btn-primary" target="_blank">Download Application</a></li>
                  </ul>
                </div>
                @endif -->
              <!-- </div> -->
              <div class="card-body">

                <input type="hidden" name="_token" value="{{csrf_token()}}">

                 <div class="form-group row">
                  <div class="col-sm-2"><label for="statename">Election Type <sup>*</sup></label></div>
                  <div class="col">
                    <div class="" style="width:100%;">
                      <select name="election_id" class="form-control" id="election_id">
                        <option value="{{$ele_details->ELECTION_ID}}" selected="selected"> {{$name}}</option>
                        

                      </select>
                      @if ($errors->has('election_id'))
                      <span class="error">{{ $errors->first('election_id') }}</span>
                      @endif 
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <div class="col-sm-2"><label for="statename">State Name <sup>*</sup></label></div>
                  <div class="col">
                    <div class="" style="width:100%;">
                      <select name="st_code" class="form-control" id="st_code1">
                       <option value="{{$st->ST_CODE}}" selected="selected"> {{$st->ST_NAME}}</option>
                        
                      </select>
                      @if ($errors->has('st_code'))
                      <span class="error">{{ $errors->first('st_code') }}</span>
                      @endif 
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <div class="col-sm-2"><label for="statename">AC <sup>*</sup></label></div>
                  <div class="col">
                    <div class="" style="width:100%;">
                      <select name="ac_no" class="consttype form-control" id="ac_no1">
                        <option value="{{$ac->AC_NO}}" selected="selected"> {{$ac->AC_NAME}}</option>
                      </select>
                      @if ($errors->has('ac_no'))
                      <span class="error">{{ $errors->first('ac_no') }}</span>
                      @endif
                    </div>
                  </div>
                </div>




              </div>
        <div class="card-footer">
   <div class="form-group row float-right">       
  <div class="col">
    <button type="submit" id="save" name="save_only" class="btn btn-primary">Save</button>
   <button type="submit" id="candnomination" class="btn btn-primary">Save & Next</button>
 </div>
</div>
</div> 

            </div>
          </div>
        </div>
      </form>
    </div>
  </section>
</div>
</div>
</div>
</div>
</section>
</main>
  @endsection
  @section('script')
  <script type="text/javascript" src="{{ asset('admintheme/js/jquery-ui.js') }}"></script>
  <script>
    $(document).ready(function(){  

      if($('#breadcrumb').length){
        var breadcrumb = '';
        $.each({!! json_encode($breadcrumbs) !!},function(index, object){
          breadcrumb += "<li><a href='"+object.href+"'>"+object.name+"</a></li>";
        });
        $('#breadcrumb').html(breadcrumb);
      }
    });

    function filter_respective_state(election_id){
      html = '';
      html += "<option value=''>Select</option>";
      var states = <?php echo json_encode($states); ?>;
      var st_code = "<?php echo $st_code ?>";
      $.each(states, function(index, object){
        if(object.election_id == election_id){
          if(object.st_code == st_code){
            html += "<option value='"+object.st_code+"' selected='selected'>"+object.st_name+"</option>";
          }else{
            html += "<option value='"+object.st_code+"'>"+object.st_name+"</option>";
          }
        }
      });
      $("#st_code").empty().append(html);
      if(st_code == ''){
        $("#st_code").val($("#st_code option:first").val());
      }
    }

    function filter_respective_acs(st_code){
      html = '';
      html += "<option value=''>Select</option>";
      var acs = <?php echo json_encode($acs); ?>;
      var election_id = $('#election_id').val();
      var st_code = $('#st_code').val();
      var ac_no = "<?php echo $ac_no; ?>";
      $.each(acs, function(index, object){
        if(object.st_code == st_code && object.election_id == election_id){
          if(object.ac_no == ac_no){
            html += "<option value='"+object.ac_no+"' selected='selected'>"+object.ac_name+"</option>";
          }else{
            html += "<option value='"+object.ac_no+"'>"+object.ac_name+"</option>";
          }
        }
      });
      $("#ac_no").empty().append(html);
      if(ac_no == ''){
        $("#ac_no").val($("#ac_no option:first").val());
      }
    }

    $(document).ready(function(e){
      filter_respective_state("<?php echo $election_id ?>");
      filter_respective_acs("<?php echo $st_code ?>");
    });


  </script>
  @if (session('success_mes'))
<script type="text/javascript">
 success_messages("{{session('success_mes') }}");
 </script>
@endif
@if (session('error_mes'))
  <script type="text/javascript">
  error_messages("{{session('error_mes') }}");
</script>
@endif

@endsection