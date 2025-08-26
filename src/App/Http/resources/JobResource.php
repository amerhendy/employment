<?php
namespace Amerhendy\Employment\App\Http\resources;
use Illuminate\Http\Resources\Json\JsonResource;
class JobResource extends JsonResource {
    public function toArray($request) {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'description' => $this->description ?: null,
            'count' => $this->count,
            'ageIn' => $this->ageformat,
            'driver' => $this->driver,
            'mosama_groups' => $this->Mosama_JobNames->Mosama_Groups->text ?? null,
            'mosama_job_titles' => $this->Mosama_JobNames->Mosama_JobTitles->text ?? null,
            'mosama_job_names' => [
                'text' => $this->Mosama_JobNames->text ?? null,
                'degrees' => $this->Mosama_JobNames->Mosama_Degrees->text ?? null,
                'tasks' => $this->Mosama_JobNames->Mosama_Tasks->pluck('text'),
                'skills' => $this->Mosama_JobNames->Mosama_Skills->pluck('text'),
                'goals' => $this->Mosama_JobNames->Mosama_Goals->pluck('text'),
                'experiences' => $this->Mosama_JobNames->Mosama_Experiences->map(fn($e) => [$e->type, $e->time]),
                'competencies' => $this->Mosama_JobNames->Mosama_Competencies->pluck('text'),
            ],
            'employment_start_annonces' => [
                'number' => $this->Employment_StartAnnonces->number,
                'year' => $this->Employment_StartAnnonces->year,
                'description' => $this->Employment_StartAnnonces->description,
                'employment_stages' => [
                    'text' => $this->Employment_StartAnnonces->Employment_Stages->text,
                    'front' => (int) $this->Employment_StartAnnonces->Employment_Stages->front,
                    'page' => $this->Employment_StartAnnonces->Employment_Stages->page,
                    'functionName' => $this->Employment_StartAnnonces->Employment_Stages->functionName,
                ],
                'governorates' => $this->Employment_StartAnnonces->governorate->pluck('name'),
                'qualifications' => $this->Employment_StartAnnonces->Employment_Qualifications->pluck('text'),
            ],
            'employment_ama' => $this->Employment_Ama->pluck('text'),
            'employment_army' => $this->Employment_Army->pluck('text'),
            'employment_health' => $this->Employment_Health->pluck('text'),
            'employment_instructions' => $this->Employment_Instructions->pluck('text'),
            'employment_marital_status' => $this->Employment_MaritalStatus->pluck('text'),
            'employment_qualifications' => $this->Employment_Qualifications->pluck('text'),
            'employment_drivers' => $this->Employment_Drivers->pluck('text'),
            'employment_included_files' => $this->Employment_IncludedFiles->pluck('file_name'),
            'mosama_educations' => $this->Mosama_Educations->pluck('text'),
            'cities' => $this->City->pluck('name'),
        ];
    }
    public function toObject($request) {
        $data=$this;
        $result=new \stdClass;
        $result->id=$data->id;
        $result->code=$data->code;
        if($data->description == 'null' || $data->description == null || $data->description == ''){
            $data->description=null;
        }
        $result->Description=$data->description;
        $result->Count=$data->count;
        $result->AgeIn=$data->ageformat;
        $result->Driver=$data->driver;
        $result->Mosama_Groups=$data->Mosama_JobNames->Mosama_Groups->text;
        $result->Mosama_JobTitles=$data->Mosama_JobNames->Mosama_JobTitles->text;
        $result->Mosama_JobNames=new \stdClass;
        $result->Mosama_JobNames->Text=$data->Mosama_JobNames->text;
        $result->Mosama_JobNames->Mosama_Degrees=$data->Mosama_JobNames->Mosama_Degrees->text;
        $result->Mosama_JobNames->Mosama_Tasks=\Arr::map($data->Mosama_JobNames->Mosama_Tasks->toArray(),function($v,$k){return $v['text'];});
        $result->Mosama_JobNames->Mosama_Skills=\Arr::map($data->Mosama_JobNames->Mosama_Skills->toArray(),function($v,$k){return $v['text'];});
        $result->Mosama_JobNames->Mosama_Goals=\Arr::map($data->Mosama_JobNames->Mosama_Goals->toArray(),function($v,$k){return $v['text'];});
        $result->Mosama_JobNames->Mosama_Experiences=\Arr::map($data->Mosama_JobNames->Mosama_Experiences->toArray(),function($v,$k){return [$v['type'],$v['time']];});
        $result->Mosama_JobNames->Mosama_Competencies=\Arr::map($data->Mosama_JobNames->Mosama_Competencies->toArray(),function($v,$k){return $v['text'];});
        $result->Employment_StartAnnonces=new \stdClass;
        $result->Employment_StartAnnonces->Number=$data->Employment_StartAnnonces->number;
        $result->Employment_StartAnnonces->Year=$data->Employment_StartAnnonces->year;
        $result->Employment_StartAnnonces->Description=$data->Employment_StartAnnonces->description;
        $result->Employment_StartAnnonces->Employment_Stages=
        [
            'text'=>$data->Employment_StartAnnonces->Employment_Stages->text,
            'front'=>(int)$data->Employment_StartAnnonces->Employment_Stages->front,
            'page'=>$data->Employment_StartAnnonces->Employment_Stages->page,
            'functionName'=>$data->Employment_StartAnnonces->Employment_Stages->functionName
        ];
        $result->Employment_StartAnnonces->Governorates=$data->Employment_StartAnnonces->governorate->pluck('name')->toArray();
        $result->Employment_StartAnnonces->Employment_Qualifications=$data->Employment_StartAnnonces->Employment_Qualifications->pluck('text')->toArray();
        $result->Employment_Ama=$data->Employment_Ama->pluck('text')->toArray();
        $result->Employment_Army = $data->Employment_Army->pluck('text')->toArray();
        $result->Employment_Health=$data->Employment_Health->pluck('text')->toArray();
        $result->Employment_Instructions=$data->Employment_Instructions->pluck('text')->toArray();
        $result->Employment_MaritalStatus=$data->Employment_MaritalStatus->pluck('text')->toArray();
        $result->Employment_Qualifications=$data->Employment_Qualifications->pluck('text')->toArray();
        $result->Employment_Drivers=$data->Employment_Drivers->pluck('text')->toArray();
        $result->Employment_IncludedFiles=$data->Employment_IncludedFiles->pluck('file_name')->toArray();
        $result->Mosama_Educations=$data->Mosama_Educations->pluck('text')->toArray();
        $result->Cities=\Arr::map($data->City->toArray(),function($v,$k){return $v["name"];});
        $result->Cities = $data->City->pluck('name')->toArray();
        return $result;
    }
}
