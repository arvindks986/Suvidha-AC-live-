
<main role="main" class="inner cover mt-4">
    <section>
        <div class="container-fluid">
            <div class="row">
                <div class="card text-left" style="width:100%; margin:0 auto;">
                    <div class=" card-header">
                        <div class=" row">
                            <div class="col-md-7 text-left">
                                <h4>Update Turnout From Web</h4>
                            </div>
                            <!--<div class="col-md-4 text-right">
                                <a href="{{ url('publish-vtr?update=yes&scheduleid='.Request::get('scheduleid')) }}" class="btn btn-lg btn-success">Synchronize Data</a>
                            </div>-->
                        </div>
                    </div>

		@if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
        @endif
        
         @if (session('error_mes'))
          <div class="alert alert-danger"> {{session('error_mes') }}</div>
        @endif


                    <div class="card-body">
                        <table id="list-table" class="table table-striped table-bordered table-hover"
                            style="width:100%" border="1">
                            <thead>
                                <tr>
                                    <th colspan="3" class="text-center"></th>
                                    <th colspan="3" class="text-center">Web</th>
                                    <th colspan="3" class="text-center">App Turnout</th>
                                </tr>
                                <tr>
                                    <td>Phase No.</td>
                                    <td>STATE NAME</td>
                                    <td>AC Name</td>
									<td>Poll %</td>
                                    <td>Elector</td>
                                    <td>Voter</td>
                                    <td>Poll %</td>
                                    <td>Elector</td>
                                    <td>Voter</td>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($results_data)>0)
                                @foreach ($results_data as $item)
                                    <tr>
                                        <td class="text-center"> {{$item->scheduleid}} </td>
                                        <td class="text-center">{{ getstatebystatecode($item->st_code)->ST_NAME }}</td>
                                        <td class="text-center">{{ getacbyacno($item->st_code, $item->ac_no)->AC_NAME }}</td>
                                        <td class="text-center">{{ $item->est_turnout_total }}%</td>
                                        <td class="text-center">{{ $item->electors_total }}</td>
                                        <td class="text-center">{{ $item->est_voters }}</td>
                                        <td class="text-center">{{ $item->est_turnout_total_temp }}%</td>
                                        <td class="text-center">{{ $item->electors_total_temp }}</td>
                                        <td class="text-center">{{ $item->est_voters_temp }}</td>
                                    </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>