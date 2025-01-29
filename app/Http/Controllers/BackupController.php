<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\Facades\DataTables;

class BackupController extends Controller
{
    public function index()
    {
        $backupDisk = Storage::disk('c_drive');

        // Debug: Check if the disk is accessible
        if (!$backupDisk->exists('.')) {
            dd('Disk not accessible or path is incorrect.');
        }

        return view('backend.pages.backups.index');
    }

    public function data()
    {
        $backupDisk = Storage::disk('c_drive');
        $backups = $backupDisk->allFiles();

        // Transform the array of file paths into an array of arrays
        $data = array_map(function ($filename) {
            return [
                'filename' => $filename, // This matches the 'data' key in your DataTable column definition
                'action' => '<a href="' . route('backup.download', ['filename' => $filename]) . '">Download</a>'
            ];
        }, $backups);

        return DataTables::of($data)
            ->rawColumns(['action']) // Render HTML in the action column
            ->make(true);
    }

    public function create()
    {
        try {
            // Run the backup command
            Artisan::call('backup:run --only-db');
            $output = Artisan::output();
            Log::info("Backup created successfully: " . $output);
            session()->flash('success', 'Successfully created backup!');
        } catch (Exception $e) {
            Log::error("Backup failed: " . $e->getMessage());
            session()->flash('danger', 'Backup failed: ' . $e->getMessage());
        }
        return redirect()->back();
    }

    public function download($filename)
    {
        $backupDisk = Storage::disk('c_drive');

        // Sanitize the filename by removing slashes
        $sanitizedFilename = str_replace(['/', '\\'], '_', $filename);

        // Check if the file exists
        if (!$backupDisk->exists($filename)) {
            abort(404, 'File not found.');
        }

        // Get the full path to the file
        $filePath = $backupDisk->path($filename);

        // Create a BinaryFileResponse for the file download
        $response = Response::download($filePath, $sanitizedFilename);

        // // Add a cookie to the response
        // $cookie = new Cookie('download_started', 'true', time() + 3600); // 1 hour expiration
        // $response->headers->setCookie($cookie);

        return $response;
    }
}
