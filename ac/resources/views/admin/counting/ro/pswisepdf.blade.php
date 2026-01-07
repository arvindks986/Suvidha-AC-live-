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
            height:400px !important;
            width: 100%;
            float: left;
          }
          .small{
            display: none;
          }
      </style>
    </head>
    <body>

<htmlpageheader name="page-header" >
  <div class="header_section">
    <p align="center" class="text-center"> <span style="font-size:20px;font-weight:bold;color:blue;"> (Preview)</span></p>
    <p align="right" class="text-right"> <small style="font-size:10px;"> Encore Audit Ref.:-  {!!$ref_no!!} </small></p>
         <!--HEADER STARTS HERE-->
            <table style="width:100%;padding:10px 0;" border="0" align="center" cellpadding="5">
               <thead>
                <tr> <th style="width:100%; font-size: 20px;margin:20px 0;" align="center" > TABLEWISE RECORDING OF VOTES </th></tr>
                <tr> <th style="width:100%; font-size:14px;margin:20px 0;" align="center">Round Number :<b>{!! $round !!}</b> , Table Number :<b style="min-width: 250px;">{!! $table_id !!}</b>
                 </th> </tr>
              </thead>
            </table>
        <!--HEADER ENDS HERE-->
      <style type="text/css">
          .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
      </style>
        <table style="width:100%" border="0" align="center">  
          
                <tr>
                 <td  style="width:100%;">
                    <table  style="width:100%;padding:15px 0;">
                      <tbody>

                        <tr>
                          <td > State: <b>{!! $st_name !!}</b><br> <br> </td>
                           <td align="right" width="200px">  Date:-   {!!$print_date!!} </td> 
                          
                        </tr>
                        <tr>
                         <td>No. & Name of the constituency :<b>{!! $ac_no !!}-{!! $ac_name !!}</b></td>
                          <td align="right"> Enter By:-  {!!$enter_by!!}  </td>
                         </tr>
                         
                      <tr>
                         <td colspan="2">Polling Station Number: <b>{!! $ps_no !!}</b></td>
                          
                         </tr>>
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
          <th width="60%">Name of Candidate</th>
          <th>No. Of  Votes recorded</th>
        </tr>
        </thead>
        <tbody>

        <?php foreach ($results as $result) { ?>
            <tr>
            <td>{!! $result['sr_no'] !!}</td>
            <td align="left">{!! $result['candidate_name'] !!}</td>
            <td>{!! $result['current_total'] !!}</td>
            
            </tr>
          <?php } ?>
       </tbody>
     </table>
    </div>


  <div class="footer_section">
      <table style="width:100%; border-collapse: collapse;" align="center" border="0" cellpadding="15">
          <tbody>
            <tr> <td align="left" colspan="2"><!-- <br>Date <u>{!!$print_date!!}</u><br> -->  </td> </tr>
            <tr> <td align="right" colspan="2"> Signature of Counting Staff (With Full Name) <br></td> </tr>
            <tr> <td align="right" colspan="2">  *to be handed over to the Observer Only <br></td> </tr>
          </tbody>
      </table>
  </div>


    </body>
</html>
