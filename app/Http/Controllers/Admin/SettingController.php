<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Affiche la page des paramètres système.
     */
    public function index()
    {
        $generalSettings = SystemSetting::getGroup('general');
        $deadlineSettings = SystemSetting::getGroup('deadlines');
        $aiSettings = SystemSetting::getGroup('ai');

        return view('admin.settings.index', compact('generalSettings', 'deadlineSettings', 'aiSettings'));
    }

    /**
     * Met à jour les paramètres système.
     */
    public function update(Request $request)
    {
        $settings = SystemSetting::all();
        $updated = [];

        foreach ($settings as $setting) {
            $key = $setting->key;
            if ($request->has("settings.{$key}")) {
                $newValue = $request->input("settings.{$key}");
                $oldValue = $setting->value;

                if ($oldValue !== $newValue) {
                    $setting->update(['value' => $newValue]);
                    $updated[] = $setting->label;
                }
            }
        }

        if (count($updated) > 0) {
            ActivityLog::log('updated', 'Paramètres système modifiés : ' . implode(', ', $updated) . '.');
        }

        return back()->with('success', 'Les paramètres ont été enregistrés avec succès.');
    }
}
