<!-- education -->
<div class="row text-right border-bottom">
            <div class='col-lg-3 nsscwwbgcolor text-white text-center'>
                {{trans('JOBLANG::Employment_People.Mosama_Educations.Mosama_Educations')}}
            </div>
            <div class='col col-lg'>
            <div class="row">
                   <div class="col-lg-12">
                        {{trans('JOBLANG::Employment_People.Mosama_Educations.Mosama_Educations')}}
                        <select class="form-control" name="Education_id" id="Education_id" data-init-function='set_edu' option='Mosama_Educations' dd="text" placeholder="{{trans('EMPLANG::Mosama_Educations.Mosama_Educations')}}" old="{{old('Education_id') ?? $data->value->Education_id ?? ""}}"></select>
                   </div>
                   <div class="col-lg-12">
                        {{trans('JOBLANG::Employment_People.Mosama_Educations.year')}}
                       <input type="year" minlen='1' name="EducationYear" id="EducationYear"  style="width:100%" class="form-control" data-provide="datepicker"  onblur='trim(this)' placeholder="{{trans('JOBLANG::Employment_People.Mosama_Educations.year')}}" value="{{old('EducationYear') ?? $data->value->EducationYear ?? ""}}">
                   </div>
               </div>
            </div>
            <div class='col-lg-3 peach-gradient text-white text-center' id='ed_info'>
            </div>
        </div>
