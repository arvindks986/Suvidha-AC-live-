    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>{!! $heading_title !!}</title>
        <style type="text/css">
        @page {
        header: page-header;
        footer: page-footer;
      }
      .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
          .header_section{
            height: 500px !important;
            width: 100%;
            float: left;
          }
      </style>
    </head>
    <body>
         





<htmlpageheader name="page-header" >
  <div class="header_section">

        <!--HEADER STARTS HERE-->
           <table style="width:100%;padding: 25px 0;" border="0" align="center" cellpadding="5">
               <thead>
                <tr>
                   <th style="width:100%; font-size: 30px;" align="center" >
                        Election Commission of India 
                    </th>
                </tr>
                <tr>
                  <th  style="width: 100%; text-align: center;font-weight: bold;font-size: 20px;">
                    Postal Ballot Declaration Form
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
        <table style="width:100%;" border="0" align="center">  
          
                <tr>
                 <td  style="width:100%;">
                    <table  style="width:100%;padding: 15px 0;">
                      <tbody>
                         <tr>
                          <td colspan="2">
                            <br>
                            State: <b>{!! $st_name !!}</b><br>
                            Election: <b>{!! $election !!}</b>
                            <br>
                          </td>
                        </tr>
                         <tr>  
                           <td>Number & Name of the constituency <b style="min-width: 250px;">{!! $ac_no !!}-{!! $ac_name !!}</b> <br> Date <b>{{ date('d-M-Y h:i a') }}</b></td>
                         </tr>
                      </tbody>
                    </table>  
                 </td>

               </tr>

              
              
            </table>

        </div>
</htmlpageheader>



                       
        <table class="table-strip" style="width: 100%;" border="1" align="center" cellpadding="5">
            <thead>     

        <tr>
         <th>Sr. no.</th>
         <th>Candidate Name</th>
         <th>Party</th>
         <!--  <th>EVM Votes</th> -->
          <th>Postal Votes</th>
         <!-- <th>Total Votes</th> -->
        </tr>
        </thead>
        <tbody>

        <?php echo $table; ?>
       </tbody>
     </table>
      </div>


  <div class="footer_section">
      <table style="width:100%; border-collapse: collapse;" align="center" border="0" cellpadding="15">
          <tbody>
            <tr>
              <td align="left" colspan="2"><br>RO_________________________<br>  <br>  <br>               Observer___________________________
                <br>  
              </td>


            </tr>
          </tbody>
      </table>
  </div>


    </body>
</html>