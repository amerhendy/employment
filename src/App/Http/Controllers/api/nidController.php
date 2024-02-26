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
    private static $annonce,$job,$nid,$people,$error,$request;
    public function __construct(){
        self::$error=new \stdClass();
        self::$error->number=402;
        self::$error->page=\Str::between(\Str::after(__FILE__,__DIR__),'\\','.php');
    }
    public static function checkPeopleExists(){
        $sckannonce=Employment_People::with(
            'Employment_StartAnnonces','Employment_Stages','Employment_Job','Employment_Job.Mosama_JobNames','Employment_PeopleNewStage','Employment_Job.Mosama_Groups'
            ,'Employment_Job.Mosama_JobTitles','Employment_PeopleNewStage.Employment_Stages',
            'Employment_PeopleNewData','Employment_PeopleNewData.Employment_Stages','Employment_PeopleNewData.Employment_Job',
            'Employment_Grievance',
            )->where('NID',self::$nid)->where('Annonce_id',self::$annonce->id)->first();
            self::$people=$sckannonce;
    }
    public static function employment_apply_checknid($an_slug,$jbslug,Request $request){
        self::$request=$request;
        $errors=self::mainErrors($an_slug,$jbslug);
        if($errors !== null){
            return $errors;
        }
        if(self::$annonce->Employment_Stages->Page !== $request->input('annoncestage')){
            self::$error->message=trans("JOBLANG::apply.nid_phisical_error");self::$error->result='stage';self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        self::checkPeopleExists();
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
    private static function mainErrors($an_slug,$jbslug){
        $request=self::$request;
        $wanted=['nid','page','annoncestage','_token','_method'];
        
        if($request->has($wanted)){
            self::$nid=trim($request->input('nid'));
        }else{
            self::$error->message=trans("JOBLANG::apply.nid_phisical_error");self::$error->result='not14';self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        if(strlen(self::$nid) !== 14){
            self::$error->message=trans("JOBLANG::apply.nid_phisical_error");self::$error->result='not14';self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        $an= Employment_StartAnnonces::with('Employment_Stages')->where('Slug',$an_slug)->first();
        if(!$an){
            self::$error->message=trans("JOBLANG::apply.nid_error_annonce");self::$error->result='errorannonce';self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        self::$annonce=$an;
        $job=Employment_Jobs::where('Slug',$jbslug)->where('Annonce_id',self::$annonce->id)->first();
        if(!$job){
            self::$error->message=trans("JOBLANG::apply.nid_error_annonce");self::$error->result='errorjob';self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        self::$job=$job;
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
        if(!self::$request->has('page')){
            self::$error->message=trans("JOBLANG::apply.nid_error_annonce");self::$error->result='error';self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        if(self::$request->input('page') == 'showjob' || self::$request->input('page') == 'apply'){
            if(self::$people == null){
                self::$error->message=trans("JOBLANG::apply.nidtestSuccess");self::$error->result='success';self::$error->number=200;return \AmerHelper::responseError(self::$error,self::$error->number);
            }else{
                self::$error->message=trans("JOBLANG::apply.nid_error_annonce");self::$error->result='error';self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
            }
        }
    }
    private static function onSearch(){
        if(!self::$request->has('page')){
            self::$error->message=trans("JOBLANG::apply.nid_error_annonce");self::$error->result='error';self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        
        if(self::$people == null){
            self::$error->message=trans("JOBLANG::apply.nid_not_Exists");self::$error->result='error';self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }else{
            self::$error->message=self::$people;self::$error->result='success';self::$error->number=200;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
    }
}
