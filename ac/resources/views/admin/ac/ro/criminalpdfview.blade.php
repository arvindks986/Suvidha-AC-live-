 <style type="text/css">
  th, td{padding:5px; font-family:arial;text-align:center;}
   
 </style>
<div class="container">
    <div align="center"> 
      <h2 align="center">List of Contesting Candidates CA Report</h2>
      
     
    </div>  
    <?php  $role_id=Auth::user()->role_id; 
    $st=getstatebystatecode(Auth::user()->st_code); 
               $ac=getacname(Auth::user()->st_code,Auth::user()->ac_no);
                if(isset($ac))  $ac_name=$ac->AC_NAME;  
               if(isset($st))   $st_name=$st->ST_NAME; 
     ?>
    <table style="width:100%; border: 1px solid #000;" border="0" align="center">  
                <tr>
                 <td style="width:50%;">
                    <table  style="width:100%">
                      <tbody>
                         <tr>
                           <td><strong>State:</strong>{{$st_name }}</td>
                         </tr>
                         @if( $role_id==19)
                         <tr>  
                           <td><strong>AC Name:</strong> {{$ac_name}}</td>
                         </tr>
                         @endif
                      </tbody>
                    </table>  
                 </td>
                 <td  style="width:50%">
                  <table style="width:100%">
                      <tbody>
                         <tr>
                           <td align="right"><strong>Date of Print:</strong> {{ date('d-M-Y h:i a') }}</td>
                         </tr>
                         <tr>  
                           <td align="right">&nbsp;</td>
                         </tr> 
                      </tbody>
                    </table>
                 </td>
               </tr>
              
            </table>
<?php  $role_id=Auth::user()->role_id; ?>
  <table class="table" border="1" cellpadding="0" cellspacing="0" width="100%" align="center" style="font-size:14px;  ">
    <thead><tr style="background-color: #fff3cd;">
      <th>Sl. No</th>
      <th>Name Of Candidate </th>
       <th>Nomination ID </th>
         @if ( $role_id==4)
           <th>AC Name</th>
           @endif
       
               <th>1st Publication</th>
                <th>1st Publication Date</th>
              <th>2nd Publication</th>
               <th>2nd Publication Date</th>
              <th>3rd Publication</th>
               <th>3rd Publication Date</th>
      <th>Publication Status</th>
    </tr></thead>
    <tr>
    
    
    </tr>
     <tbody>  
   
       <?php $k=1;?>
        @if(!empty($lists))
    @foreach ($lists as $key => $item)
            <?php 

            
               //  $st=getstatebystatecode($item->st_code);   
               // // $dist=getdistrictbydistrictno($item->candidate_residence_stcode,$item->candidate_residence_districtno); 
                
                $ac=getacname($item->st_code,$item->ac_no);
                if(isset($ac))  $ac_name=$ac->AC_NAME;  
               //  if(isset($st))   $st_name=$st->ST_NAME; 
               // if(isset($dist))   $dist_name=$dist->DIST_NAME;  
                if( $item->check_1==1){$check1="&#10003;";}else{$check1="&#x2717"; }
                if( $item->check_2==1){$check2="&#10003;";}else{$check2="&#x2717"; }
                if( $item->check_3==1){$check3="&#10003;";}else{$check3="&#x2717"; }

                 if(!empty($item->check_1_date)){$check1_date=date('d-m-Y',strtotime($item->check_1_date));}else{$check1_date="N/A"; }
                if(!empty($item->check_2_date)){$check2_date=date('d-m-Y',strtotime($item->check_2_date));}else{$check2_date="N/A"; }
                if(!empty($item->check_3_date) ){$check3_date=date('d-m-Y',strtotime($item->check_3_date));}else{$check3_date="N/A"; }

                 if($item->check_1 == 1 && $item->check_2 == 1 &&  $item->check_3 == 1)
                  {
                    $status_is="Completed";
                  }else{
                    $status_is="Pending";
                  }
                
             //  dd(strtotime($item->check_3_date));
         ?>
        <tr>
          <td>{{$k++}}</td>
           <td>{{ $item->cand_name}}</td>
           <td>{{ $item->nom_id}}</td>
           @if ( $role_id==4)
           <td>{{ $ac_name}} </td>
           @endif
           <td class="text-center"><?php echo $check1; ?> </td>
           <td class="text-center"><?php echo $check1_date; ?> </td>
            <td class="text-center"><?php echo $check2; ?> </td>
            <td class="text-center"><?php echo $check2_date; ?> </td>
             <td class="text-center"><?php echo $check3; ?> </td>
             <td class="text-center"><?php echo $check3_date; ?> </td>
          <!--  <td>{{ $item->check_2}}</td>
           <td>{{ $item->check_3}}</td> -->
            <td>{{$status_is}}</td>
        </tr>

    @endforeach
    @endif
  <tbody>
  </table>
    
        
</div>