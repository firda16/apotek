<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use QCod\AppSettings\SavesSettings;

class SettingController extends Controller
{
    use SavesSettings;

    public function update(Request $request)
    {
        $settings = $request->except(['_token', 'logo']);

        // Simpan logo jika ada upload
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo')->store('logos', 'public');
            $settings['logo'] = $logo;
        }

        foreach ($settings as $key => $value) {
            app('settings')->set($key, $value);
        }

        return back()->with('success', 'Settings berhasil diperbarui!');
    }
}
