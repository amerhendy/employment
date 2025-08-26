@extends(Baseview('app'))
@push('after_scripts')
@loadScriptOnce('js/employment/checknid.js')
@loadScriptOnce('js/employment/showjob.js')
@loadScriptOnce('js/arabic.js')
<script type="application/javascript">
    //jstrans['nid_phisical_error']="{{trans('JOBLANG::apply.nid_phisical_error')}}";
    //jstrans['nid_Already_Exists']="{{trans('JOBLANG::apply.nid_Already_Exists')}}";
    //jstrans['nid_not_Exists']="{{trans('JOBLANG::apply.nid_not_Exists')}}";
    //jstrans['close']="{{trans('JOBLANG::apply.close')}}";
    jstrans['homepage_annonce_number']="{{trans('JOBLANG::Employment_StartAnnonces.homepage_annonce_number')}}";
    jstrans['homepage_annonce_foryear']="{{trans('JOBLANG::Employment_StartAnnonces.homepage_annonce_foryear')}}"
    //jstrans['TypeYourNid']="{{trans('JOBLANG::apply.TypeYourNid')}}"
    months={{ Illuminate\Support\Js::from(trans("AMER::trojan.months")) }};
    jstrans['Mosama_JobTitles']={{ Illuminate\Support\Js::from(trans('EMPLANG::Mosama_JobTitles')) }};
    jstrans['apply']={{ Illuminate\Support\Js::from(trans('JOBLANG::apply')) }};
    jstrans['Employment_Army']={{ Illuminate\Support\Js::from(trans('JOBLANG::Employment_Army')) }};
    jstrans['Employment_Health']={{ Illuminate\Support\Js::from(trans('JOBLANG::Employment_Health')) }};
    jstrans['Employment_Instructions']={{ Illuminate\Support\Js::from(trans('JOBLANG::Employment_Instructions')) }};
    jstrans['Employment_jobs']={{ Illuminate\Support\Js::from(trans('JOBLANG::Employment_jobs')) }};
    jstrans['Employment_MaritalStatus']={{ Illuminate\Support\Js::from(trans('JOBLANG::Employment_MaritalStatus')) }};
    jstrans['Employment_Ama']={{ Illuminate\Support\Js::from(trans('JOBLANG::Employment_Ama')) }};
    jstrans['Employment_Drivers']={{ Illuminate\Support\Js::from(trans('JOBLANG::Employment_Drivers')) }};
    jstrans['Employment_Qualifications']={{ Illuminate\Support\Js::from(trans('JOBLANG::Employment_Qualifications')) }};
    jstrans['Employment_Army']={{ Illuminate\Support\Js::from(trans('JOBLANG::Employment_Army')) }};
    jstrans['Employment_IncludedFiles']={{ Illuminate\Support\Js::from(trans('JOBLANG::Employment_IncludedFiles')) }};
    jstrans['Mosama_Experiences']={{ Illuminate\Support\Js::from(trans('EMPLANG::Mosama_Experiences')) }};
    jstrans['Mosama_Competencies']={{ Illuminate\Support\Js::from(trans('EMPLANG::Mosama_Competencies')) }};
    jstrans['Mosama_Tasks']={{ Illuminate\Support\Js::from(trans('EMPLANG::Mosama_Tasks')) }};
    jstrans['Mosama_Goals']={{ Illuminate\Support\Js::from(trans('EMPLANG::Mosama_Goals')) }};
    jstrans['Mosama_Skills']={{ Illuminate\Support\Js::from(trans('EMPLANG::Mosama_Skills')) }};
    jstrans['Mosama_Groups']={{ Illuminate\Support\Js::from(trans('EMPLANG::Mosama_Groups')) }};
    jstrans['Mosama_Educations']={{ Illuminate\Support\Js::from(trans('EMPLANG::Mosama_Educations')) }};
</script>
@endpush
<?php
?>
@section('content')
    @parent
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel"></h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="" method="POST">
      <div class="modal-body">
      <input type="number" name="nid" id="nid" class="form-control" value="28807051203034">
      <input type="hidden" name="page" value="showjob">
      <input type="hidden" id='nidannoncestage' name="annoncestage" value="">
            <span id="nidreal"></span>
            @csrf
            @method('POST')
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{trans('JOBLANG::apply.close')}}</button>
        <button type="submit" class="btn btn-primary" method="POST" id="savechanges" style="display: none;">Save changes</button>
      </div>
    </form>
    </div>
  </div>
</div>
<template id="annonceInfo">
  <figure>
    <blockquote class="blockquote">
      <p>
      {{trans('JOBLANG::Employment_StartAnnonces.homepage_annonce_number')}}
      (<i CLASS="annonceNumber"></i>)
      {{trans('JOBLANG::Employment_StartAnnonces.homepage_annonce_foryear')}}
      (<SPAN CLASS="annonceYear"></SPAN>)
      </p>
      <p class="lead">(<i CLASS="annonceGovernorates"></i>)</p>
    </blockquote>
    <figcaption class="blockquote-footer">
      <cite title="Source Title" class="annonceDescription">Source Title</cite>
    </figcaption>
  </figure>
</template>
<template id="infoTemplate">
<div class="col">
    <div class="card">
          <div class="card-header">{{trans('EMPLANG::Mosama_JobTitles.singular')}}</div>
      <div class="card-body">
        <p class="card-text">
            <SPAN CLASS="Mosama_JobTitles"></SPAN><br><SPAN CLASS="sjjjn">
              <br>
              {{trans('JOBLANG::apply.homepage_job_age_not_more.homepage_job_age_not_more')}} (<SPAN CLASS="sjanm"></SPAN>) {{trans('JOBLANG::apply.homepage_job_age_not_more.year')}}
            {{trans('JOBLANG::apply.homepage_job_age_not_more.in')}} <SPAN CLASS="sjanmat"></SPAN>
        </p>
      </div>
    </div>
</div>
</template>
<template id="nidaskTemplate">
  <span class="btn btn-primary btn-lg " id="app_pro" role="button"  data-bs-toggle="modal" data-bs-target="#exampleModal"></span>
</template>
<article>
<form id='showjobs' annonceid='{{$annid}}' jobid='{{$jobid}}' method="POST">
  <div class="container omg">
  @csrf
  @method('POST')
      <section id="annonceInfoSection">
          <div class="jumbotron my-0 text-center" id="annonceInfoDiv"></div>
      </section>
      <section id="jobInfoSection"></div>
      <section id="showJob-footer">
  <!-- <span class="btn btn-primary btn-lg" id="code" role="button" onclick="getcode();">كود</span> -->
  </section>
  </div>
</form>
</article>
@endsection
@push('after_styles')
@loadStyleOnce("css/employment/showjop.css","print")
@endpush
