<table>
      <thead>
		<tr>
          <th colspan="17" align="center"> <b> 11 - AC Wise Number Of Electors </b> </th>
        </tr>
		<tr>
		  <td rowspan="2"><b>AC No.</b></td>
          <td rowspan="2"><b>AC Name</b></td>
          <td colspan="4" style="text-align: center;"><b>GENERAL(Including NRIs)</b></td>
          <td colspan="3" style="text-align: center;"><b>SERVICE </b></td>
          <td colspan="4" style="text-align: center;"><b>All Electors</b></td>
          <td colspan="4" style="text-align: center;"><b>NRIs</b></td>
        </tr>
		
		<tr>
          <td><b>MALE</b></td>
          <td><b>FEMALE </b></td>
          <td><b>THIRD GENDER </b></td>
          <td><b>TOTAL</b></td>
		  
		  <td><b>MALE</b></td>
          <td><b>FEMALE </b></td>
          <td><b>TOTAL</b></td>
		  
		  <td><b>MALE</b></td>
          <td><b>FEMALE </b></td>
          <td><b>THIRD GENDER </b></td>
          <td><b>TOTAL</b></td>
		  
		  <td><b>MALE</b></td>
          <td><b>FEMALE </b></td>
          <td><b>THIRD GENDER </b></td>
          <td><b>TOTAL</b></td>
        </tr>
	
      </thead>
          <tbody>
        
		@foreach($electorsdata as $key => $data)
	  
        <tr>
          <td>{{$data->AC_NO}}</td>
          <td>{{$data->AC_NAME}} @if($data->AC_TYPE != 'GEN')({{$data->AC_TYPE}}) @endif</td>
          <td>{{$data->gen_male}}</td>
          <td>{{$data->gen_female}}</td>
          <td>{{$data->gen_third}}</td>
          <td>{{$data->gen_total}}</td>
          <td>{{$data->service_male}}</td>
          <td>{{$data->service_female}}</td>
          <td>{{$data->service_total}}</td>
          <td>{{$data->grand_male}}</td>
          <td>{{$data->grand_female}}</td>
          <td>{{$data->grand_third}}</td>
          <td>{{$data->grand_total}}</td>
          <td>{{$data->nri_male}}</td>
          <td>{{$data->nri_female}}</td>
          <td>{{$data->nri_third}}</td>
          <td>{{$data->nri_total}}</td>
        </tr>
		@endforeach
		
		@foreach($electorsdata_total as $key => $data)
	  
        <tr>
          <td colspan="2" ><b>Total</b></td>
          <td><b>{{$data->gen_male}}</b></td>
          <td><b>{{$data->gen_female}}</b></td>
          <td><b>{{$data->gen_third}}</b></td>
          <td><b>{{$data->gen_total}}</b></td>
          <td><b>{{$data->service_male}}</b></td>
          <td><b>{{$data->service_female}}</b></td>
          <td><b>{{$data->service_total}}</b></td>
          <td><b>{{$data->grand_male}}</b></td>
          <td><b>{{$data->grand_female}}</b></td>
          <td><b>{{$data->grand_third}}</b></td>
          <td><b>{{$data->grand_total}}</b></td>
          <td><b>{{$data->nri_male}}</b></td>
          <td><b>{{$data->nri_female}}</b></td>
          <td><b>{{$data->nri_third}}</b></td>
          <td><b>{{$data->nri_total}}</b></td>
        </tr>
		@endforeach
		
		
		
		
	<tr></tr>	  
	  <tr>
        <td colspan="2"><b>Disclaimer</b></td>
      </tr>
	  <tr>
        <td colspan="17">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</td>

      </tr>
      </tbody>
    </table>