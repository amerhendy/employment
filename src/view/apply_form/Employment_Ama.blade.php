<!-- ama -->
<div class="row text-right"  id='div_Employment_Ama'>
            <div class='col-lg-3 primary-color bg-gradient text-white text-center'>
            {{trans('JOBLANG::Employment_Ama.Employment_Ama')}}
            </div>
            <div class='col col-lg  rounded border'>
                <select class="form-control" name="ama_id" id="Employment_Ama" style="width:100%" option='Employment_Ama' dd='text' placeholder="{{trans('JOBLANG::Employment_Ama.Employment_Ama')}}" old="{{@old('Employment_Ama') ?? $data->value->Employment_Ama ?? ""}}" required></select>
            </div>
            <div class='col-lg-3 peach-gradient text-white text-center' id='Employment_Ama_info'>
            {{trans('JOBLANG::Employment_People.Employment_Ama.hint')}}
            </div>
        </div>
