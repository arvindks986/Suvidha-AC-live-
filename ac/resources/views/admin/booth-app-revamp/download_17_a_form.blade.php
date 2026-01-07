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
      height: 50px !important;
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
                   <th style="width:100%; font-size: 18px;" align="center" >
                        <strong>FORM-17A <br>(See Rule 49L) <br>REGISTER OF VOTERS <br><br><br></strong>
                    </th>
                </tr>

              </thead>
            </table>
        <!--HEADER ENDS HERE-->

        
        </div>
</htmlpageheader>
<div class="body_section">
  <table style="width: 100%;"  align="center" cellpadding="5">
    <tbody>
      <tr>
        <td>
          Election to the House of the People/Legislative Assembly of the <b>{{$st_name}}</b> from Constituency No. <b>{{$ac_no}}</b> and Name of Polling Station <b>{{$ps_name}}</b> PS No. <b>{{$ps_no}}</b> of Electoral Roll 
        </td>
      </tr>
    </tbody>
  </table>

                       
<table class="table-strip" style="width: 100%;" border="1" align="center" cellpadding="5">

    <thead>
      <tr>
        <td>SI. No.</td>
        <td>SI. No. of elector in the electoral roll</td>
        <td>Signature/Thumb<br>impression of elector </td>
        <td>Remarks</td>
      </tr>
    </thead>
    <tbody id="oneTimetab">   
      @foreach($results as $result)
      <tr>
        <td>{{$result['sr_no']}}</td>
        <td>{{$result['elector_sr_no']}}</td>
        <td></td>
        <td></td>
      </tr>
      @endforeach

    </tbody>
  </table>
</div>

<htmlpagefooter name="page-footer" >
  <div class="footer_section">
      <table style="width:100%; border-collapse: collapse;" align="center" border="0" cellpadding="15">
          <tbody>
            <tr>
              <td align="left">
                  Page {PAGENO}
              </td>
              <td align="right"><br>Signature of the Presiding Officer<br>  <br>  <br>               ___________________________
                <br>  
              </td>


            </tr>
          </tbody>
      </table>
  </div>
</htmlpagefooter>






 
</body>
</html>