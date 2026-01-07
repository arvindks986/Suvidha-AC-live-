<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>{!! $heading_title !!}</title>

</head>

<body>
  <!--HEADER STARTS HERE-->
  <table style="width:100%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
    <thead>
      <tr>
        <th style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="{{ public_path('/admintheme/img/logo/eci-logo.png') }}" alt="" width="100" border="0" /></th>
        <th style="width:50%" align="right" style="border-bottom: 1px dotted #d7d7d7;">
          SECRETARIAT OF THE<br>
          ELECTION COMMISSION OF INDIA<br>
          Nirvachan Sadan, Ashoka Road, New Delhi-110001<br>
        </th>
      </tr>
    </thead>
  </table>
  <!--HEADER ENDS HERE-->
  <style type="text/css">
    .table-strip {
      border-collapse: collapse;
    }

    .table-strip th,
    .table-strip td {
      text-align: center;
    }

    .table-strip tr:nth-child(odd) {
      background-color: #f5f5f5;
    }
  </style>
  <table style="width:100%; border: 1px solid #000;" border="0" align="center">
    <tr>
      <td align="center">
        <h3>{{$state_name}} phase {{$state_phase}} Election</h3>
      </td>
    </tr>
  </table>
  <table style="width:100%; border: 1px solid #000;" border="0" align="center">
    <tr>
      <td style="width:50%;"><strong>User:</strong> {{$user_data->placename}}</td>
      <td style="width:50%;" align="right"><strong>Date of Print:</strong> {{ date('d-M-Y h:i a') }}</td>
    </tr>
  </table>
  <table style="width:100%; border: 1px solid #000;" border="0" align="center">
    <tr>
      <td style="width:100%;"><strong>Close of Poll:</strong> It is an estimated voter turnout percentage entered by the RO after the poll closes.</td>
    </tr>
    <tr>
      <td style="width:100%;"><strong>End of Poll:</strong> This is the voter turnout percentage calculated by the system after finalizing the polling station-wise male, female and transgender voters by RO, DEO, and CEO.</td>
    </tr>
  </table>
  <table class="table-strip" style="width: 100%;" border="1" align="center">
    <thead>
      <tr>
        <th align="right"> AC No </th>
        <th align="left"> AC Name </th>
        <th align="left"> District</th>
        <th> Close of Poll</th>
        <th> End of Poll</th>
      </tr>
    </thead>
    <tbody>
      @foreach($results as $result)
      <tr>
        <td align="right">{{$result['ac_no'] }}</td>
        <td align="left">{{$result['ac_name'] }}</td>
        <td align="left">{{$result['dist_name']}}</td>
        <td>{{$result['updated_at_close_of_poll'] }}</td>
        <td>{{$result['end_of_poll_finalize'] }}</td>
      </tr>
      @endforeach

    </tbody>
  </table>
  <table style="width:100%; border-collapse: collapse;" align="center" border="1" cellpadding="5">
    <tbody>
      <tr>
        <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>
      </tr>
      <tr>
        <td colspan="2" align="center"><strong>NOTE: The Close of poll updating date may vary for those ACs who have taken approval to update the percentage from the concerned DEC.</strong></td>
      </tr>
    </tbody>
  </table>
</body>

</html>