@extends('admin.layouts.theme')
@section('title', 'Create Schedule')
@section('content') 
@include('admin.includes.script')
@include('admin.includes.list_script')
<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
    

    <div class="page-contant">
      <div class="head-title">
              <h3><i><img src="{{ asset('admintheme/images/icons/tab-icon-002.png')}}" /></i>Election Details </h3>
      </div>
            
      <!-- Start Of Page Sub Setion Div --> 
       <div class="page-sub-setion"> 
      <!-- Start Of Intra section Div -->
          <div class="intra-section">
          
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">

           <!-- Start Table Here -->  
            <div class="table-responsive">
           <table id="example" class="table table-striped table-bordered" style="width:100%">
               <thead><tr>
                    <th>Sr. No.</th><th>State</th><th>Constituency</th><th>Constituency Type</th>  
                    <th>State Phase No.</th><th>Election</th><th>Un-Assign</th> 
                    </tr>
               </thead><tbody><?php $i=0; ?>

               @foreach($list_ele as $list)
                   <?php $i++;  $s=\app(App\adminmodel\StateMaster::class)->where(['ST_CODE' =>$list->ST_CODE])->first(); 
                          $ele=\app(App\adminmodel\ElectionMaster::class)->where(['election_id' =>$list->ELECTION_TYPEID])->first(); 
                        if($list->CONST_TYPE=="AC") {
                          $cons=\app(App\adminmodel\AcMaster::class)->where(['ST_CODE' =>$list->ST_CODE])->where(['AC_NO' =>$list->CONST_NO])->first();
                          $cname= $cons->AC_NAME;  
                        }
                        else {
                          $cons=\app(App\adminmodel\PCMaster::class)->where(['ST_CODE' =>$list->ST_CODE])->where(['PC_NO' =>$list->CONST_NO])->first(); 
                          $cname= $cons->PC_NAME;  
                        }
                       
                        ?>

                     <tr><td>{{$i}}</td><td>{{$s->ST_CODE}}-{{$s->ST_NAME}}</td><td>{{$list->CONST_NO}} - {{$cname }}</td><td>{{ $list->CONST_TYPE }}</td><td>{{ $list->StatePHASE_NO }}</td>
                    <td>{{$ele->election_sort_name}}-{{$ele->election_type }}</td> 
                    <td> <a href="{{ url('/eci/unassign') }}/{{$list->ST_CODE}}/{{$list->CONST_NO}}/{{$sched_id}}/{{$list->StatePHASE_NO}}">Un-Assign</a></td>  </tr>
                @endforeach
              </tbody>
              <tfoot>
                    <tr>
                    <th>Sr. No.</th><th>State</th><th>Constituency</th><th>Constituency Type</th>  
                    <th>State Phase No.</th><th>Election</th><th>Un-Assign</th> 
                    </tr>
        </tfoot>
            </table>
            </div><!-- End Of table-responsive Div -->
            <div class="btns-actn"><center>
              <a href="{{url('eci/election-details') }}"><input type="submit" value="Back"> </a>
                      </center>
               </div> 
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