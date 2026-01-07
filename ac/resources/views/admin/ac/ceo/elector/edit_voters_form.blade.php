@extends('admin.layouts.ac.theme')
@section('bradcome')
  <li><span class="icon icon-beaker"> </span> Voters Entry Form</li>
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
  .tables_for_form tr td:first-child{
    text-align: left;
    text-transform: capitalize;
    width: 40%;
  }
  .middle_table_column{
    width: 20%;
  }
  .tables_for_form tr td:last-child{
    text-align: right;
    width: 40%;
  }
  .tables_for_form tr td:last-child input{
    text-align: right;
  }
  .voters_table input{
    min-width: 200px; 
  }
  input[type="text"]:disabled {
    background: transparent;
    border: 0px;
  }
  #result_declared_date[readonly="readonly"]{
    background: transparent !important;
  }
</style>

<section class="dashboard-header pt-3 pb-3">
  <div class="container-fluid">
  
        
      <form id="generate_report_id" class="row" method="get" onsubmit="return false;">

          <div class="form-group col-md-3"> <label>AC </label> 
          
            <select name="ac_no" id="ac_no" class="form-control" onchange ="filter()">
            <option value="">Select PC</option>
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

          @if($ac_no)
          <form action="{!! $action !!}" method="post" id="election_form">

          <table class="table table-bordered tables_for_form voters_table">
 
          
            <input type="hidden" name="_token" value="{!! csrf_token() !!}">
            <input type="hidden" name="ac_no" value="{!! $ac_no !!}">
            <thead>
            <tr>
              <th rowspan="2">Voters Turned Up for Voting</th>
              <th colspan="2" class="text-center">General</th>
              
              <th rowspan="2" class="text-center">Total</th>
            </tr>
            <tr>
              <th>Other Than NRIs</th>
              <th>NRIs</th>
            </tr>

          </thead>
          <tbody class="index_voting">   
            <tr>
              <td>Male</td>
              <td><input type="text" name="general_male_voters" value="{!! $object['general_male_voters'] !!}" length="6"  size="6" class="form-control evm_input general for_total"></td>
              <td><input type="text" name="nri_male_voters" value="{!! $object['nri_male_voters'] !!}" length="6"  size="6" class="form-control evm_input nri for_total"></td>
              <td><input type="text" value="{!! $total_nri_and_general_male !!}" class="form-control total_nri_and_general_male total"  length="6"  size="6" disabled="disabled"></td>
            </tr>
               <tr>
                <td>Female</td>
                <td><input type="text" name="general_female_voters" value="{!! $object['general_female_voters'] !!}" length="6"  size="6" class="form-control evm_input general for_total"></td>
                <td><input type="text" name="nri_female_voters" value="{!! $object['nri_female_voters'] !!}" class="form-control evm_input nri for_total" ></td>
                <td><input type="text" value="{!! $total_nri_and_general_female !!}" class="form-control total_nri_and_general_female total"  length="6"  size="6" disabled="disabled"></td>
               </tr>
               <tr>
                <td>Third Gender</td>
                <td><input type="text" name="general_other_voters" value="{!! $object['general_other_voters'] !!}" length="6"  size="6" class="form-control evm_input general for_total"></td>
                <td><input type="text" name="nri_other_voters" value="{!! $object['nri_other_voters'] !!}" class="form-control evm_input nri for_total" ></td>
                <td><input type="text" value="{!! $total_nri_and_general_other !!}" class="form-control total_nri_and_general_other total"  length="6"  size="6" disabled="disabled"></td>
               </tr>
               <tr>
                <td>Total</td>
                <td> <input type="text" class="form-control total_general_voters" value="" length="6"  size="6" value="{!! $object['total_general_voters'] !!}" length="6"  size="6" disabled="disabled"> </td>
                <td> <input type="text" class="form-control total_nri_voters" value="" length="6"  size="6" value="{!! $total_nri_voters !!}" length="6"  size="6" disabled="disabled"> </td>
                <td><input type="text" value="{!! $total_nri_and_general !!}" class="form-control total_nri_and_general"  length="6"  size="6" disabled="disabled"></td>
               </tr>
          </tbody>
        </table>

        <table class="table table-bordered tables_for_form">
          <thead>
            <tr>
              <th colspan="3">Details of Votes Polles on EVM</th>
            </tr>
          </thead>
          <tbody>
               <tr><td>Test Votes under Rule 49 MA</td><td class='text-center middle_table_column'>:</td><td><input type="text" name="test_votes_49_ma" value="{!! $object['test_votes_49_ma'] !!}" class="form-control evm_input"></td>
               </tr>
              <?php /* <tr><td>votes not retreived from evm</td><td class='text-center middle_table_column'>:</td><td><input type="text" name="votes_not_retreived_from_evm" value="{!! $object['votes_not_retreived_from_evm'] !!}"  class="form-control evm_input"></td>
               </tr> */?>
			   
			   <tr><td>Votes counted from CU of EVM</td><td class='text-center middle_table_column'>:</td><td><input type="text" name="votes_counted_from_evm" value="{!! $object['votes_counted_from_evm'] !!}"  class="form-control evm_input"></td>
               </tr>
			   
			   <tr><td>Votes counted from VVPAT (whenever votes not retrieved from CU)</td><td class='text-center middle_table_column'>:</td><td><input type="text" name="votes_counted_from_vvpat" value="{!! $object['votes_counted_from_vvpat'] !!}"  class="form-control evm_input"></td>
               </tr>
			   
			   
			   
               <tr><td>Votes not counted from CU(s) as per ECI Instructions</td><td class='text-center middle_table_column'>:</td><td><input type="text" name="rejected_votes_due_2_other_reason" value="{!! $object['rejected_votes_due_2_other_reason'] !!}" class="form-control evm_input"></td>
               </tr>
          </tbody>
        </table>

        <table class="table table-bordered tables_for_form">
          <thead>
            <tr>
              <th colspan="3">Details of Postal Votes</th>
            </tr>
          </thead>
          <tbody>
               <tr><td>Postal Votes Counted for service voter under sub-section (8) of Section 20 of R.P. Act, 1950</td><td class='text-center middle_table_column'>:</td><td><input type="text" name="service_postal_votes_under_section_8" value="{!! $object['service_postal_votes_under_section_8'] !!}"  class="form-control evm_input"></td>
               </tr>
               <tr>
                <td>Postal votes counted for Govt. election duty servants on (including all police personnel, driver, conductors, cleaner) and Absentee Voters.</td><td class='text-center middle_table_column'>:</td><td><input type="text" name="service_postal_votes_gov" value="{!! $object['service_postal_votes_gov'] !!}"   class="form-control evm_input"></td>
               </tr>
      
              </tbody>
            </table>

            <table  class="table table-bordered tables_for_form">
              <thead>
                <tr>
                  <th colspan="3">Miscellaneous</th>
                </tr>
              </thead>
              <tbody>
               <tr><td>proxy votes </td><td class='text-center middle_table_column'>:</td><td><input type="text" name="proxy_votes" value="{!! $object['proxy_votes'] !!}"  class="form-control evm_input"></td>
               </tr>

               <tr><td>Total number of polling Station set up in the Constituency</td><td class='text-center middle_table_column'>:</td><td><input type="text" name="total_polling_station_s_i_t_c" value="{!! $object['total_polling_station_s_i_t_c'] !!}"  class="form-control evm_input"></td>

              </tr>
              <tr><td>Date(s) Of Re-Poll, if any</td><td class='text-center middle_table_column'>:</td><td><input type="text" name="date_of_repoll" value="{!! $object['date_of_repoll'] !!}"  class="form-control">
                <br><small>(comma seperated eg: 2019-01-01, 2018-03-07)</small>
              </td>
              </tr>

              <tr>
                <td>Number Of Polling Stations where Re-poll was ordered (mention date of Order also)</td><td class='text-center middle_table_column'>:</td><td>
                  <textarea name="no_poll_station_where_repoll" class="form-control">{!! $object['no_poll_station_where_repoll'] !!}</textarea>

                </td>
              </tr>

              <tr><td>Whether this is Bye Election or Countemented Election?</td><td class='text-center middle_table_column'>:</td><td><select name="is_by_or_countermanded_election" class="form-control">
                <?php if($object['is_by_or_countermanded_election'] == 1){ ?>  
                <option value="1" selected="selected">Yes</option>
                <option value="0">No</option>
              <?php }else{ ?>
                <option value="1">Yes</option>
                <option value="0" selected="selected">No</option>
              <?php } ?>
              
              </select>

              </td>
              </tr>
              <tr><td>If Yes, Reason There Of</td><td class='text-center middle_table_column'>:</td><td><input type="text" name="reasons_for_by_or_countermanded_election" value="{!! $object['reasons_for_by_or_countermanded_election'] !!}" class="form-control"></td>
                
            </tr>
			
			<tr>
                <td>Result Declaration Date:</td><td class='text-center middle_table_column'>:</td><td>
                  <input name="result_declared_date" id="result_declared_date" class="form-control" value="{!! $object['result_declared_date'] !!}" readonly="readonly">

                </td>
              </tr>
			
			
            <?php if(!$is_finalize){  ?>
         <tr>
              <td colspan="15">
                <button class="btn btn-success pull-right" type="submit">Save</button>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
     
          </form>
            
          @else
          <table>
          <tbody>
          <tr>
            <td colspan="15" cellpadding='5' align="center">
              Please Select a AC.
            </td>
          </tr>
          </tbody>
          </table>
          @endif

           
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

    <?php if($custom_error){ ?>
      $("[name = '<?php echo $key ?>']").after("<span class='text-danger small_text'><?php echo $custom_error; ?></span>");
      $("[name = '<?php echo $key ?>']").addClass('is-valid');
    <?php } ?>

  <?php } ?>
});
</script>

<script type="text/javascript">
$(document).ready(function () {
  $('#election_form .evm_input').each(function(i,object){
    $(".evm_input").removeClass("is-valid");
    $(object).on('keyup change keydown',function (e) {
      $(object).parent('td').find('.text-danger').remove();
      if (parseInt($(object).val()) >= 0 && !isNaN($(object).val()) && $(object).val().indexOf('.') == '-1'){
        $(object).removeClass("is-valid"); 
        $(object).val(trim_number($(object).val()));
      }else{
        $(object).addClass("is-valid");
        $(object).parent('td').append("<span class='text-danger small_text'></span>");
        $(object).parent('td').find('.text-danger').text("please enter positive numeric value.").show();
        $(object).val('');
      }
    });
  });


  $('.nri,.general').on('keyup change keydown',function (e) {
    calculate_total();
  });

  calculate_total();

});

function calculate_total(){
  var total_count = 0;
  $('.nri').each(function(i,object){
    if(parseInt($(object).val()) >= 0 && !isNaN($(object).val())){
      total_count = parseInt(total_count) + parseInt($(object).val());
    }
  });
  $('.total_nri_voters').val(total_count);

  var total_general = 0;
  $('.general').each(function(i,object){
    if(parseInt($(object).val()) >= 0 && !isNaN($(object).val())){
      total_general = parseInt(total_general) + parseInt($(object).val());
    }
  });
  $('.total_general_voters').val(total_general);

  $('.index_voting tr').each(function(i,object){
    var total_row = 0;
    $($(object).find('.for_total')).each(function(inedx1,object1){
      if(parseInt($(object1).val()) >= 0 && !isNaN($(object1).val())){
        total_row = parseInt(total_row) + parseInt($(object1).val());
      }
    });
    $($(object).find('.total')).val(total_row);
  });

  var total = 0;
  $('.index_voting .total').each(function(i,object){
    if(parseInt($(object).val()) >= 0 && !isNaN($(object).val())){
      total = parseInt(total) + parseInt($(object).val());
    }
  });
  $('.total_nri_and_general').val(total);
  
}


function trim_number(s) {
  while (s.substr(0,1) == '0' && s.length>1) { s = s.substr(1,9999); }
  return s;
}
</script>

<?php if($is_finalize){ ?>
<script type="text/javascript">
  $('.card-body input, .card-body select, .card-body textarea').attr('disabled','disabled');
</script>
<?php } ?>

<link rel="stylesheet" type="text/css" href="{!! url('admintheme/css/jquery-ui.css') !!}">
<script type="text/javascript" src="{!! url('admintheme/js/jquery-ui.js') !!}"></script>
<script type="text/javascript">
$(document).ready(function() {  
  $("#result_declared_date").datepicker({
    dateFormat: 'yy-mm-dd'
  });
}); 
</script>

@endsection