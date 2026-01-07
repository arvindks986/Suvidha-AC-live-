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
  @if($state != null)
  <table class="table-strip" style="width: 100%;" border="1" align="center">
    <thead>
      <tr>
        <th colspan="17" class="text-center">{!! $heading_title_with_all !!}</th>
      </tr>
      <tr>
        <td></td>
        <td>VT %</td>
        <td>Missing No. Of ACs</td>
        <td>Change of VT %</td>
        <td>Final VT %</td>
      </tr>

    </thead>
    <tbody>
      <tr>
        <td>09:30</td>
        <td>{{$results['round1_per_exclude_missed_ac']}} %</td>
        <td>{{$results['round1_missed_ac_count']}}</td>
        <td>{{number_format(($results['round1_per_include_missed_ac']-$results['round1_per_exclude_missed_ac']), 2, '. ', '' )}} %</td>
        <td>{{$results['round1_per_include_missed_ac']}} %</td>
      </tr>
      <tr>
        <td>11:30</td>
        <td>{{$results['round2_per_exclude_missed_ac']}} %</td>
        <td>{{$results['round2_missed_ac_count']}}</td>
        <td>{{number_format(($results['round2_per_include_missed_ac']-$results['round2_per_exclude_missed_ac']), 2, '. ', '' )}} %</td>
        <td>{{$results['round2_per_include_missed_ac']}} %</td>
      </tr>
      <tr>
        <td>01:30</td>
        <td>{{$results['round3_per_exclude_missed_ac']}} %</td>
        <td>{{$results['round3_missed_ac_count']}}</td>
        <td>{{number_format(($results['round3_per_include_missed_ac']-$results['round3_per_exclude_missed_ac']), 2, '. ', '' )}} %</td>
        <td>{{$results['round3_per_include_missed_ac']}} %</td>
      </tr>
      <tr>
        <td>03:30</td>
        <td>{{$results['round4_per_exclude_missed_ac']}} %</td>
        <td>{{$results['round4_missed_ac_count']}}</td>
        <td>{{number_format(($results['round4_per_include_missed_ac']-$results['round4_per_exclude_missed_ac']), 2, '. ', '' )}} %</td>
        <td>{{$results['round4_per_include_missed_ac']}} %</td>
      </tr>
      <tr>
        <td>05:30</td>
        <td>{{$results['round5_per_exclude_missed_ac']}} %</td>
        <td>{{$results['round5_missed_ac_count']}}</td>
        <td>{{number_format(($results['round5_per_include_missed_ac']-$results['round5_per_exclude_missed_ac']), 2, '. ', '' )}} %</td>
        <td>{{$results['round5_per_include_missed_ac']}} %</td>
      </tr>
      <tr>
        <th>State</th>
        <th>{{$state_name}}</th>
        <th colspan="2">Final Voter Turnout %</th>
        <th>{{$results['final']}} %</th>
      </tr>
    </tbody>
  </table>
  @endif
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