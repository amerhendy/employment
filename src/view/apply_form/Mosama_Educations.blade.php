<!-- education -->
<div class="row text-right">
            <div class='col-lg-3 bg-gradient text-white text-center'>
                {{trans('JOBLANG::Employment_People.Mosama_Educations.Mosama_Educations')}}
            </div>
            <div class='col col-lg rounded border'>
            <div class="row">
                   <div class="col-lg-12">
                        {{trans('JOBLANG::Employment_People.Mosama_Educations.Mosama_Educations')}}
                        <select class="form-control" name="education_id" id="education_id" option='Mosama_Educations' dd="text" placeholder="{{trans('EMPLANG::Mosama_Educations.Mosama_Educations')}}" old="{{old('education_id') ?? $data->value->education_id ?? ""}}" required></select>
                   </div>
                   <div class="col-lg-12">
                        {{trans('JOBLANG::Employment_People.Mosama_Educations.year')}}
                       <input type="year" minlen='1' name="educationyear" id="educationyear"  style="width:100%" class="form-control" data-provide="datepicker"  onblur='trim(this)' placeholder="{{trans('JOBLANG::Employment_People.Mosama_Educations.year')}}" value="{{old('educationyear') ?? $data->value->educationyear ?? ""}}"  required>
                   </div>
               </div>
            </div>
            <div class='col-lg-3 peach-gradient text-white text-center' id='ed_info'>
            </div>
        </div>
