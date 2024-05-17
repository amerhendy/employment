<?php
//dd($data['job']['Employment_People']->Job_id,$data['job']);
$applyText=$data['job']['Employment_People']->Stage_id->apply->Text;
$completeText=$data['job']['job']['user']->Employment_StartAnnonces->Employment_Stages[0];
//dd($data['job']['Employment_People']);
$oldUser=$data['job']['Employment_People'];
$userinfo=$data['job']['user'];
$job=$data['job']['job']['user'];
$annonce=$job->Employment_StartAnnonces;
$annonceNumber=$annonce->Number;
$annonceYear=$annonce->Year;
if(!empty($job->Employment_Drivers)){
    $driver=$job->Employment_Drivers;
}else{
    $driver=1;
}
//if($job->Employment_Drivers == 'null' || $job->Employment_Drivers == '' || $job->Employment_Drivers=null || (is_array($job->Employment_Drivers) && count($job->Employment_Drivers) == 0)){$driver=0;}else{$driver=$data->Employment_Drivers;}

if(count($annonce->Governorates)){
    $annonceGovernorates=implode(" - ",$annonce->Governorates);
}else{
    $annonceGovernorates=null;
}
if($annonce->Description == null || $annonce->Description == ''){$Description=null;}else{$Description=$annonce->Description;}

function setJob($data){
    if(property_exists($data,'Mosama_JobNames')){
        $html[]=$data->Mosama_JobNames->Text;
    }elseif(property_exists($data,'Text')){
        $html[]=$data->Text;
    }
    if(property_exists($data,'code')){
        $html[]=trans('JOBLANG::Employment_jobs.Code').': '.$data->code;
    }elseif(property_exists($data,'Code')){
        $html[]=trans('JOBLANG::Employment_jobs.Code').': '.$data->Code;
    }
    return implode(' - ',$html);
}
function setExperiences($data){
    $ex=[];
    foreach ($data as $key => $value) {
        $ex[]=\Amerhendy\Employment\App\Http\Controllers\api\printTrait::khebraToStr($value);
    }
    return implode('<Br>',$ex);
}
function setList($data){
    $html='<ul><li>';
    $html.= implode('</li><li>',$data);
    $html.='</li></ul>';
    return $html;
}
?>
<table>
    <thead>
    <tr>
            <td class="loHightTd">
                {{$data['job']['headerTitle']}}
            </td>
            <td>{{$userinfo['apply_date']}}</td>
        </tr>
        <tr>
            <td class="loHightTd">
                {{trans('JOBLANG::Employment_StartAnnonces.homepage_annonce_number')}} - 
                <i CLASS="annonceNumber">{{$annonceNumber ?? '00'}}</i>
                {{trans('JOBLANG::Employment_StartAnnonces.homepage_annonce_foryear')}}
                <SPAN CLASS="annonceYear">{{$annonceYear ?? '1900'}}</SPAN>
                @if($annonce->Description !== null)
                - 
                <cite title="Source Title" class="annonceDescription">{!! $Description ?? '.....' !!}</cite> -
                @endif
                @if($annonceGovernorates !== null)
                <i CLASS="annonceGovernorates">{{$annonceGovernorates ?? ' ---- '}}</i>
                @endif

            </td>
        </tr>
        <tr>
            <td class="loHightTd w30 wborder border_dashed text-center"></td>
            <td class="loHightTd w80 wborder border_dashed text-center">{{$applyText ?? 'apply'}}</td>
            <td class="loHightTd w80 wborder border_dashed text-center">{{$completeText ?? 'complete'}}</td>
        </tr>
    </thead>
    <tbody>
    <TR>
            <td class="loHightTd w30 wborder">
                    {{trans('EMPLANG::Mosama_JobTitles.singular')}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {!! setJob($job) !!}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {!! setJob($data['job']['Employment_People']->Job_id) !!}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                    {{ trans('JOBLANG::Employment_People.FULLname')}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$oldUser->FullName}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$userinfo['fullname']}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                    {{ trans('JOBLANG::Employment_People.NID')}}
            </td>
            <td class="loHightTd w160 fullright wborder">
                {{$oldUser->NID}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                    {{ trans('JOBLANG::Employment_People.Sex.Sex')}}
            </td>
            <td class="loHightTd w160 fullright wborder">
                {{$oldUser->Sex}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                    {{ trans('JOBLANG::Employment_People.Age.Age')}}
            </td>
            <td class="loHightTd w160 fullright wborder">
                {{implode("/",[$oldUser->AgeYears,$oldUser->AgeMonths,$oldUser->AgeDays])}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                    {{ trans('JOBLANG::Employment_People.bornPlace.bornPlace')}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$oldUser->BornPlace}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$userinfo['birth_blace']}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                    {{ trans('JOBLANG::Employment_People.LivePlace.LivePlace')}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$oldUser->LivePlace}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$userinfo['live_place']}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                {{ trans('JOBLANG::Employment_People.Connection.Connection')}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{ trans('JOBLANG::Employment_People.Connection.LandLine')}}:{{$oldUser->Connection->Landline}}<br>
                {{ trans('JOBLANG::Employment_People.Connection.Mobile')}}:{{$oldUser->Connection->Mobile}}<br>
                {{ trans('JOBLANG::Employment_People.Connection.Email')}}:{{$oldUser->Connection->Email}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{ trans('JOBLANG::Employment_People.Connection.LandLine')}}:{{$userinfo['ConnectLandline']}}<br>
                {{ trans('JOBLANG::Employment_People.Connection.Mobile')}}:{{$userinfo['ConnectMobile']}}<br>
                {{ trans('JOBLANG::Employment_People.Connection.Email')}}:{{$userinfo['ConnectEmail']}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                {{ trans('JOBLANG::Employment_Health.Employment_Health')}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$oldUser->Health_id}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$userinfo['Health_id']}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                {{ trans('JOBLANG::Employment_MaritalStatus.Employment_MaritalStatus')}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$oldUser->MaritalStatus_id}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$userinfo['MaritalStatus_id']}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                {{ trans('JOBLANG::Employment_Army.Employment_Army')}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$oldUser->Arm_id}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$userinfo['Employment_Army']}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                {{ trans('JOBLANG::Employment_Ama.Employment_Ama')}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$oldUser->Ama_id}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$userinfo['Employment_Ama']}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                {{ trans('JOBLANG::Employment_People.Mosama_Educations.Mosama_Educations')}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$oldUser->Education_id}}<br>{{ trans('JOBLANG::Employment_People.Mosama_Educations.year')}}: {{$oldUser->EducationYear}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$userinfo['Education_id']}}<br>{{ trans('JOBLANG::Employment_People.Mosama_Educations.year')}}: {{$userinfo['EducationYear']}}
            </td>
        </TR>
        @if(((int) $oldUser->Job_id->Driver == 1) || ((int) $driver == 1))
        <TR>
            <td class="loHightTd w30 wborder">
                {{ trans("JOBLANG::Employment_People.Employment_Drivers.DriverDegree")}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                @if((int) $oldUser->Job_id->Driver == 1) 
                {{ trans("JOBLANG::Employment_People.Employment_Drivers.DriverDegree")}}:{{$oldUser->DriverDegree}}<br>
                {{ trans("JOBLANG::Employment_People.Employment_Drivers.DriverStart")}}:{{$oldUser->DriverStart}}<br>
                {{ trans("JOBLANG::Employment_People.Employment_Drivers.DriverEnd")}}:{{$oldUser->DriverEnd}}
                @endif
            </td>
            <td class="loHightTd w80 fullright wborder">
                @if((int) $driver == 1)
                {{ trans("JOBLANG::Employment_People.Employment_Drivers.DriverDegree")}}:{{$userinfo['DriverDegree']}}<br>
                {{ trans("JOBLANG::Employment_People.Employment_Drivers.DriverStart")}}:{{$userinfo['DriverStart']}}<br>
                {{ trans("JOBLANG::Employment_People.Employment_Drivers.DriverEnd")}}:{{$userinfo['DriverEnd']}}
                @endif

            </td>
        </TR>
        @endif
        <TR>
            <td class="loHightTd w30 wborder">
                {{trans('EMPLANG::Mosama_Experiences.Mosama_Experiences')}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$oldUser->Khebra}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$userinfo['Khebra_type']}} - {{$userinfo['Khebra']}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                {{ trans('JOBLANG::Employment_people.Tamin.Tamin')}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$oldUser->Tamin}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$userinfo['Tamin']}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                {{ trans('JOBLANG::Employment_IncludedFiles.Employment_IncludedFiles')}}
            </td>
            <td class="loHightTd w80 fullright wborder"></td>
            <td class="loHightTd w80 fullright wborder">
                {{$userinfo['uploades']}}
            </td>
        </TR>
    </tbody>
</table>