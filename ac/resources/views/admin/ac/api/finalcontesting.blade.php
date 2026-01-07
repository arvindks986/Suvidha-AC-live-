<style type="text/css">
  th, td{padding:5px; font-family:arial; vertical-align:middle;}
 </style>
<div class="container">
    <div align="center"> <h2 align="center">LIST OF CONTESTING CANDIDATES</h2>
    </div>  
  <table class="table" border="1" cellpadding="0" cellspacing="0" width="100%" align="center" style="font-size:14px; ">
    <thead><tr>
      <th>Serial No.</th>
      <th>Name Of Candidate </th>
      <th>Address of Candidate </th>
      <th>Party Affiliation </th>
      <th>Symbol Allotted </th>
    </tr></thead>
    <tr>
      <td align="center"> 1 </td>
      <td align="center"> 2 </td>
      <td align="center"> 3 </td>
      <td align="center"> 4 </td>
      <td align="center"> 5 </td>
    </tr>
    
       
    @foreach ($candlist as $cand)
        <tr>
		  <td>{{ $cand["cand_sn"] }}<br></td>
          <td>{{ $cand["cand_name"] }}<br><span><img src="{{ url($cand["cand_img"]) }}" width="120px"></span></td>
          <td>{{ $cand["candidate_residence_address"] }}</td>
          <td>{{ $cand["party_name"] }}</td>
          <td>{{ $cand["symbol_name"] }}</td>
        </tr>

    @endforeach
    
    <tbody>
  </table>
    
        
</div>