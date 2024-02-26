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
<div class="row text-right border-bottom"  id='fullname'>
            <div class='col-lg-3 nsscwwbgcolor text-white text-center'>
            {{trans('JOBLANG::Employment_People.FULLname')}}
            </div>
            <div class='col-lg md-form input-group'>
                <?php
                $fullnameinputs=[
                    'Fname','Sname','Tname','Lname'
                ];
                ?>
                @foreach($fullnameinputs as $input)
                <input 
                        type="text" 
                        aria-label="{{trans('JOBLANG::Employment_People.'.$input)}}" 
                        class="form-control" placeholder="{{trans('JOBLANG::Employment_People.'.$input)}}" 
                        name='{{$input}}' 
                        onblur='trim(this)' 
                        value="{{old($input) ?? $data->value->$input}}" 
                        minlen='1'>
                @endforeach
            </div>
            <div class='col-lg-3 peach-gradient text-white text-center' id='fullname_info'>
            {{trans('JOBLANG::Employment_People.apply_info_fullname')}}
            </div>
        </div>