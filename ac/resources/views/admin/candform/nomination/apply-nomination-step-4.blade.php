      @extends('admin.layouts.ac.theme')
      @section('title', 'Nomination')
      @section('content')
      <style type="text/css">
        .error{
          font-size: 12px; 
          color: red;
        }
        .display_none{
          display: none;
        }
        .form_steps p{
          padding: 15px 15px;
        }
        .heading-part1 p{
          padding: 0px !important;
        }
        .fullwidth{
          width: 100%;
          float: left;
        }
        #imagePreview{
          width: 150px;
          height: 150px;
          border: 1px solid #efefef;
        }
        .button-next{
          margin-top: 30px;
        }
        .button-next button{
          float: right;
        }
      </style>
      <link rel="stylesheet" href="{{ asset('css/custom.css') }}" id="theme-stylesheet">
      <link rel="stylesheet" href="{{ asset('admintheme/css/jquery-ui.css') }}" id="theme-stylesheet">
      <main role="main" class="inner cover mb-3">
      @if(count($errors->all())>0 || session('flash-message'))
        <section>
       <div class="container">
            <div class="row">

             <div class="card text-left mt-3" style="width:100%; margin:0 auto 10px auto;">


              <div class="row">

               @if(count($errors->all())>0)
               <div class="alert alert-danger">
                 @foreach($errors->all() as $iterate_error)
                 <p class="text-left">{!! $iterate_error !!}</p>
                 @endforeach
               </div>
               @endif

               @if (session('flash-message'))
               <div class="alert alert-success"> {{session('flash-message') }}</div>
               @endif

             </div>
           </div>
         </div>    
       </div>
	   </section>
     @endif

<div class="container-fluid">
   <div class="col-md-12 mt-3">
     <ul style="text-align:center;margin-bottom:40px;" class="arrow-steps clearfix">
      <li class="step step1 ">Personal Details</li>
      <li class="step step2">Election Details</li>
       <li class="step step3 ">Part I/II</li>
       <li class="step step4 current first">Part III<span></span></li>
       <li class="step step5">Part IIIA<span></span></li>
       <li class="step step4">Upload Affidavit<span></span></li>
       <li class="step step4">Finalize Application<span></span></li>
     </ul>
 </div>

</div>



       <section>
        <div class="container p-0">
          <div class="row">
<div class="fullwidth" style="float: left;width: 100%;">
     
                
                @if(isset($reference_id) && isset($href_download_application))
                <div class="col-md-5 float-right">
                  <ul class="list-inline float-right">
                    <li class="list-inline-item text-right">Reference ID: <b style="text-decoration: underline;">{{$reference_id}}</b></li>
                    <li class="list-inline-item text-right"><a href="{!! $href_download_application !!}" class="btn btn-primary" target="_blank">Download Application</a></li>
                  </ul>
                </div>
                @endif
              </div>
        </div>
        
          <div class="row">

            <div class="col-md-12">
              <div class="card">
			    <form method="post" action="{!! $action !!}" enctype="multipart/form-data">
                              <input type="hidden" name="_token" value="{{csrf_token()}}"/>
                              <input type="hidden" name="nomination_id" value="{{$nomination_id}}"/>
                              <input type="hidden" name="st_code" value="{{$st_code}}">
     <input type="hidden" name="ac_no" value="{{$ac_no}}">
      <input type="hidden" name="election_id" value="{{$election_id}}">
               <div class="card-header d-flex align-items-center">
                 <h4>{!! $heading_title !!}</h4>
               </div>
			  
               <div class="card-body">
                 <div class="row">

                   <div class="col form-inline" >
 
                      

                    <div class="part3 form_steps nomination-detail">
                     
 
                      <div class="nomination-parts">
                        <div class="nomination-form-heading text-center  mb-3">
                          <h3><strong>PART III</strong></h3>
                        </div>

                        <div class="nomination-detail">
						<p>
                        I, the candidate mentioned in Part I/Part II (Strike out which is not applicable) assent to this nomination and hereby declare—</p>
						
                          <ul class="Ullist p-3">
                            <li>(a) that I am a citizen of India and have not acquired the citizenship of any foreign State/country.</li>
                            <li>(b) that I have completed <input type="text" name="age" placeholder="Enter Age" class="form-control nomination-field-2" value="{{$age}}"> years of age; <br>
							</li>
								<li class="mt-3 mb-2"><b>[STRIKE OUT c(i) or c(ii) BELOW WHICHEVER IS NOT APPLICABLE]</b></li>
                            <div class="nomination-options strikeout">

                              @if($recognized_party == '1')
                              <div class="checkbox recognised" style="">
                               (c) (i) that I am set up at this election by the 
                                  <select name="party_id" class="form-control nomination-field-2" style="width: 300px;">
                                    <option value="">-- Select Party --</option>
                               

                                    @foreach($parties as $iterate_party)
               @if($party_id == $iterate_party['party_id'])
               <option value="{{ $iterate_party['party_id'] }}" selected="selected">{{ $iterate_party['name'] }}</option>
               @else 
               <option value="{{ $iterate_party['party_id'] }}"> {{ $iterate_party['name'] }}</option>
               @endif
             @endforeach

                                  </select> party, which is recognised National Party/State Party in this State and that the symbol reserved for the above party be allotted to me.
                                </div>


                                @else
                                <div class="checkbox not-recognized">
                                  (c) (ii) that I am set up at this election by the 
                                   <select name="party_id" class="form-control nomination-field-2" style="width:300px;">
                                    <option value="">-- Select Party --</option>
                                    @foreach($parties as $iterate_party)
									@if($party_id == $iterate_party['party_id'])
									<option value="{{ $iterate_party['party_id'] }}" selected="selected">{{ $iterate_party['name'] }}</option>
									@else 
									<option value="{{ $iterate_party['party_id'] }}"> {{ $iterate_party['name'] }}</option>
									@endif
									@endforeach
                                  </select>
                                  party, which is a registered-unrecognised political party/that I am contesting this election as an independent candidate. (Strike out which is not applicable) and that the symbols I have chosen, in order of preference, are:—  
                                  (i)<input type="text" name="suggest_symbol_1" class="form-control nomination-field-2" value="{{$suggest_symbol_1}}">(ii)<input type="text" name="suggest_symbol_2" class="form-control nomination-field-2" value="{{$suggest_symbol_2}}">(iii)<input type="text" name="suggest_symbol_3" class="form-control nomination-field-2"  value="{{$suggest_symbol_3}}">
                                </div>
                                @endif

                              </div>

                              <li>(d) that my name and my father's/mother's/husband's name have been correctly spelt out above in <input type="text" name="language" class="form-control nomination-field-2" value="{{$language}}"> (name of the language);</li>
                              <li>(e) that to the best of my knowledge and belief, I am qualified and not also disqualified for being chosen to fill the seat in the Legislative Assembly of this State.</li>


							
                           

                            <li>
                              <p>*I further declare that I am a member of the <select name="category" class="form-control nomination-field-2">
                                  <option value="">Select</option>
                                  @foreach($categories as $iterate_category)
                                   @if($category == $iterate_category['id'])
                                   <option value="{{$iterate_category['id']}}" selected="selected">{{$iterate_category['name']}}</option>
                                   @else
                                   <option value="{{$iterate_category['id']}}">{{$iterate_category['name']}}</option>
                                   @endif
                                   @endforeach
                                </select>**Caste/tribe which is a scheduled **caste/tribe of the State of<select name="part3_cast_state" class="form-control" id="part3_cast_state">
             <option value="">-- Select States --</option>
             @foreach($states as $iterate_state)
               @if($part3_cast_state == $iterate_state['st_code'])
               <option value="{{ $iterate_state['st_code'] }}" selected="selected">{{ $iterate_state['st_name'] }}</option>
               @else 
               <option value="{{ $iterate_state['st_code'] }}"> {{ $iterate_state['st_name'] }}</option>
               @endif
             @endforeach
           </select>in relation to<input type="text" name="part3_address" class="form-control nomination-field-2" value="{{$part3_address}}">(area) in that State.

                              I also declare that I have not been, and shall not be nominated as a candidate at the present general election/the bye-elections being held simultaneously, to the Legislative Assembly <select name="part3_legislative_state" class="form-control" id="part3_legislative_state">
             <option value="">-- Select States --</option>
             @foreach($states as $iterate_state)
               @if($part3_legislative_state == $iterate_state['st_code'])
               <option value="{{ $iterate_state['st_code'] }}" selected="selected">{{ $iterate_state['st_name'] }}</option>
               @else 
               <option value="{{ $iterate_state['st_code'] }}"> {{ $iterate_state['st_name'] }}</option>
               @endif
             @endforeach
           </select> of (State) from more than two Assembly constituencies.</li>
           </ul>


                            </div>
 <input type="hidden" name="part3_date" id="part3_date" class="form-control nomination-field-2" value="{{$part3_date}}" readonly="readonly"> 
<hr class="mt-5" />
                            <div class="nomination-note">
								<small>*Score out the words "assembly constituency comprised within" in the case of Jammu and Kashmir, Andaman and Nicobar Islands, Chandigarh, Dadra and Nagar Haveli, Daman and Diu and Lakshadweep.</small>
								<br /><small> *Score out this paragraph, if not applicable.</small>
								<br /><small> **Score out the words not applicable. N.B.—A "recognised political party" means a political party recognised by the Election Commission under the Election Symbols (Reservation and Allotment) Order, 1968 in the State concerned.</small>
							</div>
                          </div>
						
						   </div>      
						   
						   </div> </div> </div>
						
		


                  <div class="card-footer">
          <div class="form-group row ">
            <!-- <div class="col">
              <a href="" id="" class="btn btn-secondary float-left">Back</a>
            </div> -->
            <div class="col ">
              <div class="form-group row float-right">
                <!-- <button type="submit" id="save" name="save_only" class="btn btn-primary">Save</button> -->
                <button type="submit" class="btn btn-primary save_next">Save & Next</button>
            </div>
            </div>
            </div>
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
     <script type="text/javascript" src="{{ asset('admintheme/js/jquery-ui.js') }}"></script>

     <script>
      $(document).ready(function(){  
       if($('#breadcrumb').length){
         var breadcrumb = '';
         $.each({!! json_encode($breadcrumbs) !!},function(index, object){
          breadcrumb += "<li><a href='"+object.href+"'>"+object.name+"</a></li>";
        });
         $('#breadcrumb').html(breadcrumb);
       }

       $('#part3_date').datepicker({
        dateFormat: 'dd-mm-yy'
       });

     });
   </script>
  @if (session('success_mes'))
<script type="text/javascript">
 success_messages("{{session('success_mes') }}");
 </script>
@endif
@if (session('error_mes'))
  <script type="text/javascript">
  error_messages("{{session('error_mes') }}");
</script>
@endif

@endsection