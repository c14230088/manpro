<?php

namespace App\Http\Controllers;

use App\Models\Files;
use App\Models\Matkul;
use App\Models\Folders;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RepositoryController extends Controller
{
    public function index(Request $request)
    {
        $folderId = $request->get('folder');
        
        if (!$folderId) {
            $rootFolder = Folders::whereNull('parent_id')->first();
            $folderId = $rootFolder ? $rootFolder->id : null;
        }
        
        $currentFolder = $folderId ? Folders::findOrFail($folderId) : null;
        
        $folders = Folders::with('creator')
            ->where('parent_id', $folderId)
            ->orderBy('name')
            ->get();
        
        $files = Files::with('creator')
            ->where('folder_id', $folderId)
            ->orderBy('original_name')
            ->get();

        $breadcrumbs = $this->getBreadcrumbs($currentFolder);

        return view('admin.repository', compact('folders', 'files', 'currentFolder', 'breadcrumbs'));
    }

    public function createFolder(Request $request)
    {
        $data = $request->only(['name', 'parent_id']);
        
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first(), 'errors' => $validator->errors()], 422);
        }

        $parentPath = '/';
        if ($data['parent_id']) {
            $parent = Folders::findOrFail($data['parent_id']);
            $parentPath = $parent->full_path . '/';
        }

        $fullPath = $parentPath . $data['name'];
        
        if (Folders::where('full_path', $fullPath)->exists()) {
            return response()->json(['success' => false, 'message' => 'A folder with this name already exists in this location.'], 409);
        }

        $folder = Folders::create([
            'name' => $data['name'],
            'parent_id' => $data['parent_id'],
            'full_path' => $fullPath,
            'is_root' => false,
            'creator_id' => Auth::user()->id,
        ]);

        return response()->json(['success' => true, 'folder' => $folder]);
    }

    public function uploadFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'folder_id' => 'required|exists:folders,id',
            'file' => 'required|file|max:102400'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first(), 'errors' => $validator->errors()], 422);
        }

        $uploadedFile = $request->file('file');
        $originalName = $uploadedFile->getClientOriginalName();
        
        $existingFile = Files::where('folder_id', $request->folder_id)
            ->where('original_name', $originalName)
            ->first();
        
        if ($existingFile) {
            return response()->json(['success' => false, 'message' => 'A file with this name already exists in this folder.'], 409);
        }
        
        $extension = $uploadedFile->getClientOriginalExtension();
        $storedName = Str::uuid() . ($extension ? '.' . $extension : '');
        
        $uploadedFile->storeAs('repository', $storedName, 'public');

        $file = Files::create([
            'folder_id' => $request->folder_id,
            'original_name' => $originalName,
            'stored_name' => $storedName,
            'mime_type' => $uploadedFile->getMimeType(),
            'size' => $uploadedFile->getSize(),
            'creator_id' => Auth::user()->id,
        ]);

        return response()->json(['success' => true, 'file' => $file]);
    }

    public function renameFolder(Request $request, Folders $folder)
    {
        if ($folder->is_root) {
            $matkul = Matkul::where('root_folder_id', $folder->id)->first();
            if ($matkul) {
                return response()->json(['success' => false, 'message' => 'Cannot rename Matkul root folder. Please rename from Matkul management.'], 403);
            }
        }

        $protectedFolders = Folders::whereIn('name', ['Matkuls', 'Modules'])
            ->whereHas('parent', fn($q) => $q->whereNull('parent_id'))
            ->pluck('id');
        
        if ($protectedFolders->contains($folder->id)) {
            return response()->json(['success' => false, 'message' => 'Cannot rename protected system folder.'], 403);
        }

        $data = $request->only(['name']);
        
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $parentPath = $folder->parent_id ? Folders::find($folder->parent_id)->full_path . '/' : '/';
        $folder->update([
            'name' => $data['name'],
            'full_path' => $parentPath . $data['name']
        ]);

        return response()->json(['success' => true, 'folder' => $folder]);
    }

    public function renameFile(Request $request, Files $file)
    {
        $data = $request->only(['name']);
        
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $file->update(['original_name' => $data['name']]);

        return response()->json(['success' => true, 'file' => $file]);
    }

    public function deleteFolder(Folders $folder)
    {
        if ($folder->is_root) {
            $matkul = Matkul::where('root_folder_id', $folder->id)->first();
            if ($matkul) {
                return response()->json(['success' => false, 'message' => 'Cannot delete Matkul root folder. Please delete from Matkul management.'], 403);
            }
        }

        $protectedFolders = Folders::whereIn('name', ['Matkuls', 'Modules'])
            ->whereHas('parent', fn($q) => $q->whereNull('parent_id'))
            ->pluck('id');
        
        if ($protectedFolders->contains($folder->id)) {
            return response()->json(['success' => false, 'message' => 'Cannot delete protected system folder.'], 403);
        }

        $folder->delete();
        return response()->json(['success' => true]);
    }

    public function deleteFile(Files $file)
    {
        Storage::disk('public')->delete('repository/' . $file->stored_name);
        $file->delete();
        return response()->json(['success' => true]);
    }

    public function moveFolder(Request $request, Folders $folder)
    {
        if ($folder->is_root) {
            $matkul = Matkul::where('root_folder_id', $folder->id)->first();
            if ($matkul) {
                return response()->json(['success' => false, 'message' => 'Cannot move Matkul root folder.'], 403);
            }
        }

        $protectedFolders = Folders::whereIn('name', ['Matkuls', 'Modules'])
            ->whereHas('parent', fn($q) => $q->whereNull('parent_id'))
            ->pluck('id');
        
        if ($protectedFolders->contains($folder->id)) {
            return response()->json(['success' => false, 'message' => 'Cannot move protected system folder.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'parent_id' => 'required|exists:folders,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $rootFolder = Folders::whereNull('parent_id')->first();
        if ($protectedFolders->contains($folder->id) && $request->parent_id != $rootFolder->id) {
            return response()->json(['success' => false, 'message' => 'Protected folders can only be in root directory.'], 403);
        }

        $exists = Folders::where('parent_id', $request->parent_id)
            ->where('name', $folder->name)
            ->where('id', '!=', $folder->id)
            ->exists();
        
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'A folder with this name already exists in the destination.'], 409);
        }

        $folder->update(['parent_id' => $request->parent_id]);
        return response()->json(['success' => true]);
    }

    public function moveFile(Request $request, Files $file)
    {
        $validator = Validator::make($request->all(), [
            'parent_id' => 'required|exists:folders,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $exists = Files::where('folder_id', $request->parent_id)
            ->where('original_name', $file->original_name)
            ->where('id', '!=', $file->id)
            ->exists();
        
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'A file with this name already exists in the destination.'], 409);
        }

        $file->update(['folder_id' => $request->parent_id]);
        return response()->json(['success' => true]);
    }

    public function downloadFile(Files $file)
    {
        $path = storage_path('app/public/repository/' . $file->stored_name);
        
        if (!file_exists($path)) {
            return response()->json(['success' => false, 'message' => 'File not found'], 404);
        }
        
        return response()->download($path, $file->original_name);
    }

    public function getFolderFiles(Folders $folder)
    {
        $files = Files::where('folder_id', $folder->id)->get();
        return response()->json(['files' => $files]);
    }

    private function getBreadcrumbs($folder)
    {
        $breadcrumbs = [];
        while ($folder) {
            array_unshift($breadcrumbs, $folder);
            $folder = $folder->parent;
        }
        return $breadcrumbs;
    }
}
