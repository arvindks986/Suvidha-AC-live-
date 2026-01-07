@extends('admin.layouts.ac.dashboard-theme')
@section('content')
<style type="text/css">
  .heading th{
    text-transform: capitalize;
    text-align: left;
  }
  .complain-heading-main{
    text-transform: capitalize;
    text-align: center;
  }
</style>

<?php if(Auth::user()->designation == 'ROAC'){
			$prefix 	= 'roac';
		}else if(Auth::user()->designation == 'CEO'){	
			$prefix 	= 'acceo';
		}else if(Auth::user()->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if(Auth::user()->role_id == '7'){
			$prefix 	= 'eci';
		}   ?>



<section class="dashboard-header pt-3 pb-3">
  <div class="container-fluid">
  
        
      <form id="generate_report_id" class="row" method="get" onsubmit="return false;">
  

          <div class="form-group col-md-3"> <label>State</label> 
          
            <select name="st_code" id="st_code" class="form-control" onchange ="filter('1')">
              <option value="">Select State</option>
            @foreach($states as $iterate_state)
              @if($st_code == $iterate_state['st_code'])
                <option value="{{$iterate_state['st_code']}}" selected="selected" >{{$iterate_state['st_name']}}</option> 
              @else 
                <option value="{{$iterate_state['st_code']}}">{{$iterate_state['st_name']}}</option> 
              @endif  
            @endforeach
        
            </select>
          </div>

          <div class="form-group col-md-3"> <label>AC </label> 
          
            <select name="ac_no" id="ac_no" class="form-control" onchange ="filter('0')">
            <option value="">Select AC</option>
            @foreach($acs as $result)
              @if($ac_no == $result['ac_no'])
                <option value="{{$result['ac_no']}}" selected="selected" >{{$result['ac_no']}}-{{$result['ac_name']}}</option> 
              @else 
                <option value="{{$result['ac_no']}}" >{{$result['ac_no']}}-{{$result['ac_name']}}</option> 
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
                 <div class="col"><h4>Bye Election Index-Card Reports</h4></div>
                </div> <!-- end col-->
                </div><!-- end row-->
              
            <div class="card-body"> 

    

           <div class="table-responsive">
          <table class="table table-bordered " id="list-table">
            <tr>
              <th>State Name</th>
              <th>AC Name</th>
              <th><p style="">View Report</p></th>
              <th><p style="">Check for final preview</p></th>
       
            </tr>
          @if( count($results)>0)
            
            @foreach($results as $result)
              <tr>
                <td>{!! $result['st_name'] !!}</td>
                <td>{!! $result['ac_no'] !!}-{!! $result['ac_name'] !!}</td>
                
                
                <?php  if (verifyreport_index($result['st_code'],$result['ac_no']) != 0){ ?>
									
							 <td class="text-center">
								<a href="{!! url('/'.$prefix.'/index-card?st_code='.$result['st_code'].'&ac_no='.$result['ac_no']) !!}" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
							  </td>
				<?php   } else { ?>		
					  
					   <td class="text-center">
						<a href="{!! url('/'.$prefix.'/index-card?st_code='.$result['st_code'].'&ac_no='.$result['ac_no']) !!}" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
					</td>
									
				<?php } ?>	
									
									
									
									<td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,'{{$result['st_code']}}','{{$result['ac_no']}}')" 
											 <?php  if (verifyreport_index($result['st_code'],$result['ac_no']) != 0){ ?> checked <?php } ?> >
											<?php  if (verifyreport_index($result['st_code'],$result['ac_no']) != 0){ ?>
                                              
                                            <div class="w-75"></div>
											
											<?php   } else { ?>
                                               
                                              <span class="w-75">Click to final preview</span>
											  
											<?php } ?>
                                          
                                     </td>    
              </tr>
            @endforeach
          @else
          <tbody>
          <tr>
            <td colspan="6" cellpadding='5' align="center">
              No Record Found.
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

function insert_verify(obj,st_code,ac_no) {

  if($(obj).is(":checked")){

    $.ajax({
    type: "GET",
    url: "./bye-report-listing-verify-checkbox",
    data: {is_verified:1,st_code:st_code, ac_no:ac_no},
    dataType: "JSON",
    success: function(data) {
    location.reload();
    },
    error: function(data){
            window.console.log(data);
        }

    });

  }else{
    //alert("Not checked"); //when not checked

    $.ajax({
    type: "GET",
    url: "./bye-report-listing-verify-checkbox",
    data: {is_verified:0, st_code:st_code, ac_no:ac_no},
    dataType: "JSON",
    success: function(data) {
    location.reload();
    },
    error: function(data){
            window.console.log(data);
        }

    });
  }
}


</script>

<script type="text/javascript">

function filter(st){
  var url = "<?php echo $current_page ?>";
  var query = '';
    
    if($("#st_code").val() != ''){
      query += '&st_code='+$("#st_code").val();
    }
	
	if(st == '0'){
		if($("#ac_no").val() != ''){
		  query += '&ac_no='+$("#ac_no").val();
		}
	}
	
    window.location.href = url+'?'+query.substring(1);
}
</script>
@endsection