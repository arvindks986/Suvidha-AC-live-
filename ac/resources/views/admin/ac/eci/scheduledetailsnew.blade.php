@extends('admin.layouts.themenew')
@section('title', 'Create Schedule')
@section('content') 

<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
   <div class="nw-crte-usr">
         <div class="head-title">
          <h3><i><img src="{{ asset('admintheme/images/icons/tab-icon-010.png')}}" /></i>Schedule Details</h3>
         </div>
			
			<button class="accordion">Phase 1</button>
			<div class="panel">
				<div class="col-sm-12"  > 
					 <div class="form-group">
						<label class="control-label col-sm-3" for="">Name:<span>*</span></label>
						<div class="col-sm-9">
						  <input type="text" class="form-control" placeholder="Enter Name" name="">
						</div>
					 </div>
					 <div class="form-group">
						<label class="control-label col-sm-3" for="">Name:<span>*</span></label>
						<div class="col-sm-9">
						  <input type="text" class="form-control" placeholder="Enter Name" name="">
						</div>
					 </div>
				</div>
			</div>

			<button class="accordion">Phase 2</button>
			<div class="panel">
			  <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
			</div>


          
         
          </div><!-- End Of nw-crte-usr Div -->
   
       <!--    Listing -->


          </div>
           
          </div>  
        </div><!-- End Of intra-section Div -->   
        </div><!-- End Of page-sub-setion Div -->
      
    </div><!-- End OF page-contant Div -->


       <!-- end list-->
    
    </div> <!-- End Of child-area Div -->     
  </div><!-- End Of parent-wrap Div -->
  </div> 
@endsection

@section('script')
<script>
var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
    if (panel.style.maxHeight){
      panel.style.maxHeight = null;
    } else {
      panel.style.maxHeight = panel.scrollHeight + "px";
    } 
  });
}
</script>
@endsection