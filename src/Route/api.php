<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

    
    ///Employment Reports

    Route::post('Filters','api\AnnoncesController@FilterCollection');
    Route::post('employmentReports/Counts','api\PeopleFilterController@Counts');
    Route::post('employmentReports/People','api\PeopleController@index');
    Route::post('employmentReports/message_template','api\PeopleController@message_template');
    //route::post('employmentReports/PrintForm','api\AdminUpToDate@PrintForm')->name('EmploymentsPrintFormApi');
    //route::post('employmentReports/PrintForm/Seating','api\AdminUpToDate@SeatingForm')->name('SeatingFormApi');
    Route::post('employmentReports/adminuptodate','api\AdminUpToDate@index');
    Route::post('employmentReports/adminuptodate/downloadZip','api\AdminUpToDate@downloadZip');
    Route::post('employmentReports/adminuptodate/PrintUserData','api\AdminUpToDate@printUserData');
