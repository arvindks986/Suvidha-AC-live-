@extends('admin.layouts.ac.theme')
@section('title', 'Candidate & Counting')
@section('bradcome', 'Users Assign Table')
@section('content')

<style type="text/css">
  .tableSort td:nth-child(3) {
    word-break: break-all;
  }
</style>
<main role="main" class="inner cover mb-3">
  <section>
    <div class="container mt-5">
      <div class="row">
        @if(Session::has('success_admin'))
        <div class="alert alert-success"><strong> {{ nl2br(Session::get('success_admin')) }}</strong> </div>
        @endif
        @if(Session::has('error_mes'))
        <div class="alert alert-danger"><strong> {{ nl2br(Session::get('error_mes')) }}</strong></div>
        @endif
        <!--  @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif -->

        <div class="card text-left" style="width:100%; margin:0 auto;">
          <div class=" card-header">
            <div class=" row">
              <div class="col">
                <h4 class="mr-auto">Booth Counting Table Assignment</h4>
              </div>
              <div class="col">
                <p class="mb-0 text-right"><b class="bolt">State Name:</b>
                  <span class="badge badge-info">{{$st_name}}</span> &nbsp;&nbsp; <b class="bolt">AC Name:</b>
                  <span class="badge badge-info">{{$ac_name}}</span>&nbsp;&nbsp;
                </p>
              </div>
            </div>
          </div>

          <div class="card-body">
            <div class="col">

              <p class="mb-0 text-right">
                <b class="bolt">Total User: &nbsp;</b><span class="badge badge-info">{{$usercount}}</span> &nbsp;&nbsp;
                <b class="bolt">Total Table: &nbsp;</b><span class="badge badge-info">{{$total_no_tables}}</span> &nbsp;&nbsp;
                <b class="bolt">Total Assign Table: &nbsp;</b><span class="badge badge-info">{{$totalassigntable}}</span> &nbsp;&nbsp;
                <b class="bolt">Total Not Assigned Table: &nbsp;</b><span class="badge badge-info">{{$total_unassigntable}}</span> &nbsp;&nbsp;
              </p>
            </div>
            @if($evmfinalized==0)
            @if(!isset($countingstart))
            <form class="form-horizontal" id="election_form" method="POST" action="{{url('roac/counting/verify-user-assign') }}" autocomplete='off' enctype="x-www-urlencoded">
              {{ csrf_field() }}

              <div class="form-group">
                <label>Select Counting User<sup>*</sup></label>
                <select name="users" id="users" class="form-control">
                  <option value="">Select User</option>
                  @foreach ($lists as $list ))
                  <option value="{{ $list->officername }}">{{$list->officername}}-{{strtoupper($list->name)}}</option>
                  @endforeach
                </select>
                @if ($errors->has('users'))
                <span class="text-danger">{{ $errors->first('users') }}</span>
                @endif
                <span id="errmsg" class="text-danger"></span>
              </div>
              <div class="row align-items-center">
                <div class="col-md-11 col-12">
                  <div class="form-group">
                    <label>Select Tables<sup>*</sup></label>
                    <select name="tables[]" id="tables" class="form-control sumoselect SelectSumo" data-actions-box="true" multiple="multiple">
                      <?php for ($i = 1; $i <= $total_no_tables; $i++) {
                        $v = 0;

                        if ($listassigntable != '') {
                          foreach ($listassigntable as $assign) {
                            if ($assign == $i) {
                              $v = 1;
                              break;
                            }
                          }
                        }
                      ?>
                        @if($v==0)
                        <option value="{{$i}}"> Table-{{$i}}</option>
                        @endif
                      <?php } ?>
                    </select>
                    @if ($errors->has('tables'))
                    <span class="text-danger">{{ $errors->first('tables') }}</span>
                    @endif
                    <span id="errmsg1" class="text-danger"></span>
                  </div>
                </div>
                <div class="col-md-1 col-12">
                  <div class="mt-3">
                    <input type="submit" style="position:relative;" value="Assign" placeholder="" class="btn btn-success submit-button">
                  </div>
                </div>
              </div>





            </form>
            @endif
            @endif
            <br>
            @if(!$results->isEmpty())
            <table class="example table table-striped table-bordered mt-5 tableSort" style="width:100%">
              <thead>
                <tr>
                  <th>Sr. No</th>
                  <th>User Name</th>
                  <th>Allotted Table</th>
                  <th style="width: 120px;">Date</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php $j = 0;
                $url = URL::to("/");   ?>
                @foreach($results as $list)
                <?php $j++;    ?>
                <tr>
                  <td>{{$j}}</td>
                  <td>{{$list->users_name}} </td>
                  <td>{{$list->table_no}} </td>
                  <td>{{date("d-m-Y",strtotime($list->created_at))}} </td>
                  <td>
                    @if($evmfinalized==0)
                    @if(!isset($countingstart))
                    <input type="button" value="Un-Assign" placeholder="" class="btn btn-primary pull-right" onclick="location.href='{{$url}}/roac/counting/remove-counting-users-table?id={{encrypt_string($list->id)}}';">
                    @endif
                    @endif

                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
            @else
            <div class="norecords"><i class="fa fa-ban"></i>
              <h4>No Records Found</h4>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

@endsection
@section('script')
<script type="text/javascript">
  $(document).ready(function() {

    $("#election_form").submit(function() {
      var is_error = false;
      if ($('#election_form #users').val() == "") {
        $('#election_form #users').next('.text-danger').text("please select user.").show();
        is_error = true;
      }
      if ($('#election_form #tables[]').val() == "") {
        $('#election_form #tables[]').next('.text-danger').text("please select tables.").show();
        is_error = true;

      }
      if (is_error) {
        return false;
      }
    });
  });
</script>
<script type="text/javascript">
  $(document).ready(function() {
    $('.sumoselect').SumoSelect({
      selectAll: true
    });
  });
</script>
@endsection