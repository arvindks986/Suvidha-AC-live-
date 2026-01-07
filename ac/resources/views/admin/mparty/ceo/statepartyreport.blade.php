<!DOCTYPE html>
<html lang="en">
 <?php   $url = URL::to("/");  ?>
    <head>
        <meta charset="utf-8">
        <title>{!! $heading_title !!}</title>
      <style type="text/css">
        @page {
        header: page-header;
        footer: page-footer;
        font-family:freeserif;
      }
      
      </style>
      <style type="text/css">         
      html,body{font-family: {{$font_data}}, sans-serif;  margin:0; overflow-x:hidden; }        
        * {-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%}
        
  
      </style>
    </head>
    <body>

@if(isset($records))
<htmlpageheader name="page-header">
  <div class="header_section">
    <table style="width:100%; text-align:center;" border="0" align="center" cellpadding="2" cellspacing="2" >
     <thead>
      <tr> <td align="center" colspan="2"><h2> {{$heading_title}}</h2></td></tr>
      <tr> <td align="Left"><h4>  State Name:-{{$st_name}} </h4></td>
           <td align="right"><h4> State vernacular Language:-{{$state_language}}</h4></td>
      </tr>
      <tr hight="25"> <td colspan="2"> &nbsp;</td> </tr>
    </thead>
 </table>
      
  </div>
</htmlpageheader>
                   
<table style="width:100%; text-align:center;" border="1" align="center" cellpadding="5" cellspacing="0" >
  <thead> <tr> 
       <th align="center" >Sr. No.</th> 
       <th align="center" >Party Abbree</th>
       <th align="center" >Party Name In English</th>
       <!-- <th align="center" >Party Name In Hindi</th> -->
       <th align="center" >Party Name In Vernacular</th>
    </tr>  
   </thead>
  <tbody>  <?php $i=0; ?>
  @foreach ($records as $key => $item)
    <?php $i++; ?>
    <tr>
    <td>{{$i}}</td>
    <td>@if($item->party_abbre!='') {{$item->party_abbre }} @endif</td>
    <td>@if($item->party_name!=''){{$item->party_name }} @endif</td>
    <!-- <td>@if($item->party_hname!='')
      <span style="font-family:freeserif;">{{trim($item->party_hname)}}</span>@else  @endif</td> -->
    <td>@if($item->party_vname!='')<span style="font-family:{{$fonts}};">{{$item->party_vname }}</span> @else  @endif</td>
    </tr>
  @endforeach
  </tbody>  
  </table> 
  <div >
  </div>
 @endif      
     

    </body>
</html>