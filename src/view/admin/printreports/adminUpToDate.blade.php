<?php
$value=$data['data']->toArray();
$header=$value['header'];
$users=$value['users'];
$Title=trans('JOBLANG::Employment_Reports.UpToDateForm.UpToDateForm');
//dd($header);
?>
<table>
    <thad>
            <tr>
                <td style="width:{{$data['tablewidth'] ?? 190}}mm;" class="text-center">
                    <h3>{{$Title}}</h3>
                </td>
            </tr>
            <tr>
                <th class="loHightTd w30 text-center">{{trans('JOBLANG::Employment_Reports.UpdDate')}}</th>
                <th class="wborder loHightTd w50 text-center">{{\Carbon\Carbon::now()}}</th>
                <th class="loHightTd w15 text-center">{{$header['publisher']['text']}}</th>
                <th class="wborder loHightTd w50 text-center">
                    {{ \Str::limit(\AmerHelper::ArabicNumbersText($header['publisher']['value']['id']." ").$header['publisher']['value']['name'],20) }}</th>
                
                <th class="loHightTd w15 text-center">{{$header['Stage_id']['text']}}</th>
                <td class="wborder loHightTd w50 text-center">{{$header['Stage_id']['value']['Text']}}</td>
                <th class="loHightTd w15 text-center">{{$header['Status_id']['text']}}</th>
                <td class="wborder loHightTd w50 text-center">{{$header['Status_id']['value']['Text']}}</td>
            </tr>
            <tr>
                <th class="loHightTd w15 text-center">{{$header['Message']['text']}}</th>
                <td class="wborder loHightTd w260 text-center">{!! \Str::limit(\AmerHelper::decodeHTMLEntities($header['Message']['value']),500) !!}</td>
            </tr>
            <tr>
                <th class="wborder loHightTd w10 text-center">{{trans('JOBLANG::Employment_People.uid')}}</th>
                <th class="wborder loHightTd w40 text-center">{{trans('JOBLANG::Employment_People.FULLname')}}</th>
                <th class="wborder loHightTd w10 text-center">{{trans('JOBLANG::Employment_Reports.Result')}}</th>
            </tr>
    </thad>
    <tbody>
      @foreach($users as $k=>$v)
        <tr>
    <?php
    //dd($v);
    if(!isset($v['errors'])){
        //dd($v);
    }
        
    ?>
            <th class="wborder loHightTd w10 text-center">{{\AmerHelper::ArabicNumbersText($v['uid']['id'])}}</th>
            <td class="wborder loHightTd w40 fullright">{{$v['uid']['FullName']}}</td>
            @if(isset($v['errors']['stage']))
            <th class="wborder loHightTd w10 text-center">x</th>
            @else
            <th class="wborder loHightTd w10 text-center">{{\AmerHelper::ArabicNumbersText($v['LastStage']['id']) ?? 'X'}}</th>
            @endif
        </tr>
      @endforeach
    <?php
        //dd($data);
    ?>
    </tbody>
</table>