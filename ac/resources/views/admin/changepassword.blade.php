@extends('admin.layouts.pc.report-theme')
@section('title', 'Change Password')
@section('content') 
 <?php  //$st=app(App\commonModel::class)->getstatebystatecode($user_data->st_code);  
       // $pc=app(App\commonModel::class)->getpcbypcno($user_data->st_code,$user_data->pc_no); 
		//dd($pc);
		?>
<main role="main" class="inner cover mb-3">
<section>	 
  <div class="container">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
		 <div class=" card-header">
		<div class=" row">
			<div class="col-md-4"><h4>Change Password Form</h4></div> 
	  	<div class="col"><p class="mb-0 text-right">
			<div class="" style="width:100%; margin:0 auto;"></div>
      </span>&nbsp;&nbsp;  
			</p>
			</div><!--end col-->
		</div> <!--end row-->
		</div><!--end card-header -->
      
    <div class="card-body">  
          @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
          @endif
          @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
          @endif
  <form enctype="multipart/form-data" id="changepassword_form" method="POST"  action="{{url('ropc/changepassword') }}" onsubmit="return checkPassword(this);">
	  {{ csrf_field() }}
             <div class="form-group{{ $errors->has('current-password') ? ' has-error' : '' }}">
                                <label for="new-password" class="col-md-4 control-label">Current Password <sup>*</sup></label>

                                <div class="col">
                                    <input id="current-password" type="password" class="form-control" name="current-password" value="{{ old('current-password') }}">

                                    @if ($errors->has('current-password'))
                                        <span class="help-block">
                                        <strong><span style="color:red;">{{ $errors->first('current-password') }}</span></strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('new-password') ? ' has-error' : '' }}">
                                <label for="new-password" class="col-md-4 control-label">New Password <sup>*</sup></label>

                                <div class="col">
                                    <input id="new-password" type="password" class="form-control" name="new-password" value="{{ old('new-password') }}" >

                                    @if ($errors->has('new-password'))
                                        <span class="help-block">
                                        <strong><span style="color:red;">{{ $errors->first('new-password') }}</span></strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="new-password-confirm" class="col-md-4 control-label">Confirm New Password <sup>*</sup></label>
                                <div class="col">
                                    <input id="new-password-confirm" type="password" class="form-control" name="new-password-confirm" value="{{ old('new-password-confirmation') }}">
                                    @if ($errors->has('new-password-confirm'))
                                        <span class="help-block">
                                        <strong><span style="color:red;">{{ $errors->first('new-password-confirm') }}</span></strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group float-right">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Change Password
                                    </button>
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
	</main>
@endsection

@section('script')

<script type="text/javascript">

  function checkPassword(str)
  {
    
    if(document.getElementById("current-password").value == "") {
      alert("Error: Current Password cannot be blank!");
      document.getElementById("current-password").focus();
      return false;
    }

    if(document.getElementById("new-password").value == "") {
      alert("Error: New Password cannot be blank!");
      document.getElementById("new-password").focus();
      return false;
    }
    if(document.getElementById("new-password-confirm").value == "") {
      alert("Error: Confirm Password cannot be blank!");
      document.getElementById("new-password-confirm").focus();
      return false;
    }
    var re = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
    if(!re.test(document.getElementById("new-password").value)) {
      alert("Error: New Password must contain only letters,Capital letter, numbers and underscores and atleast 8 characters!");
      document.getElementById("new-password").focus();
      return false;
    }
    if(document.getElementById("new-password").value != document.getElementById("new-password-confirm").value) {
      alert("Error: Please check that you've entered and confirmed your password not match!");
      document.getElementById("new-password-confirm").focus();
      return false;
    }
    return true;
  }

  

</script>

@endsection