@extends('admin.layouts.ac.theme')
@section('title', 'Nomination')
@section('content')

<link rel="stylesheet" href="{{ asset('css/custom.css') }}" id="theme-stylesheet">
<link rel="stylesheet" href="{{ asset('admintheme/css/jquery-ui.css') }}" id="theme-stylesheet">
<style type="text/css">
  .input-group.col-xs-12.formData { width: 100%;   padding-bottom: 26px;}
  .formData input { padding: 30px 20px;}
  .formData button {     padding: 16.5px 20px; border-radius: 0 3px 3px 0;  font-size: 18px;}
  .file-frame img{ 
    width: 100px;
    height:100px;
    float: left; 
  }
  .file {
    float: right;
    background-color:#bb4292;
    border-color:#bb4292;
    color:#fff;
    width: 100%;
  }
  .file-frame-error{
    border: 2px solid red;
  }
</style>
<main role="main" class="inner cover mb-3">

  <section style="height: auto !important;">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          @if(count($errors->all())>0)
          <div class="alert alert-danger">
            <ul>
              @foreach($errors->all() as $iterate_error)
              <li><p class="text-left">{!! $iterate_error !!}</p></li>
              @endforeach
            </ul>
          </div>
          @endif

          @if (session('flash-message'))
          <div class="alert alert-success"> {{session('flash-message') }}</div>
          @endif
        </div>
      </div>
    </div>
  </section>
  <div class="container-fluid">
    <div class="col-md-12 mt-3">
      <ul style="text-align:center;margin-bottom:40px;" class="arrow-steps clearfix">
        <li class="step step1 ">Personal Details</li>
        <li class="step step2">Election Details</li>
        <li class="step step3 current first">Part I/II</li>
        <li class="step step4">Part III<span></span></li>
        <li class="step step5">Part IIIA<span></span></li>
       <li class="step step4">Upload Affidavit<span></span></li>
       <li class="step step4">Finalize Application<span></span></li>
      </ul>
    </div>

  </div>
  <section>
    <div class="container">

      <div class="row">
        <div class="fullwidth" style="float: left;width: 100%;">


          @if(isset($reference_id) && isset($href_download_application))
          <div class="col-md-5 float-right">
            <ul class="list-inline float-right">
              <li class="list-inline-item text-right">Reference ID: <b style="text-decoration: underline;">{{$reference_id}}</b></li>
              <li class="list-inline-item text-right"><a href="{!! $href_download_application !!}" class="btn btn-primary" target="_blank">Download Application</a></li>
            </ul>
          </div>
          @endif
        </div>
      </div>

      <div class="card">
        <div class="card-header">


          <div class="nomination-parts1">

            <div class="fullwidth">
              <div class="text-center fullwidth heading-part1">
                <p>FORM 2B</p>
                <p>(See rule 4)</p>
                <p>NOMINATION PAPER<p>
                  <p>Election to the Legislative Assembly of <span class="nominationvalue">{{$st_name}}</span>(State)</p>
                </div>
              </div>


            </div>
          </div>
          <div class="card-body">
            <div class="nomination-options col-lg-12">
              <div class="checkbox">
                <label>
                  @if($recognized_party == '1')
                  <input type="radio" class="parts-opt recognized_party" name="recognized_party" value="1" checked="checked">
                  @else
                  <input type="radio" class="parts-opt recognized_party" name="recognized_party" value="1">
                  @endif

                Candidate set up by recognised political party</label>
              </div>
              <div class="checkbox">
                <label>
                  @if($recognized_party == '2')
                  <input type="radio" class="parts-opt recognized_party" name="recognized_party" value="2" checked="checked">
                  @else
                  <input type="radio" class="parts-opt recognized_party" name="recognized_party" value="2">
                  @endif
                Candidate not set up by recognised political party</label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section>
      <div class="container">
        <form method="post" action="{!! $action !!}" enctype="multipart/form-data" class="form-inline recognized display_none">


          <div class="card">

            <div class="card-body">
              <div class="nomination-parts box  fullwidth">


                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <input type="hidden" name="recognized_party" value="1">
                <input type="hidden" name="nomination_id" value="{{$nomination_id}}">
                <input type="hidden" name="st_code" value="{{$st_code}}">
                <input type="hidden" name="ac_no" value="{{$ac_no}}">
                <input type="hidden" name="election_id" value="{{$election_id}}">

                <div class="form-group ">
                  <div class="fullwidth float-right" style="width: 100%;">
                    <div class="browse_image_outer">

                      <div class="avatar-upload btn file-frame">
                              <img src="{{$thumb}}" class="img-responsive">
                              <button class="file btn" type="button">Browse <i class="fa fa-upload"></i></button>
                              <input type="hidden" name="image" class="image" value="{{$profileimg}}">
                            </div>
                    </div>
                  </div>
                </div>

                <div class="nomination-form-heading text-center fullwidth">
                  STRIKE OFF PART I OR PART II BELOW WHICHEVER IS NOT APPLICABLE<br>
                  <strong>PART I</strong><br>
                  (To be used by candidate set up by recognised political party)
                </div>

                <div class="nomination-detail">
                  <p>I nominate as a candidate for election to the Legislative Assembly from the 
                    <select name="legislative_assembly" id="legislative_assembly" class="form-control nomination-field-2 ac_no" readonly="readonly">
                      @foreach($acs as $iterate_ac)
                      @if($iterate_ac['ac_no'] == $ele_details->CONST_NO)
                      <option value="{!! $iterate_ac['ac_no'] !!}" selected="selected" >{!! $iterate_ac['ac_name'] !!}</option>
                      @else
                     <!--  <option value="{!! $iterate_ac['ac_no'] !!}">{!! $iterate_ac['ac_name'] !!}</option> -->
                      @endif
                      @endforeach
                    </select> Assembly Constituency.<br>

                    Candidate's name <input type="text" name="name" id="name" class="form-control nomination-field-2" placeholder="Candidate Name" value="{{$name}}"> Father's/mother's/husband's name <input type="text" name="father_name" id="father_name" placeholder="Father's/Mother's/Husband name" value="{!! $father_name !!}" class="form-control nomination-field-3">

                    His postal address <input type="text" name="address" id="address" placeholder="Address" value="{!! $address !!}" class="form-control nomination-field-12"> 

                    His name is entered at Sl.No <input type="text" name="serial_no" id="serial_no" class="form-control nomination-field-2" placeholder="S.No." value="{{$serial_no}}">

                    in part no <input type="text" name="part_no" id="part_no" placeholder="Part No" class="form-control nomination-field-2" value="{{$part_no}}">

                    of the electoral roll for 
                    <select name="resident_ac_no" id="resident_ac_no" class="form-control nomination-field-2">
                      <option value="">Select</option>
                      @foreach($resident_acs as $iterate_ac)
                      @if($iterate_ac['ac_no'] == $resident_ac_no)
                      <option value="{!! $iterate_ac['ac_no'] !!}" selected="selected">{!! $iterate_ac['ac_name'] !!}</option>
                      @else
                      <option value="{!! $iterate_ac['ac_no'] !!}">{!! $iterate_ac['ac_name'] !!}</option>
                      @endif
                      @endforeach
                    </select>
                    Assembly constituency.<br><br>

                    <!-- Recognised Party Proposer Detail -->
                    My name is <input type="text" name="proposer_name" id="proposer_name" value="{{$proposer_name}}" class="form-control nomination-field-2" placeholder="Proposer Name"> 

                    and it is entered at Sl.No <input type="text" name="proposer_serial_no" id="proposer_serial_no" class="form-control nomination-field-2" value="{{$proposer_serial_no}}" placeholder="Proposer S.No."> 

                    in part no <input type="text" name="proposer_part_no" id="proposer_part_no" value="{{$proposer_part_no}}" placeholder="Proposer Part No" class="form-control nomination-field-2"> 

                    of the electoral roll for
                    <select name="proposer_assembly" id="proposer_assembly" class="form-control nomination-field-2">
                      <option value="">Select</option>
                      @foreach($acs as $iterate_ac)
                      @if($iterate_ac['ac_no'] == $proposer_assembly)
                      <option value="{!! $iterate_ac['ac_no'] !!}" selected="selected">{!! $iterate_ac['ac_name'] !!}</option>
                      @else
                      <option value="{!! $iterate_ac['ac_no'] !!}">{!! $iterate_ac['ac_name'] !!}</option>
                      @endif
                      @endforeach
                    </select> Assembly constituency.</p>
                  </div>

                  <div class="nomination-signature">
                    <span class="nomination-date left">Date 
                      <input type="text" name="apply_date" class="nomination-field-4 form-control" id="apply_date" value="{{$apply_date}}" readonly="readonly">
                    </span>

                  </div>



                </div>


              <hr class="mt-5" />
                            <div class="nomination-note">
                <small>*Score out the words "assembly constituency comprised within" in the case of Jammu and Kashmir, Andaman and Nicobar Islands, Chandigarh, Dadra and Nagar Haveli, Daman and Diu and Lakshadweep.</small>
                <br /><small> *Score out this paragraph, if not applicable.</small>
                <br /><small> **Score out the words not applicable. N.B.—A "recognised political party" means a political party recognised by the Election Commission under the Election Symbols (Reservation and Allotment) Order, 1968 in the State concerned.</small>
              </div>


              </div>


              <div class="card-footer">
                <div class="form-group row ">
                  <!-- <div class="col " style="display: none;">
                    <a href="" id="" class="btn btn-secondary float-left">Back</a>
                  </div> -->
                  <div class="col ">
                    <div class="form-group row float-right">
                      <button type="submit" id="save" name="save_only" class="btn btn-primary">Save</button>
                      <button type="submit" class="btn btn-primary save_next float-right">Save & Next</button>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </form>
        </div>
      </section>


      <section>  
        <form method="post" action="{!! $action !!}" enctype="multipart/form-data" class="not-recognized display_none">
          <div class="container">
            <div class="card">
              <div class="card-body">
                <div class="nomination-parts box  fullwidth">

                  <input type="hidden" name="_token" value="{{csrf_token()}}">
                  <input type="hidden" name="recognized_party" value="2">
                  <input type="hidden" name="nomination_id" value="{{$nomination_id}}">
                  <input type="hidden" name="st_code" value="{{$st_code}}">
                  <input type="hidden" name="ac_no" value="{{$ac_no}}">
                  <input type="hidden" name="election_id" value="{{$election_id}}">
                  <div class="fullwidth">
                    <div class="text-center fullwidth">


                      <div class="form-group ">
                        <div class="fullwidth float-right" style="width: 100%;">
                          <div class="browse_image_outer">
                            <div class="avatar-upload btn file-frame">
                              <img src="{{$thumb}}" class="img-responsive">
                              <button class="file btn" type="button">Browse <i class="fa fa-upload"></i></button>
                              <input type="hidden" name="image" class="image" value="{{$profileimg}}">
                            </div>
                          </div>
                        </div>
                      </div>




                    </div>
                  </div>
                  <div class="nomination-form-heading">
                    <strong>PART II</strong><br>
                    (To be used by candidate NOT set up by recognised political party) 
                  </div>
                  <div class="nomination-detail-02">
                    <div class="form-inline nomination-detail">
                      <p>
                        We hereby nominate as candidate for election to the Legislative Assembly from the
                        <select name="legislative_assembly" id="legislative_assembly" class="form-control nomination-field-2 ac_no" readonly="readonly">
                           
                          @foreach($acs as $iterate_ac)
                          @if($iterate_ac['ac_no'] == $ele_details->CONST_NO)
                          <option value="{!! $iterate_ac['ac_no'] !!}" selected="selected">{!! $iterate_ac['ac_name'] !!}</option>
                          @else
                          <!-- <option value="{!! $iterate_ac['ac_no'] !!}">{!! $iterate_ac['ac_name'] !!}</option> -->
                          @endif
                          @endforeach
                        </select>
                        Assembly Constituency<br>
                        Candidate's name <input type="text" name="name" id="name" class="form-control nomination-field-2" placeholder="Candidate Name" value="{{$name}}"> Father's/mother's/husband's name <input type="text" name="father_name" id="father_name" placeholder="Father's/Mother's/Husband name" value="{!! $father_name !!}" class="nomination-field-3">
                        His postal address <input type="text" name="address" id="address" placeholder="Address" value="{!! $address !!}" class="nomination-field-12"> 
                        His name is entered at Sl.No <input type="text" name="serial_no" id="serial_no" class="form-control nomination-field-2" placeholder="S.No." value="{{$serial_no}}">
                        in part no <input type="text" name="part_no" id="part_no" placeholder="Part No" class="form-control nomination-field-2" value="{{$part_no}}">
                        of the electoral roll for 
                        <select name="resident_ac_no" id="resident_ac_no" class="form-control nomination-field-2">
                          <option value="">Select</option>
                          @foreach($resident_acs as $iterate_ac)
                          @if($iterate_ac['ac_no'] == $resident_ac_no)
                          <option value="{!! $iterate_ac['ac_no'] !!}" selected="selected">{!! $iterate_ac['ac_name'] !!}</option>
                          @else
                          <option value="{!! $iterate_ac['ac_no'] !!}">{!! $iterate_ac['ac_name'] !!}</option>
                          @endif
                          @endforeach
                        </select>
                        Assembly constituency.<br><br>
                        <div class="nomination-signature">
                          <span class="nomination-date left">Date 
                            <input type="text" name="apply_date" class="form-control nomination-field-4" id="apply_date" value="{{$apply_date}}" readonly="readonly">
                          </span>
                        </div>
                      We declare that we are electors of this Assembly constituency and our names are entered in the electoral roll for this Assembly constituency as indicated below and we append our signatures below in token of subscribing to this nomination: -</p>
                      <div class="table-heading">Particulars of the proposers and their signatures</div>
                    </div>

                    <table class="table table-bordered proposers-table">
                      <thead>
                        <tr>
                          <th>Sr No.</th>
                          <th colspan="2">Part No of Proposer </th>
                          <th>Full Name</th>
                          <th>Signature</th>
                          <th>Date</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>&nbsp;</td>
                          <td>Serial No. of Electoral Roll</td>
                          <td>S.No. in that part</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                        </tr>
                        <?php $key = 0;
                        foreach($non_recognized_proposers as $iterate_proposer){ 

                          ?>
                          <tr class="non_recognized_proposers_row">
                            <td>{{$iterate_proposer['s_no']}}
                              <input type="hidden" name="non_recognized_proposers[{{$key}}][s_no]" value="{{$iterate_proposer['s_no']}}">
                              <input type="hidden" name="non_recognized_proposers[{{$key}}][candidate_id]" value="{{$iterate_proposer['candidate_id']}}">
                              <input type="hidden" name="non_recognized_proposers[{{$key}}][nomination_id]" value="{{$iterate_proposer['nomination_id']}}">
                            </td>
                            <td><input type="text" placeholder="Serial No" class="form-control  particulars-field-12" name="non_recognized_proposers[{{$key}}][serial_no]" value="{{$iterate_proposer['serial_no']}}"></td>
                            <td><input type="text" placeholder="Part No" class="form-control  particulars-field-12" name="non_recognized_proposers[{{$key}}][part_no]" value="{{$iterate_proposer['part_no']}}"></td>
                            <td><input type="text" placeholder="Full Name" class="form-control  particulars-field-12" name="non_recognized_proposers[{{$key}}][fullname]" value="{{$iterate_proposer['fullname']}}"><span id="error_message"></span></td>
                            <td><input type="hidden" class="form-control " name="non_recognized_proposers[{{$key}}][signature]" value="{{$iterate_proposer['signature']}}">.................</td>
                            <td><input type="text" class="form-control particulars-field-12 recognized_date" name="non_recognized_proposers[{{$key}}][date]" value="{{$iterate_proposer['date']}}" readonly="readonly"></td>
                          </tr>
                          <?php $key++; } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <hr class="mt-5" />
                            <div class="nomination-note">
                <small>*Score out the words "assembly constituency comprised within" in the case of Jammu and Kashmir, Andaman and Nicobar Islands, Chandigarh, Dadra and Nagar Haveli, Daman and Diu and Lakshadweep.</small>
                <br /><small> *Score out this paragraph, if not applicable.</small>
                <br /><small> **Score out the words not applicable. N.B.—A "recognised political party" means a political party recognised by the Election Commission under the Election Symbols (Reservation and Allotment) Order, 1968 in the State concerned.</small>
              </div>


                </div>



                <div class="card-footer">
                  <div class="form-group row ">
                     
                    <div class="col ">
                      <div class="form-group row float-right">
                        <button type="submit" class="btn btn-primary save_next float-right">Save & Next</button>
                      </div>
                    </div>
                  </div>
                </div>





              </div>

            </div>
          </form>
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

          $('#apply_date').datepicker({
            dateFormat: 'dd-mm-yy'
          });

          $('.non_recognized_proposers_row').each(function(index,object){
            $('.recognized_date').datepicker({
              dateFormat: 'dd-mm-yy'
            });
          });

          $('.recognized_party').change(function(e){
            change_recognised();
          });

          change_recognised();
        });

        function change_recognised(){
          if($(".recognized_party:checked").val() == '1'){
            $('.not-recognized').addClass('display_none');
            $('.recognized').removeClass('display_none');
          }else{
            $('.not-recognized').removeClass('display_none');
            $('.recognized').addClass('display_none');
          }
        }

        function read_url(input, part) {
          if (input.files && input.files[0]) {

            var reader = new FileReader();    
            reader.onload = function(e) {
              $('.'+part+' .avatar-preview').html("<img src='"+ e.target.result+"' width='100px' height='100px'>");
            }
            reader.readAsDataURL(input.files[0]);

          }
        }

      </script>
      <script type="text/javascript">
        $(document).on('click', '.browse', function(){
          var file = $(this).parent().parent().parent().find('.file');
          file.trigger('click');
        });
        $(document).on('change', '.file', function(){
          $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
        });
      </script>

      <script type="text/javascript">
        $(document).ready(function () {
          $('.file').on('click', function() {
            $('#form-upload').remove();
            $('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" value="" /></form>');
            $('#form-upload input[name=\'file\']').trigger('click');
            if (typeof timer != 'undefined') {
              clearInterval(timer);
            }
            timer = setInterval(function() {
              if ($('#form-upload input[name=\'file\']').val() != '') {
                clearInterval(timer);
                $.ajax({
                  url: "<?php echo $href_file_upload; ?>?_token=<?php echo csrf_token(); ?>",
                  type: 'POST',
                  dataType: 'json',
                  data: new FormData($('#form-upload')[0]),
                  cache: false,
                  contentType: false,
                  processData: false,
                  beforeSend: function() {
                    $('.file-frame').removeClass("file-frame-error");
                    $('.file i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
                    $('.file').prop('disabled', true);
                    $('.text-danger').remove();
                  },
                  complete: function() {
                    $('.file i').replaceWith('<i class="fa fa-upload"></i>');
                    $('.file').prop('disabled', false);
                  },
                  success: function(json) {
                    if(json['success'] == false) {
                      $('.file-frame').after("<span class='text-danger'>"+json['errors']+"</span>");
                      $('.file-frame').addClass("file-frame-error");
                    }
                    if (json['success'] == true) {
                      $('.file-frame').find('.image').val(json['path']);
                      $('.file-frame').find('img').attr("src","<?php echo url('/'); ?>/"+json['path']);
                    }
                  },
                  error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                  }
                });
              }
            }, 500);
          });
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