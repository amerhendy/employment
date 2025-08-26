<!-- appliedForGrievance -->
<?php
$inputs=[
    ['name'=>'GrievanceAnnonce','title'=>trans('JOBLANG::Employment_StartAnnonces.singular'),'select2'=>'','value'=>[]],
    ['name'=>'GrievanceJob','title'=>trans('JOBLANG::Employment_Jobs.singular'),'select2'=>'set_job','value'=>[],'multiple'=>'true'],
    ['name'=>'GrievanceType','title'=>trans('JOBLANG::Employment_Grievance.GrievanceType'),
    'select2'=>'set_GrievanceType',
    'value'=>[
        'Grievance_Practical'=>trans('JOBLANG::Employment_Grievance.PracticalGrievance'),
        'Grievance_apply'=>trans('JOBLANG::Employment_Grievance.AppliedGrievance'),
        'WritingGrievance'=>trans('JOBLANG::Employment_Grievance.WritingGrievance')
    ],'multiple'=>'true'],
    ['name'=>'GrievanceResult','title'=>trans('JOBLANG::Employment_Reports.Result'),'select2'=>'setGrievanceResult','value'=>[
        0=>trans('JOBLANG::Employment_Reports.AllGrievance'),
        1=>trans('JOBLANG::Employment_Reports.AcceptGrievance'),
        2=>trans('JOBLANG::Employment_Reports.NAcceptGrievance'),
    ]],
    ['name'=>'GrievanceStart','title'=>trans('JOBLANG::Employment_Reports.Start'),'value'=>[]],
    ['name'=>'GrievanceEnd','title'=>trans('JOBLANG::Employment_Reports.End'),'value'=>[]],
];
?>
<div class='row'>
    @foreach($inputs as $a=>$b)
    <div class='col-md-2  nsscwwbgcolor'>
            {{$b['title']}}
        </div>
        <div class='col-sm-4'>
            <select 
            id="{{$b['name']}}" 
            name="{{$b['name']}}" 
                    
    @isset($b['select2'])
        {{!empty($b['select2']) ? "data-set-select2=".$b['select2'] :''}}
    @endisset
                    @isset($b['multiple']){{$b['multiple'] == true ? 'multiple':''}}@endisset
                     class="form-control" 
                     style="width:100%"
                     >
                     @isset($b['value'])
                     @if(empty($b['value']))
                     <option></option>
                     @else
                        @foreach($b['value'] as $c=>$d)
                            <option value="{{$c}}">{{$d}}</option>
                        @endforeach
                     
                     @endif
                     @endisset
                    </select>
        </div>
    @endforeach
    
    <div class='col-sm-2' id=''>
                <button class='btn btn-primary btn-sm btn-circle' id='Grievanceshow'><i class="fa fa-eye"></i></button>
                <!--<span class='btn btn-primary btn-sm btn-circle' id='byAnnoncePrint'><i class="fa fa-print"></i></span> -->
            </div>
        <div class='col-sm-1'><span class='' id='GrievanceLength' style=''></span></div>
    </div>
<!-- appliedForGrievance -->