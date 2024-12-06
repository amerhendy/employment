<?php
$tablewidth=$data['tablewidth'];
$data=$data['job'];
$userinfo=$data;
$job=$data->job;
$annonce=$job->Employment_StartAnnonces;
$annonceNumber=$annonce->Number;
$annonceYear=$annonce->Year;
$code=$data->QR;
$type='QRCODE,H';
$barcodeobj=new \TCPDF2DBarcode($data->QR, $type);
$base64 = 'data:image/png;base64,' . base64_encode($barcodeobj->getBarcodePngData());

if(!empty($job->Employment_Drivers)){
    $driver=$job->Employment_Drivers;
}else{
    $driver=0;
}
//if($job->Employment_Drivers == 'null' || $job->Employment_Drivers == '' || $job->Employment_Drivers=null || (is_array($job->Employment_Drivers) && count($job->Employment_Drivers) == 0)){$driver=0;}else{$driver=$data->Employment_Drivers;}

if(count($annonce->Governorates)){
    $annonceGovernorates=implode(" - ",$annonce->Governorates);
}else{
    $annonceGovernorates=null;
}
if($annonce->Description == null || $annonce->Description == ''){$Description=null;}else{$Description=$annonce->Description;}
function setJob($data){
    $html[]=$data->Mosama_JobNames->Text;
    $html[]=trans('JOBLANG::Employment_jobs.Code').': '.\AmerHelper::ArabicNumbersText($data->code);
    $html[]=trans('JOBLANG::apply.homepage_job_age_not_more.homepage_job_age_not_more').' <i>'.\AmerHelper::ArabicNumbersText($data->AgeIn->Age).'</i> '.
    trans('JOBLANG::apply.homepage_job_age_not_more.in').' <i>'.\AmerHelper::ArabicDate($data->AgeIn->agein->Year,$data->AgeIn->agein->Month,$data->AgeIn->agein->Day).'</i> '.
    '';
    if($data->Count !== 0){$html[]=trans('JOBLANG::Employment_jobs.Count').': '.\AmerHelper::ArabicNumbersText($data->Count);}
    if(!is_null($data->Description)){$html[]=trans('JOBLANG::Employment_jobs.Description').': '.$data->Description;}
    $html[]=trans('EMPLANG::Mosama_Groups.Mosama_Groups').': '.$data->Mosama_Groups;

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
            <td scope="col" class="loHightTd" colspan="1">
                {{$data->headerTitle}}
            </td>
            <td scope="col" class="loHightTd" colspan="1">{{$userinfo->apply_date}}</td>
            <td scope="col" class="loHightTd    " rowspan="3"><img src="{{$base64}}" style="width:25mm;position: absolute;rotate:90deg" class="rotate90"></td>
        </tr>
        <tr>
            <td class="loHightTd"  scope="row" colspan="2">
                {{trans('JOBLANG::Employment_StartAnnonces.homepage_annonce_number')}} -
                <i CLASS="annonceNumber">{{\AmerHelper::ArabicNumbersText($annonceNumber ?? '00')}}</i>
                {{trans('JOBLANG::Employment_StartAnnonces.homepage_annonce_foryear')}}
                <SPAN CLASS="annonceYear">{{\AmerHelper::ArabicNumbersText($annonceYear ?? '1900')}}</SPAN>
                @if($annonce->Description !== null)
                -
                <cite title="Source Title" class="annonceDescription">{!! $Description ?? '.....' !!}</cite> -
                @endif
                @if($annonceGovernorates !== null)
                <i CLASS="annonceGovernorates">{{$annonceGovernorates ?? ' ---- '}}</i>
                @endif

            </td>
        </tr>
    </thead>
    <tbody>
        <!-- setJob -->
        <tr>
            <td class="loHightTd w30 scopeRow">
                {{trans('EMPLANG::Mosama_JobTitles.singular')}}
            </td>
            <td class="loHightTd w90 fullright border_dashed">
                {!! setJob($job) !!}

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
            <td class="loHightTd w65 fullright border_dashed">
                <?php
                ?>
                {!! \AmerHelper::ArabicNumbersText(implode('<br>',$job->Mosama_JobNames->Mosama_Experiences)) !!}
            </td>
            <td class="loHightTd w35 scopeRow">
                {{trans('JOBLANG::Employment_jobs.CityBornLive')}}
                <br>
                <i class="info">{{trans('JOBLANG::apply.chooseone')}}</i>
            </td>
            <td class="loHightTd w60 fullright border_dashed">
                {!! implode(', ',$job->Cities) !!}
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
            <td class="loHightTd w65 fullright border_dashed">
                {!! implode(' - ',$job->Employment_Ama) !!}
            </td>
            <td class="loHightTd w35 scopeRow">
                {{trans('JOBLANG::Employment_Army.Employment_Army')}}
                <br>
                <i class="info">{{trans('JOBLANG::apply.chooseone')}}</i>
            </td>
            <td class="loHightTd w60 fullright border_dashed">
                {!! implode(', ',$job->Employment_Army) !!}
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
            <td class="loHightTd w65 fullright border_dashed">
                {!! implode(' - ',$job->Employment_Health) !!}
            </td>
            <td class="loHightTd w35 scopeRow">
                {{trans('JOBLANG::Employment_MaritalStatus.Employment_MaritalStatus')}}
                <br>
                <i class="info">{{trans('JOBLANG::apply.chooseone')}}</i>
            </td>
            <td class="loHightTd w60 fullright border_dashed">
                {!! implode(', ',$job->Employment_MaritalStatus) !!}
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
            <td class="loHightTd w65 fullright border_dashed">
                {!! setList($job->Mosama_Educations) !!}
            </td>
            <td class="loHightTd w35 border_dashed @if($driver !== 0) scopeRow @endif">
            @if($driver !== 0)
                {{trans('JOBLANG::Employment_Drivers.Employment_Drivers')}}
                <br>
                <i class="info">{{trans('JOBLANG::apply.chooseone')}}</i>
                @endif
            </td>
            <td class="loHightTd w60 fullright border_dashed">
            @if($driver !== 0)
                {!! setList($job->Employment_Drivers) !!}
                @endif
            </td>

        </tr>
        <!-- Mosama_Educations -->
        <!-- Employment_Qualifications -->
        <tr>
            <td class="loHightTd w30 scopeRow">
                {{trans('JOBLANG::Employment_Qualifications.Employment_Qualifications')}}
                <br>
                <i class="info">{{trans('JOBLANG::apply.chooseall')}}</i>
            </td>
            <td class="loHightTd w160 fullright border_dashed">
                {!! setList($job->Employment_Qualifications) !!}
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
                {!! setList($job->Employment_IncludedFiles) !!}
            </td>
        </tr>
        <!-- Employment_IncludedFiles -->
        <tr>
            <td class="loHightTd w30 scopeRow">{{ trans('JOBLANG::Employment_People.FULLname')}}</td>
            <td class="loHightTd w65 fullright border_dashed">{{$userinfo->fullname}}</td>
            <td class="loHightTd w35 scopeRow">{{ trans('JOBLANG::Employment_People.NID')}}</td>
            <td class="loHightTd w60 fullright border_dashed">{{\AmerHelper::ArabicNumbersText($userinfo->NID)}}</td>
        </tr>
        <?php
        if(is_object($userinfo->age)){
            $userage=[];
            $userage[]="(".$userinfo->age->ageyears.')'.trans('JOBLANG::Employment_People.Age.AgeYears');
            $userage[]="(".$userinfo->age->agemonths.')'.trans('JOBLANG::Employment_People.Age.AgeMonths');
            $userage[]="(".$userinfo->age->agedays.')'.trans('JOBLANG::Employment_People.Age.AgeDays');
            $userage=\AmerHelper::ArabicNumbersText(implode(' - ',$userage));
        }else{
            $userage=\AmerHelper::ArabicNumbersText($userinfo->age);
        }
        ?>
        <tr>
            <td class="loHightTd w30 scopeRow">{{ trans('JOBLANG::Employment_People.Sex.Sex')}}</td>
            <td class="loHightTd w65 fullright border_dashed">{{$userinfo->Sex}}</td>
            <td class="loHightTd w35 scopeRow">{{ trans('JOBLANG::Employment_People.Age.Age')}}</td>
            <td class="loHightTd w60 fullright border_dashed">{{$userage}}</td>
        </tr>
        <?php
        if(is_object($userinfo->birth_place)){
            $birth_place=implode(' - ',[$userinfo->birth_place->BornGov,$userinfo->birth_place->BornCity]);
        }
        if(is_object($userinfo->live_place)){
            $live_place=\AmerHelper::ArabicNumbersText(implode(' - ',[$userinfo->live_place->LiveGov,$userinfo->live_place->LiveCity,$userinfo->live_place->liveaddress]));
        }
        ?>
        <tr>
            <td class="loHightTd w30 scopeRow">{{ trans('JOBLANG::Employment_People.bornPlace.bornPlace')}}</td>
            <td class="loHightTd w65 fullright border_dashed">{{$birth_place}}</td>
            <td class="loHightTd w35 scopeRow">{{ trans('JOBLANG::Employment_People.LivePlace.LivePlace')}}</td>
            <td class="loHightTd w60 fullright border_dashed">{{$live_place}}</td>
        </tr>
        <tr>
            <td class="loHightTd w30 scopeRow" rowspan="2">{{ trans('JOBLANG::Employment_People.Connection.Connection')}}</td>
            <td class="loHightTd w30 scopeRow">{{ trans('JOBLANG::Employment_People.Connection.LandLine')}}</td>
            <td class="loHightTd w35 fullright border_dashed">{{\AmerHelper::ArabicNumbersText($userinfo->ConnectLandline)}}</td>
            <td class="loHightTd w35 scopeRow">{{ trans('JOBLANG::Employment_People.Connection.Mobile')}}</td>
            <td class="loHightTd w60 fullright border_dashed">{{\AmerHelper::ArabicNumbersText($userinfo->ConnectMobile)}}</td>
        </tr>
        <tr>

            <td class="loHightTd w30 scopeRow">{{ trans('JOBLANG::Employment_People.Connection.Email')}}</td>
            <td class="loHightTd w130 fullright border_dashed">{{$userinfo->ConnectEmail}}</td>
        </tr>
        <tr>
            <td class="loHightTd w30 scopeRow">{{ trans('JOBLANG::Employment_Health.Employment_Health')}}</td>
            <td class="loHightTd w65 fullright border_dashed">{{$userinfo->Health_id}}</td>
            <td class="loHightTd w35 scopeRow">{{ trans('JOBLANG::Employment_MaritalStatus.Employment_MaritalStatus')}}</td>
            <td class="loHightTd w60 fullright border_dashed">{{$userinfo->MaritalStatus_id}}</td>
        </tr>
        <tr>
            <td class="loHightTd w30 scopeRow">{{ trans('JOBLANG::Employment_Army.Employment_Army')}}</td>
            <td class="loHightTd w65 fullright border_dashed">{{$userinfo->Employment_Army}}</td>
            <td class="loHightTd w35 scopeRow">{{ trans('JOBLANG::Employment_Ama.Employment_Ama')}}</td>
            <td class="loHightTd w60 fullright border_dashed">{{$userinfo->Employment_Ama}}</td>
        </tr>
        <tr>
            <td class="loHightTd w30 scopeRow">{{ trans('JOBLANG::Employment_People.Mosama_Educations.Mosama_Educations')}}</td>
            <td class="loHightTd w65 fullright border_dashed">{{$userinfo->Education_id}}</td>
            <td class="loHightTd w35 scopeRow">{{ trans('JOBLANG::Employment_People.Mosama_Educations.year')}}</td>
            <td class="loHightTd w60 fullright border_dashed">{{\AmerHelper::ArabicNumbersText($userinfo->EducationYear)}}</td>
        </tr>
        @if($driver !== 0)
        <tr>
            <td class="loHightTd w30 scopeRow">{{ trans("JOBLANG::Employment_People.Employment_Drivers.DriverDegree")}}</td>
            <td class="loHightTd w31 fullright border_dashed">{{$userinfo->DriverDegree}}</td>
            <td class="loHightTd w31 border_left border_dashed">{{ trans("JOBLANG::Employment_People.Employment_Drivers.DriverStart")}}</td>
            <td class="loHightTd w31 fullright border_dashed">{{$userinfo->DriverStart}}</td>
            <td class="loHightTd w31 border_left border_dashed">{{ trans("JOBLANG::Employment_People.Employment_Drivers.DriverEnd")}}</td>
            <td class="loHightTd w35 fullright border_dashed">{{$userinfo->DriverEnd}}</td>
        </tr>
        @endif
        <tr>
            <td class="loHightTd w30 scopeRow">{{ trans('JOBLANG::Employment_People.Khebra.years')}}</td>
            <td class="loHightTd w65 fullright border_dashed">{{$userinfo->Khebra_type}} - {{\AmerHelper::ArabicNumbersText($userinfo->Khebra)}}</td>
            <td class="loHightTd w35 scopeRow">{{ trans('JOBLANG::Employment_people.Tamin.Tamin')}}</td>
            <td class="loHightTd w60 fullright border_dashed">{{\AmerHelper::ArabicNumbersText($userinfo->Tamin)}}</td>
        </tr>
        <tr>
            <td class="loHightTd w30 scopeRow">{{ trans('JOBLANG::Employment_IncludedFiles.Employment_IncludedFiles')}}</td>
            <td class="loHightTd w160 fullright border_dashed">{{$userinfo->uploades}}
            </td>
        </tr>

        <?php
         //dd($userinfo);
        ?>
    </tbody>
</table>
