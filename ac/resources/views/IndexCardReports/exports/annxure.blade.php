<table>
      <thead>
		<tr>
          <th colspan="6" align="center"> <b> Electors Data Summary Annxure </b> </th>
        </tr>
	  
        <tr>
          <th rowspan="2"></th>
          <th colspan="4" align="center"> <b> TYPE OF CONSTITUENCY </b> </th>
        </tr>
        <tr>
          <td class="bolds blc"><b> GEN </b></td>
          <td class="bolds blc"><b> SC </b></td>
          <td class="bolds blc"><b> ST </b></td>
          <td class="bolds blc"> <b>TOTAL </b></td>
        </tr>
      </thead>
          <tbody>
        <tr>
          <td><b> 1. NO. OF CONSTITUENCIES </b></td>
          <td>{{isset($actypecountNew['GEN']['genac']) ? $actypecountNew['GEN']['genac'] : 0 }}</td>
         
          <td>{{isset($actypecountNew['SC']['scac']) ? $actypecountNew['SC']['scac']:0}}</td>
         
          <td>{{isset($actypecountNew['ST']['stac']) ? $actypecountNew['ST']['stac'] : 0}}</td>
          <td>{{(isset($actypecountNew['GEN']['genac']) ? $actypecountNew['GEN']['genac'] : 0)+ (isset($actypecountNew['SC']['scac'])? $actypecountNew['SC']['scac'] :0 )
          + (isset($actypecountNew['ST']['stac'])?$actypecountNew['ST']['stac']:0)}}</td>
        </tr>
        <tr>
          <td colspan="4"><b>2. POSTAL VOTES</b></td>
        </tr>
        <tr>
          <td class="boldn">a. Postal Votes(For Service Voters <br> Under sub-Section(8) of Section 20 of <br> R.P. Act,1950)
          </td>
          <td>{{isset($postalvoteNew['GEN']['postalvotesec8'])?$postalvoteNew['GEN']['postalvotesec8'] : 0}}</td>
          <td>{{isset($postalvoteNew['SC']['postalvotesec8'])? $postalvoteNew['SC']['postalvotesec8']:0}}</td>
          <td>{{isset($postalvoteNew['ST']['postalvotesec8']) ? $postalvoteNew['ST']['postalvotesec8'] : 0}}</td>
          <td>{{(isset($postalvoteNew['GEN']['postalvotesec8'])? $postalvoteNew['GEN']['postalvotesec8']:0)+(isset($postalvoteNew['SC']['postalvotesec8']) ? $postalvoteNew['SC']['postalvotesec8']:0) +(isset($postalvoteNew['ST']['postalvotesec8']) ? $postalvoteNew['ST']['postalvotesec8']:0)}}</td>
        </tr>
        <tr><td><p></p></td></tr>
        <tr>
          <td class="boldn">b. Postal Votes(For Govt. Servants <br> on election duty(including all Police <br>Pesonnel, drivers, conductors, <br> cleaners)  and Absentee Voters.
          </td>
          <td>{{isset($postalvoteNew['GEN']['postalvoteservice']) ? $postalvoteNew['GEN']['postalvoteservice']: 0}}</td>
          <td>{{isset($postalvoteNew['SC']['postalvoteservice']) ? $postalvoteNew['SC']['postalvoteservice'] : 0}}</td>
          <td>{{isset($postalvoteNew['ST']['postalvoteservice']) ? $postalvoteNew['ST']['postalvoteservice'] : 0}}</td>
          <td>{{(isset($postalvoteNew['GEN']['postalvoteservice']) ?$postalvoteNew['GEN']['postalvoteservice'] :0) +(isset($postalvoteNew['SC']['postalvoteservice']) ? $postalvoteNew['SC']['postalvoteservice']:0)+(isset($postalvoteNew['ST']['postalvoteservice'])? $postalvoteNew['ST']['postalvoteservice']:0)}}</td>
        </tr>
        <tr>
          <td class="blcs"><b>TOTAL POSTAL VOTES</b></td>
          <td class="blcs">{{(isset($postalvoteNew['GEN']['postalvotesec8']) ?$postalvoteNew['GEN']['postalvotesec8']:0) +(isset($postalvoteNew['GEN']['postalvoteservice']) ? $postalvoteNew['GEN']['postalvoteservice'] :0)}}</td>
          <td class="blcs">{{(isset($postalvoteNew['SC']['postalvotesec8']) ? $postalvoteNew['SC']['postalvotesec8']:0)+(isset($postalvoteNew['SC']['postalvoteservice']) ? $postalvoteNew['SC']['postalvoteservice'] :0) }}</td>
          <td class="blcs">{{(isset($postalvoteNew['ST']['postalvotesec8']) ? $postalvoteNew['ST']['postalvotesec8'] :0)+(isset($postalvoteNew['ST']['postalvoteservice'])? $postalvoteNew['ST']['postalvoteservice'] :0) }}</td>
          <td class="blcs">{{(isset($postalvoteNew['GEN']['postalvotesec8']) ? $postalvoteNew['GEN']['postalvotesec8'] :0) +(isset($postalvoteNew['SC']['postalvotesec8']) ? $postalvoteNew['SC']['postalvotesec8'] : 0) + (isset($postalvoteNew['ST']['postalvotesec8']) ? $postalvoteNew['ST']['postalvotesec8']:0) +(isset($postalvoteNew['GEN']['postalvoteservice'])? $postalvoteNew['GEN']['postalvoteservice']:0)+(isset($postalvoteNew['SC']['postalvoteservice']) ? $postalvoteNew['SC']['postalvoteservice'] : 0)+ (isset($postalvoteNew['ST']['postalvoteservice'])?$postalvoteNew['ST']['postalvoteservice']:0) }}</td>
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