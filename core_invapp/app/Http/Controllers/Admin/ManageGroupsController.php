<?php


namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\UserGroup;

use App\Enums\UserRoles;
use App\Enums\UserGroupStatus;

use App\Mail\SystemEmail;
use App\Filters\UserFilter;
use App\Traits\WrapInTransaction;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;

class ManageGroupsController extends Controller
{
    use WrapInTransaction;

    public function index(UserFilter $filter, $group = null)
    {
        if (!Schema::hasTable('user_groups') && !Schema::hasTable('user_group_term')) {
            return back()->with(['notice' => __('To continue, please install the update and migrate the application database.')]);
        }

        if (empty($group)) {
            $getUserGroup = collect([]);
            $query = User::withoutSuperAdmin()->where('role', '<>', 'admin')->orderBy('id', user_meta('user_order', 'desc'))->filter($filter);
            $userList = $query->paginate(user_meta('user_perpage', 10))->onEachSide(0);
        } else {
            $getUserGroup = UserGroup::findOrFail($group);
            if (blank($getUserGroup)) {
                return redirect()->route('admin.users.manage.groups')->withErrors(['error' => __('Opps! Something went wrong! We are unable to process your request. Please try again later.')]);
            } else {
                $query = $getUserGroup->users()->withoutSuperAdmin()->where('role', '<>', 'admin')->orderBy('id', user_meta('user_order', 'asc'))->filter($filter);
                $userList = $query->paginate(user_meta('user_perpage', 10))->onEachSide(0);
            }
        }

        $countAllUsers = $user_counts = User::withoutSuperAdmin()->where('role', '<>', 'admin')->count();
        $userGroups = UserGroup::orderBy('id', user_meta('userGroup_order', 'desc'))->get();
        return view('admin.user.group.list', compact('userGroups', 'getUserGroup', 'userList', 'countAllUsers'));
    }

    public function addNewGroup(Request $request)
    {
        if ($request->ajax()) {
            return view('admin.user.misc.modal-add-group')->render();
        } else {
            return redirect()->route('admin.users.manage.groups');
        }
    }

    public function saveGroup(Request $request)
    {
        $this->validate($request, [
            'group_label' => 'required|string|min:3|max:190',
            'group_color' => 'nullable|string',
            'group_desc' => 'nullable|string',
            'group_status' => 'nullable'
        ]);

        $data = [
            'label' => strip_tags($request->get('group_label') ?? ''),
            'color' => !empty($request->get('group_color')) ? strip_tags($request->get('group_color')) : 'blue',
            'desc' => !empty($request->get('group_desc')) ? strip_tags($request->get('group_desc')) : null,
            'status' => UserGroupStatus::SHOW
        ];

        $userGroup = new UserGroup();
        $userGroup->fill($data);
        $userGroup->save();

        return response()->json(['url' => route('admin.users.manage.groups', data_get($userGroup, 'id')), 'title' => __('Group Added!'), 'msg' => __('New group has been added successfully.')]);
    }

    public function editGroup(Request $request)
    {
        $groupId = $request->get('gid');
        if ($groupId) {
            $userGroup = UserGroup::find($groupId);
            if (blank($userGroup)) {
                throw ValidationException::withMessages(['id' => __('Invalid Group!')]);
            } else {
                if ($request->ajax()) {
                    return view('admin.user.misc.modal-edit-group', compact('userGroup'))->render();
                } else {
                    return redirect()->route('admin.users.manage.groups');
                }
            }
        } else {
            throw ValidationException::withMessages(['action' => __('Sorry, we are unable to proceed your action.')]);
        }
    }

    public function updateGroup(Request $request, $id)
    {
        $this->validate($request, [
            'group_label' => 'required|string|min:3|max:190',
            'group_color' => 'nullable|string',
            'group_desc' => 'nullable|string',
        ]);

        $data = [
            'label' => strip_tags($request->get('group_label') ?? ''),
            'color' => !empty($request->get('group_color')) ? strip_tags($request->get('group_color')) : 'blue',
            'desc' => !empty($request->get('group_desc')) ? strip_tags($request->get('group_desc')) : null,
        ];

        $userGroup = UserGroup::find($id);
        if (blank($userGroup)) {
            throw ValidationException::withMessages(['action' => __('Sorry, we are unable to proceed your action.')]);
        } else {
            $userGroup->fill($data);
            $userGroup->save();

            return response()->json(['title' => __('Group Updated!'), 'msg' => __('The group has been updated successfully.'), 'reload' => true ]);
        }
    }

    public function deleteGroup(Request $request)
    {
        $check = false;
        $groupId = $request->get('gid');
        if ($groupId) {
            $userGroup = UserGroup::find($groupId);
            if (blank($userGroup)) {
                throw ValidationException::withMessages(['id' => __('Invalid Group!')]);
            } else {
                $check = $this->wrapInTransaction(function ($userGroup) {
                    $userGroup->users()->detach();
                    $userGroup->delete();
                    return true;
                }, $userGroup);

                if ($check) {
                    return response()->json(['url' => route('admin.users.manage.groups'), 'title' => __('Group Removed!'), 'msg' => __('The group has been removed successfully.')]);
                } else {
                    throw ValidationException::withMessages(['action' => __('Opps! Something went wrong! We are unable to process your request. Please try again later.')]);
                }
            }
        } else {
            throw ValidationException::withMessages(['action' => __('Sorry, we are unable to proceed your action.')]);
        }
    }

    public function bulkAction(Request $request)
    {
        $this->validate($request, [
            'action' => 'required|string',
            'users' => 'required|array|min:1',
        ]);

        $action = $request->get('action');
        $users = $request->get('users');

        if ($action && $users) {
            $userQuery = User::whereIn('id', $users)->whereNotIn('role', [UserRoles::SUPER_ADMIN]);

            if (!blank($userQuery)) {
                switch ($action) {
                    case 'assignGroup':
                        $userGroups = UserGroup::WithoutHiddenGroup()->get();
                        return response()->json([
                            'status' => true,
                            'embed' => view('admin.user.misc.modal-bulk-assign', compact('users', 'userGroups'))->render()
                        ]);
                        break;

                    case 'sendEmail':
                        return response()->json([
                            'status' => true,
                            'embed' => view('admin.user.misc.modal-bulk-email', compact('users'))->render()
                        ]);
                        break;
                }
                return response()->json([ 'title' => 'Bulk Updated', 'msg' => __('All the selected users has been :what.', ['what' => __($action)]), 'reload' => true ]);
            }
            return response()->json([ 'type' => 'info', 'msg' => __('Failed to update the selected users.') ]);
        }
        throw ValidationException::withMessages(['invalid' => __('An error occurred. Please try again.')]);
    }

    public function assignBulkGroup(Request $request)
    {
        $this->validate($request, [
            'gid' => 'required|array',
            'users' => 'required'
        ], [
            'gid.required' => __('Please select any group'),
        ]);

        if (is_locked('assign', 'bulk_locked')) {
            throw ValidationException::withMessages(['invalid' => __("Sorry, one of your system administrator is working, please try again after few minutes.")]);
        }

        $time = now()->timestamp;
        upss('bulk_locked_assign', $time);

        $groupID = array_keys($request->get('gid')) ?? [];
        $userID =  is_json($request->get('users')) ? json_decode($request->get('users')) : [];

        $users = User::whereIn('id', $userID)->get();
        $userGroups = UserGroup::whereIn('id', $groupID)->get();
        try {
            if (!blank($users) && !blank($userGroups)) {
                foreach ($users as $user) {
                    $user->user_groups()->sync(array_unique(array_merge($groupID, $user->user_groups->modelKeys())));
                }
            } else {
                upss('bulk_locked_assign', null);
                throw ValidationException::withMessages(['invalid' => __("Sorry, unable to proceed for invalid data format.") . ' ' . __("Please reload the page and try again.")]);
            }
        } catch (\Exception $e) {
            save_msg_log($e->getMessage(), 'send-email-user');
        }

        upss('bulk_locked_assign', null);
        return response()->json(['msg' => __('Users has been assigned!'), 'reload' => true]);
    }

    public function processBulkEmail(Request $request)
    {
        $request->validate([
            'subject' => 'required|string',
            'greeting' => 'nullable|string',
            'message' => 'required|string',
            'params' => 'nullable|in:on'
        ]);

        if (is_locked('email', 'bulk_locked')) {
            throw ValidationException::withMessages(['invalid' => __("Sorry, one of your system administrator is working, please try again after few minutes.")]);
        }

        Session::put('bulk_email_details', []);
        $users =  is_json($request->get('users')) ? json_decode($request->get('users')) : [];
        $data = [
            "subject" => $request->get('subject'),
            "greeting" => $request->get('greeting'),
            "message" => $request->get('message')
        ];

        $data = array_map('strip_tags_map', $data);
        if (isset($data['greeting']) && empty($data['greeting'])) {
            $data['greeting'] = __("Hello");
        }

        if (!empty($request->get('params')) && $request->get('params') == 'on') {
            $data['params'] = $request->get('params');
        }

        Session::put('bulk_email_details', $data);
        return response()->json([
            'status' => true,
            'embed' => view('admin.user.misc.modal-process-emails', ['users' => $users, 'total' => count($users), 'data' => $data])->render()
        ]);
    }

    public function sendBulkEmail(Request $request)
    {
        if (empty($request->get('done')) && is_locked('email', 'bulk_locked')) {
            throw ValidationException::withMessages(['invalid' => __("Sorry, one of your system administrator is working, please try again after few minutes.")]);
        }

        if (gss('bulk_cancel_email') == true) {
            upss('bulk_cancel_email', null);
            upss('bulk_locked_email', null);
            throw ValidationException::withMessages(['invalid' => __("Bulk sending email is cancelled.")]);
        }

        $request->validate([
            'batchs' => 'required',
            'done' => 'nullable',
            'total' => 'nullable',
            'idx' => 'nullable',
        ], [
            'batchs.required' => __("Sorry, unable to proceed for invalid data format.") . ' ' . __("Please reload the page and try again.")
        ]);

        $userID = $request->get('batchs');
        $done = (int) $request->get('done', 0);
        $total = (int) $request->get('total', 0);
        $idx = (int) $request->get('idx', 0);

        if ($done == 0) {
            $time = now()->timestamp;
            upss('bulk_locked_email', $time);
        }

        $user = User::find($userID);
        try {
            if (!blank($user) && in_array($user->role, [UserRoles::USER])) {
                $data = Session::get('bulk_email_details');
                $template = data_get($data, 'params') == 'on' ? 'users.custom-email-params' : 'users.custom-email';
                Mail::to($user->email)->send(new SystemEmail($data, $template));
            }
        } catch (\Exception $e) {
            save_msg_log($e->getMessage(), 'send-email-user');
            upss('bulk_cancel_email', null);
            upss('bulk_locked_email', null);
            throw ValidationException::withMessages(['invalid' => __('Sorry, we are unable to send email to user.')]);
        }
        $done++;

        $left = ($total - $done);
        $progress = (($done / $total) * 100);
        $next = ($left == 0 || $total <= $done) ? false : true;

        if ($left == 0) {
            upss('bulk_locked_email', null);
            upss('bulk_cancel_email', null);
            Session::forget('bulk_email_details');
            $message = __("Emails has been sent to the users.");
        } else {
            $message = __("Sending email to user batch processed.");
        }

        return response()->json([
            'status' => 'success', 'message' => $message, 'idx' => ($idx + 1),
            'done' => $done, 'total' => $total, 'progress' => $progress, 'next' => $next
        ]);
    }

    public function cancelBulkEmail(Request $request)
    {
        $request->validate(['cancel' => 'required|integer|in:1']);

        if ($request->cancel == 1 && is_locked('email', 'bulk_locked')) {
            upss('bulk_cancel_email', true);
        }
    }

    public function showUserDetails(Request $request)
    {
        $userId = $request->get('uid');
        $user = User::WithoutSuperAdmin()->where('role', '<>', 'admin')->find($userId);

        if (blank($user)) {
            throw ValidationException::withMessages(['invalid' => __('Invalid User!')]);
        }

        if ($request->ajax()) {
            return view('admin.user.group.user-details', compact('user'))->render();
        } else {
            return redirect()->route('admin.users.manage.groups');
        }
    }

    public function showChangeGroup(Request $request)
    {
        $type = $request->get('type');
        if (!in_array($type, ['assign', 'change'])) {
            throw ValidationException::withMessages(['error' => __('Sorry, we are unable to proceed your action.')]);
        }

        $userId = $request->get('uid');
        $user = User::WithoutSuperAdmin()->where('role', '<>', 'admin')->find($userId);

        if (blank($user)) {
            throw ValidationException::withMessages(['invalid' => __('Invalid User!')]);
        }

        $userGroups = ($type == 'change') ? UserGroup::all() : UserGroup::WithoutHiddenGroup()->get();
        if (blank($userGroups)) {
            throw ValidationException::withMessages(['invalid' => __('There are no available groups!')]);
        }

        if ($request->ajax()) {
            return view('admin.user.misc.modal-change-group', compact('user', 'userGroups', 'type'))->render();
        } else {
            return ($type == 'change') ? redirect()->route('admin.users.manage.groups') : redirect()->route('admin.users');
        }
    }

    public function changeGroup(Request $request)
    {
        $this->validate($request, [
            'uid' => 'required',
            'gid' => 'nullable|array',
            'form_type' => 'required|in:assign,change',
        ], [
            'gid.required' => __('Please select any group'),
        ]);

        $msg = '';
        $userId = $request->get('uid');
        $groupId = (!empty($request->get('gid')) && is_array($request->get('gid'))) ? array_keys($request->get('gid')) : null;
        $type = $request->get('form_type');

        $user = User::WithoutSuperAdmin()->where('role', '<>', 'admin')->find($userId);
        if (blank($user)) {
            throw ValidationException::withMessages(['invalid' => __('Invalid User!')]);
        }

        if (!empty($groupId)) {
            $userGroup = UserGroup::whereIn('id', $groupId)->get();
            if (blank($userGroup)) {
                throw ValidationException::withMessages(['invalid' => __('Invalid Group!')]);
            }
            $user->user_groups()->sync($groupId);
            $msg = ($type == 'change') ? __('This user group has been updated.') : __('This user has been assigned to groups.');
        } else {
            $user->user_groups()->detach();
            $msg = __('No group is selected for this user.');
        }

        return response()->json([ 'title' => __('User Group Updated!'), 'msg' => $msg, 'reload' => true ]);
    }

    public function removeUserGroup(Request $request)
    {
        $userId = $request->get('uid');
        $groupId = $request->get('gid');

        if ($userId && $groupId) {
            $user = User::WithoutSuperAdmin()->where('role', '<>', 'admin')->find($userId);
            if (blank($user)) {
                throw ValidationException::withMessages(['invalid' => __('Invalid User!')]);
            }

            $userGroup = UserGroup::find($groupId);
            if (blank($userGroup)) {
                throw ValidationException::withMessages(['id' => __('Invalid Group!')]);
            }

            $userGroup->users()->detach($userId);
            return response()->json([ 'title' => __('User Removed!'), 'msg' => __('This user has been removed from the group.'), 'reload' => true ]);
        } else {
            throw ValidationException::withMessages(['action' => __('Sorry, we are unable to proceed your action.')]);
        }
    }

    public function sendEmailAll(Request $request)
    {
        $groupId = $request->get('gid');
        if ($groupId) {
            $userGroup = UserGroup::find($groupId);
            if (blank($userGroup)) {
                throw ValidationException::withMessages(['id' => __('Invalid Group!')]);
            }

            if (blank($userGroup->users)) {
                throw ValidationException::withMessages(['invalid' => __('There is no user in this group!')]);
            }

            $users = $userGroup->users->pluck('id')->toArray();
            if ($request->ajax()) {
                return view('admin.user.misc.modal-bulk-email', compact('users'))->render();
            } else {
                return redirect()->route('admin.users.manage.groups');
            }
        } else {
            throw ValidationException::withMessages(['action' => __('Sorry, we are unable to proceed your action.')]);
        }
    }
}
