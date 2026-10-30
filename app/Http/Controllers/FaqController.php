<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use RealRashid\SweetAlert\Facades\Alert;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = \App\Support\SiteCache::remember('faqs.active', fn () => Schema::hasTable('faqs') ? Faq::where('isActive', 1)->orderBy('sort_order')->orderBy('id')->get() : collect());

        return view('pages.faq', compact('faqs'));
    }

    /*
    | Admin — add, edit and delete on one page
    */

    public function adminIndex(Request $request)
    {
        $faqs = Faq::orderBy('sort_order')->orderBy('id')->get();
        $editing = $request->filled('edit') ? Faq::find($request->edit) : null;

        return view('pages.admin-panel.faqs', compact('faqs', 'editing'));
    }

    public function store(Request $req)
    {
        $faq = Faq::create($this->validated($req));
        ActivityLog::record('faq.create', "Added FAQ \"{$faq->question}\"", $faq);
        Alert::success('Success', 'Question added');

        return redirect()->route('admin.faqs.list');
    }

    public function update(Request $req, Faq $faq)
    {
        $faq->update($this->validated($req));
        ActivityLog::record('faq.update', "Updated FAQ \"{$faq->question}\"", $faq);
        Alert::success('Success', 'Question updated');

        return redirect()->route('admin.faqs.list');
    }

    public function destroy(Faq $faq)
    {
        ActivityLog::record('faq.delete', "Deleted FAQ \"{$faq->question}\"", $faq);
        $faq->delete();
        Alert::success('Success', 'Question deleted');

        return redirect()->route('admin.faqs.list');
    }

    private function validated(Request $req): array
    {
        $req->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:5000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        return [
            'question' => $req->question,
            'answer' => $req->answer,
            'sort_order' => (int) $req->sort_order,
            'isActive' => $req->boolean('isActive'),
        ];
    }
}
