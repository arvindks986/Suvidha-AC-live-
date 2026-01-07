@extends('admin.layouts.pc.report-theme')
@section('title', 'Electors Polling Stations')
@section('content') 
  <?php  $st=getstatebystatecode($user_data->st_code);   
       /* if($ele_details->CONST_TYPE=="PC")
          $pc=getpcbypcno($st_code,$pc_no);*/
          $pc='';
        $j=0;
       
  ?> 
<style type="text/css">
      th, td { white-space: nowrap;}
        <!-- .dataTables_wrapper .row:nth-child(2) .col-sm-12 { overflow: scroll;} -->
        html {
              overflow: scroll;
              overflow-x: hidden;
             }
              ::-webkit-scrollbar {    width: 0px; 
              background: transparent;  /* optional: just make scrollbar invisible */
              }
              ::-webkit-scrollbar-thumb {
                background: #ff9800;
                }
                .report_tabs input {
    /* margin: 5px 0; */
    text-align: center;
    border: none;
    background: #d5d5d561;
    width: 100%;
}
              div.dataTables_wrapper {margin:0 auto;} 
              .table-bordered thead th, .table-bordered thead td {
    border-bottom-width: 0px;
    text-align: center;
}
.table-bordered th, .table-bordered td {
    border: 1px solid #dee2e6;
    text-align: center;
    vertical-align: middle;}
    .table th, .table td{padding:0px;}
  </style>
 <main role="main" class="inner cover mb-3">
   
<section>
  <div class="container-fluid">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
            <div class=" card-header">
                <div class=" row">
                  <div class="col"><h4>Electors Polling Station Data Form</h4></div> 
                   <div class="col"><p class="mb-0 text-right">
                   <b>State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; 
                   <b></b> 
                   <span class="badge badge-info"></span>&nbsp;&nbsp; </p>
                   </div>
                </div>
            </div>
   <div class="row">
    <div class="col">
    @if(Session::has('success_admin'))
          <div class="alert alert-success"><strong> {{ nl2br(Session::get('success_admin')) }}</strong> </div>
       @endif   
      @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
      @endif
      @if (session('error_mes'))
          <div class="alert alert-danger"> {{session('error_mes') }}</div>
      @endif
      @if (session('error_mes1'))
          <div class="alert alert-danger"> {{session('error_mes1') }}</div>
      @endif
      @if(!empty($errors->first()))
        <div class="alert alert-danger"> <span>{{ $errors->first() }}</span> </div>
      @endif  
    </div>
    </div>
       
    <div class="card-body">  
     <form class="form-horizontal" id="electorsPollingStation_form" method="POST"  action="{{url('ropc/electors-ropollingstation') }}" >
        {{ csrf_field() }} 
       <table   class="table table-striped table-bordered report_tabs" style="width:100%">
      
        
        <thead>
            <tr>
            <th colspan="4">AC No & Name </th>
            <th colspan="4">General Electors</th>
            <th colspan="4">Service Electors</th>
            <th colspan="3">Polling Stations</th>
            </tr>
            
            <tr>
            <th size="2">Action</th>
            <th size="2">S.No.</th>
            <th>AC No</th>
            <th>Name</th>

            <th size="2">Male</th>
            <th size="2">Female</th>
            <th size="2">Third Gender</th>
            <th size="2">Total</th>

            <th size="2">Male</th>
            <th size="2">Female</th>
            <th size="2">Third Gender</th>
            <th size="2">Total</th>

            <th size="2">Regular</th>
            <th size="2">Auxillary</th>
            <th size="2">Total</th>
            </tr>
        </thead>
        <tbody id="acViewBody">
        <?php 
      if(!empty($acdata)){
      $j=0;
      foreach($acdata as $acdataList){ 
        $j++;  
       // echo '<pre>'; print_r($acdataList);
        ?>
         <input type="hidden" name="pc_no" value="{{ $ele_details->CONST_NO }}">
         <input type="hidden" name="st_code" value="{{ $ele_details->ST_CODE }}">
        <tr>
        <td><input type="checkbox" name="checkbox[]" value="{{ $acdataList->AC_NO }}" size="2"></td>
        <td><input type="hidden"   name="s_no"  value="{{$j}}" readonly="readonly" size="2"><span>{{$j}}</span></td> 
        <td><input type="hidden"  name="ac_no[]"  value="{{ $acdataList->AC_NO }}" readonly="readonly"><span>{{ $acdataList->AC_NO }} </span></td> 
         <td><input type="hidden" name="ac_name[]"  value="{{ $acdataList->AC_NAME }}" readonly="readonly"><span>{{ $acdataList->AC_NAME }}</span></td> 
         <td><input type="text"  maxlength='5' name="gen_male[{{ $acdataList->AC_NO }}]" id="gen_male" readonly="readonly" value="<?php echo $acdataList->gen_m=empty($acdataList->gen_m) ? '':$acdataList->gen_m; ?>" size="2"></td> 
         <td><input type="text"   maxlength='5' name="gen_female[{{ $acdataList->AC_NO }}]" id="gen_female" readonly="readonly" value="<?php echo $acdataList->gen_f=empty($acdataList->gen_f) ? '':$acdataList->gen_f; ?>" size="2"> </td>         
         <td><input type="text"  maxlength='5'  name="gen_third[{{ $acdataList->AC_NO }}]" id="gen_third" readonly="readonly" value="<?php echo $acdataList->gen_o=empty($acdataList->gen_o) ? '':$acdataList->gen_o; ?>" size="2"> </td>          
         <td><input type="text"  maxlength='5'  name="gen_total[{{ $acdataList->AC_NO }}]" id="gen_total" readonly="readonly" value="<?php echo $acdataList->gen_t=empty($acdataList->gen_t) ? '':$acdataList->gen_t; ?>" size="2"> </td>  

         <td><input type="text"  maxlength='5'  name="ser_male[{{ $acdataList->AC_NO }}]" id="ser_male" readonly="readonly" value="<?php echo $acdataList->ser_m=empty($acdataList->ser_m) ? '':$acdataList->ser_m; ?>" size="2"> </td> 
         <td><input type="text"  maxlength='5' name="ser_female[{{ $acdataList->AC_NO }}]" id="ser_female" readonly="readonly" value="<?php echo $acdataList->ser_f=empty($acdataList->ser_f) ? '':$acdataList->ser_f; ?>" size="2"> </td>          
         <td><input type="text"  maxlength='5'  name="ser_third[{{ $acdataList->AC_NO }}]" id="ser_third" readonly="readonly" value="<?php echo $acdataList->ser_o=empty($acdataList->ser_o) ? '':$acdataList->ser_o; ?>" size="2"> </td> 
         <td><input type="text"  maxlength='5'  name="ser_total[{{ $acdataList->AC_NO }}]" id="ser_total" readonly="readonly" value="<?php echo $acdataList->ser_t=empty($acdataList->ser_t) ? '':$acdataList->ser_t; ?>" size="2"> </td> 
         
         <td><input type="text"  maxlength='5' name="regular[{{ $acdataList->AC_NO }}]" id="regular" readonly="readonly" value="<?php echo $acdataList->polling_reg=empty($acdataList->polling_reg) ? '':$acdataList->polling_reg; ?>" size="2"> </td> 
         <td><input type="text"  maxlength='5' name="auxillary[{{ $acdataList->AC_NO }}]" id="auxillary" readonly="readonly" value="<?php echo $acdataList->polling_auxillary=empty($acdataList->polling_auxillary) ? '':$acdataList->polling_auxillary; ?>" size="2"> </td> 
         <td><input type="text"  maxlength='5' name="polling_total[{{ $acdataList->AC_NO }}]" id="polling_total" readonly="readonly" value="<?php echo $acdataList->polling_total=empty($acdataList->polling_total) ? '':$acdataList->polling_total; ?>" size="2"></span> </td> 
         </tr>
       <?php 
         }
       } ?>
       
        </tbody>
    </table>
        <?php  $url = URL::to("/");  ?>
             <div class="form-group float-right">  
                <input type="submit" value="Save" class="btn btn-primary">
                <!-- <input type="button" value="Delete" class="btn btn-primary" onclick="location.href = '{{$url}}/ropc/counting-finalized';"> -->
             </div>
        </form> 
    </div>
    </div>
  </div>
  </div>
  </section>
  </main>
@endsection

<script src="{{ asset('js/jquery.js')}}" type="text/JavaScript"></script> 
<script type="text/javascript">
   $(document).ready(function () {  
  //called when change the pc name
  jQuery("select[name='pc_no']").change(function(){
    var pc_no = jQuery(this).val();  
        jQuery.ajax({
            url: "{{url('/pcceo/getaclistbypc')}}",
            type: 'GET',
            data: {pc_no:pc_no},
            success: function(result){
              //alert(result);
              $('#acViewBody').html(result);;
                }
            });
        });
    $(':checkbox').click(function() {
    var checkbox = $(this);
    var row = checkbox.closest('tr');
    var inputText = $('input[type=text]', row);
    if (checkbox.is(':checked')) {
      inputText.removeAttr('readonly');
    }
    else {
      inputText.attr('readonly', 'readonly');
       
    }
});
    $("form").submit(function(){
		if ($('input:checkbox').filter(':checked').length < 1){
        alert("Please Check at least one row!");
		return false;
		}
    });

});
 </script>