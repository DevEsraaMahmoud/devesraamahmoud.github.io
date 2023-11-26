<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\MultiImg;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function portfolio($id){
        $project = Portfolio::findOrFail($id);
        $multiImag = MultiImg::where('project_id',$id)->get();
        return view('frontend.portfolio-details', compact('project', 'multiImag'));
    }

    public function projectDetails($id){
    $project = Portfolio::findOrFail($id);
     $multiImag = MultiImg::where('project_id',$id)->get();
    return view('frontend.portfolio-details', compact('project', 'multiImag'));
    }
}
