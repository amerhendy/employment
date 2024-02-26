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
use Amerhendy\Amer\App\Models\Traits\AmerTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
class Employment_Jobs extends Model
{
    use HasFactory,SoftDeletes,AmerTrait,HasRoles,HasApiTokens,Sluggable, SluggableScopeHelpers;
    protected $table = 'Employment_Jobs';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = ['Annonce_id','Group_id','Job_id','JobTitle_id','Description','Code','Name','JobName','Job','Slug','Functional_id','Count','AgeIn','Age','Driver','Statue'];
    protected $dates = ['deleted_at','AgeIn'];
    public static $list=[];
    public static $fileds=[];
public function sluggable(): array
    {
        return [
            'Slug' => [
                'source' => ['Annonce_id','Group_id','JobTitle_id','Job_id','Code','Description','Employment_Ama','Employment_Army'],
            ],
        ];
    }
        public function Employment_StartAnnonces()
        {
            return $this->belongsTo(Employment_StartAnnonces::class, 'Annonce_id');
        }
        public function Mosama_JobNames()
        {
            return $this->belongsTo(\Amerhendy\Employers\App\Models\Mosama_JobNames::class,'Job_id');
        }
        public function Employment_Jobs_City()
        {   
            return $this->belongsToMany(\Amerhendy\Amer\App\Models\Cities::class, 'Employment_Jobs','id','id');
        }
        public function Cities()

        {
            return $this->belongsToMany(\Amerhendy\Amer\App\Models\Cities::class, 'Employment_Jobs_City','Job_id','City_id')->withTrashed();
        }
        public function Mosama_Groups()
        {
            return $this->belongsTo(\Amerhendy\Employers\App\Models\Mosama_Groups::class,'Group_id');
        }
        public function Mosama_JobTitles()
        {
            return $this->belongsTo(\Amerhendy\Employers\App\Models\Mosama_JobTitles::class, 'JobTitle_id');
        }
        
        public function Employment_Jobs_Ama()
        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_Ama::class, 'Employment_Jobs','id','id');
        }
        public function Employment_Ama()

        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_Ama::class, 'Employment_Jobs_Ama','Job_id','Ama_id')->withTrashed();

        }
        public function Employment_Jobs_Army()
        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_Army::class, 'Employment_Jobs','id','id');
        }
        public function Employment_Army()

        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_Army::class, 'Employment_Jobs_Army','Job_id','Arm_id')->withTrashed();

        }
        public function Employment_Jobs_MaritalStatus()
        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_MaritalStatus::class, 'Employment_Jobs','id','id');
        }
        public function Employment_MaritalStatus()
        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_MaritalStatus::class, 'Employment_Jobs_MaritalStatus','Job_id','MaritalStatus_id')->withTrashed();
        }
        public function Employment_Jobs_Health()
        {
            return $this->belongsToMany(Employment_Health::class, 'Employment_Jobs','id','id');
        }
        public function Employment_Health()

        {
            return $this->belongsToMany(Employment_Health::class, 'Employment_Jobs_Health','Job_id','Health_id')->withTrashed();

        }
        public function Employment_Jobs_Educations()
        {
            return $this->belongsToMany(\Amerhendy\Employers\App\Models\Mosama_Educations::class, 'Employment_Jobs','id','id');
        }
        public function Mosama_Educations()

        {
            return $this->belongsToMany(\Amerhendy\Employers\App\Models\Mosama_Educations::class, 'Employment_Jobs_Educations','Job_id','Education_id')->withTrashed();

        }
        public function Employment_Jobs_Qualifications()
        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_Qualifications::class, 'Employment_Jobs','id','id');
        }
        public function Employment_Qualifications()

        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_Qualifications::class, 'Employment_Jobs_Qualifications','Job_id','Qualification_id')->withTrashed();
        }
        public function Employment_Jobs_IncludedFiles()
        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_IncludedFiles::class, 'Employment_Jobs','id','id');
        }
        public function Employment_IncludedFiles()

        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_IncludedFiles::class, 'Employment_Jobs_IncludedFiles','Job_id','Inf_id')->withTrashed();

        }
        public function Employment_Jobs_Instructions()
        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_Instructions::class, 'Employment_Jobs','id','id');
        }
        public function Employment_Instructions()

        {
            return $this->belongsToMany(\Amerhendy\Employment\App\Models\Employment_Instructions::class, 'Employment_Jobs_Instructions','Job_id','Instraction_id')->withTrashed();

        }
        public function Employment_Jobs_OrgStru_Sections()
        {
            return $this->belongsToMany(\Amerhendy\Employers\App\Models\OrgStru\OrgStru_Sections::class, 'Employment_Jobs','id','id');
        }
        public function OrgStru_Sections()

        {
            return $this->belongsToMany(\Amerhendy\Employers\App\Models\OrgStru\OrgStru_Sections::class, 'Employment_Jobs_OrgStru_Sections','Job_id','Section_id')->withTrashed();

        }
        public function Employment_Jobs_OrgStru_Areas()
        {
            return $this->belongsToMany(\Amerhendy\Employers\App\Models\OrgStru\OrgStru_Areas::class, 'Employment_Jobs','id','id');
        }
        public function OrgStru_Areas()

        {
            return $this->belongsToMany(\Amerhendy\Employers\App\Models\OrgStru\OrgStru_Areas::class, 'Employment_Jobs_OrgStru_Areas','Job_id','Area_id')->withTrashed();

        }
        public function Employment_Jobs_OrgStru_Mahtas()
        {
            return $this->belongsToMany(\Amerhendy\Employers\App\Models\OrgStru\OrgStru_Mahatas::class, 'Employment_Jobs','id','id');
        }
        public function OrgStru_Mahatas()

        {
            return $this->belongsToMany(\Amerhendy\Employers\App\Models\OrgStru\OrgStru_Mahatas::class, 'Employment_Jobs_OrgStru_Mahtas','Job_id','Mahta_id')->withTrashed();

        }
        
    //Employment_Jobs
        

    //Employment_Jobs
        public function Employment_Jobs_Drivers()
        {
            return $this->belongsToMany(Employment_Drivers::class, 'Employment_Jobs','id','id');
        }
        public function Employment_Drivers()

        {
            return $this->belongsToMany(Employment_Drivers::class, 'Employment_Jobs_Drivers','Job_id','Driver_id')->withTrashed();

        }

    //Employment_Jobs
        

    //Employment_Jobs
        

    //Employment_Jobs
        

    //Employment_Jobs
        

    //Employment_Jobs
        

    //Employment_Jobs
        public function Employment_Jobs_Places()
        {
            return $this->belongsToMany(Employment_Places::class, 'Employment_Jobs','id','id');
        }
        public function Employment_Places()

        {
            return $this->belongsToMany(Employment_Places::class, 'Employment_Jobs_Places','Job_id','Places_id')->withTrashed();

        }

    //Employment_Jobs
        
}
