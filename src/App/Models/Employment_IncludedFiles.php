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

class Employment_IncludedFiles extends Model
{
    use HasFactory,SoftDeletes,AmerTrait,HasRoles,HasApiTokens,HasUuids;
    protected $table = 'employment_included_files';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = ['checked','file_name'];
    //$table->enum('checked', ['mandatory', 'Non-binding', 'According_to_the_job'])->default('mandatory');  // enum مع القيم الافتراضية
    protected $dates = ['deleted_at'];
    public static $list=[];
    public static $fileds=[];
    public function employment_jobs_includedfiles()
        {
            return $this->belongsToMany(Employment_Job::class, 'Employment_IncludedFiles','id','id');
        }
        public function Employment_Job()

        {
            return $this->belongsToMany(Employment_Job::class, 'employment_jobs_included_file','included_file_id','job_id')->withTrashed();

        }
}
