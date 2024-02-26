<!-- Tamin -->
<div class="row text-right border-bottom" id='div_malaf'>
            <div class='col-lg-3 nsscwwbgcolor text-white text-center'>
            {{trans('JOBLANG::Employment_people.Tamin.Tamin')}}
            </div>
            <div class='col col-lg'>
            <input type="number" name="Tamin" class="form-control w-100"  onblur='trim(this)' placeholder="{{trans('JOBLANG::Employment_people.Tamin.Tamin')}}" value="{{old('Tamin') ?? $data->value->Tamin}}" minlen='1'>
            </div>
            <div class='col-lg-3 peach-gradient text-white text-center' id='Tamin_info'>
            {{trans('JOBLANG::Employment_people.Tamin.hint')}}
            </div>
        </div>
