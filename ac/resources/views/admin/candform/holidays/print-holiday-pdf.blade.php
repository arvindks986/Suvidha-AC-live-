<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Holidays</title>
</head>

<body>
    <!--HEADER STARTS HERE-->
    <table style="width:100%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
        <thead>
            <tr>
                <th style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;">
                    <img src="{{ public_path('/admintheme/img/logo/eci-logo.png') }}" alt=""  width="100" border="0"/>
                </th>
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
                            <td><strong>User:</strong> {{ Auth::user()->officername}}--{{Auth::user()->placename}}</td>
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
                <th colspan="3" class="text-center">List Of All Holidays</th>
            </tr>
            <tr>
                <th>Name of Holidays</th>
                <th>Date of Holidays</th>
                <th>Type of Holidays</th>
            </tr>
        </thead>
        <tbody>
            @if(count($final_data)>0)
            @foreach($final_data as $each_data)
            <tr>
                <td>{{ $each_data['title'] }}</td>
                <td>{{ $each_data['datetoshow'] }}</td>
                <td>@php if($each_data['className'] == 'fc-bg-blue'){
                    echo 'Gazetted Holiday';
                }elseif($each_data['className'] == 'fc-bg-lightblue') {
                    echo 'Avoidable Date';
                }elseif($each_data['className'] == 'fc-bg-lightgreen'){
                    echo 'State Holiday';
                }  @endphp
                </td>
            </tr>
            @endforeach
            @else
            <tr>
                <td class="text-center" colspan="3">No Data Found</td>
            </tr>
            @endif
        </tbody>
    </table>
    <table style="width:100%; border-collapse: collapse;" align="center" border="1" cellpadding="5">
        <tbody>
            <tr>
                <td colspan="3" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>
            </tr>
        </tbody>
    </table>
</body>

</html>