@extends(Baseview('app'))
@push('after_styles')
@push('scripts')
<script type="application/javascript">
  function goback(){
    window.history.back();
  }
  </script>
@endpush

@section('content')
   @parent
   <div class="card">
  <div class="card-header">
  {{$data['stage_name']}}
  </div>
  <div class="card-body">
    <p class="card-text">{!! $data['content'] !!}</p>
  </div>
</div>
@endsection
