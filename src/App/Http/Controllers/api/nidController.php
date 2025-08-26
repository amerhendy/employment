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
    public static $AnnnonceSlug,$JobSlug,$peopleDB;
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
        //$request{annonceid,jobid,page,nid}
        //checkrequests
        $errors=self::checknid();
        if($errors !== true){
            return $errors;
        }
        //peopleTrait
        self::EmploymentPeopleUsingNIDAnnonceJob();
        ////////////////////
        ////////////////check Stage//////////////////
        $fn=self::$annonce->Employment_Stages->functionName;
        if($fn->function){
            switch ($fn->function) {
                case 'complete':
                    return self::onComplete();
                    break;
                case 'create':
                    return self::oncreate();
                    break;
                case 'search':
                    return self::onSearch();
                    break;
                
                default:
                    return self::$people;
                    break;
            }
        }
        if($peopleDB == false){
            self::$error->message="employment.apply.nid_not_Exists";
            self::$error->result='error';
            self::$error->line=__LINE__;
            return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
    }
    private static function onComplete(){
        if(!self::$people && !self::$peopleDB){
            self::$error->message="employment.apply.nid_not_Exists";
            self::$error->result='error';
            self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        $people=self::$people ?? self::$peopleDB;
        if ($people instanceof \Illuminate\Database\Eloquent\Collection) {
            $people=$people[0];
        }elseif($people instanceof \Illuminate\Database\Eloquent\Model){

        }
        $st=new PeopleStagesController($people);
        if($st->get('completeEntry')){
            self::$error->message="employment.apply.Complete.doneBefore";self::$error->result='appliedBefore';self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }else{
            if($st->get('completeStage')){
                self::$error->message="employment.apply.Complete.Allowed";
                self::$error->NidactionLabel="employment.apply.Complete.Complete";
                self::$error->result='success';
                self::$error->number=200;
                return \AmerHelper::responsedata(self::$error);
            }else{
                self::$error->message="employment.apply.Complete.notAloowed";self::$error->result='appliedBefore';self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
            }
        }
    }
    private static function oncreate(){
        if(self::$request->page == 'showjob'){
            if(self::$peopleDB == false){
                self::$error->message="employment.apply.nidtestSuccess";self::$error->NidactionLabel="تقديم";self::$error->result='success';self::$error->number=200;return \AmerHelper::responsedata(self::$error);
            }else{
                self::$error->message="employment.apply.nidIssetBefore";self::$error->result='error';
                self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
            }
        }
            if(self::$people == null){
                self::$error->message="employment.apply.nidtestSuccess";
                self::$error->NidactionLabel="employment.apply.pagetitle.apply";
                self::$error->result='success';
                self::$error->number=200;
                return \AmerHelper::responsedata(self::$error);
            }else{
                self::$error->message="employment.apply.nidIssetBefore";self::$error->result='error';self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
            }
    }
    private static function onSearch(){
        self::setErrorClass();
        if(self::$people == null){
            self::$error->message="employment.apply.nid_not_Exists";self::$error->result='error';self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }else{
            return \AmerHelper::responsedata(self::$people,200);
        }
    }
}
