<!-- Tamin -->
<div class="row text-right" id='div_malaf'>
            <div class='col-lg-3 bg-gradient text-white text-center'>
            {{trans('JOBLANG::Employment_people.Tamin.Tamin')}}
            </div>
            <div class='col col-lg rounded border'>
            <input type="number" name="tamin" class="form-control w-100"  onblur='trim(this)' placeholder="{{trans('JOBLANG::Employment_people.Tamin.Tamin')}}" value="{{old('tamin') ?? $data->value->tamin ?? ""}}" minlen='1' required>
            </div>
            <div class='col-lg-3 peach-gradient text-white text-center' id='Tamin_info'>
            {{trans('JOBLANG::Employment_people.Tamin.hint')}}
            </div>
        </div>
