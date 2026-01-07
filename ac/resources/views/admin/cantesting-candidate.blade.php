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
       @if(isset($cands) && count($cands)>0)
             <tr colspan="5">
      <td colspan="5" align="left">(i) Candidates of Recognised National and State Political Parties</td>
    </tr>
    @endif

           
            @foreach ($cands as $key => $item)
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
                    <td>{{ $item->cand_name }}  <br></br>
                    
                      @if($item->cand_image!='')
                       <img src="{{public_path($item->cand_image)}}" style="width:100px" class="prfl-pic img-thumbnail" alt="">
                      @endif </td>
                    <td>{{ $item->candidate_residence_address }}</td>
                    <td>{{ $item->PARTYNAME }}</td>
                    <td>{{ $item->SYMBOL_DES }}</td>
                  </tr>
             @endforeach
            
@if(isset($candu) && count($candu)>0)
             <tr colspan="5">
      <td colspan="5" align="left">(ii) Candidates of registered political parties (other than recognised National and State Political Parties) </td>
    </tr>
    @endif
             @foreach ($candu as $key => $item)
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
                    <td>{{ $item->cand_name }}  <br></br>
                    
                      @if($item->cand_image!='')
                       <img src="{{public_path($item->cand_image)}}" style="width:100px" class="prfl-pic img-thumbnail" alt="">
                      @endif </td>
                    <td>{{ $item->candidate_residence_address }}</td>
                    <td>{{ $item->PARTYNAME }}</td>
                    <td>{{ $item->SYMBOL_DES }}</td>
                  </tr>
             @endforeach 
             @if(isset($candz) && count($candz) > 0)
             <tr colspan="5">
      <td colspan="5" align="left">(iii) Other Candidates </td>
    </tr>
    @endif
             @foreach ($candz as $key => $item)
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
                    <td>{{ $item->cand_name }}  <br></br>
                    
                      @if($item->cand_image!='')
                       <img src="{{public_path($item->cand_image)}}" style="width:100px" class="prfl-pic img-thumbnail" alt="">
                      @endif </td>
                    <td>{{ $item->candidate_residence_address }}</td>
                    <td>{{ $item->PARTYNAME }}</td>
                    <td>{{ $item->SYMBOL_DES }}</td>
                  </tr>
             @endforeach  

          </tbody>
  </table>
    
        
</div>