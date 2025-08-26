<!-- nid -->
<div class="row text-right"  id='niddiv'>
            <div class='col-lg-3 bg-gradient text-white text-center'>
            {{trans('JOBLANG::Employment_People.NID')}}
            </div>
            <div class='col col-lg  rounded border md-form input-group'>
            <label id='nidreal' style="width:100%"></label>
            <?php
            $nidinputs=[
                ['type'=>'number','name'=>'nid','placeHolder'=>'NID','min'=>4,'max'=>14,'title'=>trans('JOBLANG::apply.apply_info_nid')],
                ['type'=>'hidden','name'=>'birthdate','placeHolder'=>'NID','min'=>9],
                ['type'=>'hidden','name'=>'sex','placeHolder'=>'NID','min'=>1],
                ['type'=>'hidden','name'=>'ageyears','placeHolder'=>'NID','min'=>4],
                ['type'=>'hidden','name'=>'agemonths','placeHolder'=>'NID','min'=>1],
                ['type'=>'hidden','name'=>'agedays','placeHolder'=>'NID','min'=>1],
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
                        value="{{old($input['name']) ?? $data->value->$inp ?? ''}}"
                        minlen="{{$input['min']??''}}" maxlength="{{$input['max']??''}}"
                        @isset($input['title'])
                        title="{{$input['title'] ?? ''}}"
                        @endisset
                        required
                        >
            @endforeach
            </div>
            <div class='col-lg-3 peach-gradient text-white text-center'>
            {{trans('JOBLANG::apply.apply_info_nid')}}
            </div>
        </div>
