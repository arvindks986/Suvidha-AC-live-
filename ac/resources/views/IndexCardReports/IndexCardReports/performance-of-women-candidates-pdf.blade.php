<html>
  <head>
    <style>

@page { sheet-size: A4-L; }
        @page bigger { sheet-size: 420mm 370mm; }
        @page toc { sheet-size: A4; }
		
  @page {
            header: page-header;
            footer: page-footer;
        }

    td {
    font-size: 12px !important;
    font-weight: 500 !important;
    text-align: center;
    padding: 2px;
    color: #000;
	width:10.4%;
    font-family: "Times New Roman", Times, serif;
    }
    h3{
    font-size: 18px !important;
    font-weight: 600;
    }
   
    .table-bordered{
    border:1px solid #000;
    }
    .table-bordered td,
    .table-bordered th {
    border: 1px solid #000 !important
    }
    .table {
    width: 100%;
    border-collapse: collapse;
    font-size: .9em;
    color: #000;
    margin-bottom: 1rem;
    color: #212529;
    }
    .blc{
    border-collapse: collapse;
    border-bottom: 1px solid #000;
    border-spacing: 0px 8px;
    }
    .bolds{
      font-weight: bold;
	  text-align: center;
    }

    .bold{
      font-weight: bold;
	  padding:12px 0px 0px 14px;
    }
    .blcs{
    border-collapse: collapse;
    border-bottom: 1px solid #000;
    border-top: 1px solid #000;
    }
    .border{
    border: 1px solid #000;
    }
    .borders{
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
    }
    th {
    font-size: 15px;
    font-weight: bold !important;
    text-align: left;
    }
    table{
    width: 100%;
    }
    </style>
  </head>
  <?php  $st=getstatebystatecode($st_code);   ?>
  <div class="bordertestreport">



          <table class="">
           <tr>
              <td style="text-align: center; font-weight: bold !important;">
			  <p style="font-size: 12px;font-weight: bold;"><strong>Election Commission of India, State Election,{{getElectionYear()}} to the legislative assembly of {{$st->ST_NAME}}</strong></p>
			  </td>
            </tr>
		</table>

<table class="border">
  <tr><td style="text-align: center; font-weight: bold !important;">
                        <p style="font-size: 17px; text-transform: uppercase;"><strong>7 - INDIVIDUAL PERFORMANCE OF WOMEN CANDIDATES
</strong></p>
                  </td>
              </tr>

</table>


  <table class="">
  <?php  if (verifyreport(7, $st_code) == 0){ ?>
           <tr>
        <td style="text-align: left;">
		<b style="font-size: 11px; ">User</b>: ECI</td>
        <td></td>
     
        <td style="">
		<p style="font-size: 11px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?></p>
		</td>
			<td><p style="font-size: 11px;font-weight: bold;">Draft</p></td>
      </tr>
	  
	  <?php } ?>


  </table>






   
  

  <table class="table" style="width: 100%;">
			  <thead>
				
			<tr>
				<th class="bolds">AC No.</th>
				<th class="bolds">Name of AC</th>
				<th class="bolds">Name of candidate </th>
				<th class="bolds">Party</th>
				<th class="bolds">Party Type</th>
				<th class="bolds">Votes Secured</th>
				<th class="bolds">Status</th>
				<th class="bolds">Total votes polled</th>
				<th class="bolds">Valid Votes</th>
				<th colspan="3" class="bolds" style="text-decoration: underline;text-align: center;">% of votes secured over</th>
			</tr>
				<tr>
				  <th colspan="9" class="bolds blc"></th>
				  <th class="bolds blc">Total Electors</th>
				  <th class="bolds blc">Total votes polled</th>
				  <th class="bolds blc">Valid Votes</th>
				</tr>
			  </thead>
			  <tbody>
				
				
				    @foreach($dataArray as $key => $data)
 
        @foreach($data as $key1 => $raw)
        <?php   if($raw['PARTYTYPE']=='Z'){
                  $partytype='IND';
        }else{
             $partytype=$raw['PARTYTYPE'];

        } ?> 
        <tr>
        <td>{{$raw['AC_NO']}}</td>
          <td style="width:15%; text-align: left;">{{$raw['AC_NAME']}}</td>
           
          <td style="width:15%; text-align: left;"> {{ucwords(strtolower($raw['candidate_name']))}} </td>
          <td> {{$raw['party_abbre']}} </td>
          <td> {{$partytype}} </td>
           <td>{{$raw['candidate_votes']}} </td>
           <td> {{$raw['status']}} </td>
           
          <td> {{$raw['total_votes_polled']}} </td>
            <td>{{$raw['total_votes']}} </td>

          <td>@if($raw['total_electors'] > 0)
            {{number_format((float)($raw['candidate_votes']*100)/$raw['total_electors'], 2, '.', '')}}
            @else
              0
            @endif
          </td>
          <td>@if($raw['total_votes'])
          {{number_format((float)($raw['candidate_votes']*100)/$raw['total_votes_polled'], 2, '.', '')}}
            @else
              0
            @endif</td>
          <td>@if($raw['total_votes'])
          {{number_format((float)($raw['candidate_votes']*100)/$raw['total_votes'], 2, '.', '')}}
            @else
              0
            @endif</td>
         
          
        </tr>
        @endforeach
        @endforeach

				
			  </tbody>
			</table>
 



   <h4 style="border-top: 2px solid #000;padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;font-size: 13px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>

 <?php  if ($st_code == 'S17'){ ?>
 <p style="position: relative;top: -11px;font-size: 13px;"><b>*The Election in AC-31 - Akuluto (ST) Nagaland was uncontested. </b></p>
 <?php  } ?>

      <htmlpagefooter name='page-footer'>
 <table>
 <tr>
 
 <td align="right"><span style="float:right;">Page {PAGENO}</span></td>
</tr>

<?php  if (verifyreport(7, $st_code) != 0){ ?>

<tr>
	<td align="left"><span style="float:left; font-size:8px;">{{getreportsequence(7777, $st_code)}}</span></td>
</tr>

<?php } ?>
</table>
 </htmlpagefooter>


</div>
</html>