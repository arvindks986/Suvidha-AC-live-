@extends('admin.layouts.ac.theme')
@section('bradcome', 'Treasury Account')
@section('content')
<link rel="stylesheet" href="{{ asset('theme/css/custom-dark.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('theme/css/dark_custom.css') }} " type="text/css">
<link rel="stylesheet" href="{{ asset('theme/css/prenom.css')}}" />
<main role="main" class="inner cover mt-4">
    <section>
        <div class="container-fluid">
            <div class="row">
                <div class="card text-left" style="width:100%; margin:0 auto;">
                    <div class=" card-header">
                        <div class=" row">
                            <div class="col-md-7">
                                <h4>{{$heading_title}}</h4>
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
                                    <th>S.No</th>
                                    <th>State Name</th>
                                    <th>District Name</th>
                                    <th>District Code of Bihar</th>
                                    <th>Transaction Code</th>
                                    <th>Treasury Account No</th>
									<th>Merchant Code</th>
									@if(isset($results[0]['is_verified']))<th>Is Verified</th>@endif
                                </tr>
                            </thead>
                            <tbody>
                              @if(count($results)>0)
                                @foreach($results as $item)
                                  <tr>
                                    <td>{{$item['sno']}}</td>
                                    <td>{{$item['st_name']}}</td>
                                    <td>{{$item['dist_code_nomination'].'-'.$item['district_treasury_name']}}</td>
                                    <td>{{$item['dist_code_bihar']}}</td>
                                    <td>{{$item['trs_code']}}</td>
                                    <td>{{$item['hd_ac1']}}</td>
                                    <td>{{$item['merchant_code']}}</td>
                                    @if(isset($item['is_verified']))
                                    <td>
                                        <div class="row">
                                            <div class="col">
                                                <label class="radioBtn">
                                                <input type="checkbox" name="isVerified"
                                                        value="1" @if($item['is_verified']!='1') st_code='{{ $item['st_code'] }}' dist_no={{ $item['dist_code_nomination'] }} class="isVerified" @endif
                                                        {{ $item['is_verified']=='1' ? 'checked disabled' : '' }}>
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </td>
                                    @endif
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
@if(session('success_mes'))
	<script type="text/javascript">
	success_messages("{{session('success_mes') }}");
</script>
@endif

@if(session('error_mes'))
<script type="text/javascript">
	error_messages("{{session('error_mes') }}");
</script>
@endif

<script>
    $(document).ready(function(e) { 

        $('.isVerified').change(function(e) {
			var filter = $('.isVerified:checked').val();
            if(!filter){
                filter = '';
            }
            st_code = $(this).attr('st_code');
            dist_no = $(this).attr('dist_no');
            $.ajax({
			url: "{{ url('/acceo/is_data_verified') }}",
			type: 'POST',
			data: '_token=<?php echo csrf_token() ?>&is_ceo_verified='+filter+'&st_code='+st_code+'&dist_no='+dist_no,
			dataType: 'json',
			beforeSend: function() {
			},
			complete: function() {
			},
			success: function(json) {
				if(json['success']){
                    location.reload();
                }
			},
			error: function(data) {
			}
			});

			// let new_url = addParam('status', filter);
			// window.location.href = new_url;
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