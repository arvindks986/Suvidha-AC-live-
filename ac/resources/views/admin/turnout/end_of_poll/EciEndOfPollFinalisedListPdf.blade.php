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
        <thead>

          <tr>
            <th colspan="3" class="text-center">{!! $heading_title !!} -
              @if(isset($filter_buttons) && count($filter_buttons)>0)
              @foreach($filter_buttons as $button)
              <?php $but = explode(':', $button); ?>
              <span class="pull-right" style="margin-right: 10px;">
                <span><b>{!! $but[0] !!}:</b></span>
                <span class="badge badge-info">{!! $but[1] !!}</span>

              </span>
              @endforeach
              @endif
            </th>
          </tr>
          <tr>
            <th>State</th>
            <th> AC No - Name </th>
            <th>AC Finalised Status </th>
          </tr>
        </thead>
        <tbody>

          <?php

          $index = 0;


          ?>
          @foreach($results as $result)

          <tr>

            <td><span>{!! $result['label'] !!}</span></td>
            <td>{{$result['const_no'] }} - {{$result['const']}}</td>


            @php if($result['finalized_const'] == 'Yes'){ @endphp
            <td style="color:#008000;">{{$result['finalized_const'] }}</td>
            @php }else{ @endphp
            <td style="color:#FF0000;">{{$result['finalized_const'] }}</td>
            @php } @endphp

          </tr>

          <?php $index++; ?>

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