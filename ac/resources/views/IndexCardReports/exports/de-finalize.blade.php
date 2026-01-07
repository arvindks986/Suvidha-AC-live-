  
        <table class="table-strip" style="width: 100%;" border="1" align="center">
         <thead>
         <tr>
          <th>Sl No</th>
          <th>State Name</th> 
          <th>AC No - AC Name</th> 
          <th>De-Finalized Type</th> 
          <th>De-Finalized Date</th> 
         
        </tr>
        </thead>
        <tbody>
        @php  

        $count = 1;
         @endphp

        @forelse($results as $result)
          <tr>
             <td>{{ $count }}</td>

            <td>{{ $result->st_name }}</td>
            <td>{{ $result->ac_no }} - {{ $result->ac_name }}  </td>
			<td>{{ ucfirst($result->type_finalize) }}  </td>
            <td>{{ date('d-m-Y h:i A', strtotime($result->created_at)) }}  </td>

           

          
          
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Index Card Finalize Statusss</td>                 
              </tr>
          @endforelse
        </tbody>
    </table>