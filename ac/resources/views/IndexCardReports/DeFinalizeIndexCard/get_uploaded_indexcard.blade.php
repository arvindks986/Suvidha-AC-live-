@extends('admin.layouts.ac.dashboard-theme')
@section('content')
<section class="dashboard-header pt-3 pb-3">
  <div class="container-fluid">
  
        
      <form id="generate_report_id" class="row" method="get" onsubmit="return false;">
  

          <div class="form-group col-md-3"> <label>Election</label> 
          
            <select name="election_id" id="election_id" class="form-control" onchange ="filter()">
              <option value="">Seelct</option>
            @foreach($elections as $election)
              @if($election_id == $election['election_id'])
                <option value="{{$election['election_id']}}" selected="selected" >{{$election['election_type']}}</option> 
              @else 
                <option value="{{$election['election_id']}}">{{$election['election_type']}}</option> 
              @endif  
            @endforeach
        
            </select>
          </div>
         
        </form>   
  
    
  </div>
</section>

<main role="main" class="inner cover mb-3 mt-3">
<section>  

  <div class="container-fluid">
  <div class="row">   


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
      <div class="alert <?php echo $class; ?>">
        {{ Session::get('flash-message') }}
      </div>
    @endif  


<div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h4>{!! $heading_title !!}</h4></div> 
                  <div class="col"><p class="mb-0 text-right">

                    @if(isset($filter_buttons) && count($filter_buttons)>0)
                            @foreach($filter_buttons as $button)
                                <?php $but = explode(':',$button); ?>
                                <b>{!! $but[0] !!}:</b>
                                <span class="badge badge-info">{!! $but[1] !!}</span>
                            @endforeach  
                    @endif
                



                    &nbsp;&nbsp; 
                  <b></b> 
                   <span class="badge badge-info"></span>&nbsp;&nbsp;  </p></div>
                </div> <!-- end col-->
                </div><!-- end row-->
              
            <div class="card-body"> 

    

           <div class="table-responsive">
          <table class="table table-bordered " id="example">

          @if(count($results)>0)
          <thead>
            <tr>
                  <th >SI No</th>
                  <th >Constituency Name</th>
                  <th >Request Date</th>
                  <th >Approved/Rejected Date</th>
                  <th>Issue</th>
                  <th >Status</th>
                  <th >Reason (If Rejected)</th>
                </tr>
          </thead>
  
          <tbody>  
            <?php $i = 1; ?> 
            @foreach($results as $result)
            <tr>
             <td>{!! $i; !!}</td>
             <td>{!! $result['ac_name']; !!}</td>
             <td>{!! $result['submitted_at']; !!}</td>
             <td>{!! $result['review_at']; !!}</td>
             <td>{!! $result['issue']; !!}</td>
             <td>{!! $result['review_status']; !!}</td>
             <td>{!! $result['review_comment']; !!}</td>
            </tr>
            <?php $i++; ?>
            @endforeach
          </tbody>

          @else
          <tbody>
          <tr>
            <td colspan="15" cellpadding='5' align="center">
              Please Select a Election Type.
            </td>
          </tr>
          </tbody>
          @endif

           </table>
         </div><!-- End Of  table responsive -->  
       </div>
     </div>
      </div><!-- End Of intra-table Div -->   
        
         
      </div><!-- End Of random-area Div -->
      
</section>
</main>


<script type="text/javascript">
function filter(){
  var url = "<?php echo $current_page ?>";
  var query = '';

    if($("#election_id").val() != ''){
      query += '&election_id='+$("#election_id").val();
    }
    window.location.href = url+'?'+query.substring(1);
}
</script>
@endsection