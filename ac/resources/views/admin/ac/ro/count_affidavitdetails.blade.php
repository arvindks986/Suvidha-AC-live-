@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Counter Affidavit details')
@section('content')
<?php $st = getstatebystatecode($ele_details->ST_CODE);
$pc = getacbyacno($ele_details->ST_CODE, $ele_details->CONST_NO);
$url = URL::to("/");
$j = 0;
$getlastwithdrawl_stateACwise = getlastwithdrawl_stateACwise($ele_details->ST_CODE,$ele_details->CONST_NO);
   
   
    $closeupdate_btn= date('Y-m-d', strtotime($getlastwithdrawl_stateACwise->LDT_WD_CAN));
?>
<style type="text/css">
  th,
  td {
    white-space: nowrap;
  }

  < !-- .dataTables_wrapper .row:nth-child(2) .col-sm-12 {
    overflow: scroll;
  }

  -->html {
    overflow: scroll;
    overflow-x: hidden;
  }

  ::-webkit-scrollbar {
    width: 0px;
    background: transparent;
    /* optional: just make scrollbar invisible */
  }

  ::-webkit-scrollbar-thumb {
    background: #ff9800;
  }

  div.dataTables_wrapper {
    margin: 0 auto;
  }
</style>
<main role="main" class="inner cover mb-3">

  <div class="container-fluid">
    <div class="row">
      <div class="card text-left" style="width:100%; margin:0 auto;">
        <div class=" card-header">
          <div class="row">
            <div class="col">
              <h4>Candidate Affidavit Details</h4>
            </div>
            <div class="col">
              <p class="mb-0 text-right"><b>State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b>AC Name:</b>
                <span class="badge badge-info">{{$pc->AC_NAME}}</span>&nbsp;&nbsp;
              </p>
            </div>
            <button type="button" class="btn btn-primary"><a href="{{url('/roac/candidateaffidavit')}}"><spna style="color: white">Back</spna></a></button>
           
          

          </div>
        </div>
        <div class="row">
          <div class="col">
            @if (\Session::has('success'))
            <div class="alert alert-success">
              <ul>
                <li>{!! \Session::get('success') !!}</li>
              </ul>
            </div>
            @endif


          </div>
        </div>

        <div class="card-body">

          <table class="table table-striped table-bordered" style="width:100%">
            <thead>
              <tr>
                <th>Sl. No.</th>
                <th>Candidate Name</th>
                <th> Date (Time)</th>
                <th>Affidavit Details</th>
                 <?php if(date("Y-m-d") <= $closeupdate_btn) { ?>
                <th>Delete Affidavit</th>
              <?php } ?>
              </tr>
            </thead>
            <tbody>@if(isset($list))
              @foreach($list as $lis)
           
              <?php $j++;
              $nom = getById('candidate_nomination_detail', 'nom_id', $lis->nom_id);
              $cand = getById('candidate_personal_detail', 'candidate_id', $nom->candidate_id);
              $affidavit = getById('candidate_affidavit_detail', 'candidate_affidavit_id', $lis->candidate_affidavit_id);
              $party = getpartybyid($nom->party_id);
              $explodeis=explode(" ",$affidavit->created_at);
             // dd($explodeis);
              $dateis=date('d-m-Y',strtotime($explodeis[0]));

              ?>
              <tr>
                <td>{{$j}}</td>
                <td>Nom Id:-{{$lis->nom_id}}-{{$cand->cand_name}} <br> S/O or W/O:-{{$cand->candidate_father_name}}</td>
                <td>{{$dateis}} ({{$explodeis[1]}})</td>

                <td>@if(!empty($affidavit->affidavit_name)) <a href="{{asset($affidavit->affidavit_path)}}" download> <b>FORM 26</b></a>@else No Affidavit @endif</td>
             
<?php
                     if(date("Y-m-d") <= $closeupdate_btn) { ?>
                    <td><button type="button"  class="btn btn-primary getdata" data-toggle="modal" data-target="#changestatus" data-nomid="{{$lis->nom_id}}" data-canid="{{$lis->candidate_affidavit_id}}" >Delete</button> </td>
                  <?php   } ?>
                   </tr>

              @endforeach
              @endif
            </tbody>

          </table>


        </div>
      </div>


    </div>
  </div>
  </section>


<div class="modal fade" id="changestatus" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <small class="modal-title" id="exampleModalLabel">Remove Affidavit.</small>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="election_form" method="POST"  action="{{url('roac/deleteaffidavit') }}" >
                {{ csrf_field() }}   
         
    <input type="hidden" name="nom_id" id="nom_id" value="" readonly="readonly">
     <input type="hidden" name="affidavit_id" id="affidavit_id" value="" readonly="readonly">
    <div class="mb-3">
      
     <p style="font-size:14px;" class="">Are you sure. You want to Delete Affidavit<sup>*</sup>
     <br /> </p>
      
    <br />
   
     </div>

      
   
  <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Delete</button>
      </div>
    </form>
      </div>
      
    </div>
  </div>
</div>



</main>

@endsection
@section('script')

<script type="text/javascript">
  $(document).ready(function() {
    //called when key is pressed in textbox

    $("#election_form").submit(function() {

      if ($("#candidate_id").val() == '') {
        $("#errmsg").text("");
        $("#errmsg").text("Please select Candidate");
        $("#candidate_id").focus();
        return false;
      }
      if ($("#counteraffidavit").val() == '') {
        $("#errmsg").text("");
        $("#errmsg1").text("Please select pdf file");
        $("#counteraffidavit").focus();
        return false;
      }



    });
  });

  $(document).on("click", ".getdata", function () {
       nomid = $(this).attr('data-nomid');
       canid = $(this).attr('data-canid'); 
       $("#nom_id").val(nomid);
       $("#affidavit_id").val(canid);
        
   });
</script>
@endsection