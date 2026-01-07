@extends('admin.central.common.theme')
@section('title', 'Descriptive Election Period Report')
  <?php 
  $breadcrumbs = [];
  $breadcrumbs[] = [
    'href' => '#',
    'name' => 'State Wise Account Count'
  ]; 
  ?>
  
@section('content')



<style>	

.custombold td{
    font-weight: bold;
}

.bolds{
	font-weight: bold;
	.SumoSelect {
    width: 450px !important;
}
}

.form-group label {
    float: left;
    text-align: left;
    font-weight: normal;
}

.form-group select {
    display: inline-block;
    width: 400px;;
    vertical-align: middle;
}
.align_filter{
    margin-right:2%;
}
.widthcustome th{
    width:10%;
    
}


</style>





<section class="">
  <div class="container-fluid">
    <div class="row">
      <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
        <div class=" card-header">
          <div class=" row">
            <div class="col"><h4> State Wise Account List</div>
            <div class="col">
            
            <p class="mb-0  ">
              <a  href="{{ URL::previous() }}" class="btn btn-primary float-right">Back</a>
            </p>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive" style="width: 100%;">
          <!-- Content goes Here -->

		<table class="table table-bordered table-striped" style="width: 100%;" id="example">
			<thead>
				<tr class="widthcustome">
					<th rowspan="2">Sr.No.</th>
          <th rowspan="2">State</th>
          <th rowspan="2">Total districts</th>
					<th colspan="2">Account details entered for Nomination</th>
					<th colspan="2">Account details entered for Duplicate EPIC</th>
				</tr>
        <tr>
          <th>Details Entered</th>
          <th>Finalized</th>	
          <th>Details Entered</th>
          <th>Finalized</th>	
        </tr>
			</thead>
			  <tbody>
              <?php
               //print_r($account_data_merge); die; 
                $dist_total = 0;
                $nom_count_totoal = 0;
                $epic_count_totoal = 0;
                $epic_count_fintotoal = 0;
                $nom_count_fintotoal = 0;
               ?> 		
              @if(count($account_data_merge) > 0)
              @php $i = 1; @endphp
              @foreach($account_data_merge as $key => $value)											
                <?php 
                
                $dist_total += $value['dist_count']; 
                $nom_count_totoal += $value['nom_count']; 
                $epic_count_totoal += $value['epic_count']; 
                $epic_count_fintotoal += $value['epic_count_fin']; 
                $nom_count_fintotoal += $value['nom_count_fin']; 
                
                ?>
                <tr>
                  <td>{{$i}}</td>
                  <td><a href="{{url('eci/view_account_info?st_codeee='.$value['st_code'])}}" target="_blank">{{$value['st_name']}}</a></td>
                  <td style="text-align: center;"><a href="{{url('eci/view_account_info?st_codeee='.$value['st_code'])}}" target="_blank">{{$value['dist_count']}}</a></td>
                  <td style="text-align: center;">{{$value['nom_count']}}</td>
                  <td style="text-align: center;">{{$value['nom_count_fin']}}</td>
                  <td style="text-align: center;">{{$value['epic_count']}}</td>
                  <td style="text-align: center;">{{$value['epic_count_fin']}}</td>
                 
                </tr>

                @php $i++; @endphp
                @endforeach

                <tr class="custombold">
                  <td colspan="2" align="center">Total</td>
                  <td style="text-align: center;">{{$dist_total}}</td>
                  <td style="text-align: center;">{{$nom_count_totoal}}</td>
				  <td style="text-align: center;">{{$nom_count_fintotoal}}</td>
                  <td style="text-align: center;">{{$epic_count_totoal}}</td>
                  <td style="text-align: center;">{{$epic_count_fintotoal}}</td>
                  
                 
                </tr> 

					@else 
						<tr><td colspan="4" align="center">No record found</td></tr> 
					@endif

                
                   
					
			  </tbody>
			</table>			
        </div>
      </div>
    </div>
  </div>
</div>
</section>


@endsection
@section('script')

@endsection
