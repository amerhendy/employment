<?php
$person=$value;
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
if(isset($person['Degrees'])){
        if($person['Degrees'] == null){
                $collectDegreesEditorial=floatval(0);$collectDegreesPractical=floatval(0);$collectDegreesInterview=floatval(0);
                $writeDegreesEditorial='.....';$writeDegreesPractical='....';$writeDegreesInterview='....';
        }else{
                if($person['Degrees']['Editorial'] == null){$writeDegreesEditorial='.....';$collectDegreesEditorial=floatval(0);}else{$writeDegreesEditorial=$collectDegreesEditorial=floatval($person['Degrees']['Editorial']);}
                if($person['Degrees']['Practical'] == null){$writeDegreesPractical='....';$collectDegreesPractical=floatval(0);}else{$writeDegreesPractical=$collectDegreesPractical=floatval($person['Degrees']['Practical']);}
                if($person['Degrees']['Interview'] == null){$writeDegreesInterview='....';$collectDegreesInterview=floatval(0);}else{$writeDegreesInterview=$collectDegreesInterview=floatval($person['Degrees']['Interview']);}
        }
        $totalDegrees=$collectDegreesEditorial + $collectDegreesPractical + $collectDegreesInterview;
}
?>
<table>
        <tr>
            <th  class="border_left loHightTd w30">{{trans('JOBLANG::Employment_StartAnnonces.plural')}}</th>
            <td class="loHightTd border_dashed fullright" style="width:160mm">{{trans('JOBLANG::Employment_StartAnnonces.homepage_annonce_number')}} ({{$annonceNumber ?? 'annonceNumber'}}) {{trans('JOBLANG::Employment_StartAnnonces.homepage_annonce_foryear')}} {{$annonceYear ?? 'annonceYear'}}</td>
    </tr>
    <tr>
            <th  class="border_left loHightTd w30">{{trans('JOBLANG::Employment_Jobs.plural')}}</th>
            <td class="border_dashed  loHightTd fullright" style="width:160mm">{{$job ?? 'job'}}</td>
    </tr>
    <tr>
            <th  class="border_left loHightTd w30">{{trans('JOBLANG::Employment_People.uid')}}</th>
            <td class="border_dashed  loHightTd w65">{{$uid ?? 'uid'}}</td>
            <th  class="loHightTd w30">{{trans('JOBLANG::Employment_People.NID')}}</th>
            <td class="border_dashed  loHightTd w65">{{$NID ?? 'NID'}}</td>
    </tr>
    <tr>
            <th  class="border_left loHightTd w30">{{trans('JOBLANG::Employment_People.FULLname')}}</th>
            <td class="border_dashed  loHightTd fullright" style="width:160mm">{{$fullName}}</td>
    </tr>
    <tr>
            <th class="border_left loHightTd w30">{{trans('JOBLANG::Employment_People.Connection.Connection')}}</th>
            <td class="border_dashed loHightTd w53"><img src="{{$svg.'email-svgrepo-com.svg'}}" style="width:4mm;">{{$Face['ConnectEmail']}}</td>
            <td class="border_dashed loHightTd w53"><img src="{{$svg.'phone-book-svgrepo-com.svg'}}" style="width:4mm;">{{\AmerHelper::ArabicNumbersText($Face['ConnectLandline'])}}</td>
            <td class="border_dashed loHightTd w53"><img src="{{$svg.'smartphone-svgrepo-com.svg'}}" style="width:4mm;">{{\AmerHelper::ArabicNumbersText($Face['ConnectMobile'])}}</td>
    </tr>
    <tr>
            <th  class="border_left loHightTd w30">{{trans('JOBLANG::Employment_Health.Employment_Health')}}</th>
            <td class="border_dashed  loHightTd w160">{{$Face['Health_id']}}</td>
    </tr>
    <tr>
            <th class="border_left loHightTd w30" rowspan="4">{{trans('JOBLANG::Employment_Reports.lastStage.lastStage')}}</th>
            <th  class="border_bottom loHightTd w30">{{trans('JOBLANG::Employment_Reports.lastStage.Name')}}</th>
            <td class="border_dashed loHightTd w130">{{$Face['lastStage']['Text']}}</td>
    </tr>
    <tr><th  class="border_bottom loHightTd w30">{{trans('JOBLANG::Employment_Reports.lastStage.Result')}}</th><td class="border_dashed loHightTd w130">{{$Face['lastStage']['Result']}}</td></tr>
    <tr><th  class="border_bottom loHightTd w30">{{trans('JOBLANG::Employment_Reports.lastStage.Message')}}</th><td class="border_dashed loHightTd w130">{!! \AmerHelper::ArabicNumbersText($Face['lastStage']['Message']) !!}</td></tr>
    <tr><th  class="border_bottom loHightTd w30">{{trans('JOBLANG::Employment_Reports.lastStage.Date')}}</th><td class="border_dashed loHightTd w130">{{\AmerHelper::ArabicNumbersText($Face['lastStage']['created_at'])}}</td></tr>
        @if(isset($person['Degrees']))
        <?php 
                //dd($person['Degrees']); 
        ?>
        <tr>
                <th class="border_left loHightTd w30" rowspan="4">{{trans('JOBLANG::Employment_Reports.printForm.actions.Degrees')}}</th>
                <th  class="border_bottom loHightTd w30">{{trans('JOBLANG::Employment_Reports.UpToDateForm.DegreeTahriry')}}</th>
                <td class="border_dashed loHightTd w130">{!! \AmerHelper::ArabicNumbersText(' '.$writeDegreesEditorial) !!}</td>
        </tr>
        <tr><th  class="border_bottom loHightTd w30">{{trans('JOBLANG::Employment_Reports.UpToDateForm.DegreeAmaly')}}</th><td class="border_dashed loHightTd w130">{!! \AmerHelper::ArabicNumbersText(' '.$writeDegreesPractical) !!}</td></tr>
        <tr><th  class="border_bottom loHightTd w30">{{trans('JOBLANG::Employment_Reports.UpToDateForm.DegreeMeeting')}}</th><td class="border_dashed loHightTd w130">{!! \AmerHelper::ArabicNumbersText(' '.$writeDegreesInterview) !!}</td></tr>
        <tr><th  class="border_bottom loHightTd w30">{{trans('JOBLANG::Employment_Reports.UpToDateForm.TotalDegrees')}}</th><td class="border_dashed loHightTd w130">{!! \AmerHelper::ArabicNumbersText(' '.$totalDegrees) !!}</td></tr>
        @endif
        @if(isset($person['Downloads']))
        <?php 
                $downloadLinks=$person['Downloads'];
                $count=count($downloadLinks);
                $links=[];
                for($i=0;$i<$count;$i++){
                        $links[]=$i.':<a href="'.$downloadLinks[$i].'" target="_blank">'.$downloadLinks[$i].'</a>';
                }
         ?>
                @if(count($downloadLinks) !== 0)
                <tr>
                        <th class="border_left loHightTd w30">{{trans('JOBLANG::Employment_Reports.printForm.actions.Downloads')}}</th>
                        <td class="border_dashed loHightTd w160">
                                {!!implode('<br>',$links)!!}
                        </td>
                </tr>
                @endif
        @endif

        @if(isset($person['Grievance']))
        <?php
        $grReq=[['GrievanceApply','apply'],['GrievanceEditorial','Editorial'],['GrievancePractical','Practical']];
        foreach ($grReq as $key => $value) {
                if(
                        (in_array($value[0],$config['request']['actions'])) &&
                        (array_key_exists($value[1],$person['Grievance'])) &&
                        (($person['Grievance'][$value[1]] !== null) && (count($person['Grievance'][$value[1]]) !== 0))

                ){
                        $grReq[$key][]=$person['Grievance'][$value[1]];
                }
        }
        ?>
        @foreach($grReq as $v)
        <?php
        if($v[1] == 'apply'){
                $lang='AppliedGrievance';
            }else if($v[1] == 'Editorial'){
                $lang='WritingGrievance';
            }else if($v[1] == 'Practical'){
                $lang='PracticalGrievance';
            }
        ?>
                @if(count($v) == 3)
                <tr>
                        <?php 
                        
                                $obj=$v[2][array_keys($v[2])[0]];
                                //dd($v);
                        ?>
                        <th class="border_left loHightTd w30">{{trans('JOBLANG::Employment_Grievance.'.$lang)}}</th>
                        <td class="border_dashed loHightTd w160">{{trans('JOBLANG::Employment_Grievance.GrievanceDone')}} - {{$obj['created_at']}}</td>
                </tr>
                @else
                <tr>
                        <th class="border_left loHightTd w30">{{trans('JOBLANG::Employment_Grievance.'.$lang)}}</th>
                        <td class="border_dashed loHightTd w160">{{trans('JOBLANG::Employment_Grievance.GrievanceNotDone')}}</td>
                </tr>
                @endif
        @endforeach
    <tr>
            <th class="border_left loHightTd w30">{{trans('JOBLANG::Employment_People.Connection.Connection')}}</th>
            <td class="border_dashed loHightTd w53"><img src="{{$svg.'email-svgrepo-com.svg'}}" style="width:4mm;">{{$Face['ConnectEmail']}}</td>
            <td class="border_dashed loHightTd w53"><img src="{{$svg.'phone-book-svgrepo-com.svg'}}" style="width:4mm;">{{\AmerHelper::ArabicNumbersText($Face['ConnectLandline'])}}</td>
            <td class="border_dashed loHightTd w53"><img src="{{$svg.'smartphone-svgrepo-com.svg'}}" style="width:4mm;">{{\AmerHelper::ArabicNumbersText($Face['ConnectMobile'])}}</td>
    </tr>
                @endif
        
</table>
