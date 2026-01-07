<!DOCTYPE html>
<html lang="en">
 <?php   $url = URL::to("/"); $i=1;  ?>
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

@if(isset($lists))
<htmlpageheader name="page-header">
  <div class="header_section">
    <table style="width:100%; text-align:center;" border="0" align="center" cellpadding="2" cellspacing="2" >
     <thead>
      <tr> <td align="center" colspan="2"><h2> {{$heading_title}}</h2></td></tr>
       
    </thead>
 </table>
      
  </div>
</htmlpageheader>
                   
<table style="width:100%; text-align:center;" border="1" align="center" cellpadding="5" cellspacing="0" >
  <thead><tr>
              <th>Sl. No.</th>
              <th>Party Abbre</th>
              <th>Party Name</th> 
              <th>State</th>
              
          </tr> 
   </thead>
  <tbody> 
    @foreach ($lists as $key=>$list)
           <tr>
              <td>{{$i}}</td>
              <td width="100px" align="left">@if(isset($list['PARTYABBRE'])) {{$list['PARTYABBRE']}} @endif</td>
              <td align="left">@if(isset($list['PARTYNAME'])) {{$list['PARTYNAME']}} @endif</td>
              <td align="left">@if(isset($list['ST_NAME'])) {{$list['ST_CODE']}}-{{$list['ST_NAME']}} @endif</td>
           </tr>
           <?php $i++;?>
          @endforeach
  </tbody>  
  </table> 
  <div >
  </div>
 @endif      
     

    </body>
</html>