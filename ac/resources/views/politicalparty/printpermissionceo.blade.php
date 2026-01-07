<html lang="en" style="background: #e7e7e7;">
 <head>
    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link href="https://fonts.googleapis.com/css2?family=Hindi&display=swap" rel="stylesheet">
<style type="text/css">

table{}
	tr{
    height: 30px;
}
	td{
    border-bottom: 1px solid #fcf9f9;
}
	td:first-child{
    font-size: 14px;
    padding: 10px;
    color: #706e6e;
}
	td:last-child{
    font-size: 14px;
    background: #f2f2f282;
    padding-left: 15px;
}
</style>
</head>
	<body style="font-family: freesans">
		<main>
			<table style="
    width: 600px;
    /* border: 1px solid #d5d5d5; */
    margin: 0 auto;
    padding: 10px;
    background: #fff;
    box-shadow: 1px 1px 6px 1px #bcbcbc;
">
				<tbody><tr style="
    margin: 0 auto;
    text-align: center;
">
					<td colspan="2" style="
    background: #fff;
    border-bottom: none;
"><img src="{{ public_path('theme/img/logo/eci-logo1.png') }}" style="max-width: 222px;" alt=""> <br><h2>Election Commission of India</h2></td>
</tr>
<tr style="
    margin: 0 auto;
   
">
					<td colspan="2" style="
    padding: 0;
    border-bottom: none;
    background: #fff;
"><h3 style="
    /* border-bottom: 1px solid #d5d5d5; */
    /* padding-bottom: 6px; */
    font-size: 18px;
    vertical-align: middle;
    margin: 0;
    background: #fe8505;
    padding: 10px;
    color: #fff;
    border-radius: 34px;
    text-align: center;
    margin-bottom: 19px;
    text-transform: uppercase;
    letter-spacing: 2px;
">Permission Details</h3></td>
@if(!empty($result))
              @foreach($result as $res) 
				</tr>
				<tr>
					<td>Reference Number</td>
					<td>{{$res->reference_id}}</td>
				</tr>
				<tr>
					<td>Name</td>
					<td>{{$res->name}}</td>
				</tr>
				
				<tr>
					<td>Mobile Number</td>
					<td>{{$res->mobileno}}</td>
				</tr>
				<tr>
					<td>Email</td>
					<td>{{$res->email}}</td>
				</tr>	
				<tr>
					<td>State</td>
					<td>{{$res->ST_NAME}}</td>
				</tr>	
				 
				
				<tr>
					<td>Permission Type</td>
					<td>{{$res->permission_name}}</td>
				</tr>
				
				<tr>
					<td>Location</td>
					 @if(!empty($res->location_name))
                        <td>{{$res->location_name}}</td>
                        @else
                        <td>{{$res->Other_location}}</td>
                        @endif
					
				</tr>
				<tr>
					<td>Date &amp; Timing</td>
					<td>{{GetReadableDateForm($res->date_time_start)}} to {{GetReadableDateForm($res->date_time_end)}}</td>
				</tr>
				<tr>
					<td>Application Status</td>
					@if(($res->approved_status)==0 && ($res->cancel_status)!=1) 
                      <td>Pending</td>
                      @elseif(($res->approved_status)==2 && ($res->cancel_status)!=1)
                      <td>Accepted</td>
                      @elseif(($res->approved_status)==1 && ($res->cancel_status)!=1)
                      <td>In Progress </td>
                      @elseif(($res->approved_status)==3 && ($res->cancel_status)!=1)
                      <td>Reject</td>
                      @elseif(($res->cancel_status)==1)
                      <td>Cancelled </td>
                      @endif
				</tr>
				<tr>
					<td>Comment</td>
					
                    @if(count($pdf)>0)
                      @foreach($pdf as $comment)
                          @if($comment->ro_cancel_status == 0)
                            <td>{{$comment->comment}}</td>
                          @endif
                      @endforeach
                    @endif
				</tr>
				<tr>
					<td>Cancelation Comment</td>
                    @if(count($pdf)>0)
                      @foreach($pdf as $comment)
                          @if($comment->ro_cancel_status == 1)
                            <td>{{$comment->comment}}</td>
                          @endif
                      @endforeach
                    @endif

				</tr>


				@endforeach
				@endif
			</tbody></table>

		</main>
	
</body></html>
