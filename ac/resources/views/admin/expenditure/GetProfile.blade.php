<style type="text/css">
    .row.mis_gap {
        padding: 10px;
        border: 1px solid #6666;
    }
</style>
<?php 
  
// 
//$pc_no=!empty($profileData[0]) ? $profileData[0]->ac_no:'';
$st_code=!empty($profileData[0]->st_code) ? $profileData[0]->st_code:0;
$party_id=!empty($profileData[0]) ? $profileData[0]->party_id:0;
// 
// $candiatePcName = getpcbypcno($st_code, $pc_no);       
// $candiatePcName =  !empty($candiatePcName)? $candiatePcName->PC_NAME:'N/A';
 $stateName= getstatebystatecode($st_code); 
 $stateName =  !empty($stateName->ST_NAME)? $stateName->ST_NAME:'N/A';  
 $partyname = getpartybyid($party_id);
 $partyname =  !empty($partyname->PARTYNAME)? $partyname->PARTYNAME:'N/A';  
 
  
  
?>
<div class=" text-left" style="width:100%;">
    <!--SELECT CANDIDATE-->
 
    <div  class="collapse show">  
        <table   class="table table-striped table-bordered table-hover" style="width:100%">
            <tbody>
                @if(count($profileData)>0)
                <tr>
                    <td>Name</td>
                    <td>{{!empty($profileData[0]) ? $profileData[0]->cand_name:'N/A'}}</td>
                </tr>
                <tr>
                    <td>Phone</td>
                    <td>{{!empty($profileData[0]) ? $profileData[0]->cand_mobile:'N/A'}}</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>{{!empty($profileData[0]) ? $profileData[0]->cand_email:'N/A'}} </td>
                </tr>
                <tr>
                    <td>AC Name</td>
                    <td>{{!empty($Acdetail->AC_NAME)? $Acdetail->AC_NAME:'N/A' }}</td>
                </tr>
                <tr>
                    <td>Party Name</td>
                    <td>{{$partyname}}</td>
                </tr>
                <tr>
                    <td>Residence Address</td>
                    <td>{{!empty($profileData[0]) ? $profileData[0]->candidate_residence_address:'N/A'}}</td>
                </tr>
                <tr>
                    <td>Election Type</td>
                     <td>AC</td>
                    <!-- <td>{{!empty($profileData[0]) ? $profileData[0]->ELECTION_TYPE:'--'}}</td> -->
                </tr>                
                <tr>
                    <td>State</td>
                    <td>{{$stateName}}</td>
                </tr>
                
                @else
                <tr>
                    <td colspan="2">No Record Available</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>


</div>
