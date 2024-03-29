<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Video;
use App\Subscription;

class ViewerController extends Controller
{
    //
    public function home()
    {
        $subscription_ids = Subscription::where('user_id', Auth::user()->id)->get()->map(function ($subscription) {
            return $subscription->subscription_id;
        });

        if (sizeof($subscription_ids) > 0) {
            $videos = Video::where('user_id', $subscription_ids)->orderBy('created_at', 'desc')->paginate(12);
        } else {
            $videos = [];
        }

        return view('viewer.home', [
            'videos' => $videos
        ]);
    }

    public function watch($id)
    {
        $video = Video::findOrFail($id);
        $comments = Video::find($id)->comments()->paginate(5);

        foreach ($comments as &$comment) {
            $comment->user = User::find($comment->user_id);
        }

        return view('viewer.watch', [
            'video' => $video,
            'comments' => $comments
        ]);
    }

    public function profile()
    {
        return view('viewer.profile', [
            'user' => Auth::user()
        ]);
    }

    public function channels()
    {
        $subscriptions = Subscription::where('user_id', Auth::id())->paginate(12);
        foreach($subscriptions as &$subscription) {
            $subscription->channel = User::find($subscription->subscription_id);
        }

        return view('viewer.channels', [
            'subscriptions' => $subscriptions
        ]);
    }
}
