
@foreach($data as $a=>$b)
<?php
$d=\Arr::flatten($b);
$membercount=count($d[0]->printSeatings->Committee_Memebers);
$membercount=10;
//dd($membercount);
?>
    <table>
        <thead>
            <tr>
                <td class="w30 noborder">{{\Str::limit($d[0]->printSeatings->Committee_Name,20)}}</td>
                <td class="w30">{{\AmerHelper::ArabicNumbersText($d[0]->printSeatings->Committee_Date)}}</td>
                <td class="w40"></td>
                <td class="w90">{{$d[0]->Face->Job_id->Mosama_JobNames}}</td>
            </tr>
            <tr>
                <td class="w10 wborder">{{trans('JOBLANG::Employment_People.uid') ?? 'id'}}</td>
                <td class="w10 wborder">{{trans('JOBLANG::Employment_seatings.plural') ?? 'SeatingNumber'}}</td>
                <td class="w30 wborder">{{trans('JOBLANG::Employment_People.FULLname') ?? 'Full Name'}}</td>
                <td class="w30 wborder">{{trans('JOBLANG::Employment_People.NID') ?? 'NID'}}</td>
                @foreach($d[0]->printSeatings->Committee_Memebers as $member)
                <td class="wborder" style="width:calc({{$tablewidth-50}}/{{$membercount+1}}mm)">{{$member['name'] ?? 'name'}}</td>
                @endforeach
                <td class="wborder w20">{{trans('JOBLANG::Employment_seatings.Degree') ?? 'Degree'}}</td>
            </tr>
        </thead>
        <tbody>
            @foreach($d as $l)
            <tr>
                <td class="w10 wborder">{{\AmerHelper::ArabicNumbersText($l->Face->id ?? id)}}</td>
                <td class="w10 wborder">{{\AmerHelper::ArabicNumbersText(" ".$l->printSeatings->Number ?? SeatingNumber)}}</td>
                <td class="w30 fullright wborder">{{$l->Face->FullName ?? FullName}}</td>
                <td class="w30 fullLeft wborder">{{\AmerHelper::ArabicNumbersText($l->Face->NID ?? nid)}}</td>
                @foreach($d[0]->printSeatings->Committee_Memebers as $member)
                <td class="wborder" style="width:calc({{$tablewidth-50}}/{{$membercount+1}}mm)"></td>
                @endforeach
                <td class="wborder w20"></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
        <?php 
            $master=\Arr::where($d[0]->printSeatings->Committee_Memebers,function($v,$k){
                return $v['Position']=='Master';
            });
            $mems=\Arr::where($d[0]->printSeatings->Committee_Memebers,function($v,$k){
                return $v['Position']!=='Master';
            });
        ?>
            <tr>
                <td class="w90 fullright sign">
                @foreach($mems as $member)
                    <div class="sign">{{$member['name'] ?? 'name'}}</div>
                @endforeach
                </td>
                <td class="w60"></td>
                <td class="w90 fullright">
                @foreach($master as $member)
                <div class="sign">{{$member['name'] ?? 'name'}}</div>
                @endforeach
                </td>
            </tr>
        </tfoot>
    </table>
    
    
    <br pagebreak="true" />
@endforeach
        
