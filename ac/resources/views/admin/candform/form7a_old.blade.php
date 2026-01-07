@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Form 7A Details')
@section('content')
 <?php   
         $url = URL::to("/"); $j=0;
    ?>
 <style type="text/css">
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
              div.dataTables_wrapper {margin:0 auto;} 
  </style>
  
  <main role="main" class="inner cover mb-3">
  <section class="mt-3">
  <div class="container">
<div class="row">
  				
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                <div class="col"> <h4>Form 7A Detils Updated by RO ( View and Update)</h4> </div> 
				<div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> 
					<span class="badge badge-info">{{$st_name}}</span> &nbsp;&nbsp; 
					<b class="bolt">AC Name:</b> <span class="badge badge-info">{{$ac_name}}</span> </p>
				</div>
         
                </div>
                </div>
   <div class="row">
    <div class="col">
        @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
        @endif
         @if (session('error_mes'))
          <div class="alert alert-danger"> {{session('error_mes') }}</div>
        @endif
         @if (\Session::has('success'))
			<div class="alert alert-success">
				<ul>
					<li>{!! \Session::get('success') !!}</li>
				</ul>
			</div>
		@endif
      
         
    </div>
    </div>
   		
       
    <div class="card-border1 p-4">  
       <form class="form-horizontal" id="election_form" method="post" action="{{url('roac/updated-form7A-details')}}" enctype="multipart/form-data" autocomplete='off'>
  {{csrf_field()}}
		<input type="hidden" name="id" value="{{$record->id}}" id='test'/>
		 <div class="form-group row">
					<div class="col">
					<label for="candidate_id" class="col-form-label">Form 7A Detils In English </label> 
				 </div>
				 <div class="col">
					<label for="candidate_id" class="col-form-label">Form 7A Detils In Vernacular (state Vernacular Language-{{$state_language}})</label> 
				 </div>
		       </div>
	         <div class="line"></div>   
			 <div class="form-group row">
					<div class="col">
					   <input type='text' readonly="readonly"  name="title1e" id="title1e" class="form-control" value="{{$record->title1}}"/>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vtitle1" id="vtitle1" class="form-control"  
					  value="@if(isset($record)){{isset($record->vtitle1)?$record->vtitle1:old('vtitle1')}} @endif"/>
  					</div>
 
					@if ($errors->has('vtitle1'))
                                     <span style="color:red;">{{ $errors->first('vtitle1') }}</span>
                                  @endif
					<span id="errmsg1" class="text-danger"></span>
								
					</div>
  				<div class="line"></div> 
		       <div class="form-group row">
					<div class="col">
					   <input type='text'  readonly="readonly" name="title2" id="title2" class="form-control" value="{{$record->title2}}"/>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vtitle2" id="vtitle2" class="form-control" 
					  value="@if(isset($record)){{isset($record->vtitle2)?$record->vtitle2:old('vtitle2')}} @endif"/>
  					</div>
 
					@if ($errors->has('vtitle2'))
                                     <span style="color:red;">{{ $errors->first('vtitle2') }}</span>
                                  @endif
					<span id="errmsg2" class="text-danger"></span>
								
					</div>
  				<div class="line"></div> 

				 <div class="form-group row">
					<div class="col">
					   <input type='text'   readonly="readonly" name="title3" id="title3" class="form-control" value="{{$record->title3}}"/>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vtitle3" id="vtitle3" class="form-control" 
					  value="@if(isset($record)){{isset($record->vtitle3)?$record->vtitle3:old('vtitle3')}} @endif"/>
  					</div>
 
					@if ($errors->has('vtitle3'))
                                     <span style="color:red;">{{ $errors->first('vtitle3') }}</span>
                                  @endif
					<span id="errmsg3" class="text-danger"></span>
								
					</div>
  				<div class="line"></div> 

				  <div class="form-group row">
					<div class="col">
					   <input type='text'   readonly="readonly" name="title4" id="title4" class="form-control" value="{{$record->title4}}"/>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vtitle4" id="vtitle4" class="form-control" 
					  value="@if(isset($record)){{isset($record->vtitle4)?$record->vtitle4:old('vtitle4')}} @endif"/>
  					</div>
 
					@if ($errors->has('vtitle4'))
                                     <span style="color:red;">{{ $errors->first('vtitle4') }}</span>
                                  @endif
					<span id="errmsg4" class="text-danger"></span>
								
					</div>
			   <div class="line"></div>
  				<div class="form-group row">
					<div class="col">
					   <input type='text'   readonly="readonly" name="header1" id="header1" class="form-control" value="{{$record->header1}}"/>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vheader1" id="vheader1" class="form-control" 
					  value="@if(isset($record)){{isset($record->vheader1)?$record->vheader1:old('vheader1')}} @endif"/>
  					</div>
 
					@if ($errors->has('vheader1'))
                                     <span style="color:red;">{{ $errors->first('vheader1') }}</span>
                                  @endif
					<span id="errmsg5" class="text-danger"></span>
								
					</div>
				<div class="line"></div>

				<div class="form-group row">
					<div class="col">
					   <input type='text'  readonly="readonly"  name="header2" id="header2" class="form-control" value="{{$record->header2}}"/>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vheader2" id="vheader2" class="form-control" 
					  value="@if(isset($record)){{isset($record->vheader2)?$record->vheader2:old('vheader2')}} @endif"/>
  					</div>
 
					@if ($errors->has('vheader2'))
                                     <span style="color:red;">{{ $errors->first('vheader2') }}</span>
                                  @endif
					<span id="errmsg6" class="text-danger"></span>
								
					</div>
				<div class="line"></div>

				<div class="form-group row">
					<div class="col">
					   <input type='text'  readonly="readonly"  name="header3" id="header3" class="form-control" value="{{$record->header3}}"/>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vheader3" id="vheader3" class="form-control" 
					  value="@if(isset($record)){{isset($record->vheader3)?$record->vheader3:old('vheader3')}} @endif"/>
  					</div>
 
					@if ($errors->has('vheader3'))
                                     <span style="color:red;">{{ $errors->first('vheader3') }}</span>
                                  @endif
					<span id="errmsg7" class="text-danger"></span>
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					   <input type='text'  readonly="readonly"  name="header4" id="header4" class="form-control" value="{{$record->header4}}"/>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vheader4" id="vheader4" class="form-control" 
					  value="@if(isset($record)){{isset($record->vheader4)?$record->vheader4:old('vheader4')}} @endif"/>
  					</div>
 
					@if ($errors->has('vheader4'))
                                     <span style="color:red;">{{ $errors->first('vheader4') }}</span>
                                  @endif
					<span id="errmsg8" class="text-danger"></span>
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					   <input type='text'  readonly="readonly" name="header5" id="header5" class="form-control" value="{{$record->header5}}"/>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vheader5" id="vheader5" class="form-control" 
					  value="@if(isset($record)){{isset($record->vheader5)?$record->vheader5:old('vheader5')}} @endif"/>
  					</div>
 
					@if ($errors->has('vheader5'))
                                     <span style="color:red;">{{ $errors->first('vheader5') }}</span>
                                  @endif
					<span id="errmsg9" class="text-danger"></span>
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					   <input type='text'   readonly="readonly" name="subheader1" id="subheader1" class="form-control" value="{{$record->subheader1}}"/>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vsubheader1" id="vsubheader1" class="form-control" 
					  value="@if(isset($record)){{isset($record->vsubheader1)?$record->vsubheader1:old('vsubheader1')}} @endif"/>
  					</div>
 
					@if ($errors->has('vsubheader1'))
                                     <span style="color:red;">{{ $errors->first('vsubheader1') }}</span>
                                  @endif
					<span id="errmsg18" class="text-danger"></span>
								
					</div>
				<div class="line"></div>

				<div class="form-group row">
					<div class="col">
					   <input type='text'  readonly="readonly"  name="subheader2" id="subheader2" class="form-control" value="{{$record->subheader2}}"/>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vsubheader2" id="vsubheader2" class="form-control" 
					  value="@if(isset($record)){{isset($record->vsubheader2)?$record->vsubheader2:old('vsubheader2')}} @endif"/>
  					</div>
 
					@if ($errors->has('vsubheader2'))
                                     <span style="color:red;">{{ $errors->first('vsubheader2') }}</span>
                                  @endif
					<span id="errmsg19" class="text-danger"></span>
								
					</div>
				<div class="line"></div>

				<div class="form-group row">
					<div class="col">
					   <input type='text'  readonly="readonly"  name="subheader3" id="subheader3" class="form-control" value="{{$record->subheader3}}"/>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vsubheader3" id="vsubheader3" class="form-control" 
					  value="@if(isset($record)){{isset($record->vsubheader3)?$record->vsubheader3:old('vsubheader3')}} @endif"/>
  					</div>
 
					@if ($errors->has('vsubheader3'))
                                     <span style="color:red;">{{ $errors->first('vsubheader3') }}</span>
                                  @endif
					<span id="errmsg20" class="text-danger"></span>
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					   <input type='text'  readonly="readonly"  name="subheader4" id="subheader4" class="form-control" value="{{$record->subheader4}}"/>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vsubheader4" id="vsubheader4" class="form-control" 
					  value="@if(isset($record)){{isset($record->vsubheader4)?$record->vsubheader4:old('vsubheader4')}} @endif"/>
  					</div>
 
					@if ($errors->has('vsubheader4'))
                                     <span style="color:red;">{{ $errors->first('vsubheader4') }}</span>
                                  @endif
					<span id="errmsg21" class="text-danger"></span>
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					   <input type='text'  readonly="readonly" name="subheader5" id="subheader5" class="form-control" value="{{$record->subheader5}}"/>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vsubheader5" id="vsubheader5" class="form-control" 
					  value="@if(isset($record)){{isset($record->vsubheader5)?$record->vsubheader5:old('vsubheader5')}} @endif"/>
  					</div>
 
					@if ($errors->has('vsubheader5'))
                                     <span style="color:red;">{{ $errors->first('vsubheader5') }}</span>
                                  @endif
					<span id="errmsg22" class="text-danger"></span>
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					   <input type='text'  readonly="readonly" name="middle_title1" id="middle_title1" class="form-control" value="{{$record->middle_title1}}"/>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vmiddle_title1" id="vmiddle_title1" class="form-control" 
					  value="@if(isset($record)){{isset($record->vmiddle_title1)?$record->vmiddle_title1:old('vmiddle_title1')}} @endif"/>
  					</div>
 
					@if ($errors->has('vmiddle_title1'))
                                     <span style="color:red;">{{ $errors->first('vmiddle_title1') }}</span>
                                  @endif
					<span id="errmsg10" class="text-danger"></span>
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					   <input type='text'  readonly="readonly" name="middle_title2" id="middle_title2" class="form-control" value="{{$record->middle_title2}}"/>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vmiddle_title2" id="vmiddle_title2" class="form-control" 
					  value="@if(isset($record)){{isset($record->vmiddle_title2)?$record->vmiddle_title2:old('vmiddle_title2')}} @endif"/>
  					</div>
 
					@if ($errors->has('vmiddle_title2'))
                                     <span style="color:red;">{{ $errors->first('vmiddle_title2') }}</span>
                                  @endif
					<span id="errmsg11" class="text-danger"></span>
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					   <input type='text'  readonly="readonly" name="middle_title3" id="middle_title3" class="form-control" value="{{$record->middle_title3}}"/>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vmiddle_title3" id="vmiddle_title3" class="form-control" 
					  value="@if(isset($record)){{isset($record->vmiddle_title3)?$record->vmiddle_title3:old('vmiddle_title3')}} @endif"/>
  					</div>
 
					@if ($errors->has('vmiddle_title3'))
                                     <span style="color:red;">{{ $errors->first('vmiddle_title3') }}</span>
                                  @endif
					<span id="errmsg12" class="text-danger"></span>
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					   <input type='text'  readonly="readonly" name="footer1" id="footer1" class="form-control" value="{{$record->footer1}}"/>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vfooter1" id="vfooter1" class="form-control" 
					  value="@if(isset($record)){{isset($record->vfooter1)?$record->vfooter1:old('vfooter1')}} @endif"/>
  					</div>
 
					@if ($errors->has('vfooter1'))
                                     <span style="color:red;">{{ $errors->first('vfooter1') }}</span>
                                  @endif
					<span id="errmsg13" class="text-danger"></span>
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					   <input type='text'  readonly="readonly" name="footer2" id="footer2" class="form-control" value="{{$record->footer2}}"/>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vfooter2" id="vfooter2" class="form-control" 
					  value="@if(isset($record)){{isset($record->vfooter2)?$record->vfooter2:old('vfooter2')}} @endif"/>
  					</div>
 
					@if ($errors->has('vfooter2'))
                                     <span style="color:red;">{{ $errors->first('vfooter2') }}</span>
                                  @endif
					<span id="errmsg14" class="text-danger"></span>
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					   <input type='text'  readonly="readonly" name="footer3" id="footer3" class="form-control" value="{{$record->footer3}}"/>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vfooter3" id="vfooter3" class="form-control" 
					  value="@if(isset($record)){{isset($record->vfooter3)?$record->vfooter3:old('vfooter3')}} @endif"/>
  					</div>
 
					@if ($errors->has('vfooter3'))
                                     <span style="color:red;">{{ $errors->first('vfooter3') }}</span>
                                  @endif
					<span id="errmsg15" class="text-danger"></span>
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					   <input type='text'  readonly="readonly" name="footer4" id="footer4" class="form-control" value="{{$record->footer4}}"/>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vfooter4" id="vfooter4" class="form-control" 
					  value="@if(isset($record)){{isset($record->vfooter4)?$record->vfooter4:old('vfooter4')}} @endif"/>
  					</div>
 
					@if ($errors->has('vfooter4'))
                                     <span style="color:red;">{{ $errors->first('vfooter4') }}</span>
                                  @endif
					<span id="errmsg16" class="text-danger"></span>
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					   <input type='text'  readonly="readonly" name="footer5" id="footer5" class="form-control" value="{{$record->footer5}}"/>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vfooter5" id="vfooter5" class="form-control" 
					  value="@if(isset($record)){{isset($record->vfooter5)?$record->vfooter5:old('vfooter5')}} @endif"/>
  					</div>
 
					@if ($errors->has('vfooter5'))
                                     <span style="color:red;">{{ $errors->first('vfooter5') }}</span>
                                  @endif
					<span id="errmsg17" class="text-danger"></span>
								
					</div>
				<div class="line"></div>
				<div class="form-group row" align="text-right">

						<button type="submit" id="candnomination" class="btn btn-primary custombtn" align="text-right">Upload</button></div>
			
				</div>
		</form>   
  
        

    </div>
    </div>
  
  
  </div>
  </div>
  </section>
   
   
  </main>
 
@endsection
 @section('script')

<script type="text/javascript">
   $(document).ready(function () {  
  //called when key is pressed in textbox vheader3
   
  $("#election_form").submit(function(){
    var vtitle1 = $('input[name="vtitle1"]').val();
    var vtitle2 = $('input[name="vtitle2"]').val();
    var vtitle3 = $('input[name="vtitle3"]').val(); 
    var vtitle4 = $('input[name="vtitle4"]').val(); 
    
    var vheader1 = $('input[name="vheader1"]').val();
    var vheader2 = $('input[name="vheader2"]').val();
    var vheader3 = $('input[name="vheader3"]').val(); 
    var vheader4 = $('input[name="vheader4"]').val(); 
    var vheader5 = $('input[name="vheader5"]').val();
    var vsubheader1 = $('input[name="vsubheader1"]').val();
    var vsubheader2 = $('input[name="vsubheader2"]').val();
    var vsubheader3 = $('input[name="vsubheader3"]').val(); 
    var vsubheader4 = $('input[name="vsubheader4"]').val(); 
    var vsubheader5 = $('input[name="vsubheader5"]').val();

    var vmiddle_title1 = $('input[name="vmiddle_title1"]').val();
    var vmiddle_title2 = $('input[name="vmiddle_title2"]').val(); 
    var vmiddle_title3 = $('input[name="vmiddle_title3"]').val(); 

    var vfooter1 = $('input[name="vfooter1"]').val();
    var vfooter2 = $('input[name="vfooter2"]').val();
    var vfooter3 = $('input[name="vfooter3"]').val(); 
    var vfooter4 = $('input[name="vfooter4"]').val(); 
    var vfooter5 = $('input[name="vfooter5"]').val();

    var is_error = false;
         
  if(vtitle1.trim() == ''){
        $('#errmsg1').html('');
        $('#errmsg1').text('Please enter title1');
        $( "input[name='vtitle1']" ).focus();
        is_error = true;
      }
  if(vtitle2.trim() == ''){
       $('#errmsg2').html('');
       $('#errmsg2').html('Please enter title2');
       $( "input[name='vtitle2']" ).focus();
       is_error = true;
    }
   if(vtitle3.trim() == ''){
        $('#errmsg3').html('');
        $('#errmsg3').html('Please enter title3');
        $( "input[name='vtitle3']" ).focus();
         is_error = true;
      }
    if(vtitle4.trim() == ''){
      $('#errmsg4').html('');
      $('#errmsg4').html('Please enter title4');
      $( "input[name='vtitle4']" ).focus();
       is_error = true;
     }
    
    if(vheader1.trim() == ''){
      $('#errmsg5').html('');
      $('#errmsg5').html('Please enter header 1');
      $( "input[name='vheader1']" ).focus();
       is_error = true;
     }

    if(vheader2.trim() == ''){
      $('#errmsg6').html('');
      $('#errmsg6').html('Please enter header 2');
      $( "input[name='vheader2']" ).focus();
       is_error = true;
     }
    if(vheader3.trim() == ''){
      $('#errmsg7').html('');
      $('#errmsg7').html('Please enter header 3');
      $( "input[name='vheader3']" ).focus();
       is_error = true;
     }
     if(vheader4.trim() == ''){
      $('#errmsg8').html('');
      $('#errmsg8').html('Please enter header 4');
      $( "input[name='vheader4']" ).focus();
       is_error = true;
     }
     if(vheader5.trim() == ''){
      $('#errmsg9').html('');
      $('#errmsg9').html('Please enter header 5');
      $( "input[name='vheader5']" ).focus();
       is_error = true;
     }
      
      if(vsubheader1.trim() == ''){
      $('#errmsg18').html('');
      $('#errmsg18').html('Please enter sub header 1');
      $( "input[name='vsubheader1']" ).focus();
       is_error = true;
     }

    if(vsubheader2.trim() == ''){
      $('#errmsg19').html('');
      $('#errmsg19').html('Please enter sub header 2');
      $( "input[name='vsubheader2']" ).focus();
       is_error = true;
     }
    if(vsubheader3.trim() == ''){
      $('#errmsg20').html('');
      $('#errmsg20').html('Please enter sub header 3');
      $( "input[name='vsubheader3']" ).focus();
       is_error = true;
     }
     if(vsubheader4.trim() == ''){
      $('#errmsg21').html('');
      $('#errmsg21').html('Please enter sub header 4');
      $( "input[name='vsubheader4']" ).focus();
       is_error = true;
     }
     if(vsubheader5.trim() == ''){
      $('#errmsg22').html('');
      $('#errmsg22').html('Please enter sub header 5');
      $( "input[name='vsubheader5']" ).focus();
       is_error = true;
     }


      if(vmiddle_title1.trim() == ''){
      $('#errmsg10').html('');
      $('#errmsg10').html('Please enter vmiddle title1 ');
      $( "input[name='vmiddle_title1']" ).focus();
       is_error = true;
     }
     if(vmiddle_title2.trim() == ''){
      $('#errmsg11').html('');
      $('#errmsg11').html('Please enter vmiddle title2');
      $( "input[name='vmiddle_title2']" ).focus();
       is_error = true;
     }
     if(vmiddle_title3.trim() == ''){
      $('#errmsg12').html('');
      $('#errmsg12').html('Please enter vmiddle title3');
      $( "input[name='vmiddle_title3']" ).focus();
       is_error = true;
     }

     if(vfooter1.trim() == ''){
      $('#errmsg13').html('');
      $('#errmsg13').html('Please enter footer 1');
      $( "input[name='vfooter1']" ).focus();
       is_error = true;
     }
     if(vfooter2.trim() == ''){
      $('#errmsg14').html('');
      $('#errmsg14').html('Please enter footer 2');
      $( "input[name='vfooter2']" ).focus();
       is_error = true;
     }
     if(vfooter3.trim() == ''){
      $('#errmsg15').html('');
      $('#errmsg15').html('Please enter footer 3');
      $( "input[name='vfooter3']" ).focus();
       is_error = true;
     }
     if(vfooter4.trim() == ''){
      $('#errmsg16').html('');
      $('#errmsg16').html('Please enter footer 4');
      $( "input[name='vfooter4']" ).focus();
       is_error = true;
     }
     if(vfooter5.trim() == ''){
      $('#errmsg17').html('');
      $('#errmsg17').html('Please enter footer 5');
      $( "input[name='vfooter5']" ).focus();
       is_error = true;
     }
    if(is_error){
      return false;
    } 
      

 
    });
});
 </script>


 @endsection