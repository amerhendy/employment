        <button class="btn btn-toggle list-group-item list-group-item-action rounded" data-bs-toggle="collapse" data-bs-target="#Adminemployment-collapse" aria-expanded="false">
        <i class="fa fa-user"></i>التوظيف
        </button>
        <div class="collapse list-group list-group-flush" id="Adminemployment-collapse" style="">
        <button class="btn btn-toggle list-group-item list-group-item-action rounded" data-bs-toggle="collapse" data-bs-target="#AdminemploymentPages-collapse" aria-expanded="false">
                <i class="fa fa-briefcase"></i>{{trans('JOBLANG::Employment_Stages.Employment_Stages')}}
                </button>
                <div class="collapse list-group list-group-flush" id="AdminemploymentPages-collapse" style="">
                        <a href="{{Route('Employment.Employment_Stages.index')}}" class="list-group-item list-group-item-action"><i class="fa fa-calendar-o"></i>{{trans('JOBLANG::Employment_Stages.Employment_Stages')}}</a>
                        <a href="{{Route('Employment.Employment_DinamicPages.index')}}" class="list-group-item list-group-item-action"><i class="fa fa-search"></i>{{trans('JOBLANG::Employment_DinamicPages.Employment_DinamicPages')}}</a>
                        <a href="{{Route('Employment.Employment_StaticPages.index')}}" class="list-group-item list-group-item-action"><i class="fa fa-file"></i>{{trans('JOBLANG::Employment_StaticPages.Employment_StaticPages')}}</a>
                </div>
                <button class="btn btn-toggle list-group-item list-group-item-action rounded" data-bs-toggle="collapse" data-bs-target="#Employment_StartAnnonces-collapse" aria-expanded="false">
                <i class="fa fa-briefcase"></i>{{trans('JOBLANG::Employment_StartAnnonces.Employment_StartAnnonces')}}
                </button>
                <div class="collapse list-group list-group-flush" id="Employment_StartAnnonces-collapse" style="">
                        <a href="{{Route('Employment.Employment_StartAnnonces.index')}}" class="list-group-item list-group-item-action"><i class="fa-brands fa-critical-role"></i>{{trans('JOBLANG::Employment_StartAnnonces.Employment_StartAnnonces')}}</a>
                        <a href="{{Route('Employment.Employment_Jobs.index')}}" class="list-group-item list-group-item-action"><i class="fa-brands fa-critical-role"></i>{{trans('JOBLANG::Employment_Jobs.Employment_Jobs')}}</a>
                </div>
        <a href="{{Route('Employment.Employment_Ama.index')}}" class="list-group-item list-group-item-action"><i class="fa-brands fa-critical-role"></i>{{trans('JOBLANG::Employment_Ama.Employment_Ama')}}</a>
        <a href="{{Route('Employment.Employment_Army.index')}}" class="list-group-item list-group-item-action"><i class="fa-brands fa-critical-role"></i>{{trans('JOBLANG::Employment_Army.Employment_Army')}}</a>
        <a href="{{Route('Employment.Employment_Health.index')}}" class="list-group-item list-group-item-action"><i class="fa-brands fa-critical-role"></i>{{trans('JOBLANG::Employment_Health.Employment_Health')}}</a>
        <a href="{{Route('Employment.Employment_Drivers.index')}}" class="list-group-item list-group-item-action"><i class="fa-brands fa-critical-role"></i>{{trans('JOBLANG::Employment_Drivers.Employment_Drivers')}}</a>
        
        <a href="{{Route('Employment.Employment_IncludedFiles.index')}}" class="list-group-item list-group-item-action"><i class="fa-brands fa-critical-role"></i>{{trans('JOBLANG::Employment_IncludedFiles.Employment_IncludedFiles')}}</a>
        <a href="{{Route('Employment.Employment_Instructions.index')}}" class="list-group-item list-group-item-action"><i class="fa-brands fa-critical-role"></i>{{trans('JOBLANG::Employment_Instructions.Employment_Instructions')}}</a>
        <a href="{{Route('Employment.Employment_Committee.index')}}" class="list-group-item list-group-item-action"><i class="fa-brands fa-critical-role"></i>{{trans('JOBLANG::Employment_Committee.Employment_Committee')}}</a>
        <a href="{{Route('Employment.Employment_MaritalStatus.index')}}" class="list-group-item list-group-item-action"><i class="fa-brands fa-critical-role"></i>{{trans('JOBLANG::Employment_MaritalStatus.Employment_MaritalStatus')}}</a>
        <a href="{{Route('Employment.Employment_Qualifications.index')}}" class="list-group-item list-group-item-action"><i class="fa-brands fa-critical-role"></i>{{trans('JOBLANG::Employment_Qualifications.Employment_Qualifications')}}</a>
        <a href="{{Route('EmploymentsIndex')}}" class="list-group-item list-group-item-action"><i class="fa-brands fa-critical-role"></i>{{trans('JOBLANG::Employment_Reports.Employment_Reports')}}</a>
        </div>