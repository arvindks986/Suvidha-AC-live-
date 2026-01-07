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
<style>
  .disabled {
    cursor: not-allowed;
    color: #000;
  }
</style>
<section class="dashboard-header section-padding">
  <div class="container-fluid">
    <form class="row" method="get" action="{{url('acceo/turnout/AcCeoAcElectoralReport')}}">
      <?php if (isset($phases) && count($phases) > 0) { ?>
        <div class="form-group col-md-3"> <label>Election Phase</label>
          <select name="phase" id="phase" class="form-control">
            @foreach($phases as $key => $result)
            @if($phase==$result->SCHEDULENO)
            <option value="{{$result->SCHEDULENO}}" selected="selected">{{$key+1}}-Phase</option>
            @else
            <option value="{{$result->SCHEDULENO}}">{{$key+1}}-Phase</option>
            @endif
            @endforeach
          </select>
        </div>
      <?php } else { ?>
        <input type="hidden" id="phase" name="phase" value="{!! $phase !!}">
      <?php } ?>
      <div class="form-group col-md-2">
        <label>&nbsp;</label>
        <button type="submit" class="btn btn-success" style="width:100%">Submit</button>
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
        <span class="report-btn"><a class="btn btn-warning" href="{{url('acceo/turnout/AcCeoAcElectoralReport?excel=download')}}{{($phase) ? '&phase='.$phase : ''}}" title="Export in Excel">Export in Excel</a></span>
      </div>
      @endif
    </div>
    <div class="col-md-12">
      <div class="table-responsive">

        <table id="data_table_table" class="table table-striped table-bordered" style="width:100%">
          <thead>
            <tr>
              <th> S.no</th>
              <th> AC Name </th>
              <th> AC No</th>
              <th> Electors Male </th>
              <th> Electors Female </th>
              <th> Electors Other </th>
              <th> Electors Total </th>
              <th> Electors Service </th>
              <th> Electors Grand Total </th>
              <th> Status </th>
            </tr>


          </thead>
          <tbody>
            @foreach($results as $key => $result)
            <tr>
              <td>{{$key + 1 }}</td>
              <td>{{$result['ac_name'] }}</td>
              <td>{{$result['ac_no'] }}</td>
              <td>{{$result['electors_male'] }}</td>
              <td>{{$result['electors_female'] }}</td>
              <td>{{$result['electors_other'] }}</td>
              <td>{{$result['electors_total'] }}</td>
              <td>{{$result['electors_service'] }}</td>
              <td>{{$result['electors_gt']}}</td>
              <td>
                @if($result['ps_finalized'] == 2)
                <button type="button" class="btn btn-success">Verified By RO</button>
                @elseif($result['ps_finalized'] == 1)
                <button type="button" class="btn btn-warning">Not Verify By RO</button>
                @else
                <button type="button" class="btn btn-danger">Data not entered By RO</button>
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
  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <h5 class="modal-title" id="exampleModalLabel">Confirmation For PS wise electoral details Definalize!</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" action="{{url('acceo/turnout/AcCeoPSElectoralDefinalziedUpdate')}}">
        {{ csrf_field() }}
        <input type="hidden" name="ac_no" value="">
        <div class="modal-body">
          <div class="mb-3">
            <div style="font-size:16px;">Are you sure you want to definalize <b id="acname"></b> electoral details for modifications?</div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" id="submit_final_form" class="btn btn-success submit-button">Update</button>
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
      $('#acname').text($(this).attr('data-acno') + '-' + $(this).attr('data-acname'));
      $('#definalizeModal').modal('show');

    })
  })
</script>
@endsection