<!-- searchform.filter --><?php
$inputs=[
    ['name'=>'FiltersRes','title'=>trans('JOBLANG::Employment_Reports.Filters'),'select2'=>'','value'=>[]],
    ['name'=>'FiltersAnnonce','title'=>trans('JOBLANG::Employment_StartAnnonces.singular'),'select2'=>'','value'=>[]],
    ['name'=>'FiltersJob','title'=>trans('JOBLANG::Employment_Jobs.singular'),'select2'=>'set_job','value'=>[],'multiple'=>'true'],
    ['name'=>'FiltersStages','title'=>trans('JOBLANG::Employment_Stages.singular'),'select2'=>'','value'=>[],'multiple'=>'true'],
    ['name'=>'FiltersStart','title'=>trans('JOBLANG::Employment_Reports.Start'),'value'=>[]],
    ['name'=>'FiltersEnd','title'=>trans('JOBLANG::Employment_Reports.End'),'value'=>[]],
];
?>
<div class='row'>
    <div class='col-md-2  nsscwwbgcolor'>{{trans('JOBLANG::Employment_Reports.Filters')}}</div>
    <div class='col-sm-10'>
        <select id="Filters" data-set-select2="null"class="form-control" style="width:100%">
            <option></option>
            @foreach(trans('JOBLANG::Employment_Reports.FiltersTypes') as $a=>$b)
            <option value="{{$a}}">{{$b}}</option>
            @endforeach
        </select>
    </div>
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
                <button class='btn btn-primary btn-sm btn-circle' id='Filtersshow'><i class="fa fa-eye"></i></button>
                <!--<span class='btn btn-primary btn-sm btn-circle' id='byAnnoncePrint'><i class="fa fa-print"></i></span> -->
            </div>
        <div class='col-sm-1'><span class='' id='FiltersLength' style=''></span></div>
    </div>