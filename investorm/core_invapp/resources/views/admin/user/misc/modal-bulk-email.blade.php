<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
        <a href="javascript:void(0)" class="close" data-dismiss="modal"><em class="icon ni ni-cross-sm"></em></a>
        <div class="modal-body modal-body-md">
            <h5 class="title nk-modal-title">{{ __('Send Email') }}</h5>
            <form action="{{ route('admin.users.group.bulk.email') }}" method="POST" class="form-validate is-alter">
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
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input name="params" id="header-footer" type="checkbox" checked class="custom-control-input">
                                <label for="header-footer" class="custom-control-label">{{ __('Enable global email header/footer') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                            <li>
                                <button type="button" class="btn btn-lg btn-primary" id="bulk-mail">
                                    <span>{{ __('Next') }}</span>
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

<script>
    $('#bulk-mail').on('click', function() {
        var $self = $(this), $form = $self.parents("form"), url = $form.attr("action"), data = $form.serializeArray();
        data.push({name: 'users', value: '{!! json_encode($users) !!}'});
        
        if (url) {
            NioApp.Form.toAjax(url, data, {
                onSuccess: function (res) {
                    if (res.embed) {
                        $('#ajax-modal').html(res.embed);
                        $('#ajax-modal').modal({show: true, backdrop: 'static'});
                    }
                }
            });
        }
    });
</script>
