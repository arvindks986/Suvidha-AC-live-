@extends('admin.layouts.ac.theme')
@section('bradcome', 'APPOINTMENT REPORT')
@section('content')
<main role="main" class="inner cover mt-4">
    <section>
        <div class="container-fluid">
            <div class="row">
                <div class="card text-left" style="width:100%; margin:0 auto;">
                    <div class=" card-header">
                        <div class=" row">
                            <div class="col-md-7">
                                <h4>{{$heading_title}}</h4>
                                <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                    <i class="fa fa-calendar"></i>&nbsp;
                                    <span></span> <i class="fa fa-caret-down"></i>
                                </div>
                            </div>
                            <div class="col-md-5 text-right">
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
                                    <th colspan="4" class="text-center"></th>
                                    <th colspan="4" class="text-center">APPOINTMENT DETAILS COUNT</th>
                                </tr>
                                <tr>
                                    <th>S.No</th>
                                    <th>State Name</th>
									<th>District Name</th>
                                    <th>AC Name</th>
									<th>APPOINTMENT PENDING</th>
                                    <th>APPOINTMENT GIVEN</th>
                                    <th>TOTAL APPOINTMENT</th>
                                </tr>
                            </thead>
                            <tbody>
                              @if(count($results)>0)
                                @foreach($results as $item)
                                <tr>
                                    <td>{{$item['sno']}}</td>
                                    <td>{{$item['st_name']}}</td>
                                    <td>{{$item['dist_name']}}</td>
                                    <td>{{$item['ac_name']}}</td>
                                    <td><a href="{{ $item['pending_appointment_url'] }}">{{$item['pending_appointment']}}</a></td>
                                    <td><a href="{{ $item['done_appointment_url'] }}">{{$item['done_appointment']}}</a></td>
                                    <td><a href="{{ $item['total_nomination_url'] }}">{{$item['total_nomination']}}</a></td>
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

        function addParam(key,val) {
          var currentUrl = "<?php echo url()->full(); ?>";
          var url = new URL(currentUrl);
          url.searchParams.set(key, val);
          return url.href; 
        }
    });
</script>
@endsection