<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
    Route::post('frontpage','Amerhendy\Employment\App\Http\Controllers\api\AnnoncesController@frontpage')->name('frontpage');
    Route::post('employment/getjob','Amerhendy\Employment\App\Http\Controllers\api\AnnoncesController@getjob_by_id');
    Route::post('employment/getjob/{annslug}','Amerhendy\Employment\App\Http\Controllers\api\AnnoncesController@getjob_by_Annonce_slug');
    Route::post('employment/checknid','Amerhendy\Employment\App\Http\Controllers\api\nidController@employment_apply_checknid');       //checked
    Route::post('employment/allgovs','Amerhendy\Employment\App\Http\Controllers\api\AnnoncesController@allgovs');        //checked
    Route::post('employment/cities_by_gov/{govid}','Amerhendy\Employment\App\Http\Controllers\api\AnnoncesController@bygovid');        //checked
    Route::get('employment/allPlaces','Amerhendy\Employment\App\Http\Controllers\api\AnnoncesController@allPlaces');        //checked
    Route::get('Employment_Health','Amerhendy\Employment\App\Http\Controllers\api\AnnoncesController@healthCollection');      //checked
    Route::get('Employment_Army','Amerhendy\Employment\App\Http\Controllers\api\AnnoncesController@armCollection');            //checked
    Route::get('Employment_MaritalStatus','Amerhendy\Employment\App\Http\Controllers\api\AnnoncesController@mirCollection');            //checked
    Route::get('Employment_Ama','Amerhendy\Employment\App\Http\Controllers\api\AnnoncesController@amaCollection');            //checked
    Route::get('Mosama_Educations','Amerhendy\Employment\App\Http\Controllers\api\AnnoncesController@EducationCollection'); //checked
    Route::get('Employment_Drivers','Amerhendy\Employment\App\Http\Controllers\api\AnnoncesController@driverCollection');  //checked
    //only review
    //Route::post('/employment_operation/stage/{annid}/{jobid}/{process}/review', 'Amerhendy\Employment\App\Http\Controllers\apply@review')->name('apply_review');
    Route::post('/employment_operation/stage/{annid}/{jobid}/{process}/review', 'Amerhendy\Employment\App\Http\Controllers\userOperatings@review')->name('apply_review');
    // complete,apply
    //Route::post('/employment_operation/stage/{annid}/{jobid}/{process}/check', 'Amerhendy\Employment\App\Http\Controllers\api\ApplyController@applycheck')->name('apply_check');
    Route::post('/employment_operation/stage/{annid}/{jobid}/{process}/check', 'Amerhendy\Employment\App\Http\Controllers\userOperatings@addTodaTabase')->name('apply_check');
    Route::get('/removeINIT',function(){
        $peopls=\Amerhendy\Employment\App\Models\Employment_PeopleNewData::withTrashed()->where('people_id','e8b8fb6a-70a0-40a2-8aa4-e1cab1be9804')->first();
        if($peopls)$peopls->forceDelete();
        $peopls=\Amerhendy\Employment\App\Models\Employment_PeopleNewStage::withTrashed()->where('id','<>','50d0d692-18df-47e3-b5e5-2c43145193c8')->where('people_id','e8b8fb6a-70a0-40a2-8aa4-e1cab1be9804')->first();
        if($peopls)$peopls->forceDelete();
        return true;
    });
    Route::post('employment/getresult','Amerhendy\Employment\App\Http\Controllers\api\searchController@get_result');
    Route::post('employment/PeopleInfo','Amerhendy\Employment\App\Http\Controllers\api\PeopleController@userInfoForComplete')->name('userInfo');
    
    
    ///Employment Reports
    Route::post('status','Amerhendy\Employment\App\Http\Controllers\api\AnnoncesController@statusCollection');
    Route::post('stages','Amerhendy\Employment\App\Http\Controllers\api\AnnoncesController@stagesCollection');
    Route::post('Annonces','Amerhendy\Employment\App\Http\Controllers\api\AnnoncesController@annonceCollection');
    Route::post('AnnonceJobs','Amerhendy\Employment\App\Http\Controllers\api\AnnoncesController@annonceJobsCollection');
