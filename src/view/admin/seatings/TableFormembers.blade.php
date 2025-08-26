<?php
//dd($data[15]);
?>
@foreach($data as $a=>$b)
    @foreach($b as $c=>$d)
    
    <?php
    //dd($d[0]->printSeatings->Number);
    ?>
    @foreach($d[0]->printSeatings->Committee_Memebers as $member)
    <table class="noborder">
        <thead>
            <tr>
                <td class="w30 noborder">{{\Str::limit($d[0]->printSeatings->Committee_Name,20)}}</td>
                <td class="w30">{{\AmerHelper::ArabicNumbersText($c)}}</td>
                <td class="w40"></td>
                <td class="w90">{{$d[0]->Face->Job_id->Mosama_JobNames}}</td>
            </tr>
            <tr>
                <td class="w20 wborder">{{trans('JOBLANG::Employment_People.uid') ?? 'id'}}</td>
                <td class="w20 wborder">{{trans('JOBLANG::Employment_seatings.plural') ?? 'SeatingNumber'}}</td>
                <td class="w90 wborder">{{trans('JOBLANG::Employment_People.FULLname') ?? 'Full Name'}}</td>
                <td class="w30 wborder">{{trans('JOBLANG::Employment_People.NID') ?? 'NID'}}</td>
                <td class="w30 wborder">{{trans('JOBLANG::Employment_seatings.Degree') ?? 'Degree'}}</td>
            </tr>
        </thead>
        <tbody>
            @foreach($d as $l)
            <tr>
                <td class="w20 wborder">{{\AmerHelper::ArabicNumbersText($l->Face->id ?? id)}}</td>
                <td class="w20 wborder">{{\AmerHelper::ArabicNumbersText(" ".$l->printSeatings->Number ?? SeatingNumber)}}</td>
                <td class="w90 fullright wborder">{{$l->Face->FullName ?? FullName}}</td>
                <td class="w30 fullLeft wborder">{{\AmerHelper::ArabicNumbersText($l->Face->NID ?? nid)}}</td>
                <td class="w30 wborder"></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td class="w90"></td>
                <td class="w60"><div class="sign">{{$member['name'] ?? 'name'}}</div></td>
                <td class="w40"></td>
            </tr>
        </tfoot>
    </table>
    <br pagebreak="true" />
    @endforeach
        
    @endforeach
@endforeach
        
