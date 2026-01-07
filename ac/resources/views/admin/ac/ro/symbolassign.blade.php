@extends('admin.layouts.pc.theme')
@section('title', 'Candidate Nomination Details')
@section('bradcome', 'Symbol Assign to Candidate')
@section('content') 
 

<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start Child Area Div --> 
    <div class="child-area">
     <!-- Start Page Content Div -->  
    <div class="page-contant">
          <div class="head-title">
        <h3><i><img src="{{ asset('admintheme/images/icons/tab-icon-002.png')}}" /></i>Assign Symbol </h3>
      </div>
       @if (\Session::has('success_mes'))
          <div class="alert alert-success"> {!! \Session::get('success_mes') !!} </div>
      @endif
      @if (\Session::has('error_mes'))
         <div class="alert alert-danger"> {!! \Session::get('error_mes') !!} </div>
      @endif
       
      <?php 
      $sym=\app(App\adminmodel\SymbolMaster::class)->get(); //->where(['Ind_Symbol' =>'T'])->orwhere(['Ind_Symbol' =>'F'])
      
      ?>

       <div class="datatable-wrap">
        <form class="form-horizontal" id="symbol_form" method="POST"  action="{{url('ropc/updatesymbol') }}" >
                {{ csrf_field() }}   
        <table cellspacing="10" cellpadding="10">
             <input type="hidden" name="candidate_id" value="{{$lists->candidate_id}}">
             <input type="hidden" name="nom_id" value="{{$lists->nom_id}}">
           <tr><td> Candidate Name : - </td> <td>{{$lists->cand_name}} </td></tr>
           <tr><td> Candidate Father's Name : - </td> <td>{{$lists->candidate_father_name}} </td></tr>
           <tr><td> Select Symbol : - <span class="pagespanred">*</span></td> <td> 
            <select name="symbol" id="symbol" style="width:200px;">
             <option value="" selected="selected">Selected</option>
                         @foreach($sym as $s)
                           <option value="{{ $s->SYMBOL_NO }}">{{ $s->SYMBOL_DES }}</option>
                             @endforeach 
            </select> <span id="err"  style="color:red;"></span>
            @if ($errors->has('symbol'))
                  <span style="color:red;"><strong>{{ $errors->first('symbol') }}</strong></span>
               @endif  </td></tr>
         </table>     
             
              <div class="btns-actn">
                 <input type="submit" value="Submit">
              </div>
            </form>
      </div><!-- End Of datatable-wrap Div --> 
    
  </div><!-- End OF page-contant Div -->
  </div>          
  </div><!-- End Of parent-wrap Div -->
  </div> 
@endsection
<script src="{{ asset('js/jquery.js')}}" type="text/JavaScript"></script> 
<script>
$(document).ready(function(){
 
  $("#symbol_form").submit(function(){
   
    if($("#symbol").val()=="")
    {
      $("#err").text("Please select symbol");
      $("#symbol").focus();
      return false;
    }
     
    });
  });
</script>