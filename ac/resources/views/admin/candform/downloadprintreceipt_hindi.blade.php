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

    body,
    p,
    td,
    div {
      font-family: freesans;
    }

    .table-strip {
      border-collapse: collapse;
    }

    .table-strip th,
    .table-strip td {
      text-align: center;
    }

    .header_section {
      height: 300px !important;
      width: 100%;
      float: left;
    }

    .table-strip {
      border-collapse: collapse;
    }

    .table-strip th,
    .table-strip td {
      text-align: center;
    }
  </style>
</head>

<body>
  <?php
  if ($caddata->cand_name == $caddata->nomination_submittedby) {
    $applied_by = '(उम्मीदवार)';
  } else {
    $applied_by = '(प्रस्तावक)';
  }
  ?>
  <htmlpageheader name="page-header">
    <div class="header_section">
      <p align="right" class="text-right"> <small style="font-size:10px;"> एनकॉर ऑडिट रेफ.:- {!!$ref_no!!} </small></p>
      <!--HEADER STARTS HERE-->
      <table style="width:100%;" border="0" align="center" cellpadding="5">
        <thead>
          <tr>
            <th style="width:100%; font-size:27px;" align="center" colspan="2"><img src="{{ public_path('/theme/img/logo/eci-logo.png') }}" alt="" width="100" border="0" /></th>
          </tr>
          <tr>
            <th style="width:100%; font-size:27px;" align="center" colspan="2">
              भाग 6
            </th>
          </tr>
          </thead>
          <tbody>
            <tr>
              <td style="width: 100%; text-align: center;font-size:14px;" colspan="2">
                <strong>नामनिर्देशन पत्र के लिए रसीद और संवीक्षा की सूचना</strong>
                <br />
                (नामनिर्देशन-पत्र उपस्थित करने वाले व्‍यक्ति को दिए जाने के लिए)
              </td>
            </tr>
            <tr>
              <td style="width: 100%; text-align: center;font-size:12px;" colspan="2"> राज्य: <b>{!! $st_name !!}</b>
                विधानसभा क्षेत्र: <b>{{$ac_no}}-{{$ac_name}}</b></td>
            </tr>
          </tbody>
      </table>

      <!--HEADER ENDS HERE-->

    </div>
  </htmlpageheader>



  <table class="table-strip" style="width: 100%;" border="0" align="center" cellpadding="10">
    <tr>
      <td  style="width: 100%; text-align:justify;font-size:12px; line-height:30px;">नामनिर्देशन-पत्र की क्रम सं0 <b><u>{{$caddata->nomination_papersrno}} </u></b></td>
    </tr>
    <tr>
      <td  style="width: 100%; text-align:justify;font-size:12px; line-height:30px;">
      <strong><u>{{strtoupper($caddata->cand_name)}} </u></strong> का, जो @if(!empty($ac)) <strong> <strong><u>{{strtoupper($ac->AC_NAME) }}</u> </strong> </strong>@endif विधान सभा निर्वाचन-क्षेत्र से निर्वाचन के लिए अभ्‍यर्थी हैं, 
      नामनिर्देशन-पत्र मुझे/मेरे कार्यालय में <strong><u>{{date("d-m-Y",strtotime($caddata->rosubmit_date))}} </u></strong> (तारीख) को <strong><u>{{$caddata->rosubmit_time}}</u> </strong> (बजे) अभ्‍यर्थी/प्रस्‍थापक द्वारा परिदत्‍त किया गया। 
      सभी नामनिर्देशन पत्रों की संवीक्षा <strong><u>{{date("d-m-Y",strtotime($caddata->scrutiny_date))}} </u></strong> (तारीख) को <strong><u>{{$caddata->scrutiny_time}}</u> </strong> (बजे) <strong><u>{{strtoupper($caddata->place)}}</u> </strong> (स्‍थान) में की जाएगी।
      </td>
    </tr>
  </table>


  <table style="width: 100%; margin-bottom: 5px; margin-top:30px;">
    <tr>
      <td>तारीख:- <b>{{$caddata->fdate}}</b></td>
      <td align="right">
        रिटर्निंग ऑफिसर <br><br> <b>{{$ac_no}}-{{$ac_name}}</b></td>
    </tr>
  </table>


</body>

</html>