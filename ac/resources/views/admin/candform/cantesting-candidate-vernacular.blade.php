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
                  <tr> <td align="center">@if(isset($message)){{@$message}}@endif <span style="font-family:{{$fonts}};">{{$record->vtitle1}}</span></td>  </tr>
                  <tr> <td align="center"><span style="font-family:{{$fonts}};">{{$record->vtitle2}}</span></td>  </tr>
                 <tr> <td align="center"><span style="font-family:{{$fonts}};">{{$record->vtitle3}}</span></td>  </tr>
                 <tr> <td align="center"><span style="font-family:{{$fonts}};">{{$record->vtitle4}} </span></td>  </tr>
                </thead>
             </table>
    
</htmlpageheader>
         <table style="width:100%; text-align:center;" border="1" align="center" cellpadding="5" cellspacing="0" >
                <thead>
                  <tr> <td align="center"><span style="font-family:{{$fonts}};">{{$record->vheader1}}</span></td>  
                       <td align="center"><span style="font-family:{{$fonts}};">{{$record->vheader2}}</span></td>  
                       <td align="center"><span style="font-family:{{$fonts}};">{{$record->vheader3}}</span></td>  
                       <td align="center"><span style="font-family:{{$fonts}};">{{$record->vheader4}}</span></td>  
                       <td align="center"><span style="font-family:{{$fonts}};">{{$record->vheader5}}</span></td>
                       <td align="center"><span style="font-family:{{$fonts}};">{{$record->vheader6}}</span></td>  
                </tr>
                  <tr>  <td align="center"><span style="font-family:{{$fonts}};">{{$record->vsubheader1}}</span></td>  
                        <td align="center"><span style="font-family:{{$fonts}};">{{$record->vsubheader2}}</span></td>  
                        <td align="center"><span style="font-family:{{$fonts}};">{{$record->vsubheader3}}</span></td>  
                        <td align="center"><span style="font-family:{{$fonts}};">{{$record->vsubheader4}}</span></td>  
                        <td align="center"><span style="font-family:{{$fonts}};">{{$record->vsubheader5}}</span></td> 
                        <td align="center"><span style="font-family:{{$fonts}};">{{$record->vsubheader6}}</span></td> 
                </tr>
             </thead> 
             <tbody>  
             <tr> <td align="center" colspan="6"><span style="font-family:{{$fonts}};">{{$record->vmiddle_title1}} </span></td>  </tr>   
            @foreach ($cands as $key => $item)
            
                  <tr>
                    <td><span style="font-family:{{$fonts}};">{{$item->new_srno}} </span> </td>
                    <td><span style="font-family:{{$fonts}};">{{$item->cand_vname}}</span>  </td>
                     <td>  
                      @if($item->cand_image!='')
                       <img src="{{public_path($item->cand_image)}}" style="width:100px" class="prfl-pic img-thumbnail" alt="">
                      @endif </td>
                    <td><span style="font-family:{{$fonts}}">{{$item->candidate_residence_addressv}}</span></td>
                    <td><span style="font-family:{{$fonts}};">{{ $item->party_vname }}</span></td>
                    <td><span style="font-family:{{$fonts}};">{{ $item->symbol_vname }}</span></td>
                  </tr>
             @endforeach
              <tr> <td align="center" colspan="6"><span style="font-family:{{$fonts}};">{{$record->vmiddle_title2}} </span></td>  </tr>

             @foreach ($candu as $key => $item)
            
                  <tr>
                    <td><span style="font-family:{{$fonts}};">{{$item->new_srno}} </span> </td>
                    <td><span style="font-family:{{$fonts}};">{{$item->cand_vname}}</span>  </td>
                     <td>  
                      @if($item->cand_image!='')
                       <img src="{{public_path($item->cand_image)}}" style="width:100px" class="prfl-pic img-thumbnail" alt="">
                      @endif </td>
                    <td><span style="font-family:{{$fonts}}">{{$item->candidate_residence_addressv}}</span></td>
                    <td><span style="font-family:{{$fonts}};">{{ $item->party_vname }}</span></td>
                    <td><span style="font-family:{{$fonts}};">{{ $item->symbol_vname }}</span></td>
                  </tr>
             @endforeach 
             <tr> <td align="center" colspan="6"><span style="font-family:{{$fonts}};">{{$record->vmiddle_title3}}</span></td>  </tr>
             @foreach ($candz as $key => $item)
            
                    <tr>
                    <td><span style="font-family:{{$fonts}};">{{$item->new_srno}} </span> </td>
                    <td><span style="font-family:{{$fonts}};">{{$item->cand_vname}}</span>  </td>
                     <td>  
                      @if($item->cand_image!='')
                       <img src="{{public_path($item->cand_image)}}" style="width:100px" class="prfl-pic img-thumbnail" alt="">
                      @endif </td>
                    <td><span style="font-family:{{$fonts}}">{{$item->candidate_residence_addressv}}</span></td>
                    <td><span style="font-family:{{$fonts}};">{{ $item->party_vname }}</span></td>
                    <td><span style="font-family:{{$fonts}};">{{ $item->symbol_vname }}</span></td>
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
                  <tr> <td align="left"><span style="font-family:{{$fonts}};">{{$record->vfooter1}}</span></td> <td> &nbsp;&nbsp;</td><td align="right"><span style="font-family:{{$fonts}};">{{$record->vfooter3}}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
                  <tr> <td align="left"><span style="font-family:{{$fonts}};">{{$record->vfooter2}}</span></td><td> &nbsp;&nbsp;</td> <td align="right"><span style="font-family:{{$fonts}};">{{$record->vfooter4}}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
                  <tr> <td> &nbsp;&nbsp;</td><td> &nbsp;&nbsp;</td> <td align="right"><span style="font-family:{{$fonts}};">{{$record->vfooter5}}</span></td></tr>
                </thead>
             </table>
           </htmlpagefooter>  
           @else 
          <h2> Form 7 A heading not enter by RO.</h2>  
    @endif       
          
    </body>
</html>
