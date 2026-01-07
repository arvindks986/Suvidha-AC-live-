@extends('admin.layouts.ac.theme')
@section('title', 'Suvidha')
@section('bradcome', 'Polling Station Electors Details')
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

@if(($electorCdac->electors_male != $psTotalElectorMale || $electorCdac->electors_female != $psTotalElectorFemale || $electorCdac->electors_other != $psTotalElectorOther || $electorCdac->electors_total != $psTotalElector) && (!$diabledPSModifications || $modificationEnabledByECI))
<div class="alert alert-danger">
  <h1><strong>Warning:</strong> Total Elector In AC is not equal to the sum of total elector in Polling Station</h1>
</div>
@endif

@if($diabledPSModifications || $modificationEnabledByECI)
<div class="alert alert-warning">
  @if($modificationEnabledByECI)
  <h4><strong>Notice:</strong> Electoral data finalize date is past but Modification is enable by admin for correction</h4>
  @else
  <h3><strong>Notice:</strong> Modification of Polling Station is Disabled now.</h3>
  @endif
</div>
@endif

<section class="statistics color-grey pt-4 pb-2">

  <div class="container-fluid">
    <div class="row">
      <div class="col-md-4 pull-left">
        <h4>{!! $heading_title !!}</h4>
      </div>

      <div class="col-md-8  pull-right text-right">
        <form method="POST" action="{{url('roac/turnout/polling-station-electors-details-export')}}">
          {{ csrf_field() }}
          <span class="report-btn"><a class="btn btn-info" download="SOP to import PS data" href="{{url('docs/SOP-to-import-PS-data.pdf')}}" title="Download Instruction">Download Instruction</a></span>
          <span class="report-btn"><a class="btn btn-success" href="{{url('excel/Polling_station_import_template.xlsx')}}" title="Download Excel">Download Sample Excel File</a></span>
          @if(false)
          @if($importPollingStationStatus)
          <span class="report-btn"><a class="btn btn-warning" data-toggle="modal" data-target="#importPollingStationModal" title="Import Polling Station">Import Polling Station</a></span>
          @endif
          @endif
          @if(!empty($results))
          <span class="report-btn">
            <button type="submit" title="Export Data In Excel" class="btn btn-danger">Export Data In Excel</button>
          </span>
        </form>
        @endif
      </div>

    </div>
  </div>
</section>

@if(isset($filter_buttons) && count($filter_buttons)>0)
<section class="statistics pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        @foreach($filter_buttons as $button)
        <?php $but = explode(':', $button); ?>
        <span class="pull-right" style="margin-right: 10px;">
          <span><b>{!! $but[0] !!}:</b></span>
          <span class="badge badge-info">{!! $but[1] !!}</span>

        </span>

        @endforeach
      </div>
    </div>
  </div>
</section>
@endif

<div class="container-fluid">
  <!-- Start parent-wrap div -->
  <div class="parent-wrap">
    <!-- Start child-area Div -->
    <div class="child-area">
      <div class="page-contant">
        <div class="random-area">
          @if($electorCdac->electors_male == $psTotalElectorMale && $electorCdac->electors_female == $psTotalElectorFemale && $electorCdac->electors_other == $psTotalElectorOther && $electorCdac->electors_total == $psTotalElector)
          <div class="text-center">
            @if(count($results) > 0 && count($results) == $totalPsElectoralDataFinalized)
            <div class="alert alert-success"><strong>All Polling station elector data is finalized by RO</strong></div>
            @elseif(count($results) > 0 && count($results) != $totalPsElectoralDataFinalized)
            <form method="POST" action="{{url('roac/turnout/polling-station-electors-details-finalized')}}">
              {{ csrf_field() }}
              <h3>Please <button type="submit" class="btn btn-success">Click Here</button> to finalize polling station electors data and freeze this data</h3>
            </form>
            @endif

          </div>
          @endif
          <div class="table-responsive">
            <table id="data_table_table" class="table table-striped table-bordered" style="width:100%">
              <thead>
                <tr>
                  <th class="text-center" colspan="4">Total Elector In AC</th>
                  <th class="text-center" colspan="4">Total Sum of PS Elector </th>
                </tr>
                <tr>
                  <!--  <th>Serial No</th> -->
                  <th>Male</th>
                  <th>Female</th>
                  <th>Other</th>
                  <!-- <th>Service</th> -->
                  <th>Total</th>
                  <th>Male</th>
                  <th>Female</th>
                  <th>Other</th>
                  <th>Total</th>

                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>{{$electorCdac->electors_male}}</td>
                  <td>{{$electorCdac->electors_female}}</td>
                  <td>{{$electorCdac->electors_other}}</td>
                  <!-- <td>{{$electorCdac->electors_service}}</td> -->
                  <td>{{$electorCdac->electors_total}}</td>
                  <td>{{$psTotalElectorMale}}</td>
                  <td>{{$psTotalElectorFemale}}</td>
                  <td>{{$psTotalElectorOther}}</td>
                  <td>{{$psTotalElector}}</td>
                </tr>
              </tbody>
            </table>

            <h1>{!! $heading_title !!}</h1>
            <table id="polling_station_data_table" class="table table-striped table-bordered" style="width:100%">
              <thead>
                <tr>
                  <!--  <th>Serial No</th> -->
                  <th>Part No</th>
                  <th>Part Name</th>
                  <th>PS No</th>
                  <th>PS Name EN</th>
                  <th>PS Type</th>
                  <th>PS Category</th>
                  <th>Location Type</th>
                  <th>Electors Male</th>
                  <th>Electors Female</th>
                  <th>Electors Other</th>
                  <th>Electors Total</th>
                  <th>Action</th>

                </tr>


              </thead>
              <tbody>
                @php
                $count = 1;

                $TotalElectorMale = 0;
                $TotalElectorFeMale = 0;
                $TotalElectorOther = 0;
                $TotalElector = 0;



                @endphp

                @forelse ($results as $key=>$listdata)

                @php

                $TotalElectorMale +=$listdata->electors_male;
                $TotalElectorFeMale +=$listdata->electors_female;
                $TotalElectorOther +=$listdata->electors_other;
                $TotalElector +=$listdata->electors_total;


                @endphp


                <tr>
                  <!--    <td>{{ $count }}</td> -->
                  <td>{{$listdata->PART_NO }}</td>
                  <td>{{$listdata->PART_NAME }}</td>
                  <td>{{$listdata->PS_NO }}</td>
                  <td>{{$listdata->PS_NAME_EN }}</td>
                  <td>{{$listdata->PS_TYPE }}</td>
                  <td>{{$listdata->PS_CATEGORY }}</td>
                  <td>{{$listdata->LOCN_TYPE }}</td>
                  <td>{{$listdata->electors_male }}</td>
                  <td>{{$listdata->electors_female }}</td>
                  <td>{{$listdata->electors_other }}</td>
                  <td>{{$listdata->electors_total }}</td>
                  <td>
                    @if(false)
                    <!-- elseif(!$diabledPSModifications || $listdata->electors_enable_edit_by_eci) -->
                    <!-- <button type="button" class="btn btn-primary editPollingStationPopUp" data-toggle="modal" data-target="#EditPsModal" data-partno="{{$listdata->PART_NO }}" data-partname="{{$listdata->PART_NAME }}" data-psno="{{$listdata->PS_NO }}" data-psname="{{$listdata->PS_NAME_EN }}" data-type="{{$listdata->PS_TYPE }}" data-category="{{$listdata->PS_CATEGORY }}" data-locationtype="{{$listdata->LOCN_TYPE }}" data-electors_male="{{$listdata->electors_male }}" data-emale="{{$listdata->electors_male }}" data-efemale="{{$listdata->electors_female }}" data-eother="{{$listdata->electors_other }}" data-total="{{$listdata->electors_total }}" data-ccode="{{$listdata->CCODE }}">Edit</button> -->
                    @endif
                    @if($listdata->electors_finalize_by_ro)
                    Finalized
                    @else
                    Not Yet Finalize
                    @endif
                  </td>
                </tr>

                @php $count++; @endphp
                @empty
                <tr>
                  <td class="text-center" colspan="14">No Data Found For Polling Station</td>
                </tr>
                @endforelse



              </tbody>
              <tfoot>
                <tr>
                  <td><b>Total</b></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><b>{{$TotalElectorMale}}</b></td>
                  <td><b>{{$TotalElectorFeMale}}</b></td>
                  <td><b>{{$TotalElectorOther}}</b></td>
                  <td><b>{{$TotalElector }}</b></td>
                  <td></td>
                </tr>
              </tfoot>
            </table>

          </div><!-- End Of  table responsive -->
        </div><!-- End Of intra-table Div -->


      </div><!-- End Of random-area Div -->

    </div><!-- End OF page-contant Div -->
  </div>
</div><!-- End Of parent-wrap Div -->


<!--EDIT POP UP STARTS-->
<div class="modal" id="EditPsModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Polling Station <span id="psname"></span> - <span id="psnoid"></span></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form class="form-horizontal" method="POST" action="{{url('roac/turnout/polling-station-electors-details-update')}}" id="PSEditForm">
        <!-- Modal body -->
        <div class="modal-body">
          {{ csrf_field() }}
          <input type="hidden" name="psccode" id="psccode" value="">

          <div class="form-group row">
            <label class="col-sm-4 form-control-label">PS Name <sup>*</sup></label>
            <div class="col-sm-8">
              <textarea id="PS_NAME_EN" class="form-control" name="PS_NAME_EN" cols="30" rows="3" required="true"></textarea>
              <span class="text-danger"></span>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-4 form-control-label">Part Name<sup>*</sup></label>
            <div class="col-sm-8">
              <textarea id="PART_NAME" class="form-control" name="PART_NAME" cols="30" rows="3" required="true"></textarea>
              <span class="text-danger"></span>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-4 form-control-label">Electors Male <sup>*</sup></label>
            <div class="col-sm-8">
              <input type="number" id="electors_male" maxsize="6" minsize="1" class="form-control" name="electors_male" required="true">
              <span class="text-danger"></span>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-4 form-control-label">Electors Female <sup>*</sup></label>
            <div class="col-sm-8">
              <input type="number" id="electors_female" maxsize="6" minsize="1" class="form-control" name="electors_female" required="true">
              <span class="text-danger"></span>
            </div>
          </div>


          <div class="form-group row">
            <label class="col-sm-4 form-control-label">Electors Other <sup>*</sup></label>
            <div class="col-sm-8">
              <input type="number" id="electors_other" maxsize="6" minsize="1" class="form-control" name="electors_other" required="true">
              <span class="text-danger"></span>
            </div>
          </div>


          <div class="form-group row">
            <label class="col-sm-4 form-control-label">Electors Total <sup>*</sup></label>
            <div class="col-sm-8">
              <input type="number" id="electors_total" maxsize="6" minsize="1" class="form-control" name="electors_total" required="true">
              <span class="text-danger"></span>
            </div>
          </div>
        </div>
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success">Update</button>
        </div>
      </form>

    </div>
  </div>
</div>
<!--EDIT POP UP ENDS-->

<!--EDIT POP UP STARTS-->
<div class="modal" id="importPollingStationModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Import Polling Station</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form class="form-horizontal" method="POST" action="{{url('roac/turnout/polling-station-import')}}" enctype="multipart/form-data" id="ImportPSForm">
        <!-- Modal body -->
        <div class="modal-body">
          {{ csrf_field() }}
          <div class="form-group row">
            <div class="col-sm-12">
              <input type="file" name="excel" id="excel" required>
              <span class="text-danger"></span>
            </div>
          </div>
          <!-- Modal footer -->
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success">Update</button>
          </div>
      </form>

    </div>
  </div>
</div>
<!--EDIT POP UP ENDS-->

@endsection

@section('script')
<!--**********FORM VALIDATION STARTS**********-->
<script type="text/javascript">
  $(document).on("click", ".editPollingStationPopUp", function() {

    partno = $(this).attr('data-partno');
    partname = $(this).attr('data-partname');
    psno = $(this).attr('data-psno');
    psname = $(this).attr('data-psname');
    type = $(this).attr('data-type');
    category = $(this).attr('data-category');
    locationtype = $(this).attr('data-locationtype');
    emale = $(this).attr('data-emale');
    efemale = $(this).attr('data-efemale');
    eother = $(this).attr('data-eother');
    total = $(this).attr('data-total');
    ccode = $(this).attr('data-ccode');

    $('#PART_NO').val(partno);
    $('#PS_NO').val(psno);
    $('#PART_NAME').val(partname);
    $('#PS_NAME_EN').val(psname);
    $('#PS_TYPE').val(type);
    $('#PS_CATEGORY').val(category);
    $('#LOCN_TYPE').val(locationtype);
    $('#electors_male').val(emale);
    $('#electors_female').val(efemale);
    $('#electors_other').val(eother);
    $('#electors_total').val(total);
    $('#psccode').val(ccode);
    $('#psnoid').text(psno);
    $('#psname').text(psname);

  });

  $('#polling_station_data_table').DataTable({
    scrollX: true,
    scrollCollapse: true,
    paging: false,
    columns: [{
        searchable: false
      },
      null,
      null,
      null,
      {
        searchable: false
      },
      {
        searchable: false
      },
      {
        searchable: false
      },
      {
        searchable: false
      },
      {
        searchable: false
      },
      {
        searchable: false
      },
      {
        searchable: false
      },
      {
        searchable: false
      }
    ],
    language: {
      searchPlaceholder: "PS No. or PS Name"
    }
  });
</script>
@endsection