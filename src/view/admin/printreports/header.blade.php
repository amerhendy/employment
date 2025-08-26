<?php
$Face=$value['Face'];
$annonceNumber=\AmerHelper::ArabicNumbersText($Face['Annonce_id']['Number']);
$annonceYear=\AmerHelper::ArabicNumbersText($Face['Annonce_id']['Year']);
$job=\AmerHelper::ArabicNumbersText($Face['Job_id']['Code'])."::".$Face['Job_id']['Mosama_JobTitles'];

?>
<table>
    <tr>
        <td  colspan="3">
            {{trans('JOBLANG::Employment_StartAnnonces.homepage_annonce_number') ?? 'homepage_annonce_number'}} ({{$annonceNumber ?? 'annonceNumber'}}) 
            {{trans('JOBLANG::Employment_StartAnnonces.homepage_annonce_foryear') ?? 'annonceYear'}} {{$annonceYear ?? 'annonceYear'}}م<br>
            {{trans('JOBLANG::Employment_Jobs.plural' ?? 'job')}}: <i id="shortJobName">{{$job ?? 'job'}}</i><br>
            {{trans('JOBLANG::Employment_Reports.printForm.PageName')}} : {{$value['PageName'] ?? 'PageName'}}
        </td>
        <td  colspan="3"></td>
        <td  colspan="3">
            {{trans('JOBLANG::Employment_People.uid') ?? 'uid'}}: <i id="Uid">{{\AmerHelper::ArabicNumbersText(' '.$Face['id']) ?? 'id'}}</i><br>
            {{trans('JOBLANG::Employment_People.NID') ?? 'NID'}}: <i id="Nid">{{\AmerHelper::ArabicNumbersText($Face['NID']) ?? 'NID'}}</i><br>
            {{trans('JOBLANG::Employment_Reports.ReqDate') ?? 'ReqDate'}}: <i id="Date">{{\AmerHelper::ArabicNumbersText($value['PrintDate']) ?? 'PrintDate'}}</i>
        </td>
</tr>
</table>