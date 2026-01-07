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
        <tr>
          <th colspan="5" class="text-center">Estimated Voter Turnout Comparison Report</th>
        </tr>
        <tr>
          <th colspan="1">State</th>
          <th colspan="1"> AC No &amp; Name </th>
          <th colspan="1">Previous Election Turnout (in %)</th>
          <th colspan="1">2023 Estimated Turnout (in %)</th>
          <th colspan="1">Change from Previous Election</th>
        </tr>
        </thead>
        <tbody>
          @foreach($results as $result)
          <tr>
            <td align="left">{!! $result['label'] !!}</td>
            <td align="left">{{$result['const_no'] }}-{{$result['const'] }} </td>
            <td align="right">{{ $result['old_total_percentage'] }}</td>
            <td align="right">{{$result['est_total'] }}</td>
            <td align="right">{{$result['difference'] }}</td>
          </tr>
          @endforeach
          <?php if (isset($totals) && count($$total) > 0) { ?>
        <tfoot>
          <tr>
            <td align="left" colspan="3"><span>{!! $totals['label'] !!}</span></td>
            <td></td>
            <td></td>
            <td align="right">{!! $totals['est_total_round1'] !!} </td>
            <td align="right">{!! $totals['est_total_round2'] !!} </td>
            <td align="right">{!! $totals['est_total_round3'] !!} </td>
            <td align="right">{!! $totals['est_total_round4'] !!} </td>
            <td align="right">{!! $totals['est_total_round5'] !!} </td>
            <td align="right">{!! $totals['total_percentage'] !!} </td>
            <td align="right">{!! $totals['close_of_poll'] !!} </td>
          </tr>
        </tfoot>
      <?php } ?>
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