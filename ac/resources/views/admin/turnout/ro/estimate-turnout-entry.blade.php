@extends('admin.layouts.ac.theme')
@section('bradcome', 'Estimate Turnout Entry')
@section('content')
<?php

use Illuminate\Support\Facades\URL;

$url = URL::to("/");
$j = 0;
$current_date = date("Y-m-d H:i:s");
$p1 = date("Y-m-d") . " 07:30:00";
$p2 = date("Y-m-d") . " 07:30:00";
$p3 = date("Y-m-d") . " 07:30:00";
$p4 = date("Y-m-d") . " 07:30:00";
$p5 = date("Y-m-d") . " 07:30:00";
$p6 = date("Y-m-d") . " 07:30:00";
?>

<style>
  .modal-big .modal-dialog {
    max-width: 900px;
  }

  .modal-big .modal-header {
    background-color: #f0587e;
    color: #fff;
    text-shadow: 1px 1px 1px #666;
    text-align: center;
  }

  .mcenter {
    font-size: 18px;
    line-height: 30px;
  }
</style>

<main role="main" class="inner cover mb-3">
  <section class="mt-3">
    <div class="container jumborton card">
      <div class="row">
        <div class="col-md-6">
          <table>
            <tr>
              <th colspan="2">
                <h3>Details</h3>
              </th>
            </tr>
            <tr>
              <td><b>State:</b></td>
              <td>{{$st_name}}</td>
            </tr>
            <tr>
              <td><b>AC Name:</b></td>
              <td>{{$ac_no}}-{{$ac_name}}</td>
            </tr>
          </table>

        </div>
        <div class="col-md-6  text-right totalPercentage p-4">
          <p>Estimated Turnout</p>
          <h1 class="display-1 m-0 p-0" style="line-height: 73px;">
            {{$totalturnout_per}}<small style="font-size: 67%;">%</small>
          </h1>
          @if($lists->updated_at != null)
          <small>Last Updated <span class="badge badge-success">{{date("d-m-Y H:i:s",strtotime($lists->updated_at))}}</span></small>
          @endif
        </div>
      </div>
      @if (session('success_mes'))
      <div class="alert alert-success"> {{session('success_mes') }}</div>
      @endif

      @if (session('error_mes'))
      <div class="alert alert-danger"> {{session('error_mes') }}</div>
      @endif

      @if($est_val==1)
      @if(isset($estimated_time) and $master->ceo_finalize==0)
      <?php
      $poll_date = $estimated_time->poll_date;
      $p1 = $poll_date . " " . $estimated_time->poll_st_time1;
      $p2 = $poll_date . " " . $estimated_time->poll_st_time2;
      $p3 = $poll_date . " " . $estimated_time->poll_st_time3;
      $p4 = $poll_date . " " . $estimated_time->poll_st_time4;
      $p5 = $poll_date . " " . $estimated_time->poll_st_time5;
      $p6 = $poll_date . " " . "23:59:59";

      $pt1 = $poll_date . " " . $estimated_time->poll_end_time1;
      $pt2 = $poll_date . " " . $estimated_time->poll_end_time2;
      $pt3 = $poll_date . " " . $estimated_time->poll_end_time3;
      $pt4 = $poll_date . " " . $estimated_time->poll_end_time4;
      $pt5 = $poll_date . " " . $estimated_time->poll_end_time5;
      $pt6 = $poll_date . " " . "17:40:00";


      $np = 0;
      $np1 = 0;
      $np2 = 0;
      $np3 = 0;
      $np4 = 0;
      $np5 = 0;
      $np6 = 0;
      $nid = base64_encode($lists->id);
      $r = "end";
      $round = base64_encode($r);
      ?>
      <div class="row">
        <div class="card col p-0 m-0">
          <table class="table-bordered card-body" cellpadding="0" cellspacing="0" style="width:100%;">
            <thead>
              <tr>
                <th>Time</th>
                <th>Estimated Poll Turnout %</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>09:00 AM @if( $current_date>=$pt1 and $current_date<=$p1) <p id="timmer-msg-1">
                    </p> @endif </td>
                <td>
                  @if( $current_date>=$pt1 and $current_date<=$p1 || $lists->modification_status_round1 == '1')
                    <?php $np = 1; ?>
                    <div class="">
                      <form class="form-horizontal" id="election_form" method="post" action="{{url('roac/turnout/estimated-entry')}}" autocomplete='off'>
                        {{csrf_field()}}
                        <input type="hidden" name="id" value="{{$lists->id}}">
                        <input type="hidden" name="roundno" value="1">
                        <input type="hidden" name="ceorequest" value="0">
                        @if($lists->modification_status_round1 == '1')
                        <input type="hidden" name="ecirequest" value="1">
                        @else
                        <input type="hidden" name="ecirequest" value="0">
                        @endif

                        <div class="form-row">
                          <div class="form-group col-md-4">
                            <label for="PercenTage" class="mt-2">Enter Total Percentage here</label>
                            <input type="text" name="est_turnout_round1" id="est_turnout_round1" class="PoLLInput form-control" placeholder="Estimated Poll Turnout % " value="@if($lists->est_turnout_round1>0){{$lists->est_turnout_round1}}@endif" maxlength="5" @if($exempted==1) disabled="disabled" @endif />
                          </div>
                          <div class="form-group col-md-5">
                            <label for="PercenTage" class="mt-2">Enter Confirmation Total Percentage here</label>
                            <input type="text" name="est_turnout_round1_confrim" id="est_turnout_round1_confrim" class="PoLLInput form-control" placeholder="Estimated Poll Turnout % " value="@if($lists->est_turnout_round1>0){{$lists->est_turnout_round1}}@endif" maxlength="5" @if($exempted==1) disabled="disabled" @endif />
                          </div>
                          <div class="col-md-3">
                            @if($exempted==0)
                            @php
                            if($lists->modification_status_round1 == '1'){
                            $fontsize = '15px;';
                            $buttontext = 'Update & Publish';
                            }else{
                            $fontsize = '';
                            $buttontext = 'Update';
                            }
                            @endphp
                            <button style="background: #d34c89; color: #fff;font-size:<?= $fontsize ?>" type="button" id="saverec" name="saverec" value="1" class="btn buttonActive">{{$buttontext}}</button>
                            @endif
                          </div>
                        </div>

                        <div class="">
                          <span id="errmsg" class="text-danger"></span>
                          @if ($errors->has('est_turnout_round1'))
                          <span style="color:red;">{{ $errors->first('est_turnout_round1') }}</span>
                          @endif
                        </div>
                        <input type="hidden" name="field_name" value="est_turnout_round1">
                      </form>
                    </div>
                    @endif
                    @if($np==0)
                    @if($lists->est_turnout_round1>0)
                    @if($current_date>$p1)
                    <div class="Pollcompleted">
                      <p class="PollText display-2">{{$lists->est_turnout_round1}} %</p>
                      <small class="text-white text-center">Last Updated on {{date("M d, Y H:i:s",strtotime($lists->update_at_round1))}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Updated Device:- {{$lists->update_device_round1}}</small>
                    </div>
                    @endif
                    @elseif($current_date>=$p1)
                    @if($lists->missed_status_round1 == '1')
                    <form class="form-horizontal" id="election_form" method="post" action="{{url('roac/turnout/estimated-entry')}}" autocomplete='off'> {{csrf_field()}} <input type="hidden" name="id" value="{{$lists->id}}">
                      <input type="hidden" name="roundno" value="1">
                      <input type="hidden" name="ceorequest" value="1">
                      <div class="form-row">
                        <div class="form-group col-md-4">
                          <label for="PercenTage" class="mt-2">Enter Total Percentage here</label>
                          <input type="text" name="est_turnout_round1" id="est_turnout_round1" class="PoLLInput form-control" placeholder="Estimated Poll Turnout % " value="@if($lists->est_turnout_round1>0){{$lists->est_turnout_round1}}@endif" maxlength="5" @if($exempted==1) disabled="disabled" @endif />
                        </div>
                        <div class="form-group col-md-5">
                          <label for="PercenTage" class="mt-2">Enter Confirmation Total Percentage here</label>
                          <input type="text" name="est_turnout_round1_confrim" id="est_turnout_round1_confrim" class="PoLLInput form-control" placeholder="Estimated Poll Turnout % " value="@if($lists->est_turnout_round1>0){{$lists->est_turnout_round1}} @endif" maxlength="5" @if($exempted==1) disabled="disabled" @endif />
                        </div>
                        <div class="col-md-3">
                          @if($exempted==0)
                          <label>&nbsp;</label>
                          <button style="background: #d34c89; color: #fff;font-size:15px;" type="button" id="saverec" name="saverec" value="1" class="btn buttonActive">Update & Publish</button>
                          @endif
                        </div>
                      </div>

                      <div class="">
                        <span id="errmsg" class="text-danger"></span>
                        @if ($errors->has('est_turnout_round1'))
                        <span style="color:red;">{{ $errors->first('est_turnout_round1') }}</span>
                        @endif
                      </div>
                      <input type="hidden" name="field_name" value="est_turnout_round1">
                    </form>
                    @else
                    <div class="PollMissed">
                      <p class="PollText display-2">Missed</p>
                    </div>
                    @endif
                    @else
                    <div class="PollDeactive">
                      <p class="PollText display-2">Not Open</p>
                    </div>
                    @endif
                    @endif
                </td>
              </tr>

              <tr>
                <td>11:00 AM @if( $current_date>=$pt2 and $current_date<=$p2) <p id="timmer-msg-2">
                    </p> @endif</td>
                <td>
                  @if( $current_date>=$pt2 and $current_date<=$p2 || $lists->modification_status_round2 == '1')
                    <?php $np1 = 1; ?>
                    <div class="">
                      <form class="form-horizontal" id="election_form" method="post" action="{{url('roac/turnout/estimated-entry')}}" autocomplete='off'>
                        {{csrf_field()}}
                        <input type="hidden" name="id" value="{{$lists->id}}">
                        <input type="hidden" name="roundno" value="2">
                        <input type="hidden" name="ceorequest" value="0">
                        @if($lists->modification_status_round2 == '1')
                        <input type="hidden" name="ecirequest" value="1">
                        @else
                        <input type="hidden" name="ecirequest" value="0">
                        @endif
                        <div class="form-row">
                          <div class="form-group col-md-4">
                            <label for="PercenTage" class="mt-2">Enter Total Percentage here</label>
                            <input type="text" name="est_turnout_round2" id="est_turnout_round2" class="PoLLInput form-control" placeholder="Estimated Poll Turnout%" value="@if($lists->est_turnout_round2>0){{$lists->est_turnout_round2}}@endif" maxlength="5" @if($exempted==1) disabled="disabled" @endif />
                          </div>
                          <div class="form-group col-md-5">
                            <label for="PercenTage" class="mt-2">Enter Confirmation Total Percentage here</label>
                            <input type="text" name="est_turnout_round2_confrim" id="est_turnout_round2_confrim" class="PoLLInput form-control" placeholder="Estimated Poll Turnout%" value="@if($lists->est_turnout_round2>0){{$lists->est_turnout_round2}}@endif" maxlength="5" @if($exempted==1) disabled="disabled" @endif />
                          </div>
                          <div class="col-md-3">
                            @if($exempted==0)
                            @php
                            if($lists->modification_status_round2 == '1'){
                            $fontsize = '15px;';
                            $buttontext = 'Update & Publish';
                            }else{
                            $fontsize = '';
                            $buttontext = 'Update';
                            }
                            @endphp
                            <button style="background: #d34c89; color: #fff;font-size:<?= $fontsize ?>" type="button" id="saverec1" name="saverec" value="2" class="btn buttonActive">{{$buttontext}}</button>
                            @endif
                          </div>
                        </div>

                        <div class="">
                          <span id="errmsg1" class="text-danger"></span>
                          @if ($errors->has('est_turnout_round2'))
                          <span style="color:red;">{{ $errors->first('est_turnout_round2') }}</span>
                          @endif
                        </div>

                        <input type="hidden" name="field_name" value="est_turnout_round2">
                      </form>
                    </div>

                    @endif
                    @if($np1==0)
                    @if($lists->est_turnout_round2>0)
                    @if($current_date>$pt2)
                    <div class="Pollcompleted">
                      <p class="PollText display-2">{{$lists->est_turnout_round2}} %</p>
                      <small class="text-white text-center">Last Updated on {{date("M d, Y H:i:s",strtotime($lists->update_at_round2))}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Updated Device:- {{$lists->update_device_round2}}</small>
                    </div>
                    @endif
                    @elseif($current_date>=$p2)
                    @if($lists->missed_status_round2 == '1' || $lists->modification_status_round2 == '1')
                    <form class="form-horizontal" id="election_form" method="post" action="{{url('roac/turnout/estimated-entry')}}" autocomplete='off'> {{csrf_field()}} <input type="hidden" name="id" value="{{$lists->id}}">
                      <input type="hidden" name="roundno" value="2">
                      @if($lists->missed_status_round2 == '1')
                      <input type="hidden" name="ceorequest" value="1">
                      <input type="hidden" name="ecirequest" value="0">
                      @else
                      <input type="hidden" name="ceorequest" value="0">
                      <input type="hidden" name="ecirequest" value="1">
                      @endif
                      <div class="form-row">
                        <div class="form-group col-md-4">
                          <label for="PercenTage" class="mt-2">Enter Total Percentage here</label>
                          <input type="text" name="est_turnout_round2" id="est_turnout_round2" class="PoLLInput form-control" placeholder="Estimated Poll Turnout%" value="@if($lists->est_turnout_round2>0){{$lists->est_turnout_round2}}@endif" maxlength="5" @if($exempted==1) disabled="disabled" @endif />
                        </div>
                        <div class="form-group col-md-5">
                          <label for="PercenTage" class="mt-2">Enter Confirmation Total Percentage here</label>
                          <input type="text" name="est_turnout_round2_confrim" id="est_turnout_round2_confrim" class="PoLLInput form-control" placeholder="Estimated Poll Turnout%" value="@if($lists->est_turnout_round2>0){{$lists->est_turnout_round2}}@endif" maxlength="5" @if($exempted==1) disabled="disabled" @endif />
                        </div>
                        <div class="col-md-3">
                          @if($exempted==0)
                          <button style="background: #d34c89; color: #fff;font-size:15px;" type="button" id="saverec1" name="saverec" value="2" class="btn buttonActive">Update & Publish</button>
                          @endif
                        </div>
                      </div>
                      <div class="">
                        <span id="errmsg1" class="text-danger"></span>
                        @if ($errors->has('est_turnout_round2'))
                        <span style="color:red;">{{ $errors->first('est_turnout_round2') }}</span>
                        @endif
                      </div>
                      <input type="hidden" name="field_name" value="est_turnout_round2">
                    </form>
                    @else
                    <div class="PollMissed">
                      <p class="PollText display-2">Missed</p>
                    </div>
                    @endif
                    @else
                    <div class="PollDeactive">
                      <p class="PollText display-2">Not Open</p>
                    </div>
                    @endif
                    @endif
                </td>
              </tr>

              <tr>
                <td>01:00 PM @if( $current_date>=$pt3 and $current_date<=$p3) <p id="timmer-msg-3">
                    </p> @endif </td>
                <td>
                  @if( $current_date>=$pt3 and $current_date<=$p3 || $lists->modification_status_round3 == '1')
                    <?php $np2 = 1; ?>
                    <form class="form-horizontal" id="election_form" method="post" action="{{url('roac/turnout/estimated-entry')}}" enctype="multipart/form-data" autocomplete='off'> {{csrf_field()}} <input type="hidden" name="id" value="{{$lists->id}}">
                      <input type="hidden" name="roundno" value="3">
                      <input type="hidden" name="ceorequest" value="0">
                      @if($lists->modification_status_round3 == '1')
                      <input type="hidden" name="ecirequest" value="1">
                      @else
                      <input type="hidden" name="ecirequest" value="0">
                      @endif
                      <div class="">
                        <div class="form-row">
                          <div class="form-group col-md-4">
                            <label for="PercenTage" class="mt-2">Enter Total Percentage here</label>
                            <input type="text" name="est_turnout_round3" id="est_turnout_round3" class="PoLLInput form-control" placeholder="Estimated Poll Turnout%" value="@if($lists->est_turnout_round3>0){{$lists->est_turnout_round3}}@endif" maxlength="5" @if($exempted==1) disabled="disabled" @endif />
                          </div>
                          <div class="form-group col-md-5">
                            <label for="PercenTage" class="mt-2">Enter Confirmation Total Percentage here</label>
                            <input type="text" name="est_turnout_round3_confrim" id="est_turnout_round3_confrim" class="PoLLInput form-control" placeholder="Estimated Poll Turnout%" value="@if($lists->est_turnout_round3>0){{$lists->est_turnout_round3}}@endif" maxlength="5" @if($exempted==1) disabled="disabled" @endif />
                          </div>
                          <div class="col-md-3">
                            @if($exempted==0)
                            @php
                            if($lists->modification_status_round3 == '1'){
                            $fontsize = '15px;';
                            $buttontext = 'Update & Publish';
                            }else{
                            $fontsize = '';
                            $buttontext = 'Update';
                            }
                            @endphp
                            <button style="background: #d34c89; color: #fff;font-size:<?= $fontsize ?>" type="button" id="saverec2" name="saverec" value="3" class="btn buttonActive">{{$buttontext}}</button>
                            @endif
                          </div>
                        </div>
                        <div>
                          <span id="errmsg2" class="text-danger"></span>
                          @if ($errors->has('est_turnout_round3'))
                          <span style="color:red;">{{ $errors->first('est_turnout_round3') }}</span>
                          @endif
                        </div>
                      </div>
                      <input type="hidden" name="field_name" value="est_turnout_round3">
                    </form>
                    @endif

                    @if($np2==0)
                    @if($lists->est_turnout_round3>0)
                    @if($current_date>$pt3)
                    <div class="Pollcompleted">
                      <p class="PollText display-2">{{$lists->est_turnout_round3}} %</p>
                      <small class="text-white text-center">Last Updated on {{date("M d, Y H:i:s",strtotime($lists->update_at_round3))}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Updated Device:- {{$lists->update_device_round3}}</small>
                    </div>
                    @endif
                    @elseif($current_date>=$p3)
                    @if($lists->missed_status_round3 == '1')
                    <form class="form-horizontal" id="election_form" method="post" action="{{url('roac/turnout/estimated-entry')}}" enctype="multipart/form-data" autocomplete='off'> {{csrf_field()}} <input type="hidden" name="id" value="{{$lists->id}}">
                      <input type="hidden" name="roundno" value="3">
                      @if($lists->missed_status_round3 == '1')
                      <input type="hidden" name="ceorequest" value="1">
                      @else
                      <input type="hidden" name="ceorequest" value="0">
                      @endif
                      <div class="">
                        <div class="form-row">
                          <div class="form-group col-md-4">
                            <label for="PercenTage" class="mt-2">Enter Total Percentage here</label>
                            <input type="text" name="est_turnout_round3" id="est_turnout_round3" class="PoLLInput form-control" placeholder="Estimated Poll Turnout%" value="@if($lists->est_turnout_round3>0){{$lists->est_turnout_round3}}@endif" maxlength="5" @if($exempted==1) disabled="disabled" @endif />
                          </div>
                          <div class="form-group col-md-5">
                            <label for="PercenTage" class="mt-2">Enter Confirmation Total Percentage here</label>
                            <input type="text" name="est_turnout_round3_confrim" id="est_turnout_round3_confrim" class="PoLLInput form-control" placeholder="Estimated Poll Turnout%" value="@if($lists->est_turnout_round3>0){{$lists->est_turnout_round3}}@endif" maxlength="5" @if($exempted==1) disabled="disabled" @endif />
                          </div>
                          <div class="col-md-3">
                            @if($exempted==0)
                            <button style="background: #d34c89; color: #fff;font-size:15px;" type="button" id="saverec2" name="saverec" value="3" class="btn buttonActive">Update & Publish</button>
                            @endif
                          </div>
                        </div>
                        <div>
                          <span id="errmsg2" class="text-danger"></span>
                          @if ($errors->has('est_turnout_round3'))
                          <span style="color:red;">{{ $errors->first('est_turnout_round3') }}</span>
                          @endif
                        </div>
                      </div>
                      <input type="hidden" name="field_name" value="est_turnout_round3">
                    </form>
                    @else
                    <div class="PollMissed">
                      <p class="PollText display-2">Missed</p>
                    </div>
                    @endif
                    @else
                    <div class="PollDeactive">
                      <p class="PollText display-2">Not Open</p>
                    </div>
                    @endif
                    @endif
                </td>
              </tr>

              <tr>
                <td>03:00 PM @if( $current_date>=$pt4 and $current_date<=$p4) <p id="timmer-msg-4">
                    </p> @endif</td>
                <td>
                  @if( $current_date>=$pt4 and $current_date<=$p4 || $lists->modification_status_round4 == '1')
                    <?php $np3 = 1; ?>
                    <form class="form-horizontal" id="election_form" method="post" action="{{url('roac/turnout/estimated-entry')}}" enctype="multipart/form-data" autocomplete='off'> {{csrf_field()}} <input type="hidden" name="id" value="{{$lists->id}}">
                      <input type="hidden" name="roundno" value="4">
                      <input type="hidden" name="ceorequest" value="0">
                      @if($lists->modification_status_round4 == '1')
                      <input type="hidden" name="ecirequest" value="1">
                      @else
                      <input type="hidden" name="ecirequest" value="0">
                      @endif
                      <div class="">
                        <div class="form-row">
                          <div class="form-group col-md-4">
                            <label for="PercenTage" class="mt-2">Enter Total Percentage here</label>
                            <input type="text" name="est_turnout_round4" id="est_turnout_round4" class="PoLLInput form-control" placeholder="Estimated Poll Turnout%" value="@if($lists->est_turnout_round4>0){{$lists->est_turnout_round4}}@endif" maxlength="5" @if($exempted==1) disabled="disabled" @endif />
                          </div>
                          <div class="form-group col-md-5">
                            <label for="PercenTage" class="mt-2">Enter Confirmation Total Percentage here</label>
                            <input type="text" name="est_turnout_round4_confrim" id="est_turnout_round4_confrim" class="PoLLInput form-control" placeholder="Estimated Poll Turnout%" value="@if($lists->est_turnout_round4>0){{$lists->est_turnout_round4}}@endif" maxlength="5" @if($exempted==1) disabled="disabled" @endif />
                          </div>
                          <div class="col-md-3">
                            @if($exempted==0)
                            @php
                            if($lists->modification_status_round4 == '1'){
                            $fontsize = '15px;';
                            $buttontext = 'Update & Publish';
                            }else{
                            $fontsize = '';
                            $buttontext = 'Update';
                            }
                            @endphp
                            <button style="background: #d34c89; color: #fff;font-size:<?= $fontsize ?>" type="button" id="saverec3" name="saverec" value="4" class="btn buttonActive">{{$buttontext}}</button>
                            @endif
                          </div>
                        </div>
                        <div class="">
                          <span id="errmsg3" class="text-danger"></span>
                          @if ($errors->has('est_turnout_round4'))
                          <span style="color:red;">{{ $errors->first('est_turnout_round4') }}</span>
                          @endif
                        </div>
                      </div>
                      <input type="hidden" name="field_name" value="est_turnout_round4">
                    </form>
                    @endif
                    @if($np3==0)
                    @if($lists->est_turnout_round4>0)
                    @if($current_date>$pt4)
                    <div class="Pollcompleted">
                      <p class="PollText display-2">{{$lists->est_turnout_round4}} %</p>
                      <small class="text-white text-center">Last Updated on {{date("M d, Y H:i:s",strtotime($lists->update_at_round4))}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Updated Device:- {{$lists->update_device_round4}}</small>
                    </div>
                    @endif
                    @elseif($current_date>=$pt4)
                    @if($lists->missed_status_round4 == '1')
                    <form class="form-horizontal" id="election_form" method="post" action="{{url('roac/turnout/estimated-entry')}}" enctype="multipart/form-data" autocomplete='off'> {{csrf_field()}} <input type="hidden" name="id" value="{{$lists->id}}">
                      <input type="hidden" name="roundno" value="4">
                      @if($lists->missed_status_round4 == '1')
                      <input type="hidden" name="ceorequest" value="1">
                      @else
                      <input type="hidden" name="ceorequest" value="0">
                      @endif
                      <div class="">
                        <div class="form-row">
                          <div class="form-group col-md-4">
                            <label for="PercenTage" class="mt-2">Enter Total Percentage here</label>
                            <input type="text" name="est_turnout_round4" id="est_turnout_round4" class="PoLLInput form-control" placeholder="Estimated Poll Turnout%" value="@if($lists->est_turnout_round4>0){{$lists->est_turnout_round4}}@endif" maxlength="5" @if($exempted==1) disabled="disabled" @endif />
                          </div>
                          <div class="form-group col-md-5">
                            <label for="PercenTage" class="mt-2">Enter Confirmation Total Percentage here</label>
                            <input type="text" name="est_turnout_round4_confrim" id="est_turnout_round4_confrim" class="PoLLInput form-control" placeholder="Estimated Poll Turnout%" value="@if($lists->est_turnout_round4>0){{$lists->est_turnout_round4}}@endif" maxlength="5" @if($exempted==1) disabled="disabled" @endif />
                          </div>
                          <div class="col-md-3">
                            @if($exempted==0)
                            <button style="background: #d34c89; color: #fff;font-size:15px;" type="button" id="saverec3" name="saverec" value="4" class="btn buttonActive">Update & Publish</button>
                            @endif
                          </div>
                        </div>
                        <div class="">
                          <span id="errmsg3" class="text-danger"></span>
                          @if ($errors->has('est_turnout_round4'))
                          <span style="color:red;">{{ $errors->first('est_turnout_round4') }}</span>
                          @endif
                        </div>
                      </div>
                      <input type="hidden" name="field_name" value="est_turnout_round4">
                    </form>
                    @else
                    <div class="PollMissed">
                      <p class="PollText display-2">Missed</p>
                    </div>
                    @endif
                    @else
                    <div class="PollDeactive">
                      <p class="PollText display-2">Not Open</p>
                    </div>
                    @endif
                    @endif
                </td>
              </tr>
              <tr>
                <td>05:00 PM @if( $current_date>=$pt5 and $current_date<=$p5) <p id="timmer-msg-5">
                    </p> @endif</td>
                <td>
                  @if( ($current_date>=$pt5 and $current_date<=$p5) || $lists->modification_status_round5 == '1')
                    <?php $np4 = 1; ?>
                    <form class="form-horizontal" id="election_form" method="post" action="{{url('roac/turnout/estimated-entry')}}" enctype="multipart/form-data" autocomplete='off'> {{csrf_field()}} <input type="hidden" name="id" value="{{$lists->id}}">
                      <input type="hidden" name="roundno" value="5">
                      <input type="hidden" name="ceorequest" value="0">
                      @if($lists->modification_status_round5 == '1')
                      <input type="hidden" name="ecirequest" value="1">
                      @else
                      <input type="hidden" name="ecirequest" value="0">
                      @endif
                      <div class="">
                        <div class="form-row">
                          <div class="form-group col-md-4">
                            <label for="PercenTage" class="mt-2">Enter Total Percentage here</label>
                            <input type="text" name="est_turnout_round5" id="est_turnout_round5" class="PoLLInput form-control" placeholder="Estimated Poll Turnout%" value="@if($lists->est_turnout_round5>0){{$lists->est_turnout_round5}}@endif" maxlength="5" @if($exempted==1) disabled="disabled" @endif />
                          </div>
                          <div class="form-group col-md-5">
                            <label for="PercenTage" class="mt-2">Enter Confirmation Total Percentage here</label>
                            <input type="text" name="est_turnout_round5_confrim" id="est_turnout_round5_confrim" class="PoLLInput form-control" placeholder="Confirm Estimated Poll Turnout%" value="@if($lists->est_turnout_round5>0){{$lists->est_turnout_round5}}@endif" maxlength="5" @if($exempted==1) disabled="disabled" @endif />
                          </div>
                          <div class="col-md-3">
                            @if($exempted==0)
                            @php
                            if($lists->modification_status_round5 == '1'){
                            $fontsize = '15px;';
                            $buttontext = 'Update & Publish';
                            }else{
                            $fontsize = '';
                            $buttontext = 'Update';
                            }
                            @endphp
                            <button style="background: #d34c89; color: #fff;font-size:<?= $fontsize ?>" type="button" id="saverec4" name="saverec" value="5" class="btn buttonActive">{{$buttontext}}</button>
                            @endif
                          </div>
                        </div>

                        <div class="">
                          <span id="errmsg4" class="text-danger"></span>
                          @if ($errors->has('est_turnout_round5'))
                          <span style="color:red;">{{ $errors->first('est_turnout_round5') }}</span>
                          @endif
                        </div>
                      </div>
                      <input type="hidden" name="field_name" value="est_turnout_round3">
                    </form>
                    @endif
                    @if($np4==0)
                    @if($lists->est_turnout_round5>0)
                    @if($current_date>$pt5)
                    <div class="Pollcompleted">
                      <p class="PollText display-2">{{$lists->est_turnout_round5}} %</p>
                      <small class="text-white text-center">Last Updated on {{date("M d, Y H:i:s",strtotime($lists->update_at_round5))}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Updated Device:- {{$lists->update_device_round5}}</small>
                    </div>
                    @endif
                    @elseif($current_date>=$p5)
                    @if($lists->missed_status_round5 == '1')
                    <form class="form-horizontal" id="election_form" method="post" action="{{url('roac/turnout/estimated-entry')}}" enctype="multipart/form-data" autocomplete='off'> {{csrf_field()}} <input type="hidden" name="id" value="{{$lists->id}}">
                      <input type="hidden" name="roundno" value="5">
                      @if($lists->missed_status_round5 == '1')
                      <input type="hidden" name="ceorequest" value="1">
                      @else
                      <input type="hidden" name="ceorequest" value="0">
                      @endif
                      <div class="">
                        <div class="form-row">
                          <div class="form-group col-md-4">
                            <label for="PercenTage" class="mt-2">Enter Total Percentage here</label>
                            <input type="text" name="est_turnout_round5" id="est_turnout_round5" class="PoLLInput form-control" placeholder="Estimated Poll Turnout%" value="@if($lists->est_turnout_round5>0){{$lists->est_turnout_round5}}@endif" maxlength="5" @if($exempted==1) disabled="disabled" @endif />
                          </div>
                          <div class="form-group col-md-5">
                            <label for="PercenTage" class="mt-2">Enter Confirmation Total Percentage here</label>
                            <input type="text" name="est_turnout_round5_confrim" id="est_turnout_round5_confrim" class="PoLLInput form-control" placeholder="Confirm Estimated Poll Turnout%" value="@if($lists->est_turnout_round5>0){{$lists->est_turnout_round5}}@endif" maxlength="5" @if($exempted==1) disabled="disabled" @endif />
                          </div>
                          <div class="col-md-3">
                            @if($exempted==0)
                            <button style="background: #d34c89; color: #fff;font-size:15px;" type="button" id="saverec4" name="saverec" value="5" class="btn buttonActive">Update & Publish</button>
                            @endif
                          </div>
                        </div>
                        <div class="">
                          <span id="errmsg4" class="text-danger"></span>
                          @if ($errors->has('est_turnout_round5'))
                          <span style="color:red;">{{ $errors->first('est_turnout_round5') }}</span>
                          @endif
                        </div>
                      </div>
                      <input type="hidden" name="field_name" value="est_turnout_round3">
                    </form>
                    @else
                    <div class="PollMissed">
                      <p class="PollText display-2">Missed</p>
                    </div>
                    @endif
                    @else
                    <div class="PollDeactive">
                      <p class="PollText display-2">Not Open</p>
                    </div>
                    @endif
                    @endif
                </td>
              </tr>
              <tr>
                <td>Close Of Poll @if( $current_date>=$pt6 and $current_date<=$p6 && $lists->est_poll_close==0 && $lists->est_turnout_round5!=0) <p id="timmer-msg-6">
                    </p> @endif</td>
                <td>
                  @if( (($current_date>=$pt6 and $current_date<=$p6) && $lists->est_poll_close==0 && $lists->est_turnout_round5!=0) || $lists->modification_status_round6 == '1')
                    <?php $np5 = 1; ?>
                    <form class="form-horizontal" id="election_form" method="post" action="{{url('roac/turnout/estimated-entry')}}" enctype="multipart/form-data" autocomplete='off'> {{csrf_field()}} <input type="hidden" name="id" value="{{$lists->id}}">
                      <input type="hidden" name="roundno" value="6">
                      <input type="hidden" name="ceorequest" value="0">
                      @if($lists->modification_status_round6 == '1')
                      <input type="hidden" name="ecirequest" value="1">
                      @else
                      <input type="hidden" name="ecirequest" value="0">
                      @endif
                      <div class="">
                        <div class="form-row">
                          <div class="form-group col-md-4">
                            <label for="PercenTage" class="mt-2">Enter Total Percentage here</label>
                            <input type="text" name="est_turnout_end" id="est_turnout_end" class="PoLLInput form-control" placeholder="Estimated Poll Turnout %" value="@if($lists->close_of_poll>0){{$lists->close_of_poll}}@endif" maxlength="5" @if($exempted==1) disabled="disabled" @endif />
                          </div>
                          <div class="form-group col-md-5">
                            <label for="PercenTage" class="mt-2">Enter Confirmation Total Percentage here</label>
                            <input type="text" name="est_turnout_end_confrim" id="est_turnout_end_confrim" class="PoLLInput form-control" placeholder="Estimated Poll Turnout %" value="@if($lists->close_of_poll>0){{$lists->close_of_poll}}@endif" maxlength="5" @if($exempted==1) disabled="disabled" @endif />
                          </div>
                          <div class="col-md-3">
                            @if($exempted==0)
                            @php
                            $date_now = new DateTime();
                            $allowCloseOfPollEnteryUpdateOnly = new DateTime($poll_date." 19:00:00");
                            if($lists->modification_status_round6 == '1'){
                            $fontsize = '15px;';
                            $buttontext = 'Update & Publish';
                            }else if($date_now > $allowCloseOfPollEnteryUpdateOnly){
                            $fontsize = '15px;';
                            $buttontext = 'Update & Publish';
                            }else{
                            $fontsize = '';
                            $buttontext = 'Update';
                            }
                            @endphp
                            <button style="background: #d34c89; color: #fff;font-size:<?= $fontsize ?>" type="button" id="saverec5" name="saverec" value="6" class="btn buttonActive">{{$buttontext}}</button>
                            @endif
                          </div>
                        </div>
                        <div class="">
                          <span id="errmsg5" class="text-danger"></span>
                          @if ($errors->has('est_turnout_end'))
                          <span style="color:red;">{{ $errors->first('est_turnout_end') }}</span>
                          @endif
                        </div>
                      </div>
                      <input type="hidden" name="field_name" value="est_turnout_end">
                    </form>
                    @endif
                    @if($np5==0)
                    @if($lists->close_of_poll>0)
                    @if($current_date>$pt6)
                    <div class="Pollcompleted">
                      <p class="PollText display-2">{{$lists->close_of_poll}} %</p>
                      <small class="text-white text-center">Last Updated on {{date("M d, Y H:i:s",strtotime($lists->update_at_final))}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Updated Device:- {{$lists->update_device_final}}</small>
                    </div>
                    @endif
                    @elseif($current_date>=$p6)
                    @if($lists->missed_status_round6 == '1')
                    <form class="form-horizontal" id="election_form" method="post" action="{{url('roac/turnout/estimated-entry')}}" enctype="multipart/form-data" autocomplete='off'> {{csrf_field()}} <input type="hidden" name="id" value="{{$lists->id}}">
                      <input type="hidden" name="roundno" value="5">
                      @if($lists->missed_status_round6 == '1')
                      <input type="hidden" name="ceorequest" value="1">
                      @else
                      <input type="hidden" name="ceorequest" value="0">
                      @endif
                      <div class="">
                        <div class="form-row">
                          <div class="form-group col-md-4">
                            <label for="PercenTage" class="mt-2">Enter Total Percentage here</label>
                            <input type="text" name="est_turnout_end" id="est_turnout_end" class="PoLLInput form-control" placeholder="Estimated Poll Turnout%" value="@if($lists->close_of_poll>0) {{$lists->close_of_poll}} @endif" maxlength="5" @if($exempted==1) disabled="disabled" @endif />
                          </div>
                          <div class="form-group col-md-5">
                            <label for="PercenTage" class="mt-2">Enter Confirmation Total Percentage here</label>
                            <input type="text" name="est_turnout_end_confrim" id="est_turnout_end_confrim" class="PoLLInput form-control" placeholder="Confirm Estimated Poll Turnout%" value="@if($lists->close_of_poll>0) {{$lists->close_of_poll}} @endif" maxlength="5" @if($exempted==1) disabled="disabled" @endif />
                          </div>
                          <div class="col-md-3">
                            @if($exempted==0)
                            <button style="background: #d34c89; color: #fff;font-size:15px;" type="button" id="saverec5" name="saverec" value="6" class="btn buttonActive">Update & Publish</button>
                            @endif
                          </div>
                        </div>
                        <div class="">
                          <span id="errmsg4" class="text-danger"></span>
                          @if ($errors->has('est_turnout_end'))
                          <span style="color:red;">{{ $errors->first('est_turnout_end') }}</span>
                          @endif
                        </div>
                      </div>
                      <input type="hidden" name="field_name" value="est_turnout_round3">
                    </form>
                    @else
                    <div class="PollMissed">
                      <p class="PollText display-2">Missed</p>
                    </div>
                    @endif
                    @else
                    <div class="PollDeactive">
                      <p class="PollText display-{{($current_date>=$pt6) ? '4' : '2'}}">Not Open @if($lists->est_turnout_round5==0 && $current_date>=$pt6)because 5 PM Slot is missed @endif</p>
                    </div>
                    @endif
                    @endif
                </td>

              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>
                  <div class="PollActive">
                    <!--<p class="PollText display-4"><a href="{{url('roac/turnout/schedule-entry/'.$round)}}"> Voting Turnout Entry Details</a></p> -->
                    <p class="PollText display-4"><a href="{{url('roac/turnout/RoPsWiseDetails?state='.$st_code.'&ac_id='.$ac_no)}}"> Voting Turnout Entry Details</a></p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      @elseif($master->ceo_finalize==1)
      <div class="table-responsive">
        <table id="data_table_table" class="table table-striped table-bordered" style="width:100%">
          <thead>
            <tr>
              <th colspan="1">Round1 %<br>(Poll Start to 9:00 AM)</th>
              <th colspan="1">Round2 %<br>(Poll Start to 11:00 AM)</th>
              <th colspan="1">Round3 %<br>(Poll Start to 1:00 PM)</th>
              <th colspan="1">Round4 %<br>(Poll Start to 3:00 PM)</th>
              <th colspan="1">Round5 %<br>(Poll Start to 5:00 PM)</th>
              <th colspan="1">Close Of Poll %</th>
              <th colspan="1">Latest Updated Poll %</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td> {{$lists->est_turnout_round1}} %</td>
              <td> {{$lists->est_turnout_round2 }} %</td>
              <td> {{$lists->est_turnout_round3 }} %</td>
              <td> {{$lists->est_turnout_round4}} %</td>
              <td> {{$lists->est_turnout_round5}} %</td>
              <td> {{$lists->close_of_poll }} %</td>
              <td> {{$lists->est_turnout_total }} % </td>
            </tr>
          </tbody>
        </table>

      </div><!-- End Of  table responsive -->
      @endif
      @elseif(isset($lists))
      @if($lists->est_turnout_round1!=0.0)
      <div class="table-responsive">
        <table id="data_table_table" class="table table-striped table-bordered" style="width:100%">
          <thead>
            <tr>
              <th colspan="1">Round1 %<br>(Poll Start to 9:00 AM)</th>
              <th colspan="1">Round2 %<br>(Poll Start to 11:00 AM)</th>
              <th colspan="1">Round3 %<br>(Poll Start to 1:00 PM)</th>
              <th colspan="1">Round4 %<br>(Poll Start to 3:00 PM)</th>
              <th colspan="1">Round5 %<br>(Poll Start to 5:00 PM)</th>
              <th colspan="1">Close Of Poll %</th>
              <th colspan="1">Latest Updated Poll %</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td> {{$lists->est_turnout_round1}} %</td>
              <td> {{$lists->est_turnout_round2 }} %</td>
              <td> {{$lists->est_turnout_round3 }} %</td>
              <td> {{$lists->est_turnout_round4}} %</td>
              <td> {{$lists->est_turnout_round5}} %</td>
              <td> {{$lists->close_of_poll }} %</td>
              <td> {{$lists->est_turnout_total }} % </td>
            </tr>
          </tbody>
        </table>

      </div><!-- End Of  table responsive -->
      @else
      <p>You are not entitled for this election phase!</p>
      @endif
      @endif
    </div>
  </section>
  <div class="modal modal-big fade" id="changestatus" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header mb-3">
          <h4 class="modal-title" id="exampleModalLabel">Voter turnout update confirmation?</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <div style="font-size:22px;">Are you sure you want to update estimated voter turnout for <b id="rndtime"></b> report as </div>
            <h1 class="display-1 m-0 p-0 text-center" id="tper" style="line-height: 73px;color:blue;"></h1>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="button" id="submit_final_form" class="btn btn-success submit-button">Update</button>
          </div>

        </div>
      </div>
    </div>
  </div>
</main>

@endsection
@section('script')
<script type="text/javascript">
  $(document).ready(function() {
    $("#est_turnout_round1").keypress(function(e) {
      //if the letter is not digit then display error and don't type anything
      $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
      if ((e.which != 46 || $(this).val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg").html("Digits Only").show().fadeOut("slow");
        return false;
      }
    });
    $("#est_turnout_round2").keypress(function(e) {
      //if the letter is not digit then display error and don't type anything
      $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
      if ((e.which != 46 || $(this).val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg1").html("Digits Only").show().fadeOut("slow");
        return false;
      }
    });

    $("#est_turnout_round3").keypress(function(e) {
      //if the letter is not digit then display error and don't type anything
      $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
      if ((e.which != 46 || $(this).val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg2").html("Digits Only").show().fadeOut("slow");
        return false;
      }
    });
    $("#est_turnout_round4").keypress(function(e) {
      //if the letter is not digit then display error and don't type anything
      //if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
      $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
      if ((e.which != 46 || $(this).val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg3").html("Digits Only").show().fadeOut("slow");
        return false;
      }
    });
    $("#est_turnout_round5").keypress(function(e) {
      //if the letter is not digit then display error and don't type anything
      $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
      if ((e.which != 46 || $(this).val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg4").html("Digits Only").show().fadeOut("slow");
        return false;
      }
    });
    $("#est_turnout_end").keypress(function(e) {
      //if the letter is not digit then display error and don't type anything
      $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
      if ((e.which != 46 || $(this).val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg5").html("Digits Only").show().fadeOut("slow");
        return false;
      }
    });



    $('#saverec').click(function() {
      $('#errmsg').show();
      var est = $('input[name="est_turnout_round1"]').val()
      if (est.trim() == '') {
        $('#errmsg').html('');
        $('#errmsg').html('Please enter voters turnout');
        $("input[name='est_turnout_round1']").focus();
        return false;
      }

      /*************************  Modal conformation ********************************/
      var confest = $('input[name="est_turnout_round1_confrim"]').val();
      if (confest.trim() == '') {
        $('#errmsg').html('');
        $('#errmsg').html('Please re-enter voters turnout');
        $("input[name='est_turnout_round1_confrim']").focus();
        return false;
      }

      if (est != confest) {
        $('#errmsg').html('');
        $('#errmsg').html('Estimated Percentage entered does not match with the confirmation percentage entered');
        $("input[name='est_turnout_round1_confrim']").focus();
        return false;
      }
      est = parseInt(est);
      if (est > '99.99') {
        $('#errmsg').html('');
        $('#errmsg').html('Please enter valid value (99.99)');
        return false;
      }

      $("#rndtime").html('9 AM')
      $("#tper").html(confest + '<small style="font-size: 67%;">%</small>');
      $('#changestatus').modal('show');
      /*************************  Modal confirmation ********************************/

    }) // 

    $('#saverec1').click(function() {
      $('#errmsg').show();
      $('#errmsg').show();
      var est = $('input[name="est_turnout_round2"]').val();
      if (est.trim() == '') {
        $('#errmsg1').html('');
        $('#errmsg1').html('Please enter voters turnout');
        $("input[name='est_turnout_round2']").focus();
        return false;
      }
      /*************************  Modal confirmation ********************************/
      var confest = $('input[name="est_turnout_round2_confrim"]').val();
      if (confest.trim() == '') {
        $('#errmsg1').html('');
        $('#errmsg1').html('Please re-enter voters turnout');
        $("input[name='est_turnout_round2_confrim']").focus();
        return false;
      }

      if (est != confest) {
        $('#errmsg1').html('');
        $('#errmsg1').html('Estimated Percentage entered does not match with the confirmation percentage entered');
        $("input[name='est_turnout_round2_confrim']").focus();
        return false;
      }

      est = parseInt(est);
      if (est > '99.99') {
        $('#errmsg1').html('');
        $('#errmsg1').html('Please enter valid value (99.99)');
        return false;
      }

      $("#rndtime").html('11 AM')
      $("#tper").html(confest + '<small style="font-size: 67%;">%</small>');
      $('#changestatus').modal('show');
      /*************************  Modal confirmation ********************************/
    }) // 
    $('#saverec2').click(function() {
      $('#errmsg2').show();
      var est = $('input[name="est_turnout_round3"]').val();
      if (est.trim() == '') {
        $('#errmsg2').html('');
        $('#errmsg2').html('Please enter voters turnout');
        $("input[name='est_turnout_round3']").focus();
        return false;
      }
      /*************************  Modal confirmation ********************************/
      var confest = $('input[name="est_turnout_round3_confrim"]').val();
      if (confest.trim() == '') {
        $('#errmsg2').html('');
        $('#errmsg2').html('Please re-enter voters turnout');
        $("input[name='est_turnout_round3_confrim']").focus();
        return false;
      }

      if (est != confest) {
        $('#errmsg2').html('');
        $('#errmsg2').html('Estimated Percentage entered does not match with the confirmation percentage entered');
        $("input[name='est_turnout_round3_confrim']").focus();
        return false;
      }
      est = parseInt(est);
      if (est > '99.99') {
        $('#errmsg2').html('');
        $('#errmsg2').html('Please enter valid value (99.99)');
        return false;
      }
      $("#rndtime").html('1 PM')
      $("#tper").html(confest + '<small style="font-size: 67%;">%</small>');
      $('#changestatus').modal('show');
      /*************************  Modal confirmation ********************************/
    }) // 
    $('#saverec3').click(function() {
      $('#errmsg3').show();
      var est = $('input[name="est_turnout_round4"]').val();
      if (est.trim() == '') {
        $('#errmsg3').html('');
        $('#errmsg3').html('Please enter voters turnout');
        $("input[name='est_turnout_round4']").focus();
        return false;
      }

      /*************************  Modal confirmation ********************************/
      var confest = $('input[name="est_turnout_round4_confrim"]').val();
      if (confest.trim() == '') {
        $('#errmsg3').html('');
        $('#errmsg3').html('Please re-enter voters turnout');
        $("input[name='est_turnout_round4_confrim']").focus();
        return false;
      }

      if (est != confest) {
        $('#errmsg3').html('');
        $('#errmsg3').html('Estimated Percentage entered does not match with the confirmation percentage entered');
        $("input[name='est_turnout_round4_confrim']").focus();
        return false;
      }
      est = parseInt(est);
      if (est > '99.99') {
        $('#errmsg3').html('');
        $('#errmsg3').html('Please enter valid value (99.99)');
        return false;
      }

      $("#rndtime").html('3 PM')
      $("#tper").html(confest + '<small style="font-size: 67%;">%</small>');
      $('#changestatus').modal('show');
      /*************************  Modal confirmation ********************************/


    }) // 
    $('#saverec4').click(function() {
      $('#errmsg4').show();
      var est = $('input[name="est_turnout_round5"]').val();
      error = false;
      if (est.trim() == '') {
        $('#errmsg4').html('');
        $('#errmsg4').html('Please enter voters turnout');
        $("input[name='est_turnout_round5']").focus();
        return false;
      }

      /*************************  Modal confirmation ********************************/
      var confest = $('input[name="est_turnout_round5_confrim"]').val();
      if (confest.trim() == '') {
        $('#errmsg4').html('');
        $('#errmsg4').html('Please re-enter voters turnout');
        $("input[name='est_turnout_round5_confrim']").focus();
        return false;
      }

      if (est != confest) {
        $('#errmsg4').html('');
        $('#errmsg4').html('Estimated Percentage entered does not match with the confirmation percentage entered');
        $("input[name='est_turnout_round5_confrim']").focus();
        return false;
      }
      est = parseInt(est);
      if (est > '99.99') {
        $('#errmsg4').html('');
        $('#errmsg4').html('Please enter valid value (99.99)');
        return false;
      }
      $("#rndtime").html('5 PM')
      $("#tper").html(confest + '<small style="font-size: 67%;">%</small>');
      $('#changestatus').modal('show');
      /*************************  Modal confirmation ********************************/

    }) // 
    $('#saverec5').click(function() {
      $('#errmsg5').show();
      var est = $('input[name="est_turnout_end"]').val();
      error = false;
      if (est.trim() == '') {
        $('#errmsg5').html('');
        $('#errmsg5').html('Please enter voters turnout');
        $("input[name='est_turnout_end']").focus();
        return false;
      }

      /*************************  Modal confirmation ********************************/
      var confest = $('input[name="est_turnout_end_confrim"]').val();
      if (confest.trim() == '') {
        $('#errmsg5').html('');
        $('#errmsg5').html('Please re-enter voters turnout');
        $("input[name='est_turnout_end_confrim']").focus();
        return false;
      }

      if (est != confest) {
        $('#errmsg5').html('');
        $('#errmsg5').html('Estimated Percentage entered does not match with the confirmation percentage entered');
        $("input[name='est_turnout_end_confrim']").focus();
        return false;
      }
      est = parseInt(est);
      if (est > '99.99') {
        $('#errmsg5').html('');
        $('#errmsg5').html('Please enter valid value (99.99)');
        return false;
      }

      $("#rndtime").html('Close of poll')
      $("#tper").html(confest + '<small style="font-size: 67%;">%</small>');
      $('#changestatus').modal('show');
      /*************************  Modal confirmation ********************************/



    }) // 

  }) // end function        
</script>
<script>
  var timestamp = "{{date('Y-m-d H:i:s',strtotime($timestamp))}}";
  var po = "{{date('Y-m-d H:i:s',strtotime($p1))}}";
  var countDownDate = new Date(po).getTime();
  var current1 = new Date(timestamp).getTime();
  var x1 = setInterval(function() {
    // Find the distance between now and the count down date
    var distance = countDownDate - current1;
    current1 = current1 + 1000;

    // Time calculations for days, hours, minutes and seconds
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

    // Output the result in an element with id="timmer-msg-1"
    var timmerMsgElement = document.getElementById("timmer-msg-1");
    if (timmerMsgElement) {
      timmerMsgElement.innerHTML = hours + "- " + minutes + "-  " + seconds;
      // If the count down is over, write some text 
      if (distance < 0) {
        clearInterval(x1);
        timmerMsgElement.innerHTML = "EXPIRED";
        location.reload();
        $("#est_turnout_round1").prop("disabled", true);
        $("#est_turnout_round1_confrim").prop("disabled", true);
      }
    }

  }, 1000);
</script>
<script>
  var po2 = "{{date('Y-m-d H:i:s',strtotime($p2))}}";
  var countDownDate2 = new Date(po2).getTime();
  var current2 = new Date(timestamp).getTime();
  var x2 = setInterval(function() {
    // Find the distance between now and the count down date
    var distance2 = countDownDate2 - current2;
    current2 = current2 + 1000;

    // Time calculations for days, hours, minutes and seconds
    var days2 = Math.floor(distance2 / (1000 * 60 * 60 * 24));
    var hours2 = Math.floor((distance2 % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes2 = Math.floor((distance2 % (1000 * 60 * 60)) / (1000 * 60));
    var seconds2 = Math.floor((distance2 % (1000 * 60)) / 1000);

    // Output the result in an element with id="timmer-msg-2"
    var timmerMsgElement = document.getElementById("timmer-msg-2");
    if (timmerMsgElement) {
      timmerMsgElement.innerHTML = hours2 + "- " + minutes2 + "-  " + seconds2;
      // If the count down is over, write some text 
      if (distance2 < 0) {
        clearInterval(x2);
        timmerMsgElement.innerHTML = "EXPIRED";
        location.reload();
        $("#est_turnout_round2").prop("disabled", true);
        $("#est_turnout_round2_confrim").prop("disabled", true);
      }
    }

  }, 1000);
</script>
<script>
  var po3 = "{{date('Y-m-d H:i:s',strtotime($p3))}}";
  var countDownDate3 = new Date(po3).getTime();
  var current3 = new Date(timestamp).getTime();
  var x3 = setInterval(function() {
    // Find the distance between now and the count down date
    var distance3 = countDownDate3 - current3;
    current3 = current3 + 1000;

    // Time calculations for days, hours, minutes and seconds
    var days3 = Math.floor(distance3 / (1000 * 60 * 60 * 24));
    var hours3 = Math.floor((distance3 % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes3 = Math.floor((distance3 % (1000 * 60 * 60)) / (1000 * 60));
    var seconds3 = Math.floor((distance3 % (1000 * 60)) / 1000);

    // Output the result in an element with id="timmer-msg-3"
    var timmerMsgElement = document.getElementById("timmer-msg-3");
    if (timmerMsgElement) {
      timmerMsgElement.innerHTML = hours3 + "- " + minutes3 + "-  " + seconds3;
      // If the count down is over, write some text 
      if (distance3 < 0) {
        clearInterval(x3);
        timmerMsgElement.innerHTML = "EXPIRED";
        location.reload();
        $("#est_turnout_round3").prop("disabled", true);
        $("#est_turnout_round3_confrim").prop("disabled", true);
      }
    }
  }, 1000);
</script>
<script>
  var po4 = "{{date('Y-m-d H:i:s',strtotime($p4))}}";
  var countDownDate4 = new Date(po4).getTime();
  var current4 = new Date(timestamp).getTime();
  var x4 = setInterval(function() {
    // Find the distance between now and the count down date
    var distance4 = countDownDate4 - current4;
    current4 = current4 + 1000;

    // Time calculations for days, hours, minutes and seconds
    var days4 = Math.floor(distance4 / (1000 * 60 * 60 * 24));
    var hours4 = Math.floor((distance4 % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes4 = Math.floor((distance4 % (1000 * 60 * 60)) / (1000 * 60));
    var seconds4 = Math.floor((distance4 % (1000 * 60)) / 1000);

    // Output the result in an element with id="timmer-msg-4"
    var timmerMsgElement = document.getElementById("timmer-msg-4");
    if (timmerMsgElement) {
      timmerMsgElement.innerHTML = hours4 + "- " + minutes4 + "-  " + seconds4;
      // If the count down is over, write some text 
      if (distance4 < 0) {
        clearInterval(x4);
        timmerMsgElement.innerHTML = "EXPIRED";
        location.reload();
        $("#est_turnout_round4").prop("disabled", true);
        $("#est_turnout_round4_confrim").prop("disabled", true);
      }
    }
  }, 1000);
</script>
<script>
  var po5 = "{{date('Y-m-d H:i:s',strtotime($p5))}}";
  var countDownDate5 = new Date(po5).getTime();
  var current5 = new Date(timestamp).getTime();
  var x5 = setInterval(function() {
    // Find the distance between now and the count down date
    var distance5 = countDownDate5 - current5;
    current5 = current5 + 1000;

    // Time calculations for days, hours, minutes and seconds
    var days5 = Math.floor(distance5 / (1000 * 60 * 60 * 24));
    var hours5 = Math.floor((distance5 % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes5 = Math.floor((distance5 % (1000 * 60 * 60)) / (1000 * 60));
    var seconds5 = Math.floor((distance5 % (1000 * 60)) / 1000);

    // Output the result in an element with id="timmer-msg-5"
    var timmerMsgElement = document.getElementById("timmer-msg-5");
    if (timmerMsgElement) {
      timmerMsgElement.innerHTML = hours5 + "- " + minutes5 + "-  " + seconds5;
      // If the count down is over, write some text 
      if (distance5 < 0) {
        clearInterval(x5);
        timmerMsgElement.innerHTML = "EXPIRED";
        location.reload();
        $("#est_turnout_round5").prop("disabled", true);
        $("#est_turnout_round5_confrim").prop("disabled", true);
      }
    }
  }, 1000);
</script>
<script>
  var po6 = "{{date('Y-m-d H:i:s',strtotime($p6))}}";
  var countDownDate6 = new Date(po6).getTime();
  var current6 = new Date(timestamp).getTime();
  var x6 = setInterval(function() {
    // Find the distance between now and the count down date
    var distance6 = countDownDate6 - current6;
    current6 = current6 + 1000;

    // Time calculations for days, hours, minutes and seconds
    var days6 = Math.floor(distance6 / (1000 * 60 * 60 * 24));
    var hours6 = Math.floor((distance6 % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes6 = Math.floor((distance6 % (1000 * 60 * 60)) / (1000 * 60));
    var seconds6 = Math.floor((distance6 % (1000 * 60)) / 1000);

    // Output the result in an element with id="timmer-msg-6"
    var timmerMsgElement = document.getElementById("timmer-msg-6");
    if (timmerMsgElement) {
      timmerMsgElement.innerHTML = hours6 + "- " + minutes6 + "-  " + seconds6;
      if (hours6 == 5 && minutes6 == 0 && seconds6 == 0) {
        location.reload();
      }
      // If the count down is over, write some text 
      if (distance6 < 0) {
        clearInterval(x6);
        timmerMsgElement.innerHTML = "EXPIRED";
        location.reload();
        $("#est_turnout_end").prop("disabled", true);
        $("#est_turnout_end_confrim").prop("disabled", true);
      }
    }
  }, 1000);

  $("#submit_final_form").click(function() {
    $("#election_form").submit();
  });
</script>
<script>
  var currentTime = new Date(timestamp);
  var h = currentTime.getHours();
  var m = currentTime.getMinutes();
  var s = currentTime.getSeconds();
  var currentTimeInterval = setInterval(function() {
      s++;
      if (s == 60) {
        s = 0;
        m++;
      }
      if (m == 60) {
        m = 0;
        h++;
      }

      console.log(h, m, s);
      if ((h == 10 || h == 12 || h == 14 || h == 16 || h == 18) && m == 59 && s == 59) {
        clearInterval(currentTimeInterval);
        location.reload();
      }
    },
    1000);
</script>
@endsection