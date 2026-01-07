@extends('admin.layouts.ac.dashboard-theme')
@section('content')

@if($errors->any())
<div class="alert alert-info">{{$errors->first()}}</div>
@endif
<main role="main" class="inner cover mb-3">
 
 <!--FILTER STARTS FROM HERE-->
 <div class=" card-header">
      <div class=" row">
            <div class="col">
              
            </div> 
            <div class="col"></div>
      </div>
</div>

 <!--FILTER ENDS HERE-->
<section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
       <div class=" row">
            <div class="col"><h4> {!! $heading_title !!}</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; <b></b> 

                 @foreach($buttons as $button)
                <span class="report-btn">
                  <a role="button" class="btn btn-primary btn-md" href="{{ $button['href'] }}" 
                     <?php if($button['target']){?> target='_blank' 
                  <?php } ?> >{{ $button['name'] }}
                  </a>
                </span>
              @endforeach

             &nbsp;&nbsp;<button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>
              </p>
              </div>
            </div>
      </div>

      <div class="card-header">
        <div class=" row">
                            <div class="col-md-4">
                                <label>State </label> 
                                <select name="state" id="state" class="form-control" onchange="filter()">
                                    <option value="">Select State</option>
                                    @foreach($form_filters[0]['results'] as $result)
                                    @if($st_code == ($result['id']))
                                    <option value="{{$result['id']}}" selected="selected">{{$result['name']}}</option>
                                    @else
                                    <option value="{{$result["id"]}}">{{ $result["name"] }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                           
                            <?php if(isset($phases) && count($phases)>0){ ?>
                            <div class="form-group col-md-3"> <label>Election Phase</label> 
                            
                              <select name="phase" id="phase" class="form-control" onchange ="filter()">
                                <option value="all">All </option>
                              @foreach($phases as $result)
                                @if($phase==$result->PHASE_NO)
                                  <option value="{{$result->PHASE_NO}}" selected="selected" >{{$result->PHASE_NO}}</option> 
                                @else 
                                  <option value="{{$result->PHASE_NO}}" >{{$result->PHASE_NO}}</option> 
                                @endif  
                              @endforeach
                          
                              </select>
                            </div>
                          <?php }else{ ?>
                           <input type="hidden" id="phase" name="phase" value="{!! $phase !!}">
                          <?php } ?>
                        </div>
      </div>


   
 <div class="card-body">  

    <table id="" class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
         <tr>
          <th>Serial No</th>
          <th>Poll Events (Phase)</th>  
          <th>State</th>
          <th>Total ACs in Phase</th> 
          <th>Date of Issue of Gazette Notification</th> 
          <th>Last Date For Making Nominations</th> 
          <th>Date for Scrutiny of Nominations</th> 
          <th>Last Date For Withdrawl of Candidature</th> 
          <th>Date Of Poll</th> 
          <th>Date Of Counting</th> 
          <th>Date Of Completion</th>
        </tr>
        </thead>
        <tbody>
        @php  $count = 1; @endphp
         @forelse ($results as $result)
          <tr>
            <td>{{ $count }}</td>
            <td><a href="<?php echo $result['href'] ?>" style="color:#000000">Phase - {{$result['sid'] }}</a></td>

            <td><a href="<?php echo $result['href'] ?>" style="color:#000000">{!! $result['label'] !!}</a></td>

            <td><a href="<?php echo $result['href'] ?>" style="color:#000000">{{$result['acs'] }}</a></td>

            <td><a href="<?php echo $result['href'] ?>" style="<?php echo $result['start_nomi_class'] ?>">{{GetReadableDateFormat($result['start_nomi_date']) }}</a></td>

            <td><a href="<?php echo $result['href'] ?>" style="<?php echo $result['last_nomi_class'] ?>">{{GetReadableDateFormat($result['last_nomi_date']) }}</a></td>


            <td><a href="<?php echo $result['href'] ?>" style="<?php echo $result['nomi_scr_class'] ?>">{{GetReadableDateFormat($result['dt_nomi_scr']) }}</a></td>

            <td><a href="<?php echo $result['href'] ?>" style="<?php echo $result['last_wid_class'] ?>">{{GetReadableDateFormat($result['last_wid_date']) }}</a></td>

            <td><a href="<?php echo $result['href'] ?>" style="<?php echo $result['poll_date_class'] ?>">{{GetReadableDateFormat($result['poll_date']) }}</a></td>

            <td><a href="<?php echo $result['href'] ?>" style="<?php echo $result['count_date_class'] ?>">{{GetReadableDateFormat($result['count_date']) }}</a></td>

            <td><a href="<?php echo $result['href'] ?>" style="<?php echo $result['complete_date_class'] ?>">{{GetReadableDateFormat($result['complete_date']) }}</a></td>
            
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Election Schedule</td>                 
              </tr>
          @endforelse
        </tbody>
    </table>
    </div>
    </div>
  </div>
  </div>
  </section>
  </main>

<!-- Validation  JavaScript -->


<script type="text/javascript">
    function filter(){
    var url = "<?php echo url()->current(); ?>";
    var query = '';
    if(jQuery("#state").val() != '' && jQuery("#state").val() != 'undefined'){
    query += "&st_code="+jQuery("#state").val();
    }
    // if(jQuery("#district").val() != '' && jQuery("#district").val() != 'undefined'){
    // query += '&dist_no='+jQuery("#district").val();
    // }
    if(jQuery("#phase").val() != ''){
      query += '&phase='+jQuery("#phase").val();
    }
    window.location.href = url+'?'+query.substring(1);
    }
</script>

@endsection


