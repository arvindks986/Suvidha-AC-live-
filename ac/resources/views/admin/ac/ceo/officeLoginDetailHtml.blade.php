    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Officer Login Detail</title>
        <!--HEADER STARTS HERE-->
           
        <!--HEADER ENDS HERE-->
      <style type="text/css">
          .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
      </style>
    </head>
    <body>
       <?php  $st=getstatebystatecode($st_code);  
       date_default_timezone_set('Asia/Kolkata'); 
  ?> 

   <table style="width:98%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
               <thead>
                <tr>
                    <th  style="width:49%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="<?php echo url('/'); ?>/admintheme/images/logo/eci-logo.png" alt=""  width="100" border="0"/></th>
                    <th  style="width:49%" align="right" style="border-bottom: 1px dotted #d7d7d7;">
                        SECRETARIAT OF THE<br> 
                        ELECTION COMMISSION OF INDIA<br>
                        Nirvachan Sadan, Ashoka Road, New Delhi-110001<br>  
                    </th>
                </tr>
              </thead>
            </table>
        <table style="width:98%; border: 1px solid #000;" border="0" align="center">  
                <tr>
                 <td  style="width:49%;">
                    <table  style="width:100%">ST_NAME
                      <tbody>
                         <tr>
                           <td><strong>Officer Login Details</strong></td>
                         </tr>
                         <tr>  
                           <td><strong>State:</strong> {{$st->ST_NAME}}</td>
                         </tr>
                  
                         
                      </tbody>
                    </table>  
                 </td>
                 <td  style="width:49%">
                  <table style="width:100%">
                      <tbody>
                         <tr>
                           <td align="right"><strong>Date of Print:</strong> {{ date('d.m.Y h:i a') }}</td>
                         </tr>
                         
                        
                         <tr>  
                           <td align="right">&nbsp;</td>
                         </tr> 
                      </tbody>
                    </table>
                 </td>
               </tr>
               <
            </table>
            <?php $i=0;    $totalrg=0; $totalwg=0; $totalaccg=0;  $totalg=0; ?>
        <table class="table-strip" style="width: 98%;" border="1" align="center">
             <thead>
        <tr>
          <th>Serial No</th>
           <th>User Id</th>
           <th>Desigation</th> 
           <th>Name</th>
           <th>State</th>
           <th>Officer Level</th>
           <th>Mobile Number</th>
           <th>AC</th>
           <th>PC</th>
            <th>Passsword</th>
  
        </tr>
        </thead>
        <tbody>
        <?php $count = 1; 
       
       if(count($allUsers)>1){ ?>
       
         @foreach($allUsers as $allUser)
         <?php $pcDetails=getpcbypcno($allUser->st_code,$allUser->pc_no); 
         $acDetails =getacbyacno($allUser->st_code,$allUser->ac_no);
         //print_r($acDetails);
         ?>
         <?php  $st=getstatebystatecode($allUser->st_code);   ?> 
          <tr>
            <td>{{$count}}</td>  
            <td >{{ $allUser->officername}}</td>
            <td >{{ $allUser->designation }}</td>
            <td >{{ $allUser->name }}</td>
            <td >{{ $allUser->st_code .'-' .$st->ST_NAME}}</td>
            <td >{{ $allUser->officerlevel}}</td>
            <td >{{ $allUser->Phone_no}}</td>
            <td >@if(isset($pcDetails)) {{$allUser->pc_no.'-'.$pcDetails->PC_NAME}}@endif</td>
            <td >@if(isset($acDetails)) {{$allUser->ac_no.'-'.$acDetails->AC_NAME}}@endif</td>
            <td>demo@1234</td>
          </tr>
          <?php $count++ ?>
          @endforeach
          <?php } else { ?>
          <tr>
            <td class="col-md-6" colspan='6'> <p>No Records  Founds </p></td>
          </tr>   
          <?php }  ?>
        </tbody>
        </table>
      <table style="width:98%; border-collapse: collapse;" align="center" border="1" cellpadding="5">
          <tbody>
            <tr>
              <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>  
            </tr>
          </tbody>
      </table>
    </body>
</html>