<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $addresses = $user->addresses()->orderByDesc('is_default')->oldest()->get();
        $orders = $user->orders()->latest()->get();
        $saved = $user->savedProducts()->latest('saved_products.created_at')->get();
        return view('account.index', compact('user', 'addresses', 'orders', 'saved'));
    }

    public function order(Order $order)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if($order->user_id !== $user->id, 403);
        $user->unreadNotifications()->where('data->order_id', $order->id)->update(['read_at' => now()]);
        return view('account.order', compact('order'));
    }

    public function cancelRequest(Order $order)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if($order->user_id !== $user->id, 403);
        abort_if($order->status !== 'pending', 403);

        $order->update(['cancel_requested' => true]);

        return back()->with('success', 'Cancellation request sent. We will review it shortly.');
    }

    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['nullable', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:users,email,' . $user->id],
            'gender'     => ['nullable', 'in:male,female,prefer_not_to_say'],
        ]);

        $fullName = trim($request->first_name . ' ' . $request->last_name);

        DB::table('users')->where('id', $user->id)->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name ?? '',
            'name'       => $fullName,
            'email'      => $request->email,
            'gender'     => $request->gender,
        ]);

        return redirect()->route('account')->with('success', 'Profile updated.');
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate(['avatar' => ['required', 'image', 'max:2048']]);

        /** @var User $user */
        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return redirect()->route('account')->with('success', 'Photo updated.');
    }
}
