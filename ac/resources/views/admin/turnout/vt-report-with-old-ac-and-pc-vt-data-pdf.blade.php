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
      <td style="width:50%;">
        <table style="width:100%">
          <tbody>

            <tr>
              <td><strong>User:</strong> {{$user_data->placename}}</td>
            </tr>
          </tbody>
        </table>
      </td>
      <td style="width:50%">
        <table style="width:100%">
          <tbody>
            <tr>
              <td align="right"><strong>Date of Print:</strong> {{ date('d-M-Y h:i a') }}</td>
            </tr>
            <tr>
              <td align="right">&nbsp;</td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>



  </table>
  <table class="table-strip" style="width: 100%;" border="1" align="center">
    <tbody>
      <tr>
        <td align="center"><strong>{!! $heading_title !!}</strong></td>
      </tr>
    </tbody>
  </table>

  <table class="table-strip" style="width: 100%;" border="1" align="center">
    <thead>
      <tr>
        <th> State </th>
        <th> Phase </th>
        <th> AC No</th>
        <th> AC Name </th>
        <!-- <th> Loksabha Election - 2019</th> -->
        <th> Legislative Assembly - 2018</th>
        <th> Legislative Assembly - 2023</th>
        <th> Change In Percentage</th>
      </tr>

    </thead>
    <tbody>
      @foreach($results as $result)
      <tr>
        <td>{{$result['st_name']}}</td>
        <td>{{$result['scheduleid']}}</td>
        <td>{{$result['ac_no']}}</td>
        <td>{{$result['ac_name']}}</td>
        <!-- <td>{{$result['levt_vt']}}</td> -->
        <td>{{$result['lavt_vt']}}</td>
        <td>{{$result['est_turnout_total']}}</td>
        <td style="background-color:{{ ($result['change_in_percentage'] >= 0) ? '':'red' }};">{{$result['change_in_percentage']}}</td>
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
        <td style="color:orange;" colspan="2">Disclaimer : * This is approximate trend as data from some Polling Stations(PS) takes time. Final data for each PS is shared in Form 17C with all Polling Agents.</td>
      </tr>
    </tbody>
  </table>
</body>

</html>