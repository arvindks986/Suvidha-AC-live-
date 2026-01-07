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
        <h3>{!! $heading_title_with_all !!}</h3>
      </td>
    </tr>
  </table>
  <table style="width:100%; border: 1px solid #000;" border="0" align="center">
    <tr>
      <td style="width:50%;"><strong>User:</strong> {{$user_data->placename}}</td>
      <td style="width:50%;" align="right"><strong>Date of Print:</strong> {{ date('d-M-Y h:i a') }}</td>
    </tr>
  </table>
  <table class="table-strip" style="width: 100%;" border="1" align="center">
    <thead>
      <tr>
        <th> State </th>
        <th> Phase </th>
        <th> AC No. </th>
        <th> AC Name </th>
        <th> Round </th>
        <th> Round Percentage </th>
        <th> State Percentage </th>
        <th> Updated By </th>
        <th> Date Time </th>
      </tr>
    </thead>
    <tbody>
      @foreach($results as $result)
      <tr>
        <td>{{$result['st_code'] }} - {{$result['state']['ST_NAME'] }}</td>
        <td>Phase {{$result['phase']['StatePHASE_NO']}}</td>
        <td>{{$result['ac_no'] }}</td>
        <td>{{$result['ac']['AC_NAME'] }}</td>
        <td>{{$result['round'] }}</td>
        <td>{{$result['percentage'] }}</td>
        <td>{{$result['state_percentage'] }}</td>
        <td>{{$result['updatedby'] }}</td>
        <td>{{$result['created_at'] }}</td>
      </tr>
      @endforeach

    </tbody>
  </table>
  <table style="width:100%; border-collapse: collapse;" align="center" border="1" cellpadding="5">
    <tbody>
      <tr>
        <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>
      </tr>
    </tbody>
  </table>
</body>

</html>