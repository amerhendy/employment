<?php
$inputs=[
    ['name'=>'SeatingAnnonce','title'=>trans('JOBLANG::Employment_StartAnnonces.singular'),'select2'=>'','value'=>[]],
    ['name'=>'SeatingJob','title'=>trans('JOBLANG::Employment_Jobs.singular'),'select2'=>'set_job','value'=>[],'multiple'=>'true'],
    ['name'=>'SeatingStages','title'=>trans('JOBLANG::Employment_Stages.singular'),'select2'=>'','value'=>[],'multiple'=>'true'],
    ['name'=>'SeatingStart','title'=>trans('JOBLANG::Employment_Reports.Start'),'value'=>[]],
    ['name'=>'SeatingEnd','title'=>trans('JOBLANG::Employment_Reports.End'),'value'=>[]],
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
                    @isset($b['select2'])
        {{!empty($b['select2']) ? "data-set-select2=".$b['select2'] : "data-set-select2=null"}}
    @endisset
                    @isset($b['multiple']){{$b['multiple'] == true ? 'multiple="multiple"':''}}@endisset
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
                <button class='btn btn-primary btn-sm btn-circle' id='Seatingshow'><i class="fa fa-eye"></i></button>
                <!--<span class='btn btn-primary btn-sm btn-circle' id='byAnnoncePrint'><i class="fa fa-print"></i></span> -->
            </div>
        <div class='col-sm-1'><span class='' id='SeatingLength' style=''></span></div>
    </div>