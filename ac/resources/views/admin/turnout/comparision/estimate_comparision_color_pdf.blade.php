    <!DOCTYPE html>
    <html lang="en">

    <head>
      <meta charset="utf-8">
      <title>{!! $heading_title !!}</title>

    </head>

    <body>
      <?php

      $report_time = '';
      $current_time = date('Y-m-d H:i');
      //$current_time = ("$poll_date 17:29");
      //dd($current_time);


      if (strtotime($current_time) >= strtotime("$poll_date 18:00")) {
        //$report_time = 'Latest';
        $report_time = date('h:i A');
      } else if (strtotime($current_time) >= strtotime("$poll_date 17:00")) {
        $report_time = '5 PM';
      } else if (strtotime($current_time) >= strtotime("$poll_date 15:30")) {
        $report_time = '3 PM';
      } else if (strtotime($current_time) >= strtotime("$poll_date 13:30")) {
        $report_time = '1 PM';
      } else if (strtotime($current_time) >= strtotime("$poll_date 11:30")) {
        $report_time = '11 AM';
      } else if (strtotime($current_time) >= strtotime("$poll_date 9:30")) {
        $report_time = '9 AM';
      } else {
        // $report_time = 'Latest';
        $report_time = date('h:i A');
      }

      // dd($report_time);

      ?>





      <!--HEADER STARTS HERE-->
      <table style="width:100%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
        <thead>
          <tr>
            <th style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="<?php echo public_path(); ?>/admintheme/img/logo/eci-logo.png" alt="" width="100" border="0" /></th>
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
          <td style="width:25%;">
            <table style="width:100%">
              <tbody>

                <tr>
                  <td><strong>User:</strong> {{$user_data->placename}}</td>
                </tr>
              </tbody>
            </table>
          </td>
          <td style="width:18%;">
            <table style="width:100%">
              <tbody>

                <tr>
                  <td><strong>{{$report_time}} Report</strong></td>
                </tr>
              </tbody>
            </table>
          </td>


          <td style="width:45%">
            <table style="width:100%">
              <tbody>
                <tr>
                  <td align="right"><strong>Date of Print:</strong> {{ date('d-M-Y h:i A') }}</td>
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
        <tr>
          <th colspan="5" class="text-center">Estimated Voter Turnout Comparison Report</th>
        </tr>
        <tr>
          <th colspan="1">State</th>
          <th colspan="1"> AC No </th>
          <th colspan="1"> AC Name </th>
          <th colspan="1" style="background-color:#ffc1074f;"> Previous Election Turnout (in %)</th>
          <th colspan="1" style="background-color:#90ee90;"> 2023 Estimated Turnout (in %) by {{$report_time}}</th>

        </tr>


        </thead>
        <tbody>
          @foreach($results as $result)
          <tr>
            <td align="left"> {!! $result['label'] !!} </td>
            <td align="right"> {{$result['const_no'] }} </td>
            <td align="left"> {{$result['const'] }} </td>
            <td style="background-color:#ffc1074f;" align="right"> {{ $result['old_total_percentage'] }} </td>
            <td style="background-color:#90ee90;" align="right"> {{$result['est_total'] }} </td>
          </tr>
          @endforeach
        </tbody>
        @if (isset($statetotal))
        @foreach ($statetotal as $raw)
        <tfoot>
          <tr>
            <td colspan="2" align="center" style="font-size:16px;"> <b>{!! $raw['label'] !!} Phase {{$phase}} ({{count($results)}} ACs)</b> </td>
            <td align="center" style="font-size:16px;"> <b>{!! $raw['label'] !!} Loksabha 2019 Turnout</b> </td>
            <td align="center" style="font-size:16px;"> <b>{!! $raw['label'] !!} Assembly Elections 2018 Turnout</b> </td>
            <td align="center" style="font-size:16px;"> <b>{!! $raw['label'] !!} (Phase {{$phase}})
                2023Elections Turnout
                By {{$report_time}} </b>
            </td>
          </tr>
          <tr>
            <td colspan="2" align="center"> Turnout % </td>
            <td align="right"> {{ $raw['loksabha'] }} </td>
            <td align="right"> {{ $raw['assembly'] }} </td>
            <td align="right"> {{$raw['est_total'] }} </td>
          </tr>
        </tfoot>
        @endforeach
        @endif
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