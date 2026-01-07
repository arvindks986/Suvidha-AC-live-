<!DOCTYPE html>
<html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link href="https://fonts.googleapis.com/css2?family=Hindi&display=swap" rel="stylesheet">
        <style type="text/css">
            td {
                color:#6c6a6a;
                font-size:16px!important;
            }
            #text{
                color:#000;
            }
            #top{
                color: green;
            }
        </style>
    </head>
    <body  style="font-family: freesans">

        
        <div class="card-body getpermission">
            <div class="card-header d-flex align-items-center">
                <h4 style="text-align: center;font-size: 20"><img src="{{public_path('img/logo/eci-logo.png')}}" alt="" class="mr-3" style=" max-width: 40px;" />Election Commission Of India</h4>
        </div>
        <hr class="row" />
        <p class="checkedreciept"  style="text-align: center"><img src="{{public_path('img/logo/tick.png')}}" ></p>
        <p style="text-align: center;" id="top">Submission Successful</p>
				<p style="display: block; text-align: center;">Thankyou for submitting your application <br />Your Application Details as Follows</p>
				<br />
				<hr class="row" />
		
            @if(!empty($getDetails))
            @foreach($getDetails as $result)	
            <table  border='0' cellspacing='0' width="100%">
                <tr>
                    <td>Reference ID:</td>
                    <td>
                        @if(!empty($result->reference_id))
                        <p id='text'>{{$result->reference_id}}</p>
                        @else
                        <p id='text'>{{$result->permission_id}}</p>
                        @endif
                    </td>
                </tr> 

                <tr>
                    <td>Name:</td>
                    <td>
                        <p id='text'>{{$result->name}}</p>
                    </td>
                </tr>  
                <tr>
                    <td>Address:</td>
                    <td>
                        <p id='text'>{{$result->address}}</p>
                    </td>
                </tr>
                <tr>
                    <td>Mobile No:</td>
                    <td>
                        <p id='text'>{{$result->mobile}}</p>
                    </td>
                </tr>
                <tr>
                    <td>Permission Type:</td>
                    <td>
                        <p id='text'>{{$result->pname}}</p>
                    </td>
                </tr>
                <tr>
                    <td>Party Name:</td>
                    <td>
                        <p id='text'>{{$result->PARTYNAME}}</p>
                    </td>
                </tr>
                <tr>
                    <td>State:</td>
                    <td>
                        <p id='text'>{{$result->ST_NAME}}</p>
                    </td>
                </tr>
                @if(!empty($result->DIST_NAME))
                <tr>
                    <td>District:</td>
                    <td>
                        <p id='text'>{{$result->DIST_NAME}}</p>
                    </td>
                </tr>
                @endif
                @if(!empty($result->AC_NAME))
                <tr>
                    <td>AC:</td>
                    <td>
                        <p id='text'>{{$result->AC_NAME}}</p>
                    </td>
                </tr>
                @endif
                <tr>
                    <td>Location:</td>
                    <td>
                        @if(!empty($result->location_name))
                            <p id='text'>{{$result->location_name}}</p>
                        @else
                            <p id='text'>{{$result->Other_location}}</p>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Date &amp; Timing:</td>
                    <td>
                        <p id='text'>{{$result->date_time_start }} {{'to'}} {{ $result->date_time_end}}</p>
                    </td>
                </tr> 


            </table>
            @endforeach
            @endif
            <hr class="row" />
            @if(!empty($getRodetails))
            @foreach($getRodetails as $RO)
            <table border='0' cellspacing='0' width="100%">
                @if($RO->ro_cancel_status == 0)
                <tr>
                    <td>CEO Comment</td>
                    <td>
                        <p id='text'>{{$RO->comment}}</p>
                    </td>
                </tr>
                <tr>
                    <td>Approved Status</td>
                    @if($RO->approved_status == 2)
                    <td>
                        <p style="color:green">{{'Approve'}}</p>
                    </td>
                    @elseif($RO->approved_status == 1)
                    <td>
                        <p style="color:yellow">{{'Inprogress'}}</p>
                    </td>
                    @elseif($RO->approved_status == 3)
                    <td>
                        <p style="color:red">{{'Rejected'}}</p>
                    </td>
                    @else
                    <td>
                        <p style="color:red">{{'pending'}}</p>
                    </td>
                    @endif
                </tr> 
                @else
                 <tr>
                    <td>CEO Comment</td>
                    <td>
                        <p id='text'>{{$RO->comment}}</p>
                    </td>
                </tr>
                <tr>
                    @if($RO->ro_cancel_status == 0)
                    <td>Approved Status</td>
                    @if($RO->approved_status == 2)
                    <td>
                        <p style="color:green">{{'Approve'}}</p>
                    </td>
                    @elseif($RO->approved_status == 1)
                    <td>
                        <p style="color:yellow">{{'Inprogress'}}</p>
                    </td>
                    @else
                    <td>
                        <p style="color:red">{{'Rejected'}}</p>
                    </td>
                    @endif
                    @else
                    <td>Approved Status</td>
                    <td>
                        <p style="color:red">{{'Cancelled'}}</p>
                    </td>
                    @endif
                </tr> 
                
                @endif
            </table>
            @endforeach
            @endif
           
        </div>

    </body>

</html>