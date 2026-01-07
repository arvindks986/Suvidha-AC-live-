@extends('admin.layouts.ac.theme')
@section('title', 'Suvidha')
@section('bradcome', '')
@section('content')


@if($errors->any())
<div class="alert alert-info">{{$errors->first()}}</div>
@endif

@if (session('error'))
<div class="alert alert-info">{{ session('error') }}</div>
@endif

@if($newdsymbol > 0)
<div class="alert alert-success">We have  <b>{{$newdsymbol}}</b> new Symbol{{ ($newdsymbol > 1) ? 'S' : '' }} </div>
@endif
<style type="text/css">
  .loader {
    position: fixed;
    left: 50%;
    right: 50%;
    border: 16px solid #f3f3f3;
    /* Light grey */
    border-top: 16px solid #3498db;
    /* Blue */
    border-radius: 50%;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
    z-index: 99999;
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }

  .default-cursor{
    cursor: default;
  }
</style>

<div class="loader" style="display:none;"></div>


<section class="statistics color-grey pt-4 pb-2">

  <div class="container-fluid">
    <div class="row">
      <div class="col-md-7 pull-left">
        <h4>{!! $heading_title !!}</h4>
      </div>

      <div class="col-md-5  pull-right text-right">
      <form action="{{$update_link}}" method="get">
        <button class="btn btn-success">{{($newdsymbol > 0) ? 'Insert & Update Symbols' : 'Update Symbols'}}  </button>
      </form>
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
          <br>



          <div class="table-responsive">

            <table id="data_table_table" class="table table-striped table-bordered" style="width:100%">
              <thead>

                <tr>
                  <th colspan="13" class="text-center">{!! $heading_title !!}</th>
                </tr>


                <tr>
                  <th>Serial No</th>
                  <th>SYMBOL_NO</th>
                  <th>SYMBOL_DES</th>
                  <th>SYMBOL_HDES</th>
                  <th>inserted date</th>
                  <th>updated date</th>
                  <th>Is Present in Master</th>
                  <!--   <th>Action</th> -->

                </tr>


              </thead>
              <tbody>
                @php
                $count = 1;
                @endphp

                @forelse ($results as $key=>$listdata)

                <tr>
                  <td>{{ $count }}</td>
                  <td>{{$listdata['SYMBOL_NO'] }}</td>
                  <td>{{$listdata['SYMBOL_DES'] }}</td>
                  <td>{{$listdata['SYMBOL_HDES']  }}</td>
                  <td>{{$listdata['INSERT_DATE']  }}</td>
                  <td>{{$listdata['UPDATE_DATE']  }}</td>
                  <td>{!! ($self->getSymbol($listdata['SYMBOL_NO']) == 0) ? '<label class="badge badge-danger default-cursor">NO</label>' : '<label class="badge badge-success default-cursor">YES</label>' !!}</td>
                </tr>

                @php $count++; @endphp
                @empty
                <tr>
                  <td class="text-center" colspan="7">No Data Found For Polling Station</td>
                </tr>
                @endforelse
              </tbody>
            </table>

          </div><!-- End Of  table responsive -->
        </div><!-- End Of intra-table Div -->


      </div><!-- End Of random-area Div -->

    </div><!-- End OF page-contant Div -->
  </div>
</div><!-- End Of parent-wrap Div -->
</div>

@endsection

@section('script')
<script type="text/javascript">
  
</script>
@endsection