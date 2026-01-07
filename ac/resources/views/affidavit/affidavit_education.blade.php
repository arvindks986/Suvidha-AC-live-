@extends( (Auth::user()->role_id != '19') ? 'layouts.theme' : 'admin.layouts.ac.theme')
@section('title', 'Affidavit Education Details') @section('content')
<style type="text/css">
.affidavit_nav .step-current a,.affidavit_nav .step-success a{
    color:#fff!important;
}
.affidavit_nav a{
    color:#999!important;
}

.err {
    white-space: pre;
    color: red;
    font-size: 11px;
    font-weight: 600;
}
.error {
    font-size: 12px;
    color: red;
}
.step-wrap.mt-4 ul li {
    margin-bottom: 21px;
}
.panel-heading.active {
    background-color: #2c963c;
}
.panel-title {
    font-size: 14px;
}
.panel-title > a {
    display: block;
    padding: 15px;
    text-decoration: none;
}
.more-less {
    float: right;
    color: #212121;
}
.width100{
    width: 100px !important;
}
            .accordion_head {
                font-size: 20px;
                padding: 8px 15px 1px;
                background-color: #007bff;
                color: white;
                cursor: pointer;                
                margin: 5px 0 10px 0;               
                border-radius: 4px;
                overflow: hidden;
                box-shadow: 0 4px 4px -2px rgba(0, 0, 0, 0.5);
                      
            }
            .accordion_body {
                width: 100%;
                padding: 1em;
                box-shadow: 0 4px 4px -2px rgba(0, 0, 0, 0.5);
                margin-top: -10px;
                background: #fafafa;
                border: #e9e9e9 solid 1px;
            }            
            .plusminus {
              float: right;
              font-size: 30px;
              margin-top: -5px;
            }
            .purple {
                background-color: #4e6fd9;
            }
            .purpleTable th{
                background-color: #4e6fd9!important;
                color: #ffffff;
            }
            .nextBtn, button.nextBtn {
                border: 2px solid #9b59b6;
                padding: 0.65em 1.2em;
                border-radius: 2.5em;
                cursor: pointer;
                min-width: 131px;
                text-align: center;
                transition: all 0.25s;
                margin: 1em auto;
                box-sizing: border-box;               
                display: block;
                font-weight: 500;
                color: #9b59b6;
                outline: none;
                white-space: nowrap;
            }
            .nextBtn:hover , button.nextBtn:hover {
                background-color: #9b59b6;
                color: white;
                outline: none;
                box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
            }  

            .cencelBtn, button.cencelBtn {
                min-width: 131px;
                text-align: center;
                border: 2px solid #dc3545;
                padding: 0.65em 1.2em;
                border-radius: 2.5em;
                cursor: pointer;
                transition: all 0.25s;
                margin: 1em auto;
                box-sizing: border-box;               
                display: block;
                font-weight: 500;
                outline: none;
                white-space: nowrap;
                color: #dc3545;
                text-decoration: none!important;
            }
            .cencelBtn:hover , button.cencelBtn:hover {
                background-color: #dc3545;
                color: white;
                outline: none;
                box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
            }  

            .backBtn, button.backBtn {
                min-width: 131px;
                text-align: center;
                border: 2px solid #868e96;
                padding: 0.65em 1.2em;
                border-radius: 2.5em;
                cursor: pointer;
                transition: all 0.25s;
                margin: 1em auto;
                box-sizing: border-box;               
                display: block;
                font-weight: 400;
                outline: none;
                white-space: nowrap;
                text-decoration: none;
                color:#868e96;
            }
            .backBtn:hover , button.backBtn:hover {
                background-color: #868e96;
                color: white;
                outline: none;
                text-decoration: none;
                box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
            } 
            .footerSection{
                width: 100%;
                background: transparent!important;
            }
            .main_heading {
                position: relative;
                font-size: 1.50rem;
                font-weight: 600;
                margin-top: 12px;
                margin-bottom: 10px;
                text-align: center;
                color: #101010;
                padding-bottom: 7px;
            }
            .main_heading::before {
                background: #d0d0d0;
                bottom: -2px;
                content: "";
                height: 1px;
                left: 50%;
                position: absolute;
                transform: translateX(-50%);
                width: 200px;
            }
            .main_heading::after {
                background: #ed457e;
                bottom: -3px;
                content: "";
                height: 3px;
                left: 50%;
                position: absolute;
                transform: translateX(-50%);
                width: 50px;
            }
            .modal-dialog .close, .modal-content button:hover {
                opacity: 1;
                color: #fff;                
                box-shadow: none;
                outline: 0;
            }
            .modal button.close {
                background-color: #f0587e;
                padding: 8px 16px;
                border: none;
                font-size: 20px;
                border: none;
                border: 1px solid #f0587e;
            }
			
			.step-wrap {
			    text-align: center;
			}
			.step-wrap>ul>li {      
			    border-radius: 25px;            
			    padding: 0.15rem 1.05rem 0.15rem 0.18rem;
			}
			.step-wrap>ul>li>span {
			    display: inline-block;
			    vertical-align: middle;
			    width: 60px!important;
			    color: #999;
			    font-size: 0.80rem!important;
			    text-align: center;
			    line-height: 0.95rem!important;
			}

</style>
<link rel="stylesheet" href="{{ asset('appoinment/css/custom-dark.css') }} " type="text/css" /> 
<main role="main" class="inner cover mb-3">
    <section>
        <div class="container-fluid">
            @if (session('flash-message'))
            <div class="alert alert-success mt-4">{{session('flash-message') }}</div>
            @endif @if ($message = Session::get('Init'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
        </div>
    </section>
	
<?php if(Auth::user()->role_id == '19'){
	$menu_action = 'roac/';
}else{
	$menu_action = '';
} ?>	
	
	
 <div class="container-fliud">
        <div class="step-wrap mt-4">
            <ul class="affidavit_nav">
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'affidavitdashboard')}}">{{Lang::get('affidavit.initial_details') }}</a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'affidavit/candidatedetails')}}">{{Lang::get('affidavit.candidate_details') }}</a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'affidavit/pending-criminal-cases')}}">{{Lang::get('affidavit.court_cases') }}</a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'Affidavit/MovableAssets')}}">{{Lang::get('affidavit.movable_assets') }}</a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'immovable-assets')}}">{{Lang::get('affidavit.immovable_assets') }}</a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'liabilities')}}">{{Lang::get('affidavit.liabilities') }}</a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="{{url($menu_action.'Profession')}}">{{Lang::get('affidavit.profession') }}</a></span></li>
                <li class="step-current"><b>&#10004;</b><span><a href="{{url($menu_action.'education')}}">{{Lang::get('affidavit.education')}}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'preview')}}">{{Lang::get('affidavit.preview_finalize') }}</a></span></li>
                <li class=""><b>&#10004;</b><span><a href="{{url($menu_action.'part-a-detailed-report')}}">{{Lang::get('affidavit.reports') }}</a></span></li>
            </ul>
        </div>
    </div>
    <section>
        <div class="col-md-12">
            <div class="row">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="main_heading">{{Lang::get('affidavit.education')}}</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- New design implement -->
                            <div class="accordion_head">{{Lang::get('affidavit.education')}}<span class="plusminus">+</span></div>
                                <div class="accordion_body" style="display: none">
                                     <table id="relative" class="table table-bordered table-hover purpleTable w-100">
                                        <thead>
                                            <tr>
                                                <th>{{Lang::get('affidavit.qualification')}}</th>
                                                <th>{{Lang::get('affidavit.full_form_certificate')}}</th>
                                                <th>{{Lang::get('affidavit.school_college')}}</th>
                                                <th>{{Lang::get('affidavit.board_university')}}</th>
                                                <th>{{Lang::get('affidavit.year_of_completion')}}</th>
                                                <th>{{Lang::get('affidavit.action')}}</th>          
                                            </tr>
                                        </thead>
                                        <tbody>                                
                                             @if(!empty($education))
                                                @foreach($education as $row)
                                                <tr id="tr{{$row->id}}">
                                                    <td>{{$row->qualification}}</td>     
                                                    <td>{{$row->full_form_course}}</td>     
                                                    <td>{{$row->school_college}}</td>     
                                                    <td>{{$row->board_univ}}</td>
                                                    <td>{{$row->q_year}}</td>     
                                                    <td nowrap="nowrap">
                                                        <a href="javascript:void(0)" class=" btn btn-info btn-sm" title="{{Lang::get('affidavit.edit') }}" onclick="javascript:open_modal({{$row->id}})"
                                                        data-qualification="{{$row->qualification}}"
                                                        data-full_form_course="{{$row->full_form_course}}"
                                                        data-school_college="{{$row->school_college}}"
                                                        data-board_univ="{{$row->board_univ}}"
                                                        data-q_year="{{$row->q_year}}"
                                                        data-candidate_id="{{$row->candidate_id}}"
                                                        id="edit_education{{$row->id}}">
                                                    <i class="fa fa-edit"></i> {{Lang::get('affidavit.edit') }}
                                                </a>
												@if(Auth::user()->role_id != '19')
                                                <a href="javascript:void(0)" class="btn btn-danger btn-sm" title="{{Lang::get('affidavit.delete') }}" onclick="javascript:delete_education({{$row->id}})">
                                                    <i class="fa fa-times"></i> {{Lang::get('affidavit.delete') }}
                                                </a>
												@endif
                                                </td>   
                                                </tr>
                                               
                                                @endforeach                                                        
                                            @endif
                                          
										  @if(Auth::user()->role_id != '19')
                                            <form id="form">
                                            <tr id="education">

                                                <td>
                                                    <input type="text" onkeypress="return blockSpecialChar(event)"  name="qualification" id="qualification" class="form-control" required="required" >
                                                </td>
                                                
                                                <td>
                                                    <input type="text"  name="full_form_course" class="form-control" id="full_form_course" required="required" onkeypress="return blockSpecialChar(event)">
                                                </td>
                                                
                                                <td>
                                                    <input type="text" class="form-control"  name="school_college" id="school_college"required="required" onkeypress="return blockSpecialChar(event)">
                                                </td>   
                                                
                                                <td>
                                                    <input type="text" class="form-control" name="board_univ" id="board_univ" required="required" onkeypress="return blockSpecialChar(event)">
                                                </td>

                                                <td>
                                                    <input type="text" class="form-control" name="q_year" id="q_year" onkeydown="return NumbersOnly(event,this)" maxlength="4" required="required">
                                                </td>
                                                
                                                <td> 
                                                  <a href="javascript:void(0)" title="Edit" id="save" onclick="javascript:save_education()" >
                                                    <span class=" btn btn-info btn-sm"><i class="fa fa-check"></i> {{Lang::get('affidavit.save') }}</span>
                                                </a>
                                                </td>
                                            </tr>
                                        </form>
										
										@endif
										
                                        </tbody>
                                    </table>
                                </div>    
                            <!-- New Design implement -->
                        </div>
                        <div class="card-footer footerSection">
                            <div class="row">                               
                                <div class="col-12">
                                    <a href="{{url($menu_action.'Profession') }}" class="float-left backBtn">{{Lang::get('affidavit.back') }}</a>
                                    <a href="{{url($menu_action.'preview')}}" type="submit" class="float-right nextBtn">{{Lang::get('affidavit.save')}} &amp; {{Lang::get('affidavit.next') }}</a>
                                     &nbsp; &nbsp; &nbsp;<a href="{{url()->previous() }}" class="float-right cencelBtn mr-2">{{Lang::get('affidavit.cancel') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Educational Qualification Edit Modal Start-->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.edit_educational_qualification_details')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="education_model">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="Qualification">{{Lang::get('affidavit.qualification')}}:</label>
                        <input type="text" class="form-control" name="modal_qualification" id="modal_qualification" required="required">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="Full Form Certificate/Deploma/Degree/Course">{{Lang::get('affidavit.full_form_certificate')}}:</label>
                        <input type="text" class="form-control" name="modal_full_form_course" id="modal_full_form_course" required="required">
                    </div>
                </div>
            </div>
            <div class="row">
                
				<div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{Lang::get('affidavit.school_college')}}:</label>
						<input type="text" class="form-control" name="modal_school_college" id="modal_school_college" required="required">
                    </div>
                </div>
				
				<div class="col-md-6">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.board_university')}}:</label>
						<input type="text" class="form-control" name="modal_board_univ" id="modal_board_univ" required="required">
                    </div>
                </div>

            </div>			
			
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">{{Lang::get('affidavit.year_of_completion')}}:</label>
                        <input type="text" class="form-control" name="modal_q_year" id="modal_q_year" onkeypress="return NumbersOnly(event,this)" maxlength="4" required="required">
                    </div>
                </div>
				
            </div>
			
            <input type="hidden" name="modal_education_id" id="modal_education_id">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.close')}}</button>
        <button type="button" class="btn btn-primary" onclick="javascript:update_education()">{{Lang::get('affidavit.update')}}</button>
      </div>
    </div>
  </div>
</div>

<!-- Educational Qualification Edit Modal End-->

<!-- Educational Qualification Delete Modal Start-->
<div class="modal fade coustoModal" id="deleteAgriculturalLandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">{{Lang::get('affidavit.delete_educational_qualification_details')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
            <h5>{{Lang::get('affidavit.are_you_sure_to_delete_this_entry')}}</h5>
            <input type="hidden" name="modal_education_id" id="modal_education_id">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{Lang::get('affidavit.no')}}</button>
        <button type="button" class="btn btn-primary" onclick="javascript:delete_education_entry()">{{Lang::get('affidavit.yes')}}</button>
      </div>
    </div>
  </div>
</div>
<!-- Educational Qualification Delete Modal End-->


@endsection @section('script')
<!-- <script type="text/javascript" src="{{ asset('admintheme/js/jquery-ui.js') }}"></script> -->
<script type="text/javascript" src="{{ asset('affidavit/js/remove_special_character.js') }}"></script>
<script type="text/javascript" src="{{ asset('affidavit/js/affidavit_validation.js') }}"></script>
<script type="text/javascript" src="{{ asset('affidavit/js/sweetalert.js') }}"></script>
<script type="text/javascript" src="{{ asset('affidavit/js/jquery-ui.js') }}"></script>
 <script>
    
  function NumbersOnly(evt,obj) {
   var charCode = (evt.which) ? evt.which : evt.keyCode;
   /* if(charCode == 190 || charCode == 110)
   {
        return true;
   }else */ 
   if (charCode >= 96 && charCode <= 106) {
       return true;
   }else if (charCode > 31 && (charCode < 48 || charCode > 57)) {
       return false;
   } else {
       if(charCode != 32)
       {
           return true;
       }
       else
       {
           return false;
       }
   }
   }
</script>
<script type="text/javascript">
jQuery(function ($) {
      var $active = $('#accordion .panel-collapse.in').prev().addClass('active');
      $active.find('a').prepend('<i class="glyphicon glyphicon-minus"></i>');
      $('#accordion .panel-heading').not($active).find('a').prepend('<i class="glyphicon glyphicon-plus"></i>');
      $('#accordion').on('show.bs.collapse', function (e) {
          $('#accordion .panel-heading.active').removeClass('active').find('.glyphicon').toggleClass('glyphicon-plus glyphicon-minus');
          $(e.target).prev().addClass('active').find('.glyphicon').toggleClass('glyphicon-plus glyphicon-minus');
      })
});
</script>

<link rel="stylesheet" type="text/css" href="{!! url('admintheme/css/jquery-ui.css') !!}">
<script type="text/javascript" src="{!! url('admintheme/js/jquery-ui.js') !!}"></script>
<script type="text/javascript" src="{!! url('js/jquery.validate.min.js') !!}"></script>
<script type="text/javascript" src="{!! url('js/additional-methods.min.js') !!}"></script>
<script type="text/javascript">
$(document).ready(function() {  
  $(".datepicker").datepicker({
    dateFormat: 'yy-mm-dd'
  });
}); 
</script>

<!-- Educational Qualification Script Start-->


<script type="text/javascript">
function save_education()
{	
    var qualification 				= $("#qualification").val();
    var full_form_course 			= $("#full_form_course").val();
    var school_college 				= $("#school_college").val();
    var board_univ 					= $("#board_univ").val();
    var q_year 						= $("#q_year").val();

	
	if(validate("education")){
	$.ajax({
        url: "{{ url('save_education') }}",
        type: 'GET',
        data: { 
                qualification:qualification, 
                full_form_course:full_form_course,
                school_college:school_college, 
                board_univ:board_univ, 
                q_year:q_year
        },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
            if(data!=0)
            {

				datas = JSON.parse(data);
				
                var edit = '<a href="javascript:void(0)" title="Edit"onclick="javascript:open_modal('+datas.id+')"  data-qualification="'+qualification+'" data-full_form_course="'+full_form_course+'" data-school_college="'+school_college+'"  data-board_univ="'+board_univ+'" data-q_year="'+q_year+'" id="edit_education'+datas.id+'"> <span class="btn btn-info btn-sm"><i class="fa fa-edit"></i> Edit</span> </a>';
                var del = '<a href="javascript:void(0)" title="Delete" onclick="javascript:delete_education('+datas.id+')"><span class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Delete</span></a>';

                 $('#relative').prepend('<tr id="tr'+datas.id+'"><td>'+qualification+'</td><td>'+full_form_course+'</td><td>'+school_college+'</td><td>'+board_univ+'</td><td>'+q_year+'</td><td>'+edit+' '+del+'</td></tr>');

                $("#qualification").val('');
                $("#full_form_course").val('');
                $("#school_college").val('');
                $("#board_univ").val('');
                $("#q_year").val('');
            }
        }
    });
	}
}
</script>

<script type="text/javascript">
    function open_modal(id)
    {
        var qualification = "";
        var full_form_course =  "";
        var school_college =  "";
        var board_univ =  "";
        var q_year =  "";

        qualification 		= $("#edit_education"+id).data("qualification");
        full_form_course 	= $("#edit_education"+id).data("full_form_course");
        school_college 		= $("#edit_education"+id).data("school_college");
        board_univ 			= $("#edit_education"+id).data("board_univ");
        q_year 				= $("#edit_education"+id).data("q_year");
       
        $("#modal_qualification").val(qualification);
        $("#modal_full_form_course").val(full_form_course);
        $("#modal_school_college").val(school_college);
        $("#modal_board_univ").val(board_univ);
        $("#modal_q_year").val(q_year);
        $("#modal_education_id").val(id);
		$("#exampleModal").modal('show');
    }
</script>



<script type="text/javascript">
function update_education()
{
	//alert(12);

    var qualification 			= $("#modal_qualification").val();
    var full_form_course 		= $("#modal_full_form_course").val();
    var school_college 			= $("#modal_school_college").val();
    var board_univ 				= $("#modal_board_univ").val();
    var q_year 					= $("#modal_q_year").val();
    var id 						= $("#modal_education_id").val();
  console.log(validate("education_model"));
  if(validate("education_model")){
    $.ajax({
        url: "{{ url($menu_action.'update_education') }}",
        type: 'GET',
        data: { 
                id:id, 
                qualification:qualification, 
                full_form_course:full_form_course,
                school_college:school_college, 
                board_univ:board_univ, 
                q_year:q_year
        },            
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(data){
            if(data!=0)
            {
                datas = JSON.parse(data);
				
				$('#tr'+id).remove();

                var edit = '<a href="javascript:void(0)" title="Edit"onclick="javascript:open_modal('+datas.id+')"  data-qualification="'+qualification+'" data-full_form_course="'+full_form_course+'" data-school_college="'+school_college+'"  data-board_univ="'+board_univ+'" data-q_year="'+q_year+'" id="edit_education'+datas.id+'"> <span class="btn btn-info btn-sm"><i class="fa fa-edit"></i> Edit</span> </a>';
				
				<?php if(Auth::user()->role_id != '19') { ?>
					
                var del = '<a href="javascript:void(0)" title="Delete" onclick="javascript:delete_education('+datas.id+')"><span class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Delete</span></a>';
				
				<?php } else { ?>
				var del = '';	
				<?php } ?>

                 $('#relative').prepend('<tr id="tr'+datas.id+'"><td>'+qualification+'</td><td>'+full_form_course+'</td><td>'+school_college+'</td><td>'+board_univ+'</td><td>'+q_year+'</td><td>'+edit+' '+del+'</td></tr>');

                $("#exampleModal").modal('hide');
            }
        }
    });
  }else{
	 $("#exampleModal").modal('show'); 
  }
}
</script>


<script type="text/javascript">
function delete_education(id)
{
	$("#modal_education_id").val(id);
    $("#deleteAgriculturalLandModal").modal('show');
}
</script>

<script type="text/javascript">
function delete_education_entry()
{
   var id = $("#modal_education_id").val();
   if(id)
   {
        $.ajax({
            url: "{{ url('delete_education') }}",
            type: 'GET',
            data: {  id:id },            
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
            if(data==1)
            {
                $('#tr'+id).remove();
                $("#deleteAgriculturalLandModal").modal('hide');
            }
            }
        });
   }
}
</script>

<!-- Educational Qualification Script End-->
<!-- validation -->
<script type="text/javascript">
function validate(formval)
{
    if(formval)
    {
        var result = true;
        $('#'+formval+' :input').each(function()
        {
            if($(this).prop('required')) 
            {
                var value = $(this).val();
                var id = $(this).attr('id');
                $("#span_"+id).remove();
                /*if(id=="modal_make")
                {
                    alert(id);
                    alert(value);
                    alert(value.length);
                }*/
                if(!value || value=='' || value.length==0 || value <= 0)
                {                  
                    $('#'+id).after('<span class="err" id="span_'+id+'">{{Lang::get("affidavit.this_field_is_required") }}</span>');      
                    $('#'+formval).css("border-color", "solid 1px red");          
                    result =  false;
                }
            }
        });
        return result;
    }
}


$(document).ready(function() {            
            $(".accordion_head").click(function() {               
              if ($('.accordion_body').is(':visible')) {
                 $(".accordion_body").slideUp(500);
                 $(".plusminus").text('+');                                
              }
              if ($(this).next(".accordion_body").is(':visible')) {
                $(this).next(".accordion_body").slideUp(500);
                $(this).children(".plusminus").text('+');                
              } else {
                $(this).next(".accordion_body").slideDown(500);
                $(this).children(".plusminus").text('-');               
              }
            });
          }); 


var minLength = 3;
$(document).ready(function(){
    $('#full_form_course').on('keydown keyup change', function(){
        var char = $(this).val();
        var charLength = $(this).val().length;
		
		$("#span_full_form_course").remove();
		
        if(charLength < minLength){
			$('#full_form_course').after('<span class="err" id="span_full_form_course">{{Lang::get("affidavit.length_is_short_minimum_required") }}</span>');      
            $('#full_form_course').css("border-color", "solid 1px red"); 			
		}else{
            //$('#full_form_course').text('Length is valid');
        }
    });
});

</script>
<!-- validation -->

@endsection
