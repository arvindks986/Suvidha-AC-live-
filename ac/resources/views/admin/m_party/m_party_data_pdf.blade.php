<table class="table table-bordered ">
    <thead>
   
     <tr>  
       <th>CCODE</th>
       <th>Party Abbr</th>
       <th>Party Abbr (Hindi)</th>
       <th>Party name</th> 
       <th>Party name (Hindi)</th>
       <th>Party type</th>
       <th>Delete flag</th>
       <th>Created at</th> 
       <th>Updated at</th>
      
     </tr>
   </thead>
   <tbody id="oneTimetab">   
       @foreach($results as $val)
       <tr>
         <td>{{$val->CCODE}}</td>
         <td>
             {{$val->PARTYABBRE}}

         </td>
         <td>
           {{$val->PARTYHABBR}}

       </td>
         <td>
             {{$val->PARTYNAME}}
         </td>
         <td>
           {{$val->PARTYHNAME}}
       </td>
         <td>
             {{$val->PARTYTYPE}}
         </td>
         <td>
          {{$val->deleteflag}}
      </td>
         <td>
           {{ date('d-m-Y',strtotime($val->created_at))}}
       </td>
       <td>
         {{ date('d-m-Y',strtotime($val->updated_at))}}
     </td>

       </tr>
       @endforeach


   </tbody>
    </table>