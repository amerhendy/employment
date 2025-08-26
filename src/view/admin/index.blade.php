@extends(Baseview('app'))
@push('after_styles')
@loadStyleOnce('js/packages/DataTables/datatables.min.css')
@loadStyleOnce('js/packages/select2/dist/css/select2.min.css')
@loadStyleOnce('js/packages/select2-bootstrap-theme/dist/select2-bootstrap-5-theme.rtl.min.css')
@loadStyleOnce('js/packages/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css')
<style>
        .requireinput{
            border-color: #0B90C4;
        }
        .select2-results__group{
        background-color:gray;
        }
        .has-error{
            border-color: rgb(185, 74, 72) !important;
        }
        .noty_body{
            color:#fff !important;
            background-color: rgba(0,183,74,var(--mdb-bg-opacity)) !important;
            background-image: var(--mdb-gradient) !important;
            --mdb-bg-opacity: 1;
        }
        .btn-circle {
			border-radius: 60px;
			font-size: 15px;
			text-align: center;
		}
    </style>
@endpush
@push('after_scripts')
<script type="application/javascript">
    jstrans['FULLname']="{{trans('JOBLANG::Employment_people.FULLname')}}";
    jstrans['exper_0']="{{trans('EMPLANG::Mosama_Experiences.enum_0')}}";
    jstrans['exper_1']="{{trans('EMPLANG::Mosama_Experiences.enum_1')}}";
    jstrans['exper_2']="{{trans('EMPLANG::Mosama_Experiences.enum_2')}}";
    jstrans['exper_translate']="{{trans('EMPLANG::Mosama_Experiences.translate')}}";
    jstrans['stage']="{{trans('JOBLANG::Employment_Stages.singular')}}";
    jstrans['Actions']="{{ trans('AMER::actions.actions') }}";
    jstrans['infoabout']="{{trans('AMER::datatables.infoabout')}}";
    jstrans['export']="{{ trans('AMER::datatables.export.export') }}";
    jstrans['column_visibility']="{{ trans('AMER::datatables.export.column_visibility') }}";

    //jstrans['LogMessages']="{{trans('JOBLANG::Employment_Reports.UpToDateForm.LogMessages')}}";
    //jstrans['DownloadList']="{{trans('JOBLANG::Employment_Reports.UpToDateForm.DownloadList')}}";
    
    jstrans['errors']={{ Illuminate\Support\Js::from(trans('AMER::errors')) }};
    jstrans['Employment_Reports']={{ Illuminate\Support\Js::from(trans('JOBLANG::Employment_Reports')) }};
    jstrans['Employment_Grievance']={{ Illuminate\Support\Js::from(trans('JOBLANG::Employment_Grievance')) }};
  </script>
@loadScriptOnce('js/packages/select2/dist/js/select2.full.min.js')
@loadScriptOnce('js/packages/DataTables/datatables.js')

@loadScriptOnce('js/packages/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')
@loadScriptOnce('js/employment/adminUptodate.js')
@loadScriptOnce('js/employment/adminUptodateDataTables.js')
@loadScriptOnce('js/employment/adminUptodatePost.js')

  @loadScriptOnce('js/arabic.js')
@endpush
@section('content')
   @parent
   <div class="container-fluid" data-bs-theme="dark">
    @include("Employment::admin.navbar")
    @include("Employment::admin.searchForm")
    @include("Employment::admin.searchForm.table")
    @include("Employment::admin.searchForm.updateArea")
    @include("Employment::admin.searchForm.printForm")
    @include("Employment::admin.searchForm.downloadform")
    @include("Employment::admin.searchForm.SeatingForm")
  </div>
@endsection