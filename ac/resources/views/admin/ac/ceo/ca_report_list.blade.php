@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomination Details')
@section('bradcome', 'Candidate CA Report')
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
$cons_no= !empty($_GET['ac']) ? $_GET['ac'] : 0;
//dd($cons_no);
$acdetails = getacbyacno($user_data->st_code, $cons_no);
 
$acName = !empty($acdetails->AC_NAME) ? $acdetails->AC_NAME : 'ALL';
 
$all_ac="";

$receivefilter_value=!empty($_GET['receivefilter']) ? $_GET['receivefilter'] : 0;
 ?>
 
<section class="data_table mt-3 form">
  <div class="container-fluid">
         @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
        @endif
<?php $i=1; $url = URL::to("/"); $j=0; ?>
   
  <div class="row d-flex align-items-center">
<div class="col">  <h5>Publication Criminal Antecedent</h5></div>
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
  
  <div class="col-sm-12 mt-3">
                     <div class="errormessage"></div>
                    <div class="successmessage"></div>
               <!--FILTER STARTS FROM HERE-->
                
              <form method="get" action="{{url('/acceo/contested-criminal-report')}}" id="EcidashboardFilter">           
                 <div class="row justify-content-center">
                    
                            <div class="col-sm-3">
                  <label for="" class="mr-3">Select AC</label>    
                  <select name="ac" id="ac" class="consttype form-control" >
                    <option value="">-- All AC --</option>
                    <?php $all_ac = getacbystate($user_data->st_code);
                        // dd($user_data->s);
                    
                    foreach($all_ac as $getAc){
                    if ($cons_no == $getAc->AC_NO) { ?>
                      <option value="{{ $getAc->AC_NO }}" selected>{{$getAc->AC_NAME}} - {{$getAc->AC_NAME_HI}}</option>
                     
                    <?php    }else{ ?>
                         
                      <option value="{{ $getAc->AC_NO }}" <?php if(!empty($_GET['ac']) && $getAc->AC_NO==$_GET['ac']){ echo "selected";} ?>>{{$getAc->AC_NAME}} - {{$getAc->AC_NAME_HI}}</option>
                    <?php } } ?>
                    </select>
                        @if ($errors->has('ac'))
                          <span style="color:red;">{{ $errors->first('ac') }}</span>
                        @endif
                     
                            <div class="acerrormsg errormsg errorred"></div>
                        </div>
                        <div class="col-sm-3">                                
                                 <label for="" class="mr-3">Publication Status</label>    
                                <select name="receivefilter"   class=" form-control" >
                                    <option value="" @if($receivefilter_value=='') selected @endif >-- All --</option>    
                                    <option value="1" @if($receivefilter_value=='1') selected @endif >Completed</option>                                    
                                    <option value="2" @if($receivefilter_value=='2') selected @endif>Pending</option>                                
                                                                       
                                </select> 
                            </div>
                       
                        <div class="col-sm-1 mt-2">
                            <p class="mt-4 text-left">
                            <!-- <button type="button" id="Back" class="btn btn-primary">Filter</button> -->
                          <input type="submit" value="Filter" id="Filter" class="btn btn-primary">
                </p>
                        </div>
                    </div>
                </form> 
             
                 
                    <!-- final action-->
                 <!--FILTER ENDS HERE-->
                </div></div></div> <br>
    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4 mb-3 allnom d-flex">
    
    </div>
    
  
  <?php //if (!empty($lists->CA_Report_candid) || $lists->CA_Report_candid != ''){  //  } ?>
  </div>
  <div class="col-md-5  pull-right text-right">
   <a href="{{url('/acceo/contesting-candidate-list-pdf/'.$cons_no.'/'.$receivefilter_value)}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
   <a href="{{url('/acceo/contesting_candidate_list_excel/'.$cons_no.'/'.$receivefilter_value)}}" class="btn btn-info" role="button">Excel Download</a> &nbsp;&nbsp;
 </div>
    <!-- <div class="alert alert-success" style="display:block";>
    <strong>Success!</strong> Saved Successfully 
  </div> -->
  <?php //print_R($nomid); ?>
 <br></br>
<!-- ==========================-->
     <table id="exampled" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
              <th class="text-center">SL.No</th>
                <th class="text-center">Candidate Name </th>
                <th class="text-center">Nomination ID</th>
            
               <th class="text-center">AC Name </th>
              <th class="text-center" colspan="2">1st Publication</th>
              <th class="text-center" colspan="2">2nd Publication</th>
              <th class="text-center" colspan="2">3rd Publication</th>
              <th class="text-center">Publication Status</th>
              <!-- <th>Action</th> -->
            
            </tr>
             <tr>  
           <th></th>
           <th></th>
            <th></th>
             <th></th>
              <th>Confirmed</th>
              <th>Date</th> 
              <th>Confirmed</th>
              <th>Date</th>
              <th>Confirmed</th> 
              <th>Date</th> 
              <th></th>
              
            </tr>
        </thead>
       
          <tbody>
         
        @if(!empty($list))
            @foreach($list as $lists) 
          
           
               <?php $j++; 
                  if($lists->check_1 == 1 && $lists->check_2 == 1 &&  $lists->check_3 == 1)
                  {
                    $status_is="Completed";
                  }else{
                    $status_is="Pending";
                  }
              
                if( $lists->check_1==1){$check1="&#10003;";}else{$check1="&#x2717"; }
                if( $lists->check_2==1){$check2="&#10003;";}else{$check2="&#x2717"; }
                if( $lists->check_3==1){$check3="&#10003;";}else{$check3="&#x2717"; }

                  if(!empty($lists->check_1_date)){$check1_date=date('d-m-Y',strtotime($lists->check_1_date));}else{$check1_date="N/A"; }
                if(!empty($lists->check_2_date)){$check2_date=date('d-m-Y',strtotime($lists->check_2_date));}else{$check2_date="N/A"; }
                if(!empty($lists->check_3_date) ){$check3_date=date('d-m-Y',strtotime($lists->check_3_date));}else{$check3_date="N/A"; }

                 $ac=getacname($lists->st_code,$lists->ac_no);
                if(isset($ac))  $ac_name=$ac->AC_NAME;  
               
             ?> 
              <div> <input type="hidden" id="cand_id" value={{$lists->candidate_id}}></div>
             <div> <input type="hidden" id="st_code" value={{$lists->st_code}}></div>
             <div> <input type="hidden" id="ac_no" value={{$lists->ac_no}}></div>
             <div> <input type="hidden" id="election_id" value={{$lists->election_id}}></div>
             <div> <input type="hidden" id="nom_id" value={{$lists->nom_id}}></div>      
        
        <tr>
          <td >{{$j}}</td>
          <td>{{$lists->cand_name}}</td> 
          <td class="text-center"><b>{{$lists->nom_id}}</td> 
          
            <td class="text-center">{{$ac_name}}</td>  
          <td class="text-center"><?php echo $check1; ?></td>&nbsp;&nbsp;<td> <?php echo $check1_date; ?></td>
          <td class="text-center"><?php echo $check2; ?> </td>&nbsp;&nbsp;<td><?php echo $check2_date; ?></td>
           <td class="text-center"><?php echo $check3; ?></td>&nbsp;&nbsp;<td><?php echo $check3_date; ?></td>
         

         <td class="text-center">{{$status_is}}</td>

        
        </tr>
 

           
            @endforeach 
            @endif  
        </tbody>
        
      </table>
    
  
</div>
</section>
  <!-- Modal Content Starts here -->

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


</script>





    
    

 
@endsection