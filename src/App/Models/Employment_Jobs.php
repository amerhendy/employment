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

    protected $fillable = ['annonce_id','group_id','job_id','jobtitle_id','description','code','name','jobname','job','slug','functional_id','count','agein','age',
    'driver','statue'];
    protected $dates = ['deleted_at','agein'];
    public static $list=[];
    public static $fileds=[];
        public function Employment_StartAnnonces()
        {
            return $this->belongsTo(Employment_StartAnnonces::class, 'annonce_id');
        }
        public function Mosama_JobNames()
        {
            return $this->belongsTo(\Amerhendy\Employers\App\Models\Mosama_JobNames::class,'job_id');
        }
        public function employment_jobs_cities()
        {
            return $this->belongsToMany(\Amerhendy\Amer\App\Models\City::class, 'employment_jobs','id','id');
        }
        public function City()

        {
            return $this->belongsToMany(\Amerhendy\Amer\App\Models\City::class, 'employment_jobs_cities','job_id','city_id')->withTrashed();
        }
        public function Mosama_Groups()
        {
            return $this->belongsTo(\Amerhendy\Employers\App\Models\Mosama_Groups::class,'group_id');
        }
        public function Mosama_JobTitles()
        {
            return $this->belongsTo(\Amerhendy\Employers\App\Models\Mosama_JobTitles::class, 'jobtitle_id');
        }

        public function employment_jobs_amas()
        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_Ama::class, 'employment_jobs','id','id');
        }
        public function Employment_Ama()

        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_Ama::class, 'employment_jobs_amas','job_id','ama_id')->withTrashed();

        }
        public function employment_jobs_armies()
        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_Army::class, 'employment_jobs','id','id');
        }
        public function Employment_Army()

        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_Army::class, 'employment_jobs_armies','job_id','arm_id')->withTrashed();

        }
        public function employment_jobs_maritalstatus()
        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_MaritalStatus::class, 'employment_jobs','id','id');
        }
        public function Employment_MaritalStatus()
        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_MaritalStatus::class, 'employment_jobs_maritalstatus','job_id','maritalstatus_id')->withTrashed();
        }
        public function employment_jobs_healthes()
        {
            return $this->belongsToMany(Employment_Health::class, 'employment_jobs','id','id');
        }
        public function Employment_Health()

        {
            return $this->belongsToMany(Employment_Health::class, 'employment_jobs_healthes','job_id','health_id')->withTrashed();

        }
        public function employment_jobs_educations()
        {
            return $this->belongsToMany(\Amerhendy\Employers\App\Models\Mosama_Educations::class, 'employment_jobs','id','id');
        }
        public function Mosama_Educations()

        {
            return $this->belongsToMany(\Amerhendy\Employers\App\Models\Mosama_Educations::class, 'employment_jobs_educations','job_id','education_id')->withTrashed();

        }
        public function employment_jobs_qualifications()
        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_qualifications::class, 'employment_jobs','id','id');
        }
        public function Employment_qualifications()

        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_qualifications::class, 'employment_jobs_qualifications','job_id','qualification_id')->withTrashed();
        }
        public function employment_jobs_includedfiles()
        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_IncludedFiles::class, 'employment_jobs','id','id');
        }
        public function Employment_IncludedFiles()

        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_IncludedFiles::class, 'employment_jobs_includedfiles','job_id','includedfiles_id')->withTrashed();

        }
        public function employment_jobs_instructions()
        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_Instructions::class, 'employment_jobs','id','id');
        }
        public function Employment_Instructions()

        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_Instructions::class, 'employment_jobs_instructions','job_id','instraction_id')->withTrashed();

        }
        public function employment_jobs_orgstru_sections()
        {
            return $this->belongsToMany(\Amerhendy\Employers\App\Models\OrgStru\OrgStru_Sections::class, 'employment_jobs','id','id');
        }
        public function OrgStru_Sections()

        {
            return $this->belongsToMany(\Amerhendy\Employers\App\Models\OrgStru\OrgStru_Sections::class, 'employment_jobs_orgstru_sections','job_id','section_id')->withTrashed();

        }
        public function employment_jobs_orgstru_areas()
        {
            return $this->belongsToMany(\Amerhendy\Employers\App\Models\OrgStru\OrgStru_Areas::class, 'employment_jobs','id','id');
        }
        public function OrgStru_Areas()

        {
            return $this->belongsToMany(\Amerhendy\Employers\App\Models\OrgStru\OrgStru_Areas::class, 'employment_jobs_orgstru_areas','job_id','area_id')->withTrashed();

        }
        public function employment_jobs_orgstru_mahtas()
        {
            return $this->belongsToMany(\Amerhendy\Employers\App\Models\OrgStru\OrgStru_Mahatas::class, 'employment_jobs','id','id');
        }
        public function OrgStru_Mahatas()

        {
            return $this->belongsToMany(\Amerhendy\Employers\App\Models\OrgStru\OrgStru_Mahatas::class, 'employment_jobs_orgstru_mahtas','job_id','mahta_id')->withTrashed();

        }

    //employment_jobs


    //employment_jobs
        public function employment_jobs_drivers()
        {
            return $this->belongsToMany(Employment_Drivers::class, 'employment_jobs','id','id');
        }
        public function Employment_Drivers()

        {
            return $this->belongsToMany(Employment_Drivers::class, 'employment_jobs_drivers','job_id','driver_id')->withTrashed();

        }

    public function ageformat(): Attribute
    {
        return Attribute::make(
            get:function (mixed $value, array $attributes){

                $data=new \stdClass;
                $data->Age=$attributes['age'];
                $agein=new \stdClass;
                $agein->Day=\Carbon\Carbon::parse($attributes['agein'])->format('d');
                $agein->Month=\Carbon\Carbon::parse($attributes['agein'])->format('m');
                $agein->Year=\Carbon\Carbon::parse($attributes['agein'])->format('Y');
                $data->agein=$agein;
                return $data;
            }
        );
    }
}
