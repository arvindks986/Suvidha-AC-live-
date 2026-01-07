@extends('admin.central.common.theme')
@section('title', 'ECI ')
@section('content')
<main>
    @if($user_data->role_id=='43')
      @include('admin/central/common/aero/aero-dashboard')
    @endif
</main>
@include('admin/central/common/footer')
@endsection