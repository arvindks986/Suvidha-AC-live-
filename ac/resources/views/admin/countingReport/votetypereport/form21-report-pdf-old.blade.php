<!DOCTYPE html>
<html><head>
    <title>Election Commission Of India:: Form 21 E</title>
    
    <style>
        table{}
        .table1 td{text-align:center;}
    </style>
    </head>
<body style="
    width: 800px;
    text-align: center;
    margin: 0 auto;
">

<table class="table1" width="100%">
	<tbody><tr>
		<td style="  width: 50%;  text-align: left;">(G.P.V) Y-165-1,350-10-2007-A4*</td><td></td></tr>
		<tr><td colspan="2"><h1>FORM-21E</h1></td></tr>
		<tr><td colspan="2" style="font-size:18px;">Return of Election</td></tr>
		<tr><td colspan="2" style="font-style:italic;">[See rule 64 of the Conduct of Elections Rules, 1961]</td></tr>
                <tr><td colspan="2">Election to the <span><b><?php echo $state;?></b></span> From the <span></span><span><b><?php echo $pcname;?></b></span>&nbsp;&nbsp;Constituency</td></tr>
		<tr><td colspan="2"><h1 style="font-size:20px;padding-top: 5px;">RETURN OF ELECTION</h1></td></tr>
</tbody></table>
<table border="1" width="100%" cellspacing="0" cellpadding="6">
<tbody><tr>
</tr></tbody><thead>
	<tr><th>Serial No.</th>
	<th>Name of Candidate</th>
	<th>Party Affiliation</th>
	<th>Number of votes polled</th>
	</tr></thead>
	<tbody>
                @php $i=0;
                    @endphp
                    @if(count($array)>0)
                    @foreach($array as $data)
                    @php $i++;
                    @endphp
                            <tr>
                        <td>{{$i}}</td>
                        <td>{{$data['candidate_name']}}</td>
                        <td>{{$data['party_name']}}</td>
                        <td>{{$data['total_vote']}}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr><td colspan="6" style="text-align:center">-- No Record Available --</td></tr>
                    @endif
                              </tbody>

</table>

<table style="
    text-align: left;
    width: 100%;
">
	<tbody><tr><td style="
    width: 22%;
"></td><td>Total numbers of electors - <?php echo number_format($tot_electrol);?><span style="
    width: 100%;
    border-bottom: 1px solid #d5d5d5;
"></span></td></tr>
	<tr><td></td><td><span class="text">Total numbers of valid votes polled - <?php echo number_format($total_validpol);?></span><span class="line"></span></td></tr>
	<tr><td></td><td><span class="text">Total numbers of rejected votes</span><span class="line"></span></td></tr>
	<tr><td></td><td><span class="text">Total numbers of tendered votes</span><span class="line"></span></td></tr>
	<tr><td></td><td><span class="text">I declare that:-</span><span class="line"></span></td></tr>
	<tr><td colspan="2"><span class="line"></span>(Name)</td></tr>
	<tr><td colspan="2"><span class="text">of</span><span class="line"></span><span class="text">(address)</span></td></tr>
	<tr><td colspan="2"><span class="text">has been duly elected to fill the seat</span></td></tr>
	
</tbody></table>
<table>
	<tbody><tr>
                <td><span class="text">Place:-</span> <span class="line"></span></td>
		<td></td>
	</tr>
	<tr>
                 <?php  date_default_timezone_set("Asia/Calcutta");  ?>
		<td><span class="text">Date:-</span><span>{{ date('d-m-Y H:i:s') }}</span></td>
		<td>Returning Officer</td>
	</tr>
</tbody></table>


</body></html>



