<style>
#EMPPRONAV a{
    background:none;
    color:black;
}
#EMPPRONAV a:hover{
    color:white;
}
#EMPPRONAV ul .dropdown-menu{
    box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
}
#EMPPRONAV{
    background: linear-gradient(var(--bs-graidnet),var(--color-p),var(--color-a)) !important;
}
</style>
<div id="text"></div>
<nav class="navbar navbar-expand-sm navbar-dark border" id="EMPPRONAV" data-bs-theme="dark">
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#Emp_PEO_" aria-controls="Emp_PEO_" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
</button>
      <div class="collapse navbar-collapse justify-content-md-center" id="Emp_PEO_">
        <ul class="navbar-nav">
        <li class="nav-link"><a class="text-decoration-none"href="{{route('EmploymentsIndex')}}">{{trans('JOBLANG::Employment_Reports.EmploymentsIndex')}}</a></li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="Emp_PEO_SHOWFILTER" data-bs-toggle="dropdown" aria-expanded="false">{{trans('JOBLANG::Employment_Reports.show')}}</a>
            <ul class="dropdown-menu shadow-sm p-1 mb-1 rounded purple-gradient" style="width: max-content;" aria-labelledby="Emp_PEO_SHOWFILTER">
                <li><a class="dropdown-item text-decoration-none"  href="{{url('upgrade/show/people/filter')}}">عرض بالفلاتر - أرقام الجلوس</a></li>
                <li><a class="dropdown-item text-decoration-none"href="{{url('upgrade/recordedInStage')}}">عرض المسجل بمرحلة</a></li>
                <li><a class="dropdown-item text-decoration-none"href="{{url('upgrade/getStatics')}}">احصائية</a></li>
                <li><a class="dropdown-item text-decoration-none"href="{{url('upgrade/getUidByNidCsv')}}">تحويل الرقم القومى للتعريفى</a></li>
                <li><a class="dropdown-item text-decoration-none"href="{{url('upgrade/createTestsLegan')}}">عمل لجان اختبارات</a></li>
                <li><a class="dropdown-item text-decoration-none"href="{{url('upgrade/showAmalyPeople')}}">عرض المتقدمين للعملى</a></li>
                <li><a class="dropdown-item text-decoration-none"href="{{url('upgrade/showMeetingPeopleXml')}}">بيانات المتقدمين للمقابلة xml</a></li>
            </ul>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="Emp_PEO_UPDATE" data-bs-toggle="dropdown" aria-expanded="false">CSV</a>
            <ul class="dropdown-menu shadow-sm p-1 mb-1 rounded purple-gradient" style="width: max-content;"  aria-labelledby="Emp_PEO_UPDATE">
                <li><a class="dropdown-item text-decoration-none"href="{{url('upgrade/addstagefromcv')}}">اضافة مرحلة من ملف CSV</a></li>
                <li><a class="dropdown-item text-decoration-none"href="{{url('upgrade/add_degrees_fromcv')}}">اضافة درجات من ملف CSV</a></li>
                <li><a class="dropdown-item text-decoration-none"href="{{url('upgrade/add_unaccepted_to_stage')}}">اضافة غير المقبولين لمرحلة</a></li>
                <li><a class="dropdown-item text-decoration-none"href="{{url('upgrade/add_tahriry_legan_csv')}}">اضافة لجان التحريرى</a></li>
                <li><a class="dropdown-item text-decoration-none"href="{{url('upgrade/update_tazalom')}}">عرض واغلاق التظلم</a></li>
            </ul>
          </li>

            <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="Emp_PEO_END" data-bs-toggle="dropdown" aria-expanded="false">اغلاق و حذف</a>
            <ul class="dropdown-menu shadow-sm p-1 mb-1 rounded purple-gradient" style="width: max-content;"  aria-labelledby="Emp_PEO_END">
                <li><a class="dropdown-item text-decoration-none" href="{{url('upgrade/closeannonce')}}">اغلاق اعلان</a></li>
                <li><a class="dropdown-item text-decoration-none" href="{{url('upgrade/remove_dup_in_stage')}}">حذف المراحل المتشابهة لشخص واحد</a></li>
                <li><a class="dropdown-item text-decoration-none"href="{{url('remove_from_newstage/annonce/stage/resutl/type')}}">ارقام جلوس امتحان</a></li>
            </ul>
            </li>
          
        </ul>
      </div>
      </nav>