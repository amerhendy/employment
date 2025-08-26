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
use Illuminate\Database\Eloquent\Casts\Attribute;
class Employment_Seatings extends Model
{
    use HasFactory,SoftDeletes,AmerTrait,HasRoles,HasApiTokens;
    protected $table = 'employment_seatings';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    //public $cast=['Seatings'=>'json'];

    protected $fillable = ['people_id','stage_id','number','committee_id','Committee_date'];
    protected $dates = ['deleted_at'];
        public function employment_stages()
        {
            return $this->belongsTo(employment_stages::class, 'stage_id');
        }
        public function setSeatings(){
            return json_decode($this->Seatings,true);
        }
        protected function Seatings(): Attribute

        {

            return Attribute::make(

                get: fn ($value) => json_decode($value, true),

                set: fn ($value) => json_encode($value),

            );

        }
        public function Employment_Committee(){
            return $this->belongsTo(Employment_Committee::class,'committee_id','id');
        }
    }
