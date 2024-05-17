<?php $arco=[];
        $arco[]=config('Amer.amer.co_name');
        $arco[]=config('Amer.amer.hc_name');
        $arco[]=config('Amer.amer.min_name');
        $arco=join('<br>',$arco);
?>
<table cellspacing="0" cellpadding="0" border="0" align="center">
        <tr>
                <td rowspan="3">{!! $arco ?? '' !!}</td>
                <td><img src="{{$config['pdfHeaderLogo']['Src']}}" width="60"></td>
                <td rowspan="3">{{config('Amer.amer.co_name_english')}}</td>
        </tr>
</table>
<hr>