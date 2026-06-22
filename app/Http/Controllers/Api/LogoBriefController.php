<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLogoBriefRequest;
use App\Models\LogoBrief;
use App\Models\LogoBriefFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LogoBriefController extends Controller
{
    /**
     * POST /api/logo-brief
     * Public — called from React form.
     */
    public function store(StoreLogoBriefRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Remove files from data before creating model
        unset($data['reference_files']);

        $brief = LogoBrief::create($data);

        // ─── Handle file uploads ───────────────────────────────────────────────
        if ($request->hasFile('reference_files')) {
            foreach ($request->file('reference_files') as $file) {
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path     = $file->storeAs("logo-briefs/{$brief->id}", $fileName, 'public');

                LogoBriefFile::create([
                    'logo_brief_id' => $brief->id,
                    'file_name'     => $fileName,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path'     => $path,
                    'mime_type'     => $file->getMimeType(),
                    'file_size'     => $file->getSize(),
                ]);
            }
        }

        Log::info('New logo brief submitted', ['id' => $brief->id, 'email' => $brief->email]);

        return response()->json([
            'success' => true,
            'message' => 'Logo brief submitted successfully.',
            'data'    => ['id' => $brief->id],
        ], 201);
    }
}