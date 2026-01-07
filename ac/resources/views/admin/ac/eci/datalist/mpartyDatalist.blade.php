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

@if($newdparty > 0)
<div class="alert alert-success">We have  <b>{{$newdparty}}</b> new M_party{{ ($newdparty > 1) ? 's' : '' }} </div>
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
      <div class="col-md-4 pull-left">
        <h4>{!! $heading_title !!}</h4>
      </div>

      <div class="col-md-8  pull-right text-right">
      <div class="btn-group" role="group" aria-label="Basic example">

        <button class="btn btn-primary" type="button" onclick="showALL()">Show ALL</button>
        <button class="btn btn-info" type="button" onclick="showNewPartys()">Show New Partys</button>
        <button class="btn btn-warning" type="button" onclick="showUpdatedPartys()">Show Updated Partys</button>
        <form action="{{$update_link}}" method="get">
          <button class="btn btn-success">{{($newdparty > 0) ? 'Insert & Update M_Party' : 'Update M_Party'}}  </button>  
        </form>
      </div>
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
                  <th>CCODE</th>
                  <th>PARTYABBRE</th>
                  <th>PARTYHNAME</th>
                  <th>PARTYNAME</th>
                  <!-- <th>PARTYTYPE</th>-->
                  <th>Status</th> 
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
                <tr class="rowdata-list {{ ($listdata['isExist'] > 0) ? 'isPresentinMaster' : 'notPresentinMaster' }} {{ ($listdata['UPDATE_DATE'] != '') ? 'isUpdatedParty' : '' }}">
                  <td>{{ $count }}</td>
                  <td>{{$listdata['CCODE'] }}</td>
                  <td>{{$listdata['PARTYABBRE'] }}</td>
                  <td>{{$listdata['PARTYHNAME']  }}</td>
                  <td>{{$listdata['PARTYNAME']  }}</td>
                  <!-- <td>{{$listdata['PARTYTYPE']  }}</td>-->
                  <td>@if($listdata['deleteflag'] == 'N') Active @elseif($listdata['deleteflag'] == 'I') Inactive  @elseif($listdata['deleteflag'] == 'Y') Deleted @else $listdata['deleteflag'] @endif</td> 
                  <td>{{$listdata['INSERT_DATE']  }}</td>
                  <td>{{$listdata['UPDATE_DATE']  }}</td>
                  <td>{!! ($listdata['isExist'] == 0) ? '<label class="badge badge-danger default-cursor">NO</label>' : '<label class="badge badge-success default-cursor">YES</label>' !!}</td>
                </tr>

                @php $count++; @endphp
                @empty
                <tr>
                  <td class="text-center" colspan="8">No Data Found For Polling Station</td>
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
  function showALL(){
    $('.rowdata-list').css('display','table-row')
  }
  function showNewPartys(){
    $('.rowdata-list').css('display','none')
    $('.notPresentinMaster').css('display','table-row')
  }
  function showUpdatedPartys(){
    $('.rowdata-list').css('display','none')
    $('.isUpdatedParty').css('display','table-row')
  }
</script>
@endsection