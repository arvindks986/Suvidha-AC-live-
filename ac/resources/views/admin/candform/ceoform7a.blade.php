@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Form 7A Details')
@section('content')
 <?php   
         $url = URL::to("/"); $j=0;
    ?>

  
  <main role="main" class="inner cover mb-3">
  <section class="mt-3">
  <div class="container">
<div class="row">
  				  <div class="row">
    <div class="col">
        @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
        @endif
         @if (session('error_mes'))
          <div class="alert alert-danger"> {{session('error_mes') }}</div>
        @endif
        @if(!empty($errors->first()))
        <div class="alert alert-danger"> <span>{{ $errors->first() }}</span> </div>
      @endif 
         
         
    </div>
    </div>
	
  <div class="card text-left" style="width:100%; margin:0 auto;">
   <form class="form-horizontal" id="election_form" method="post" action="{{url('acceo/ceoupdated-form7A-details')}}" enctype="multipart/form-data" autocomplete='off'>
                <div class=" card-header">
                <div class=" row">
                <div class="col"> <h4>Label text Updation in Vernacular or Form 7A Detils </h4> </div> 
				<div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> 
					<span class="badge badge-info">{{$st_name}}</span>  </p>
				</div>
         
                </div>
                </div>
 
   		
       
    <div class="card-body">  
      
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
						 <input type='text'  name="title1" id="title1" class="form-control"  
					  value="@if(isset($record)){{isset($record->title1)?$record->title1:old('title1')}} @endif"/>
  					
					@if ($errors->has('title1'))
                                     <span style="color:red;">{{ $errors->first('title1') }}</span>
                                  @endif
					<span id="errmsg101" class="text-danger"></span>
					    
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vtitle1" id="vtitle1" class="form-control"  
					  value="@if(isset($record)){{isset($record->vtitle1)?$record->vtitle1:old('vtitle1')}} @endif"/>
  					
					@if ($errors->has('vtitle1'))
                                     <span style="color:red;">{{ $errors->first('vtitle1') }}</span>
                                  @endif
					<span id="errmsg1" class="text-danger"></span>
					
					</div>
				</div>
				
  				<div class="line"></div> 
		       <div class="form-group row">
					<div class="col">
					    <input type='text'  name="title2" id="title2" class="form-control" 
					  value="@if(isset($record)){{isset($record->title2)?$record->title2:old('title2')}} @endif"/>
  					
					@if ($errors->has('title2'))
                                     <span style="color:red;">{{ $errors->first('title2') }}</span>
                                  @endif
					<span id="errmsg102" class="text-danger"></span>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vtitle2" id="vtitle2" class="form-control" 
					  value="@if(isset($record)){{isset($record->vtitle2)?$record->vtitle2:old('vtitle2')}} @endif"/>
  					
					@if ($errors->has('vtitle2'))
                                     <span style="color:red;">{{ $errors->first('vtitle2') }}</span>
                                  @endif
					<span id="errmsg2" class="text-danger"></span>
					</div>
 </div>
  				<div class="line"></div> 

				 <div class="form-group row">
					<div class="col">
					  <input type='text'  name="title3" id="title3" class="form-control" 
					  value="@if(isset($record)){{isset($record->title3)?$record->title3:old('title3')}} @endif"/>
  					
					@if ($errors->has('title3'))
                                     <span style="color:red;">{{ $errors->first('title3') }}</span>
                                  @endif
					<span id="errmsg103" class="text-danger"></span>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vtitle3" id="vtitle3" class="form-control" 
					  value="@if(isset($record)){{isset($record->vtitle3)?$record->vtitle3:old('vtitle3')}} @endif"/>
  					
					@if ($errors->has('vtitle3'))
                                     <span style="color:red;">{{ $errors->first('vtitle3') }}</span>
                                  @endif
					<span id="errmsg3" class="text-danger"></span>
					
					</div>
 
					
								
					</div>
  				<div class="line"></div> 

				  <div class="form-group row">
					<div class="col">
					  <input type='text'  name="title4" id="title4" class="form-control" 
					  value="@if(isset($record)){{isset($record->title4)?$record->title4:old('title4')}} @endif"/>
  					
					@if ($errors->has('title4'))
                                     <span style="color:red;">{{ $errors->first('title4') }}</span>
                                  @endif
					<span id="errmsg104" class="text-danger"></span>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vtitle4" id="vtitle4" class="form-control" 
					  value="@if(isset($record)){{isset($record->vtitle4)?$record->vtitle4:old('vtitle4')}} @endif"/>
  					
					@if ($errors->has('vtitle4'))
                                     <span style="color:red;">{{ $errors->first('vtitle4') }}</span>
                                  @endif
					<span id="errmsg4" class="text-danger"></span>
					</div>
 
					
								
					</div>
			   <div class="line"></div>
  				<div class="form-group row">
					<div class="col">
					  <input type='text'  name="header1" id="header1" class="form-control" 
					  value="@if(isset($record)){{isset($record->header1)?$record->header1:old('header1')}} @endif"/>
  					
					@if ($errors->has('header1'))
                                     <span style="color:red;">{{ $errors->first('header1') }}</span>
                                  @endif
					<span id="errmsg105" class="text-danger"></span>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vheader1" id="vheader1" class="form-control" 
					  value="@if(isset($record)){{isset($record->vheader1)?$record->vheader1:old('vheader1')}} @endif"/>
  					
					@if ($errors->has('vheader1'))
                                     <span style="color:red;">{{ $errors->first('vheader1') }}</span>
                                  @endif
					<span id="errmsg5" class="text-danger"></span>
					</div>
 
					
								
					</div>
				<div class="line"></div>

				<div class="form-group row">
					<div class="col">
					  <input type='text'  name="header2" id="header2" class="form-control" 
					  value="@if(isset($record)){{isset($record->header2)?$record->header2:old('header2')}} @endif"/>
  					
					@if ($errors->has('header2'))
                                     <span style="color:red;">{{ $errors->first('header2') }}</span>
                                  @endif
					<span id="errmsg106" class="text-danger"></span>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vheader2" id="vheader2" class="form-control" 
					  value="@if(isset($record)){{isset($record->vheader2)?$record->vheader2:old('vheader2')}} @endif"/>
  					
					@if ($errors->has('vheader2'))
                                     <span style="color:red;">{{ $errors->first('vheader2') }}</span>
                                  @endif
					<span id="errmsg6" class="text-danger"></span>
					</div>
 
					
								
					</div>
				<div class="line"></div>

				<div class="form-group row">
					<div class="col">
					 <input type='text'  name="header3" id="header3" class="form-control" 
					  value="@if(isset($record)){{isset($record->header3)?$record->header3:old('header3')}} @endif"/>
  					
					@if ($errors->has('header3'))
                                     <span style="color:red;">{{ $errors->first('header3') }}</span>
                                  @endif
					<span id="errmsg107" class="text-danger"></span>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vheader3" id="vheader3" class="form-control" 
					  value="@if(isset($record)){{isset($record->vheader3)?$record->vheader3:old('vheader3')}} @endif"/>
  					
					@if ($errors->has('vheader3'))
                                     <span style="color:red;">{{ $errors->first('vheader3') }}</span>
                                  @endif
					<span id="errmsg7" class="text-danger"></span>
					</div>
 
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					   <input type='text'  name="header4" id="header4" class="form-control" 
					  value="@if(isset($record)){{isset($record->header4)?$record->header4:old('header4')}} @endif"/>
  					
					@if ($errors->has('header4'))
                                     <span style="color:red;">{{ $errors->first('header4') }}</span>
                                  @endif
					<span id="errmsg108" class="text-danger"></span>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vheader4" id="vheader4" class="form-control" 
					  value="@if(isset($record)){{isset($record->vheader4)?$record->vheader4:old('vheader4')}} @endif"/>
  					
					@if ($errors->has('vheader4'))
                                     <span style="color:red;">{{ $errors->first('vheader4') }}</span>
                                  @endif
					<span id="errmsg8" class="text-danger"></span>
					</div>
 
					
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					   <input type='text'  name="header5" id="header5" class="form-control" 
					  value="@if(isset($record)){{isset($record->header5)?$record->header5:old('header5')}} @endif"/>
  					
					@if ($errors->has('header5'))
                                     <span style="color:red;">{{ $errors->first('header5') }}</span>
                                  @endif
					<span id="errmsg109" class="text-danger"></span>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vheader5" id="vheader5" class="form-control" 
					  value="@if(isset($record)){{isset($record->vheader5)?$record->vheader5:old('vheader5')}} @endif"/>
  					
					@if ($errors->has('vheader5'))
                                     <span style="color:red;">{{ $errors->first('vheader5') }}</span>
                                  @endif
					<span id="errmsg9" class="text-danger"></span>
					</div>
 
					
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					    <input type='text'  name="header6" id="header6" class="form-control" 
					  value="@if(isset($record)){{isset($record->header6)?$record->header6:old('header6')}} @endif"/>
  					
					@if ($errors->has('header6'))
                                     <span style="color:red;">{{ $errors->first('header6') }}</span>
                                  @endif
					<span id="errmsg125" class="text-danger"></span>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vheader6" id="vheader6" class="form-control" 
					  value="@if(isset($record)){{isset($record->vheader6)?$record->vheader6:old('vheader6')}} @endif"/>
  					
					@if ($errors->has('vheader6'))
                                     <span style="color:red;">{{ $errors->first('vheader6') }}</span>
                                  @endif
					<span id="errmsg25" class="text-danger"></span>
					</div>
 
					
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					   
					  <input type='text'  name="subheader1" id="subheader1" class="form-control" 
					  value="@if(isset($record)){{isset($record->subheader1)?$record->subheader1:old('subheader1')}} @endif"/>
  					
					@if ($errors->has('subheader1'))
                                     <span style="color:red;">{{ $errors->first('subheader1') }}</span>
                                  @endif
					<span id="errmsg118" class="text-danger"></span>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vsubheader1" id="vsubheader1" class="form-control" 
					  value="@if(isset($record)){{isset($record->vsubheader1)?$record->vsubheader1:old('vsubheader1')}} @endif"/>
  					
					@if ($errors->has('vsubheader1'))
                                     <span style="color:red;">{{ $errors->first('vsubheader1') }}</span>
                                  @endif
					<span id="errmsg18" class="text-danger"></span>
					</div>
 
					
								
					</div>
				<div class="line"></div>

				<div class="form-group row">
					<div class="col">
					   <input type='text'  name="subheader2" id="subheader2" class="form-control" 
					  value="@if(isset($record)){{isset($record->subheader2)?$record->subheader2:old('subheader2')}} @endif"/>
  					
					@if ($errors->has('subheader2'))
                                     <span style="color:red;">{{ $errors->first('subheader2') }}</span>
                                  @endif
					<span id="errmsg119" class="text-danger"></span>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vsubheader2" id="vsubheader2" class="form-control" 
					  value="@if(isset($record)){{isset($record->vsubheader2)?$record->vsubheader2:old('vsubheader2')}} @endif"/>
  					
					@if ($errors->has('vsubheader2'))
                                     <span style="color:red;">{{ $errors->first('vsubheader2') }}</span>
                                  @endif
					<span id="errmsg19" class="text-danger"></span>
					</div>
 
					
								
					</div>
				<div class="line"></div>

				<div class="form-group row">
					<div class="col">
					 <input type='text'  name="subheader3" id="subheader3" class="form-control" 
					  value="@if(isset($record)){{isset($record->subheader3)?$record->subheader3:old('subheader3')}} @endif"/>
  					@if ($errors->has('subheader3'))
                                     <span style="color:red;">{{ $errors->first('subheader3') }}</span>
                                  @endif
					<span id="errmsg120" class="text-danger"></span>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vsubheader3" id="vsubheader3" class="form-control" 
					  value="@if(isset($record)){{isset($record->vsubheader3)?$record->vsubheader3:old('vsubheader3')}} @endif"/>
  					@if ($errors->has('vsubheader3'))
                                     <span style="color:red;">{{ $errors->first('vsubheader3') }}</span>
                                  @endif
					<span id="errmsg20" class="text-danger"></span>
					</div>
 
					
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					  <input type='text'  name="subheader4" id="subheader4" class="form-control" 
					  value="@if(isset($record)){{isset($record->subheader4)?$record->subheader4:old('subheader4')}} @endif"/>
  					
						@if ($errors->has('subheader4'))
                                     <span style="color:red;">{{ $errors->first('subheader4') }}</span>
                                  @endif
					<span id="errmsg121" class="text-danger"></span>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vsubheader4" id="vsubheader4" class="form-control" 
					  value="@if(isset($record)){{isset($record->vsubheader4)?$record->vsubheader4:old('vsubheader4')}} @endif"/>
  					
						@if ($errors->has('vsubheader4'))
                                     <span style="color:red;">{{ $errors->first('vsubheader4') }}</span>
                                  @endif
					<span id="errmsg21" class="text-danger"></span>
					</div>
 
				
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					 <input type='text'  name="subheader5" id="subheader5" class="form-control" 
					  value="@if(isset($record)){{isset($record->subheader5)?$record->subheader5:old('subheader5')}} @endif"/>
  					@if ($errors->has('subheader5'))
                                     <span style="color:red;">{{ $errors->first('subheader5') }}</span>
                                  @endif
					<span id="errmsg122" class="text-danger"></span>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vsubheader5" id="vsubheader5" class="form-control" 
					  value="@if(isset($record)){{isset($record->vsubheader5)?$record->vsubheader5:old('vsubheader5')}} @endif"/>
  					@if ($errors->has('vsubheader5'))
                                     <span style="color:red;">{{ $errors->first('vsubheader5') }}</span>
                                  @endif
					<span id="errmsg22" class="text-danger"></span>
					
					</div>
 
					
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					  <input type='text'  name="subheader6" id="subheader6" class="form-control" 
					  value="@if(isset($record)){{isset($record->subheader6)?$record->subheader6:old('subheader6')}} @endif"/>
  					@if ($errors->has('subheader6'))
                                     <span style="color:red;">{{ $errors->first('subheader6') }}</span>
                                  @endif
					<span id="errmsg126" class="text-danger"></span>
					
					</div>
								
				   <div class="col">
					  <input type='text'  name="vsubheader6" id="vsubheader6" class="form-control" 
					  value="@if(isset($record)){{isset($record->vsubheader6)?$record->vsubheader6:old('vsubheader6')}} @endif"/>
  					@if ($errors->has('vsubheader6'))
                                     <span style="color:red;">{{ $errors->first('vsubheader6') }}</span>
                                  @endif
					<span id="errmsg26" class="text-danger"></span>
					
					</div>
 
					
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					   <input type='text'  name="middle_title1" id="middle_title1" class="form-control" 
					  value="@if(isset($record)){{isset($record->middle_title1)?$record->middle_title1:old('middle_title1')}} @endif"/>
  					@if ($errors->has('middle_title1'))
                                     <span style="color:red;">{{ $errors->first('middle_title1') }}</span>
                                  @endif
					<span id="errmsg110" class="text-danger"></span>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vmiddle_title1" id="vmiddle_title1" class="form-control" 
					  value="@if(isset($record)){{isset($record->vmiddle_title1)?$record->vmiddle_title1:old('vmiddle_title1')}} @endif"/>
  					@if ($errors->has('vmiddle_title1'))
                                     <span style="color:red;">{{ $errors->first('vmiddle_title1') }}</span>
                                  @endif
					<span id="errmsg10" class="text-danger"></span>
					
					</div>
 
					
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					   <input type='text'  name="middle_title2" id="middle_title2" class="form-control" 
					  value="@if(isset($record)){{isset($record->middle_title2)?$record->middle_title2:old('middle_title2')}} @endif"/>
  					
					@if ($errors->has('middle_title2'))
                                     <span style="color:red;">{{ $errors->first('middle_title2') }}</span>
                                  @endif
					<span id="errmsg111" class="text-danger"></span>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vmiddle_title2" id="vmiddle_title2" class="form-control" 
					  value="@if(isset($record)){{isset($record->vmiddle_title2)?$record->vmiddle_title2:old('vmiddle_title2')}} @endif"/>
  					
					@if ($errors->has('vmiddle_title2'))
                                     <span style="color:red;">{{ $errors->first('vmiddle_title2') }}</span>
                                  @endif
					<span id="errmsg11" class="text-danger"></span>
					</div>
 
					
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					   <input type='text'  name="middle_title3" id="middle_title3" class="form-control" 
					  value="@if(isset($record)){{isset($record->middle_title3)?$record->middle_title3:old('middle_title3')}} @endif"/>
  					@if ($errors->has('middle_title3'))
                                     <span style="color:red;">{{ $errors->first('middle_title3') }}</span>
                                  @endif
					<span id="errmsg112" class="text-danger"></span>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vmiddle_title3" id="vmiddle_title3" class="form-control" 
					  value="@if(isset($record)){{isset($record->vmiddle_title3)?$record->vmiddle_title3:old('vmiddle_title3')}} @endif"/>
  					@if ($errors->has('vmiddle_title3'))
                                     <span style="color:red;">{{ $errors->first('vmiddle_title3') }}</span>
                                  @endif
					<span id="errmsg12" class="text-danger"></span>
					</div>
 
					
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					  <input type='text'  name="footer1" id="footer1" class="form-control" 
					  value="@if(isset($record)){{isset($record->footer1)?$record->footer1:old('footer1')}} @endif"/>
  					@if ($errors->has('footer1'))
                                     <span style="color:red;">{{ $errors->first('footer1') }}</span>
                                  @endif
					<span id="errmsg113" class="text-danger"></span>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vfooter1" id="vfooter1" class="form-control" 
					  value="@if(isset($record)){{isset($record->vfooter1)?$record->vfooter1:old('vfooter1')}} @endif"/>
  					@if ($errors->has('vfooter1'))
                                     <span style="color:red;">{{ $errors->first('vfooter1') }}</span>
                                  @endif
					<span id="errmsg13" class="text-danger"></span>
					
					</div>
 
					
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					   <input type='text'  name="footer2" id="footer2" class="form-control" 
					  value="@if(isset($record)){{isset($record->footer2)?$record->footer2:old('footer2')}} @endif"/>
  					
					@if ($errors->has('footer2'))
                                     <span style="color:red;">{{ $errors->first('footer2') }}</span>
                                  @endif
					<span id="errmsg114" class="text-danger"></span>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vfooter2" id="vfooter2" class="form-control" 
					  value="@if(isset($record)){{isset($record->vfooter2)?$record->vfooter2:old('vfooter2')}} @endif"/>
  					
					@if ($errors->has('vfooter2'))
                                     <span style="color:red;">{{ $errors->first('vfooter2') }}</span>
                                  @endif
					<span id="errmsg14" class="text-danger"></span>
					</div>
 
					
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					  <input type='text'  name="footer3" id="footer3" class="form-control" 
					  value="@if(isset($record)){{isset($record->footer3)?$record->footer3:old('footer3')}} @endif"/>
  					
					@if ($errors->has('footer3'))
                                     <span style="color:red;">{{ $errors->first('footer3') }}</span>
                                  @endif
					<span id="errmsg115" class="text-danger"></span>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vfooter3" id="vfooter3" class="form-control" 
					  value="@if(isset($record)){{isset($record->vfooter3)?$record->vfooter3:old('vfooter3')}} @endif"/>
  					
					@if ($errors->has('vfooter3'))
                                     <span style="color:red;">{{ $errors->first('vfooter3') }}</span>
                                  @endif
					<span id="errmsg15" class="text-danger"></span>
					</div>
 
					
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					  <input type='text'  name="footer4" id="footer4" class="form-control" 
					  value="@if(isset($record)){{isset($record->footer4)?$record->vfooter4:old('footer4')}} @endif"/>
  					
					@if ($errors->has('footer4'))
                                     <span style="color:red;">{{ $errors->first('footer4') }}</span>
                                  @endif
					<span id="errmsg116" class="text-danger"></span>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vfooter4" id="vfooter4" class="form-control" 
					  value="@if(isset($record)){{isset($record->vfooter4)?$record->vfooter4:old('vfooter4')}} @endif"/>
  					
					@if ($errors->has('vfooter4'))
                                     <span style="color:red;">{{ $errors->first('vfooter4') }}</span>
                                  @endif
					<span id="errmsg16" class="text-danger"></span>
					</div>
 
					
								
					</div>
				<div class="line"></div>
				<div class="form-group row">
					<div class="col">
					  <input type='text'  name="footer5" id="footer5" class="form-control" 
					  value="@if(isset($record)){{isset($record->footer5)?$record->footer5:old('footer5')}} @endif"/>
  					
					@if ($errors->has('footer5'))
                                     <span style="color:red;">{{ $errors->first('footer5') }}</span>
                                  @endif
					<span id="errmsg117" class="text-danger"></span>
				    </div>	
								
				   <div class="col">
					  <input type='text'  name="vfooter5" id="vfooter5" class="form-control" 
					  value="@if(isset($record)){{isset($record->vfooter5)?$record->vfooter5:old('vfooter5')}} @endif"/>
  					
					@if ($errors->has('vfooter5'))
                                     <span style="color:red;">{{ $errors->first('vfooter5') }}</span>
                                  @endif
					<span id="errmsg17" class="text-danger"></span>
					</div>
 
					
								
					</div>
			</div>
			<div class="card-footer">
					<div class="form-group text-right" align="text-right">

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

    var title1 = $('input[name="title1"]').val();
    var title2 = $('input[name="title2"]').val();
    var title3 = $('input[name="title3"]').val(); 
    var title4 = $('input[name="title4"]').val(); 
    
    var header1 = $('input[name="header1"]').val();
    var header2 = $('input[name="header2"]').val();
    var header3 = $('input[name="header3"]').val(); 
    var header4 = $('input[name="header4"]').val(); 
    var header5 = $('input[name="header5"]').val();
    var subheader1 = $('input[name="subheader1"]').val();
    var subheader2 = $('input[name="subheader2"]').val();
    var subheader3 = $('input[name="subheader3"]').val(); 
    var subheader4 = $('input[name="subheader4"]').val(); 
    var subheader5 = $('input[name="subheader5"]').val();

    var middle_title1 = $('input[name="middle_title1"]').val();
    var middle_title2 = $('input[name="middle_title2"]').val(); 
    var middle_title3 = $('input[name="middle_title3"]').val(); 

    var footer1 = $('input[name="footer1"]').val();
    var footer2 = $('input[name="footer2"]').val();
    var footer3 = $('input[name="footer3"]').val(); 
    var footer4 = $('input[name="footer4"]').val(); 
    var footer5 = $('input[name="footer5"]').val();

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


    if(title1.trim() == ''){
        $('#errmsg101').html('');
        $('#errmsg101').text('Please enter title1');
        $( "input[name='title1']" ).focus();
        is_error = true;
      }
  if(title2.trim() == ''){
       $('#errmsg102').html('');
       $('#errmsg102').html('Please enter title2');
       $( "input[name='title2']" ).focus();
       is_error = true;
    }
   if(title3.trim() == ''){
        $('#errmsg103').html('');
        $('#errmsg103').html('Please enter title3');
        $( "input[name='title3']" ).focus();
         is_error = true;
      }
    if(title4.trim() == ''){
      $('#errmsg104').html('');
      $('#errmsg104').html('Please enter title4');
      $( "input[name='title4']" ).focus();
       is_error = true;
     }
    
    if(header1.trim() == ''){
      $('#errmsg105').html('');
      $('#errmsg105').html('Please enter header 1');
      $( "input[name='header1']" ).focus();
       is_error = true;
     }

    if(header2.trim() == ''){
      $('#errmsg106').html('');
      $('#errmsg106').html('Please enter header 2');
      $( "input[name='header2']" ).focus();
       is_error = true;
     }
    if(header3.trim() == ''){
      $('#errmsg107').html('');
      $('#errmsg107').html('Please enter header 3');
      $( "input[name='header3']" ).focus();
       is_error = true;
     }
     if(header4.trim() == ''){
      $('#errmsg108').html('');
      $('#errmsg108').html('Please enter header 4');
      $( "input[name='header4']" ).focus();
       is_error = true;
     }
     if(header5.trim() == ''){
      $('#errmsg109').html('');
      $('#errmsg109').html('Please enter header 5');
      $( "input[name='header5']" ).focus();
       is_error = true;
     }
      
      if(subheader1.trim() == ''){
      $('#errmsg118').html('');
      $('#errmsg118').html('Please enter sub header 1');
      $( "input[name='subheader1']" ).focus();
       is_error = true;
     }

    if(subheader2.trim() == ''){
      $('#errmsg119').html('');
      $('#errmsg119').html('Please enter sub header 2');
      $( "input[name='subheader2']" ).focus();
       is_error = true;
     }
    if(subheader3.trim() == ''){
      $('#errmsg120').html('');
      $('#errmsg120').html('Please enter sub header 3');
      $( "input[name='subheader3']" ).focus();
       is_error = true;
     }
     if(subheader4.trim() == ''){
      $('#errmsg121').html('');
      $('#errmsg121').html('Please enter sub header 4');
      $( "input[name='subheader4']" ).focus();
       is_error = true;
     }
     if(subheader5.trim() == ''){
      $('#errmsg122').html('');
      $('#errmsg122').html('Please enter sub header 5');
      $( "input[name='subheader5']" ).focus();
       is_error = true;
     }


      if(middle_title1.trim() == ''){
      $('#errmsg110').html('');
      $('#errmsg110').html('Please enter vmiddle title1 ');
      $( "input[name='middle_title1']" ).focus();
       is_error = true;
     }
     if(middle_title2.trim() == ''){
      $('#errmsg111').html('');
      $('#errmsg111').html('Please enter vmiddle title2');
      $( "input[name='middle_title2']" ).focus();
       is_error = true;
     }
     if(middle_title3.trim() == ''){
      $('#errmsg112').html('');
      $('#errmsg112').html('Please enter vmiddle title3');
      $( "input[name='middle_title3']" ).focus();
       is_error = true;
     }

     if(footer1.trim() == ''){
      $('#errmsg113').html('');
      $('#errmsg113').html('Please enter footer 1');
      $( "input[name='footer1']" ).focus();
       is_error = true;
     }
     if(footer2.trim() == ''){
      $('#errmsg114').html('');
      $('#errmsg114').html('Please enter footer 2');
      $( "input[name='footer2']" ).focus();
       is_error = true;
     }
     if(footer3.trim() == ''){
      $('#errmsg115').html('');
      $('#errmsg115').html('Please enter footer 3');
      $( "input[name='footer3']" ).focus();
       is_error = true;
     }
     if(footer4.trim() == ''){
      $('#errmsg116').html('');
      $('#errmsg116').html('Please enter footer 4');
      $( "input[name='footer4']" ).focus();
       is_error = true;
     }
     if(footer5.trim() == ''){
      $('#errmsg117').html('');
      $('#errmsg117').html('Please enter footer 5');
      $( "input[name='footer5']" ).focus();
       is_error = true;
     }
    if(is_error){
      return false;
    } 
      

 
    });
});
 </script>


 @endsection