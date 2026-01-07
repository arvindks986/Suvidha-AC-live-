<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Candidate Wise Report</title>   
    </head>
    <body>
        <?php ini_set("pcre.backtrack_limit", "50000000");?>
        <!--HEADER STARTS HERE-->
        <table style="width:100%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
            <thead>
                <tr>
                    <th  style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="{{ public_path('/admintheme/img/logo/eci-logo.png') }}" alt=""  width="100" border="0"/></th>
                    <th  style="width:50%" align="right" style="border-bottom: 1px dotted #d7d7d7;">
                        SECRETARIAT OF THE<br>
                        ELECTION COMMISSION OF INDIA<br>
                        Nirvachan Sadan, Ashoka Road, New Delhi-110001<br>  
                    </th>
                </tr>
            </thead>
        </table>
        <!--HEADER ENDS HERE-->
        <style type="text/css">
            .table-strip{border-collapse: collapse;}
            .table-strip th,.table-strip td{text-align: center;}
            .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
        </style>
        <table style="width:100%; border: 1px solid #000;" border="0" align="center">  

            <tr>
                <td  style="width:50%;">
                    <table  style="width:100%">
                        <tbody>

                            <tr>  
                                <td><strong>User:</strong> {{$user_data->placename}}</td>
                            </tr>
                        </tbody>
                    </table>  
                </td>
                <td  style="width:50%">
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
                    <td align="center" ><strong>Candidate Wise Report</strong></td>
                </tr>

            </tbody>
        </table>

        <table class="table-strip" style="width: 100%;" border="1" align="center">
            <thead>
                <tr>
                    <th>SL No</th>
                    <th>State Name</th>
                    <th>AC Name</th>
                    <th>Candidate Name</th> 
                    <th>Party Name</th> 
                    <th>EVM Vote</th> 
                    <th>Postal Vote</th> 
                    <th>Total Vote</th> 
                </tr>
            </thead>
            <tbody>
                @php $i=0;
                @endphp
                @if(count($array)>0)
                @foreach($array as $data)
                @php $i++;
                @endphp
                        <tr>
                    <td>{{$i}}</td>
                    <td>{{$data['state_name'] }}</td>
                    <td>{{$data['ac_name'] }}</td>
                    <td>{{$data['candidate_name']}}</td>
                    <td>{{$data['party_name']}}</td>
                    <td>{{$data['evm_vote']}}</td>
                    <td>{{$data['postal_vote']}}</td>
                    <td>{{$data['total_vote']}}</td>
                </tr>
                @endforeach
                @else
                <tr><td colspan="6" style="text-align:center">-- No Record Available --</td></tr>
                @endif

            </tbody></table>
        <table style="width:100%; border-collapse: collapse;" align="center" border="1" cellpadding="5">
            <tbody>
                <tr>
                    <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>  
                </tr>
            </tbody>
        </table>
    </body>
</html>

