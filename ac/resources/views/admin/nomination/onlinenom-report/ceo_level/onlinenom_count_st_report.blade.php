@extends('admin.layouts.ac.theme')
@section('bradcome', 'Nomination Count Report')
@section('content')
<main role="main" class="inner cover mt-4">
@php
  $getallsche = getallschedule();
@endphp
    <section>
        <div class="container-fluid">
            <div class="row">
                <div class="card text-left" style="width:100%; margin:0 auto;">
                    <div class=" card-header">
                        <h4>{{$heading_title}}</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <select class="form-control" name="election_type_id" id="election_type_id">
                                    <option value="" {{ ($election_type_id=='') ? 'selected' : '' }}>All Election Type</option>
                                    <option value="3" {{ ($election_type_id=='3') ? 'selected' : '' }}>AC-GENERAL</option>
                                    <option value="4" {{ ($election_type_id=='4') ? 'selected' : '' }}>AC-BYE</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <select class="form-control" name="election_phase" id="election_phase">
                                    <option value="" {{ ($election_phase=='') ? 'selected' : '' }}>All Phase</option>
                                    @foreach ($getallsche as $each_data)
                                        <option value="{{ $each_data->SCHEDULEID }}" {{ ($election_phase==$each_data->SCHEDULEID) ? 'selected' : '' }}>{{ $each_data->SCHEDULEID.'-'.'Phase' }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                    <i class="fa fa-calendar"></i>&nbsp;
                                    <span></span> <i class="fa fa-caret-down"></i>
                                </div>
                            </div>
                            
                            <div class="col-md-2 text-right">
                                @foreach($buttons as $button)
                                    <span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="card-body"> 
                      @if(isset($filter_buttons) && count($filter_buttons)>0)
                      <section class="statistics pt-4 pb-2">
                        <div class="container-fluid">
                          <div class="row">
                            <div class="col-lg-12">
                              @foreach($filter_buttons as $button)
                                  <?php $but = explode(':',$button); ?>
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

                      {{-- @include('admin/common/form-filter') --}}


                        <table id="list-table" class="table table-striped table-bordered table-hover"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th colspan="3" class="text-center"></th>
                                    <th colspan="1" class="text-center">NOMINATION COUNT</th>
                                </tr>
                                <tr>
                                    <th>S.No</th>
                                    <th>State Name</th>
                                    <th>District Name</th>
                                    {{-- <th>Offline Nomination</th> --}}
                                    <th>Online Nomination</th>
                                    {{-- <th>Total Nomination</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                              @if(count($results)>0)
                                @foreach($results as $item)
                                  <tr>
                                    <td>{{$item['sno']}}</td>
                                    <td>{{$item['st_name']}}</td>
                                    <td><a href="{{$item['action_url']}}">{{$item['dist_name']}}</a></td>
                                    {{-- <td>{{$item['offline_nom']}}</td> --}}
                                    <td><a href="{{ $item['candidate_list_url'] }}">{{$item['online_nom']}}</a></td>
                                    {{-- <td>{{$item['total_nom']}}</td> --}}
                                  </tr>
                                @endforeach
                              @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
@section('script')

<script>
    $(document).ready(function(e) { 
        var start = moment(new Date("<?php echo isset($between[0])? $between[0] : Date('m/d/Y') ?>"));
        var end   = moment(new Date("<?php echo isset($between[1])? $between[1] : Date('m/d/Y') ?>"));
        function cb(start, end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

    jQuery('#reportrange').daterangepicker({
       ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 14 Days': [moment().subtract(13, 'days'), moment()]           
        //    'This Month': [moment().startOf('month'), moment().endOf('month')],
           //'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        maxDate: new Date()
    }, cb);

    cb(start, end);

    <?php if(empty($between[0])){ ?>
        $('#reportrange span').html("--------------All----------------");
    <?php } ?>

        $("#all").click(function(e) {
            var url = "<?php echo url('/eci/online_nom/count-report') ?>";
            window.location.href = url;
        });

        $('#reportrange').on('hide.daterangepicker', function(ev, picker) {
            var start = picker.startDate.format('MM/DD/YYYY');
            var end = picker.endDate.format('MM/DD/YYYY');
            var val   = start +' - '+ end;
            var newurl = addParam('date', val);
			      window.location.href = newurl;
        });

        $("#election_type_id, #election_phase").change(function(e) {
            val = $(this).val();
            if($(this)[0].id == 'election_type_id'){
                var newurl = addParam('election_type_id', val);
			    window.location.href = newurl;
            }else if($(this)[0].id == 'election_phase'){
                var newurl = addParam('election_phase', val);
			    window.location.href = newurl;
            }
        });

        function addParam(key,val) {
          var currentUrl = "<?php echo url()->full(); ?>";
          var url = new URL(currentUrl);
          url.searchParams.set(key, val);
          return url.href; 
        }
    });
</script>
@endsection