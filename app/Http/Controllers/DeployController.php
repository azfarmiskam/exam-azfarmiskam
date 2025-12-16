<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DeployController extends Controller
{
    public function index()
    {
        $dbStatus = 'Unknown';
        $dbError = null;

        try {
            DB::connection()->getPdo();
            $dbStatus = 'Connected to database: ' . DB::connection()->getDatabaseName();
        } catch (\Exception $e) {
            $dbStatus = 'Connection Failed';
            $dbError = $e->getMessage();
        }

        return view('deploy.index', compact('dbStatus', 'dbError'));
    }

    public function migrate()
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();
            return back()->with('success', 'Migration completed successfully.')->with('output', $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Migration failed: ' . $e->getMessage());
        }
    }

    public function seed()
    {
        try {
            Artisan::call('db:seed', ['--force' => true]);
            $output = Artisan::output();
            return back()->with('success', 'Seeding completed successfully.')->with('output', $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Seeding failed: ' . $e->getMessage());
        }
    }

    public function clear()
    {
        try {
            Artisan::call('optimize:clear');
            $output = Artisan::output();
            return back()->with('success', 'Cache cleared successfully.')->with('output', $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Cache clear failed: ' . $e->getMessage());
        }
    }
}
