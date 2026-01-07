@extends('admin.layouts.ac.theme')
@section('bradcome', 'Estimate Turnout Entry')
@section('content')
<main role="main" class="inner cover mt-4">
    <section>
        <div class="container-fluid">
            <div class="row">
                <div class="card text-left" style="width:100%; margin:0 auto;">
                    <div class=" card-header">
                        <div class=" row">
                            <div class="col-md-7 text-left">
                                <h4>Update Turnout From Boothapp</h4>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="{{ url('eci/turnout/update_turnout_data') }}" class="btn btn-lg btn-success">Synchronize Data</a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <table id="list-table" class="table table-striped table-bordered table-hover"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th colspan="3" class="text-center"></th>
                                    <th colspan="3" class="text-center">BoothApp</th>
                                    <th colspan="3" class="text-center">Final Turnout</th>
                                </tr>
                                <tr>
                                    <th>Poll Date</th>
                                    <th>STATE NAME</th>
                                    <th>AC Name</th>
									<th>Poll %</th>
                                    <th>Elector</th>
                                    <th>Voter</th>
                                    <th>Poll %</th>
                                    <th>Elector</th>
                                    <th>Voter</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($results_data)>0)
                                @foreach ($results_data as $item)
                                    <tr>
                                        <td>{{ $item['poll_date'] }}</td>
                                        <td>{{ $item['st_name'] }}</td>
                                        <td>{{ $item['ac_name'] }}</td>
                                        <td>{{ $item['poll_percent'] }}%</td>
                                        <td>{{ $item['total_elector'] }}</td>
                                        <td>{{ $item['total_voter'] }}</td>
                                        <td>{{ $item['suvidha_poll_percent'] }}%</td>
                                        <td>{{ $item['suvidha_total_elector'] }}</td>
                                        <td>{{ $item['suvidha_total_voter'] }}</td>
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
@endsection