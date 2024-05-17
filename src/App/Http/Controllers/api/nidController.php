<?php

namespace Amerhendy\Employment\App\Http\Controllers\api;
use Amerhendy\Amer\App\Helpers\AmerHelper;
use \Amerhendy\Employment\App\Models\Employment_StartAnnonces;
use \Amerhendy\Employment\App\Models\Employment_Jobs;
use \Amerhendy\Employment\App\Models\Employment_People;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
class nidController extends AmerController
{
    use checkRequests,peopleTrait;
    public static $AnnnonceSlug,$JobSlug;
    private static $annonce,$job,$nid,$people,$error,$request;
    public function __construct(){
        self::setErrorClass();
    }
    public static function setErrorClass(){//29310021499811
        self::$error=new \stdClass();
        self::$error->number=402;
        self::$error->page=\Str::between(\Str::after(__FILE__,__DIR__),'\\','.php');
    }
    /**
     * employment_apply_checknid
     *
     * @param  mixed $request
     * @return void
     * check nid in:
     *  before apply
     *  apply
     *  
     */
    public static function employment_apply_checknid(Request $request){
        self::setErrorClass();
        self::$request=$request;
        $errors=self::checknid();
        if($errors !== true){
            return $errors;
        }
        $peopleDB=self::EmploymentPeopleUsingNIDAnnonceJob();
        if($peopleDB == false){
            return false;
        }
        ////////////////////
        ////////////////check Stage//////////////////
        $pageid=\Str::after(self::$annonce->Employment_Stages->Page,":");
        if(\Str::before(self::$annonce->Employment_Stages->Page,":") == 'D'){
            $page=\Amerhendy\Employment\App\Models\Employment_DinamicPages::where('id',$pageid)->first();
            if($page->Function == 'complete'){   
                return self::onComplete();
            }elseif($page->Function == 'create'){
                return self::oncreate();
            }elseif($page->Function == 'search'){
                return self::onSearch();
            }else{
                return self::$people;
            }
        }
        self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
    }
    private static function onComplete(){
        if(!self::$people){
            self::$error->message=trans("JOBLANG::apply.nid_not_Exists");self::$error->result='errorannonce';self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        $st=new PeopleStagesController(self::$people);
            //$st=$st::$person=self::$people;
        if($st::get('completeEntry')){
            self::$error->message=trans("JOBLANG::apply.Complete.doneBefore");self::$error->result='appliedBefore';self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }else{
            if($st::get('completeStage')){
                self::$error->message=trans("JOBLANG::apply.Complete.Allowed");self::$error->result='success';self::$error->number=200;return \AmerHelper::responseError(self::$error,self::$error->number);
            }else{
                self::$error->message=trans("JOBLANG::apply.Complete.notAloowed");self::$error->result='appliedBefore';self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
            }
        }
        
    }
    private static function oncreate(){
            if(self::$people == null){
                self::$error->message=trans("JOBLANG::apply.nidtestSuccess");self::$error->result='success';self::$error->number=200;return \AmerHelper::responsedata(self::$error);
            }else{
                self::$error->message=trans("JOBLANG::apply.nidIssetBefore");self::$error->result='error';self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
            }
    }
    private static function onSearch(){
        self::setErrorClass();
        if(self::$people == null){
            self::$error->message=trans("JOBLANG::apply.nid_not_Exists");self::$error->result='error';self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }else{
            return \AmerHelper::responsedata(self::$people,200);
        }
    }
}
