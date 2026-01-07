@extends('admin.layouts.ac.theme')
@section('title', 'Officer update profile')
@section('content')
 <?php  $st=getstatebystatecode($user_data->st_code);   ?> 
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
              div.dataTables_wrapper {margin:0 auto;} 
  </style>
<main role="main" class="inner cover mb-3">
<section class="mt-5">
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h6 class="mr-auto">Officer Update Profile</h6></div> 
             <div class="col"><p class="mb-0 text-right">
              <b>State Name:</b> 
              <span class="badge badge-info">{{$st->ST_NAME}}</span> 
              </p></div>
            </div>
            </div>
<div class="card-body">  
   <form enctype="multipart/form-data" id="changepassword_form" method="POST"  action="{{url('pcceo/updateuser') }}" onsubmit="return checkPassword(this);">
    {{ csrf_field() }}
             <div class="form-group{{ $errors->has('current-password') ? ' has-error' : '' }}">
                                <label for="new-password" class="col-md-4 control-label">Name <sup>*</sup></label>
                                 <input type="hidden" name="offid" value="{{$offrecords->id}}">
                                <div class="col">
                                    <input id="uname" type="text" class="form-control" name="uname" value="{{$offrecords->officername}}">

                                    @if ($errors->has('uname'))
                                        <span class="help-block">
                                        <strong><span style="color:red;">{{ $errors->first('uname') }}</span></strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="currenterrormsg errormsg errorred"></div>
                                 <div class="col"> </div>
                            </div>

                            <div class="form-group{{ $errors->has('new-password') ? ' has-error' : '' }}">
                                <label for="new-password" class="col-md-4 control-label">E-Mail<sup>*</sup></label>

                                <div class="col">
                                    <input id="uemail" type="text" class="form-control" name="uemail" value="{{$offrecords->email}}" >

                                    @if ($errors->has('uemail'))
                                        <span class="help-block">
                                        <strong><span style="color:red;">{{ $errors->first('uemail') }}</span></strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="newpassword errormsg errorred"></div>

                            </div>

                            <div class="form-group">
                                <label for="new-password-confirm" class="col-md-4 control-label">Mobile Number <sup>*</sup></label>
                                <div class="col">
                                    <input id="umobile" type="text" class="form-control" name="umobile" maxlength="10" value="{{$offrecords->Phone_no}}">
                                    @if ($errors->has('umobile'))
                                        <span class="help-block">
                                        <strong><span style="color:red;">{{ $errors->first('umobile') }}</span></strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="confirmpassword errormsg errorred"></div>

                            </div>

                            <div class="form-group float-right">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary"> Update</button>
                                </div>
                            </div>

            </div><!-- end row-->
          </div> <!-- end COL-->
        </div>
        </div>
      </div>
    </div>    
    </section>
    </form>
          </div>
        </div>
  </div>
  </div>
  </section>
  </main>

@endsection