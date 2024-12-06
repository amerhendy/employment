<style>
    @media (max-width: 480px) {
.input-group-addon, .input-group-btn, .input-group .form-control {
    width:100%;
    display: block;
    margin-bottom: 10px;
    clear: both;
    }
.input-group {
    display: block;
    }
}
</style>
<!-- fullname -->
<div class="row text-right"  id='fullname'>
            <div class='col-sm-3 bg-gradient text-white text-center'>
            {{trans('JOBLANG::Employment_People.FULLname')}}
            </div>
    <div class='col-lg md-form input-group rounded border'>
                <?php
                $fullnameinputs=[
                    'Fname'=>'fname','Sname'=>'sname','Tname'=>'tname','Lname'=>'lname'
                ];
                ?>
                @foreach($fullnameinputs as $tr=>$input)
                <input
                        type="text"
                        aria-label="{{trans('JOBLANG::Employment_People.'.$tr)}}"
                        class="form-control" placeholder="{{trans('JOBLANG::Employment_People.'.$tr)}}"
                        name='{{$input}}'
                        onblur='trim(this)'
                        value="{{old($input) ?? $data->value->$input ?? ''}}"
                        minlen='1'
                        title="{{trans('JOBLANG::Employment_People.apply_info_fullname')}}"
                        required
                        >
                @endforeach
    </div>
    <div class='col-lg-3 peach-gradient text-white text-center' id='fullname_info'>
        {{trans('JOBLANG::Employment_People.apply_info_fullname')}}
    </div>
        </div>
