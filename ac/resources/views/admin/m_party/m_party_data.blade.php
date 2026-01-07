@extends('admin.layouts.ac.theme')
@section('content')
<style type="text/css">
  .loader {
   position: fixed;
   left: 50%;
   right: 50%;
   border: 16px solid #f3f3f3; /* Light grey */
   border-top: 16px solid #3498db; /* Blue */
   border-radius: 50%;
   width: 120px;
   height: 120px;
   animation: spin 2s linear infinite;
   z-index: 99999;
  }
      @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
    }
  </style>

  <div class="loader" style="display:none;"></div>
  
<section class="statistics color-grey pt-4 pb-2">
<div class="container-fluid">
  <div class="row">
  <div class="col">
   <h4>M Party data</h4>
  </div>
  </div>
</div>  
</section>
<section class="dashboard-header section-padding">
  <div class="container-fluid">
  
        
      <form id="generate_report_id" autocomplete="off" class="row" method="get" onsubmit="return false;">
    <div class="form-group col-md-6">
         <label>Datewise Filter</label> &nbsp; <input value="" id="date_range" name="date_range" type="text" class="ranges form-control" placeholder="Date Range" />
    </div>


    

          <div class="form-group col-md-3"> <label>Type </label> 
          
            <select name="type" id="type" class="form-control" onchange ="search_by()">
            <option value="all">All</option>
            <option value="new">New</option>
            <option value="modified">Modified</option>
            </select>
          </div>
         
        
        </form>   
  
    
  </div>
</section>

<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
     <div class="page-contant">
     <div class="random-area">
  <br>

  <div class="row">
    <div class="col text-left">
      <label><b>Total records</b> : {{ \App\Http\Controllers\Admin\EciMPartyMasterData::getTotalMPartyRecords() }} </label>
      </div>
  </div>
  <div class="row">
      <div class="col text-left">
      <label><b>Total New</b> : {{ \App\Http\Controllers\Admin\EciMPartyMasterData::EciMPartyNewData() }}</label>
      </div>
  </div>
  <div class="row">
    <div class="col text-left">
      <label><b>Total Modified</b> : {{ \App\Http\Controllers\Admin\EciMPartyMasterData::EciMPartyModifiedData() }}</label>
    </div>
  </div>

  <div class="row mb-3">
    
    </div>
  <div class="col text-right">
    @if(count($results)>0)
    <span class="report-btn" id="export-csv-btn"><a class="btn btn-primary" href="{{url('eci/m_party_export_to_excel',['from_date'=>$from_date,'from_to'=>$from_to,'rp_str'=>$rp_str])}}" title="Download Excel" target="_blank">Export Excel</a></span>
    <span class="report-btn" id="export-pdf-btn"><a class="btn btn-primary" href="{{url('eci/m_party_export_to_pdf',['from_date'=>$from_date,'from_to'=>$from_to,'rp_str'=>$rp_str])}}" title="Download PDF" target="_blank">Export PDF</a></span>
    @endif
  </div>
    </div>
           <div class="table-responsive">
            <table id="list-table" class="table table-striped table-bordered table-hover mt-8"
            style="width:100%">
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
         </div><!-- End Of  table responsive -->  
      </div><!-- End Of intra-table Div -->   
        
         
      </div><!-- End Of random-area Div -->
      
    </div><!-- End OF page-contant Div -->
    </div>      
  </div><!-- End Of parent-wrap Div -->
  </div> 


<script type="text/javascript">
$(document).ready(function() {  
  $('#date_range').daterangepicker({
    <?php if(isset($from) && isset($to)){ ?>
      startDate: moment('<?php echo $from ?>'),
      endDate: moment('<?php echo $to ?>'),
    <?php } ?>
      ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          //  'Last 14 Days': [moment().subtract(13, 'days'), moment()] ,          
          //  'This Month': [moment().startOf('month'), moment().endOf('month')],
          //  'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      maxDate: new Date()
  });
}); 


<?php if(!isset($from) && !isset($to)){ ?>
$(document).ready(function(e){
    $('#date_range').val('');
});
<?php } ?>
</script>

<script type="text/javascript">

jQuery(document).ready(function(e){
  jQuery('#date_range').change(function(e){
    filter();
  });
});


function filter(){
  var url = "searchMPartyData";
  var query = '';
  
    
  

    var val=  jQuery('#date_range').val();
    var timeInterval= val.split('-'); 
    if(timeInterval[0] !='' && timeInterval[1] != ''){
      var from = moment(timeInterval[0]).format('DD-MM-YYYY');
      var to = moment(timeInterval[1]).format('DD-MM-YYYY');
      query += "&from="+from+'&to='+to;
    }
    window.location.href = url+'?'+query.substring(1);
}



  </script>


<script type="text/javascript">
  function search_by(){
  var full_url = "<?php echo url()->full(); ?>";
  var res = full_url.split("?");
  var from_and_to=res[1];
  
  var query = '';
    // if(jQuery("#type").val() != '' && jQuery("#type").val() != 'undefined'){
    // query = "type="+jQuery("#type").val();
    // }

 var type_val=jQuery("#type").val();
  if(from_and_to===undefined)
  {
    var url="searchMPartyData"+'?'+'&type='+type_val;
  }
  else{
    var url='searchMPartyData'+'?'+from_and_to+'&type='+type_val;
  }
  

// alert(url);

  

  
    window.location.href = url+'?'+query.substring(1);
}
</script>
@endsection