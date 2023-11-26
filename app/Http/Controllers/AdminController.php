<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Portfolio;
use App\Models\MultiImg;
use Image;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
    public function AllPortfolio(){

        $portfolio = Portfolio::latest()->get();
        return view('admin.portfolio.portfolio_all',compact('portfolio'));
    } // End Method


    public function AddPortfolio(){
        return view('admin.portfolio.portfolio_add');
    } // End Method


    public function StorePortfolio(Request $request){

        $request->validate([
            'title' => 'required',
            'category' => 'required',

        ],[

            'title.required' => 'Portfolio Titile is Required',
        ]);

        $image = $request->file('project_image');
        $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();  // 3434343443.jpg

        Image::make($image)->resize(1020,519)->save('upload/portfolio/'.$name_gen);
        $save_url = 'upload/portfolio/'.$name_gen;

       $project_id= Portfolio::insertGetId([
            'title' => $request->title,
            'description' => $request->description,
            'project_url' => $request->project_url,
            'project_client' => $request->project_client,
            'category' => $request->category,
            'created_at' => Carbon::now(),

        ]);
        $images = $request->file('multi_img');
        foreach ($images as $img) {
            $make_name = hexdec(uniqid()).'.'.$img->getClientOriginalExtension();
            Image::make($img)->resize(917,1000)->save('upload/portfolio/'.$make_name);
            $uploadPath = 'upload/portfolio/'.$make_name;

            MultiImg::insert([

                'project_id' => $project_id,
                'photo_name' => $uploadPath,
                'created_at' => Carbon::now(),

            ]);

        }
        $notification = array(
            'message' => 'Portfolio Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.portfolio')->with($notification);

    }// End Method



    public function EditPortfolio($id){

        $portfolio = Portfolio::findOrFail($id);
        $multiImgs = MultiImg::where('project_id',$id)->get();

        return view('admin.portfolio.portfolio_edit',compact('portfolio','multiImgs'));
    }// End Method


    public function UpdatePortfolio(Request $request){
        $portfolio_id = $request->id;



        Portfolio::findOrFail($portfolio_id)->update([
            'title' => $request->title,
            'description' => $request->description,
            'project_url' => $request->project_url,
            'github_url' => $request->github_url,
            'project_client' => $request->project_client,
            'category' => $request->category,
            'created_at' => Carbon::now(),

        ]);


            return redirect()->route('all.portfolio');

        } // end Else

     // End Method


    public function DeletePortfolio($id){

        $portfolio = Portfolio::findOrFail($id);
        $img = $portfolio->project_image;
        unlink($img);
        $images = MultiImg::where('project_id',$id)->get();
        foreach ($images as $img) {
            unlink($img->photo_name);
            MultiImg::where('project_id',$id)->delete();
        }

        Portfolio::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Portfolio Image Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);

    }// End Method


    public function PortfolioDetails($id){

        $portfolio = Portfolio::findOrFail($id);
        return view('frontend.portfolio_details',compact('portfolio'));
    } // End Method


    public function HomePortfolio(){
        $portfolios = Portfolio::latest()->get();
        return view('frontend.portfolio-details',compact('portfolios'));
    } // End Method

    public function MultiImageUpdate(Request $request){
        $imgs = $request->multi_img;

        foreach ($imgs as $id => $img) {
            $imgDel = MultiImg::findOrFail($id);
            unlink($imgDel->photo_name);

            $make_name = hexdec(uniqid()).'.'.$img->getClientOriginalExtension();
            Image::make($img)->resize(917,1000)->save('upload/portfolio/'.$make_name);
            $uploadPath = 'upload/portfolio/'.$make_name;

            MultiImg::where('id',$id)->update([
                'photo_name' => $uploadPath,
                'updated_at' => Carbon::now(),

            ]);

        } // end foreach

        $notification = array(
            'message' => 'Product Image Updated Successfully',
            'alert-type' => 'info'
        );

        return redirect()->back()->with($notification);

    } // end mehtod

    public function ImageUpdate(Request $request){
        $pro_id = $request->id;
        $oldImage = $request->old_img;
        unlink($oldImage);

        $image = $request->file('project_image');
        $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
        Image::make($image)->resize(917,1000)->save('upload/portfolio/'.$name_gen);
        $save_url = 'upload/portfolio/'.$name_gen;

        Portfolio::findOrFail($pro_id)->update([
            'project_image' => $save_url,
            'updated_at' => Carbon::now(),

        ]);

        $notification = array(
            'message' => 'Product Image Updated Successfully',
            'alert-type' => 'info'
        );

        return redirect()->back()->with($notification);

    } // end method
}
