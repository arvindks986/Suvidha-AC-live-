 @extends('layouts.theme')
 @section('title', 'Permission')

 @section('content')
 <style type="text/css">
   .checkedreciept {
     text-align: center;
     margin: 5px auto 10px auto !important;
     text-transform: uppercase;
     color: green !important;
   }

   .checkedreciept .fa {
     font-size: 50px;
     margin: 0;
     border: 2px solid green;
     border-radius: 50%;
     padding: 14px;
     color: green;
   }
 </style>

 <main id="date_time_start" role="main" class="inner cover mb-3 mb-auto">
   <div class="container">
     <div class="row">

     </div>
   </div>

   <section class="mt-5" id="wrapper">
     <div class="container">

       <div class="well reciept">
         <div class="row">
           <div class="col-lg-12">
             <div class="card" id="thisok">

               <table id="example" class="table table-striped table-bordered" style="width:100%">
                 <thead>
                   <div class="card-header d-flex align-items-center">
                     <h4><img src="{{ asset('theme/img/logo/eci-logo1.png')}}" alt="" class="mr-3"
                         style=" max-width: 40px;" />Election Commission Of India</h4>
                   </div>
                   <div class="card-body getpermission">
                     <p class="checkedreciept"><i class="fa fa-check"></i> <br />
                       Submission Successful
                     </p>
                     <small class="text-center" style="display: block;">Thankyou for submitting your
                       application. <br />Your Application Details as Follows</small>
                     <br />

                 </thead>

                 @forelse ($detaildata as $user)
                 <tbody>
                   <tr>
                     <td>Reference Number</td>
                     <td>{{$user->reference_id}}</td>
                   </tr>
                   <tr>
                     <td>Name</td>
                     <td>{{ $user->name }}</td>
                   </tr>
                   <tr>
                     <td>Email</td>
                     <td>{{ $user->email }}</td>
                   </tr>
                   <tr>
                     <td>Mobile Number</td>
                     <td>{{ $user->mobileno }}</td>
                   </tr>
                   <tr>
                     <td>Gender</td>
                     <td>{{ $user->gender }}</td>
                   </tr>
                   <tr>
                     <td>Date Of Birth</td>
                     <td>{{ date('d-m-Y', strtotime($user->dob)) }}</td>
                   </tr>
                   <tr>
                     <td>State</td>
                     <td>{{ $user->ST_NAME }}</td>
                   </tr>
                   @if(isset($user->DIST_NAME))
                   <tr>
                     <td>District</td>
                     <td>{{ $user->DIST_NAME }}</td>
                   </tr>
                   @else

                   @endif
                   <tr>
                     <td>Permission Type</td>
                     <td>{{ $user->permission_name }}</td>
                   </tr>
                   <tr>
                     <td>Event Place</td>
                     @if($user->location_id != "other" && $user->location_id != 0)
                     <td>{{ $user->location_name }}</td>
                     @else
                     <td>{{ $user->Other_location }}</td>
                     @endif
                   </tr>
                   <tr>
                     <td>Event Start Date & Time</td>
                     <td>{{ GetReadableDateForm($user->date_time_start) }}</td>
                   </tr>
                   <tr>
                     <td>Event End Date & Time</td>
                     <td>{{ GetReadableDateForm($user->date_time_end) }}</td>
                   </tr>
                   <tr>
                     <td></td>
                     <td><input type="button" class="btn btn-primary" value="Print"
                         onclick="myFunction()"></td>



                   </tr>


                 </tbody>
                 @empty
                 <p>No users</p>
                 @endforelse
               </table>







             </div>
           </div>
         </div>
       </div>
   </section>
 </main>
 @endsection

 @section('script')
 <script>
   function myFunction() {

     // alert('ok');

     var divToPrint = document.getElementById('thisok');
     var popupWin = window.open('', '_blank', 'width=1100,height=1100');
     popupWin.document.open();
     popupWin.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + '</html>');
     popupWin.document.close();
   }
 </script>
 @endsection