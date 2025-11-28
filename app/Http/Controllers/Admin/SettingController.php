<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function edit()
    {
        // Ambil row pertama (default setting)
        $setting = Setting::first();

        return view('admin.settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama'    => 'required|string|max:255',
            'alamat'  => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:255',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'favicon'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $setting = Setting::first();

        if (!$setting) {
            $setting = new Setting();
        }

        $setting->nama    = $request->nama;
        $setting->alamat  = $request->alamat;
        $setting->telepon = $request->telepon;
        $setting->email   = $request->email;

        if ($request->hasFile('image')) {
            $fileName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/settings'), $fileName);
            $setting->image = 'uploads/settings/' . $fileName;
        }
        if ($request->hasFile('favicon')) {
            $fileName = time() . '.' . $request->favicon->extension();
            $request->favicon->move(public_path('uploads/settings'), $fileName);
            $setting->favicon = 'uploads/settings/' . $fileName;
        }

        $setting->save();

        return redirect()->route('settings.edit')->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
