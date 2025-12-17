<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Matkul;
use App\Models\Files;
use App\Models\Folders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Module::with(['file', 'matkul', 'author', 'lastEditor'])
            ->where('active', true)
            ->get();
        
        return view('admin.modules', compact('modules'));
    }

    public function matkulModules($matkulId)
    {
        $matkul = Matkul::with('rootFolder')->findOrFail($matkulId);
        $modules = Module::with(['file', 'author', 'lastEditor'])
            ->where('matkul_id', $matkulId)
            ->where('active', true)
            ->get();
        
        return view('admin.matkul-modules', compact('matkul', 'modules'));
    }

    public function getModuleDetails($moduleId)
    {
        $module = Module::with(['file.folder', 'matkul', 'author', 'lastEditor'])->findOrFail($moduleId);
        
        $olderVersions = Module::with(['file', 'author', 'lastEditor'])
            ->where('matkul_id', $module->matkul_id)
            ->where('file_id', '!=', $module->file_id)
            ->where('active', false)
            ->whereNull('deleted_at')
            ->orderBy('last_edited_at', 'desc')
            ->get();
        
        $deletedVersions = Module::with(['file', 'author', 'lastEditor'])
            ->where('matkul_id', $module->matkul_id)
            ->onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'module' => $module,
            'olderVersions' => $olderVersions,
            'deletedVersions' => $deletedVersions
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'matkul_id' => 'required|exists:matkuls,id',
            'file' => 'required|file|max:102400',
            'workload_hours' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $matkul = Matkul::findOrFail($request->matkul_id);
        
        if (!$matkul->rootFolder) {
            return response()->json(['success' => false, 'message' => 'Matkul does not have a root folder.'], 400);
        }

        $uploadedFile = $request->file('file');
        $originalName = $uploadedFile->getClientOriginalName();
        $extension = $uploadedFile->getClientOriginalExtension();
        $storedName = Str::uuid() . ($extension ? '.' . $extension : '');
        
        $uploadedFile->storeAs('repository', $storedName, 'public');

        $file = Files::create([
            'folder_id' => $matkul->rootFolder->id,
            'original_name' => $originalName,
            'stored_name' => $storedName,
            'mime_type' => $uploadedFile->getMimeType(),
            'size' => $uploadedFile->getSize(),
            'creator_id' => Auth::id(),
        ]);

        $module = Module::create([
            'file_id' => $file->id,
            'matkul_id' => $request->matkul_id,
            'author_id' => Auth::id(),
            'workload_hours' => $request->workload_hours,
            'last_edited_at' => now(),
            'last_edited_by' => Auth::id(),
            'active' => true
        ]);

        return response()->json(['success' => true, 'module' => $module->load(['file', 'matkul', 'author'])]);
    }

    public function update(Request $request, Module $module)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'nullable|file|max:102400',
            'workload_hours' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        if ($request->hasFile('file')) {
            $module->update(['active' => false]);

            $uploadedFile = $request->file('file');
            $originalName = $uploadedFile->getClientOriginalName();
            $extension = $uploadedFile->getClientOriginalExtension();
            $storedName = Str::uuid() . ($extension ? '.' . $extension : '');
            
            $uploadedFile->storeAs('repository', $storedName, 'public');

            $file = Files::create([
                'folder_id' => $module->file->folder_id,
                'original_name' => $originalName,
                'stored_name' => $storedName,
                'mime_type' => $uploadedFile->getMimeType(),
                'size' => $uploadedFile->getSize(),
                'creator_id' => Auth::id(),
            ]);

            $newModule = Module::create([
                'file_id' => $file->id,
                'matkul_id' => $module->matkul_id,
                'author_id' => $module->author_id,
                'workload_hours' => $request->workload_hours,
                'last_edited_at' => now(),
                'last_edited_by' => Auth::id(),
                'active' => true
            ]);

            return response()->json(['success' => true, 'module' => $newModule->load(['file', 'matkul', 'author'])]);
        }

        $module->update([
            'workload_hours' => $request->workload_hours,
            'last_edited_at' => now(),
            'last_edited_by' => Auth::id()
        ]);

        return response()->json(['success' => true, 'module' => $module->load(['file', 'matkul', 'author'])]);
    }

    public function destroy(Module $module)
    {
        $module->delete();
        return response()->json(['success' => true]);
    }
}
