@extends('admin.layouts.ac.theme')
@section('content')
<style type="text/css">
  .loader {
   position: fixed;
   left: 50%;
   right: 50%;
   border: 16px solid #f3f3f3; /* Light grey */
   border-top: 16px solid #3498db; /* Blue */
   border-radius: 50%;
   width: 120px;
   height: 120px;
   animation: spin 2s linear infinite;
   z-index: 99999;
 }
 .list-inline li{
  display: inline;
}
td {
    height: 125px !important;
}
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
table td{position:relative;}
p.btn.btn-table {
    position: absolute;
    bottom: 0;
    width: 100%;
    text-align: center;
    left: 0;
    background: #17a2b8;
    border-radius: 0;
}
p.btn.btn-add { position: absolute;
    bottom: 0;
    width: 100%;
    /* text-align: center; */
    left: 0;
    background: #c0c0c0;
    border-radius: 0;
    /* height: 100%; */
    display: flex;
    height: 125px; padding:0;}
p.btn.btn-add a {
   padding: 50px 0 0 0;
    display: block;
    width: 100%;
}
p.btn.btn-table a, p.btn.btn-add a { color: #fff; font-size:14px;}
p.btn.btn-table:hover{background:#49a8a4;}
p.btn.btn-add:hover{background:#49a8a4;}
.second-td{
  max-width: 150px;
}
</style>

<div class="loader" style="display:none;"></div>


<section class="statistics color-grey pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col pull-left">
       <h4>{!! $heading_title !!}</h4>
     </div>

     <div class="col  pull-right  text-right">
      @if(count($results)>0)
      @foreach($buttons as $button)
      <span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="{{ $button['name'] }}" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
      @endforeach
      @endif   

@if(isset($filter_buttons) && count($filter_buttons)>0)

        @foreach($filter_buttons as $button)
        <?php $but = explode(':',$button); ?>
        <span class="pull-right" style="margin-right: 10px;">
          <span><b>{!! $but[0] !!}:</b></span>
          <span class="badge badge-info">{!! $but[1] !!}</span>

        </span>

        @endforeach

@endif	  
    </div>
  </div>
</div>  
</section>








<div class="container-fluid">
  <!-- Start parent-wrap div -->  
  <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
      @if(Session::has('flash-message'))
@if(Session::has('status'))
<?php
$status = Session::get('status');
if($status==1){
 $class = 'alert-success';
}
else{
  $class = 'alert-danger';
}
?>
@endif
<div class="alert <?php echo $class; ?> in">
  <a href="#" class="close" data-dismiss="alert">&times;</a>
  {{ Session::get('flash-message') }}
</div>
@endif
     <div class="page-contant">
       <div class="random-area">
        <br>

        <div class="table-responsive">
          <table class="table table-bordered list-table-remove" id=""> 
           <thead>
            <tr> 
              <th>PS No</th>
              <th class="second-td">PS Name</th>
              @for($i = 1; $i <= $max_blo; $i++)
              <th>BLO {{$i}}</th>
              @endfor
              <th>PRO</th>
              @for($i = 1; $i <= $max_po; $i++)
              <th>PO {{$i}}</th>
              @endfor

            </tr>

          </thead>
          @if(count($results)>0)
          <tbody id="oneTimetab">   
            @foreach($results as $result)
            <tr>
              <td>{{$result['ps_no']}} </td>
              <td class="second-td">{{$result['ps_name']}}</td>

              @for($i = 1; $i <= $max_blo; $i++)
              <td>
                @if(array_key_exists($i, $result['blo']))
                <p>{{$result['blo'][$i]['name']}}</p>
                <p style="font-family: arial;  font-size: 14px;  font-weight: 600;"><i class="fa fa-mobile-phone"></i>  {{$result['blo'][$i]['mobile']}}</p>
                <p>{{$result['blo'][$i]['is_active']}}</p>
                <p class="btn btn-table"><a href="{{$result['blo'][$i]['href']}}" class=""><i class="fa fa-edit"></i> Edit</a></p>
                @else
                <p class="btn btn-add"><a href="{{$add_new_url}}?role_id=33&ps_no={{$result['ps_no']}}" class=""><i class="fa fa-plus"></i> Add</a></p>
                @endif
              </td>
              @endfor
              <!-- BLO END -->

              <td>
                @if(count($result['pro'])>0)
                <p>{{$result['pro']['name']}}</p>
                <p style="font-family: arial;  font-size: 14px;  font-weight: 600;"><i class="fa fa-mobile-phone"></i> {{$result['pro']['mobile']}}</p>
                <p>{{$result['pro']['is_active']}}</p>
                <p class="btn btn-table"><a href="{{$result['pro']['href']}}" class=""><i class="fa fa-edit"></i> Edit</a></p>
                @else
                <p class="btn btn-add"><a href="{{$add_new_url}}?role_id=35&ps_no={{$result['ps_no']}}" class=""><i class="fa fa-plus"></i> Add</a></p>
                @endif
              </td>

              @for($i = 1; $i <= $max_po; $i++)
              <td>
                @if(array_key_exists($i, $result['po']))
                <p>{{$result['po'][$i]['name']}}</p>
                <p style="font-family: arial;  font-size: 14px;  font-weight: 600;"><i class="fa fa-mobile-phone"></i>  {{$result['po'][$i]['mobile']}}</p>
                <p>{{$result['po'][$i]['is_active']}}</p>
                <p class="btn btn-table"><a href="{{$result['po'][$i]['href']}}" class=""><i class="fa fa-edit"></i> Edit</a></p>
                @else
                <p class="btn btn-add"><a href="{{$add_new_url}}?role_id=34&ps_no={{$result['ps_no']}}" class=""><i class="fa fa-plus"></i> Add</a></p>
                @endif
              </td>
              @endfor

            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td colspan="{{$max_blo+$max_po+4}}">
                {!! urldecode($pag_results->appends(Request::except('page'))->render()) !!}
              </td>
            </tr>
          </tfoot>
          @else
          <tbody>
            <tr>
              <td colspan="{{$max_blo+$max_po+4}}">
                No Record Found.
              </td>
            </tr>
          </tbody>
          @endif
        </table>
      </div><!-- End Of  table responsive -->  
    </div><!-- End Of intra-table Div -->   


  </div><!-- End Of random-area Div -->

</div><!-- End OF page-contant Div -->
</div>      
</div><!-- End Of parent-wrap Div -->
</div> 


@endsection
<?php /*
@section('script')
<script type="text/javascript">
$(document).ready(function() {
    $('#server-side-datatable').DataTable({
        serverSide: true,
        ordering: true,
        searching: true,
        ajax: function ( data, callback, settings ) {
            var out = [];
 
            for ( var i = data.start, ien=data.start+data.length; i < ien; i++ ) {
                out.push( [ i+'-1', i+'-2', i+'-3', i+'-4', i+'-5' ] );
            }
 
            setTimeout( function () {
                callback( {
                    draw: data.draw,
                    data: out,
                    recordsTotal: <?php echo count($results) ?>,
                    recordsFiltered: <?php echo count($results) ?>
                } );
            }, 50 );
        },
        scrollY: 200,
        scroller: {
            loadingIndicator: true
        },
    });
});
</script>
@endsection
*/?>