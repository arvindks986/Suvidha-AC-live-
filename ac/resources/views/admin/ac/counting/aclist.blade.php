@extends('admin.layouts.pc.theme')
@section('title', 'Create Schedule')
@section('content') 
  <?php   $st=getstatebystatecode($st_code);  
          $pc=getpcbypcno($st_code,$pc_no); 
          $url = URL::to("/"); $j=0;
    ?>
 <style type="text/css">
      th, td { white-space: nowrap;}
        <!-- .dataTables_wrapper .row:nth-child(2) .col-sm-12 { overflow: scroll;} -->
        
        html {
              overflow: scroll;
              overflow-x: hidden;
             }
              ::-webkit-scrollbar {    width: 0px; 
              background: transparent;  /* optional: just make scrollbar invisible */
              }

              ::-webkit-scrollbar-thumb {
                background: #ff9800;
                }
              div.dataTables_wrapper {margin:0 auto;} 
  </style>
 <main role="main" class="inner cover mb-3">
  
  <div class="container mt-5">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"> <h4>List All AC PC Wise</h4> </div> 
          <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b class="bolt">PC Name:</b> 
            <span class="badge badge-info">{{$pc->PC_NAME}}</span>&nbsp;&nbsp;  
            </p></div>
         
                </div>
                </div>
   <div class="row">
    <div class="col">
          @if(Session::has('success_admin'))
          <div class="alert alert-success"><strong> {{ nl2br(Session::get('success_admin')) }}</strong> </div>
       @endif   
      @if(Session::has('unsuccess_insert'))
        <div class="alert alert-danger"><strong> {{ nl2br(Session::get('unsuccess_insert')) }}</strong></div>
      @endif
      <div class="form-group float-right ml-4"> 
       <!-- @if($ropc==1)
            <span class="input-group-btn"><a href="{{url('ropc/counting/activate_allac') }}" class="btn btn-primary btn-sm mt-3 mr-3">Activate All AC for Counting</a></span>
        @endif-->
      </div> 
         
    </div>
    </div>
   
       
    <div class="card-body"> 
    @if($pc_counting->isEmpty())  <h6>Counting Data can not be add.</h6>  @endif 
   <form class="form-horizontal" id="" method="POST"  action="" >       
   <table   class="table table-striped table-bordered" style="width:100%">
        <thead> <tr> <th>Sl. No.</th><th>AC Name</th><th>Logedin User</th><th>Finalize</th> <th>Total Votes</th></tr></thead>
        <tbody>@if(!empty($lists))
            @foreach($lists as $list) 
            <?php $j++;   
                $new_table=strtolower("counting_master_".$list->st_code);
                $ac=getacbyacno($list->st_code,$list->ac_no);
                $rec=\app(App\adminmodel\PCCountingModel::class)->totalvotsbyacwise($new_table,$list->ac_no,$list->election_id);
                $luser=getloginuser($list->st_code,$list->ac_no,'AC','ARO')
                 
           ?>      
        <tr><td>{{$j}}</td><td align="left">{{$list->ac_no}}-{{$ac->AC_NAME}}</td><td>{{$luser->officername}}</td><td>@if($list->finalized_ac==0) NO @else Yes @endif </td> <td>@if(isset($rec)) {{$rec->sum_total}} @endif</td></tr>
 
           
            @endforeach 
            @endif 
        </tbody>
     
    </table>
     </form>      

    </div>
    </div>
  
  
  </div>
  </div>
  </section>
  </main>
 
@endsection