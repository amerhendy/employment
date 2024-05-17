<!-- ama -->
<div class="row text-right border-bottom"  id='div_Employment_Ama'>
            <div class='col-lg-3 primary-color nsscwwbgcolor text-white text-center'>
            {{trans('JOBLANG::Employment_Ama.Employment_Ama')}}
            </div>
            <div class='col col-lg'>
                <select class="form-control" name="Ama_id" id="Employment_Ama" style="width:100%" data-init-function='set_ama' option='Employment_Ama' dd='Text' placeholder="{{trans('JOBLANG::Employment_Ama.Employment_Ama')}}" old="{{@old('Employment_Ama') ?? $data->value->Employment_Ama ?? ""}}"></select>
            </div>
            <div class='col-lg-3 peach-gradient text-white text-center' id='Employment_Ama_info'>
            {{trans('JOBLANG::Employment_People.Employment_Ama.hint')}}
            </div>
        </div>
