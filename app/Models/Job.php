<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;
     // protected $table = 'jobs';
     protected $fillable = [
        'title',
        'category_id',
        'jobt_type_id',
        'vacancy',
        'salary',
        'location',
        'description',
        'benefits',
        'responsibility',
        'qualification',
        'keywords',
        'experience',
        'company_name',
        'status',
        'isFeatured',
        'company_location',
        'company_website',
        'created_at',
        'updated_at',
    ];

   
    public function jobType() {

        return $this->belongsTo(JobType::class);
    }

     public function category() {

        return $this->belongsTo(Category::class);
    }
    public function applications() {

        return $this->hasMany(jobApplication::class);
    }
}
