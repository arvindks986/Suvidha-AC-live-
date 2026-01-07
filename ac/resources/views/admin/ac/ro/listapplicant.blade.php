@extends('admin.layouts.theme')
@section('title', 'List Applicant')
@section('content') 
@include('admin.includes.list_script')

<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start Child Area Div --> 
    <div class="child-area">
     <!-- Start Page Content Div -->  
    <div class="page-contant">
          <div class="head-title">
        <h3><i><img src="{{ asset('admintheme/images/icons/tab-icon-002.png')}}" /></i>List of All Applicants</h3>
      </div>
      
   @if(isset($lists)) 
   
       <div class="col-md-12" align="left">
           <table><tr><th> @if($checkval==1)
                      <h4> All AC's are already Finalized. NO Further action can be taken now.</h4> 
            @endif
          </th></tr> </table>
       </div> 
      <?php  if($ele_details->CONST_TYPE=="AC") { 
                    $v= 'ac_no'; $m=$ele_details->CONST_NO;
                    }
            elseif($ele_details->CONST_TYPE=="PC") { 
                    $v= 'pc_no'; $m=$ele_details->CONST_NO;
                 }
      $total=\app(App\adminmodel\CandidateNomination::class)->where(['ST_CODE' =>$user_data->ST_CODE,$v =>$m])->get()->count();
         
      $totalverify= \app(App\adminmodel\CandidateNomination::class)->where(['ST_CODE' =>$user_data->ST_CODE,$v =>$m])->where(['application_status' =>'verified'])->get()->count() ;
      $totalrec=\app(App\adminmodel\CandidateNomination::class)->where(['ST_CODE' =>$user_data->ST_CODE,$v =>$m])->where(['application_status' =>'receipt_generated'])->get()->count();
      $bal=\app(App\adminmodel\CandidateNomination::class)->where(['ST_CODE' =>$user_data->ST_CODE,$v =>$m])->where(['application_status' =>'applied'])->get()->count();
             
     ?>
        <div class="col-md-2">
          <p>Applied :- &nbsp;{{ $total }}</p>
           
        </div>
        <div class="col-md-2">
          <p>Verified :- &nbsp;{{ $totalverify }}</p>
        </div>
        <div class="col-md-2">
          <p>Receipt Generated :- &nbsp;{{ $totalrec }}</p>
        </div>
        <div class="col-md-2">
          <p>Not Verified :- &nbsp;{{ $bal }}</p>
        </div>
         
        <div class="col-md-4" align="right">
          <form name="frmstatus" id="frmstatus" method="GET"  action="{{url('roac/applicant') }}" > 
          <table><tr><th>Select Status :- </th><th>
            <select name="cand_status" id="cand_status" onchange="this.form.submit()">
              <option value="" @if($status=='') selected="selected" @endif>All</option>
              @foreach($status_list as $s)  
              @if($s->id<=2)  
              <option value="{{$s->id}}" @if($status==$s->id) selected="selected" @endif >{{$s->status}}</option>
              @endif
              @endforeach
               
            </select></th></tr>     
          </table></form>
        </div>
    <table id="example" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
              <th>Sl. No.</th><th>QR Code</th>
              <th>Name</th><th>Father's / Mother's Name / Husband's Name</th>
              <th>Date of Submission</th><th>Party</th>
              <th>Symbol</th><th>Party type</th>
              <th>Current Status</th><th>Affidavit</th> 
            </tr>
        </thead>
        <tbody>
            <?php $i=1; $url = URL::to("/");  ?>
      @foreach ($lists as $key=>$list)
           <tr><td>{{$i}}</td>
            <?php 
             $affidavit= \app(App\adminmodel\Candidateaffidavit::class)->where(['nom_id' =>$list->nom_id])->first(); 
             $party= \app(App\adminmodel\PartyMaster::class)->where(['CCODE' =>$list->party_id])->first();
             $symb= \app(App\adminmodel\SymbolMaster::class)->where(['SYMBOL_NO' =>$list->symbol_id])->first();
             $s= \app(App\commonModel::class)->getnameBystatusid($list->application_status);
            ?>   
            <td>@if($checkval==0)<a href="{{$url}}/ro/qrscan/{{$list->qrcode}}"> {{$list->qrcode}} </a> @else {{$list->qrcode}}@endif</td>
                <td>{{$list->cand_name}}
                  @if($list->cand_image!='')
                      <img src="{{$url.'/'.$list->cand_image}}" alt="no images" width="50" height="60">
                    @else 
                      <img src="{{ asset('admintheme/images/User-Icon.png') }}" alt="" width="50" height="60">
                    @endif 
                  </td> 
                  <td>{{$list->candidate_father_name}}</td>
                  <td>{{date("d-m-Y",strtotime($list->date_of_submit))}}</td> 
                  <td>@if(!empty($party)){{$party->PARTYNAME}} @endif</td>
                  <td>@if(!empty($symb)){{$symb->SYMBOL_DES}} @endif</td>
                  <td>@if(!empty($party)) @if($party->PARTYTYPE=="N") National   @endif @if($party->PARTYTYPE=="S") State  @endif @if($party->PARTYTYPE=="U") Unrecognized  @endif 
                    @if($party->PARTYTYPE=="Z") Independent  @endif @endif</td>
                  <td>{{ucfirst($s)}} <br>@if($list->application_status>=3) <a href="{{$url}}/ro/reprint-receipt/{{$list->nom_id}}">Re-Print</a> @endif</td>
                  <td>@if(!empty($affidavit->affidavit_name)) <a href="{{asset($affidavit->affidavit_path)}}" target="_blank" >{{$affidavit->affidavit_name}} </a> @endif</td> 
              </tr>
              <?php $i++; ?>
          @endforeach
            
        </tbody>
        <!--<tfoot>
            <tr>
              <th>Sl. No.</th><th>QR Code</th>
              <th>Name</th><th>Father's Name</th>
              <th>Date of Submission</th><th>Party</th>
              <th>Symbol</th><th>Party Type</th>
              <th>Status</th><th>Affidavit</th> 
            </tr>
        </tfoot>-->
    </table>
   @else
    <div class="col-md-6">
          <p>No Application  Founds </p>
           
        </div> 
   @endif
  </div><!-- End OF page-contant Div -->
  </div>          
  </div><!-- End Of parent-wrap Div -->
  </div> 
@endsection