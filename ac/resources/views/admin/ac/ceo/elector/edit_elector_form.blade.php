@extends('admin.layouts.ac.theme')
@section('bradcome')

  <li><span class="icon icon-beaker"> </span>Electors Entry Form</li>

@endsection
@section('content')
<style type="text/css">
  .capatlize th{
    text-transform: capitalize;
    font-size: 12px;
    text-align: center;
  }
  .table th, .table td{
    padding: 3px !important;
  }
  .table td .form-control{
    font-size: 12px;
  }
  .small_text{
    font-size: 10px;
    line-height: 12px;
  }
</style>

<section class="dashboard-header pt-3 pb-3">
  <div class="container-fluid">
  
        
      <form id="generate_report_id" class="row" method="get" onsubmit="return false;">

          <div class="form-group col-md-3"> <label>AC </label> 
          
            <select name="ac_no" id="ac_no" class="form-control" onchange ="filter()">
            <option value="">Select AC</option>
            @foreach($acs as $result)
              @if($ac_no == $result['ac_no'])
                <option value="{{$result['ac_no']}}" selected="selected" >{{$result['ac_no']}}-{{$result['ac_name']}}</option> 
              @else 
                <option value="{{$result['ac_no']}}" >{{$result['ac_no']}}-{{$result['ac_name']}}</option> 
              @endif  
            @endforeach
        
            </select>
          </div>
         
        </form>   
  
    
  </div>
</section>

<main role="main" class="inner cover mb-3 mt-3">
<section>  

  <div class="container-fluid">
  <div class="row">   


  @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
          @endif
          @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
          @endif   


<?php if($is_finalize){  ?>
<div class="alert alert-danger">
Indexcard has been finalized.
</div>
<?php } ?>


<div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h4>{!! $heading_title !!}</h4></div> 
                  <div class="col"><p class="mb-0 text-right">

                    @if(isset($filter_buttons) && count($filter_buttons)>0)
                            @foreach($filter_buttons as $button)
                                <?php $but = explode(':',$button); ?>
                                <b>{!! $but[0] !!}:</b>
                                <span class="badge badge-info">{!! $but[1] !!}</span>
                            @endforeach  
                    @endif
                



                    &nbsp;&nbsp; 
                  <b></b> 
                   <span class="badge badge-info"></span>&nbsp;&nbsp;  </p></div>
                </div> <!-- end col-->
                </div><!-- end row-->
              
            <div class="card-body"> 

    

           <div class="table-responsive">
          <table class="table table-bordered ">
           <thead class="capatlize">

            <tr>
              <th rowspan="2">Ac Name</th>
               <th colspan="3">General Electors(other than NRI)</th> 
              <th colspan="3">NRI Electors</th> 
              <th colspan="3">Service Electors</th> 

    
            </tr>

            <tr> 
              
             <th>male </th>
              <th>female</th>
              <th>third Gender</th>
              <th>male</th>
              <th>female</th>
              <th>Other</th> 
              <th>male</th>
              <th>female</th> 
              <th>Other</th> 
              

           
            </tr>

          </thead>
          @if(count($results)>0)
          <form action="{!! $action !!}" method="post">
            <input type="hidden" name="_token" value="{!! csrf_token() !!}">
            <input type="hidden" name="ac_no" value="{!! $ac_no !!}">
          <tbody>   
            <?php $i = 0; ?>
            @foreach($results as $result)
               <input type="hidden" name="elector[<?php echo $i ?>][id]" value="{!! $result['id'] !!}">
              <tr id="table_row_{{$i}}">
                <td><input type="hidden" name="elector[<?php echo $i ?>][ac_name]" value="{!! $result['ac_name'] !!}">{!! $result['ac_name'] !!}</td>
               
                <td> <input class="form-control" name="elector[<?php echo $i ?>][gen_electors_male]" value="{!! $result['gen_electors_male'] !!}" maxlength="6" size="6"></td>
                <td> <input class="form-control" name="elector[<?php echo $i ?>][gen_electors_female]" value="{!! $result['gen_electors_female'] !!}" maxlength="6" size="6"></td>
                <td> <input class="form-control" name="elector[<?php echo $i ?>][gen_electors_other]" value="{!! $result['gen_electors_other'] !!}" maxlength="6" size="6"></td>

                <td> <input class="form-control" name="elector[<?php echo $i ?>][nri_male_electors]" value="{!! $result['nri_male_electors'] !!}" maxlength="6" size="6"></td>
                <td> <input class="form-control" name="elector[<?php echo $i ?>][nri_female_electors]" value="{!! $result['nri_female_electors'] !!}" maxlength="6" size="6"></td>
                <td> <input class="form-control" name="elector[<?php echo $i ?>][nri_third_electors]" value="{!! $result['nri_third_electors'] !!}" maxlength="6" size="6"></td>
              
                <td> <input class="form-control" name="elector[<?php echo $i ?>][service_male_electors]" value="{!! $result['service_male_electors'] !!}" maxlength="6" size="6"></td>
                <td> <input class="form-control" name="elector[<?php echo $i ?>][service_female_electors]" value="{!! $result['service_female_electors'] !!}" maxlength="6" size="6"></td>
                <td> <input class="form-control" name="elector[<?php echo $i ?>][service_third_electors]" value="{!! $result['service_third_electors'] !!}" maxlength="6" size="6"></td>
           

              </tr>
              <?php $i++; ?>
            @endforeach

          </tbody>
          <?php if(!$is_finalize){  ?>
          <tfoot>
            <tr>
              <td colspan="15">
                <button class="btn btn-success pull-right" type="submit">Save</button>
              </td>
            </tr>
          </tfoot>
        <?php } ?>
          </form>
          @else
          <tbody>
          <tr>
            <td colspan="15" cellpadding='5' align="center">
              Please Select a AC.
            </td>
          </tr>
          </tbody>
          @endif

           </table>
         </div><!-- End Of  table responsive -->  
       </div>
     </div>
      </div><!-- End Of intra-table Div -->   
        
         
      </div><!-- End Of random-area Div -->
      
</section>
</main>
@endsection

@section('script')

<script type="text/javascript">
function filter(){
  var url = "<?php echo $current_page ?>";
  var query = '';
    if($("#ac_no").val() != ''){
      query += '&ac_no='+$("#ac_no").val();
    }
    window.location.href = url+'?'+query.substring(1);
}

$(document).ready(function(e){
  <?php foreach($custom_errors as $key => $custom_error){ ?>
    <?php foreach($custom_error as $second_key => $err){ ?>
    <?php if($err){ ?>
      $("input[name = 'elector[<?php echo $key ?>][<?php echo $second_key ?>]'").after("<span class='text-danger small_text'><?php echo $err; ?></span>");
      $("input[name = 'elector[<?php echo $key ?>][<?php echo $second_key ?>]'").addClass('is-valid');
    <?php } ?>
    <?php } ?>
  <?php } ?>
});
</script>

<?php if($is_finalize){ ?>
<script type="text/javascript">
  $('.card-body input, .card-body select, .card-body textarea').attr('disabled','disabled');
</script>
<?php } ?>
@endsection