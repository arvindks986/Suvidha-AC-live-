<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link href="https://fonts.googleapis.com/css2?family=Hindi&display=swap" rel="stylesheet">
    <body   style="font-family: freesans">

        <div class="card-header d-flex align-items-center">
            <h4><img src="{{public_path('img/logo/eci-logo.png')}}" alt="" class="mr-3" style=" max-width: 40px;" />Election Commission Of India</h4>
        </div>
        <div class="card-body getpermission">
            <p class="checkedreciept"><i class="fa fa-check"></i> <br />
                Permission Details
            </p>
            <hr class="row" />
            <br />
            @if(!empty($getDetails))
            @foreach($getDetails as $result)	
            <table class="form-horizontal" border='1' cellspacing='0'>
                <tr>
                    <td>PermissionId</td>
                    <td>
                        <p>{{$result->id}}</p>
                    </td>
                </tr> 

                <tr>
                    <td>Name</td>
                    <td>
                        <p>{{$result->name}}</p>
                    </td>
                </tr>  
                <tr>
                    <td>Address</td>
                    <td>
                        <p>{{$result->address}}</p>
                    </td>
                </tr>
                <tr>
                    <td>Mobile No</td>
                    <td>
                        <p>{{$result->mobile}}</p>
                    </td>
                </tr>
                <tr>
                    <td>Permission Type</td>
                    <td>
                        <p>{{$result->pname}}</p>
                    </td>
                </tr>
                <tr>
                    <td>State</td>
                    <td>
                        <p>{{$result->ST_NAME}}</p>
                    </td>
                </tr>
                <tr>
                    <td>District</td>
                    <td>
                        <p>{{$result->DIST_NAME}}</p>
                    </td>
                </tr>
                <tr>
                    <td>AC</td>
                    <td>
                        <p>{{$result->AC_NAME}}</p>
                    </td>
                </tr> 
                <tr>
                    <td>Location</td>
                    <td>
                        <p>{{$result->name}}</p>
                    </td>
                </tr>
                <tr>
                    <td>Date &amp; Timing</td>
                    <td>
                        <p>{{$result->date_time_start }} {{'to'}} {{ $result->date_time_end}}</p>
                    </td>
                </tr> 


            </table>
            @endforeach
            @endif
            <hr class="row" />
            @if(!empty($getNodaldetails))
@foreach($getNodaldetails as $nodal)
            <table>
                <tr>
                    <td>Nodal Name</td>
                    <td>
                        <p>{{$nodal->name}}</p>
                    </td>
                </tr>
                <tr>
                    <td>Authority Type</td>
                    <td>
                        <p>{{$nodal->auth_name}}</p>
                    </td>
                </tr>
                <tr>
                    <td>Approved Status</td>
                    @if($nodal->accept_status == 1)
                    <td>
                        <p>{{'Accept'}}</p>
                    </td>
                    @else
                    <td>
                        <p>{{'Pending'}}</p>
                    </td>
                    @endif
                </tr>
                <tr>
                    <td>Document Uploded by Nodal</td>
                    @if(!empty($nodal->file) && $nodal->file != 'NULL')
                    <td>
                         <a href="{{asset('uploads/Nodal-Uploaddocument')}}/{{$nodal->permission_request_id}}/{{$nodal->file}}" download >{{$nodal->file}}</a>
                    </td>
                    @else
                    <p>{{'Nill'}}</p>
                    @endif
                </tr>
            </table>
<br>
@endforeach
@endif
            <hr class="row" />
            @if(!empty($getRodetails))
            @foreach($getRodetails as $RO)
            <table>
                <tr>
                    <td>Ro Comment</td>
                    <td>
                        <p>{{$RO->comment}}</p>
                    </td>
                </tr>
                <tr>
                    <td>Approved Status</td>
                    @if($RO->approved_status == 2)
                    <td>
                        <p>{{'Approve'}}</p>
                    </td>
                    @else
                    <td>
                        <p>{{'pending'}}</p>
                    </td>
                    @endif
                </tr> 
                <tr>
                    <td>Document Uploded by RO</td>
                    @if(!empty($RO->file) && $RO->file != 'NULL')
                    <td>
                        <a href="{{asset('uploads/RO-Uploaddocument')}}/{{$RO->permission_request_id}}/{{$RO->file}}" download >{{$RO->file}}</a>
                    </td>
                    @else
                    <td>
                        <p>Nill</p>
                    </td>
                    @endif
                </tr>
            </table>
            @endforeach
            @endif
        </div>

    </body>

</html>