@php
    use \App\Enums\UserStatus as uStatus;
    use \App\Enums\UserRoles as uRole;

    $searchFormAction = blank($getUserGroup) ? route('admin.users.manage.groups') : route('admin.users.manage.groups', data_get($getUserGroup, 'id'));
@endphp

@extends('admin.layouts.master')
@section('title', __('Manage Groups'))

@section('content')
<div class="nk-content-body">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">{{ __('Manage Groups') }}</h3>
                <p>{{ __('List of groups that you can manage / edit.') }}</p>
            </div>
            <div class="nk-block-head-content">
                <ul class="nk-block-tools g-3">
                    <li>
                        <a href="javascript:void(0)" class="btn btn-primary d-none d-sm-inline-flex usr-group" data-method="new">
                            <em class="icon ni ni-plus"></em><span>{{ __('New Group') }}</span>
                        </a>
                        <a href="javascript:void(0)" class="btn btn-icon btn-primary d-inline-flex d-sm-none usr-group" data-method="new">
                            <em class="icon ni ni-plus"></em>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="nk-block">
        <div class="card card-bordered card-stretch">
            <div class="card-aside-wrap">
                <div class="card-aside card-aside-left user-list-aside toggle-slide toggle-slide-left toggle-break-lg toggle-screen-lg" data-content="userListAside" data-toggle-screen="lg" data-toggle-overlay="true" data-toggle-body="true">
                    <div class="nk-wg-head">
                        <h6 class="mb-0">{{ __("Manage Group") }}</h6>
                        <a href="javascript:void(0)" class="link link-primary usr-group" data-method="new"><em class="icon ni ni-plus"></em> <span>{{ __("Add") }}</span></a>
                    </div>
                    <ul class="nk-wg-menu">
                        <li>
                            <a href="{{ route('admin.users.manage.groups') }}" class="nk-wg-menu-item nk-wg-menu-item-lead{{ blank($getUserGroup) ? ' active' : '' }}">
                                <span class="nk-wg-menu-icon"><em class="icon ni ni-users-fill"></em></span>
                                <span class="nk-wg-menu-text">{{ __("All Users") }}</span>
                            </a>
                            <span class="badge rounded-pill badge-primary">{{ $countAllUsers }}</span>
                        </li>
                        @foreach($userGroups as $group)
                        <li>
                            <a href="{{ route('admin.users.manage.groups', data_get($group, 'id')) }}" class="nk-wg-menu-item{{ data_get($getUserGroup, 'id') == data_get($group, 'id') ? ' active' : '' }}">
                                <span class="nk-wg-menu-icon"><span class="dot dot-xl dot-label bg-{{ data_get($group, 'color') }}"></span></span>
                                <span class="nk-wg-menu-text">{{ __(ucfirst(data_get($group, 'label'))) }}</span>
                            </a>
                            <span class="badge badge-dim rounded-pill badge-gray">{{ $group->users()->count() }}</span>
                            <div class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><em class="icon ni ni-more-v"></em></a>
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                    <ul class="link-list-opt no-bdr">
                                        <li><a href="javascript:void(0)" class="usr-group" data-method="edit" data-gid="{{ data_get($group, 'id') }}"><span>{{ __("Edit Label") }}</span></a></li>
                                        <li><a href="javascript:void(0)" class="usr-group" data-method="remove" data-gid="{{ data_get($group, 'id') }}"><span>{{ __("Remove Label") }}</span></a></li>
                                        <li><a href="javascript:void(0)" class="usr-group" data-method="sendEmail" data-gid="{{ data_get($group, 'id') }}"><span>{{ __("Send Email to All") }}</span></a></li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        @endforeach
                        
                    </ul>                    
                </div>
                <div class="card-content">
                    <div class="nk-wg-head position-relative card-tools-toggle">
                        <div class="card-title-group w-100">
                            <div class="card-tools d-flex gx-4">
                                <div class="d-lg-none">
                                    <button class="toggle btn btn-sm btn-dim btn-outline-light btn-white btn-icon" data-target="userListAside" ><em class="icon ni ni-menu-left"></em></button>
                                </div>
                                <div class="form-inline flex-nowrap gx-3">
                                    <div class="form-wrap w-150px">
                                        <select id="bulk-action" class="form-select form-select-sm" data-search="off" data-ui="sm" data-placeholder="Bulk Action">
                                            <option value="0">{{ __('Bulk Action') }}</option>
                                            <option value="assignGroup">{{ __('Assign to Group') }}</option>
                                            <option value="sendEmail">{{ __('Send Email') }}</option>
                                        </select>
                                    </div>
                                    <div class="btn-wrap">
                                        <span class="d-none d-md-block"><button class="bulk-apply btn btn-sm btn-dim btn-outline-light disabled" disabled>{{ __('Apply') }}</button></span>
                                        <span class="d-md-none"><button class="bulk-apply btn btn-sm btn-dim btn-outline-light btn-icon disabled" disabled><em class="icon ni ni-arrow-right"></em></button></span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-tools mr-n1">
                                <ul class="btn-toolbar gx-1">
                                    <li>
                                        <a href="javascript:void(0)" class="btn btn-icon search-toggle toggle-search" data-target="search"><em class="icon ni ni-search"></em></a>
                                    </li>
                                    <li class="btn-toolbar-sep"></li>
                                    <li>
                                        <div class="dropdown">
                                            <a href="javascript:void(0)" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="dropdown">
                                                <em class="icon ni ni-setting"></em></a>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-xs dropdown-menu-right">
                                                <ul class="link-check">
                                                    <li><span>{{ __('Show') }}</span></li>
                                                    @foreach(config('investorm.pgtn_pr_pg') as $item)
                                                    <li class="update-meta{{ (user_meta('user_perpage', '10') == $item) ? ' active' : '' }}">
                                                        <a href="#" data-value="{{ $item }}" data-meta="perpage" data-type="user">{{ __(ucfirst($item)) }}</a>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                                <ul class="link-check">
                                                    <li><span>{{ __('Order') }}</span></li>
                                                    @foreach(config('investorm.pgtn_order') as $item)
                                                    <li class="update-meta{{ (user_meta('user_order', 'desc') == $item) ? ' active' : '' }}">
                                                        <a href="#" data-value="{{ $item }}" data-meta="order" data-type="user">{{ __(strtoupper($item)) }}</a>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                                <ul class="link-check">
                                                    <li><span>{{ __('Density') }}</span></li>
                                                    @foreach(config('investorm.pgtn_dnsty') as $item)
                                                    <li class="update-meta{{ (user_meta('user_display', 'regular') == $item) ? ' active' : '' }}">
                                                        <a href="#" data-value="{{ $item }}" data-meta="display" data-type="user">{{ __(ucfirst($item)) }}</a>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                    
                                </ul>
                            </div>
                        </div>
                        <div class="card-search search-wrap{{ (request()->get('query', false)) ? ' active' : '' }}" data-search="search">
                            <div class="card-body">
                                <form action="{{ $searchFormAction }}">
                                    <div class="search-content">
                                        <a href="javascript:void(0)" class="search-back btn btn-icon toggle-search{{ (request()->get('query', false)) ? ' active' : '' }}" data-target="search"><em class="icon ni ni-arrow-left"></em></a>
                                        <input name="query" type="text" value="{{ request()->get('query', '') }}" class="form-control border-transparent form-focus-none" placeholder="{{ __('Search by user or email') }}">
                                        <button type="submit" class="search-submit btn btn-icon"><em class="icon ni ni-search"></em></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @if(filled($userList))
                    <div class="card-inner p-0 border-bottom border-light">
                        <div class="nk-tb-list nk-tb-ulist{{ user_meta('user_display') == 'compact' ? ' is-compact': '' }}">
                            <div class="nk-tb-item nk-tb-head">
                                <div class="nk-tb-col nk-tb-col-check">
                                    <div class="custom-control custom-control-sm custom-checkbox notext">
                                        <input type="checkbox" class="custom-control-input qs-checkbox" id="choose-by-all">
                                        <label class="custom-control-label" for="choose-by-all"></label>
                                    </div>
                                </div>
                                <div class="nk-tb-col"><span class="sub-text">{{ __('User') }}</span></div>
                                <div class="nk-tb-col tb-col-sm"><span class="sub-text">{{ __("Groups") }}</span></div>
                                <div class="nk-tb-col"><span class="sub-text">{{ __("Status") }}</span></div>
                                <div class="nk-tb-col nk-tb-col-tools text-right">&nbsp;</div>
                            </div>

                            @foreach($userList as $user)
                            <div class="nk-tb-item">
                                <div class="nk-tb-col nk-tb-col-check">
                                    <div class="custom-control custom-control-sm custom-checkbox notext">
                                        <input type="checkbox" class="custom-control-input qs-checkbox-i" data-uid="{{ data_get($user, 'id') }}" id="qs-checkbox-uid-{{ data_get($user, 'id') }}">
                                        <label class="custom-control-label" for="qs-checkbox-uid-{{ data_get($user, 'id') }}"></label>
                                    </div>
                                </div>
                                <div class="nk-tb-col">
                                    <div class="user-card">
                                        <div class="user-avatar xs bg-primary">
                                            <span>{!! user_avatar($user, 'xs') !!}</span>
                                        </div>
                                        <div class="user-name">
                                            <span class="tb-lead">{{ data_get($user, 'name') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="nk-tb-col tb-col-sm">
                                    @if ($user->user_groups()->count() > 0)
                                    <ul class="g-1">
                                        @php $i = 0; @endphp
                                        @foreach ($user->user_groups as $usrGroup)
                                        @if ($i < 2)
                                        <li class="btn-group">
                                            <span class="badge badge-xs badge-dim text-{{ data_get($usrGroup, 'color') }}">{{ __(ucfirst(data_get($usrGroup, 'label'))) }}</span>
                                        </li>
                                        @endif
                                        @php $i++; @endphp
                                        @endforeach
                                        @if ($i > 2)
                                        <li class="btn-group">
                                            <span class="fs-11px">+ {{ $i - 2 }}</span>
                                        </li>
                                        @endif
                                    </ul>
                                    @endif
                                </div>

                                <div class="nk-tb-col">
                                    <span class="tb-status u-status-{{ data_get($user, 'id') . css_state(data_get($user, 'status'), 'text') }}">{{ __(ucfirst(data_get($user, 'status'))) }}</span>
                                </div>
                                <div class="nk-tb-col nk-tb-col-tools">
                                    <ul class="nk-tb-actions gx-2">
                                        <li class="nk-tb-action-hidden">
                                            <a href="javascript:void(0)" class="btn btn-sm btn-icon btn-trigger send-email" data-uid="{{ data_get($user, 'id') }}" data-toggle="tooltip" data-placement="top" title="{{ __('Send Email') }}">
                                                <em class="icon ni ni-mail-fill"></em>
                                            </a>
                                        </li>
                                        <li>
                                            <div class="drodown">
                                                <a href="javascript:void(0)" class="btn btn-sm btn-icon btn-trigger dropdown-toggle" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <ul class="link-list-opt no-bdr">
                                                        @if(data_get($user, 'status') !== uStatus::INACTIVE)
                                                        <li><a href="javascript:void(0)" class="user-group-action" data-method="userDetails" data-uid="{{ data_get($user, 'id') }}"><em class="icon ni ni-user-alt"></em><span>{{ __('View Details') }}</span></a></li>
                                                        @endif

                                                        <li><a href="javascript:void(0)" class="send-email" data-uid="{{ data_get($user, 'id') }}"><em class="icon ni ni-mail"></em><span>{{ __('Send an Email') }}</span></a></li>
                                                        <li class="divider"></li>
                                                        <li><a href="javascript:void(0)" class="user-group-action" data-method="changeGroup" data-uid="{{ data_get($user, 'id') }}"><em class="icon ni ni-tags"></em><span>{{ blank($getUserGroup) ? __("Assign Group") : __('Update Group') }}</span></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @if(filled($userList) && $userList->hasPages())
                    <div class="card-inner">
                        {{ $userList->appends(request()->all())->links('misc.pagination') }}
                    </div>
                    @endif
                    @else
                    <div class="card-inner">
                        @if(request()->filled('query'))
                        <div class="alert alert-light">
                            <div class="alert-text">
                                <em class="icon ni ni-alert fs-22px"></em>
                                <h6 class="pt-1">{{ __('Nothing Found!') }}</h6>
                                <p>{{ __("It seems we can't find what you're looking for. Maybe try another search.") }} </p>
                            </div>
                            <div class="nk-search-box mt-3 w-max-350px">
                                <form action="{{ route('admin.users.manage.groups', data_get($getUserGroup, 'id')) }}" method="GET">
                                    <div class="form-group">
                                        <div class="form-control-wrap">
                                            <input name="query" value="{{ request()->get('query') }}" type="text" class="form-control form-control-lg" placeholder="Search...">
                                            <button class="btn btn-icon form-icon form-icon-right">
                                                <em class="icon ni ni-search"></em>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-light">
                            <em class="icon ni ni-alert-circle"></em>
                            {{ __('We did not find any user here.') }}
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('modal')
<div class="modal fade" role="dialog" id="ajax-modal"></div>

{{-- Send Email Modal --}}
<div class="modal fade" tabindex="-1" role="dialog" id="send-email-user">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <a href="javascript:void(0)" class="close" data-dismiss="modal"><em class="icon ni ni-cross-sm"></em></a>
            <div class="modal-body modal-body-md">
                <h5 class="title nk-modal-title">{{ __('Send Personal Message') }}</h5>
                <form action="{{ route('admin.users.send.email') }}" method="POST" class="form-validate is-alter">
                    <input type="hidden" name="send_to" value="" id="userid">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="email-subject">{{ __('Email Subject') }} <span class="text-danger">*</span></label>
                                <div class="form-control-wrap">
                                    <input type="text" name="subject" class="form-control form-control-lg" id="email-subject" placeholder="{{ __('Subject') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="greeting">{{ __('Email Greeting') }}</label>
                                <div class="form-control-wrap">
                                    <input type="text" name="greeting" class="form-control form-control-lg" id="greeting" placeholder="{{ __('Hello!') }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="message">{{ __('Your Message') }}<span class="text-danger">*</span></label>
                                <div class="form-control-wrap">
                                    <textarea name="message" id="message" class="form-control form-control-lg" placeholder="{{ __('Write your message') }}" required></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                                <li>
                                    <button type="button" class="btn btn-lg btn-primary u-send-mail">
                                        <span>{{ __('Send Email') }}</span>
                                        <span class="spinner-border spinner-border-sm hide" role="status" aria-hidden="true"></span>
                                    </button>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" data-dismiss="modal" class="link link-light">{{ __('Cancel') }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script type="text/javascript">
    const bulk_update = "{{ route('admin.users.group.action.bulk') }}", updateSetting = "{{ route('admin.profile.update') }}";
    const qmsg = { title: "{{ __('Are you sure?') }}", btn: { cancel: "{{ __('Cancel') }}", confirm: "{{ __('Confirm') }}" }, bulkbtn: { cancel: "{{ __('No') }}", confirm: "{{ __('Yes') }}" }, context: "{!! __('Do you want to perform this action?') !!}", action: { active: "{!! __('Do you want to actived the user account?') !!}", suspend: "{!! __('Do you want to suspend the user account?') !!}", bulk: "{!! __('Do you want to update user profile with this bulk action?') !!}" } 
    };

    !(function (App, $) {
        $(document).ready(function() {
            const routes = { new: "{{ route('admin.users.group.new') }}", edit: "{{ route('admin.users.group.edit') }}", remove: "{{ route('admin.users.group.delete') }}", sendEmail: "{{ route('admin.users.group.send.email') }}", userDetails: "{{ route('admin.users.group.user.show') }}", changeGroup: "{{ route('admin.users.group.change.show') }}", removeGroup: "{{ route('admin.users.group.remove') }}" };
            const msgs = { remove: { title: "{{ __('Do you want to remove this group?') }}", btn: { cancel: "{{ __('Cancel') }}", confirm: "{{ __('Remove') }}" }, context: "{!! __('You cannot revert back this action, so please confirm that you want to remove this :type.', ['type' => __('Group')]) !!}", custom: "danger", type: "warning" }, removeGroup: { title: "{{ __('Do you want to remove this user?') }}", btn: { cancel: "{{ __('Cancel') }}", confirm: "{{ __('Remove') }}" }, context: "{!! __('You cannot revert back this action, so please confirm that you want to remove this user from the group.') !!}", custom: "danger", type: "warning" } 
            };

            let $usrGrpBtn = $('.usr-group'), $usrGrpActionBtn = $('.user-group-action'), modal = '#ajax-modal';

            $usrGrpBtn.on('click', function(e) {
                e.preventDefault();
                let $this = $(this), method = $this.data('method'), group_id = $this.data('gid'), url = routes[method], qmsg = msgs[method], data = (group_id) ? { gid: group_id } : {};
                if (url) {
                    if (method == 'remove' && qmsg) {
                        App.Ask(qmsg.title, qmsg.context, qmsg.btn, '', 'info').then(function(confirm){
                            if(confirm) {
                                App.Form.toAjax(url, data);
                            }
                        });
                    } else {
                        App.Form.toModal(url, data, { modal: $(modal) });
                    }
                }
            });

            $usrGrpActionBtn.on('click', function(e) {
                e.preventDefault();
                let $this = $(this), method = $this.data('method'), user_id = $this.data('uid'), group_id = $this.data('gid'), url = routes[method], qmsg = msgs[method], type = "{{ blank($getUserGroup) ? 'assign' : 'change' }}", data = {};
                if (url) {
                    if (method == 'removeGroup' && qmsg) {
                        App.Ask(qmsg.title, qmsg.context, qmsg.btn, '', 'info').then(function(confirm) {
                            if(confirm) {
                                data = (user_id && group_id) ? { uid: user_id, gid: group_id } : {};
                                App.Form.toAjax(url, data);
                            }
                        });
                    } else {
                        if (method == 'userDetails') {
                            data = (user_id) ? { uid: user_id } : {};
                        } else {
                            data = (user_id && type) ? { uid: user_id, type: type } : {};
                        }
                        App.Form.toModal(url, data, { modal: $(modal) });
                    }
                }
            });
        });
    })(NioApp, jQuery);
</script>
@endpush
