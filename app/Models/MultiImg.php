<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;
use App\Models\Portfolio;
class MultiImg extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function portfolio(){
        return $this->belongsTo(Portfolio::class,'project_id','id');
    }
}
