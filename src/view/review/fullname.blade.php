
<div class='row text-right' id='newline'>
    <!-- fullname -->
    <div class='col-sm-2  border-bottom fullname' data-init-function='set_lang' data-text="{{trans('JOBLANG::Employment_People.FULLname')}}"></div>
    <div class='col-sm-5 border-bottom-colored fullname' data-init-function='set_data' data-text='fullname'></div>
    <!-- apply_date -->
    <div class='col-sm-2  border-bottom apply_date' data-init-function='set_lang' data-text="{{trans('JOBLANG::Employment_People.applyDate')}}"></div>
    <div class='col-sm-3 apply_date border-bottom-colored' data-init-function='set_data' data-text='apply_date'></div>
</div>
<div class='row text-right' id='newline'>
    <!-- NID -->
    <div class='col-sm-2 NID  border-bottom' data-init-function='set_lang' data-text="{{trans('JOBLANG::Employment_People.NID')}}"></div>
    <div class='col-sm-3 border-bottom-colored NID' data-init-function='set_data' data-text='NID'></div>
    <!-- sex -->
    <div class='col-sm-1  border-bottom Sex' data-init-function='set_lang' data-text="{{trans('JOBLANG::Employment_People.Sex.Sex')}}"></div>
    <div class='col-sm-1 border-bottom-colored Sex' data-init-function='set_data' data-text='Sex'></div>
</div>
<div class='row text-right' id='newline'>
    <!-- BirthDate -->
    <div class='col-sm-2  border-bottom BirthDate' data-init-function='set_lang' data-text="{{trans('JOBLANG::Employment_People.BirthDate')}}"></div>
    <div class='col-sm-2 border-bottom-colored BirthDate' data-init-function='set_data' data-text='BirthDate'></div>
    <!-- Age -->
    <div class='col-sm-2  border-bottom age' data-init-function='set_lang' data-text="{{trans('JOBLANG::Apply.homepage_job_age_not_more.age')}}"></div>
    <div class='col-sm-3 border-bottom-colored age' data-init-function='set_data' data-text='age'></div>
</div>
<div class='row text-right' id='newline'>
    <!-- live_place -->
    <div class='col-sm-2  border-bottom live_place' data-init-function='set_lang' data-text="{{trans('JOBLANG::Employment_People.LivePlace.LivePlace')}}"></div>
    <div class='col-sm border-bottom-colored live_place' data-init-function='set_data' data-text='live_place'></div>
    <!-- birth_blace -->
    <div class='col-sm-2  border-bottom birth_blace' data-init-function='set_lang' data-text="{{trans('JOBLANG::Employment_People.bornPlace.bornPlace')}}"></div>
    <div class='col-sm border-bottom-colored birth_blace' data-init-function='set_data' data-text='birth_blace'></div>
</div>
<div class='row text-right' id='newline'>
    <!-- ConnectEmail -->
    <div class='col-sm-2  border-bottom ConnectEmail' data-init-function='set_lang' data-text="{{trans('JOBLANG::Employment_People.Connection.Email')}}"></div>
    <div class='col-sm-4 border-bottom-colored ConnectEmail' data-init-function='set_data' data-text='ConnectEmail'></div>
    <!-- ConnectLandline -->
    <div class='col-sm-1  border-bottom ConnectLandline' data-init-function='set_lang' data-text="{{trans('JOBLANG::Employment_People.Connection.LandLine')}}"></div>
    <div class='col-sm-2 border-bottom-colored ConnectLandline' data-init-function='set_data' data-text='ConnectLandline'></div>
    <!-- ConnectMobile -->
    <div class='col-sm-1  border-bottom ConnectMobile' data-init-function='set_lang' data-text="{{trans('JOBLANG::Employment_People.Connection.Mobile')}}"></div>
    <div class='col-sm-2 border-bottom-colored ConnectMobile' data-init-function='set_data' data-text='ConnectMobile'></div>
</div>
<div class='row text-right' id='newline'>
<!-- Employment_Ama -->
    <div class='col-sm-2  border-bottom Employment_Ama' data-init-function='set_lang' data-text="{{trans('JOBLANG::Employment_Ama.singular')}}"></div>
    <div class='col-sm-4 border-bottom-colored Employment_Ama' data-init-function='set_data' data-text='Employment_Ama'></div>
    <!-- Employment_Army -->
    <div class='col-sm-2  border-bottom Employment_Army' data-init-function='set_lang' data-text="{{trans('JOBLANG::Employment_Army.singular')}}"></div>
    <div class='col-sm-4 border-bottom-colored Employment_Army' data-init-function='set_data' data-text='Employment_Army'></div>
</div>
<div class='row text-right' id='newline'>
    <!-- Health_id -->
    <div class='col-sm-2  border-bottom Health_id' data-init-function='set_lang' data-text="{{trans('JOBLANG::Employment_Health.singular')}}"></div>
    <div class='col-sm border-bottom-colored Health_id' data-init-function='set_data' data-text='Health_id'></div>
    <!-- MaritalStatus_id -->
    <div class='col-sm-2  border-bottom MaritalStatus_id' data-init-function='set_lang' data-text="{{trans('JOBLANG::Employment_MaritalStatus.singular')}}"></div>
    <div class='col-sm border-bottom-colored MaritalStatus_id' data-init-function='set_data' data-text='MaritalStatus_id'></div>
</div>
<div class='row text-right' id='newline'>
    <!-- Education_id -->
    <div class='col-sm-2  border-bottom Education_id' data-init-function='set_lang' data-text="{{trans('JOBLANG::Employment_People.Mosama_Educations.Mosama_Educations')}}"></div>
    <div class='col-sm border-bottom-colored Education_id' data-init-function='set_data' data-text='Education_id'></div>
    <!-- EducationYear -->
    <div class='col-sm-2  border-bottom EducationYear' data-init-function='set_lang' data-text="{{trans('JOBLANG::Employment_People.Mosama_Educations.year')}}"></div>
    <div class='col-sm border-bottom-colored EducationYear' data-init-function='set_data' data-text='EducationYear'></div>
</div>
<?php
$Driver=$request['annonce_job']['Driver'];
?>
@if($Driver == 0)
<div class='row text-right driverDegree' id='newline'>
    <!-- DriverDegree -->
    <div class='col-sm-2  border-bottom DriverDegree' data-init-function='set_lang' data-text="{{trans('JOBLANG::Employment_People.Employment_Drivers.DriverDegree')}}"></div>
    <div class='col-sm border-bottom-colored DriverDegree' data-init-function='set_data' data-text='DriverDegree'></div>
    <!-- DriverStart -->
    <div class='col-sm-2  border-bottom DriverStart' data-init-function='set_lang' data-text="{{trans('JOBLANG::Employment_People.Employment_Drivers.DriverStart')}}"></div>
    <div class='col-sm border-bottom-colored DriverStart' data-init-function='set_data' data-text='DriverStart'></div>
    <!-- DriverEnd -->
    <div class='col-sm-2  border-bottom DriverEnd' data-init-function='set_lang' data-text="{{trans('JOBLANG::Employment_People.Employment_Drivers.DriverEnd')}}"></div>
    <div class='col-sm border-bottom-colored DriverEnd' data-init-function='set_data' data-text='DriverEnd'></div>
</div>
@endif
<div class='row text-right' id='newline'>
    <!-- Khebra -->
    <div class='col-sm-3  border-bottom Khebra' data-init-function='set_lang' data-text="{{trans('JOBLANG::Employment_People.Khebra.years')}}"></div>
    <div class='col-sm border-bottom-colored Khebra' data-init-function='set_data' data-text='Khebra'></div>
    <!-- Tamin -->
    <div class='col-sm-2  border-bottom Tamin' data-init-function='set_lang' data-text="{{trans('JOBLANG::Employment_People.Tamin.Tamin')}}"></div>
    <div class='col-sm border-bottom-colored Tamin' data-init-function='set_data' data-text='Tamin'></div>
</div>
<div class='row text-right' id='newline'>
    <!-- uploades -->
    <div class='col-sm-1  border-bottom uploades' data-init-function='set_lang' data-text="{{trans('JOBLANG::Employment_People.Uploaded_files')}}"></div>
    <div class='col-sm border-bottom-colored uploades' data-init-function='set_data' data-text='uploades'></div>
</div>
<?php
//dd($request['user']);
?>