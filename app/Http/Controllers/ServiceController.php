<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Support\ImageUpload;
use Illuminate\Http\Request;
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
            'icon_url' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'short_description' => 'required|string',
        ]);

        $data['title'] = $req->title;
        $data['price'] = $req->price;
        $data['description'] = $req->short_description;

        // ২. Service Main Image Upload
        if ($req->hasFile('image_url')) {
            $data['image_url'] = ImageUpload::store($req->file('image_url'), 'services');
        }

        // ৩. Service Icon Image Upload
        if ($req->hasFile('icon_url')) {
            $data['icon_url'] = ImageUpload::store($req->file('icon_url'), 'services/icons');
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
        $service = Service::findOrFail($id);
        return view("pages.admin-panel.service.edit", ["service"=>$service]);
    }

    public function editService(Request $req){

        $req->validate([
            'title' => 'required|string|max:255',
            'price' => 'nullable|numeric',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'icon_url' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'short_description' => 'required|string',
        ]);

        $service = Service::findOrFail($req->id);

        $data['title'] = $req->title;
        $data['price'] = $req->price;
        $data['description'] = $req->short_description;
        $data['isActive'] = $req->isActive == "1" || $req->isActive == "on" ? 1 : 0;

        // ২. New Main Image Upload (delete old upload new)
        if ($req->hasFile('image_url')) {
            $data['image_url'] = ImageUpload::replace($service->image_url, $req->file('image_url'), 'services');
        }

        // ৩. New Icon Upload (old delete andnew set)
        if ($req->hasFile('icon_url')) {
            $data['icon_url'] = ImageUpload::replace($service->icon_url, $req->file('icon_url'), 'services/icons');
        }

        $service->update($data);
        Alert::success('Success', 'Service updated Successfully');

        return redirect()->route("viewServiceList");
    }

    public function deleteService($id){
        $service = Service::find($id);

        if ($service) {
            // file remove time service will remove from server
            ImageUpload::delete($service->image_url);
            ImageUpload::delete($service->icon_url);

            $service->delete();
            Alert::success('Success', 'Service deleted Successfully');
        }

        return redirect()->back();
    }
}
