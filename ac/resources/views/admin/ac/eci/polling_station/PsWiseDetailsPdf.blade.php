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
            <th colspan="12" class="text-center">{!! $heading_title !!}</th>
          </tr>
          <tr>
            <th>Serial No</th>
            <th>PS No</th>
            <th>PS Name</th>
            <th>PS Type</th>
            <th>Electors Male</th>
            <th>Electors Female</th>
            <th>Electors Other</th>
            <th>Electors Total</th>
            <th>Voter Male</th>
            <th>Voter Female</th>
            <th>Voter Other</th>
            <th>Voter Total</th>
          </tr>



        </thead>
        <tbody>

          @php
          $count = 1;

          $TotalElectorMale = 0;
          $TotalElectorFeMale = 0;
          $TotalElectorOther = 0;
          $TotalElector = 0;
          $TotalVoterMale = 0;
          $TotalVoterFeMale = 0;
          $TotalVoterOther = 0;
          $TotalVoter = 0;

          @endphp

          @forelse ($results as $key=>$listdata)

          @php

          $TotalElectorMale +=$listdata->electors_male;
          $TotalElectorFeMale +=$listdata->electors_female;
          $TotalElectorOther +=$listdata->electors_other;
          $TotalElector +=$listdata->electors_total;
          $TotalVoterMale +=$listdata->voter_male;
          $TotalVoterFeMale +=$listdata->voter_female;
          $TotalVoterOther +=$listdata->voter_other;
          $TotalVoter +=$listdata->voter_total;


          @endphp


          <tr>
            <td>{{ $count }}</td>
            <td>{{$listdata->PS_NO }}</td>
            <td>{{$listdata->PS_NAME_EN }}</td>
            <td>{{$listdata->PS_TYPE }}</td>
            <td>{{$listdata->electors_male }}</td>
            <td>{{$listdata->electors_female }}</td>
            <td>{{$listdata->electors_other }}</td>
            <td>{{$listdata->electors_total }}</td>
            <td>{{$listdata->voter_male }}</td>
            <td>{{$listdata->voter_female }}</td>
            <td>{{$listdata->voter_other }}</td>
            <td>{{$listdata->voter_total }}</td>
          </tr>

          @php $count++; @endphp
          @empty
          <tr>
            <td colspan="5">No Data Found For Polling Station</td>
          </tr>
          @endforelse

          <tr>
            <td><b>Total</b></td>
            <td></td>
            <td></td>
            <td></td>
            <td><b>{{$TotalElectorMale}}</b></td>
            <td><b>{{$TotalElectorFeMale}}</b></td>
            <td><b>{{$TotalElectorOther}}</b></td>
            <td><b>{{$TotalElector }}</b></td>
            <td><b>{{$TotalVoterMale}}</b></td>
            <td><b>{{$TotalVoterFeMale}}</b></td>
            <td><b>{{$TotalVoterOther}}</b></td>
            <td><b>{{$TotalVoter}}</b></td>
          </tr>
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