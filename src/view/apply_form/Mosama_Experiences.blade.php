<!-- khebr -->
<div class="row text-right" id='div_Khebra'>
            <div class='col-lg-3 bg-gradient text-white text-center'>
                {{trans('JOBLANG::Employment_People.Khebra.years')}}
            </div>
            <div class='col col-lg rounded border'>
                <?php
                if(isset($data->value->Khebra)){
                    $data->value->Khebra=json_decode($data->value->Khebra,true);
                //    dd($data['value']['Khebra']);
                }
                $khebs=[2,0,1];
                $kebval=old('Khebra_type') ?? $data->value->Khebra[0] ?? "";
                ?>
                <select id="Khebra_type" name="Khebra_type" class="form-control w-100" placeholder="{{trans('JOBLANG::Employment_People.Khebra.type')}}" value="{{old('Khebra_type') ?? $data->value->Khebra[0] ?? ""}}" required>
                    @foreach($khebs as $num)
                    @if((old('Khebra_type') ?? $data->value->Khebra[0] ?? "") == $num)
                        <option selected value='{{$num}}'>{{trans("EMPLANG::Mosama_Experiences.enum_".$num)}}</option>
                    @else
                        <option value='{{$num}}'>{{trans("EMPLANG::Mosama_Experiences.enum_".$num)}}</option>
                    @endif

                    @endforeach
                </select>
                <input type="number" name="Khebra" class="form-control w-100"  onblur='trim(this)' placeholder="{{trans('JOBLANG::Employment_People.Khebra.years')}}"
                value="{{old('Khebra') ?? $data->value->Khebra[1] ?? ""}}"
                 minlen='1' required>
            </div>
            <div class='col-lg-3 peach-gradient text-white text-center' id='Khebra_info'>
            {!! trans('JOBLANG::Employment_People.Khebra.hint') !!}
            </div>
        </div>
