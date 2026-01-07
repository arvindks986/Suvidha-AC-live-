<!DOCTYPE html>
<html lang="en">
 <?php   $url = URL::to("/");  ?>
    <head>
        <meta charset="utf-8">
        <title>{!! $heading_title !!}</title>
       <style type="text/css">         
            html,body{ font-size:14px; font-weight:bold;  margin:0; overflow-x:hidden; }        
           * {-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%}
         p{ line-height:30px; }
         ul { line-height:25px; word-spacing:2px; padding:5px; }

      </style>
      
    </head>
    <body>
        <!--HEADER STARTS HERE-->
        <p align="right" class="text-right"> <small style="font-size:10px;"> Encore Audit Ref.:-  {!!$ref_no!!} </small></p>
            <table style="width:100%;" border="0" align="center" cellpadding="5">
               <thead>
                <tr>
                    <th  style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="<?php echo url('/'); ?>/theme/img/logo/eci-logo1.png" alt=""  width="100" border="0"/></th>
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
        <table style="width:100%; border:0px solid #000;" border="0" align="center">  
                <tr>
                 <td  style="width:100%;">
                    <table  style="width:100%" align="center">
                      <tbody>
                         <tr> <td align="center"><h1>DATA CORRECTNESS CERTIFICATE</h1></td> </tr>
                       </tbody>
                    </table>  
                 </td>
                  
               </tr>
              
            </table>
         <table style="width:90%; text-align:left; font-size:14px; font-weight:bold;" border="0" align="center" cellpadding="5" cellspacing="0" >
                <thead>
                  <tr> <td align="left" width="50"> To,</td><td align="center">&nbsp;</td></tr> 
                   <tr> <td align="left">&nbsp;</td><td align="left">Chief Electoral Officer</td></tr> 
                    <tr> <td align="left">&nbsp;</td><td align="left">{{$st_name}}</td></tr> 
                       
                   <tr style="height:40px;"><td>&nbsp;</td><td>&nbsp;</td></tr>
             </thead> 
             <tbody>  
             <tr> <td  style="text-align: justify;" colspan="2"> 
              <p>I certify that all data entered in ENCORE is correct and verified.</p>
              <p>The following points shall specifically be ensured:-</p></td></tr>
              <tr> <td>&nbsp;</td>
               <td  style="text-align: justify;">  
                <ul >
                <li style="margin-left:20px;"> Candidate sequence is as per Form 7A. </li>
                <li style="margin-left:20px;"> Candidate names are as per Form 7A and correct both in English and Hindi.</li>
                <li style="margin-left:20px;"> Candidate address is correct.</li>
                <li style="margin-left:20px;"> Party name in English, Hindi and symbol allotted is correct.</li>
                <li style="margin-left:20px;"> Electors count is correct </li>
                <li style="margin-left:20px;"> Voter count is correct </li>
                <li style="margin-left:20px;"> All the data entered in ENCORE is correct and verified.</li>
                </ul>

             </td>  </tr>   
             
            </tbody>
          </table> 
     <table style="width:100%; border-collapse: collapse; margin-top:20px;" align="center" border="0" cellpadding="5">
                  <tbody>
                    <tr style="height: 70px"><td colspan="4">&nbsp;</td></tr>
                    <tr> <td align="left">Place:- {{strtoupper($ac_name)}}</td> 
                          <td align="right">  RETURNING OFFICER </td> </tr>
                    <tr> <td align="left">Date:-{{$print_date}}</td><td align="right"> {{$ac_no}}-{{$ac_name}}  </td> </tr>
                  </tbody>
              </table>
    </body>
</html>