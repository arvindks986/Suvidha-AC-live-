@extends('admin.layouts.ac.theme')
@section('content')

@if ($errors->any())
<div class="alert  alert-warning alert-dismissible fade show" role="alert">
  @foreach ($errors->all() as $error)
  <span>
    <p>{{ $error }}</p>
  </span>
  @endforeach
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif

@if (session('success'))
<div class="alert  alert-success alert-dismissible fade show" role="alert">
  {{ session('success') }}
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif


@if (session('error') && !is_array(session('error')))
<div class="alert alert-danger">{{ session('error') }}</div>
@elseif(session('error') && is_array(session('error')))
@foreach(session('error') as $error)
<div class="alert alert-danger"><strong>Error:</strong> Unable to Import Excel because In row {{ $error->row()}} {{ $error->errors()[0] }}</div>
@endforeach
@elseif(session('error'))
<div class="alert  alert-danger alert-dismissible fade show" role="alert">
  {{ session('error') }}
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif
<div class="alert alert-warning">
  <h3>Once you disabled modification from this page Respected RO has to Finalize Polling Station Electoral From their Account again.</h3>
</div>
<section class="dashboard-header section-padding">
  <div class="container-fluid">


    <form class="row" method="get" action="{{url('eci/turnout/AcECIPSElectoralDefinalzied')}}">

      <div class="form-group col-md-3"> <label>State </label>
        <select name="state" id="state" class="form-control" onchange="filter()">
          <option value="all">All</option>
          @foreach($states as $result)
          @if($state == $result['st_code'])
          <option value="{{$result['st_code']}}" selected="selected">{{$result['state']['ST_NAME'].' ('. $result['st_code'].')'}}</option>
          @else
          <option value="{{$result['st_code']}}">{{$result['state']['ST_NAME'].' ('. $result['st_code'].')'}}</option>
          @endif
          @endforeach
        </select>
      </div>
      <?php if (isset($phases) && count($phases) > 0) { ?>
        <div class="form-group col-md-3"> <label>Election Phase</label>

          <select name="phase" id="phase" class="form-control" required>
            @foreach($phases as $result)
            @if($phase==$result->SCHEDULENO)
            <option value="{{$result->SCHEDULENO}}" selected="selected">{{$result->PHASE_NO}}-Phase</option>
            @else
            <option value="{{$result->SCHEDULENO}}">{{$result->PHASE_NO}}-Phase</option>
            @endif
            @endforeach

          </select>
        </div>
      <?php } else { ?>
        <input type="hidden" id="phase" name="phase" value="{!! $phase !!}">
      <?php } ?>


      <div class="form-group col-md-2"> <label>&nbsp </label>
        <button class="btn btn-success" style="width:100%">Submit</button>
      </div>
  </div>
  </form>


  </div>
</section>
<section class="statistics color-grey pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-8 pull-left">
        <h4>{!! $heading_title !!}</h4>
      </div>
      @if(count($results) > 0)
      <div class="col-md-4 pull-right text-right">
        <span class="report-btn"><a class="btn btn-warning" href="{{url('/eci/turnout/AcECIPSElectoralDefinalzied?excel=download')}}{{($phase) ? '&phase='.$phase : ''}}{{($state) ? '&state='.$state : ''}}" title="Export in Excel">Export in Excel</a></span>
      </div>
      @endif
    </div>
    <div class="col-md-12">
      <div class="table-responsive">

        <table id="data_table_table" class="table table-striped table-bordered" style="width:100%">
          <thead>
            <tr>
              <th> S.no</th>
              <th> State Code</th>
              <th> State Name</th>
              <th> AC No</th>
              <th> Name </th>
              <th> Status </th>
              <th> Action </th>
            </tr>


          </thead>
          <tbody>
            @foreach($results as $key => $result)
            <tr>
              <td>{{$key + 1 }}</td>
              <td>{{$result['ST_CODE'] }}</td>
              <td>{{$result['ST_NAME'] }}</td>
              <td>{{$result['AC_NO'] }}</td>
              <td>{{$result['AC_NAME'] }}</td>
              <td>{{($result['ps_finalized']==1) ? 'Finalized' : 'Not Yet Finalize'}}</td>
              <td>
                @if($showDefinalizeAndEditEnableBtn && $result['show_enable_edit_btn'] == 0)
                <button type="button" class="btn btn-success definalize" data-acname="{{$result['AC_NAME']}}" data-acno="{{$result['AC_NO']}}" data-stcode="{{$result['ST_CODE']}}" data-editdisbaled="0">{{ ($result['ps_finalized']==1) ? 'Definalize It & ' : ''}} Enable PS Electoral Modification</button>
                @elseif( $showDefinalizeAndEditEnableBtn && $result['show_enable_edit_btn'] == 1)
                <button class="btn btn-warning definalize" data-acname="{{$result['AC_NAME']}}" data-acno="{{$result['AC_NO']}}" data-stcode="{{$result['ST_CODE']}}" data-editdisbaled="1">Disabled PS Electoral Modification Option</button>
                @else
                <span class="btn btn-danger disabled">
                  Option will open after {{$poll_date}}
                </span>
                @endif
              </td>
            </tr>
            @endforeach

          </tbody>
        </table>

      </div><!-- End Of  table responsive -->
    </div>
  </div>
  </div>
</section>
<div class="modal modal-big fade" id="definalizeModal" tabindex="-1" role="dialog" aria-labelledby="definalizeModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <h5 class="modal-title" id="definalizeModalLabel"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" action="{{url('eci/turnout/AcECIPSElectoralDefinalziedUpdate')}}">
        {{ csrf_field() }}
        <input type="hidden" name="ac_no" value="">
        <input type="hidden" name="st_code" value="">
        <input type="hidden" name="disableEdit" value="">
        <div class="modal-body text-center">
          <div class="mb-3">
            <div style="font-size:16px;" id="definalizeModalMsg"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" id="submit_final_form" class="btn btn-success submit-button">Confirm</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    $(document).on('click', '.definalize', function() {
      $('input[name=ac_no]').val($(this).attr('data-acno'));
      $('input[name=st_code]').val($(this).attr('data-stcode'));
      var disableEdit = $(this).attr('data-editdisbaled')
      $('input[name=disableEdit]').val(disableEdit);
      $('#acname').text();
      if (disableEdit == 1) {
        $('#definalizeModalLabel').text('Confirmation For Disabled Edit option for PS wise electoral!');
        var html = "<div style='padding: 4px 9px;font-size: 13px;' class='bg bg-warning'><b>Warning</b> :- Once you disabled modification Respected RO has to Finalize Polling Station Electoral From their Account again.</div><hr/>"
        $('#definalizeModalMsg').html(html + 'Are you sure you want to disabled <b>' + $(this).attr('data-acno') + '-' + $(this).attr('data-acname') + '</b> electoral details for modifications?')
      } else {
        $('#definalizeModalLabel').text('Confirmation For Definalize & Enable modification option for PS wise electoral!');
        $('#definalizeModalMsg').html('Are you sure you want to definalize <b>' + $(this).attr('data-acno') + '-' + $(this).attr('data-acname') + '</b> electoral details for modifications?')
      }

      $('#definalizeModal').modal('show');
    })
  })

  function filter() {
    var url = "<?= url('eci/turnout/AcECIPSElectoralDefinalzied') ?>";
    var currentPhase = "<?= $state ?>";
    var query = '';
    if (jQuery("#state").val() != '' && jQuery("#state").val() != 'undefined') {
      query += '&state=' + jQuery("#state").val();
      query += '&phase=1';
    }
    window.location.href = url + '?' + query.substring(1);
  }
</script>
@endsection