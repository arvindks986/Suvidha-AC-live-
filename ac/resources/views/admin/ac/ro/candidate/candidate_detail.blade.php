@extends('admin.layouts.ac.theme')
@section('content')
<style type="text/css">
  .loader {
   position: fixed;
   left: 50%;
   right: 50%;
   border: 16px solid #f3f3f3; /* Light grey */
   border-top: 16px solid #3498db; /* Blue */
   border-radius: 50%;
   width: 120px;
   height: 120px;
   animation: spin 2s linear infinite;
   z-index: 99999;
  }
      @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
    }
  </style>

  <div class="loader" style="display:none;"></div>
<main role="main" class="inner cover mb-3">
  @forelse ($EciViewNomination as $key=>$listdata)

    <section class="mt-5">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h4>View Candidate Information</h4>
          </div>
          <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <div class="avatar-upload">
                 
                @if($listdata->cand_image != '' )
                  <div class="avatar-preview">
                    <div id="imagePreview">
                      <img src="{{url($listdata->cand_image)}}" height="180" width="180"/>
                    </div>
                  </div>
                @else
                  <div class="avatar-preview">
                    <div id="imagePreview"><img src="{{url(img/vendor/no-image-cand.png)}}" height="180" width="180"/></div>
                  </div>
                @endif
                 
              </div>
               
            </div>

            <div class="col"> 

            <div class="form-group row mt-5">
              <label class="col-sm-4">Candidate Name </label>
              <div class="col-sm-8"> 
                <div class="" style="width:100%;">{{$listdata->cand_name}} &nbsp;&nbsp;{{$listdata->cand_hname}}&nbsp;&nbsp;{{$listdata->cand_vname}}</div>
              </div>
            </div>         

            <div class="form-group row mt-5">
              <label class="col-sm-4">Party Name </label>
              <div class="col-sm-8"> 
                <div class="" style="width:100%;"> {{ $listdata->PARTYNAME }}</div>
              </div>
            </div>
            
            <div class="form-group row">
              <label class="col-sm-4">Symbol </label>
              <div class="col-sm-8">
               <div class="" style="width:100%;">{{$listdata->SYMBOL_DES}} </div>
              </div>
            </div>
           
            </div>
          </div>
          </div>
        </div>
        </div>
      </div>
    </div>    
    </section>
    <section class="">
    <div class="container">
      <div class="row">
      
        <div class="col-md-12">
        <div class="card">
                <div class="card-header d-flex align-items-center">
                  <h4>Candidate Personal Details</h4>
                </div>
                <div class="card-body">
        <div class="row">
        
          <div class="col">                  
                  <form class="form-horizontal">
                    <!--<div class="form-group row">
                      <label class="col-sm-3">Name</label>
                      <div class="col">{{$listdata->cand_name}}  </div>  
             <div class="col">{{$listdata->cand_hname}} </div>
             <div class="col">{{$listdata->cand_vname}} </div>
                    </div>
          <div class="form-group row">
                      <label class="col-sm-3">Candidate Alias Name </label>
                      <div class="col">{{$listdata->cand_alias_name}}  </div>  
             <label class="col-sm-3">Hindi</label><div class="col">{{$listdata->cand_alias_hname}} </div>
              
                   </div>-->
          
          <div class="form-group row">
                      <label class="col-sm-3">Father's / Husband's Name </label>
                      <div class="col">{{$listdata->candidate_father_name}}  </div>  
             <label class="col-sm-3">Hindi</label><div class="col">{{$listdata->cand_fhname}} </div>
             
                    </div>
           
          
          <div class="form-group row">
            <label class="col-sm-3">Email </label>
                        <div class="col">{{$listdata->cand_email}} </div>
               
              <label class="col-sm-3">Mobile No </label>
             <div class="col">{{$listdata->cand_mobile}} </div>
               
                    </div>
          
          
          <div class="form-group row">
                      <label class="col-sm-3">Gender </label>
                <div class="col">{{$listdata->cand_gender}} </div>
               
           <label class="col-sm-3">PAN Number </label>
            <div class="col">{{$listdata->cand_panno}} </div>
             
          </div>
            <div class="form-group row">
              <!--<label class="col-sm-3">Date of Birth </label>
              <div class="col"> </div>-->
               
              <label class="col-sm-3">Age </label>
              <div class="col">{{$listdata->cand_age}} </div>
            </div>
                    
                     
            <div class="form-group row">
                      <label class="col-sm-3">Address</label>
                      <div class="col">{{$listdata->candidate_residence_address}} </div>
                      <label class="col-sm-3">Address In Hindi </label>  
            <div class="col">{{$listdata->candidate_residence_addressh}} </div>
             
                    </div>
           
           
          
          <div class="form-group row">
          <div class="col-sm-3"><label for="statename">State Name </label></div>
          <div class="col">{{$listdata->ST_NAME}} </div>
             
          <div class="col-sm-3"><label for="statename">AC Name </label></div>
          <div class="col">{{$listdata->AC_NAME}} </div>
          
          </div> 
          <div class="form-group row">
          
       
             
           
          
          <div class="col-sm-3"><label for="statename">Category </label></div>
            <div class="col">{{$listdata->cand_category}} </div>
               
             
          </div> 
         
         <div class="form-group row float-right">       
            <div class="col">
              <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>
             
            </div>
         </div>
                </form>
          </div>
        </div>
                </div>
              </div>
        </div>
      </div>
    </div>    
    </section>

    @empty
        <tr>
          <td colspan="5">No Data Found For Candidate</td>                 
      </tr>
   @endforelse

</main>

@endsection