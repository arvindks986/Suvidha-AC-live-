@extends('admin.layouts.ac.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
@section('description', '')
@section('content') 
<main role="main" class="inner cover mb-3">
   <section>
      <form enctype="multipart/form-data" id="entryForm" method="POST" action="/eci/storeMasterEntry">
{{ csrf_field() }}
         <div class="container">
            <div class="row">
               @if (Session::has('message'))
            <div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>{{ Session::get('message') }} </div> 
        @php Session::forget('message'); @endphp
        @elseif (Session::has('error'))
            <div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                {{ Session::get('error') }} <br/>
          
            </div>
        @php Session::forget('error'); @endphp
        @endif
               <div class="card text-left" style="width:100%; margin:0 auto;">
                  <div class="card-header">
                     <div class="row">
                        <div class="col">
                           <h4>Master Data Entry</h4>
                        </div>
                     </div>
                     <!--End row-->
                  </div>
                  <!--End card-header-->
                  <div class="container p-0">
                     <div class="row">
                        <div class="col-md-12">
                           <div class="card">
                              <div class="card-body">
                                 <div class="row">
                                    <div class="col">
                                       <div class="form-group row">
                                          <label class="col-sm-3">Date of Declaration Result <sup>*</sup></label>
                                          <div class="col">

               <input id="name" type="date" class="form-control" name="result_declaration_date" id="result_declaration_date" value="<?php echo !empty($singleMaster->result_declaration_date)?$singleMaster->result_declaration_date:"" ?>" required="required">
               <input type="hidden" name="master_id" value="<?php echo !empty($singleMaster->id)?$singleMaster->id:"";?>">
                                          </div>
                                       </div>
                                       <div class="line"></div>
                                       <div class="row">
                                          <div class="col">
                                             <div class="form-group row">
                                                <label class="col-sm-3">Type of Election<sup>*</sup></label>
                                                <div class="col">
                                                   <select name="type_of_election" id="type_of_election" required="required" class="form-control" >
                                                      <option>Select Election Type</option>
                                                      <option value="General" <?php if(!empty($singleMaster) && $singleMaster->type_of_election=="General"){ echo "selected";}?>>General</option>
                                                      <option value="Bye" <?php if(!empty($singleMaster) && $singleMaster->type_of_election=="Bye"){ echo "selected";}?>> Bye</option>
                                                   </select>
                                                </div>
                                             </div>
                                             <div class="line"></div>
                                             <div class="row">
                                                <div class="col">
                                                   <div class="form-group row">
                                                      <label class="col-sm-3">State<sup>*</sup></label>
                                                      <div class="col">
                                                         <select name="st_code" id="st_code" class="form-control">
                          <option value="all">Select name of State</option>
                         @php $statelist = getallstate(); @endphp

                          @foreach ($statelist as $state_List )
                <option <?php if(!empty($singleMaster->st_code) && $singleMaster->st_code == $state_List->ST_CODE){ echo "selected"; } ?>  value="{{ $state_List->ST_CODE }}" >{{$state_List->ST_NAME}}</option>
                          
                          @endforeach

                          @if ($errors->has('state'))
                          <span class="help-block">
                              <strong class="user">{{ $errors->first('state') }}</strong>
                          </span>
                          @endif
                      
                      </select>
                                                      </div>
                                                   </div>
                                                   <div class="line"></div>
                                                   <div class="row">
                                                      <div class="col">
                                                         <div class="form-group row">
                                                            <label class="col-sm-3">Ceiling Amount<sup>*</sup></label>
                                                            <div class="col">
                                                               <input id="ceiling_amt" type="text" class="form-control" name="ceiling_amt" value="<?php echo !empty($singleMaster->ceiling_amt)?$singleMaster->ceiling_amt:"" ?>" pattern="[0-9]{1,7}" maxlength="7" required="required">
                                                            </div>
                                                         </div>
                                                         <div class="line"></div>
                                                         <div class="row">
                                                            <div class="col">
                                                               <div class="form-group row">
                                                                  <label class="col-sm-3">Last date for lodging of expenditure accounts by the candidate<sup>*</sup></label>
                                                                  <div class="col">
                                                                     <input id="lodged_date" type="date" class="form-control" name="lodged_date" value="<?php echo !empty($singleMaster->lodged_date)?$singleMaster->lodged_date:"" ?>" required="required">
                                                                  </div>
                                                               </div>
                                                               <div class="line"></div>
                                                            </div>
                                                         </div>
                                                         <div class="form-group row float-right">
                                                            <div class="col">
                                                               <button type="submit" class="btn btn-primary">Submit</button>
                                                            </div>
                                                         </div>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </form>
   </section>
</main>

<script type="text/javascript">
	$(document).ready(function() {
    $("#entryForm").validate({
        rules: {
            result_declaration_date: {required:true},
            type_of_election: {required:true},
            st_code: {required:true},
            ceiling_amt: {number:true,required:true},
            lodged_date: {required:true}
        },
        messages: {
            result_declaration_date: "Please enter Date of Declaration Result",
            type_of_election: "Please enter Type of Election",
            st_code: "Please enter State",
            ceiling_amt: "Please enter Ceiling Amount",
            lodged_date: "Please enter Last date for lodging of expenditure accounts by the candidate"

        }
    });
    });
</script>
@endsection