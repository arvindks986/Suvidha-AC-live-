<table class="table table-bordered ">
    <thead>
   
     <tr>  
             <th>SYMBOL_NO</th>
              <th>SYMBOL_DES</th>
              <th>SYMBOL_HDES</th>
              <th>SYMBOL_BMP</th>
              <th>SYMBOL_HFOCDES</th> 
              <th>Ind_Symbol</th>
              <th>created_at</th>
              <th>updated_at</th>
      
     </tr>
   </thead>
   <tbody id="oneTimetab">   
       @foreach($results as $val)
       <tr>
        <td>{{$val->SYMBOL_NO}}</td>
        <td>
            {{$val->SYMBOL_DES}}

        </td>
        <td>
            {{ $val->SYMBOL_HDES}}

      </td>
        <td>
            {{$val->SYMBOL_BMP}}
        </td>
        <td>
            {{$val->SYMBOL_HFOCDES}}
        </td>
        <td>
            {{$val->Ind_Symbol}}
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