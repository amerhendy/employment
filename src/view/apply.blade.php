@extends(Baseview('app'))
@push('after_styles')
@loadStyleOnce('js/packages/jquery-ui-1.14.0.custom/jquery-ui.css')
@loadStyleOnce('js/packages/select2/dist/css/select2.min.css')
@loadStyleOnce('js/packages/select2-bootstrap-theme/dist/select2-bootstrap-5-theme.rtl.min.css')
@loadStyleOnce('js/packages/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css')
@loadStyleOnce('css/employment/apply.css')
<style>
    label {
      display: inline-block; width: 5em;
    }
    fieldset div {
      margin-bottom: 2em;
    }
    fieldset .help {
      display: inline-block;
    }
    .ui-tooltip {
      width: 210px;
    }
    </style>
@endpush
@push('scripts')
@loadScriptOnce('js/packages/jquery-ui-1.14.0.custom/jquery-ui.min.js')
@loadScriptOnce('js/packages/select2/dist/js/select2.full.min.js')
@loadScriptOnce('js/packages/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')
@loadScriptOnce('js/packages/bootstrap-datepicker/dist/locales/bootstrap-datepicker.ar.min.js')
@loadScriptOnce('js/employment/checknid.js')
<script type="application/javascript">
    jstrans['Mosama_Experiences'] = {{ Illuminate\Support\Js::from(trans('EMPLANG::Mosama_Experiences')) }};
    jstrans['form']= {{ Illuminate\Support\Js::from(trans('JOBLANG::apply')) }};
    jstrans['Employment_People']= {{ Illuminate\Support\Js::from(trans('JOBLANG::Employment_People')) }};
    const MainData={{Illuminate\Support\Js::from($data)}};
    const StageId={{$stageid ?? 'null'}};
    const PeopleNewStageId={{$PeopleNewStageId ?? 'null'}};
  </script>
  @loadScriptOnce('js/arabic.js')
  @loadScriptOnce('js/employment/apply.js')
  @loadScriptOnce('js/employment/applycheck.js')
@endpush
@section('content')
   @parent
   <div class="row omg">
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
        <form
        method="post"
        id="ENTRYFORM"
        class="{{$trojan['html']['direction'] ?? 'right'}}-justification {{$trojan['html']['direction'] ?? 'right'}}-aligned {{$trojan['html']['direction'] ?? 'right'}}-middle needs-validation"
        name="{{$request}}"
        enctype="multipart/form-data"
        accept-charset="utf-8"
        acc
        autocomplete="on"
        novalidate
        >
        @csrf
            @method('POST')
            <?php
            if(is_object($job)){
                $job=$job->id;
            }
            if(is_object($annonce)){
                $annonce=$annonce->id;
            }
            ?>
            <input type="hidden" data-init-function='setCurrentDateTime' name="apply_date" id="apply_date" value=''>
            <div id='hiddens' data='{{$annonce}}' jb='{{$job}}'></div>
            <input type="hidden" value="{{$annonce}}" name="annonceid" id="annonceid">
            <input type="hidden" value="{{$job}}" name="jobid" id="jobid">
            <div class="border rounded shadow  menu-area">
                <div class="p-3 mb-2 text-center">
                    {{$page_title}}
                </div>
            </div>
            <div class="row">
                <div class="">
                    @include('Employment::apply_form.jobinfo')
                </div>
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

                <input type="hidden" value="{{$nid}}" name="uinid" id="uinid" required>
                <input type="hidden" value="{{$uid}}" name="uid" id="uid" required>
                <input type="hidden" value="complete" name="actiontype" id="actiontype" data-init-function="load_old_data" required>
            @else
                <input type="hidden" value="create" name="actiontype" id="actiontype" required>
                <div class="progress" style="z-index:1;">
                    <div id='progress-bar' class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 1%"></div>
                </div>
            @endif
        </form>
    </div>
@endsection
