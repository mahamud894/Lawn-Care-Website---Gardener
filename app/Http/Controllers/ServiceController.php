<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use RealRashid\SweetAlert\Facades\Alert;

class ServiceController extends Controller
{
    public function viewServicePage()
    {
        if (Schema::hasTable('services')) {
            try {
                $services = Service::where(['isActive' => 1])->get();
            } catch (\Exception $e) {
                $services = collect();
            }
        } else {
            $services = collect();
        }

        return view('pages.service', ['services' => $services]);
    }

    public function viewServiceAddForm(){
        return view('pages.admin-panel.service.add');
    }

    public function storeService(Request $req){
        // ১. form validation
        $req->validate([
            'title' => 'required|string|max:255',
            'price' => 'nullable|numeric',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'icon_url' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:1024',
            'short_description' => 'required|string',
        ]);

        $data['title'] = $req->title;
        $data['price'] = $req->price;
        $data['description'] = $req->short_description;

        // ২. Service Main Image Upload
        if ($req->hasFile('image_url')) {
            $image = $req->file('image_url');
            $imageName = time() . '_image.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/services'), $imageName);
            $data['image_url'] = 'uploads/services/' . $imageName;
        }

        // ৩. Service Icon Image Upload
        if ($req->hasFile('icon_url')) {
            $icon = $req->file('icon_url');
            $iconName = time() . '_icon.' . $icon->getClientOriginalExtension();
            $icon->move(public_path('uploads/services/icons'), $iconName);
            $data['icon_url'] = 'uploads/services/icons/' . $iconName;
        }

        Service::create($data);
        Alert::success('Success', 'Service create Successfull');

        return redirect()->route("viewServiceList");
    }

    public function viewServiceList(){
        $services = Service::get();

        return view("pages.admin-panel.service.list", ["data"=>$services]);
    }

    public function viewServiceEditForm($id){
        $service = Service::where(["id"=>$id])->first();
        return view("pages.admin-panel.service.edit", ["service"=>$service]);
    }

    public function editService(Request $req){

        $req->validate([
            'title' => 'required|string|max:255',
            'price' => 'nullable|numeric',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'icon_url' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:1024',
            'short_description' => 'required|string',
        ]);

        $service = Service::findOrFail($req->id);

        $data['title'] = $req->title;
        $data['price'] = $req->price;
        $data['description'] = $req->short_description;
        $data['isActive'] = $req->isActive == "1" || $req->isActive == "on" ? 1 : 0;

        // ২. New Main Image Upload (delete old upload new)
        if ($req->hasFile('image_url')) {
            if ($service->image_url && File::exists(public_path($service->image_url))) {
                File::delete(public_path($service->image_url));
            }

            $image = $req->file('image_url');
            $imageName = time() . '_image.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/services'), $imageName);
            $data['image_url'] = 'uploads/services/' . $imageName;
        }

        // ৩. New Icon Upload (old delete andnew set)
        if ($req->hasFile('icon_url')) {
            if ($service->icon_url && File::exists(public_path($service->icon_url))) {
                File::delete(public_path($service->icon_url));
            }

            $icon = $req->file('icon_url');
            $iconName = time() . '_icon.' . $icon->getClientOriginalExtension();
            $icon->move(public_path('uploads/services/icons'), $iconName);
            $data['icon_url'] = 'uploads/services/icons/' . $iconName;
        }

        $service->update($data);
        Alert::success('Success', 'Service updated Successfully');

        return redirect()->route("viewServiceList");
    }

    public function deleteService($id){
        $service = Service::find($id);

        if ($service) {
            // file remove time service will remove from server
            if ($service->image_url && File::exists(public_path($service->image_url))) {
                File::delete(public_path($service->image_url));
            }
            if ($service->icon_url && File::exists(public_path($service->icon_url))) {
                File::delete(public_path($service->icon_url));
            }

            $service->delete();
            Alert::success('Success', 'Service deleted Successfully');
        }

        return redirect()->back();
    }
}
