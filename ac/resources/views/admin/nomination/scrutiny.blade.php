@extends('admin.layouts.ac.theme')
@section('content')
<main role="main" class="inner cover mb-3">





  <section class="statistics color-grey pt-4 pb-2">


    <div class="container-fluid">
      <div class="row">
        <div class="col-md-7 pull-left">
          <h4>{!! $heading_title !!}</h4>
        </div>

        <div class="col-md-5  pull-right text-right">

          @foreach($buttons as $button)
          <span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download Excel" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
          @endforeach

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

  @include('admin/common/form-filter')




  <div class="container-fluid">
    <!-- Start parent-wrap div -->  
    <div class="parent-wrap">
      <!-- Start child-area Div --> 
      <div class="child-area">
        <div class="page-contant">
          <div class="random-area">
            <br>



            <div class="table-responsive">
      <table class="table table-bordered ">
           <thead>
            <tr> 
              <th rowspan="2">Constituency Name</th>
              <th colspan="1">Before Scrutiny</th>
              <th colspan="4">After Scrutiny</th> 
     
            </tr>
            <tr>  
              <th>Total Nomination Applied</th>
              <th>Accepted</th> 
              <th>Rejected</th>
              <th>Withdrawn</th>
              <th>Contesting</th> 
            </tr>
          </thead>
          <tbody id="oneTimetab">   
              @foreach($results as $result)
              <tr>
                <td>{{$result['const_no']}}-{{$result['const_name']}} </td>
                <td>
                @if($result['total_applied']>0)
                <a href="<?php echo $action.'/detail/applied?'.$result['filter'] ?>">
                {{$result['total_applied']}}
                </a>
                @else
                {{$result['total_applied']}}
                @endif

                </td>
                
                <td>
                @if($result['total_accepted']>0)
                <a href="<?php echo $action.'/detail/accepted?'.$result['filter'] ?>">
                {{$result['total_accepted']}}</a>
                @else
                {{$result['total_accepted']}}
                @endif
                </td>

                <td>
                @if($result['total_rejected']>0)
                <a href="<?php echo $action.'/detail/rejected?'.$result['filter'] ?>">
                {{$result['total_rejected']}}</a>
                @else
                {{$result['total_rejected']}}
                @endif
                </td>

                <td>
                @if($result['total_withdraw']>0)
                <a href="<?php echo $action.'/detail/withdraw?'.$result['filter'] ?>">
                {{$result['total_withdraw']}}</a>
                @else
                {{$result['total_withdraw']}}
                @endif

                </td>
                
                <td>
                @if($result['total_contested']>0)
                <a href="<?php echo $action.'/detail/contested?'.$result['filter'] ?>">
                {{$result['total_contested']}}</a>
                @else
                {{$result['total_contested']}}
                @endif
                </td> 
           
                
              </tr>
              @endforeach

              <tr>
                <td>{{$totals['label']}} </td>
                <td>{{$totals['total_applied']}}</td>
                <td>{{$totals['total_accepted']}}</td>
                <td>{{$totals['total_rejected']}}</td>
                <td>{{$totals['total_withdraw']}}</td>
                <td>{{$totals['total_contested']}}</td> 
          
                
              </tr>
            
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