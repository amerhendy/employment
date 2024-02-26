<?php
//dd($data);
?>
<table>
  <thead>
    <tr>
      <td class="theadtdid">id</td>
      <td>{{trans('JOBLANG::Employment_People.FULLname') ?? 'Full Name'}}</td>
      <td>{{trans('JOBLANG::Employment_Jobs.plural') ?? 'Job'}}</td>
      <td>TestType</td>
      <td>SeatingNumber</td>
      <td>Committe</td>
      <td class="theadnid">nid</td>
    </tr>
  </thead>
  <tbody>
    @foreach($data as $a=>$b)
      <?php 
      $face=$b->Face; 
      $fullname=implode(' ',[$face->Fname,$face->Sname,$face->Tname,$face->Lname]);
      $Seatings=$b->printSeatings;
      ?>
      @if(count($Seatings))
      @foreach($Seatings as $key=>$person)
    <tr>
      <td class="theadtdid">{{$face->id}}</td>
      <td>{{$fullname}}</td>
      <td>{{\Str::limit($face->Job_id->Mosama_JobTitles,20,'..')}}</td>
      <td  class="theadnid">{{\Str::limit($face->NID,12,'_ _')}}</td>
    </tr>
    @endforeach
    @endif
    @endforeach
  </tbody>
</table>