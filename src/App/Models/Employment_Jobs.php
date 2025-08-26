<?php

namespace Amerhendy\Employment\App\Models;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Amerhendy\Amer\App\Models\Traits\AmerTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Employment_Jobs extends Model
{
    use HasFactory,SoftDeletes,AmerTrait,HasRoles,HasApiTokens,HasUuids;
    protected $table = 'employment_jobs';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = ['annonce_id','group_id','job_name_id','job_title_id','description','code','count','age_in','age','driver','status'];
    protected $dates = ['deleted_at','age_in'];
    public static $list=[];
    public static $fileds=[];
    //$table->enum('driver', [true,false])->default(false);
    //$table->enum('status', ['publish', 'draft'])->default('publish');
        public function Employment_StartAnnonces()
        {
            return $this->belongsTo(Employment_StartAnnonces::class, 'annonce_id');
        }
        public function Mosama_JobNames()
        {
            return $this->belongsTo(\Amerhendy\Employers\App\Models\Mosama_JobNames::class,'job_name_id');
        }
        public function employment_jobs_city()
        {
            return $this->belongsToMany(\Amerhendy\Amer\App\Models\Cities::class, 'employment_jobs','id','id');
        }
        public function City()

        {
            return $this->belongsToMany(\Amerhendy\Amer\App\Models\Cities::class, 'employment_jobs_city','job_id','city_id')->withTrashed();
        }
        public function Mosama_Groups()
        {
            return $this->belongsTo(\Amerhendy\Employers\App\Models\Mosama_Groups::class,'group_id');
        }
        public function Mosama_JobTitles()
        {
            return $this->belongsTo(\Amerhendy\Employers\App\Models\Mosama_JobTitles::class, 'job_title_id');
        }

        public function employment_jobs_ama()
        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_Ama::class, 'employment_jobs','id','id');
        }
        public function Employment_Ama()

        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_Ama::class, 'employment_jobs_ama','job_id','ama_id')->withTrashed();

        }
        public function employment_jobs_army()
        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_Army::class, 'employment_jobs','id','id');
        }
        public function Employment_Army()

        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_Army::class, 'employment_jobs_army','job_id','arm_id')->withTrashed();

        }
        public function employment_jobs_marital_status()
        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_MaritalStatus::class, 'employment_jobs','id','id');
        }
        public function Employment_MaritalStatus()
        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_MaritalStatus::class, 'employment_jobs_marital_status','job_id','marital_status_id')->withTrashed();
        }
        public function employment_jobs_health()
        {
            return $this->belongsToMany(Employment_Health::class, 'employment_jobs','id','id');
        }
        public function Employment_Health()
        {
            return $this->belongsToMany(Employment_Health::class, 'employment_jobs_health','job_id','health_id')->withTrashed();

        }
        public function employment_jobs_educations()
        {
            return $this->belongsToMany(\Amerhendy\Employers\App\Models\Mosama_Educations::class, 'employment_jobs','id','id');
        }
        public function Mosama_Educations()

        {
            return $this->belongsToMany(\Amerhendy\Employers\App\Models\Mosama_Educations::class, 'employment_jobs_educations','job_id','education_id')->withTrashed();

        }
        public function employment_jobs_qualification()
        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_qualifications::class, 'employment_jobs','id','id');
        }
        public function Employment_qualifications()

        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_qualifications::class, 'employment_jobs_qualification','job_id','qualification_id')->withTrashed();
        }
        public function employment_jobs_included_file()
        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_IncludedFiles::class, 'employment_jobs','id','id');
        }
        public function Employment_IncludedFiles()

        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_IncludedFiles::class, 'employment_jobs_included_files','job_id','included_file_id')->withTrashed();

        }
        public function employment_jobs_instructions()
        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_Instructions::class, 'employment_jobs','id','id');
        }
        public function Employment_Instructions()

        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_Instructions::class, 'employment_jobs_instructions','job_id','instruction_id')->withTrashed();

        }
        public function employment_jobs_org_stru_section()
        {
            return $this->belongsToMany(\Amerhendy\Employers\App\Models\OrgStru\OrgStru_Sections::class, 'employment_jobs','id','id');
        }
        public function OrgStru_Sections()

        {
            return $this->belongsToMany(\Amerhendy\Employers\App\Models\OrgStru\OrgStru_Sections::class, 'employment_jobs_org_stru_section','job_id','section_id')->withTrashed();

        }
        public function employment_jobs_org_stru_area()
        {
            return $this->belongsToMany(\Amerhendy\Employers\App\Models\OrgStru\OrgStru_Areas::class, 'employment_jobs','id','id');
        }
        public function OrgStru_Areas()

        {
            return $this->belongsToMany(\Amerhendy\Employers\App\Models\OrgStru\OrgStru_Areas::class, 'employment_jobs_org_stru_area','job_id','area_id')->withTrashed();

        }
        public function employment_jobs_org_stru_mahta()
        {
            return $this->belongsToMany(\Amerhendy\Employers\App\Models\OrgStru\OrgStru_Mahatas::class, 'employment_jobs','id','id');
        }
        public function OrgStru_Mahatas()

        {
            return $this->belongsToMany(\Amerhendy\Employers\App\Models\OrgStru\OrgStru_Mahatas::class, 'employment_jobs_org_stru_mahta','job_id','mahta_id')->withTrashed();

        }

    //employment_jobs


    //employment_jobs
        public function employment_jobs_driver()
        {
            return $this->belongsToMany(Employment_Drivers::class, 'employment_jobs','id','id');
        }
        public function Employment_Drivers()

        {
            return $this->belongsToMany(Employment_Drivers::class, 'employment_jobs_driver','job_id','driver_id')->withTrashed();

        }

    public function ageformat(): Attribute
    {
        return Attribute::make(
            get:function (mixed $value, array $attributes){

                $data=new \stdClass;
                $data->Age=$attributes['age'];
                $agein=new \stdClass;
                $agein->Day=\Carbon\Carbon::parse($attributes['age_in'])->format('d');
                $agein->Month=\Carbon\Carbon::parse($attributes['age_in'])->format('m');
                $agein->Year=\Carbon\Carbon::parse($attributes['age_in'])->format('Y');
                $data->agein=$agein;
                return $data;
            }
        );
    }
}
