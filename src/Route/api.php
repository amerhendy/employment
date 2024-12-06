<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
    Route::post('frontpage','api\AnnoncesController@frontpage')->name('frontpage');
    Route::post('employment/getjob','api\AnnoncesController@getjob_by_id');
    Route::post('employment/getjob/{annslug}','api\AnnoncesController@getjob_by_Annonce_slug');
    Route::post('employment/checknid','api\nidController@employment_apply_checknid');       //checked
    Route::post('employment/allgovs','api\AnnoncesController@allgovs');        //checked
    Route::post('employment/cities_by_gov/{govid}','api\AnnoncesController@bygovid');        //checked
    Route::post('Employment_Health','api\AnnoncesController@healthCollection');      //checked
    Route::post('Employment_Army','api\AnnoncesController@armCollection');            //checked
    Route::post('Employment_MaritalStatus','api\AnnoncesController@mirCollection');            //checked
    Route::post('Employment_Ama','api\AnnoncesController@amaCollection');            //checked
    Route::post('Mosama_Educations','api\AnnoncesController@EducationCollection'); //checked
    Route::post('Employment_Drivers','api\AnnoncesController@driverCollection');  //checked
    Route::post('employment_operation/stage/{annid}/{jobid}/{process}/check', 'api\ApplyController@applycheck')->name('apply_check');
    Route::post('employment/getresult','api\searchController@get_result');
    ///Employment Reports
    Route::post('status','api\AnnoncesController@statusCollection');
    Route::post('stages','api\AnnoncesController@stagesCollection');
    Route::post('Annonces','api\AnnoncesController@annonceCollection');
    Route::post('AnnonceJobs','api\AnnoncesController@annonceJobsCollection');
    Route::post('Filters','api\AnnoncesController@FilterCollection');
    Route::post('employmentReports/Counts','api\PeopleFilterController@Counts');
    Route::post('employmentReports/People','api\PeopleController@index');
    Route::post('employmentReports/message_template','api\PeopleController@message_template');
    route::post('employmentReports/PrintForm','api\AdminUptoDate@PrintForm')->name('EmploymentsPrintFormApi');
    //route::post('employmentReports/PrintForm/Seating','api\AdminUptoDate@SeatingForm')->name('SeatingFormApi');
    Route::post('employmentReports/adminuptodate','api\AdminUptoDate@index');
    Route::post('employmentReports/adminuptodate/downloadZip','api\AdminUptoDate@downloadZip');
    Route::post('employmentReports/adminuptodate/PrintUserData','api\AdminUptoDate@printUserData');
