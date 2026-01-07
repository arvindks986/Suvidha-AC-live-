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

body{
  background-color: #e6e6e6;
  width: 100%;
  height: 100%;
}
 #success_tic .page-body{
  max-width:300px;
  background-color:#FFFFFF;
  margin:10% auto;
}
 #success_tic .page-body .head{
  text-align:center;
}
/* #success_tic .tic{
  font-size:186px;
} */
#success_tic .close{
      opacity: 1;
    position: absolute;
    right: 0px;
    font-size: 30px;
    padding: 3px 15px;
  margin-bottom: 10px;
}
#success_tic .checkmark-circle {
  width: 150px;
  height: 150px;
  position: relative;
  display: inline-block;
  vertical-align: top;
}
.checkmark-circle .background {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  background: #1ab394;
  position: absolute;
}
#success_tic .checkmark-circle .checkmark {
  border-radius: 5px;
}
#success_tic .checkmark-circle .checkmark.draw:after {
  -webkit-animation-delay: 300ms;
  -moz-animation-delay: 300ms;
  animation-delay: 300ms;
  -webkit-animation-duration: 1s;
  -moz-animation-duration: 1s;
  animation-duration: 1s;
  -webkit-animation-timing-function: ease;
  -moz-animation-timing-function: ease;
  animation-timing-function: ease;
  -webkit-animation-name: checkmark;
  -moz-animation-name: checkmark;
  animation-name: checkmark;
  -webkit-transform: scaleX(-1) rotate(135deg);
  -moz-transform: scaleX(-1) rotate(135deg);
  -ms-transform: scaleX(-1) rotate(135deg);
  -o-transform: scaleX(-1) rotate(135deg);
  transform: scaleX(-1) rotate(135deg);
  -webkit-animation-fill-mode: forwards;
  -moz-animation-fill-mode: forwards;
  animation-fill-mode: forwards;
}
#success_tic .checkmark-circle .checkmark:after {
  opacity: 1;
  height: 75px;
  width: 37.5px;
  -webkit-transform-origin: left top;
  -moz-transform-origin: left top;
  -ms-transform-origin: left top;
  -o-transform-origin: left top;
  transform-origin: left top;
  border-right: 15px solid #fff;
  border-top: 15px solid #fff;
  border-radius: 2.5px !important;
  content: '';
  left: 35px;
  top: 80px;
  position: absolute;
}

@-webkit-keyframes checkmark {
  0% {
    height: 0;
    width: 0;
    opacity: 1;
  }
  20% {
    height: 0;
    width: 37.5px;
    opacity: 1;
  }
  40% {
    height: 75px;
    width: 37.5px;
    opacity: 1;
  }
  100% {
    height: 75px;
    width: 37.5px;
    opacity: 1;
  }
}
@-moz-keyframes checkmark {
  0% {
    height: 0;
    width: 0;
    opacity: 1;
  }
  20% {
    height: 0;
    width: 37.5px;
    opacity: 1;
  }
  40% {
    height: 75px;
    width: 37.5px;
    opacity: 1;
  }
  100% {
    height: 75px;
    width: 37.5px;
    opacity: 1;
  }
}
@keyframes checkmark {
  0% {
    height: 0;
    width: 0;
    opacity: 1;
  }
  20% {
    height: 0;
    width: 37.5px;
    opacity: 1;
  }
  40% {
    height: 75px;
    width: 37.5px;
    opacity: 1;
  }
  100% {
    height: 75px;
    width: 37.5px;
    opacity: 1;
  }
}


</style>
 
<section class="data_table mt-3 form">
  <div class="container-fluid">
         @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
        @endif
<?php $i=1; $url = URL::to("/"); $j=0; ?>
   
  <div class="row d-flex align-items-center">
<div class="col"> <h5>Publication Criminal Antecedent</h5></div>
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

   
  
    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4 mb-3 allnom d-flex">
    
    </div>
  
  <?php //if (!empty($lists->CA_Report_candid) || $lists->CA_Report_candid != ''){  //  } ?>
  </div>
   <?php $date_wdl=$Schedule_details->LDT_WD_CAN;
        $date_polldate=$Schedule_details->DATE_POLL;
        $wt_date_plusone=date('Y-m-d', strtotime("+1 day", strtotime($date_wdl)));
        $polldate_minustwo=date('Y-m-d', strtotime("-2 day", strtotime($date_polldate)));
        // print_R($wt_date_plusone); echo "--------"; print_r($polldate_minustwo);
         $current_date = date('Y-m-d');
       

       
      
        //if($wt_date_plusone<=$polldate_minustwo){
   ?>
  
         
  <div class="col-md-5  pull-right text-right">
   <a href="{{url('/roac/contesting-candidate-list-pdf')}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
   <a href="{{url('/roac/contesting_candidate_list_excel')}}" class="btn btn-info" role="button">Excel Download</a> &nbsp;&nbsp;
  
  </div> 
 <br></br>
 
<!-- ==========================-->
     <table id="exampled" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
              <th class="text-center" >SL.No</th>
              <th class="text-center">Candidate Name </th>
               <th class="text-center" colspan="2">1st Publication</th>
              <th class="text-center" colspan="2">2nd Publication</th>
              <th class="text-center" colspan="2">3rd Publication</th>
              <th class="text-center">Publication Status</th>
              <th class="text-center">Action</th>
            
            </tr>
            <tr>  
           <th></th>
           <th></th>
              <th>Confirm</th>
              <th>Date</th> 
              <th>Confirm</th>
              <th>Date</th>
              <th>Confirm</th> 
              <th>Date</th> 
              <th></th>
              <th></th>
            </tr>
        </thead>
       
          <tbody>
         
        @if(!empty($list))
            @foreach($list as $lists) 
          
            <?php $j++; 
           
               if($lists->check_1 == 1 && $lists->check_2 == 1 && $lists->check_3 == 1)
                  {
                    $status_is="Completed";
                  }else{
                    $status_is="Pending";
                  }
              
             ?> 
             <div> <input type="hidden" id="cand_id" value={{ $lists->candidate_id }}></div>
             <div> <input type="hidden" id="st_code" value={{ $lists->st_code}}></div>
             <div> <input type="hidden" id="ac_no" value={{ $lists->ac_no}}></div>
             <div> <input type="hidden" id="election_id" value={{ $lists->election_id}}></div>
             <div> <input type="hidden" id="nom_id" value={{$lists->nom_id}}></div>     


        
        <tr>
          <td>{{$j}}</td>
         
          <td >Nom. Id- {{$lists->nom_id}}-{{$lists->cand_name}}</td>

          <td class="text-center"><input   name="admin{{$lists->nom_id}}" type="checkbox"id="first{{$lists->nom_id}}" value="1" {{  ($lists->check_1 == 1 ? ' checked' : '') }}> </td>
          <td> <input type="date" id="first_date{{$lists->nom_id}}" required name="admin_first{{$lists->nom_id}}" value="{{!empty($lists->check_1_date)?$lists->check_1_date:''}}"   style="height: 40px !important;"></td>

          <td class="text-center"><input  name="admin{{$lists->nom_id}}" type="checkbox" id="second{{$lists->nom_id}}" value="1" {{  ($lists->check_2 == 1 ? ' checked' : '') }}></td>

          <td> <input type="date" id="second_date{{$lists->nom_id}}" value="{{!empty($lists->check_2_date)?$lists->check_2_date:''}}"  required  name="admin_second{{$lists->nom_id}}" style="height: 40px !important;"></td>
          <td class="text-center"><input  name="admin{{$lists->nom_id}}" type="checkbox" id="third{{$lists->nom_id}}"value="1" {{  ($lists->check_3 == 1 ? ' checked' : '') }}></td>
          <td> <input type="date" id="third_date{{$lists->nom_id}}" value="{{!empty($lists->check_3_date)?$lists->check_3_date:''}}"  required name="admin_third{{$lists->nom_id}}" style="height: 40px !important;"></td>



            <td class="text-center">{{$status_is}}</td>

          <td ><button type="submit" class="btn btn-success" value="{{$lists->nom_id}}_{{$lists->candidate_id}}" onclick="return fetchvalue(this.value);" id="sav_{{$lists->nom_id}}"> Save
        </button></td>

        
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
        "emptyTable":     "No Record Found for Final Contesting Candidate CA Report ",
        "backgroundcolor": "green"
    },
    "ordering": false
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
   var first_date= $('#first_date'+nomidis).val();
   if(first_date.length==0){
   $('#first_date'+nomidis).css('border', "2px solid red");
    $('#first_date'+nomidis).focus();exit;
    }else{
        $('#first_date'+nomidis).css('border', "green");
        $('#first_date'+nomidis).focus();

    }

  }else{
    var first='0';
    var first_date="";
  }
  var check=$('input:checkbox[id=second'+nomidis+']').is(':checked');
  if(check==true)
  {
   var second= $('#second'+nomidis).val();
   var second_date= $('#second_date'+nomidis).val();
   if(second_date.length==0){
   $('#second_date'+nomidis).css('border', "2px solid red");
    $('#second_date'+nomidis).focus();exit;
    }else{
        $('#second_date'+nomidis).css('border', "green");
        $('#second_date'+nomidis).focus();

    }

  }else{
    var second='0';
    var second_date="";
  }
   var check=$('input:checkbox[id=third'+nomidis+']').is(':checked');
  if(check==true)
  {
   var third= $('#third'+nomidis).val();
   var third_date= $('#third_date'+nomidis).val();
   if(third_date.length==0){
   $('#third_date'+nomidis).css('border', "2px solid red");
    $('#third_date'+nomidis).focus();exit;
    }else{
        $('#third_date'+nomidis).css('border', "green");
        $('#third_date'+nomidis).focus();

    }

  }else{
    var third='0';
    var third_date="";
  }
  var stcode=$('#st_code').val();
  var election_id=$('#election_id').val();
 
  var ac_no=$('#ac_no').val();
  jQuery.ajax({
                    url: "{{url('/roac/save-contestd-criminal-report')}}",
                    type: 'POST',
                    data: '_token={!! csrf_token() !!}&first='+first+'&second='+second+'&third='+third+'&stcode='+stcode
                    +'&election_id='+election_id+'&cand_nom='+nomid+'&ac_no='+ac_no+'&first_date='+first_date+'&second_date='+second_date+'&third_date='+third_date,
                    
                    success: function(result){

                      if(result==1){
                        $("#success_tic").modal('show');
                        setTimeout(function() {$('#success_tic').modal('hide');}, 2000);
                        setTimeout(function(){
                        window.location.reload();
                         }, 3000);
                        //$("#success_tic").show();
                     //alert("Saved Successfully");
                    }
              }
            });
         

  

  



  }


</script>

<!-- <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#success_tic">Open Modal</button> -->

<!-- Modal -->
<div id="success_tic" class="modal fade" role="dialog" >
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <a class="close" href="#" data-dismiss="modal">&times;</a>
      <div class="page-body">
    <div class="head">  
     
      <h4>Saved Successfully</h4>
    </div>

  <h1 style="text-align:center;"><div class="checkmark-circle">
  <div class="background"></div>
  <div class="checkmark draw"></div>
</div><h1>

  </div>
</div>
    </div>

  </div>




    
    

 
@endsection