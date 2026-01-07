@extends('admin.layouts.ac.theme')
        @section('content')
        <style type="text/css">
          .fullwidth{
            width: 100%;
            float: left;
          }
          .button-next{
            margin-top: 30px;
          }
          .button-next button{
            float: right;
          }
          .affidavit-preview{
            min-height: 600px;
          }
		  .affidavit-preview.min-width{min-height:0px;}
        </style>
       <link rel="stylesheet" href="{{ asset('css/custom.css') }}" id="theme-stylesheet">
    <link rel="stylesheet" href="{{ asset('admintheme/css/jquery-ui.css') }}" id="theme-stylesheet">
    <main role="main" class="inner cover mb-3">
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
               @if (session('success_mes'))
               <div class="alert alert-success"> {{session('success_mes') }}</div>
               @endif
      <div class="container">

  <div class="row">
   <div class="col-md-12 mt-3">
     <ul style="text-align:center;margin-bottom:40px;" class="arrow-steps clearfix">
       <li class="step step1  first">Part I/II</li>
       <li class="step step2">Part III<span></span></li>
       <li class="step step3">Part IIIA<span></span></li>
       <li class="step step4 current">Upload Affidavit<span></span></li>
       <li class="step step4">Finalize Application<span></span></li>
     </ul>
   </div>
 </div>

</div>
       <section>
        <div class="container p-0 mt-5">
          <div class="row">

            <div class="col-md-12">
              <div class="card">
               <div class="card-header d-flex align-items-center">
                 <h4>{!! $heading_title !!}</h4>
               </div>
               <div class="card-body">
                 <div class="row">

                   <div class="col">



                     <div class="form-group row">


                      <!-- fieldsets -->
                      

                      
                      

                      <div class="nomination-parts box recognized fullwidth">

                        <form method="post" action="{!! $action !!}" enctype="multipart/form-data">
                          <input type="hidden" name="_token" value="{{csrf_token()}}">
                          <input type="hidden" name="recognized_party" value="recognized">
                          <input type="hidden" name="nomination_id" value="{{$nomination_id}}">

                          <div class="fullwidth">
                            
                            <div class="fullwidth mb-3">
       
                          <div class="file-frame" style="width: 100%;">
                            <button class="file btn btn-primary"  type="button" style="width: 100%;">Browse <i class="fa fa-upload"></i></button>
                            <input type="hidden" name="affidavit" class="affidavit" value="{{$affidavit}}">
                          </div>
                              
                              
                            </div>

                            <fieldset class="fullwidth">
                             
                              <div id="affidavit-preview" class="affidavit-preview min-width">
                                <iframe src="" width="100%" height="500"></iframe>
                              </div>
                              
                              
                            </fieldset>


                            
                          </div>

                          

                      <div class="fullwidth" style="margin-top: 30px;"> 
          <div class="form-group">
            <div class="col">
    
            </div>
            <div class="col ">
              <div class="form-group row float-right">
    
              <button type="submit" class="btn btn-primary save_next">Upload</button>
            </div>
            </div>
            </div>
          </div>
       

            

                        </form>
                      </div>
                      
                      
                      


                      
                    </form>
                  </div> 

                  

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>    
  </section>

  </main>
  @endsection

  @section('script')
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

    function read_url(input) {
      if (input.files && input.files[0]) {
        var file = input.files[0];
        var fileName = file.name;
        var ext = fileName.split('.').reverse()[0]
        if(file.type === 'application/pdf') {
          var file_object = URL.createObjectURL(file);
          $('.affidavit-preview').html('<iframe src="' +  file_object + '" width="100%" height="500"></iframe>');
        }else{
          alert("Please select a PDF file.");
        }
      }
    }

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
                $('.file-frame').find('.affidavit').val(json['path']);
                $('.affidavit-preview iframe').attr("src","<?php echo url('/'); ?>/"+json['path']);
              }
            },
            error: function(xhr, ajaxOptions, thrownError) {
              alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
          });
        }
      }, 500);
    });

    <?php if($affidavit){ ?>
      $('.affidavit-preview iframe').attr("src","<?php echo url($affidavit); ?>");
    <?php } ?>

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