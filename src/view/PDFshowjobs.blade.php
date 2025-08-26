<?php
$data=$data['job'];
$annonce=$data->Employment_StartAnnonces;
$annonceNumber=$annonce->Number;
$annonceYear=$annonce->Year;
$DriverNull=['null',null,'',false,'false'];
if(in_array($data->Employment_Drivers,$DriverNull)){
    $driver=0;
}elseif(!in_array(gettype($data->Employment_Drivers),['array','object'])){
    $driver=0;
}else{
    if(count($data->Employment_Drivers) == 0){$driver=0;}else{$driver=$data->Employment_Drivers;}
}
if(count($annonce->Governorates)){
    $annonceGovernorates=implode(" - ",$annonce->Governorates);
}else{
    $annonceGovernorates=null;
}
if($annonce->Description == null || $annonce->Description == ''){$Description=null;}else{$Description=$annonce->Description;}

function setJob($data){
    $html[]=$data->Mosama_JobNames->Text.`(<i>`.$data->Mosama_JobTitles.`</i>)`;
    $html[]=trans('JOBLANG::apply.homepage_job_age_not_more.homepage_job_age_not_more').' <i>'.$data->AgeIn->Age.'</i> '.
    trans('JOBLANG::apply.homepage_job_age_not_more.in').' <i>'.\AmerHelper::ArabicDate($data->AgeIn->agein->Year,$data->AgeIn->agein->Month,$data->AgeIn->agein->Day).'</i> '.
    '';
    $html[]=trans('JOBLANG::Employment_Jobs.Code').': '.$data->code;
    if($data->Count !== 0){$html[]=trans('JOBLANG::Employment_Jobs.Count').': '.$data->Count;}
    if(!is_null($data->Description)){$html[]=trans('JOBLANG::Employment_Jobs.Description').': '.$data->Description;}
    $html[]=trans('EMPLANG::Mosama_Groups.Mosama_Groups').': '.$data->Mosama_Groups;

    return implode('<Br>',$html);
}
function setExperiences($data){
    $ex=[];
    if(is_array($data)){
        foreach ($data as $key => $value) {
            $ex[]=\Amerhendy\Employment\App\Http\Controllers\api\printTrait::khebraToStr($value);
        }
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
                {{trans('JOBLANG::Employment_StartAnnonces.homepage_annonce_number')}}
                <i CLASS="annonceNumber">{{$annonceNumber ?? '00'}}</i>
                {{trans('JOBLANG::Employment_StartAnnonces.homepage_annonce_foryear')}}
                <SPAN CLASS="annonceYear">{{$annonceYear ?? '1900'}}</SPAN>
                @if($annonce->Description !== null)
                <br>
                <cite title="Source Title" class="annonceDescription">{!! $Description ?? '.....' !!}</cite>
                @endif
            </td>
        </tr>
        @if($annonceGovernorates !== null)
        <tr>
            <td class="lead loHightTd">
                <i CLASS="annonceGovernorates">{{$annonceGovernorates ?? ' ---- '}}</i>
            </td>
        </tr>
        @endif
    </thead>
    <tbody>
        <!-- setJob -->
        <tr>
            <td class="loHightTd w30 scopeRow">
                {{trans('EMPLANG::Mosama_JobTitles.singular')}}
            </td>
            <td class="loHightTd w160 fullright border_dashed">
                {!! setJob($data) !!}
            </td>
        </tr>
        <!-- setJob -->
        <!-- Mosama_Experiences -->
        <tr>
            <td class="loHightTd w30 scopeRow">
                {{trans('EMPLANG::Mosama_Experiences.singular')}}
                <br>
                <i class="info">{{trans('JOBLANG::apply.chooseone')}}</i>
            </td>
            <td class="loHightTd w60 fullright border_dashed">
                <?php
                ?>
                {!! setExperiences($data->Mosama_JobNames->Mosama_Experiences) !!}
            </td>
            <td class="loHightTd w30 scopeRow">
                {{trans('JOBLANG::Employment_Jobs.CityBornLive')}}
                <br>
                <i class="info">{{trans('JOBLANG::apply.chooseone')}}</i>
            </td>
            <td class="loHightTd w60 fullright border_dashed">
                {!! implode(', ',$data->Cities) !!}
            </td>
        </tr>
        <!-- Mosama_Experiences -->
        <!-- Employment_Ama -->
        <tr>
            <td class="loHightTd w30 scopeRow">
                {{trans('JOBLANG::Employment_Ama.Employment_Ama')}}
                <br>
                <i class="info">{{trans('JOBLANG::apply.chooseone')}}</i>
            </td>
            <td class="loHightTd w60 fullright border_dashed">
                {!! implode(' - ',$data->Employment_Ama) !!}
            </td>
            <td class="loHightTd w30 scopeRow">
                {{trans('JOBLANG::Employment_Army.Employment_Army')}}
                <br>
                <i class="info">{{trans('JOBLANG::apply.chooseone')}}</i>
            </td>
            <td class="loHightTd w60 fullright border_dashed">
                {!! implode(', ',$data->Employment_Army) !!}
            </td>
        </tr>
        <!-- Employment_Ama -->
        <!-- Employment_Health -->
        <tr>
            <td class="loHightTd w30 scopeRow">
                {{trans('JOBLANG::Employment_Health.Employment_Health')}}
                <br>
                <i class="info">{{trans('JOBLANG::apply.chooseone')}}</i>
            </td>
            <td class="loHightTd w60 fullright border_dashed">
                {!! implode(' - ',$data->Employment_Health) !!}
            </td>
            <td class="loHightTd w30 scopeRow">
                {{trans('JOBLANG::Employment_MaritalStatus.Employment_MaritalStatus')}}
                <br>
                <i class="info">{{trans('JOBLANG::apply.chooseone')}}</i>
            </td>
            <td class="loHightTd w60 fullright border_dashed">
                {!! implode(', ',$data->Employment_MaritalStatus) !!}
            </td>
        </tr>
        <!-- Employment_Health -->
        <!-- Mosama_Educations -->
        <tr>
            <td class="loHightTd w30 scopeRow">
                {{trans('EMPLANG::Mosama_Educations.Mosama_Educations')}}
                <br>
                <i class="info">{{trans('JOBLANG::apply.chooseone')}}</i>
            </td>
            <td class="loHightTd w60 fullright border_dashed">
                {!! setList($data->Mosama_Educations) !!}
            </td>

            <td class="loHightTd w30 border_dashed @if($driver !== 0)  border_left @endif">
            @if($driver !== 0)
                {{trans('JOBLANG::Employment_Drivers.Employment_Drivers')}}
                <br>
                <i class="info">{{trans('JOBLANG::apply.chooseone')}}</i>
                @endif
            </td>
            <td class="loHightTd w60 fullright border_dashed">
            @if($driver !== 0)
                {!! setList($data->Employment_Drivers) !!}
                @endif
            </td>

        </tr>
        <!-- Mosama_Educations -->

        <!-- Mosama_Goals -->
        <tr>
            <td class="loHightTd w30 scopeRow">
                {{trans('EMPLANG::Mosama_Goals.Mosama_Goals')}}
            </td>
            <td class="loHightTd w160 fullright border_dashed">
                {!! implode(' - ',$data->Mosama_JobNames->Mosama_Goals) !!}
            </td>
        </tr>
        <!-- Mosama_Goals -->
        <!-- Mosama_Competencies -->
        <tr>
            <td class="loHightTd w30 scopeRow">
                {{trans('EMPLANG::Mosama_Competencies.Mosama_Competencies')}}
            </td>
            <td class="loHightTd w160 fullright border_dashed">
                {!! implode(' - ',$data->Mosama_JobNames->Mosama_Competencies) !!}
            </td>
        </tr>
        <!-- Mosama_Competencies -->
        <!-- Mosama_Tasks -->
        <tr>
            <td class="loHightTd w30 scopeRow">
                {{trans('EMPLANG::Mosama_Tasks.Mosama_Tasks')}}
            </td>
            <td class="loHightTd w160 fullright border_dashed">
                {!! implode(' - ',$data->Mosama_JobNames->Mosama_Tasks) !!}
            </td>
        </tr>
        <!-- Mosama_Tasks -->
        <!-- Mosama_Skills -->
        <tr>
            <td class="loHightTd w30 scopeRow">
                {{trans('EMPLANG::Mosama_Skills.Mosama_Skills')}}
            </td>
            <td class="loHightTd w160 fullright border_dashed">
                {!! implode(' - ',$data->Mosama_JobNames->Mosama_Skills) !!}
            </td>
        </tr>
        <!-- Mosama_Skills -->
        <!-- Employment_Qualifications -->
        <tr>
            <td class="loHightTd w30 scopeRow">
                {{trans('JOBLANG::Employment_Qualifications.Employment_Qualifications')}}
                <br>
                <i class="info">{{trans('JOBLANG::apply.chooseall')}}</i>
            </td>
            <td class="loHightTd w160 fullright border_dashed">
                {!! setList($data->Employment_Qualifications) !!}
            </td>
        </tr>
        <!-- Employment_Qualifications -->
        <!-- Employment_IncludedFiles -->
        <tr>
            <td class="loHightTd w30 scopeRow">
                {{trans('JOBLANG::Employment_IncludedFiles.Employment_IncludedFiles')}}
                <br>
                <i class="info">{{trans('JOBLANG::apply.chooseall')}}</i>
            </td>
            <td class="loHightTd w160 fullright border_dashed">
                {!! setList($data->Employment_IncludedFiles) !!}
            </td>
        </tr>
        <!-- Employment_IncludedFiles -->
    </tbody>
</table>
