 <style type="text/css">
  th, td{padding:5px; font-family:arial;}
 </style>
<div class="container">
    <div align="center"> 
      <h2 align="center">List of Contesting Candidates</h2>
      <p align="center">Election of  {{strtoupper($state->ST_NAME) }} Legislative Assembly from {{strtoupper($ac->AC_NAME) }}  Assembly Constituency.</p>
    </div>  
  <table class="table" border="1" cellpadding="0" cellspacing="0" width="100%" align="center" style="font-size:14px; ">
    <thead><tr>
      <td>Serial</td>
      <td>Name Of Candidate </td>
      <td>Address of Candidate </td>
      <td> Party Affiliation</td>
      <td>Symbol Allotted</td>
    </tr></thead>
    <tr>
    
      <td align="center"> 1 </td>
      <td align="center"> 2 </td>
      <td align="center"> 3 </td>
      <td align="center"> 4 </td>
      <td align="center"> 5 </td>
    </tr>
     <tbody>  
   
       
    @foreach ($candn as $key => $item)
            <?php 
               $st=getstatebystatecode($item->candidate_residence_stcode);   
               $dist=getdistrictbydistrictno($item->candidate_residence_stcode,$item->candidate_residence_districtno); 
                
               $ac=getacname($item->candidate_residence_stcode,$item->candidate_residence_acno);
               if(isset($ac))  $ac_name=$ac->AC_NAME;  
               if(isset($st))   $st_name=$st->ST_NAME; 
               if(isset($dist))   $dist_name=$dist->DIST_NAME;  
                
         ?>
        <tr>
          <td>{{ $item->new_srno }}</td>
          <td>{{ $item->cand_name }}</td>
           <td>{{ $item->candidate_residence_address }} {{$ac_name}}  {{ $dist_name}} {{ $st_name }} </td>
          <td>{{ $item->PARTYNAME }}</td>
          <td>{{ $item->SYMBOL_DES }}</td>
        </tr>

    @endforeach
    
  <tbody>
  </table>
    
        
</div>