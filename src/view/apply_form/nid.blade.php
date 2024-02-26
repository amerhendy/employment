<!-- nid -->
<div class="row text-right border-bottom"  id='niddiv'>
            <div class='col-lg-3 nsscwwbgcolor text-white text-center'>
            {{trans('JOBLANG::Employment_People.NID')}}
            </div>
            <div class='col col-lg'>
            <div class="md-form input-group">
            <label id='nidreal'></label>
            <?php
            $nidinputs=[
                ['type'=>'number','name'=>'NID','placeHolder'=>'NID','min'=>4,'max'=>14],
                ['type'=>'hidden','name'=>'BirthDate','placeHolder'=>'NID','min'=>9],
                ['type'=>'hidden','name'=>'Sex','placeHolder'=>'NID','min'=>1],
                ['type'=>'hidden','name'=>'AgeYears','placeHolder'=>'NID','min'=>4],
                ['type'=>'hidden','name'=>'AgeMonths','placeHolder'=>'NID','min'=>1],
                ['type'=>'hidden','name'=>'AgeDays','placeHolder'=>'NID','min'=>1],
            ];
            ?>
            @foreach($nidinputs as $input)
            <?php
            $inp=$input['name'];
            ?>
                <input 
                        type="{{$input['type']}}" 
                        @if($input['type']=='hidden') 
                            hidden='hidden' 
                        @else 
                            onblur='trim(this)' 
                            class="form-control" 
                        @endif
                        name="{{$input['name']}}" 
                        id="{{$input['name']}}" 
                        placeholder="{{trans('JOBLANG::Employment_People.'.$input['placeHolder'])}}" 
                        value="{{old($input['name']) ?? $data->value->$inp}}" 
                        minlen="{{$input['min']??''}}" maxlength="{{$input['max']??''}}">
            @endforeach
                </div>
            </div>
            <div class='col-lg-3 peach-gradient text-white text-center'>
            {{trans('JOBLANG::apply.apply_info_nid')}}
            </div>
        </div>
