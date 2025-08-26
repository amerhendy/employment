<?php
/**********************************
 * $data array
 * keys:
 * **** tablewidth integer
 * **** job Object  Keys:
 * **********************newData Object
 * ******************************************   uid,apply_date,actiontype,fullname,NID,BirthDate,Sex,age,birth_place,live_place,ConnectLandline,ConnectMobile,ConnectEmail,Health_id,MaritalStatus_id,Employment_Army,Employment_Ama,Education_id,EducationYear,accept_driver,DriverEnd,DriverStart,DriverDegree,Khebra,Tamin,uploades
 * **********************OldData Object
 * */
 
$tableWidth=$data['tablewidth'];
$data=$data['job'];
/*********
 * $data Object Keys
 * *****    newData Object
 * *****    OldData Object
 * *****    QR
 * *****    headerTitle
 * *****    headerTitleNote
 * *****    job Object
 * */
$newData=$data->newData;
$oldData=$data->OldData;
$Qr=$data->QR;
$headerTitle=$data->headerTitle;
$headerTitleNote=$data->headerTitleNote;

$apply_date=$newData->apply_date;
//الوظيفة الجديدة
$newJob=$data->job;
$annonce=$newJob->Employment_StartAnnonces;
//dd($data);
//->job->Employment_StartAnnonces->Employment_Stages['text']
$applyText=$oldData->Stage_id->apply->Text;
$completeText=$annonce->Employment_Stages['text'];

$annonceNumber=$annonce->Number;
$annonceYear=$annonce->Year;
$showOldDriver=$oldData->job_id->Driver;
$showNewDriver=$newData->accept_driver;

//if($newJob->Employment_Drivers == 'null' || $newJob->Employment_Drivers == '' || $newJob->Employment_Drivers=null || (is_array($newJob->Employment_Drivers) && count($newJob->Employment_Drivers) == 0)){$driver=0;}else{$driver=$data->Employment_Drivers;}

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
$annonceName=(string) \Str::of(trans('JOBLANG::Employment_StartAnnonces.fullAnnonceName'))->replaceArray('?', [$annonceNumber ?? '00',$annonceYear ?? '1900']);
//dd($newData->fullname);
?>
<table>
    <thead>
        <tr>
            <td class="loHightTd">
                {{$headerTitle}}
            </td>
            <td>{{$apply_date}}</td>
            <td>
                <img src="{{ $Qr }}" width="150" height="150" alt="QR Code">
            </td>
        </tr>
        <tr>
            <td class="loHightTd">
                {{$annonceName}}
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
                {!! setJob($newJob) !!}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {!! setJob($oldData->job_id) !!}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                    {{ trans('JOBLANG::Employment_People.FULLname')}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$oldData->FullName}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$newData->fullname ?? ''}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                    {{ trans('JOBLANG::Employment_People.NID')}}
            </td>
            <td class="loHightTd w160 fullright wborder">
                {{$oldData->NID}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                    {{ trans('JOBLANG::Employment_People.Sex.Sex')}}
            </td>
            <td class="loHightTd w160 fullright wborder">
                {{$oldData->Sex}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                    {{ trans('JOBLANG::Employment_People.Age.Age')}}
            </td>
            <td class="loHightTd w160 fullright wborder">
                {{implode("/",[$oldData->AgeYears,$oldData->AgeMonths,$oldData->AgeDays])}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                    {{ trans('JOBLANG::Employment_People.bornPlace.bornPlace')}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$oldData->BornPlace}}
            </td>
            
            <td class="loHightTd w80 fullright wborder">
                {{is_Object($newData->birth_place) ? $newData->birth_place->BornGov." - ". $newData->birth_place->BornCity:'ddd'}}
            </td>
        </TR>
        
        <TR>
            <td class="loHightTd w30 wborder">
                    {{ trans('JOBLANG::Employment_People.LivePlace.LivePlace')}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$oldData->LivePlace}}
            </td>
            
            <td class="loHightTd w80 fullright wborder">
                {{is_Object($newData->live_place) ? $newData->live_place->LiveGov." - ". $newData->live_place->LiveCity." - ". $newData->live_place->liveaddress:'ddd'}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                {{ trans('JOBLANG::Employment_People.Connection.Connection')}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{ trans('JOBLANG::Employment_People.Connection.LandLine')}}:{{$oldData->Connection->Landline}}<br>
                {{ trans('JOBLANG::Employment_People.Connection.Mobile')}}:{{$oldData->Connection->Mobile}}<br>
                {{ trans('JOBLANG::Employment_People.Connection.Email')}}:{{$oldData->Connection->Email}}
            </td>
            
            <td class="loHightTd w80 fullright wborder">
                {{ trans('JOBLANG::Employment_People.Connection.LandLine')}}:{{$newData->ConnectLandline}}<br>
                {{ trans('JOBLANG::Employment_People.Connection.Mobile')}}:{{$newData->ConnectMobile}}<br>
                {{ trans('JOBLANG::Employment_People.Connection.Email')}}:{{$newData->ConnectEmail}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                {{ trans('JOBLANG::Employment_Health.Employment_Health')}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$oldData->Health_id}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$newData->Health_id}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                {{ trans('JOBLANG::Employment_MaritalStatus.Employment_MaritalStatus')}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$oldData->MaritalStatus_id}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$newData->MaritalStatus_id}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                {{ trans('JOBLANG::Employment_Army.Employment_Army')}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$oldData->Arm_id}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$newData->Employment_Army}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                {{ trans('JOBLANG::Employment_Ama.Employment_Ama')}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$oldData->Ama_id}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$newData->Employment_Ama}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                {{ trans('JOBLANG::Employment_People.Mosama_Educations.Mosama_Educations')}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$oldData->Education_id}}<br>{{ trans('JOBLANG::Employment_People.Mosama_Educations.year')}}: {{$oldData->EducationYear}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$newData->Education_id}}<br>{{ trans('JOBLANG::Employment_People.Mosama_Educations.year')}}: {{$newData->EducationYear}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                {{ trans("JOBLANG::Employment_People.Employment_Drivers.DriverDegree")}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                @if($showOldDriver) 
                {{ trans("JOBLANG::Employment_People.Employment_Drivers.DriverDegree")}}:{{$oldData->DriverDegree}}<br>
                {{ trans("JOBLANG::Employment_People.Employment_Drivers.DriverStart")}}:{{$oldData->DriverStart}}<br>
                {{ trans("JOBLANG::Employment_People.Employment_Drivers.DriverEnd")}}:{{$oldData->DriverEnd}}
                @endif
            </td>
            <td class="loHightTd w80 fullright wborder">
                @if($showNewDriver)
                {{ trans("JOBLANG::Employment_People.Employment_Drivers.DriverDegree")}}:{{$newData->DriverDegree}}<br>
                {{ trans("JOBLANG::Employment_People.Employment_Drivers.DriverStart")}}:{{$newData->DriverStart}}<br>
                {{ trans("JOBLANG::Employment_People.Employment_Drivers.DriverEnd")}}:{{$newData->DriverEnd}}
                @endif

            </td>
        </TR>
        
        <TR>
            <td class="loHightTd w30 wborder">
                {{trans('EMPLANG::Mosama_Experiences.Mosama_Experiences')}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$oldData->Khebra}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{ $newData->Khebra }}
            </td>
        </TR>
        
        <TR>
            <td class="loHightTd w30 wborder">
                {{ trans('JOBLANG::Employment_people.Tamin.Tamin')}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$oldData->Tamin}}
            </td>
            <td class="loHightTd w80 fullright wborder">
                {{$newData->Tamin}}
            </td>
        </TR>
        <TR>
            <td class="loHightTd w30 wborder">
                {{ trans('JOBLANG::Employment_IncludedFiles.Employment_IncludedFiles')}}
            </td>
            <td class="loHightTd w80 fullright wborder"></td>
            <td class="loHightTd w80 fullright wborder">
                {{$newData->uploades}}
            </td>
        </TR>
        
    </tbody>
</table>