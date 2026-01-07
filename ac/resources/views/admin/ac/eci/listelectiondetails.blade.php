@extends('admin.layouts.theme')
@section('title', 'Create Schedule')
@section('content') 
@include('admin.includes.script')
<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
    

    <div class="page-contant">
      <div class="head-title">
              <h3><i><img src="{{ asset('theme/images/icons/tab-icon-002.png')}}" /></i>Election Details </h3>
      </div>
            
      <!-- Start Of Page Sub Setion Div --> 
       <div class="page-sub-setion"> 
      <!-- Start Of Intra section Div -->
          <div class="intra-section">
          
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
           <!-- Start Table Here -->  
            <div class="table-responsive">
              <table class="table table-bordered">
               <thead>
                      <tr><th>Code</th><th>Schedule ID</th><th>State</th><th>Cons NO</th><th>Cons Type</th><th>Phase No.</th> 
                          <th>State Phase No.</th><th>Election</th> 
                      </tr>
               </thead>
               @foreach($list_ele as $list)
                   <?php  $s=\app(App\adminmodel\StateMaster::class)->where(['ST_CODE' =>$list->ST_CODE])->first(); 
                          $ele=\app(App\adminmodel\ElectionMaster::class)->where(['election_id' =>$list->ELECTION_ID])->first(); 
                        if($list->CONST_TYPE=="AC") {
                          $cons=\app(App\adminmodel\AcMaster::class)->where(['ST_CODE' =>$list->ST_CODE])->where(['AC_NO' =>$list->CONST_NO])->first();
                          $cname= $cons->AC_NAME;  
                        }
                        else {
                          $cons=\app(App\adminmodel\PCMaster::class)->where(['ST_CODE' =>$list->ST_CODE])->where(['PC_NO' =>$list->CONST_NO])->first(); 
                          $cname= $cons->PC_NAME;  
                        }
                       
                        ?>
                     <tr><td>{{ $list->CCODE }}</td><td>{{ $list->ScheduleID }}</td> 
                     <td>{{ $s->ST_NAME }}</td><td>{{$cname }}</td><td>{{ $list->CONST_TYPE }}</td><td>{{ $list->PHASE_NO }}</td>
                     <td>{{ $list->StatePHASE_NO }}</td><td>{{$ele->election_sort_name}}-{{$ele->election_type }}</td>  </tr>
                @endforeach
              </table>
            </div><!-- End Of table-responsive Div -->
          </div>
           
          </div>  
        </div><!-- End Of intra-section Div -->   
        </div><!-- End Of page-sub-setion Div -->
      
    </div><!-- End OF page-contant Div -->


       <!-- end list-->
    
    </div> <!-- End Of child-area Div -->     
  </div><!-- End Of parent-wrap Div -->
  </div> 

@endsection