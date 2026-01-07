 @extends('admin.layouts.ac.theme')
@section('title', 'Nomination')
@section('content')
<link rel="stylesheet" href="{{ asset('css/custom.css') }}" id="theme-stylesheet">
<section >
  <div class="container">
    <div class="card text-left" style="width:100%; min-height: auto !important;">
      <div class="row">
         @if (session('success_mes'))
                <div class="alert alert-success"> {{session('success_mes') }}</div>
                @endif
                @if (session('error_mes'))
                    <div class="alert alert-danger"> {{session('error_mes') }}</div>
                @endif
      </div>
    </div>
  </section>

<div class="container-fluid">
   <div class="col-md-12 mt-3">
     <ul style="text-align:center;margin-bottom:40px;" class="arrow-steps clearfix">
      <li class="step step1">Personal Details</li>
      <li class="step step2 current first">Election Details</li>
       <li class="step step3">Part I/II</li>
       <li class="step step4">Part III<span></span></li>
       <li class="step step5">Part IIIA<span></span></li>
       <li class="step step4">Upload Affidavit<span></span></li>
       <li class="step step4">Finalize Application<span></span></li>
     </ul>
 </div>

</div>


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
              <div class="card-header d-flex align-items-center">
                <div class="col-md-7">
                <h4>{!! $heading_title !!}</h4>
                </div>
                 
              </div>
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
                <div class="form-group row ">
                  <div class="col">
                   
                  </div>
                  <div class="col ">
                    <div class="form-group row float-right">
                      <button type="submit" id="save" name="save_only" class="btn btn-primary">Save</button>
                      <button type="submit" id="candnomination" class="btn btn-primary float-left">Save & Next</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </section>
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