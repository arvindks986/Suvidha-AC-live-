@extends('layouts.theme')
@section('title', 'Permission')
@section('content')

<main role="main" class="inner cover mb-3 mb-auto">
  <section class="mt-5" id="wrapper">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 p-0">
          <div class="sidebar__inner">
            <div class="card" ><!--  style="max-width:700px; margin:0 auto;" -->
              <div class="card-header d-flex align-items-center">
                  <h2>Permission Details</h2>
              </div>
              
              @if(!empty($result))
              @foreach($result as $res)   
             <div class="card-body getpermission">
                <form class="form-horizontal">
                  <div class="form-group row">
                    <label class="col-sm-4 form-control-label">Reference Number</label>
                      <div class="col-sm-8">
                        <p>{{$res->reference_id}}</p>
                      </div>
                  </div>      
                  <div class="form-group row">
                    <label class="col-sm-4 form-control-label">Name</label>
                      <div class="col-sm-8">
                        <p>{{$res->name}}</p>
                      </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-4 form-control-label">Mobile No  </label>
                      <div class="col-sm-8">
                        <p>{{$res->mobileno}}</p>
                      </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-4 form-control-label">Email</label>
                      <div class="col-sm-8">
                        <p>{{$res->email}}</p>
                      </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-4 form-control-label">State</label>
                      <div class="col-sm-8">
                        <p>{{$res->ST_NAME}}</p>
                      </div>
                  </div>
				  @if($res->dist_no == 0)	
                  <div class="form-group row" style="display:none;">
                    <label class="col-sm-4 form-control-label">District</label>
                      <div class="col-sm-8">
                        <p></p>
                      </div>
                  </div>
				  @else
					<div class="form-group row">
                    <label class="col-sm-4 form-control-label">District</label>
                      <div class="col-sm-8">
                        <p>{{$res->DIST_NAME}}</p>
                      </div>
                  </div>
				@endif

                  <div class="form-group row">
                    <label class="col-sm-4 form-control-label">Permission Type</label>
                      <div class="col-sm-8">
                        <p>{{$res->permission_name}}</p>
                      </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-4 form-control-label">Event Place</label>
                      <div class="col-sm-8">
                        @if(!empty($res->location_name))
                        <p>{{$res->location_name}}, {{$res->location_details}}</p>
                        @else
                        <p>{{$res->Other_location}}</p>
                        @endif
                      </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-4 form-control-label">Event Start Date & Time</label>
                      <div class="col-sm-8">
                        {{GetReadableDateForm($res->date_time_start)}}
                      </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-4 form-control-label">Event End Date & Time</label>
                      <div class="col-sm-8">
                       {{GetReadableDateForm($res->date_time_end)}}
                      </div>
                  </div>

                  <!-- <div class="form-group row">
                                       <label class="col-sm-4 form-control-label">Applicant Submitted Document</label>

                                       @if(!empty($res->required_files) && ($res->required_files != 'NULL' && $res->required_files != 'null'))
                                       @php
                                       $docdata=explode(',',$res->required_files);
                                       @endphp
                                       @if(!empty($docdata))
                                       @for($i=0;$i < count($docdata); $i++)
              @if(!empty($docdata[$i]))
                                       <div class="col-sm-8">
										@if($res->fileserver_dir == "uploads1")
											<a href="{{asset('/')}}{{$docdata[$i]}}" download>Download Document </a><br>  
									    @else
											<a href="{{asset('uploads/userdoc/permission-document')}}/{{$docdata[$i]}}" download>Download Document </a><br>
											
										@endif
                                       </div>
                                       @endif
                                       @endfor
                                       @endif
                                       @else
                                       <div class="col-sm-8">
                                           <p>Nill</p>
                                       </div>
                                        @endif
                                   </div> -->
                                   <div class="form-group row">
    <label class="col-sm-4 form-control-label">Applicant Submitted Document</label>

    <div class="col-sm-8">
        @if(!empty($res->required_files) && !in_array(strtolower($res->required_files), ['null', '']))
            @php
                $docdata = explode(',', $res->required_files);
            @endphp

            @if(!empty($docdata))
                @foreach($docdata as $doc)
                    @if(!empty($doc))
                     @php $tokenNumber =  \Crypt::encryptString($doc); @endphp
                        <div class="mb-2">
                            @if($res->fileserver_dir == "uploads1")
                               <a href="{{$tokenNumber ? route('view-pdf', ['filename' => $tokenNumber]) : '#' }}" 
   target="_blank">
   Download Document
</a><br>
                           
                            @endif
                        </div>
                    @endif
                @endforeach
            @endif
        @else
            <p>Nill</p>
        @endif
    </div>
</div>

                  <div class="form-group row">
                    <label class="col-sm-4 form-control-label">Application Status</label>
                    <!-- /var/www/html/candidate1/public/uploads/RO-Uploaddocument -->
                      <div class="col-sm-8">
                        @if(($res->approved_status)==0 && ($res->cancel_status)!=1) 
                      <b>Pending</b>
                      @elseif(($res->approved_status)==2 && ($res->cancel_status)!=1)
                      <b>Accepted </b>
                      @elseif(($res->approved_status)==1 && ($res->cancel_status)!=1)
                      <b>In Progress </b>
                      @elseif(($res->approved_status)==3 && ($res->cancel_status)!=1)
                      <b>Reject </b>
                      @elseif(($res->cancel_status)==1)
                      <b>Cancelled </b>
                      @endif
                      </div><!--<a href="newsletter_01.pdf" target="_blank">Read more</a>-->
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-4 form-control-label">Comment</label>
                    <!-- /var/www/html/candidate1/public/uploads/RO-Uploaddocument -->
                      <div class="col-sm-8">
                        @if(count($pdf)>0)
                        @foreach($pdf as $comment)
                            @if($comment->ro_cancel_status != 1)
                              {{$comment->comment}}

                            @endif
                        @endforeach
                        @endif
                      
                      </div><!--<a href="newsletter_01.pdf" target="_blank">Read more</a>-->
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-4 form-control-label">Order Copy</label>
                    <!-- /var/www/html/candidate1/public/uploads/RO-Uploaddocument -->
                      <div class="col-sm-8">

                         @if(count($pdf)>0)
                        @foreach($pdf as $pdfresult)
						
                          @if($pdfresult->ro_cancel_status != 1)
                            @if(($pdfresult->file)== 'NULL')
                            NULL
                            @else
                            @php
                            $docone=explode(',',$pdfresult->file);
                            @endphp
                            @foreach($docone as $pdforderfile)
                             @php $tokenNumber =  \Crypt::encryptString($pdforderfile); @endphp
							@if($pdfresult->fileserver_dir == "uploads")
							
							@else
								 <a href="{{$tokenNumber ? route('view-pdf', ['filename' => $tokenNumber]) : '#' }}" 
   target="_blank">
   Download Order Copy<br>
</a>
							@endif
                              
                            @endforeach
                            @endif
                          @endif

                        @endforeach

                        @endif

                      </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-4 form-control-label">Cancelation Comment</label>
                    <!-- /var/www/html/candidate1/public/uploads/RO-Uploaddocument -->
                      <div class="col-sm-8">
                        <!-- {{$pdf}} -->
                      @if(count($pdf)>0)
                      @foreach($pdf as $comment)
                          @if($comment->ro_cancel_status == 1)
                            {{$comment->comment}}
                          @endif
                      @endforeach
                      @endif
                      </div><!--<a href="newsletter_01.pdf" target="_blank">Read more</a>-->
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-4 form-control-label">Cancelation Order Copy</label>
                    <!-- /var/www/html/candidate1/public/uploads/RO-Uploaddocument -->
                      <div class="col-sm-8">
                      @if(count($pdf)>0)
                        @foreach($pdf as $pdfresult)

                          @if($pdfresult->ro_cancel_status == 1)
                            
                            @if(($pdfresult->file)== 'NULL')
                            NULL
                            @else
                            @php
                            $docone=explode(',',$pdfresult->file);
                            @endphp

                            @foreach($docone as $pdforderfile)
                             @php $tokenNumber =  \Crypt::encryptString($pdforderfile); @endphp
							@if($pdfresult->fileserver_dir == "uploads")
							
							@else
								<a href="{{$tokenNumber  ? route('view-pdf', ['filename' => $tokenNumber]) : '#' }}" 
   target="_blank">
   Download Order Copy<br>
</a>
							@endif
                             
                            @endforeach
                            @endif
                          @endif

                        @endforeach

                        @endif

                      

                      </div>
                  </div>
                  <div class="form-group row">
                    <div class="col">
                      <a href="{{url('/permission')}}" class="btn btn-primary float-left">Back</a>
                      
                      <a href="{{url('/Download_Permission')}}/{{$res->approved_status}}/{{$res->permission}}/{{$res->location_id}}" class="btn btn-primary float-right glyphicon glyphicon-download-alt">Download Permission Details</a> 
                     
                    </div>            
                  </div>
                

                </form>
              </div>
              @endforeach
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

@endsection