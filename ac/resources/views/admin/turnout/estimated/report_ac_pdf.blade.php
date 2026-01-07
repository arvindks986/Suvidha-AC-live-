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
          <td style="width:50%;"><strong>User:</strong> {{$user_data->placename}}</td>
          <td style="width:50%;" align="right"><strong>Date of Print:</strong> {{ date('d-M-Y h:i a') }}</td>
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
            <th colspan="3" align="left" style="padding-left: 10px"> State </th>
            <th colspan="1">AC No & Name</th>
            <?php /*
        <th align="left">Turnout % (2014)</th>
        */ ?>
            @if(Request::get('round')==1)
            <th colspan="1">Round1 %<br>(Poll Start to 9:00 AM)</th>
            @elseif(Request::get('round')==2)
            <th colspan="1">Round2 %<br>(Poll Start to 11:00 AM)</th>
            @elseif(Request::get('round')==3)
            <th colspan="1">Round3 %<br>(Poll Start to 1:00 PM)</th>
            @elseif(Request::get('round')==4)
            <th colspan="1">Round4 %<br>(Poll Start to 3:00 PM)</th>
            @elseif(Request::get('round')==5)
            <th colspan="1">Round5 %<br>(Poll Start to 5:00 PM)</th>
            @elseif(Request::get('round')==6)
            <th colspan="1">Close Of Poll %</th>
            @else
            <th colspan="1">Round1 %<br>(Poll Start to 9:00 AM)</th>
            <th colspan="1">Round2 %<br>(Poll Start to 11:00 AM)</th>
            <th colspan="1">Round3 %<br>(Poll Start to 1:00 PM)</th>
            <th colspan="1">Round4 %<br>(Poll Start to 3:00 PM)</th>
            <th colspan="1">Round5 %<br>(Poll Start to 5:00 PM)</th>
            <th colspan="1">Close Of Poll %</th>
            <th colspan="1">Latest Updated Poll %</th>
            @endif
            <?php /*
         <th colspan="1">Change from 2014</th>
         */ ?>
          </tr>


        </thead>
        <tbody>
          @foreach($results as $result)
          <tr>
            <td colspan="3" align="left" style="padding-left: 10px">

              <span>{!! $result['label'] !!}</span>
            </td>

            <td>
              {{$result['const_no'] }}-{{$result['const'] }}
            </td>
            <?php /*
         <td>
        {{ $result['old_total_percentage'] }}
         </td>
         */ ?>
            @if(Request::get('round')==1)
            <td>
              {{ $result['est_total_round1'] }}
            </td>
            @elseif(Request::get('round')==2)
            <td>
              {{$result['est_total_round2'] }}
            </td>
            @elseif(Request::get('round')==3)
            <td>
              {{$result['est_total_round3'] }}
            </td>
            @elseif(Request::get('round')==4)
            <td>
              {{$result['est_total_round4'] }}
            </td>
            @elseif(Request::get('round')==5)
            <td>
              {{$result['est_total_round5'] }}
            </td>
            @elseif(Request::get('round')==6)
            <td>
              {{$result['close_of_poll'] }}
            </td>
            @else
            <td>
              {{ $result['est_total_round1'] }}
            </td>
            <td>
              {{$result['est_total_round2'] }}
            </td>
            <td>
              {{$result['est_total_round3'] }}
            </td>
            <td>
              {{$result['est_total_round4'] }}
            </td>

            <td>
              {{$result['est_total_round5'] }}
            </td>
            <td>
              {{$result['close_of_poll'] }}
            </td>
            <td>
              {{$result['est_total'] }}
            </td>
            @endif

            <?php /*
         <td>
        {{$result['difference'] }}
         </td>
         */ ?>

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