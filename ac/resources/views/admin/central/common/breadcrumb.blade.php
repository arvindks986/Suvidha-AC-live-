<section class="breadcrumb-section mybradcom">
  <div class="container-fluid">
    <div class="row">
      <div class="col">
        <div class="row">
          <div class="col">
            <ul id="breadcrumb" class="pt-2 mr-auto">
              <li><a href="{{url('garudapp/dashboard')}}"><span class="icon icon-home"> </span></a></li>
              @if(isset($breadcrumbs))
              @foreach($breadcrumbs as $itr_bread)
              <li><a href="{{$itr_bread['href']}}"><span class="icon icon-beaker"> </span> {{$itr_bread['name']}}</a></li>
              @endforeach
              @endif

            </ul>
          </div>
          <!--<div class="col">
            <select name="election" class="form-control" onchange="switch_election(this.value)">
              <option value="">Select Election</option>
              @foreach(DB::connection("mysql_database_history")->table("m_election_history")->orderByRaw("id DESC")->get() as $itr_election)
              <option value="{{$itr_election->id}}">{{$itr_election->const_type}} - {{$itr_election->description}}</option>
              @endforeach
            </select>
          </div> -->
          <div class="col">
            <div class="nav-header welcome float-right">
              <ul class="float-right">

                <li>
                  LoginId:- {{Auth::user()->officername}}
                </li>
              </ul>
              <input type="hidden" value="{{$_SERVER["REMOTE_ADDR"]}}" readonly>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- print header start -->
<style>
  th {
    color: black !important;
  }
</style>
<!-- print header end -->
<script>
  function switch_election(id) {
    $.ajax({
      url: "{{ url('garudapp/validate-election') }}",
      type: 'GET',
      data: 'id=' + id,
      dataType: 'json',
      beforeSend: function() {},
      complete: function() {},
      success: function(json) {
        if (json['success'] == true) {
          window.location.href = "{{ url('adminhome') }}";
        } else {
          alert(json['errors']);
        }
      },
      error: function(data) {
        var errors = data.responseJSON;
      }
    });
  };
</script>