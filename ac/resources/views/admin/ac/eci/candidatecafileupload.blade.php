@extends('admin.layouts.ac.theme')
@section('title', 'Candidate CA Upload')
@section('bradcome', 'Candidate CA Upload')
@section('content') 
<style type="text/css">
  .dataTables_empty{

    background-color: lightcyan;
  }
  th {
    text-align:center
}
</style>

<?php
$cons_no= !empty($_REQUEST['state_id']) ? $_REQUEST['state_id'] : 0;
//dd($cons_no);
$acdetails = getacbyacno($user_data->st_code, $cons_no);
 
$acName = !empty($_REQUEST['ac_id']) ? $_REQUEST['ac_id'] : 0;
 
$all_ac="";


//print_r($cons_no); echo "-----";print_r($acName);
//$receivefilter_value=!empty($_GET['receivefilter']) ? $_GET['receivefilter'] : 0;
 ?>
 
<section class="data_table mt-3 form">
  <div class="container-fluid">
         @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
        @endif
          @if (session('error_mes'))
          <div class="alert alert-danger"> {{session('error_mes') }}</div>
        @endif
<?php $i=1; $url = URL::to("/"); $j=0; ?>
   
  <div class="row d-flex align-items-center">
<div class="col">  <h5>Candidate Upload CA Report</h5></div>
    <div class="col">
    <form class="form-inline float-right">
         
      <!--   <div class="form-group float-right ml-4">
                <div class="input-group ">
                    <input type="text" class="form-control input-lg"  name="search" placeholder="Search By Candidate Name"  />
          &nbsp;
                    <span class="input-group-btn">
                        <button class="btn btn-primary  btn-lg" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
            </div> -->
        </form>
    </div>
    </div>

    <div class="row" id="myTable">
    

   <div class="container-fluid">
            <div class=" row">
  
  <div class="col-sm-6 mt-2" style="margin-left: 20%;">
                     <div class="errormessage"></div>
                    <div class="successmessage"></div>
               <!--FILTER STARTS FROM HERE-->
                
             
               <form class="form-horizontal" id="" method="post" action="{{url('eci/candidate-ca-list')}}">
      {{csrf_field()}}           
                 <div class="row justify-content-center">


                  <div class="col">
                <label for="state_id" class="col-form-label">State </label> &nbsp; &nbsp;
                <select name="state_id" id="state_id" class="form-control"  onchange="javascript:get_AC()" >
                  <option value="" class=>-- All States --</option>
                    @foreach($state_list as $rows)    
                    <option <?php if(isset($_POST['state_id']) && $_POST['state_id']==$rows->ST_CODE ) echo "selected" ?> value="{{$rows->ST_CODE}}">{{$rows->ST_CODE}}-{{$rows->ST_NAME}}</option>
                    @endforeach
                </select>
                @if ($errors->has('state_id'))
                        <span style="color:red;">{{ $errors->first('state_id') }}</span>
                 @endif
                        <span id="errmsg" class="text-danger"></span> 
                </div> 
                 <div class="col">
                <label for="state_id" class="col-form-label">AC </label> &nbsp; &nbsp;
                <select name="ac_id" id="ac_id" class="form-control">
                  <option value="" class=>-- All AC --</option>
                    @if($ac_list)
                      @foreach($ac_list as $rowac)    
                      <option <?php if(isset($_POST['ac_id']) && $_POST['ac_id']==$rowac->AC_NO ) echo "selected" ?> value="{{$rowac->AC_NO}}">{{$rowac->AC_NO}}-{{$rowac->AC_NAME}}</option>
                      @endforeach
                    @endif
                </select>
                @if ($errors->has('ac_id'))
                        <span style="color:red;">{{ $errors->first('ac_id') }}</span>
                 @endif
                        <span id="errmsg" class="text-danger"></span> 
                </div> 
                    
                            
                       
                       
                        <div class="col-sm-1 mt-2">
                            <p class="mt-4 text-left">
                            <!-- <button type="button" id="Back" class="btn btn-primary">Filter</button> -->
                          <input type="submit" value="Filter" id="Filter" class="btn btn-primary">
                </p>
                        </div>
                    </div>
                </form> <br>
             
                 
                    <!-- final action-->
                 <!--FILTER ENDS HERE-->

                </div>




              </div>

              </div> 

 

                <br>
  
   
<div class="col-md-12  pull-right text-right">
   <!-- <a href="{{url('/eci/log-candidate-list-pdf/'.$cons_no.'/'.$acName)}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp; -->
   <!-- <a href="{{url('/eci/log-candidate-list-excel/'.$cons_no.'/'.$acName)}}" class="btn btn-info" role="button">Excel Download</a> &nbsp;&nbsp; -->
 </div><br>
</div>
  
 


 
<!-- ==========================-->
    <br>
  <table id="exampled" class="table table-striped table-bordered" style="width:100%;">
        <thead> 
          <tr> 
            <th>Sl. No.</th> 
            <th>Candidate Name</th>
            <th>State</th>
            <th>AC Name</th>  
             <th>Action</th>  
           <!-- <th>Updated At</th>  
            <th>Updated BY</th> 
            <th>Files</th> -->
           
          </tr>
        </thead>
        <tbody>
          @if(!empty($lists))
         
            @foreach($lists as $list) 
           
            <?php $j++;  
//dd($list);
            $ac=getacbyacno($list->st_code,$list->ac_no);
                 if(isset($ac))  { $ac_name=$ac->AC_NAME; } else{ $ac_name="";}  
               
               $st=getstatebystatecode($list->st_code);   
               if(isset($st)){   $st_name=$st->ST_NAME; }else{ $st_name=""; } 
               ?>   
        
        <tr>
          <td>{{$j}}</td>
          
          <td><b>Candidate. Id- {{$list->candidate_id}}-{{$list->cand_name}}</b>  </td> 
          <td>{{$st_name}}</td>
        
         <td>{{$ac_name}}</td>
         <td><button type="button" id="{{$list->candidate_id}}" class="btn btn-primary getdata" data-toggle="modal" data-target="#changestatus" data-nomid="{{$list->nom_id}}" data-canid="{{$list->candidate_id}}" data-state="{{$list->st_code}}" data-pc="{{$list->ac_no}}" data-election="{{$list->election_id}}"> Upload File</button> </td>
       
        </tr>
 
           
            @endforeach 
            @endif 
        </tbody>
     
    </table>
    
  
</div>












</section>
 <!-- Modal Content Starts here -->
    <!-- Modal -->
<div class="modal fade" id="changestatus" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <h4 class="modal-title" id="exampleModalLabel">Upload File</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="election_form" method="POST" enctype="multipart/form-data"  action="{{url('eci/uploaddocumnetca') }}" >
                {{ csrf_field() }}   
         
    <input type="hidden" name="nom_id" id="nom_id" value="" readonly="readonly">
    <input type="hidden" name="candidate_id" id="candidate_id" value="" readonly="readonly">
    <input type="hidden" name="state" id="state" value="" readonly="readonly">
    <input type="hidden" name="acno" id="acno" value="" readonly="readonly">
    <input type="hidden" name="electionid" id="electionid" value="" readonly="readonly">
    <div class="mb-3">
      <div class="custom-control custom-radio custom-control-inline">
         <input type="file" name="criminalfile" class="form-control" accept=".pdf" required>
      
      </div>
     
     
      </div>
    
      
  
   
 
   
  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
      </div>
      
    </div>
  </div>
</div>
<!-- Modal Content Ends Here -->


@endsection
@section('script')




<script>
$('#exampled').DataTable( {
    "language": {
        "emptyTable":     "No Record Found ",
        "backgroundcolor": "green"
    }
} );
  function fetchvalue(nomid)
  {
  //alert(nomid);
    var values=nomid.split('_');
var nomidis=values[0];
var candis=values[1];


  var check=$('input:checkbox[id=first'+nomidis+']').is(':checked');
  if(check==true)
  {
   var first= $('#first'+nomidis).val();
  }else{
    var first='0';
  }
  var check=$('input:checkbox[id=second'+nomidis+']').is(':checked');
  if(check==true)
  {
   var second= $('#second'+nomidis).val();
  }else{
    var second='0';
  }
   var check=$('input:checkbox[id=third'+nomidis+']').is(':checked');
  if(check==true)
  {
   var third= $('#third'+nomidis).val();

  }else{
    var third='0';
  }
  var stcode=$('#st_code').val();
  var election_id=$('#election_id').val();
 
  var ac_no=$('#ac_no').val();
  jQuery.ajax({
                    url: "{{url('/roac/save-contestd-criminal-report')}}",
                    type: 'POST',
                    data: '_token={!! csrf_token() !!}&first='+first+'&second='+second+'&third='+third+'&stcode='+stcode
                    +'&election_id='+election_id+'&cand_nom='+nomid+'&ac_no='+ac_no,
                    
                    success: function(result){

                      if(result==1){
                     alert("Saved Successfully");
                    }
              }
            });
         

  

  



  }

  function get_AC()
        {
          var state_id = $("#state_id").val();

         
              $.ajax({
                  url: "{{ url('eci/get-ac-base-state') }}",
                  type: 'GET',
                  data: {id:state_id},            
                  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                  success:function(data){          
                
                      data = JSON.parse(data);
                      var count = Object.keys(data).length;
                      var all = '<option value="">-- All PC --</option>';
                      for (var i = 0; i < count; i++) { 
                          if(data[i].id!=10)
                          {
                              all += '<option value="'+ data[i].id +'">'+data[i].id+' - '+ data[i].name +'</option>'; 
                          }
                          }
                      $("#ac_id").html(all);
                  }
              });
        }







  $(document).on("click", ".getdata", function () {
       nomid = $(this).attr('data-nomid');
       canid = $(this).attr('data-canid'); 
       electionid = $(this).attr('data-election'); 
       state = $(this).attr('data-state'); 
       acno = $(this).attr('data-pc'); 
       
       
       
      
       $("#nom_id").val(nomid);
       $("#candidate_id").val(canid);
       $("#electionid").val(electionid);
       $("#state").val(state);
       $("#acno").val(acno);
       $("#rejection_message").val(message);
      
   });













</script>





    
    

 
@endsection