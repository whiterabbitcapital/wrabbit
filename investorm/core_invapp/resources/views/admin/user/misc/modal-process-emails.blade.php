<div class="modal-dialog modal-dialog-centered modal-md" role="document">
    <div class="modal-content">
        <div class="modal-body modal-body-md">
            <div class="nk-modal-title">
                <h4 class="title mb-3">{{ __('Email Preview') }}</h4>
            </div>
            <div class="nk-block">
                <div class="form-sets">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('Email Subject') }}</label>
                                <div class="form-control-wrap">
                                    <input name="subject" value="{{ data_get($data, 'subject') }}" type="text" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('Greeting') }}</label>
                                <div class="form-control-wrap">
                                    <input name="greeting" value="{{ data_get($data, 'greeting') }}" type="text" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="description" class="form-label">{{ __('Email Content') }}</label>
                                <div class="form-control-wrap">
                                    <textarea name="content" name="message" class="form-control textarea-lg" readonly>{{ data_get($data, 'message') }}</textarea>
                                </div>
                                <div class="form-note fs-12px font-italic mt-2">
                                    <p class="text-soft">{{ __("Users will recieve this email.") }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="progress-wrap mt-3">
                    <div class="progress-text">
                        <div class="progress-label text-base fw-medium">
                            {{ __('Total Users') }}
                        </div>
                        <div class="progress-amount"><span class="pq-count">0</span> / <span class="total">{{ $total }}</span></div>
                    </div>
                    <div class="progress progress-lg">
                        <div class="progress-bar progress-bar-striped progress-bar-animated pq-status"></div>
                    </div>
                </div>
                <ul class="align-center flex-nowrap gx-2 pt-4 pb-2">
                    <li>
                        <button type="button" class="btn btn-primary m-sync-pay" data-method="emails">
                            <span>{{ __('Send Emails') }}</span>
                            <span class="spinner-border spinner-border-sm hide" role="status" aria-hidden="true"></span>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="btn btn-danger m-cancel-bulk" data-dismiss="modal">
                            <span>{{ __('Cancel') }}</span>
                        </button>
                    </li>
                </ul>
                <div class="divider md stretched"></div>
                <div class="notes">
                    <ul>
                        <li class="alert-note is-plain">
                            <em class="icon ni ni-help"></em>
                            <p>{{ __("Users will receive emails one by one.") }}</p>
                        </li>
                        <li class="alert-note is-plain text-danger">
                            <em class="icon ni ni-alert"></em>
                            <p>{{ __("Do not reload the page while your are processing as the process may take several minutes.") }}</p>
                        </li>
                    </ul>
                </div>
            </div>
            <script type="text/javascript">
                var bulkpq = { queues: @json($users), total: {{ $total }}, batch: {{ count($users) }}, url: "{{ route('admin.users.group.bulk.email.send') }}" };

                var $cancelBulk = $('.m-cancel-bulk');

                $cancelBulk.on('click', function () {
                   NioApp.Form.toPost("{{ route('admin.users.group.bulk.email.cancel') }}", {cancel: 1});
                });
            </script>
        </div>
    </div>
</div>
