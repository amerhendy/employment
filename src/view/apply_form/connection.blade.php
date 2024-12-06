<!-- connection -->
<?php
$conectionInputs=[
["title"=>trans('JOBLANG::Employment_people.Connection.LandLine'),"type"=>'tel', "name"=>"connectlandline","minlen"=>'8'],
["title"=>trans('JOBLANG::Employment_people.Connection.Mobile'),"type"=>'tel', "name"=>"connectmobile","minlen"=>'10'],
["title"=>trans('JOBLANG::Employment_people.Connection.Email'),"type"=>'email', "name"=>"connectemail"],
];
?>
<div class="row text-right" id='div_con'>
    <div class='col-lg-3 bg-gradient text-white text-center'>
        {{trans('JOBLANG::Employment_people.Connection.Connection')}}
    </div>
    <div class='col col-lg rounded border'>
        <div class="row">
        @foreach($conectionInputs as $input)
            <div class="col-sm-4">
                <div class="text-right col_require">{{$input['title']}}</div>
                <input
                    type="{{$input['type']}}"
                    name="{{$input['name']}}"
                    value="{{old($input['name']) ?? $data->value->$input['name'] ?? ""}}"
                    class="form-control w-100 {{$input['class'] ?? ''}}"
                    onblur='trim(this)'
                    placeholder="{{$input['title']}}"
                    @isset($input['minlen']) minlen={{$input['minlen']}} @endisset
                    required
                >
            </div>
        @endforeach
        </div>
    </div>
    <div class='col-lg-3 peach-gradient text-white text-center' id='connection_info'>
        {{trans('JOBLANG::Employment_people.Connection.hint')}}
    </div>
</div>
