@extends(Baseview('app'))

@push('after_scripts')
    <script type="application/javascript">
        jstrans['homepage_annonce_number']="{{trans('JOBLANG::apply.homepage_annonce_number')}}";
        jstrans['homepage_annonce_foryear']="{{trans('JOBLANG::apply.homepage_annonce_foryear')}}";
        jstrans['public_qual']="{{trans('qualifications.public_qual')}}";
        function goback(){
            $('#result').modal('hide');
        }
    </script>
@loadScriptOnce("js/employment/frontpage.js")
@endpush
@section('content')
   @parent
   @isset($data['officialmessage_statue'])
   @if($data['officialmessage_statue'] == 0)
	<!-- home.blade.php -->
   <div class="row"   data-aos="zoom-in">
       <div class="col-sm-2"></div>
       <div class="col-sm-8 text-center bg-info">{!! $data['officialmessage'] !!}</div>
       <div class="col-sm-2"></div>
   </div>
   <!-- home.blade.php -->
   @endif
   @endisset
    @include('Employment::apply_form.home')
@endsection
