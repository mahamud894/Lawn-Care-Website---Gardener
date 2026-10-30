<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class ReviewController extends Controller
{
    /** Customer leaves a review for a completed booking */
    public function store(Request $req, Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id(), 403);

        if ($booking->status !== 'done') {
            Alert::error('Not yet', 'You can review a booking once the job is done.');

            return back();
        }

        if ($booking->review()->exists()) {
            Alert::info('Already reviewed', 'You have already reviewed this booking. Thank you!');

            return back();
        }

        $data = $req->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $booking->review()->create([
            'user_id' => Auth::id(),
            'rating' => $data['rating'],
            'comment' => $data['comment'],
            'isApproved' => false,
        ]);

        Alert::success('Thank you! 🌿', 'Your review has been submitted and will appear on the site after approval.');

        return back();
    }

    /*
    | Admin
    */

    public function index()
    {
        $reviews = Review::with(['user', 'booking.service'])->latest()->paginate(15);

        return view('pages.admin-panel.reviews.list', compact('reviews'));
    }

    public function toggleApprove(Review $review)
    {
        $review->update(['isApproved' => ! $review->isApproved]);
        Alert::success('Success', $review->isApproved ? 'Review is now visible on the home page.' : 'Review hidden from the home page.');

        return back();
    }

    public function destroy(Review $review)
    {
        $review->delete();
        Alert::success('Deleted', 'Review deleted.');

        return back();
    }
}
