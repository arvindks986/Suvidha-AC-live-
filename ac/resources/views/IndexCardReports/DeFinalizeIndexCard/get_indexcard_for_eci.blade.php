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
                  <th>S.No.</th>
                  <th>State Name</th>
                  <th>Constituency Name</th>
                  <th>Request Date</th>
                  <th>Approved/Rejected Date</th>
                  <th>Issue</th>
                  <th>Status</th>
                  <th>Download</th>
                  <th>Action</th>
                </tr>
          </thead>
  
          <tbody>  
            <?php $i = 1; ?> 
            @foreach($results as $result)
            <tr>
             <td>{!! $i; !!}</td>
             <td>{!! $result['st_name']; !!}</td>
             <td>{!! $result['ac_name']; !!}</td>
             <td>{!! $result['submitted_at']; !!}</td>
             <td>{!! $result['review_at']; !!}</td>
             <td>{!! $result['issue'] !!}</td>
             <td>{!! $result['review_status']; !!}</td>
             <td>
              <a href="{!! $result['file_url']; !!}" target="_blank"><i class="fa fa-download" aria-hidden="true" disabled=""></i></a></td>
              <td>
                <?php if($result['status_id'] == '0'){ ?>
                  <button type="button" class="btn btn-info btn-lg" onclick="myfunction(<?php echo $result['id'] ?>)" >Click Here</button>
                <?php }else{ ?>
                  --
                <?php } ?>
              </td>
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



<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
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

      <form class="form-vertical" action="{!! $action !!}" method="POST" enctype= "multipart/form-data">
      
      <div class="modal-header">
                  <h6 style="width: 100%;text-align: center;text-decoration: underline;">Please Select Action Status</h6>

        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">

        
              
        <input type="hidden" name="id" id="id" value="{{$id}}">
        <input type="hidden" name="election_id" value="{{$election_id}}">
        <input type="hidden" name="_token" value="<?php echo csrf_token() ?>">
        
        
              <div class="form-group row">
                <div class="col-sm-6"><p style="font-size: 15px;">*Status:</p> </div>
                <div class="col-sm-6">
                <div class="form-check-inline">
                  <input class="form-check-input" type="radio" name="review_status" id="review_status_1" value="1" <?php if($review_status == 1){ ?> checked <?php } ?>>
                  <label class="form-check-label" for="review_status_1">
                    Accept
                  </label>
                </div>
                <div class="form-check-inline">
                  <input class="form-check-input" type="radio" name="review_status" id="review_status_2" value="2" <?php if($review_status == 2){ ?> checked <?php } ?>>
                  <label class="form-check-label" for="review_status_2">
                    Reject
                  </label>  
                </div>
                </div>
              </div>
              
              <!-- checbox hidden -->
              <div class="form-group row" id="hidrow" style="display: none;">
                <div class="col-sm-6"><p style="font-size: 15px;">Please Mark Checked Sections:</p> </div>
                <div class="col-sm-6">
                  <div class="form-check">
                    <input class="form-check-input" name="issue[]" type="checkbox" id="gridCheck1" value="Index Card Issue">
                    <label class="form-check-label" for="gridCheck1">
                      Index Card Issue.
                    </label>
                  </div>
                  
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox"  name="issue[]" id="gridCheck3" value="Signature Issue">
                    <label class="form-check-label" for="gridCheck3">
                      Signature Issue.
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="issue[]" id="gridCheck4" value="Coloured Copy Issue">
                    <label class="form-check-label" for="gridCheck4">
                      Coloured Copy Issue.
                    </label>
                  </div>
          
          <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="issue[]" id="gridCheck2" value="Quality Issue">
                    <label class="form-check-label" for="gridCheck2">
                      Quality Issue.
                    </label>
                  </div>
                </div>
              </div>
            
            <!-- ends -->
            <div class="form-group row">
              <div class="col-sm-12">
              <textarea cols="10" rows="5" name="comment" class="form-control" placeholder="Comments if any">{!! $comment !!}</textarea>
              </div>
            </div>         
          
</div>
      
      <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
      </div>

      </form>
    </div>

  </div>
</div>

<script type="text/javascript">
function filter(){
  var url = "<?php echo $current_page ?>";
  var query = '';

    if($("#election_id").val() != ''){
      query += '&election_id='+$("#election_id").val();
    }
    window.location.href = url+'?'+query.substring(1);
}

function myfunction(id){
  $('#id').val(id);    
  $('#myModal').modal('show');
}

$("#review_status_2").click(function(){
  $("#hidrow").toggle();
});
$("#review_status_1").click(function(){
  $("#hidrow").hide();
});
</script>

<?php if($id != '' || $id != 0){ ?>
<script type="text/javascript">
  $(document).ready(function(e){
    var review_status = "<?php echo $review_status; ?>";
    myfunction(<?php echo $id; ?>);
    if(review_status=='2'){
      $('#review_status_2').click();
    }
  });
</script>
<?php } ?>
@endsection