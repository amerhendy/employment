<table>
  <thead>
    <tr>
      <td class="w10 wborder">{{trans('JOBLANG::Employment_People.uid') ?? 'id'}}</td>
      <td class="w40 wborder">{{trans('JOBLANG::Employment_People.FULLname') ?? 'Full Name'}}</td>
      <td class="w40 wborder">{{trans('JOBLANG::Employment_Jobs.plural') ?? 'Job'}}</td>
      <td class="w10 wborder">{{trans('JOBLANG::Employment_Committee.plural') ?? 'Committe'}}</td>
      <td class="w10 wborder">{{trans('JOBLANG::Employment_seatings.plural') ?? 'SeatingNumber'}}</td>
      <td class="w12 wborder">{{trans('JOBLANG::Employment_seatings.Time') ?? 'Time'}}</td>
      <td class="w30 wborder">{{trans('JOBLANG::Employment_People.NID') ?? 'NID'}}</td>
      <td class="w30 wborder">{{trans('JOBLANG::Employment_seatings.Sign') ?? 'NID'}}</td>
    </tr>
  </thead>
  <tbody>
  @foreach($data as $a=>$b)
      <?php 
      $face=$b->Face; 
      $person=$b->printSeatings;
      ?>
      @if($person !== null)
    <tr>
      <td class="w10 wborder">{{\AmerHelper::ArabicNumbersText($face->id)}}</td>
      <td class="fullright w40 wborder">{{$face->FullName}}</td>
      <td class=" fullright w40 wborder">{{\Str::limit($face->Job_id->Mosama_JobTitles,20,'..')}}</td>
      <?php //dd(\AmerHelper::ArabicNumbersText($person->Committee_Number)); ?>
      <td class="w10 wborder">{{\AmerHelper::ArabicNumbersText(' '.$person->Committee_Number)}}</td>
      <td class="w10 wborder">{{\AmerHelper::ArabicNumbersText(' '.$person->Number)}}</td>
      <?php
      if(!is_null($person->Date)){$time=$person->Date;}else{$time=$person->Committee_Date;}
      $time=date('H:i',strtotime($time));
      $time=\AmerHelper::ArabicNumbersText($time);
      ?>
      <td class="w12 wborder">{{$time}}</td>
      <td  class="fullLeft w30 wborder">{{\AmerHelper::ArabicNumbersText(\Str::limit($face->NID,12,'_ _'))}}</td>
      <td  class="fullLeft w30 wborder"></td>
    </tr>
    @endif
    @endforeach
  </tbody>
  <tfoot>
    <tr>
    <td>{{trans('JOBLANG::Employment_seatings.mokhtas') ?? 'mokhtas'}}</td>
    <td></td>
    <td>{{trans('JOBLANG::Employment_seatings.moragea') ?? 'moragea'}}</td>
    <td></td>
    <td>{{trans('JOBLANG::Employment_seatings.Eatmad') ?? 'Eatmad'}}</td>
    <td></td>
    </tr>
  </tfoot>
</table>