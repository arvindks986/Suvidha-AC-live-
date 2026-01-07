<?php 
if(Session::has('DB_id')){
          $DB_id = Session::get('DB_id');
        }else{
          $DB_id = 0;
        }
     ?>

    <form method="POST" action="{!! url('change-database') !!}" id="change_databsse"> 
      <input type="hidden" name="_token" value="{!! csrf_token() !!}" id="token">
      <div class="form-group">
            <select name="database" class="form-control" id="new" onchange="submit()">
                <option value="" selected="selected">Please Select a Election First</option>
                @if(isset($elec_details))
                @foreach($elec_details as $details)
          <option value="{{$details->id}}" @if($DB_id == $details->id) selected="selected" @endif  >{{$details->description}}</option>
          @endforeach
          @endif
        </select>
                 
      </div>
    </form>
    <script type="text/javascript">
      function change_database(){
        $('#change_databsse').submit();
      }
    </script>