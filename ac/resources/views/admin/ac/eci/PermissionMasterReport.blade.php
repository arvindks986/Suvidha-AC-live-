@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')
<?php
//echo "<pre>";
//print_r($report);
//exit;
?>
<main role="main" class="inner cover mb-3 mb-auto">
    <br/>
    <section id="details">
         <div class="row">
                <div class="col-sm-12 text-center mb-3">
                    <h5 style="text-decoration: underline">State wise Permission Master Report</h5>
                </div>
            </div>
        <div class="container-fluid">
            <form name = "report" method="post"  action="{{url('/eci/permissionmasterreport')}}"> 
                {{csrf_field()}}
                <div class="row">
                    
                    <div class="col-sm-3 row">
                        <label for="state" class="col-sm-4 col-form-label">Election Type</label>
                        <div class="col-sm-8 distt">
                            <select name="elect" id="state" class="form-control">
                                <option value="0">-- All Election --</option>                             
                                <option value="3">GEN Election</option>
                                <option value="4">BYE Election</option>
                            </select>
                        </div>
                    </div>
                    

                    <div class="col-sm-3  row">
                        <label for="state" class="col-sm-4 col-form-label">State</label>
                        <div class="col-sm-8 distt">
                            <select name="state" id="state" class="form-control">
                                <option value="0">-- All State --</option>
                                @foreach($statevalue as $State)
                                <option value="{{$State->ST_CODE }}"> 
                                    {{$State->ST_NAME }}
                                </option>
                                @endforeach 
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-5 row">
                        <label for="state" class="col-sm-4 col-form-label">Select Permission Name</label>
                        <div class="col-sm-8 distt">
                            <select name="pname" class="form-control" id="selectprmsn">
                                    <option value="0">Select Permission Type</option>
                                   @if(!empty($getAllPermissiontype))
                                    @foreach($getAllPermissiontype as $pdata)
                                    <option value="{{$pdata->id}}" {{ (collect(old('pname'))->contains($pdata->id)) ? 'selected':'' }}>{{$pdata->permission_name}}</option>
                                    @endforeach
                                    @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-1  row">
                        <input type="submit"  value="Submit" name="submit" class="btn btn-primary getdata">
                    </div>
                </div>
            </form>
             @if(!empty($report))
            <div class="row">
                 <div class="col-sm-12 text-right">
                <form name ="report" method="post"  action="{{url('/eci/permissionmasterreport')}}">
                {{csrf_field()}}
                <input type="hidden" name="pname" value="{{$pname}}" class="form-control" >
                <input type="hidden" name="state" class="form-control" value="{{$state}}">
                <input type="hidden" name="elect" class="form-control" value="{{$elect}}">
                    <div class="float-right mt-5">
                        <input type="submit"  value="Export Excel" name="excel" class="btn btn-primary getdata">
                        <input type="submit"  value="Export PDF" name="pdf" class="btn btn-primary getdata">
                    </div>
                </form>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 mt-2">
                    <table id="examplee" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>State Name</th>
                                <th>Permission name</th>
                                <th>Document details</th>
                                <th>Permission Level</th>
                                <th>Authority Type</th>
                                <th>Required Status</th>
                            </tr>
                        </thead>
                        <tbody>
                                <?php $state;$p_id=0;$i=1; ?>
                                @foreach($report as $key => $data)
                               <?php
                                $authname="";
                                $required_status="";
                                
//                                $file_name = $data->file_name;
                                if($data->auth_name != 'undefined' && $data->auth_name != 'null')
                                {
                                     $authname = $data->auth_name.' ';
                                }
                                else
                                {
                                      $authname = "";
                                }
                                
                                if($data->canddoc_name != 'undefined' && $data->canddoc_name != '')
                                {
                                    $authname .= $data->canddoc_name;
                                }
                               if($data->required_status == '1')
                               {
                               $required_status = 'Mandatory';
                               }
                               else
                               {
                                    $required_status = 'Not Mandatory';
                               }
                               $fileserver = $data->fileserver_dir;
                              if ($fileserver == 'uploads')
                                {
                                  $file_name = "uploads/permission-document/" . $data->st_code . "/" . $data->file_name;
                              }
                              else
                              {
                                  $file_name = "/".$data->file_name;
                              }
                               ?>
                                
                                <?php $state1=$data->st_code;$p_id1 = $data->permission_id;?>
                               
                                    <?php if(($state == $state1) && ($p_id == $p_id1)){?>
                                 <tr>
<!--                                    <td>{{''}}</td>-->
                                    <td>{{''}}</td>
                                    <td>{{''}}</td>
                                    <td>{{$data->doc_name}} <a href="{{asset($file_name)}}" download="">Download Format</a></td>
                                    <td>{{$data->role_name}}</td>
                                    <td>{{$authname}}</td>
                                    <td>{{$required_status}}</td>
                                </tr>
                                    <?php } else { ?>
                                 <tr>
<!--                                    <td>{{$i}}</td>-->
                                    <td>{{$data->st_name}}</td>
                                    <td>{{$data->pname}}</td>
                                    <td>{{$data->doc_name}} <a href="{{asset($file_name)}}" download="">Download Format</a></td>
                                    <td>{{$data->role_name}}</td>
                                    <td>{{$authname}}</td>
                                    <td>{{$required_status}}</td>
                                </tr>
                                
                                    <?php $i++; }?>
                                 
                                <?php $state=$data->st_code;$p_id = $data->permission_id;?>
                                
                                @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </section>

</main>
@endsection

