<html>
  <head>
    <style>
    td {
    font-size: 12px !important;
    font-weight: 500 !important;
    text-align: left;
    padding: 6px;
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
    font-size: 12px;
    font-weight: bold !important;
    padding: 5px;
    text-align: left;
    }
 
    table{
    width: 100%;
    border-collapse: collapse;
    }
    </style>
  </head>
  <div class="bordertestreport">
    <table class="border">
      <tr>
        <td style="text-align: left;">
          <p> <img src="<?php echo url('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/>  </p>
        </td>
        <td style="text-align: right;">
          <p style="float: right;width: 100%;font-size: 15px;"><b>SECRETARIAT OF THE <br>ELECTION COMMISSION OF INDIA
            </b>
          <br><b>Nirvachan Sadan, Ashoka Road, New Delhi-110001</b></p>
        </td>
      </tr>
    </table>
    <table class="border">
      <tr>
        <td style="text-align: left;">
          <p style="font-size: 15px;"><b>Booth App - Poll Event PS Wise Report</b></p>
        </td>
		<td style="text-align: right;">
          <p style="font-size: 15px;">{{$st_name}} ({{$ac_name}})</p>
        </td>
      </tr>
      <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style="text-align: right;"><p style="float: right;width: 100%;font-size: 15px;"><b>Date of Print</b> :<?php echo date("d-m-Y h:i A") . "\n"; ?></p></td>
      </tr>
    </table>
    <table><tr><td><p></p></td></tr>
  </table>
  <div class="table-responsive">
          <table class="table table-bordered " id="list-table">
           <thead>
            <tr> 
              <th>Sn. No.</th>
              <th>PS NO & PS Name</th>
			  <th>Poll Party Reached</th>
              <th>Mock Poll Done</th>
              <th>Poll Started</th>
              <th>Voting Started</th>
              <th>Final Data Sync</th>
              <th>Poll End</th>
            </tr>
          </thead>
          <tbody>
				  
             @if(count($results)>0)
			 
            @foreach($results as $key => $result)  
			@php
				$st_code = $result['st_code'];
				$ac_no = $result['ac_no'];
			 @endphp
			
             <tr>
              <td>{{$key +1}}</td>
              <td>{{$result['ps_no']}}-{{$result['ps_name']}}</td>
			  <td> @if($result['ps_location']) Yes @else No @endif </td>
              <td> @if($result['mock_poll_start']) Yes @else No @endif </td>             
              <td> @if($result['poll_start']) Yes @else No @endif </td>
              <td> @if($result['total_voter']) Yes @else No @endif </td>
              <td>@if($result['data_sync'] > 0) Yes @else No @endif</td>
              <td>@if($result['poll_end'] > 0) Yes @else No @endif</td>
            </tr>
					
            @endforeach
			
			
            @else 
            <tr>
              <td colspan="7">
                No Record Found.
              </td>
            </tr>
            @endif
          </tbody>
          
        </table>
  
  <table>
    <tr style="width: 100%;">
      <td colspan="7" style="text-align: center;"><p><b style="font-size: 15px;">Nirvachan Sadan, Ashoka Road, New Delhi- 110001</b></p></td>
    </tr>
  </table>
</div>
</html>