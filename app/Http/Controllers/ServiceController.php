<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Service;
use App\Support\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use RealRashid\SweetAlert\Facades\Alert;

class ServiceController extends Controller
{
    public function viewServicePage()
    {
        $services = \App\Support\SiteCache::remember('services.active', function () {
            try {
                return Schema::hasTable('services') ? Service::where(['isActive' => 1])->get() : collect();
            } catch (\Exception $e) {
                return collect();
            }
        });

        return view('pages.service', ['services' => $services]);
    }

    /** Public page for one service, e.g. /service/lawn-mowing */
    public function show(string $slug)
    {
        $service = Service::where('slug', $slug)->where('isActive', 1)->firstOrFail();
        $others = Service::where('isActive', 1)->where('id', '!=', $service->id)->take(3)->get();

        return view('pages.service-detail', compact('service', 'others'));
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
            'long_description' => 'nullable|string|max:20000',
            'price_per_100_sqft' => 'nullable|numeric|min:0',
        ]);

        $data['title'] = $req->title;
        $data['price'] = $req->price;
        $data['description'] = $req->short_description;
        $data['long_description'] = $req->long_description;
        $data['price_per_100_sqft'] = $req->price_per_100_sqft;

        // ২. Service Main Image Upload
        if ($req->hasFile('image_url')) {
            $data['image_url'] = ImageUpload::store($req->file('image_url'), 'services');
        }

        // ৩. Service Icon Image Upload
        if ($req->hasFile('icon_url')) {
            $data['icon_url'] = ImageUpload::store($req->file('icon_url'), 'services/icons');
        }

        $service = Service::create($data);
        ActivityLog::record('service.create', "Added service {$service->title}", $service);
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
            'long_description' => 'nullable|string|max:20000',
            'price_per_100_sqft' => 'nullable|numeric|min:0',
        ]);

        $service = Service::findOrFail($req->id);

        $data['title'] = $req->title;
        $data['price'] = $req->price;
        $data['description'] = $req->short_description;
        $data['long_description'] = $req->long_description;
        $data['price_per_100_sqft'] = $req->price_per_100_sqft;
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
        ActivityLog::record('service.update', "Updated service {$service->title}", $service);
        Alert::success('Success', 'Service updated Successfully');

        return redirect()->route("viewServiceList");
    }

    public function deleteService($id){
        $service = Service::find($id);

        if ($service) {
            // file remove time service will remove from server
            ImageUpload::delete($service->image_url);
            ImageUpload::delete($service->icon_url);

            ActivityLog::record('service.delete', "Deleted service {$service->title}", $service);
            $service->delete();
            Alert::success('Success', 'Service deleted Successfully');
        }

        return redirect()->back();
    }
}
