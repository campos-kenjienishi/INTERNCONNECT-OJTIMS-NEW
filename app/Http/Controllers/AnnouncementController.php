<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Classes;
use App\Mail\SendFileNotif;
Use App\Mail\TemporaryPasswordNotification;
use App\Models\Company;
use App\Models\Courses;
use App\Models\MOAUpload;
use App\Models\Professor;
use App\Models\CoursePerSY;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Models\Announcements;
use App\Helpers\AuditLogger;

class AnnouncementController extends Controller
{
    protected function ownedAnnouncementsQuery(User $user)
    {
        $query = Announcements::query();

        if (Schema::hasColumn('announcements', 'announcer_user_id')) {
            $query->where('announcer_user_id', $user->id);
        } else {
            $query->where('announcer', $user->full_name);
        }

        return $query;
    }

    public function announcement(Request $request)
    {
        if (!Session::has('loginId')) {
            return redirect('/login');
        }

        $user = User::where('id', Session::get('loginId'))->first();

        if (!$user || !in_array((string) $user->role, ['1', '2'], true)) {
            abort(403, 'Only coordinators and professors can post announcements.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'course' => ['nullable', 'string', 'max:255'],
            'room' => ['nullable', 'string', 'max:255'],
        ]);

        $audience = 'all_students';
        $targetCourse = null;
        $targetRoom = null;

        if ((string) $user->role === '2') {
            $class = Classes::where('adviser_name', $user->full_name)
                ->where('course', $request->input('course'))
                ->where('room', $request->input('room'))
                ->first();

            if (!$class) {
                abort(403, 'You can only post announcements to your own class.');
            }

            $audience = 'class';
            $targetCourse = $class->course;
            $targetRoom = $class->room;
        }

        $data = new Announcements();

        $data->title=$request->title;
        $data->content=$request->content;
        $data->announcer=$user->full_name;
        if (Schema::hasColumn('announcements', 'announcer_user_id')) {
            $data->announcer_user_id = $user->id;
        }

        if (Schema::hasColumn('announcements', 'audience')) {
            $data->audience = $audience;
        }

        if (Schema::hasColumn('announcements', 'target_course')) {
            $data->target_course = $targetCourse;
        }

        if (Schema::hasColumn('announcements', 'target_room')) {
            $data->target_room = $targetRoom;
        }

        $data->save();

        AuditLogger::log(
            'Announcements',
            'Create',
            'Posted announcement: ' . $data->title . ($audience === 'class' ? ' for ' . $targetCourse . ' - ' . $targetRoom : ' for all students'),
            $user->id
        );

        return redirect()->back();

    }

    public function destroy($id)
    {
        if (!Session::has('loginId')) {
            return redirect('/login');
        }

        $user = User::where('id', Session::get('loginId'))->first();

        if (!$user || !in_array((string) $user->role, ['1', '2'], true)) {
            abort(403, 'Only coordinators and professors can delete announcements.');
        }

        $announcement = $this->ownedAnnouncementsQuery($user)
            ->where('id', $id)
            ->firstOrFail();

        $title = $announcement->title;
        $announcement->delete();

        AuditLogger::log(
            'Announcements',
            'Delete',
            'Deleted announcement: ' . $title,
            $user->id
        );

        return redirect()->back()->with('success', 'Announcement deleted successfully.');
    }

    public function update(Request $request, $id)
    {
        if (!Session::has('loginId')) {
            return redirect('/login');
        }

        $user = User::where('id', Session::get('loginId'))->first();

        if (!$user || !in_array((string) $user->role, ['1', '2'], true)) {
            abort(403, 'Only coordinators and professors can edit announcements.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $announcement = $this->ownedAnnouncementsQuery($user)
            ->where('id', $id)
            ->firstOrFail();

        $oldTitle = $announcement->title;
        $announcement->title = $request->title;
        $announcement->content = $request->content;
        $announcement->save();

        AuditLogger::log(
            'Announcements',
            'Update',
            'Updated announcement: ' . $oldTitle . ' -> ' . $announcement->title,
            $user->id
        );

        return redirect()->back()->with('success', 'Announcement updated successfully.');
    }
    
}
