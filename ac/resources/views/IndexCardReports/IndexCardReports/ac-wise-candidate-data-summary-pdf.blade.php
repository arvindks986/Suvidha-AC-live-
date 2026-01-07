<?php  $st=getstatebystatecode($st_code);   ?>
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
    text-align: left;
    padding: 9px;
    font-family: "Times New Roman", Times, serif;
    }
    h3{
    font-size: 18px !important;
    font-weight: 600;
    }
    .left-al tr td{
    text-align: left;
    }
    .table-bordered{
    border:1px solid #000;
    }
    .table-bordered td,
    .table-bordered th {
    border: 1px solid #000 !important
    }
    .bolds{
      font-weight: bold;
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
    .top
    {
      border-top: 1px solid #000;
    }
    .boldn{
      font-weight: bold;
      padding: 12px 0px 0px 30px;
    }  

     .bold{
      font-weight: bold;
    }
    .blcs{
    border-collapse: collapse;
    border-bottom: 1px solid #000;
    border-top: 1px solid #000;
    font-weight: bold;
    }
    .border{
    border: 1px solid #000;
    }
    .borders{
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
    }
    th {
    font-size: 12px;
    font-weight: bold !important;
    text-align: left;
    }
    table{
    width: 100%;
    }
    </style>
  </head>
  <div class="bordertestreport">



          <table class="">
           <tr>
              <td style="text-align: center; font-weight: bold !important;"><p style="font-size: 12px;font-weight: bold;"><strong>Election Commission of India, State Election,{{getElectionYear()}} to the legislative Assembly of {{$st->ST_NAME}}
</strong></p></td>
            </tr>
             
  </table>

<table class="border">
  <tr><td style="text-align: center; font-weight: bold !important;">
                        <p style="font-size: 17px; text-transform: uppercase;"><strong>13 - AC Wise Candidate data Summary</strong></p>
                  </td>
              </tr>

</table>
<br>

  <table class="">
  <?php  if (verifyreport(14, $st_code) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 12px; ">User</b>: ECI</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style=""><p style="font-size: 12px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 12px;font-weight: bold;">Draft</p></td>
      </tr>
  <?php } ?>


  </table>



<table>
  <tr><td><p></p></td></tr>
</table>



    <table class="table table-bordered table-striped" style="width: 100%;">
      <thead>
        <tr>
          <td class="blcs" rowspan="2"><b>AC No.</b></td>
          <td class="blcs" rowspan="2"><b>AC Name</b></td>
          <td class="blcs" rowspan="2"><b>CANDIDATE'S CATEGORY<b></td>
          <td class="blcs" colspan="4" style="text-align: center;">NOMINATIONS FILED</td>
          <td class="blcs" colspan="4" style="text-align: center;">NOMINATIONS REJECTED</td>
          <td class="blcs" colspan="4" style="text-align: center;">NOMINATIONS WITHDRAWN</td>
          <td class="blcs" colspan="4" style="text-align: center;">CONTESTING CANDIDATES</td>
          <td class="blcs" colspan="4" style="text-align: center;">DEPOSIT FORFIETED</td>
        </tr>
		
		<tr>
		  
          <td class="blcs">MALE</td>
          <td class="blcs">FEMALE </td>
          <td class="blcs">THIRD GENDER </td>
          <td class="blcs">TOTAL</td>
		  
		  <td class="blcs">MALE</td>
          <td class="blcs">FEMALE </td>
		  <td class="blcs">THIRD GENDER </td>
          <td class="blcs">TOTAL</td>
		  
		  <td class="blcs">MALE</td>
          <td class="blcs">FEMALE </td>
          <td class="blcs">THIRD GENDER </td>
          <td class="blcs">TOTAL</td>
		  
		  <td class="blcs">MALE</td>
          <td class="blcs">FEMALE </td>
          <td class="blcs">THIRD GENDER </td>
          <td class="blcs">TOTAL</td>
		  
		  <td class="blcs">MALE</td>
          <td class="blcs">FEMALE </td>
          <td class="blcs">THIRD GENDER </td>
          <td class="blcs">TOTAL</td>
        </tr>
      </thead>
      <tbody>
	  
	  <?php 
	  
	  foreach($dataAcType as $ac_data){
		
		$ac_no = $ac_data->AC_NO;
		
		 $nom_male_total = $nom_female_total = $nom_third_total = $nom_total_total = $rej_male_total = $rej_female_total = $rej_third_total = $rej_total_total = $with_male_total = $with_female_total = $with_third_total = $with_total_total = $cont_male_total = $cont_female_total = $cont_third_total = $cont_total_total = $df_male_total = $df_female_total = $df_third_total = $df_total_total = 0;
		
		foreach($cat as $category){			
			$candatawise = App\models\Admin\CandidateCountModel::get_count_by_status_category_ac($st_code,$ac_no, $category);
			
			
			$nom_male_total += $candatawise['nom_male'] ?? 0;
			$nom_female_total += $candatawise['nom_female'] ?? 0;
			$nom_third_total += $candatawise['nom_third'] ?? 0;
			$nom_total_total += $candatawise['nom_total'] ?? 0;
			$rej_male_total += $candatawise['rej_male'] ?? 0;
			$rej_female_total += $candatawise['rej_female'] ?? 0;
			$rej_third_total += $candatawise['rej_third'] ?? 0;
			$rej_total_total += $candatawise['rej_total'] ?? 0;
			$with_male_total += $candatawise['with_male'] ?? 0;
			$with_female_total += $candatawise['with_female'] ?? 0;
			$with_third_total += $candatawise['with_third'] ?? 0;
			$with_total_total += $candatawise['with_total'] ?? 0;
			$cont_male_total += $candatawise['cont_male'] ?? 0;
			$cont_female_total += $candatawise['cont_female'] ?? 0;
			$cont_third_total += $candatawise['cont_third'] ?? 0;
			$cont_total_total += $candatawise['cont_total'] ?? 0;
			$df_male_total += $candatawise['df_male'] ?? 0;
			$df_female_total += $candatawise['df_female'] ?? 0;
			$df_third_total += $candatawise['df_third'] ?? 0;
			$df_total_total += $candatawise['df_total'] ?? 0;
		?>
		
		
		 <tr>
          <td>{{$ac_no}}</td>
          <td>{{$ac_data->AC_NAME}} @if($ac_data->AC_TYPE != 'GEN')({{$ac_data->AC_TYPE}}) @endif</td>
          <td>{{strtoupper($category)}}</td>
          <td>{{ $candatawise['nom_male'] ?? '0' }}</td>
          <td>{{ $candatawise['nom_female'] ?? '0' }}</td>
          <td>{{ $candatawise['nom_third'] ?? '0' }}</td>
          <td>{{ $candatawise['nom_total'] ?? '0' }}</td>
          <td>{{ $candatawise['rej_male'] ?? '0' }}</td>
          <td>{{ $candatawise['rej_female'] ?? '0' }}</td>
          <td>{{ $candatawise['rej_third'] ?? '0' }}</td>
          <td>{{ $candatawise['rej_total'] ?? '0' }}</td>
          <td>{{ $candatawise['with_male'] ?? '0' }}</td>
          <td>{{ $candatawise['with_female'] ?? '0' }}</td>
          <td>{{ $candatawise['with_third'] ?? '0' }}</td>
          <td>{{ $candatawise['with_total'] ?? '0' }}</td>
          <td>{{ $candatawise['cont_male'] ?? '0' }}</td>
          <td>{{ $candatawise['cont_female'] ?? '0' }}</td>
          <td>{{ $candatawise['cont_third'] ?? '0' }}</td>
          <td>{{ $candatawise['cont_total'] ?? '0' }}</td>
          <td>{{ $candatawise['df_male'] ?? '0' }}</td>
          <td>{{ $candatawise['df_female'] ?? '0' }}</td>
          <td>{{ $candatawise['df_third'] ?? '0' }}</td>
          <td>{{ $candatawise['df_total'] ?? '0' }}</td>
         
        </tr>
		
		
		<?php } ?>

		<tr>
          <td colspan="3" style="text-align: center;"><b>Total</b></td>
          <td><b>{{ $nom_male_total }}</b></td>
          <td><b>{{ $nom_female_total }}</b></td>
          <td><b>{{ $nom_third_total }}</b></td> 
		  <td><b>{{ $nom_total_total }}</b></td>
		  <td><b>{{ $rej_male_total }}</b></td>
		  <td><b>{{ $rej_female_total }}</b></td> 
		  <td><b>{{ $rej_third_total }}</b></td> 
		  <td><b>{{ $rej_total_total }}</b></td> 
		  <td><b>{{ $with_male_total }}</b></td> 
		  <td><b>{{ $with_female_total }}</b></td> 
		  <td><b>{{ $with_third_total }}</b></td>
		  <td><b>{{ $with_total_total }}</b></td>
		  <td><b>{{ $cont_male_total }}</b></td> 
		  <td><b>{{ $cont_female_total }}</b></td>
		  <td><b>{{ $cont_third_total }}</b></td>
		  <td><b>{{ $cont_total_total }}</b></td>
		  <td><b>{{ $df_male_total }}</b></td> 
		  <td><b>{{ $df_female_total }}</b></td>
		  <td><b>{{ $df_third_total }}</b></td>
		  <td><b>{{ $df_total_total }}</b></td>         
        </tr>
		
		
	<?php	}  
	  

		 $nom_male_total = $nom_female_total = $nom_third_total = $nom_total_total = $rej_male_total = $rej_female_total = $rej_third_total = $rej_total_total = $with_male_total = $with_female_total = $with_third_total = $with_total_total = $cont_male_total = $cont_female_total = $cont_third_total = $cont_total_total = $df_male_total = $df_female_total = $df_third_total = $df_total_total = 0;
		
		foreach($cat as $category){			
			$candatawise = App\models\Admin\CandidateCountModel::get_count_by_status_category_state($st_code, $category);
			
			
			$nom_male_total += $candatawise['nom_male'] ?? 0;
			$nom_female_total += $candatawise['nom_female'] ?? 0;
			$nom_third_total += $candatawise['nom_third'] ?? 0;
			$nom_total_total += $candatawise['nom_total'] ?? 0;
			$rej_male_total += $candatawise['rej_male'] ?? 0;
			$rej_female_total += $candatawise['rej_female'] ?? 0;
			$rej_third_total += $candatawise['rej_third'] ?? 0;
			$rej_total_total += $candatawise['rej_total'] ?? 0;
			$with_male_total += $candatawise['with_male'] ?? 0;
			$with_female_total += $candatawise['with_female'] ?? 0;
			$with_third_total += $candatawise['with_third'] ?? 0;
			$with_total_total += $candatawise['with_total'] ?? 0;
			$cont_male_total += $candatawise['cont_male'] ?? 0;
			$cont_female_total += $candatawise['cont_female'] ?? 0;
			$cont_third_total += $candatawise['cont_third'] ?? 0;
			$cont_total_total += $candatawise['cont_total'] ?? 0;
			$df_male_total += $candatawise['df_male'] ?? 0;
			$df_female_total += $candatawise['df_female'] ?? 0;
			$df_third_total += $candatawise['df_third'] ?? 0;
			$df_total_total += $candatawise['df_total'] ?? 0;
		?>
		
		
		 <tr>
          <td colspan="2" style="text-align: center;"><b>ALL</b></td>
          <td><b>{{strtoupper($category)}}</b></td>
          <td>{{ $candatawise['nom_male'] ?? '0' }}</td>
          <td>{{ $candatawise['nom_female'] ?? '0' }}</td>
          <td>{{ $candatawise['nom_third'] ?? '0' }}</td>
          <td>{{ $candatawise['nom_total'] ?? '0' }}</td>
          <td>{{ $candatawise['rej_male'] ?? '0' }}</td>
          <td>{{ $candatawise['rej_female'] ?? '0' }}</td>
          <td>{{ $candatawise['rej_third'] ?? '0' }}</td>
          <td>{{ $candatawise['rej_total'] ?? '0' }}</td>
          <td>{{ $candatawise['with_male'] ?? '0' }}</td>
          <td>{{ $candatawise['with_female'] ?? '0' }}</td>
          <td>{{ $candatawise['with_third'] ?? '0' }}</td>
          <td>{{ $candatawise['with_total'] ?? '0' }}</td>
          <td>{{ $candatawise['cont_male'] ?? '0' }}</td>
          <td>{{ $candatawise['cont_female'] ?? '0' }}</td>
          <td>{{ $candatawise['cont_third'] ?? '0' }}</td>
          <td>{{ $candatawise['cont_total'] ?? '0' }}</td>
          <td>{{ $candatawise['df_male'] ?? '0' }}</td>
          <td>{{ $candatawise['df_female'] ?? '0' }}</td>
          <td>{{ $candatawise['df_third'] ?? '0' }}</td>
          <td>{{ $candatawise['df_total'] ?? '0' }}</td>
         
        </tr>
		
		
		<?php } ?>

		<tr>
          <td colspan="3" style="text-align: center;"><b>ALL Total</b></td>
          <td><b>{{ $nom_male_total }}</b></td>
          <td><b>{{ $nom_female_total }}</b></td>
          <td><b>{{ $nom_third_total }}</b></td> 
		  <td><b>{{ $nom_total_total }}</b></td>
		  <td><b>{{ $rej_male_total }}</b></td>
		  <td><b>{{ $rej_female_total }}</b></td> 
		  <td><b>{{ $rej_third_total }}</b></td> 
		  <td><b>{{ $rej_total_total }}</b></td> 
		  <td><b>{{ $with_male_total }}</b></td> 
		  <td><b>{{ $with_female_total }}</b></td> 
		  <td><b>{{ $with_third_total }}</b></td>
		  <td><b>{{ $with_total_total }}</b></td>
		  <td><b>{{ $cont_male_total }}</b></td> 
		  <td><b>{{ $cont_female_total }}</b></td>
		  <td><b>{{ $cont_third_total }}</b></td>
		  <td><b>{{ $cont_total_total }}</b></td>
		  <td><b>{{ $df_male_total }}</b></td> 
		  <td><b>{{ $df_female_total }}</b></td>
		  <td><b>{{ $df_third_total }}</b></td>
		  <td><b>{{ $df_total_total }}</b></td>         
        </tr>
        
      </tbody>
    </table>
   


   <h4 style="padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;font-size: 12px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>
 
 <?php  if ($st_code == 'S17'){ ?>
 <p style="position: relative;top: -11px;font-size: 13px;"><b>*The Election in AC-31 - Akuluto (ST) Nagaland was uncontested. </b></p>
 <?php  } ?>
 
  </div>
   <htmlpagefooter name='page-footer'>
 <table>
 
<tr>

<?php  if (verifyreport(14, $st_code) != 0){ ?>


	<td align="left"><span style="font-size:8px; ">{{getreportsequence(7777, $st_code)}}</span></td>


<?php } ?>

 
 <td align="right"><span style="float:right;">Page {PAGENO}</span></td>
</tr>


</table>
 </htmlpagefooter>



</html>
