<?php
$Mosama_JobNames=$request['annonce_job']['Mosama_JobNames']['Text'];
$Mosama_Tasks=$request['annonce_job']['Mosama_JobNames']['Mosama_Tasks'];
$Mosama_Skills=$request['annonce_job']['Mosama_JobNames']['Mosama_Skills'];
$Mosama_Goals=$request['annonce_job']['Mosama_JobNames']['Mosama_Goals'];
$Mosama_Experiences=$request['annonce_job']['Mosama_JobNames']['Mosama_Experiences'];
$Mosama_Competencies=$request['annonce_job']['Mosama_JobNames']['Mosama_Competencies'];
$Code=$request['annonce_job']['code'];
$Description=$request['annonce_job']['Description'];
$Count=(int) $request['annonce_job']['Count'];
$AgeIn= $request['annonce_job']['AgeIn'];
$Age=(int) $request['annonce_job']['Age'];
$Driver=$request['annonce_job']['Driver'];
$Mosama_JobTitles=$request['annonce_job']['Mosama_JobTitles'];
$Employment_Ama=$request['annonce_job']['Employment_Ama'];
$Employment_Army=$request['annonce_job']['Employment_Army'];
$Employment_Health=$request['annonce_job']['Employment_Health'];
$Employment_Instructions=$request['annonce_job']['Employment_Instructions'];
$Employment_MaritalStatus=$request['annonce_job']['Employment_MaritalStatus'];
$Employment_Qualifications=$request['annonce_job']['Employment_Qualifications'];
$Employment_Drivers=$request['annonce_job']['Employment_Drivers'];
$Employment_IncludedFiles=$request['annonce_job']['Employment_IncludedFiles'];
$Mosama_Educations=$request['annonce_job']['Mosama_Educations'];
$Cities=$request['annonce_job']['Cities'];
$Mosama_Groups=$request['annonce_job']['Mosama_Groups'];
//dd($request['annonce_job']);
?>
<div class='row text-right' id='newline'>
    <div class='col-sm-2 border-bottom'>{{trans('EMPLANG::Mosama_JobNames.singular')}}</div>
    <div class='col-sm-7 border-bottom-colored'>{{$Mosama_JobNames}}</div>
</div>
<div class='row text-right' id='newline'>
    <div class='col-sm-2 border-bottom'>{{trans('EMPLANG::Mosama_Groups.singular')}}</div>
    <div class='col-sm border-bottom-colored'>{{$Mosama_Groups}}</div>
</div>
<div class='row text-right' id='newline'>
    <div class='col-sm-2 border-bottom'>{{trans('EMPLANG::Mosama_Educations.singular')}}</div>
    <div class='col-sm border-bottom-colored'>@php echo implode(', ',$Mosama_Educations); @endphp</div>
</div>
<div class='row text-right' id='newline'>
    <div class='col-sm-2  border-bottom'>{{trans('JOBLANG::Employment_Jobs.CityBornLive')}}</div>
    <div class='col-sm border-bottom-colored'>@php echo implode(', ',$Cities); @endphp</div>
    
</div>
<div class='row text-right' id='newline'>
    <div class='col-sm-2 border-bottom'>{{trans('JOBLANG::Employment_Ama.Employment_Ama')}}</div>
    <div class='col-sm border-bottom-colored'>@php echo implode(', ',$Employment_Ama); @endphp</div>
</div>
<div class='row text-right' id='newline'>
    <div class='col-sm-2 border-bottom'>{{trans('JOBLANG::Employment_Army.singular')}}</div>
    <div class='col-sm border-bottom-colored'>@php echo implode(', ',$Employment_Army); @endphp</div>
</div>
<div class='row text-right' id='newline'>
    <div class='col-sm-2 border-bottom'>{{trans('JOBLANG::Employment_Health.singular')}}</div>
    <div class='col-sm border-bottom-colored'>@php echo implode(', ',$Employment_Health); @endphp</div>
</div>
<div class='row text-right' id='newline'>
    <div class='col-sm-2 border-bottom'>{{trans('JOBLANG::Employment_MaritalStatus.Employment_MaritalStatus')}}</div>
    <div class='col-sm border-bottom-colored'>@php echo implode(', ',$Employment_MaritalStatus); @endphp</div>
</div>
@if($Driver == 0)
    @if(is_array($Employment_Drivers))
    <div class='row text-right' id='newline'>
        <div class='col-sm-2 border-bottom'>{{trans('JOBLANG::Employment_Drivers.Employment_Drivers')}}</div>
        <div class='col-sm border-bottom-colored'>@php echo implode(', ',$Employment_Drivers); @endphp</div>
    </div>
    @endif
@endif
<div class='row text-right' id='newline'>
    <div class='col-sm-2 border-bottom'>{{trans('EMPLANG::Mosama_Experiences.singular')}}</div>
    <div class='col-sm border-bottom-colored'>@foreach($Mosama_Experiences as $a) {{$a}}, @endforeach</div>
    </div>
    
<div class='row text-right' id='newline'>
    <div class='col-sm'>{{trans('JOBLANG::Apply.homepage_job_age_not_more.homepage_job_age_not_more')}}</div>
    <div class='col-sm'>{{$Age}} {{trans('JOBLANG::Apply.homepage_job_age_not_more.year')}} {{trans('JOBLANG::Apply.homepage_job_age_not_more.in')}}  {{\AmerHelper::ArabicDate($AgeIn->year, $AgeIn->month, $AgeIn->day)}}</div>
</div>