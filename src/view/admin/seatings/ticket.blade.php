@foreach($data->Seatings as $a=>$b)
<?php
$dat[]=$data->Face->id ?? '';
$dat[]=$data->Face->NID ?? '';
$date=\Carbon\Carbon::parse($data->Seatings[0]->Date)->locale(App::getLocale())->timeZone(config('Amer.amer.timeZone'))->format(config('Amer.amer.Carbon_dateTimeFormat')) ?? 
\Carbon\Carbon::parse($data->Seatings[0]->Committee_Date)->locale(App::getLocale())->timeZone(config('Amer.amer.timeZone'))->format(config('Amer.amer.Carbon_dateTimeFormat'));
$dat[]=$date;
$code=base64_encode(implode('*',$dat));
$type='QRCODE,H';
$barcodeobj=new \TCPDF2DBarcode($code, $type);

$base64 = 'data:image/png;base64,' . base64_encode($barcodeobj->getBarcodePngData());
//dd($base64);
?>
<div class="maindiv">
<table dir=rtl border=1 cellspacing=0 cellpadding=0 class="MsoTableGrid" >
  
 <tr>
  <td rowspan="7" colspan="1" class="rowspan5">
  <p style=""></p>
      <b>
        <span lang="AR-EG" class="rowspancontent" align="center">
        <img src="{{$base64}}" style="width:25mm;position: absolute;rotate:90deg" class="rotate90">
        </span>
      </b>
  </td>
  <td colspan="3" valign=top class="titlteTd">
      <b>
        <span lang="AR-EG">
          {{config('Amer.amer.co_name')}}
        <br>
        {{config('Amer.amer.hc_name')}}
          <br>
          {{config('Amer.amer.min_name')}}
        </span>
      </b>
  </td>
  <td colspan="1" class="imgTd" style="padding:40mm">
    
    <img class="logoImg" src="{{config('Amer.amer.public_path')}}/{{config('Amer.amer.co_logoGif')}}" align="center">
  </td>
 </tr>
 <tr>
  <td class="tdTitleText" colspan="1">
      <b>
        <span lang=AR-EG>
          {{trans('JOBLANG::Employment_People.FULLname')}}
        </span>
      </b>
  </td>
  <td class="fullname" colspan="3">
      <b>
      <span lang=AR-EG>
      {{$data->Face->Fname.' '.$data->Face->Sname.' '.$data->Face->Tname.' '.$data->Face->Lname ?? ''}}
        </span>
      </b>
  </td>
 </tr>
 <tr>
 <td class="tdTitleText" colspan="1">
      <b>
      <span lang=AR-EG>
      {{trans('JOBLANG::Employment_People.uid')}}
        </span>
      </b>
  </td>
  <td class="uid" colspan="1">
      <b>
      <span lang=AR-EG>
          {{$data->Face->id ?? ''}}
        </span>
      </b>
  </td>
 <td class="tdTitleText" colspan="1">
      <b>
      <span lang=AR-EG>
      {{trans('JOBLANG::Employment_People.NID')}}
        </span>
      </b>
  </td>
  <td class="nid" colspan="3">
      <b>
      <span lang=AR-EG>
      {{$data->Face->NID ?? ''}}
        </span>
      </b>
  </td>
 </tr>
 
 <tr>
 <td class="tdTitleText">
      <b>
        <span lang=AR-EG>
        {{trans('JOBLANG::Employment_Jobs.plural')}}
        </span>
      </b>
  </td>
  <td class="Job" colspan="3">
      <b>
        <span lang=AR-EG>
        {{$data->Face->Job_id->Mosama_JobNames}} ({{$data->Face->Job_id->Code}})
        </span>
      </b>
  </td>
 </tr>

 <tr>
  <td class="tdTitleText" colspan="1">
      <span lang=AR-EG>
      {{trans('JOBLANG::Employment_seatings.plural')}}
        </span>
  </td>
  <td class="uid"  colspan="1">
      <span lang=AR-EG>
      {{$data->Seatings[$a]->Number ?? ''}}
        </span>
  </td>
  <td class="tdTitleText"  colspan="1">
      <span lang=AR-EG>
      {{trans('JOBLANG::Employment_Committee.plural')}}
        </span>
  </td>
  <td class="nid"  colspan="1">
      <span lang=AR-EG>
      {{$data->Seatings[$a]->Committee_Name ?? ''}}
        </span>
  </td>
</tr>
<tr>
  <td class="tdTitleText">
        <span lang=AR-EG>
        {{trans('JOBLANG::Employment_seatings.Date')}}
        </span>
  </td>
  <td class="Job" colspan="4">
        <span lang=AR-EG>
          {{$date}}
        </span>
  </td>
 </tr>
</table>
</div>
@endforeach