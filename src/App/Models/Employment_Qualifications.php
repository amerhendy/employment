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
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class Employment_Qualifications extends Model
{
    use HasFactory,SoftDeletes,AmerTrait,HasRoles,HasApiTokens,HasUuids;
    protected $table = 'employment_qualifications';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    public static $list=[];
    public static $fileds=[];
        //employment_qualifications
        public function employment_jobs_qualifications()
        {
            return $this->belongsToMany(Employment_Job::class, 'employment_qualifications','id','id');
        }
        public function Employment_Job()

        {
            return $this->belongsToMany(Employment_Job::class, 'employment_jobs_qualifications','qualification_id','job_id')->withTrashed();

        }

    //employment_qualifications
        public function employment_startannonces_qualifications()
        {
            return $this->belongsToMany(employment_StartAnnonces::class, 'employment_qualifications','id','id');
        }
        public function employment_startannonces()

        {
            return $this->belongsToMany(employment_StartAnnonces::class, 'employment_startannonces_qualifications','qualification_id','annonce_id')->withTrashed();

        }
}
