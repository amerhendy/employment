<!-- jobinfo -->
<div class='col aqua-gradient text-center'>
    {{trans('JOBLANG::apply.homepage_annonce_number')}} (<span class='info_title_functional_annonce_number'></span>) {{trans('JOBLANG::apply.homepage_annonce_foryear')}} <span class='info_title_functional_annonce_number_foryear'></span> - <span class='info_title_functional_annonce_place'></span>
    <br>
    <span class='info_title_functional_annonce_desc'></span>
    <br>
    <span class="info_title_jobname"></span>
</div>
<?php
$accarray=[
    [
        'title'=>'Mosama_JobNames',
        'titletext'=>trans('JOBLANG::apply.apply_job_info'),
        'body'=>'<ul><li>'.trans('EMPLANG::Mosama_JobNames.singular').":<span class='info_title_name'></span> (<span class='info_title_jobname'></span>)</li><li>"
        .trans('EMPLANG::Mosama_Groups.singular').": <span class='info_title_functional_class'></span></li><li>"
        .trans('JOBLANG::Employment_Jobs.Code').": <span class='info_job_code'></span></li>
        <li class='mployment_JobsDescription'>".trans('JOBLANG::Employment_Jobs.Description').": <span class='info_job_description'></span></li>
        <li class='mployment_JobsCount'>".trans('JOBLANG::Employment_Jobs.Count').": <span class='info_Count'></span></li></ul>"
    ],
    [
        'title'=>'CityBornLive',
        'titletext'=>trans('JOBLANG::Employment_Jobs.CityBornLive'),
        'body'=>'<ul><li>'.trans('AMER::Governorates.Governorates')."(<span class='info_title_functional_annonce_place'></span>)</li><li>"
        .trans('AMER::Cities.Cities')."(<span class='info_idcity'></span>)</li></ul>"
    ],
    [
        'title'=>'Mosama_Experiences',
        'titletext'=>trans('EMPLANG::Mosama_Experiences.singular'),
        'body'=>"<span class='info_job_khebrayears'></span>"
    ],
    [
        'title'=>'Employment_Drivers',
        'titletext'=>trans('JOBLANG::Employment_Drivers.Employment_Drivers'),
        'body'=>"<span class='info_driver_degree'></span>"
    ],[
        'title'=>'Employment_Ama',
        'titletext'=>trans('JOBLANG::Employment_Ama.Employment_Ama').' - '.trans('JOBLANG::Employment_Army.singular'),
        'body'=>'<ul><li>'.trans('JOBLANG::Employment_Ama.Employment_Ama').": <span class='info_ama'></span></li><li>"
        .trans('JOBLANG::Employment_Army.singular').": <span class='info_arm'></span></li></ul>"
    ],
    [
        'title'=>'Mosama_Educations',
        'titletext'=>trans('EMPLANG::Mosama_Educations.singular'),
        'body'=>"<span class='info_edu'></span>"
    ],
    [
        'title'=>'Employment_Health',
        'titletext'=>trans('JOBLANG::Employment_Health.singular'),
        'body'=>"<span class='info_health'></span>"
    ],
    [
        'title'=>'Employment_MaritalStatus',
        'titletext'=>trans('JOBLANG::Employment_MaritalStatus.Employment_MaritalStatus'),
        'body'=>"<span class='info_mir'></span>"
    ],
    [
        'title'=>'Employment_IncludedFiles',
        'titletext'=>trans('JOBLANG::Employment_IncludedFiles.Employment_IncludedFiles'),
        'body'=>"<span class='included_files'></span>"
    ],
    [
        'title'=>'Employment_Instructions',
        'titletext'=>trans('JOBLANG::Employment_Instructions.singular'),
        'body'=>"<span class='info_instructions'></span>"
    ],
    [
        'title'=>'Employment_Qualifications',
        'titletext'=>trans('JOBLANG::Employment_Qualifications.singular'),
        'body'=>trans('JOBLANG::apply.homepage_job_age_not_more.homepage_job_age_not_more').
        " (<span class='job_info_age'></span>)".trans('JOBLANG::apply.homepage_job_age_not_more.year')." ".trans('JOBLANG::apply.homepage_job_age_not_more.in')." <span class='job_info_age_date'></span>
        <br><span class='info_title_functional_annonce_qual'></span><br>
        <span class='info_title_functional_job_qual'></span>"
    ],
];
list($array1, $array2) = array_chunk($accarray, ceil(count($accarray) / 2));
?>
<div id="div_job_info"  class="row">
    <div class="col-sm-6">
        <div id="accordion">
            @foreach($array1 as $item)
            <h3 id="{{$item['title']}}-heading">{{$item['titletext']}}</h3>
            <div id="{{$item['title']}}-collapse">
                {!! $item['body'] !!}
            </div>
            @endforeach
        </div>
    </div>
    <div class="col-sm-6">
        <div id="accordion1">
            @foreach($array2 as $item)
            <h3 id="{{$item['title']}}-heading">{{$item['titletext']}}</h3>
            <div id="{{$item['title']}}-collapse">
                {!! $item['body'] !!}
            </div>
            @endforeach
        </div>
    </div>
</div>
<div class="wantedagein" style="display:none;"></div>
<?php
//dd(date_default_timezone_get());
?>
