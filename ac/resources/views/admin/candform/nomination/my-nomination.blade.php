  @extends('layouts.theme')
  @section('title', 'Nomination')
  @section('content')
  <style type="text/css">
    .error{
      font-size: 12px; 
      color: red;
    }
  </style>
  <link rel="stylesheet" href="{{ asset('admintheme/css/nomination.css') }}" id="theme-stylesheet">
  <link rel="stylesheet" href="{{ asset('admintheme/css/jquery-ui.css') }}" id="theme-stylesheet">
  <main role="main" class="inner cover mb-3 mt-3">
    <section>

@if (session('flash-message'))

      <div class="container">
        <div class="row">



           @if (session('flash-message'))
           <div class="alert alert-success"> {{session('flash-message') }}</div>
           @endif

   
      
     </div>    
   </section>
   @endif
   <section>
    <div class="container p-0">
      <div class="row">

        <div class="col-md-12 mb-3 text-center display_none">
         
          <div class="parent_qr_code " style="width: 150px;overflow: hidden;">
          <video id="preview" style="width: 100%;"></video>
          <button id="close_webcam" class="btn " type="button">Close Camera</button>
          </div>
         
          <button id="open_webcam" class="btn btn-primary" type="button">Scan QR Code</button>
        </div>

        <div class="col-md-12">
          <div class="card">
           <div class="card-header d-flex align-items-center">
             <h4>{!! $heading_title !!}</h4>
           </div>
           <div class="card-body">
             <div class="row">
              <table class="table">
                <thead>
                  <tr>
                    <th>Nomination No.</th>
                    <th>Name</th>
                    <th>AC No & Name</th>
                    <th>Election</th>
                    <th>Status</th>
                    <th align="center" class="text-center">Action</th>
                  </tr>
                </thead>
              @if($results>0)
                <tbody>
                  @foreach($results as $result)
                    <tr>
                      <td>{{$result['nomination_no']}}</td>
                      <td>{{$result['name']}}</td>
                      <td>{{$result['ac_name']}}</td>
                      <td>{{$result['election_name']}}</td>
                      <td>{{$result['status']}}</td>
                      <td align="center"  class="text-center">
                        @if($result['is_finalize'] == 0)
                        <a href="{{$result['edit_href']}}" class="btn button btn-primary">Edit</a>
                        @else
                        <a href="{{$result['view_href']}}"  class="btn button btn-primary">View</a> 
                        @endif
                        <a href="{{$result['download_href']}}" target="_blank" class="btn button btn-primary">Download Application</a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              @else 
                <tr>
                  <td colspan="6">No Record Found</td>
                </tr>
              @endif
              </table>
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

  $(document).ready(function(e){
      let scanner = '';
      $('#open_webcam').click(function(e){
        $('.parent_qr_code').removeClass("display_none");
        scanner = new Instascan.Scanner({ 
          backgroundScan: false,
          video: document.getElementById('preview') 
        });
        scanner.addListener('scan', function (content) {
          window.location.href = "{!! url('nomination/detail') !!}"+'/'+content;
        });

        Instascan.Camera.getCameras().then(function (cameras) {
          if (cameras.length > 0) {
            scanner.start(cameras[0]);
          } else {
            console.error('No cameras found.');
          }
        }).catch(function (e) {
          console.error(e);
        });
      });

      $('#close_webcam').click(function(e){
        scanner.stop().then(function () {

        });
        $('.parent_qr_code').addClass("display_none");
      });

     });
</script>
@endsection