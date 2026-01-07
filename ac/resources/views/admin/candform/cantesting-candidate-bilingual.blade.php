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
@if(isset($record))
<htmlpageheader name="page-header">  
         <table style="width:100%; text-align:center;" border="0" align="center" cellpadding="2" cellspacing="2" >
                 <thead>
                  <tr> <td align="center"> @if(isset($message)){{@$message}}@endif <span style="font-family:{{$fonts}};">{{$record->vtitle1}}</span> <br>
                     {{$record->title1}} </td>  </tr>
                  <tr> <td align="center"><span style="font-family:{{$fonts}};">{{$record->vtitle2}}</span><br>{{$record->title2}} </td>  </tr>
                 <tr> <td align="center"><span style="font-family:{{$fonts}};">{{$record->vtitle3}}</span><br>{{$record->title3}} </td>  </tr> 
                 <tr> <td align="center"><span style="font-family:{{$fonts}};">{{$record->vtitle4}} </span><br>{{$record->title4}} </td>  </tr>
                </thead>
             </table>
    
</htmlpageheader>
         <table style="width:100%; text-align:center;" border="1" align="center" cellpadding="5" cellspacing="0" >
                <thead>
                  <tr> <td align="center"><span style="font-family:{{$fonts}};">{{$record->vheader1}}</span>
                    <br>{{$record->header1}}</td>  
                       <td align="center"><span style="font-family:{{$fonts}};">{{$record->vheader2}}</span><br>{{$record->header2}}</td>   
                       <td align="center"><span style="font-family:{{$fonts}};">{{$record->vheader3}}</span><br>{{$record->header3}}</td>   
                       <td align="center"><span style="font-family:{{$fonts}};">{{$record->vheader4}}</span><br>{{$record->header4}}</td>   
                       <td align="center"><span style="font-family:{{$fonts}};">{{$record->vheader5}}</span><br>{{$record->header5}}</td>
                       <td align="center"><span style="font-family:{{$fonts}};">{{$record->vheader6}}</span><br>{{$record->header6}}</td>  
                       
                </tr>
                  <tr>  <td align="center"><span style="font-family:{{$fonts}};">{{$record->subheader1}}</span> </td>  
                        <td align="center"><span style="font-family:{{$fonts}};">{{$record->subheader2}}</span> </td>   
                        <td align="center"><span style="font-family:{{$fonts}};">{{$record->subheader3}}</span> </td>   
                        <td align="center"><span style="font-family:{{$fonts}};">{{$record->subheader4}}</span> </td>   
                        <td align="center"><span style="font-family:{{$fonts}};">{{$record->subheader5}}</span> </td> 
                        <td align="center"><span style="font-family:{{$fonts}};">{{$record->subheader6}}</span> </td> 
                </tr>
             </thead> 
             <tbody>  
             <tr> <td align="center" colspan="6"><span style="font-family:{{$fonts}};">{{$record->vmiddle_title1}} </span> <br>{{$record->middle_title1}}</td>  </tr>   
            @foreach ($cands as $key => $item)
            
                  <tr>
                    <td><span style="font-family:{{$fonts}};">{{$item->new_srno}} </span> </td>
                    <td><span style="font-family:{{$fonts}};">{{$item->cand_vname}}</span> <br>
                        <span style="font-family:{{$fonts}};">{{$item->cand_name}}</span> 
                      </td>
                     <td> @if($item->cand_image!='')
                       <img src="{{public_path($item->cand_image)}}" style="width:100px" class="prfl-pic img-thumbnail" alt="">
                      @endif </td>
                    <td><span style="font-family:{{$fonts}}">{{$item->candidate_residence_addressv}}</span><br>
                      <span style="font-family:{{$fonts}}">{{$item->candidate_residence_address}}</span></td>
                    <td><span style="font-family:{{$fonts}};">{{ $item->party_vname }}</span> <br><span style="font-family:{{$fonts}};">{{ $item->PARTYNAME }}</span></td>
                    <td><span style="font-family:{{$fonts}};">{{ $item->symbol_vname }}</span> <br><span style="font-family:{{$fonts}};">{{ $item->SYMBOL_DES }}</span></td>
                  </tr>
                  
             @endforeach
              <tr> <td align="center" colspan="6"><span style="font-family:{{$fonts}};">{{$record->vmiddle_title2}} </span><br>{{$record->middle_title2}}</td>  </tr>

             @foreach ($candu as $key => $item)
            
                  <tr>
                    <td><span style="font-family:{{$fonts}};">{{$item->new_srno}} </span> </td>
                    <td><span style="font-family:{{$fonts}};">{{$item->cand_vname}}</span> <br>
                        <span style="font-family:{{$fonts}};">{{$item->cand_name}}</span> 
                      </td>
                     <td> @if($item->cand_image!='')
                       <img src="{{public_path($item->cand_image)}}" style="width:100px" class="prfl-pic img-thumbnail" alt="">
                      @endif </td>
                    <td><span style="font-family:{{$fonts}}">{{$item->candidate_residence_addressv}}</span><br>
                      <span style="font-family:{{$fonts}}">{{$item->candidate_residence_address}}</span></td>
                    <td><span style="font-family:{{$fonts}};">{{ $item->party_vname }}</span> <br><span style="font-family:{{$fonts}};">{{ $item->PARTYNAME }}</span></td>
                    <td><span style="font-family:{{$fonts}};">{{ $item->symbol_vname }}</span> <br><span style="font-family:{{$fonts}};">{{ $item->SYMBOL_DES }}</span></td>
                  </tr>
             @endforeach 
             <tr> <td align="center" colspan="6"><span style="font-family:{{$fonts}};">{{$record->vmiddle_title3}}</span> <br>{{$record->middle_title3}}</td>  </tr>
             @foreach ($candz as $key => $item)
            
                  <tr>
                    <td><span style="font-family:{{$fonts}};">{{$item->new_srno}} </span> </td>
                    <td><span style="font-family:{{$fonts}};">{{$item->cand_vname}}</span> <br>
                        <span style="font-family:{{$fonts}};">{{$item->cand_name}}</span> 
                      </td>
                     <td> @if($item->cand_image!='')
                       <img src="{{public_path($item->cand_image)}}" style="width:100px" class="prfl-pic img-thumbnail" alt="">
                      @endif </td>
                    <td><span style="font-family:{{$fonts}}">{{$item->candidate_residence_addressv}}</span><br>
                      <span style="font-family:{{$fonts}}">{{$item->candidate_residence_address}}</span></td>
                    <td><span style="font-family:{{$fonts}};">{{ $item->party_vname }}</span> <br><span style="font-family:{{$fonts}};">{{ $item->PARTYNAME }}</span></td>
                    <td><span style="font-family:{{$fonts}};">{{ $item->symbol_vname }}</span> <br><span style="font-family:{{$fonts}};">{{ $item->SYMBOL_DES }}</span></td>
                  </tr>
             @endforeach  
            </tbody>
          </table> 
            <div >

            </div>
           
   
           

           <htmlpagefooter name="page-footer">
              <table style="width:100%; text-align:center;" border="0" align="center" cellpadding="2" cellspacing="2" >
                 <thead>
                  <tr> <td> &nbsp;&nbsp;</td>  <td> &nbsp;&nbsp;</td><td> &nbsp;&nbsp;</td> </tr>
                  <tr> <td align="left"><span style="font-family:{{$fonts}};">{{$record->vfooter1}} <br>{{$record->footer1}} </span></td> <td> &nbsp;&nbsp;</td>
                    <td align="right"><span style="font-family:{{$fonts}};">{{$record->vfooter3}} &nbsp;&nbsp;&nbsp;&nbsp;<br>{{$record->footer3}} &nbsp;&nbsp;&nbsp;&nbsp; </span></td></tr>
                  <tr> <td align="left"><span style="font-family:{{$fonts}};">{{$record->vfooter2}}<br>{{$record->footer2}}</span></td><td> &nbsp;&nbsp;</td> 
                    <td align="right"><span style="font-family:{{$fonts}};">{{$record->vfooter4}}&nbsp;&nbsp;&nbsp;&nbsp;<br>{{$record->footer4}}&nbsp;&nbsp;&nbsp;&nbsp;</span></td></tr>
                  <tr> <td> &nbsp;&nbsp;</td><td> &nbsp;&nbsp;</td> 
                    <td align="right"><span style="font-family:{{$fonts}};">{{$record->vfooter5}} <br>{{$record->footer5}} </span></td></tr>
                </thead>
             </table>
           </htmlpagefooter>  
           @else 
          <h2> Form 7 A heading not enter by RO.</h2>  
    @endif       
          
    </body>
</html>
