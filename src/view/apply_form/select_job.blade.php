<!-- mir -->
        <div class="row text-right"  id='div_job_select'>
            <div class='col-lg-3 bg-gradient text-white text-center'>
            {{trans('JOBLANG::apply.Employment_Jobs.selectJob')}}
            </div>
            <div class='col col-lg rounded border'>
                <select class="form-control" name="select_job" id="select_job" style="width:100%" option='job' dd='text' placeholder="{{trans('JOBLANG::apply.Employment_Jobs.please_select_job')}}" old="{{old('select_job') ?? $job ?? ""}}" required>
                </select>
            </div>
            <div class='col-lg-3 peach-gradient text-white text-center' id='select_job_info'>
            {{trans('JOBLANG::apply.Employment_Jobs.selectJobInfo')}}
            </div>
        </div>
