<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'phone'     => ['nullable', 'string', 'max:30'],
            'address_1' => ['required', 'string', 'max:255'],
            'region'    => ['required', 'string', 'max:255'],
            'province'  => ['required', 'string', 'max:255'],
            'city'      => ['required', 'string', 'max:255'],
            'barangay'  => ['required', 'string', 'max:255'],
            'postcode'  => ['required', 'string', 'max:20'],
            'type'      => ['required', 'in:billing,shipping'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $data = $request->only('name', 'phone', 'address_1', 'region', 'province', 'city', 'barangay', 'postcode', 'type');

        if ($request->filled('address_id')) {
            $address = Address::where('id', $request->address_id)->where('user_id', $user->id)->firstOrFail();
            $address->update($data);
        } else {
            $isFirst = $user->addresses()->count() === 0;
            Address::create($data + ['user_id' => $user->id, 'is_default' => $isFirst]);
        }

        return redirect()->route('account', '#addresses')->with('success', 'Address saved.');
    }

    public function setDefault(Address $address)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($address->user_id === $user->id) {
            $user->addresses()->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        }
        return redirect()->route('account', '#addresses')->with('success', 'Default address updated.');
    }

    public function destroy(Address $address)
    {
        /** @var User $user */
        $user = Auth::user();
        if ($address->user_id === $user->id) {
            $address->delete();
        }
        return redirect()->route('account', '#addresses')->with('success', 'Address removed.');
    }
}
