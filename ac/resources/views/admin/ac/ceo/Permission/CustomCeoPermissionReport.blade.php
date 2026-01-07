@extends('admin.layouts.ac.theme')
@section('title', 'List Candidate')
@section('content')

<main role="main" class="inner cover mb-3 mb-auto">
    <br/>
    <section id="details">

        <div class="container-fluid">
            <form name ="report" method="post"  action="{{url('/acceo/customtimewisereport')}}"> 
                {{csrf_field()}}
                <div class="row">

                    <div class="col-sm-3  row">
                        <label for="state" class="col-sm-4 col-form-label">Select District</label>
                        <div class="col-sm-8 distt">
                            <select name="dist" id="dist" class="form-control">
                                <option value="0">-- Select District --</option>
                                 <option value="all">Select All</option>
                                @foreach($distvalue as $dist)
                                <option value="{{$dist->DIST_NO }}" {{ (collect(old('dist'))->contains($dist->DIST_NO)) ? 'selected':'' }}> 
                                    {{$dist->DIST_NAME }}
                                </option>
                                @endforeach 
                            </select>
                            <span class="text-danger">{{ $errors->error->first('dist') }}</span>
                        </div>
                    </div>
                    <div class="col-sm-3  row">
                        <label for="ac" class="col-sm-4 col-form-label">Select AC</label>
                        <div class="col-sm-8 distt">
                            <select name="ac" id="ac" class="form-control">
                                <option value="0">-- Select AC --</option>
                                <option value="all">Select All</option>
                            </select>
                            <span class="text-danger">{{ $errors->error->first('ac') }}</span>
                        </div>
                    </div>

                    <div class="col-sm-3  row">
                        <div class="col-sm-12 distt">
                                 <input class="form-control" type="text" name="time" placeholder="Enter Custom Time" value="{{old('time')}}">
                                <span class="text-danger">{{ $errors->error->first('time') }}</span>
                        </div>
                    </div>


                    <div class="col-sm-1  row">
                        <input type="submit"  value="Export Excel" name="excel" class="btn btn-primary getdata">
                    </div>
                    <div class="col-sm-1  row">
                    </div>
                    <div class="col-sm-1  row">
                        <input type="submit"  value="Export PDF" name="pdf" class="btn btn-primary getdata">
                    </div>
                </div>
            </form>

        </div>
    </section>

</main>
@endsection
@section('script')
<script>
    $(document).ready(function() {
        var base_url = $("#base_url").val();
        var token = $('meta[name="csrf-token"]').attr('content');

        jQuery("select[name='dist']").change(function()
        {
            var dist = jQuery(this).val();
            jQuery.ajax({
                url: base_url + '/acceo/getAllAC',
                type: 'POST',
                data: {_token: token, dist: dist},
                success: function(data) {
                    //alert(data);
                    if (data != '') {
                        var distselect = jQuery('form select[name=ac]');
                        distselect.empty();
                        var statehtml = '';
                        statehtml = statehtml + '<option value="0"> -- Select AC --</option><option value="all">Select All</option> ';
                        jQuery.each(data, function(key, value) {
                            statehtml = statehtml + '<option value="' + value.AC_NO + '">' + value.AC_NAME + '</option>';
                            jQuery("select[name='ac']").html(statehtml);
                        });
                        var statehtml_end = '';
                        jQuery("select[name='ac']").append(statehtml_end);
                    } else {
                        //alert('test');
                        jQuery("select[name='ac']").html('<option value=""> -- Select AC --</option><option value="all">Select All</option>');
                    }

                }
            });
        });
    });
</script>
@endsection
