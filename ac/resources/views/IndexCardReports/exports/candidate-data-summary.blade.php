<table>
      <thead>
	  <tr>
				  <th colspan="5" align="center"><b>9 - Candidate Data Summary</b></th>
				</tr>
				<tr>
				  <td colspan="5" align="center"></td>
				</tr>
        <tr>
        
          <th colspan="5" align="center"><b>TYPE OF CONSTITUENCY</b></th>
        
        </tr>
        <tr>
          <th></th>
          <th><b>GEN</b></th>
          <th><b>SC</b></th>
          <th><b>ST</b></th>
          <th><b>TOTAL</b></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><b>1. NO. OF CONSTITUENCIES </b></td>
          <td>{{isset($acdataarray['GEN']['seats'])?$acdataarray['GEN']['seats']:0}}</td>
          <td>{{isset($acdataarray['SC']['seats'])?$acdataarray['SC']['seats']:0}}</td>
          <td>{{isset($acdataarray['ST']['seats'])?$acdataarray['ST']['seats']:0}}</td>
          <td>{{(isset($acdataarray['GEN']['seats'])?$acdataarray['GEN']['seats']:0) + (isset($acdataarray['SC']['seats'])?$acdataarray['SC']['seats']:0) + (isset($acdataarray['ST']['seats'])?$acdataarray['ST']['seats']:0)}}</td>
        </tr>
		
        <tr>
          <td><b>2. &nbsp;NOMINATIONS FILED</b></td>
          <td colspan="4"></td>
        </tr>
        <tr>
          <td align="center"><b>a. Male</b></td>
          <td>{{isset($candatawise['GEN']['nom_male'])?$candatawise['GEN']['nom_male']:0}}</td>
          <td>{{isset($candatawise['SC']['nom_male'])?$candatawise['SC']['nom_male']:0}}</td>
          <td>{{isset($candatawise['ST']['nom_male'])?$candatawise['ST']['nom_male']:0}}</td>
          <td>{{(isset($candatawise['GEN']['nom_male'])?$candatawise['GEN']['nom_male']:0) + (isset($candatawise['SC']['nom_male'])?$candatawise['SC']['nom_male']:0) + (isset($candatawise['ST']['nom_male'])?$candatawise['ST']['nom_male']:0)}}</td>
        </tr>
        <tr>
          <td align="center"><b>b. Female</b></td>
          <td>{{isset($candatawise['GEN']['nom_female'])?$candatawise['GEN']['nom_female']:0}}</td>
          <td>{{isset($candatawise['SC']['nom_female'])?$candatawise['SC']['nom_female']:0}}</td>
          <td>{{isset($candatawise['ST']['nom_female'])?$candatawise['ST']['nom_female']:0}}</td>
          <td>{{(isset($candatawise['GEN']['nom_female'])?$candatawise['GEN']['nom_female']:0) + (isset($candatawise['SC']['nom_female'])?$candatawise['SC']['nom_female']:0) + (isset($candatawise['ST']['nom_female'])?$candatawise['ST']['nom_female']:0)}}</td>
        </tr>
        <tr>
          <td align="center"><b>c. Third Gender</b></td>
          <td>{{isset($candatawise['GEN']['nom_third'])?$candatawise['GEN']['nom_third']:0}}</td>
          <td>{{isset($candatawise['SC']['nom_third'])?$candatawise['SC']['nom_third']:0}}</td>
          <td>{{isset($candatawise['ST']['nom_third'])?$candatawise['ST']['nom_third']:0}}</td>
          <td>{{(isset($candatawise['GEN']['nom_third'])?$candatawise['GEN']['nom_third']:0) + (isset($candatawise['SC']['nom_third'])?$candatawise['SC']['nom_third']:0) + (isset($candatawise['ST']['nom_third'])?$candatawise['ST']['nom_third']:0)}}</td>
        </tr>
        <tr>
          <td align="center"><b>d. Total</b></td>
          <td>{{isset($candatawise['GEN']['nom_total'])?$candatawise['GEN']['nom_total']:0}}</td>
          <td>{{isset($candatawise['SC']['nom_total'])?$candatawise['SC']['nom_total']:0}}</td>
          <td>{{isset($candatawise['ST']['nom_total'])?$candatawise['ST']['nom_total']:0}}</td>
          <td>{{(isset($candatawise['GEN']['nom_total'])?$candatawise['GEN']['nom_total']:0) + (isset($candatawise['SC']['nom_total'])?$candatawise['SC']['nom_total']:0) + (isset($candatawise['ST']['nom_total'])?$candatawise['ST']['nom_total']:0)}}</td>
        </tr>
        <tr>
          <td><b>3.&nbsp; NOMINATIONS REJECTED</b>
          </td>
          <td colspan="4"></td>
        </tr>
        <tr>
          <td align="center"><b>a. Male</b></td>
          <td>{{isset($candatawise['GEN']['rej_male'])?$candatawise['GEN']['rej_male']:0}}</td>
          <td>{{isset($candatawise['SC']['rej_male'])?$candatawise['SC']['rej_male']:0}}</td>
          <td>{{isset($candatawise['ST']['rej_male'])?$candatawise['ST']['rej_male']:0}}</td>
          <td>{{(isset($candatawise['GEN']['rej_male'])?$candatawise['GEN']['rej_male']:0) + (isset($candatawise['SC']['rej_male'])?$candatawise['SC']['rej_male']:0) + (isset($candatawise['ST']['rej_male'])?$candatawise['ST']['rej_male']:0)}}</td>
        </tr>
        <tr>
          <td align="center"><b>b. Female</b></td>
          <td>{{isset($candatawise['GEN']['rej_female'])?$candatawise['GEN']['rej_female']:0}}</td>
          <td>{{isset($candatawise['SC']['rej_female'])?$candatawise['SC']['rej_female']:0}}</td>
          <td>{{isset($candatawise['ST']['rej_female'])?$candatawise['ST']['rej_female']:0}}</td>
          <td>{{(isset($candatawise['GEN']['rej_female'])?$candatawise['GEN']['rej_female']:0) + (isset($candatawise['SC']['rej_female'])?$candatawise['SC']['rej_female']:0) + (isset($candatawise['ST']['rej_female'])?$candatawise['ST']['rej_female']:0)}}</td>
        </tr>
        <tr>
          <td align="center"><b>c. Third Gender</b></td>
          <td>{{isset($candatawise['GEN']['rej_third'])?$candatawise['GEN']['rej_third']:0}}</td>
          <td>{{isset($candatawise['SC']['rej_third'])?$candatawise['SC']['rej_third']:0}}</td>
          <td>{{isset($candatawise['ST']['rej_third'])?$candatawise['ST']['rej_third']:0}}</td>
          <td>{{(isset($candatawise['GEN']['rej_third'])?$candatawise['GEN']['rej_third']:0) + (isset($candatawise['SC']['rej_third'])?$candatawise['SC']['rej_third']:0) + (isset($candatawise['ST']['rej_third'])?$candatawise['ST']['rej_third']:0)}}</td>
        </tr>
        <tr>
          <td align="center"><b>d. Total</b></td>
          <td>{{isset($candatawise['GEN']['rej_total'])?$candatawise['GEN']['rej_total']:0}}</td>
          <td>{{isset($candatawise['SC']['rej_total'])?$candatawise['SC']['rej_total']:0}}</td>
          <td>{{isset($candatawise['ST']['rej_total'])?$candatawise['ST']['rej_total']:0}}</td>
          <td>{{(isset($candatawise['GEN']['rej_total'])?$candatawise['GEN']['rej_total']:0) + (isset($candatawise['SC']['rej_total'])?$candatawise['SC']['rej_total']:0) + (isset($candatawise['ST']['rej_total'])?$candatawise['ST']['rej_total']:0)}}</td>
        </tr>
        <tr>
          <td><b>4.&nbsp; NOMINATIONS WITHDRAWN</b>
          </td>
          <td colspan="4"></td>
        </tr>
        <tr>
          <td align="center"><b>a. Male</b></td>
          <td>{{isset($candatawise['GEN']['with_male'])?$candatawise['GEN']['with_male']:0}}</td>
          <td>{{isset($candatawise['SC']['with_male'])?$candatawise['SC']['with_male']:0}}</td>
          <td>{{isset($candatawise['ST']['with_male'])?$candatawise['ST']['with_male']:0}}</td>
          <td>{{(isset($candatawise['GEN']['with_male'])?$candatawise['GEN']['with_male']:0) + (isset($candatawise['SC']['with_male'])?$candatawise['SC']['with_male']:0) + (isset($candatawise['ST']['with_male'])?$candatawise['ST']['with_male']:0)}}</td>
        </tr>
        <tr>
          <td align="center"><b>b. Female</b></td>
          <td>{{isset($candatawise['GEN']['with_female'])?$candatawise['GEN']['with_female']:0}}</td>
          <td>{{isset($candatawise['SC']['with_female'])?$candatawise['SC']['with_female']:0}}</td>
          <td>{{isset($candatawise['ST']['with_female'])?$candatawise['ST']['with_female']:0}}</td>
          <td>{{(isset($candatawise['GEN']['with_female'])?$candatawise['GEN']['with_female']:0) + (isset($candatawise['SC']['with_female'])?$candatawise['SC']['with_female']:0) + (isset($candatawise['ST']['with_female'])?$candatawise['ST']['with_female']:0)}}</td>
        </tr>
        <tr>
          <td align="center"><b>c. Third Gender</b></td>
          <td>{{isset($candatawise['GEN']['with_third'])?$candatawise['GEN']['with_third']:0}}</td>
          <td>{{isset($candatawise['SC']['with_third'])?$candatawise['SC']['with_third']:0}}</td>
          <td>{{isset($candatawise['ST']['with_third'])?$candatawise['ST']['with_third']:0}}</td>
          <td>{{(isset($candatawise['GEN']['with_third'])?$candatawise['GEN']['with_third']:0) + (isset($candatawise['SC']['with_third'])?$candatawise['SC']['with_third']:0) + (isset($candatawise['ST']['with_third'])?$candatawise['ST']['with_third']:0)}}</td>
        </tr>
        <tr>
          <td align="center"><b>d. Total</b></td>
          <td>{{isset($candatawise['GEN']['with_total'])?$candatawise['GEN']['with_total']:0}}</td>
          <td>{{isset($candatawise['SC']['with_total'])?$candatawise['SC']['with_total']:0}}</td>
          <td>{{isset($candatawise['ST']['with_total'])?$candatawise['ST']['with_total']:0}}</td>
          <td>{{(isset($candatawise['GEN']['with_total'])?$candatawise['GEN']['with_total']:0) + (isset($candatawise['SC']['with_total'])?$candatawise['SC']['with_total']:0) + (isset($candatawise['ST']['with_total'])?$candatawise['ST']['with_total']:0)}}</td>
        </tr>
        <tr>
          <td><b>5. &nbsp;CONTESTING CANDIDATES</b>
          </td>
          <td colspan="4"></td>
        </tr>
        <tr>
          <td align="center"><b>a. Male</b></td>
          <td>{{isset($candatawise['GEN']['cont_male'])?$candatawise['GEN']['cont_male']:0}}</td>
          <td>{{isset($candatawise['SC']['cont_male'])?$candatawise['SC']['cont_male']:0}}</td>
          <td>{{isset($candatawise['ST']['cont_male'])?$candatawise['ST']['cont_male']:0}}</td>
          <td>{{(isset($candatawise['GEN']['cont_male'])?$candatawise['GEN']['cont_male']:0) + (isset($candatawise['SC']['cont_male'])?$candatawise['SC']['cont_male']:0) + (isset($candatawise['ST']['cont_male'])?$candatawise['ST']['cont_male']:0)}}</td>
        </tr>
        <tr>
          <td align="center"><b>b. Female</b></td>
          <td>{{isset($candatawise['GEN']['cont_female'])?$candatawise['GEN']['cont_female']:0}}</td>
          <td>{{isset($candatawise['SC']['cont_female'])?$candatawise['SC']['cont_female']:0}}</td>
          <td>{{isset($candatawise['ST']['cont_female'])?$candatawise['ST']['cont_female']:0}}</td>
          <td>{{(isset($candatawise['GEN']['cont_female'])?$candatawise['GEN']['cont_female']:0) + (isset($candatawise['SC']['cont_female'])?$candatawise['SC']['cont_female']:0) + (isset($candatawise['ST']['cont_female'])?$candatawise['ST']['cont_female']:0)}}</td>
        </tr>
        <tr>
          <td align="center"><b>c. Third Gender</b></td>
          <td>{{isset($candatawise['GEN']['cont_third'])?$candatawise['GEN']['cont_third']:0}}</td>
          <td>{{isset($candatawise['SC']['cont_third'])?$candatawise['SC']['cont_third']:0}}</td>
          <td>{{isset($candatawise['ST']['cont_third'])?$candatawise['ST']['cont_third']:0}}</td>
          <td>{{(isset($candatawise['GEN']['cont_third'])?$candatawise['GEN']['cont_third']:0) + (isset($candatawise['SC']['cont_third'])?$candatawise['SC']['cont_third']:0) + (isset($candatawise['ST']['cont_third'])?$candatawise['ST']['cont_third']:0)}}</td>
        </tr>
        <tr>
          <td align="center"><b>d. Total</b></td>
          <td>{{isset($candatawise['GEN']['cont_total'])?$candatawise['GEN']['cont_total']:0}}</td>
          <td>{{isset($candatawise['SC']['cont_total'])?$candatawise['SC']['cont_total']:0}}</td>
          <td>{{isset($candatawise['ST']['cont_total'])?$candatawise['ST']['cont_total']:0}}</td>
          <td>{{(isset($candatawise['GEN']['cont_total'])?$candatawise['GEN']['cont_total']:0) + (isset($candatawise['SC']['cont_total'])?$candatawise['SC']['cont_total']:0) + (isset($candatawise['ST']['cont_total'])?$candatawise['ST']['cont_total']:0)}}</td>
        </tr>
        <tr>
          <td><b>6.&nbsp; FORFEITED DEPOSITS</b>
          </td>
          <td colspan="4"></td>
        </tr>
        <tr>
          <td align="center"><b>a. Male</b></td>
          <td>{{isset($dfdataarray['GEN']['male'])?$dfdataarray['GEN']['male']:0}}</td>
          <td>{{isset($dfdataarray['SC']['male'])?$dfdataarray['SC']['male']:0}}</td>
          <td>{{isset($dfdataarray['ST']['male'])?$dfdataarray['ST']['male']:0}}</td>
          <td>{{(isset($dfdataarray['GEN']['male'])?$dfdataarray['GEN']['male']:0) + (isset($dfdataarray['SC']['male'])?$dfdataarray['SC']['male']:0) + (isset($dfdataarray['ST']['male'])?$dfdataarray['ST']['male']:0)}}</td>
        </tr>
        <tr>
          <td align="center"><b>b. Female</b></td>
          <td>{{isset($dfdataarray['GEN']['female'])?$dfdataarray['GEN']['female']:0}}</td>
          <td>{{isset($dfdataarray['SC']['female'])?$dfdataarray['SC']['female']:0}}</td>
          <td>{{isset($dfdataarray['ST']['female'])?$dfdataarray['ST']['female']:0}}</td>
          <td>{{(isset($dfdataarray['GEN']['female'])?$dfdataarray['GEN']['female']:0) + (isset($dfdataarray['SC']['female'])?$dfdataarray['SC']['female']:0) + (isset($dfdataarray['ST']['female'])?$dfdataarray['ST']['female']:0)}}</td>
        </tr>
        <tr>
		  <td align="center"><b>c. Third Gender</b></td>
          <td>{{isset($dfdataarray['GEN']['third'])?$dfdataarray['GEN']['third']:0}}</td>
          <td>{{isset($dfdataarray['SC']['third'])?$dfdataarray['SC']['third']:0}}</td>
          <td>{{isset($dfdataarray['ST']['third'])?$dfdataarray['ST']['third']:0}}</td>
          <td>{{(isset($dfdataarray['GEN']['third'])?$dfdataarray['GEN']['third']:0) + (isset($dfdataarray['SC']['third'])?$dfdataarray['SC']['third']:0) + (isset($dfdataarray['ST']['third'])?$dfdataarray['ST']['third']:0)}}</td>
        </tr>
        <tr>
          <td align="center"><b>d. Total</b></td>
          <td>{{isset($dfdataarray['GEN']['total'])?$dfdataarray['GEN']['total']:0}}</td>
          <td>{{isset($dfdataarray['SC']['total'])?$dfdataarray['SC']['total']:0}}</td>
          <td>{{isset($dfdataarray['ST']['total'])?$dfdataarray['ST']['total']:0}}</td>
          <td>{{(isset($dfdataarray['GEN']['total'])?$dfdataarray['GEN']['total']:0) + (isset($dfdataarray['SC']['total'])?$dfdataarray['SC']['total']:0) + (isset($dfdataarray['ST']['total'])?$dfdataarray['ST']['total']:0)}}</td>
        </tr>
															  <tr></tr>	  
	  <tr>
        <td colspan="2"><b>Disclaimer</b></td>
      </tr>
	  <tr>
        <td colspan="10">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</td>
		
      </tr>
      </tbody>
    </table>