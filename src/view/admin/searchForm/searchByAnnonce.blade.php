<!-- searchByAnnonce -->
<?php
    $searchByAnnonceInputs=[
        ['name'=>'AnnonceAnnonce','title'=>trans('JOBLANG::Employment_StartAnnonces.singular'),'select2'=>'setAnnNames','value'=>[]],
        ['name'=>'AnnonceStage','title'=>trans('JOBLANG::Employment_Stages.singular'),'select2'=>'setStages','value'=>[],'multiple'=>true],
        ['name'=>'AnnonceJob','title'=>trans('JOBLANG::Employment_Jobs.singular'),'select2'=>'set_job','value'=>[],'multiple'=>true],
        ['name'=>'AnnonceStatus','title'=>trans('JOBLANG::Employment_Reports.Result'),'select2'=>'SetStatusOptions','value'=>[0=>trans('JOBLANG::Employment_Reports.AllGrievance')],'multiple'=>true],
        ['name'=>'AnnonceStart','title'=>trans('JOBLANG::Employment_Reports.Start'),'value'=>[]],
        ['name'=>'AnnonceEnd','title'=>trans('JOBLANG::Employment_Reports.End'),'value'=>[]],
    ];
?>
        <div class='row'>
        @foreach($searchByAnnonceInputs as $a=>$b)
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
            <div class='col-sm-2'><span class='' id='Annoncelength' style=''></span></div>
            <div class='col-sm-2' id=''>
                <button class='btn btn-primary btn-sm btn-circle' id='byAnnonceShow'><i class="fa fa-eye"></i></button>
                <!--<span class='btn btn-primary btn-sm btn-circle' id='byAnnoncePrint'><i class="fa fa-print"></i></span> -->
            </div>
            
        </div>
    <!-- searchByAnnonce -->