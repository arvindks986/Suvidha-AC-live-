<table class="table table-bordered ">
    <thead>
   
     <tr>  
        <th>CCODE</th>
        <th>Party Abbr</th>
        <th>ST Code</th>
        <th>Party symbol</th>
        <th>Created at</th> 
        <th>Updated at</th>
      
     </tr>
   </thead>
   <tbody id="oneTimetab">   
       @foreach($results as $val)
       <tr>
        <td>{{$val->ccode}}</td>
        <td>
            {{$val->PARTYABBRE}}

        </td>
        <td>
            {{ $val->ST_CODE }}

      </td>
        <td>
            {{$val->PARTYSYM}}
        </td>
   
        <td>
          @if($val->created_at!='0000-00-00 00:00:00')
          {{ date('d-m-Y',strtotime($val->created_at))}}
          @endif
      </td>
      <td>
        @if($val->updated_at!='0000-00-00 00:00:00')
        {{ date('d-m-Y',strtotime($val->updated_at))}}
        @endif
    </td>

      </tr>
       @endforeach


   </tbody>
    </table>