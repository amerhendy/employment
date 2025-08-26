<?php
$person=$value;
//dd($value);
$Title=trans('JOBLANG::Employment_Reports.printForm.actions.'.$config['page']);
$Face=$person['Face'];
$annonceNumber=\AmerHelper::ArabicNumbersText($Face['Annonce_id']['Number']);
$annonceYear=\AmerHelper::ArabicNumbersText($Face['Annonce_id']['Year']);
$job=\AmerHelper::ArabicNumbersText($Face['Job_id']['Code'])."::".$Face['Job_id']['Mosama_JobNames'];
$uid=\AmerHelper::ArabicNumbersText($Face['id']);
$NID=\AmerHelper::ArabicNumbersText($Face['NID']);
$fullName=\AmerHelper::ArabicNumbersText($Face['FullName']);
$public_path=realpath(config('Amer.amer.public_path' ?? public_path()));
$svg=$public_path.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'svg'.DIRECTORY_SEPARATOR;
if(is_array($Face['lastStage']['Message'])){
    $Face['lastStage']['Message']=join($Face['lastStage']['Message']);
}
if($config['page'] == 'CheckApplyData'){
    $myData=$person['Applydata'];
    $personId=\AmerHelper::ArabicNumbersText(' '.$myData['id']);
    $personNid=\AmerHelper::ArabicNumbersText($myData['NID']);
    $stageNameTrans=trans('JOBLANG::Employment_Reports.ApplyResult');
    $Message=$myData['Stage_id']['Message'];
    $Result=$myData['Stage_id']['Result'];
    $created_at=\AmerHelper::ArabicNumbersText($myData['Stage_id']['created_at']);
    $Stage_id=$myData['Stage_id']['Text'];
}
if($config['page'] == 'LastEntry'){
    $myData=$person['LastEntry'];
    $myData['Stage_id']=$person['StageList']['LastEntry'];
    $personId=\AmerHelper::ArabicNumbersText($person['Face']['id']);
    $personNid=\AmerHelper::ArabicNumbersText($person['Face']['NID']);
    $stageNameTrans=trans('JOBLANG::Employment_Reports.ApplyResult');
    $Message=$myData['Stage_id']['Message'];
    $Result=$myData['Stage_id']['Result'];
    $created_at=\AmerHelper::ArabicNumbersText($myData['Stage_id']['created_at']);
    $Stage_id=$myData['Stage_id']['Text'];
}
$Mosama_JobNames=$myData['Job_id']['Mosama_JobNames'];
$JobCode=\AmerHelper::ArabicNumbersText(' '.$myData['Job_id']['Code']);
$FullName=implode(' ',[$myData['Fname'],$myData['Sname'],$myData['Tname'],$myData['Lname']]);
$Sex=$myData['Sex'];
$AgeYears=\AmerHelper::ArabicNumbersText(' '.$myData['AgeYears']);
$AgeMonths=\AmerHelper::ArabicNumbersText(' '.$myData['AgeMonths']);
$AgeDays=\AmerHelper::ArabicNumbersText(' '.$myData['AgeDays']);
$BirthDate=\AmerHelper::ArabicNumbersText(' '.$myData['BirthDate']);
$bornPlace=implode(' - ',[$myData['BornGov'],$myData['BornCity']]);
$LivePlace=\AmerHelper::ArabicNumbersText(implode(' - ',[$myData['LiveGov'] , $myData['LiveCity'] ,$myData['LiveAddress']]));
$ConnectMobile=\AmerHelper::ArabicNumbersText($myData['ConnectMobile']);
$ConnectLandline=\AmerHelper::ArabicNumbersText($myData['ConnectLandline']);
$ConnectEmail=\AmerHelper::ArabicNumbersText($myData['ConnectEmail']);
$Employment_Health=$myData['Health_id'];
$Employment_MaritalStatus=$myData['MaritalStatus_id'];
$Employment_Army=$myData['Arm_id'];
$Employment_Ama=$myData['Ama_id'];
$Mosama_Educations=$myData['Education_id'];
$EducationYear=\AmerHelper::ArabicNumbersText($myData['EducationYear']);
$Tamin=\AmerHelper::ArabicNumbersText($myData['Tamin']);
$Khebra=$myData['Khebra'];
if(is_array($Khebra)){
    $Khebra=\AmerHelper::ArabicNumbersText(implode("-",$Khebra));
}
$DriverDegree=$myData['DriverDegree'];
$DriverEnd=\AmerHelper::ArabicNumbersText($myData['DriverEnd']);
$DriverStart=\AmerHelper::ArabicNumbersText($myData['DriverStart']);
if(is_array($Message)){$Message=\AmerHelper::ArabicNumbersText(implode(' ',$Message));}
if(is_array($person['StageList']['LastEntry']['Message'])){$person['StageList']['LastEntry']['Message']=\AmerHelper::ArabicNumbersText(implode(' - ',$person['StageList']['LastEntry']['Message']));}
if(is_array($myData['Stage_id']['Message'])){$myData['Stage_id']['Message']=\AmerHelper::ArabicNumbersText(implode(' - ',$myData['Stage_id']['Message']));}

?>
<table>
    <thad>
            <tr>
                <td style="width:190mm;" class="text-center">
                    <h3>{{$Title}}</h3>
                </td>
            </tr>
    </thad>
    <tbody>
    <tr>
                <th class="border_left loHightTd w30">{{trans('JOBLANG::Employment_People.uid')}}</th>
                <td class="border_dashed  loHightTd w65">{{$personId}}</td>
                <th class="loHightTd border_right border_left w30">{{trans('JOBLANG::Employment_People.NID')}}</th>
                <td class="border_dashed  loHightTd w65">{{$personNid}}</td>
            </tr>
        <tr>
                <th class="border_left loHightTd w30">{{trans('JOBLANG::Employment_StartAnnonces.plural')}}</th>
                <td class="border_dashed  loHightTd w65 fullright">{{trans('JOBLANG::Employment_StartAnnonces.homepage_annonce_number')}} ({{$annonceNumber}}) {{trans('JOBLANG::Employment_StartAnnonces.homepage_annonce_foryear')}} {{$annonceYear}}</td>
                <th class="border_left border_right loHightTd w30">{{trans('JOBLANG::Employment_Jobs.plural')}}</th>
                <td class="border_dashed fullright loHightTd w65">{{$Mosama_JobNames}} - {{trans('JOBLANG::Employment_Jobs.Code')}} ({{$JobCode}})</td>
        </tr>
        <tr>
                <th class="border_left loHightTd w30">{{trans('JOBLANG::Employment_People.FULLname')}}</th>
                <td class="border_dashed  loHightTd w65">{{$FullName}}</td>
                <th class="border_left border_right loHightTd w30">{{trans('JOBLANG::Employment_People.Sex.Sex')}}</th>
                <td class="border_dashed  loHightTd w65">{{$Sex}}</td>
            </tr>
            <tr>
                <th class="border_left loHightTd w30">{{trans('JOBLANG::Employment_People.Age.Age')}}</th>
                <td class="border_dashed  loHightTd w65">{{$AgeYears}} {{trans('JOBLANG::Employment_People.Age.AgeYears')}} - {{$AgeMonths}} {{trans('JOBLANG::Employment_People.Age.AgeMonths')}} - {{$AgeDays}} {{trans('JOBLANG::Employment_People.Age.AgeDays')}}</td>
                <th class="border_left border_right loHightTd w30">{{trans('JOBLANG::Employment_People.BirthDate')}}</th>
                <td class="border_dashed  loHightTd w65">{{$BirthDate}}</td>
            </tr>
            <tr>
                <th class="border_left loHightTd w30">{{trans('JOBLANG::Employment_People.bornPlace.bornPlace')}}</th>
                <td class="border_dashed  loHightTd w65">{{$bornPlace}}</td>
                <th class="border_left loHightTd w30">{{trans('JOBLANG::Employment_People.LivePlace.LivePlace')}}</th>
                <td class="border_dashed  loHightTd w65">{{$LivePlace}}</td>
            </tr>
            
    <tr>
            <th class="border_left loHightTd w30">{{trans('JOBLANG::Employment_People.Connection.Connection')}}</th>
            <td class="border_dashed loHightTd w53"><img src="{{$svg.'email-svgrepo-com.svg'}}" style="width:4mm;">{{$ConnectEmail}}</td>
            <td class="border_dashed loHightTd w53"><img src="{{$svg.'phone-book-svgrepo-com.svg'}}" style="width:4mm;">{{$ConnectLandline}}</td>
            <td class="border_dashed loHightTd w53"><img src="{{$svg.'smartphone-svgrepo-com.svg'}}" style="width:4mm;">{{$ConnectMobile}}</td>
    </tr>

    <tr>
            <th class="border_left loHightTd w30">{{trans('JOBLANG::Employment_Health.Employment_Health')}}</th>
            <td class="border_dashed  loHightTd w65">{{$Employment_Health}}</td>
            <th class="border_left loHightTd w30">{{trans('JOBLANG::Employment_MaritalStatus.Employment_MaritalStatus')}}</th>
            <td class="border_dashed  loHightTd w65">{{$Employment_MaritalStatus}}</td>
        </tr>
    <tr>
        <th class="border_left loHightTd w30">{{trans('JOBLANG::Employment_Army.Employment_Army')}}</th>
        <td class="border_dashed  loHightTd w65">{{$Employment_Army}}</td>
        <th class="border_left loHightTd w30">{{trans('JOBLANG::Employment_Ama.Employment_Ama')}}</th>
        <td class="border_dashed  loHightTd w65">{{$Employment_Ama}}</td>
    </tr>
    <tr>
        <th class="border_left loHightTd w30">{{trans('JOBLANG::Employment_People.Mosama_Educations.Mosama_Educations')}}</th>
        <td class="border_dashed  loHightTd w65">{{$Mosama_Educations}}</td>
        <th class="border_left loHightTd w30">{{trans('JOBLANG::Employment_People.Mosama_Educations.year')}}</th>
        <td class="border_dashed  loHightTd w65">{{$EducationYear}}</td>
    </tr>
    <tr>
        <th class="border_left loHightTd w30">{{trans('JOBLANG::Employment_People.Khebra.Khebra')}}</th>
        <td class="border_dashed  loHightTd w65">{{$Khebra}}</td>
        <th class="border_left loHightTd w30">{{trans('JOBLANG::Employment_People.Tamin.Tamin')}}</th>
        <td class="border_dashed  loHightTd w65">{{$Tamin}}</td>
    </tr>
    @if($DriverDegree !== null)
        <tr>
        <th class="border_left loHightTd w30">{{trans('JOBLANG::Employment_People.Employment_Drivers.DriverDegree')}}</th>
        <td class="border_left loHightTd w30">{{$DriverDegree}}</td>
        <th class="border_left loHightTd w30">{{trans('JOBLANG::Employment_People.Employment_Drivers.DriverStart')}}</th>
        <td class="border_left loHightTd w30">{{$DriverStart}}</td>
        <th class="border_left loHightTd w30">{{trans('JOBLANG::Employment_People.Employment_Drivers.DriverEnd')}}</th>
        <td class="border_left loHightTd w30">{{$DriverEnd}}</td>
    </tr>
    @endif
    @if($config['page'] == 'CheckApplyData')
        <tr>
            <th class="border_left loHightTd w30" rowspan="4">{{$stageNameTrans}}</th>
            <th  class="border_bottom loHightTd w30">{{trans('JOBLANG::Employment_Reports.lastStage.Name')}}</th>
            <td class="border_dashed loHightTd w130">{{$myData['Stage_id']['Text']}}</td>
            </tr>
            <tr>
                <th  class="border_bottom loHightTd w30">{{trans('JOBLANG::Employment_Reports.lastStage.Result')}}</th>
                <td class="border_dashed loHightTd w130">{{$myData['Stage_id']['Result']}}</td></tr>
        <tr><th  class="border_bottom loHightTd w30">{{trans('JOBLANG::Employment_Reports.lastStage.Message')}}</th><td class="border_dashed loHightTd w130">{!! \AmerHelper::ArabicNumbersText($myData['Stage_id']['Message']) !!}</td></tr>
        <tr><th  class="border_bottom loHightTd w30">{{trans('JOBLANG::Employment_Reports.lastStage.Date')}}</th><td class="border_dashed loHightTd w130">{{\AmerHelper::ArabicNumbersText($myData['Stage_id']['created_at'])}}</td></tr>
    @endif
    @if($config['page'] == 'LastEntry')
    <tr>
    <th class="border_left loHightTd w30" rowspan="4">{{trans('JOBLANG::Employment_Reports.lastStage.Entry')}}</th>
    <th  class="border_bottom loHightTd w30">{{trans('JOBLANG::Employment_Reports.lastStage.Name')}}</th>
    <td class="border_dashed loHightTd w130">{{$person['StageList']['LastEntry']['Text']}}</td>
    </tr>
    <tr>
        <th  class="border_bottom loHightTd w30">{{trans('JOBLANG::Employment_Reports.lastStage.Result')}}</th>
        <td class="border_dashed loHightTd w130">{{$person['StageList']['LastEntry']['Result']}}</td></tr>
        <tr><th  class="border_bottom loHightTd w30">{{trans('JOBLANG::Employment_Reports.lastStage.Message')}}</th><td class="border_dashed loHightTd w130">{!! \AmerHelper::ArabicNumbersText($person['StageList']['LastEntry']['Message']) !!}</td></tr>
    <tr><th  class="border_bottom loHightTd w30">{{trans('JOBLANG::Employment_Reports.lastStage.Date')}}</th><td class="border_dashed loHightTd w130">{{\AmerHelper::ArabicNumbersText($person['StageList']['LastEntry']['created_at'])}}</td></tr>
	@endif






    
    </tbody>
</table>