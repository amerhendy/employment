<?php
namespace Amerhendy\Employment\App\Http\Controllers\api;
use Amerhendy\Amer\App\Helpers\AmerHelper;
use Illuminate\Support\Collection;
use Amerhendy\Amer\App\Models\Governorates;
use Amerhendy\Amer\App\Models\Cities;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use \Amerhendy\Employment\App\Models\Employment_StartAnnonces;
use \Amerhendy\Employment\App\Models\Employment_Jobs as jobs;
use \Amerhendy\Employment\App\Models\Employment_Stages;
use \Amerhendy\Employment\App\Models\Employment_Jobs;
use \Amerhendy\Employment\App\Models\Employment_People;
use \Amerhendy\Employment\App\Models\Employment_Health;
use \Amerhendy\Employment\App\Models\Employment_Ama;
use \Amerhendy\Employment\App\Models\Employment_Army;
use \Amerhendy\Employment\App\Models\Employment_MaritalStatus;
use \Amerhendy\Employment\App\Models\Employment_Drivers;
use \Amerhendy\Employers\App\Models\Mosama_Educations;
use \Amerhendy\Employers\App\Models\Mosama_Experiences;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
class ApplyController extends AmerController
{
    use \Amerhendy\Employment\App\Http\Controllers\api\applyTrait;
    private static $validator,$annonce,$job;
    //return \AmerHelper::responsedata($result,1,1,'');
    public static function getjob_by_job_slug($annslug,$jobslug){
        $data=jobs::with('Employment_StartAnnonces')
        ->with('Employment_StartAnnonces.Employment_Stages')
        ->with('Employment_StartAnnonces.Governorates')
        ->with('Employment_StartAnnonces.Employment_Qualifications')
        ->with('Mosama_JobTitles')
        ->with(['Mosama_JobNames','Mosama_JobNames.Mosama_Competencies','Mosama_JobNames.Mosama_Experiences','Mosama_JobNames.Mosama_Goals','Mosama_JobNames.Mosama_Skills','Mosama_JobNames.Mosama_Tasks','Mosama_JobNames.Mosama_Degrees'])
        ->with('Mosama_Groups')
        ->with('Employment_Ama')
        ->with('Employment_Army')
        ->with('Cities')
        ->with('Mosama_Educations')
        ->with('Employment_Health')
        ->with('Employment_IncludedFiles')
        ->with('Employment_Instructions')
        ->with('Employment_MaritalStatus')
        ->with('Employment_Qualifications')
        ->with('Employment_Drivers')
        ->where('Slug',$jobslug)
        ->whereHas('Employment_StartAnnonces',function($query)use($annslug){
        return $query->where('Employment_StartAnnonces.Slug',$annslug);
        })
        ->get();
        if(count($data) !==1){return;}
        $data=$data[0];
        $result=[];
        $result['code']=$data->Code;
        $result['Description']=$data->Description;
        $result['Slug']=$data->Slug;
        $result['Count']=$data->Count;
        $result['AgeIn']=[
            'day'=>\Carbon\Carbon::parse($data->AgeIn)->format('d'),
            'month'=>\Carbon\Carbon::parse($data->AgeIn)->format('m'),
            'year'=>\Carbon\Carbon::parse($data->AgeIn)->format('Y'),
        ];
        $result['Age']=$data->Age;
        $result['Driver']=$data->Driver;
        $result['Mosama_JobNames']['Text']=$data->Mosama_JobNames->text;
        $result['Mosama_JobNames']['Mosama_Degrees']=$data->Mosama_JobNames->Mosama_Degrees->text;
        $result['Mosama_JobNames']['Mosama_Tasks']=\Arr::map($data->Mosama_JobNames->Mosama_Tasks->toArray(),function($v,$k){return $v['text'];});
        $result['Mosama_JobNames']['Mosama_Skills']=\Arr::map($data->Mosama_JobNames->Mosama_Skills->toArray(),function($v,$k){return $v['text'];});
        $result['Mosama_JobNames']['Mosama_Goals']=\Arr::map($data->Mosama_JobNames->Mosama_Goals->toArray(),function($v,$k){return $v['text'];});
        $result['Mosama_JobNames']['Mosama_Experiences']=\Arr::map($data->Mosama_JobNames->Mosama_Experiences->toArray(),function($v,$k){return [$v['type'],$v['time']];});
        $result['Mosama_JobNames']['Mosama_Competencies']=\Arr::map($data->Mosama_JobNames->Mosama_Competencies->toArray(),function($v,$k){return $v['text'];});
        $result['Mosama_JobTitles']=$data->Mosama_JobTitles->text;
        $result['Employment_StartAnnonces']['Number']=$data->Employment_StartAnnonces->Number;
        $result['Employment_StartAnnonces']['Year']=$data->Employment_StartAnnonces->Year;
        $result['Employment_StartAnnonces']['Description']=$data->Employment_StartAnnonces->Description;
        $result['Employment_StartAnnonces']['Employment_Stages']=[$data->Employment_StartAnnonces->Employment_Stages->Text,(int)$data->Employment_StartAnnonces->Employment_Stages->Front,$data->Employment_StartAnnonces->Employment_Stages->Page];
        $result['Employment_StartAnnonces']['Governorates']=\Arr::map($data->Employment_StartAnnonces->Governorates->toArray(),function($v,$k){return $v['Name'];});
        $result['Employment_StartAnnonces']['Employment_Qualifications']=\Arr::map($data->Employment_StartAnnonces->Employment_Qualifications->toArray(),function($v,$k){return $v['Text'];});
        $result['Employment_Ama']=\Arr::map($data->Employment_Ama->toArray(),function($v,$k){return $v['Text'];});
        $result['Employment_Army']=\Arr::map($data->Employment_Army->toArray(),function($v,$k){return $v['Text'];});
        $result['Employment_Health']=\Arr::map($data->Employment_Health->toArray(),function($v,$k){return $v['Text'];});
        $result['Employment_Instructions']=\Arr::map($data->Employment_Instructions->toArray(),function($v,$k){return $v['Text'];});
        $result['Employment_MaritalStatus']=\Arr::map($data->Employment_MaritalStatus->toArray(),function($v,$k){return $v['Text'];});
        $result['Employment_Qualifications']=\Arr::map($data->Employment_Qualifications->toArray(),function($v,$k){return $v['Text'];});
        $result['Employment_Drivers']=\Arr::map($data->Employment_Drivers->toArray(),function($v,$k){return $v['Text'];});
        $result['Employment_IncludedFiles']=\Arr::map($data->Employment_IncludedFiles->toArray(),function($v,$k){return $v['FileName'];});
        $result['Mosama_Educations']=\Arr::map($data->Mosama_Educations->toArray(),function($v,$k){return $v['text'];});
        $result['Cities']=\Arr::map($data->Cities->toArray(),function($v,$k){return $v['Name'];});
        $result['Mosama_Groups']=$data->Mosama_Groups->text;
        return \AmerHelper::responsedata($result,1,1,'');
    }
    
    public static function applycheck ($annid,$jobid,$process,Request $request){
        return self::local($annid,$jobid,$process,$request);
    }
    
    public static function sstore(Request $request,$validator){
            if($request->actiontype == 'apply'){
                $submit=self::apply_submit($request);
                if($submit[1] == 'bad'){
                    return redirect (redirect()->getUrlGenerator()->previous())->withErrors(['msg'=>1]);
                }else{
                    $data['QR']='<img src="data:image/png;base64,' . $d->getBarcodePNG(implode('TROJAN',$submit), 'QRCODE') . '" alt="barcode"   />';
                    return view ('layout.jobs.applyconfirm',['data'=>$data,'request'=>$data,'annonce_slug'=>$request['annonce_id'],'job_slug'=>$request['job_id'],'page_title'=>trans('trojan.data_entered')]);
                }
            }
            if($request['actiontype'] == 'complete'){
    
                $submit=self::complete_submit($request);
                if($submit[1] == 'bad'){
                    return redirect (redirect()->getUrlGenerator()->previous())->withErrors(['msg'=>1]);
                }else{
                    $data['QR']='<img src="data:image/png;base64,' . $d->getBarcodePNG(implode('CoTROJAN',$submit), 'QRCODE') . '" alt="barcode"   />';
                    return view ('layout.jobs.applyconfirm',['data'=>$data,'request'=>$data,'annonce_slug'=>$request['annonce_id'],'job_slug'=>$request['job_id'],'page_title'=>trans('trojan.data_entered')]);
                }
            }
    }
    
    
    
    
}
