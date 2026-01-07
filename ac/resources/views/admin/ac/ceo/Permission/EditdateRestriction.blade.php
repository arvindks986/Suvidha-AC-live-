@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content') 


<main role="main" class="inner cover mb-3 mb-auto">
   @include('admin.ac.ceo.Permission.permission-master-menu')
    @if (session('chckmessage'))
    <div class="alert alert-danger">
        {{ session('chckmessage') }}
    </div>
    @endif
    <section class="mt-5" id="wrapper">
<div class="container">
<div class="row">
<div class="col-lg-12 p-0">
<div class="sidebar__inner">
              <div class="card"><!--  style="max-width:700px; margin:0 auto;" -->
                <div class="card-header d-flex align-items-center">
                  <h2>Edit Permission Date Restriction</h2>
                </div>
                   @if (Session::has('message'))
                    <div class="alert alert-success">
                        {{ session()->get('message') }}
                    </div>
                   @endif
             <div class="card-body getpermission">
			
			 
			 
                      <form class="form-horizontal" method="POST" action="{{url('/acceo/updatedaterestriction')}}">
                          {{csrf_field()}}
                       		
			<div class="form-group row">
                          <label class="col-sm-4 form-control-label">Check if you want to enable date restriction in online mode <sup>*</sup></label>
                         @if(!empty($restrictdata))
                          <div class="form-check">
                              @if($restrictdata->restriction_status == 1)
                              <input class="form-check-input" type="checkbox" name="daterestriction" value="1" id="inlineFormCheck" checked="checked">
                                @else
                                <input class="form-check-input" type="checkbox" name="daterestriction" value="1" id="inlineFormCheck">
                                @endif
                           </div>
                         @endif
                        </div>	  
                      
                    </div>
					<div class="card-footer">
						     <div class="form-group row">
                         
                          <div class="col">
                           <button class="btn btn-success float-right" name="submit" value="ADD">Update</button>
                          </div>
                        </div>
					</div>
                   </form>
					
              </div>
           </div>
            </div>  
            </div>
</div>

</section>
</main>



@endsection
