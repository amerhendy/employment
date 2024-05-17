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
class Employment_Qualifications extends Model
{
    use HasFactory,SoftDeletes,AmerTrait,HasRoles,HasApiTokens,Sluggable, SluggableScopeHelpers;
    protected $table = 'Employment_Qualifications';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    public static $list=[];
    public static $fileds=[];
public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => [],
            ],
        ];
    }
        //Employment_Qualifications
        public function employment_Job_Qualifications()
        {
            return $this->belongsToMany(Employment_Job::class, 'Employment_Qualifications','id','id');
        }
        public function Employment_Job()

        {
            return $this->belongsToMany(Employment_Job::class, 'employment_Job_Qualifications','Qualifications_id','Job_id')->withTrashed();

        }

    //Employment_Qualifications
        public function employment_startannonces_Qualifications()
        {
            return $this->belongsToMany(Employment_StartAnnonces::class, 'Employment_Qualifications','id','id');
        }
        public function Employment_StartAnnonces()

        {
            return $this->belongsToMany(Employment_StartAnnonces::class, 'employment_startannonces_Qualifications','Qualification_id','Annonce_id')->withTrashed();

        }
}
