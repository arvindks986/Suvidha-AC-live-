@extends('admin.layouts.ac.report-theme')
@section('title', 'Electors Polling Stations')
@section('content') 
  <?php  $st=getstatebystatecode($user_data->st_code);   
        $distArr=getdistrictbydistrictno($user_data->st_code,$user_data->dist_no);
        $j=0;
       //dd($distArr);
  ?> 
 <style>
 input[type="number"] {
   
    max-width: 81px;
}
 td {
    padding: 3px!important; vertical-align:middle; font-size:11px;
}
th {
    text-align: left; padding:2px; font-size:14px;
}
.table td span {
    padding: 2px;
}
input{    border: none;
    height: 23px;
    margin-top: 5px;
    padding: 2px;
    font-size:12px;
    }
td:first-child {
    text-align: center;
    vertical-align: middle;
}
 .table-responsive::-webkit-scrollbar-track
{
	background-color: #f2f2f2;
}

.table-responsive::-webkit-scrollbar{
	height: 10px;
}


input.btn.btn-primary {
    height: auto;
}
 table, .table{margin-bottom:0px!important;}
 .table-responsive{}
 </style>
<main role="main" class="inner cover mb-3">
<section>
  <div class="container mt-5">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
            <div class=" card-header">
                <div class=" row">
                  <div class="col"><h4>Electors Polling Station Info</h4></div> 
                   <div class="col"><p class="mb-0 text-right">
                   <b class="bolt">State:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; 
                   <b class="bolt">District:</b> 
                   <span class="badge badge-info">{{$distArr->DIST_NAME}}</span>&nbsp;&nbsp; </p>
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
     <form class="form-horizontal" id="electorsPollingStation_form" method="POST"  action="{{url('acdeo/electors-deopollingstation') }}" >
        {{ csrf_field() }} 
		<div class="table-responsive">
       <table   class="table  table-bordered report_tabs" style="width:100%">
      
        
        <thead>
            <tr>
            <th colspan="4">AC No & Name </th>
            <th colspan="4">General Electors</th>
            <th colspan="4">Service Electors</th>
            <th colspan="3">Polling Stations</th>
            </tr>
            
            <tr>
            <th>Action</th>
            <th>S.No.</th>
            <th>AC No</th>
            <th>Name</th>

            <th size="5">Male</th>
            <th size="5">Female</th>
            <th size="5">Third <br>Gender</th>
            <th style="width:50px;">Total</th>

            <th size="5">Male</th>
            <th size="5">Female</th>
            <th size="5">Third <br> Gender</th>
            <th>Total</th>

            <th size="5">Regular</th>
            <th size="5">Auxillary</th>
            <th>Total</th>
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
         <input type="hidden" name="dist_no" value="{{ $distArr->DIST_NO }}">
         <input type="hidden" name="st_code" value="{{ $st->ST_CODE }}">
        <tr>
        <td><input type="checkbox" name="checkbox[]" value="{{ $acdataList->AC_NO }}" size="5"></td>
        <td><input type="hidden"   name="s_no"  value="{{$j}}" readonly="readonly" size="5"><span>{{$j}}</span></td> 
        <td><input type="hidden"  name="ac_no[]"  value="{{ $acdataList->AC_NO }}" readonly="readonly"><span>{{ $acdataList->AC_NO }} </span></td> 
         <td><input type="hidden" name="ac_name[]"  value="{{ $acdataList->AC_NAME }}" size="5" readonly="readonly"><span>{{ $acdataList->AC_NAME }}</span></td> 
         <td><input type="text"  onpaste="return false;" onCopy="return false"  onkeypress="return onlyNumbers();" maxlength='7' name="gen_male[{{ $acdataList->AC_NO }}]" id="gen_male" readonly="readonly" value="<?php echo $acdataList->gen_m=empty($acdataList->gen_m) ? '':$acdataList->gen_m; ?>" size="5"></td> 
         <td><input type="text"   onpaste="return false;" onCopy="return false" onkeypress="return onlyNumbers();"maxlength='7' name="gen_female[{{ $acdataList->AC_NO }}]" id="gen_female" readonly="readonly" value="<?php echo $acdataList->gen_f=empty($acdataList->gen_f) ? '':$acdataList->gen_f; ?>" size="5"> </td>         
         <td><input type="text"   onpaste="return false;" onCopy="return false" onkeypress="return onlyNumbers();" maxlength='7'  name="gen_third[{{ $acdataList->AC_NO }}]" id="gen_third" readonly="readonly" value="<?php echo $acdataList->gen_o=empty($acdataList->gen_o) ? '':$acdataList->gen_o; ?>" size="5"> </td>          
         <td><input type="number"   name="gen_total[{{ $acdataList->AC_NO }}]" id="gen_total" readonly="readonly" value="<?php echo $acdataList->gen_t=empty($acdataList->gen_t) ? '':$acdataList->gen_t; ?>"> </td>  

         <td><input type="text"  onpaste="return false;" onCopy="return false" onkeypress="return onlyNumbers();" maxlength='7'  name="ser_male[{{ $acdataList->AC_NO }}]" id="ser_male" readonly="readonly" value="<?php echo $acdataList->ser_m=empty($acdataList->ser_m) ? '':$acdataList->ser_m; ?>" size="5"> </td> 
         <td><input type="text"  onpaste="return false;" onCopy="return false" onkeypress="return onlyNumbers();" maxlength='7' name="ser_female[{{ $acdataList->AC_NO }}]" id="ser_female" readonly="readonly" value="<?php echo $acdataList->ser_f=empty($acdataList->ser_f) ? '':$acdataList->ser_f; ?>" size="5"> </td>          
         <td><input type="text"  onpaste="return false;" onCopy="return false" onkeypress="return onlyNumbers();" maxlength='7'  name="ser_third[{{ $acdataList->AC_NO }}]" id="ser_third" readonly="readonly" value="<?php echo $acdataList->ser_o=empty($acdataList->ser_o) ? '':$acdataList->ser_o; ?>" size="5"> </td> 
         <td><input type="number"    name="ser_total[{{ $acdataList->AC_NO }}]" id="ser_total" readonly="readonly" value="<?php echo $acdataList->ser_t=empty($acdataList->ser_t) ? '':$acdataList->ser_t; ?>" size="5"> </td> 
         
         <td><input type="text"  onpaste="return false;" onCopy="return false" onkeypress="return onlyNumbers();" maxlength='7' name="regular[{{ $acdataList->AC_NO }}]" id="regular" readonly="readonly" value="<?php echo $acdataList->polling_reg=empty($acdataList->polling_reg) ? '':$acdataList->polling_reg; ?>" size="5"> </td> 
         <td><input type="text" onpaste="return false;" onCopy="return false" onkeypress="return onlyNumbers();"  maxlength='7' name="auxillary[{{ $acdataList->AC_NO }}]" id="auxillary" readonly="readonly" value="<?php echo $acdataList->polling_auxillary=empty($acdataList->polling_auxillary) ? '':$acdataList->polling_auxillary; ?>" size="5"> </td> 
         <td><input type="number"   name="polling_total[{{ $acdataList->AC_NO }}]" id="polling_total" readonly="readonly" value="<?php echo $acdataList->polling_total=empty($acdataList->polling_total) ? '':$acdataList->polling_total; ?>" size="5"></span> </td> 
         </tr>
       <?php 
         }
       } ?>
       
        </tbody>
    </table>
	</div>
        <?php  $url = URL::to("/");  ?>
             <div class="form-group float-right">  
                <input type="submit" value="Save" class="btn btn-primary">
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
function onlyNumbers(evt)
{
var e = event || evt; // for trans-browser compatibility
var charCode = e.which || e.keyCode;
if (charCode > 31 && (charCode < 48 || charCode > 57))
    return false;
return true;

}
   $(document).ready(function () { 
  //called when change the pc name
 /* jQuery("select[name='pc_no']").change(function(){
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
        });*/
    $(':checkbox').click(function() {
    var checkbox = $(this);
    var row = checkbox.closest('tr').addClass("active");;
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