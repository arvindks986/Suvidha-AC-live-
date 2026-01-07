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
            <td align="center" style="vertical-align: middle;height: 70px;"><strong>Voter Turn Out - {!! $number_of_voting !!}%</strong></td>
          </tr>
        </tbody>
      </table>



      <table class="table-strip" style="width: 100%;" border="1" align="center">
        <thead>

          <tr>
            <th colspan="11" class="text-center">{!! $heading_title !!}</th>
          </tr>
          <tr>
            <th rowspan="2">State</th>
            <th colspan="4">Electors</th>
            <th colspan="4">Voters</th>
            <th rowspan="2">Percentage %</th>
          </tr>

          <tr>
            <th>Male</th>
            <th>Female</th>
            <th>Other</th>
            <th>Total</th>

            <th>Male</th>
            <th>Female</th>
            <th>Other</th>
            <th>Total</th>

          </tr>


        </thead>
        <tbody>

          <?php $index = 0; ?>
          @foreach($results as $result)





          <tr>


            <td>{{$result['label'] }}</td>



            <td>{{$result['old_total_male'] }}</td>

            <td>{{$result['old_total_female'] }}</td>

            <td>{{$result['old_total_other'] }}</td>

            <td>{{$result['old_total'] }}</td>

            <td>{{$result['total_male'] }}</td>

            <td>{{$result['total_female'] }}</td>

            <td>{{$result['total_other'] }}</td>

            <td>{{$result['total'] }}</td>


            <td><span>{!! $result['total_percentage'] !!}</span> </td>

          </tr>


          @endforeach
          <?php if (isset($totals)) { ?>
            <tr>
              <td>{!! $totals['label'] !!}</td>

              <td>{{$totals['old_total_male'] }}</td>

              <td>{{$totals['old_total_female'] }}</td>

              <td>{{$totals['old_total_other'] }}</td>

              <td>{{$totals['old_total'] }}</td>

              <td>{{$totals['total_male'] }}</td>

              <td>{{$totals['total_female'] }}</td>

              <td>{{$totals['total_other'] }}</td>

              <td>{{$totals['total'] }}</td>

              <td>{!! $totals['total_percentage'] !!}</td>
            </tr>
          <?php } ?>
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