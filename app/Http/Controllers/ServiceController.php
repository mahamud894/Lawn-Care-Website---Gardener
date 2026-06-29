<?php

namespace App\Http\Controllers;

use App\Models\Service;
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
        $data['title'] = $req->title;
        $data['image_url'] = $req->image_url;
        $data['icon_url'] = $req->icon_url;
        $data['description'] = $req->short_description;

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
        $data['title'] = $req->title;
        $data['image_url'] = $req->image_url;
        $data['icon_url'] = $req->icon_url;
        $data['description'] = $req->short_description;
        $data['isActive'] = $req->isActive == "on" ? 1 : 0;

        // dd($req->all());

        Service::where(["id"=>$req->id])->update($data);
        Alert::success('Success', 'Service updated Successfully');

        return redirect()->route("viewServiceList");
    }

    public function deleteService($id){
        Service::where(["id"=>$id])->delete();

        Alert::success('Success', 'Service deleted Successfully');

        return redirect()->back();
    }
}
