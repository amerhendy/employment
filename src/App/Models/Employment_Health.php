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

class Employment_Health extends Model
{
    use HasFactory,SoftDeletes,AmerTrait,HasRoles,HasApiTokens,HasUuids;
    protected $table = "employment_health";
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = ['father',"text"];
    protected $dates = ['deleted_at'];
    public static $list=[];
    public static $fileds=[];
    public function Father(){
        return $this->belongsTo(Employment_Health::class,'id');
    }
    public function Son(){
        return $this->belongsTo(Employment_Health::class,'father');
    }

    public function employment_jobs_healthes()
        {
            return $this->belongsToMany(Employment_Job::class, "employment_healths",'id','id');
        }
        public function Employment_Job()

        {
            return $this->belongsToMany(Employment_Job::class, 'employment_jobs_health','health_id','job_id')->withTrashed();

        }
}
