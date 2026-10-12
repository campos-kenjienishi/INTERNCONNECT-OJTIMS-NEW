<?php

namespace App\Http\Controllers;

use App\Exceptions\UnsafeUploadException;
use App\Services\DocumentAccessService;
use App\Services\PrivateDocumentStorage;
use App\Services\SecureDocumentResponse;
use App\Services\SecureUploadService;
use App\Services\UploadSecurityRecorder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Carbon\Carbon;
use App\Models\User;
use App\Models\Classes;
use App\Models\Company;
Use App\Mail\TemporaryPasswordNotification;
use App\Models\Courses;
use App\Models\MOAUpload;
use App\Models\Professor;
use App\Mail\SendFileNotif;
use App\Mail\SendTempNotif;
use App\Models\CoursePerSY;
use Illuminate\Support\Str;
use App\Models\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use App\Helpers\AuditLogger;
use Illuminate\Database\Eloquent\Builder;

class FileController extends Controller
{
    public function __construct(
        private readonly SecureUploadService $secureUploads,
        private readonly PrivateDocumentStorage $documents,
        private readonly DocumentAccessService $documentAccess,
        private readonly SecureDocumentResponse $documentResponse,
        private readonly UploadSecurityRecorder $securityEvidence
    ) {
    }

    protected function getLoggedInUser()
    {
        if (!Session::has('loginId')) {
            return null;
        }

        return User::where('id', Session::get('loginId'))->first();
    }

    protected function canManageUploadedFile(User $user, UploadedFile $file): bool
    {
        return $this->managedUploadedFilesQuery($user)
            ->where('id', $file->id)
            ->exists();
    }

    protected function managedUploadedFilesQuery(User $user): Builder
    {
        $query = UploadedFile::query();

        if (Schema::hasColumn('uploaded_files', 'uploader_user_id')) {
            $query->where('uploader_user_id', $user->id);
        } else {
            $query->where('uploader_name', $user->full_name);
        }

        if ((int) $user->role === 1 && Schema::hasColumn('uploaded_files', 'class_id')) {
            $query->where(function ($scope) {
                $scope->whereNull('class_id')
                    ->orWhere('class_id', 0);
            });
        }

        return $query;
    }

    public function uploadpage()
    {
        $user = $this->getLoggedInUser();

        if (!$user) {
            return response()->view('errors.something-went-wrong', ['statusCode' => 401], 401);
        }

        $data = $this->managedUploadedFilesQuery($user)
            ->latest()
            ->get();

        return view('ojtCoordinator.upload', compact('data', 'user'));
    }

    public function uploadfile(Request $request)
    {
        $user = array();
        if (Session::has('loginId')) {
            $user = User::where('id','=', Session::get('loginId'))->first();
        }

        $request->validate([
            'file' => 'required|mimes:doc,docx,pdf,xls,xlsx|max:30720',
            'name' => 'required|string|max:255',
        ]);

        try {
            $storedUpload = $this->secureUploads->store(
                $request->file('file'),
                'template',
                PrivateDocumentStorage::TEMPLATES
            );
        } catch (UnsafeUploadException $exception) {
            return back()->withInput()->withErrors(['file' => $exception->getMessage()]);
        }

        $data = new UploadedFile();
        $data->file = $storedUpload->filename;
        $data->name = $request->name;
        $data->uploader_name = $user->full_name;
        if (Schema::hasColumn('uploaded_files', 'uploader_user_id')) {
            $data->uploader_user_id = $user->id;
        }
        if (Schema::hasColumn('uploaded_files', 'class_id')) {
            $data->class_id = $request->filled('class_id') ? $request->class_id : null;
        }
        try {
            $saved = $data->save();
        } catch (\Throwable $exception) {
            $this->documents->delete(PrivateDocumentStorage::TEMPLATES, $storedUpload->filename);
            throw $exception;
        }

        if (!$saved) {
            $this->documents->delete(PrivateDocumentStorage::TEMPLATES, $storedUpload->filename);

            return back()->withInput()->with('fail', 'The file could not be saved. Please try again.');
        }

        $this->securityEvidence->link($storedUpload->securityRecordId, $data, $user->id ?? null);

        AuditLogger::log(
            'Upload Templates',
            'File Upload',
            'Uploaded a new file: ' . $data->name,
            $user->id ?? null,
            null,
            ['file' => $data->file, 'name' => $data->name]
        );

        return redirect()->back()->with('success', 'File uploaded successfully!');
    }


    public function show(Request $request)
    {
        return $this->uploadpage();
    }

    public function download(Request $request, $file)
    {   
        $user = $this->getLoggedInUser();
        $fileRecord = UploadedFile::where('file', $file)->first();

        abort_unless($user && $fileRecord && $this->documentAccess->canAccessTemplate($user, $fileRecord), 404);

        $filePath = $this->documents->resolve(PrivateDocumentStorage::TEMPLATES, $fileRecord->file);
        if ($filePath === null) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $ext = pathinfo($filePath, PATHINFO_EXTENSION);

        if ($fileRecord && !empty($fileRecord->name)) {
            $baseName = pathinfo($fileRecord->name, PATHINFO_FILENAME);
            $cleanName = Str::slug($baseName, '_');
            $downloadName = ($cleanName ?: 'file') . '.' . $ext;
        } else {
            $downloadName = $file;
        }

        return $this->documentResponse->download($filePath, $downloadName);
    }


    public function view($id)
    {
        $user = $this->getLoggedInUser();
        if (!$user) {
            return response()->view('errors.something-went-wrong', ['statusCode' => 401], 401);
        }

        $data = $this->managedUploadedFilesQuery($user)->find($id);
        if (!$data) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return view('ojtCoordinator.view',compact('data'));
    }

    

    public function remove($id)
    {
        $user = $this->getLoggedInUser();
        if (!$user) {
            return response()->view('errors.something-went-wrong', ['statusCode' => 401], 401);
        }

        $data = $this->managedUploadedFilesQuery($user)->find($id);

        if (!$data) {
            return redirect()->back()->with('error', 'File not found.');
        }

        if (!empty($data->file)) {
            $this->documents->delete(PrivateDocumentStorage::TEMPLATES, $data->file);
        }
    
        $data->delete();
        AuditLogger::log(
            'Upload Templates',
            'File Delete',
            'Deleted file: ' . $data->name,
            $user->id ?? null,
            ['file' => $data->file, 'name' => $data->name], // old values
            null // new values
        );
    
        return redirect()->back();
    }

    public function removeProfessorTemplate($id)
    {
        $user = $this->getLoggedInUser();

        if (!$user || (int) $user->role !== 2) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $file = UploadedFile::find($id);
        if (!$file) {
            return redirect()->back()->with('error', 'Template not found.');
        }

        // Professors can only remove their own room-scoped templates.
        if (!$this->canManageUploadedFile($user, $file)) {
            return redirect()->back()->with('error', 'You can only remove your own templates.');
        }

        if (!Schema::hasColumn('uploaded_files', 'class_id') || empty($file->class_id)) {
            return redirect()->back()->with('error', 'Only room templates can be removed here.');
        }

        $oldValues = [
            'id' => $file->id,
            'file' => $file->file,
            'name' => $file->name,
            'class_id' => $file->class_id,
        ];

        if (!empty($file->file)) {
            $this->documents->delete(PrivateDocumentStorage::TEMPLATES, $file->file);
        }

        $file->delete();

        AuditLogger::log(
            'Upload Templates',
            'File Delete',
            'Professor removed room template: ' . $oldValues['name'],
            $user->id,
            $oldValues,
            null
        );

        return redirect()->back()->with('success', 'Template removed successfully.');
    }
    
    public function search(Request $request){
        $user = $this->getLoggedInUser();
        if (!$user) {
            return response()->view('errors.something-went-wrong', ['statusCode' => 401], 401);
        }

        if($request->search){
            $searchFile = $this->managedUploadedFilesQuery($user)
                ->where('name', 'LIKE', '%'.$request->search. '%')
                ->latest()
                ->paginate(15);
            return view('ojtCoordinator.search',compact('searchFile'));
        }

        else{
            return redirect()->back()->with('message', 'Empty Search');
            
        }
    }


    public function sendFiless(Request $request){
       
        $request->validate([
            'email' => 'required|email',
            'file_id', // Make sure the file exists in the 'moa_uploads' table
        ]);
    
        $fileId = $request->input('file_id');
        $file = UploadedFile::find($fileId);
    
        if (!$file) {
            return back()->with('error', 'File not found.');
        }
    
        $attachmentPath = $this->documents->resolve(PrivateDocumentStorage::TEMPLATES, $file->file);

        if ($attachmentPath === null) {
            return back()->with('error', 'File not found.');
        }
    
        try {
            Mail::to($request->email)->send(new SendTempNotif($attachmentPath, $file->file));
    
            return back()->with('success', 'Email sent with file attachment.');
        } catch (\Exception $e) {
            Log::error('Email sending error: ' . $e->getMessage());
            return back()->with('error', 'Email sending failed.');
        }
    
    }

    public function downloadFile($file)
    {
        $user = $this->getLoggedInUser();
        $fileRecord = UploadedFile::where('file', $file)->first();

        abort_unless($user && $fileRecord && $this->documentAccess->canAccessTemplate($user, $fileRecord), 404);

        $filePath = $this->documents->resolve(PrivateDocumentStorage::TEMPLATES, $fileRecord->file);
        if ($filePath === null) {
            return response()->json(['message' => 'File not found'], 404);
        }

        if ($fileRecord) {
            // Check if the file is still valid
            if ($fileRecord->valid_until && now()->gt($fileRecord->valid_until)) {
                // File has expired, return a response indicating that
                return response()->json(['message' => 'File has expired'], 403);
            }
        }

        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        if ($fileRecord && !empty($fileRecord->name)) {
            $baseName = pathinfo($fileRecord->name, PATHINFO_FILENAME);
            $cleanName = Str::slug($baseName, '_');
            $downloadName = ($cleanName ?: 'file') . '.' . $ext;
        } else {
            $downloadName = $file;
        }

        return $this->documentResponse->download($filePath, $downloadName);
    }

    public function viewFile($file)
    {
        $user = $this->getLoggedInUser();
        $fileRecord = UploadedFile::where('file', $file)->first();

        abort_unless($user && $fileRecord && $this->documentAccess->canAccessTemplate($user, $fileRecord), 404);

        $filePath = $this->documents->resolve(PrivateDocumentStorage::TEMPLATES, $fileRecord->file);

        if ($filePath === null) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if ($ext !== 'pdf') {
            return $this->documentResponse->download($filePath, $fileRecord->name ?: $fileRecord->file);
        }

        return $this->documentResponse->inlinePdf($filePath, $fileRecord->name ?: $fileRecord->file);
    }
}
