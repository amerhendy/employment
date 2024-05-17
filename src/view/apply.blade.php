@extends(Baseview('app'))
@push('after_styles')
@loadStyleOnce('js/packages/select2/dist/css/select2.min.css')
@loadStyleOnce('js/packages/select2-bootstrap-theme/dist/select2-bootstrap-5-theme.rtl.min.css')
@loadStyleOnce('js/packages/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css')
@loadStyleOnce('css/employment/apply.css')
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
    </style>
@endpush
@push('scripts')
@loadScriptOnce('js/packages/select2/dist/js/select2.full.min.js')
@loadScriptOnce('js/packages/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')
@loadScriptOnce('js/packages/jquery-qrcode-master/jquery-qrcode-master/dist/jquery-qrcode.js')
@loadScriptOnce('js/employment/checknid.js')
<script type="application/javascript">
    jstrans['nidisnot14']="{{trans('JOBLANG::apply.nidisnot14')}}";
    jstrans['nidIssetBefore']="{{trans('JOBLANG::apply.nidIssetBefore')}}";
    jstrans['nidtestSuccess']="{{trans('JOBLANG::apply.nidtestSuccess')}}";
    jstrans['pleasefillinputs']="{{trans('trojan.pleasefillinputs')}}";
    jstrans['data_entered']="{{trans('trojan.data_entered')}}";
    jstrans['data_not_entered']="{{trans('trojan.data_not_entered')}}";
    jstrans['nid']="{{trans('trojan.nid')}}";
    jstrans['times_added']="{{trans('JOBLANG::apply.times_added')}}";
    jstrans['annonce_job_info_added']="{{trans('JOBLANG::apply.annonce_job_info_added')}}";
    jstrans['jobs_addes']="{{trans('jobs.jobs_addes')}}";
    jstrans['govs_added']="{{trans('places.govs_added')}}";
    jstrans['health']="{{trans('health.health')}}";
    jstrans['arm']="{{trans('arm.arm')}}";
    jstrans['ama']="{{trans('ama.khedma_ama')}}";
    jstrans['mir']="{{trans('mir.mir')}}";
    jstrans['education']="{{trans('education.education')}}";
    jstrans['bd_applyed']="{{trans('JOBLANG::apply.bd_applyed')}}";
    jstrans['age_applyed']="{{trans('JOBLANG::apply.age_applyed')}}";
    jstrans['sex']="{{trans('JOBLANG::Employment_People.Sex.Sex')}}";
    jstrans['driver']="{{trans('driver.driver')}}";
    jstrans['uploades_type']="{{trans('errors.uploades_type')}}";
    jstrans['uploades_only_one']="{{trans('errors.uploades_only_one')}}";
    jstrans['uploades_size_6000']="{{trans('errors.uploades_size_6000')}}";
    jstrans['Mosama_Experiences_enum_1']="{{trans('EMPLANG::Mosama_Experiences.enum_1')}}";
    jstrans['Mosama_Experiences_enum_0']="{{trans('EMPLANG::Mosama_Experiences.enum_0')}}";
    jstrans['Mosama_Experiences_enum_translate']="{{trans('EMPLANG::Mosama_Experiences.translate')}}";
    const MainData={{Illuminate\Support\Js::from($data)}};
    const StageId={{$stageid ?? 'null'}};
    const PeopleNewStageId={{$PeopleNewStageId ?? 'null'}};
  </script>
  @loadScriptOnce('js/arabic.js')
  @loadScriptOnce('js/employment/apply.js')
@endpush
@section('content')
   @parent
   <div class="container omg">
        <div class='errors' alop='@json($errors->getMessages())'>
            @if($errors->first('message'))
            <?php
            print_r($errors->first('message'));
            ?>
                @push('scripts')
                    <script type="application/javascript">
                        showerror(jstrans['data_entered'],jstrans['data_entered']);
                    </script>
                @endpush
            @endif
        </div>
        <form method="post" id="ENTRYFORM" class="{{$trojan['html']['direction'] ?? 'right'}}-justification {{$trojan['html']['direction'] ?? 'right'}}-aligned {{$trojan['html']['direction'] ?? 'right'}}-middle" name="{{$request}}" enctype="multipart/form-data" accept-charset="utf-8" acc autocomplete="on">
        @csrf
            @method('POST')
            <?php
            if(is_object($job)){
                $job=$job->Slug;
            }
            if(is_object($annonce)){
                $annonce=$annonce->Slug;
            }
            ?>
            <input type="hidden" data-init-function='setCurrentDateTime' name="apply_date" id="apply_date" value=''>
            <div id='hiddens' data='{{$annonce}}' jb='{{$job}}'></div>
            <input type="hidden" value="{{$annonce}}" name="annonceSlug" id="annonceSlug">
            <input type="hidden" value="{{$job}}" name="jobSlug" id="jobSlug">
            <div class="container border rounded shadow">
                <div class="row text-right border-bottom"  data-aos="zoom-in">
                    <div class="col-sm nsscwwbgcolor text-white text-center">
                        <div class="p-3 mb-2">
                            {{$page_title}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="container border rounded shadow">
                @include('Employment::apply_form.jobinfo')
                @include('Employment::apply_form.select_job')
                @include('Employment::apply_form.fullname')
                @include('Employment::apply_form.nid')
                @include('Employment::apply_form.born')
                @include('Employment::apply_form.live')
                @include('Employment::apply_form.connection')
                @include('Employment::apply_form.Employment_Health')
                @include('Employment::apply_form.Employment_MaritalStatus')
                @include('Employment::apply_form.Employment_Army')
                @include('Employment::apply_form.Employment_Ama')
                @include('Employment::apply_form.Mosama_Educations')
                @include('Employment::apply_form.DriverDegree')
                @include('Employment::apply_form.Mosama_Experiences')
                @include('Employment::apply_form.malaftaminy')
                @include('Employment::apply_form.Employment_IncludedFiles')
                @include('Employment::apply_form.Employment_Instructions')
                @include('Employment::apply_form.submit')
            </div>
            @if(isset($nid))
            
                <input type="hidden" value="{{$nid}}" name="uinid" id="uinid">
                <input type="hidden" value="{{$uid}}" name="uid" id="uid">
                <input type="hidden" value="complete" name="actiontype" id="actiontype" data-init-function="load_old_data">
            @else
                <input type="hidden" value="create" name="actiontype" id="actiontype">
                <div class="progress" style="z-index:1;">
                    <div id='progress-bar' class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 1%"></div>
                </div>
            @endif
        </form>
    </div>
@endsection
