<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Notifications\NewContactMessage;
use App\Support\SafeNotify;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class ContactController extends Controller
{
    public function viewContactPage()
    {
        return view('pages.contact');
    }

    public function store(Request $req)
    {
        // Hidden "website" field: real people leave it empty, spam bots fill it
        if ($req->filled('website')) {
            return redirect()->back();
        }

        $data = $req->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $contact = ContactMessage::create($data);
        SafeNotify::toAdmin(new NewContactMessage($contact));

        Alert::success('Message sent', 'Thanks for reaching out! We will get back to you soon.');

        return redirect()->back();
    }

    /*
    | Admin
    */

    public function index()
    {
        $messages = ContactMessage::latest()->paginate(15);

        return view('pages.admin-panel.messages.list', compact('messages'));
    }

    public function toggleRead(ContactMessage $message)
    {
        $message->update(['isRead' => ! $message->isRead]);

        return redirect()->back();
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();
        Alert::success('Deleted', 'Message deleted.');

        return redirect()->back();
    }
}
