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

        <?php // print_r($getDetails); die;?>
        <div class="card-body getpermission">
            <div class="card-header d-flex align-items-center">
                <h4 style="text-align: center;font-size: 20"><img src="{{public_path('img/logo/eci-logo.png')}}" alt="" class="mr-3" style=" max-width: 40px;" />Election Commission Of India</h4>
        </div>
        <hr class="row" />
        <p class="checkedreciept"  style="text-align: center"><img src="{{public_path('img/logo/tick.png')}}" ></p>
        <p style="text-align: center;" id="top">Submission Successful</p>
				<p style="display: block; text-align: center;">Thankyou for submitting your application to the CEO,ECI <br />Your Application Details as Follows</p>
				<br />
				<hr class="row" />
		
            @if(!empty($getDetails))
<!--            @foreach($getDetails as $result)	-->
            <table  border='0' cellspacing='0' width="100%">
                <tr>
                </tr> 

                <tr>
                    <td>Name:</td>
                    <td>
                        <p id='text'>{{$getDetails['name']}}</p>
                    </td>
                </tr>  
                <tr>
                    <td>Address:</td>
                    <td>
                        <p id='text'>{{$getDetails['address']}}</p>
                    </td>
                </tr>
                <tr>
                    <td>Mobile No:</td>
                    <td>
                        <p id='text'>{{$getDetails['mobile']}}</p>
                    </td>
                </tr>
                <tr>
                    <td>Permission Type:</td>
                    <td>
                        <p id='text'>{{$getDetails['permission_name']}}</p>
                    </td>
                </tr>
                <tr>
                    <td>State:</td>
                    <td>
                        <p id='text'>{{$getDetails['ST_NAME']}}</p>
                    </td>
                </tr>
                <tr>
                    <td>District:</td>
                    <td>
                        <p id='text'>{{$getDetails['DIST_NAME']}}</p>
                    </td>
                </tr>
                <tr>
                    <td>AC:</td>
                    <td>
                        <p id='text'>{{$getDetails['AC_NAME']}}</p>
                    </td>
                </tr> 
                <tr>
                    <td>Location:</td>
                    <td>
                        <p id='text'>{{$getDetails['location']}}</p>
                    </td>
                </tr>
                <tr>
                    <td>Date &amp; Timing:</td>
                    <td>
                        <p id='text'>{{$getDetails['date_time_start'] }} {{'to'}} {{ $getDetails['date_time_end']}}</p>
                    </td>
                </tr> 


            </table>
<!--            @endforeach-->
            @endif
            <hr class="row" />
           
        </div>

    </body>

</html>