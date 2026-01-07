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
            <th colspan="8" class="text-center">{!! $heading_title !!}</th>
          </tr>
          <tr>
            <th colspan="8" class="text-center">
              <h4 style="color:#FF0000;">The percentage is calculated on the basis of number of voters entered by RO after End of Poll*.</h4>
          </tr>
          <tr>
            <th colspan="2">State</th>
            <th colspan="2">AC No & Name</th>
            <th colspan="2">Percentage %</th>
            <th colspan="2">Finalized By RO</th>
          </tr>

        </thead>
        <tbody>

          @foreach($results as $result)

          <tr>
            <td colspan="2">{{$result['label'] }}</td>
            <td colspan="2">{{$result['ac_no'] }}-{{$result['ac_name'] }}</td>
            <td colspan="2">{!! $result['total_percentage'] !!} </td>

            @php if($result['finalized_const'] == 'Yes'){ @endphp
            <td colspan="2" style="color:#008000;">{{$result['finalized_const'] }}</td>
            @php }else{ @endphp
            <td colspan="2" style="color:#FF0000;">{{$result['finalized_const'] }}</td>
            @php } @endphp

          </tr>


          @endforeach
          <?php if (isset($totals)) { ?>
            <tr>
              <td colspan="2">{!! $totals['label'] !!}</td>
              <td colspan="2"></td>
              <td colspan="2">{!! $totals['total_percentage'] !!}</td>
              <td colspan="2"></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
      <table style="width:100%; border-collapse: collapse;" align="center" border="1" cellpadding="5">
        <tbody>
          <tr>
            <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>
          </tr>
          <tr>
            <td style="color:orange;" colspan="2">* This is approximate trend as data from some Polling Stations(PS) takes time. Final data for each PS is shared in Form 17C with all Polling Agents.</td>
          </tr>
        </tbody>
      </table>
    </body>

    </html>