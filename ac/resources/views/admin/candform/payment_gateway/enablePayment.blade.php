@extends('admin.layouts.ac.theme')
@section('title', 'Payment Service')
@section('bradcome', 'Payment Service')
@section('content')
<style>
  .switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
  }

  .switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
  }

  .slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
  }

  input:checked+.slider {
    background-color: #1db43f;
  }

  input:focus+.slider {
    box-shadow: 0 0 1px #2196F3;
  }

  input:checked+.slider:before {
    -webkit-transform: translateX(26px);
    -ms-transform: translateX(26px);
    transform: translateX(26px);
  }

  /* Rounded sliders */
  .slider.round {
    border-radius: 34px;
  }

  .slider.round:before {
    border-radius: 50%;
  }
</style>
<main role="main" class="inner cover pt-5">
  <section>
    <div class="container-fluid">

      <div class="row d-flex align-items-center mt-2">
        <div class="card text-left">
          <div class=" card-header">
            <div class="container">
              <div class="row text-center flash-message mb-1">
                @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                @if(Session::has('alert-' . $msg))
                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</p>
                @endif
                @endforeach
              </div>
            </div>
            <div class="row d-flex align-items-center">
              <div class="col-md-10 ">
                <h4>Payment Services</h4>
              </div>
              <div class="col-md-2 text-right">
                <a id="Cancel" class="btn btn-primary" href="{{url('/acceo/dashboard')}}">Back</a>
              </div>
            </div>

          </div>
          <form id="payment_gateway_service" action="{{ url('/acceo/enable_payment_gateway_submit')}}"
            method="post">
            {{ csrf_field() }}
            <div class="card-body">
              <div class="row">
                <div class="col">
                  Payment Gateway Service
                </div>
                <label class="switch ml-auto mr-3">
                  <input name="enable_status" type="checkbox" value="on" {{isset($enable_status) && $enable_status == 1 ? "checked" : ""}}>
                  <span class="slider round"></span>
                </label>
                <span id="enable_status" class="btn">{{isset($enable_status) && $enable_status == 1 ? "Enabled" : "Disabled"}}</span>
              </div>
              <hr>
              <div class="row">
                <div class="col">
                  Payment Challen Service
                </div>
                <label class="switch ml-auto mr-3">
                  <input name="challen_status" type="checkbox" value="on" {{isset($challen_status) && $challen_status == 1 ? "checked" : ""}}>
                  <span class="slider round"></span>
                </label>
                <span id="challen_status" class="btn">{{isset($challen_status) && $challen_status == 1 ? "Enabled" : "Disabled"}}</span>
              </div>
              @if($challen_status=='1')
              <hr>
              <div id="challen_url" class="row">
                <div class="col">
                  Payment Challan URL
                </div>
              <input type="text" value="{{ !empty($challen_url) ? $challen_url : 'No URL Found!' }}" readonly>
              </div>
              @endif
            </div>
          </form>
        </div>
      </div>

    </div>
  </section>
</main>

<div class="modal fade animated zoomIn" id="add_new_so_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Please Confirm</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Are You Sure You want to <span id="status_payment_gateway"></span> Payment <span id="payment_type"></span><br> Service !</p>
      </div>
      <div class="modal-footer">
        <button id="save_change" type="button" class="btn btn-primary">Yes</button>
        <button id="close_popup" type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script>
  $(document).ready(function(e) {
    var class_name = '';
    var valu = '';
    $('[name=enable_status],  [name=challen_status]').change(function(e) {

        class_name = $(this).attr('name');
        if(class_name === 'enable_status'){
          $('#payment_type').text('Gateway');
        }else{
          $('#payment_type').text('Challan');
        }
        valu = $(this).is(":checked");
        if(valu){
          $('#'+class_name).text('Enable');
          $('#status_payment_gateway').text('Enable');
        }else{
          $('#'+class_name).text('Disable');
          $('#status_payment_gateway').text('Disable');
        }

        $('#add_new_so_modal').modal('show');
    });
    $('#save_change').click(function(e) {
      $('#payment_gateway_service').submit();
    });
    $('#close_popup').click(function(e) {
      location.reload();
    });
  });
</script>
@endsection