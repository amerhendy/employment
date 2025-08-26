<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
Route::group([
    'namespace'  =>config('Amer.Employment.Controllers','\\Amerhendy\Employment\App\Http\Controllers\\')."\\Admin\Main",
    'prefix'     =>config('Amer.Employment.route_prefix','Employment'),
    'middleware' =>array_merge((array) config('Amer.Amer.web_middleware'),(array) config('Amer.Security.auth.middleware_key')),
    'name'=>config('Amer.Employment.routeName_prefix','Employment'),
], function () {
    Route::Amer('Employment_Ama','Employment_AmaAmerController');
    Route::Amer('Employment_Army','Employment_ArmyAmerController');
    Route::Amer('Employment_Health','Employment_HealthAmerController');
    Route::Amer('Employment_Drivers','Employment_DriversAmerController');
    Route::Amer('Employment_Stages','Employment_StagesAmerController');
    Route::Amer('Employment_DinamicPages','Employment_DinamicPagesAmerController');
    Route::Amer('Employment_StaticPages','Employment_StaticPagesAmerController');
    Route::Amer('Employment_IncludedFiles','Employment_IncludedFilesAmerController');
    Route::Amer('Employment_Instructions','Employment_InstructionsAmerController');
    Route::Amer('Employment_Committee','Employment_CommitteeAmerController');
    Route::Amer('Employment_MaritalStatus','Employment_MaritalStatusAmerController');
    Route::Amer('Employment_Qualifications','Employment_QualificationsAmerController');
    Route::Amer('Employment_StartAnnonces','Employment_StartAnnoncesAmerController');
    Route::Amer('Employment_Jobs','Employment_JobsAmerController');
});

Route::group([
    'namespace'  =>config('Amer.Employment.Controllers','\\Amerhendy\Employment\App\Http\Controllers\\')."\\Admin\Main",
    'prefix'     =>config('Amer.Employment.route_prefix','Employment'),
    'middleware' =>array_merge((array) config('Amer.Amer.web_middleware'),(array) config('Amer.Security.auth.middleware_key')),
    'name'=>config('Amer.Employment.routeName_prefix','Employment'),
], function () {
    route::get('employmentReports','Employment_ReportsAmerController@index')->name('EmploymentsIndex');
    route::post('employmentReports/PrintForm','Employment_ReportsAmerController@printForm')->name('EmploymentsPrintForm');
    route::get('employmentReports/upgrade/show/people/filter','Employment_ReportsAmerController@index');
    route::get('employmentReports/upgrade/seatingnumbers','Employment_ReportsAmerController@index');
    route::get('employmentReports/upgrade/recordedInStage','Employment_ReportsAmerController@index');
    route::get('employmentReports/upgrade/getStatics','Employment_ReportsAmerController@index');
    route::get('employmentReports/upgrade/getUidByNidCsv','Employment_ReportsAmerController@index');
    route::get('employmentReports/upgrade/createTestsLegan','Employment_ReportsAmerController@index');
    route::get('employmentReports/upgrade/showAmalyPeople','Employment_ReportsAmerController@index');
    route::get('employmentReports/upgrade/showMeetingPeopleXml','Employment_ReportsAmerController@index');
});

Route::get('local/temp/{path}', function (string $path){
    return Storage::disk(config('Amer.Employment.root_disk_name'))->download($path);
})->name('local.temp');

Route::group([
    'namespace'  =>config('Amer.Employment.Controllers','\\Amerhendy\Employment\App\Http\Controllers\\'),
    'middleware' =>array_merge((array) config('Amer.Amer.web_middleware')),
    'name'=>config('Amer.Employment.routeName_prefix','Employment'),
    'prefix'     =>config('Amer.Employment.route_prefix','Employment'),
], function () {
    Route::get('employment_operation/showJop/{annid}/{jobid}', 'apply@getannonce_job_info')->name('jobinfo');
    Route::post('/employment_operation/stage/{annid}/{jobid}', 'apply@selectview')->name('apply_input_page');
    Route::post('/employment_operation/stage/{annid}/{jobid}/{process}/review', 'apply@review')->name('apply_reviewhtml');
    Route::GET('front',"Amerhendy\Employment\App\Http\Controllers\apply@index")->middleware(['web'])->name('employmentFront');
});
