@extends('candidate.common.app')
  @section('seo')
    <title>Absentee Voter Form - 12D</title>
  @endsection
  @section('style')
  <style type="text/css">
  #breadcrumb li, #breadcrumb li a { color: #e65482;  margin-bottom: 10px;}
    .display_none{ display: none; }
<!-- 	.body{    margin: 50px auto 0; padding: 40px;   box-shadow: 0px 0px 8px 1px #d5d5d5;} -->
  .form2d span {  display: inline-block;    }
  .form2d span input { border: none;  width: 100%;   border-bottom: 1px dashed #000; font-weight:600;}
  .form2d span input[type="text"]:focus { border-bottom: 1px dashed red;  outline: none;}
  td{padding:5px;}
  .form2d{    margin: 50px auto 0 auto;   padding: 20px;    box-shadow: 0 0 19px #d5d5d5;}
  .readonly{background:#fff;}
  
  @media only screen and (max-width:480px){
	  .form2d span{display:block;}
	  .form2d{padding:10px; margin:0; box-shadow:none;}
	
.statistics div[class*=col-] .card {
    padding:0px;
}
	  
  }
  </style>
  <style type="text/css">

</style>
  @endsection
@section('content')
{!!$header!!}
@include('candidate.common.breadcrumb')
<section class="statistics color-grey pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">


     <div class="col  pull-right  text-right">
   
   @if(isset($filter_buttons) && count($filter_buttons)>0)

        @foreach($filter_buttons as $button)
        <?php $but = explode(':',$button); ?>
        <span class="" style="margin-right: 10px;">
          <span><b>{!! $but[0] !!}:</b></span>
          <span class="badge badge-info">{!! $but[1] !!}</span>

        </span>

        @endforeach 
    
      @if(count($buttons)>0)
      @foreach($buttons as $button)
      <span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="{{ $button['name'] }}" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
      @endforeach
      @endif  


   
@endif    
    </div>
  </div>
</div>  

 <div class="row">

      <div class="col-md-12">
        <div class="card">
         <div class="card-header d-flex align-items-center">
           <h4>{{$heading_title}}</h4>
         </div>
         <div class="card-body">
         


     



<div class="form2d container">

  <div class="row"><div class="col">
<table border="0" id="form12d_content">
    <tbody>
     <tr><td align="center"><h3 style="text-align:center;">[FORM 12D]</h3></td></tr>
     <tr><td align="center">[See rule 27-C)</td></tr>
     <tr><td align="center">PART I</td></tr>
     <tr><td align="center"><b>Letter of intimation to Assistant Returning Officer</b></td></tr>
     <tr><td align="center"><b>(for absentee voters)</b></td></tr>
  

  <tr><td align="left">To</td></tr>
  <tr><td align="left">The Assistant Returning Officer,</td></tr>
  <tr><td align="left"><small>(for the notified class of electors)</small></td></tr>
  <tr><td align="left"><span class="border_bot" style=" "><input type="text" name="constituency_type" value="{{$ac_name}}" class="readonly" /></span> Parliamentary/Assembly constituency</td></tr>
  <tr><td align="left"><span class="border_bot" style=" "><input type="text" name="aro_signature" value="{{$ac_name}}"/></span><small>(designation and  address of ARO) </small></td></tr>
  <tr><td align="left">Sir, </td></tr>
  <tr><td align="left">I <span style=" "><input type="text" name="name" class="name readonly" value="{{$name}}"/></span>son/daughter/wife of <span style=" "><input type="text" name="father_name" id="father_name" class="readonly" value="{{$father_name}}"/></span> resident <span style=" "><input type="text" name="resident" id="resident" class="readonly" value="{{$village}}"/></span> village/mohall <span style=" "><input type="text"  name="village" id="village" class="readonly" value="{{$tehsil}}" /></span></td></tr>
  <tr><td align="left">Town/city/tehsil <span style=" "><input type="text" name="city" id="city" class="readonly" value="{{$dist_name}}" /></span> District <span style=" "><input type="text" name="dist_name" id="dist_name" class="readonly" value="{{$st_name}}"/></span>(State) belong to the class of absentee voter and</td></tr>
  <tr><td align="left">wish to cast my vote by post at  the election  to the  House of  the  People/Legislative Assembly from the</td></tr>
  <tr><td align="left"><span style=""><input type="text" name="constituency_type" value="{{$ac_name}}" id="constituency_type" class="readonly"/></span> Parliamentary/Assembly constituency. </td></tr>
  <tr><td align="left">My complete present postal address is as under:- <span style=" "><input name="postal_address" id="postal_address" class="readonly" value="{{$new_address}}"/></span></td></tr>
  <tr><td align="left">House/dwelling unit/tent number<span style=" "><input type="text" name="postal_house_no" id="postal_house_no" class="readonly" value="{{$new_house_no}}"/></span></td></tr>
  <tr><td align="left">Camp/mohalla/tent number<span style=" "><input type="text" name="postal_mohalla" id="postal_mohalla" class="readonly" value="{{$new_village}}"/></span></td></tr>
  <tr><td align="left">Ward/town/tehsil<span style="min-width:320px"><input type="text" name="postal_town" class="readonly" id="postal_town" value="{{$new_tehsil}}" /></span></td></tr>
  <tr><td align="left">District <span style="min-width:320px"><input type="text" name="postal_district" class="readonly" id="postal_district" value="{{$new_dist_name}}" />
  </span></td></tr>
  <tr><td align="left">State <span style=" "><input type="text" name="postal_state" class="readonly" id="postal_state" value="{{$new_st_name}}" /></span> PIN CODE <span style=" "><input type="text" name="postal_pincode" class="readonly" id="postal_pincode" value="{{$pincode}}" /></span></td></tr>
  <tr><td align="left">Mobile Phone No. (if available) <span style=" "><input type="text" class="readonly" name="mobile" id="mobile" value="{{$mobile}}" /></span></td></tr>
  <tr><td align="left">My name is entered at serial number <span style=" "><input type="text" class="readonly" name="serial_number" id="serial_number" value="{{$serial_number}}" /></span> in Part No <span style=" "><input type="text" name="part_no" class="readonly" id="part_no" value="{{$part_no}}" /></span> of the electoral roll for</td></tr>
  <tr><td align="left"><span class="border_bot" style=" "><input type="text" name="postal_constituency_type" value="{{$ac_name}}" class="readonly"/></span> Parliamentary/Assembly constituency</td></tr>




  <tr><td align="left"><sup>*</sup>I am <span class="border_bot" style=" ;"><input type="text" name="age" id="age" class="readonly" value="{{$age}}" /></span>years of age/am a person with disability, and am not in a position to go to the polling station to cast vote.</td></tr>
  <tr><td align="left">It is requested that postal ballot paper may be issued to me as absentee voter for the above election</td></tr>


  <tr><td align="right">Yours faithfully,</td></tr> 
  <tr><td align="right"><span style=" "><input type="text" name="name" class="name" class="readonly" value="{{$name}}" /></span></td></tr>

  
  </tbody>
  <tfoot>
      
    <tr><td align="right">
   <button type="button" id="absentee-submit" class="btn btn-primary">Submit</button>
 </td>
 </tr>

  </tfoot>
</table>
</div></div>
</div>

</div>

</div>
</div>
</div>    


 </section>     

{!!$footer!!}
@endsection
@section('footerscript')
<script type="text/javascript">
  $(document).ready(function(e){
    $('#absentee-submit').click(function(e){
      $.ajax({
        url: "{!! $post_12d_submit !!}",
        type: 'POST',
        data: '_token=<?php echo csrf_token() ?>',
        dataType: 'json', 
        beforeSend: function() {
          $('.error_message').remove();
          $('#absentee-submit').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {
          $('.loading_spinner').remove();
        },        
        success: function(json) {   
          if(json['success'] == false){
             error_messages(json['message']);
          }else{
            window.location.href = json.redirect_to;
          }
        },
        error: function(data) {
          var errors = data.responseJSON;
        }
      });
    });
  });
</script>
@endsection