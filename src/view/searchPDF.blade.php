<h1>{{trans('JOBLANG::apply.Search.searchpage_searchresult')}}</h1>
<?php
$person=$data;
$Face=$person->Face;
$annonceNumber=\AmerHelper::ArabicNumbersText($Face->Annonce_id->Number);
$annonceYear=\AmerHelper::ArabicNumbersText($Face->Annonce_id->Year);
$annonceSlug=$Face->Annonce_id->Slug;
$uid=\AmerHelper::ArabicNumbersText($Face->id);
$NID=\AmerHelper::ArabicNumbersText($Face->NID);
if($person->Employment_PeopleNewData !== null){
    $fullName=$person->Employment_PeopleNewData->FullName;
    $job=\AmerHelper::ArabicNumbersText($person->Employment_PeopleNewData->Job_id->Code)."::".$person->Employment_PeopleNewData->Job_id->Mosama_JobNames;
    $jobslug=$person->Employment_PeopleNewData->Job_id->Slug;
}else{
    $fullName=$person->Face->FullName;
    $job=\AmerHelper::ArabicNumbersText($Face->Job_id->Code)."::".$Face->Job_id->Mosama_JobNames;
    $jobslug=$Face->Job_id->Slug;
}
$fullName=\AmerHelper::ArabicNumbersText($fullName);
//dd($person->stageList->Last);
$lastSatge=$person->stageList->Last;
$lType=$lastSatge->Type;
//dd($lastSatge);
$result=$lastSatge->Result;
$message=$lastSatge->Message;
if(is_array($message)){
    $message=implode('<br>',$message);
}
//dd($data);
//dd($lastSatge);
$instructions='';
if(property_exists($lastSatge,'Statics')){
if($lastSatge->Type !== 'apply'){
        $newStageId=$lastSatge->newStageId;
    }
    $website=\Str::finish(config('app.url'),'/');
    $link=$website. 'employment_operation/stage/' .$annonceSlug. '/' .$jobslug;
        $instructions='<form method="post" action="'.$link.'" id="formInstruction" target="_self" Flags="SubmitForm">
        <input type="hidden"  id="job" name="job" value="'.$jobslug.'" Fields="s ">
        <input type="hidden"  id="annonce" name="annonce" value="'.$annonceSlug.'">
        <input type="hidden"  id="page" name="page" value="search">
        <input type="hidden" name="nid" value="'.$Face->NID.'">
        <input type="hidden" name="lastStage" value="'.$newStageId.'">
        <input type="hidden" name="_token" value="'.csrf_token().'">
        <input type="hidden" name="_method" value="POST">
        <br />
                <input type="submit" name="submit" value="Submit" />
        <br />
    </form>';
    }
    // Submit Button  
$public_path=realpath(config('Amer.amer.public_path' ?? public_path()));
$svg=$public_path.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'svg'.DIRECTORY_SEPARATOR;
if(is_array($Face->lastStage->Message)){
    $Face->lastStage->Message=join($Face->lastStage->Message);
}
?>
<table class="table border">
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
            <th class="border_left loHightTd w30">{{trans('JOBLANG::Employment_Reports.lastStage.lastStage')}}</th>
            <td class="border_dashed  loHightTd fullright" style="width:160mm">{{$lastSatge->Text}}</td>
    </tr>
    @if($lType !== 'Stage')
    <tr>
            <th  class="border_left loHightTd w30">{{trans('JOBLANG::apply.Search.TheResult')}}</th>
            <td class="border_dashed  loHightTd fullright" style="width:160mm">{{$result}}</td>
    </tr>
    @endif
    @if($message !== '')
    <tr>
            <th  class="border_left loHightTd w30">{{trans('JOBLANG::apply.Search.TheMessage')}}</th>
            <td class="border_dashed  loHightTd fullright" style="width:160mm">{!! $message !!}</td>
    </tr>
    @endif
    @if($instructions !== '')
    <tr>
            <th  class="border_left loHightTd w30">{{trans('JOBLANG::apply.Search.Instructions')}}</th>
            <td class="border_dashed  loHightTd fullright" style="width:160mm">{!! $instructions !!}</td>
    </tr>
    @endif
</table>
