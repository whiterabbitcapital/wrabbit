<div class="modal-dialog modal-dialog-centered modal-md" role="document">
    <div class="modal-content">
        <a href="javascript:void(0)" class="close" data-dismiss="modal"><em class="icon ni ni-cross-sm"></em></a>
        <div class="modal-body modal-body-md">
            <h5 class="title nk-modal-title">{{ __("Update User Group") }}</h5>
            <form action="{{ route('admin.users.group.update', data_get($userGroup, 'id')) }}" method="POST" class="form-validate is-alter">
                <div class="row gy-4">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="form-label" for="group-label">{{ __("Name of Group") }}</label>
                            <div class="form-control-wrap">
                                <input type="text" name="group_label" class="form-control" id="group-label" value="{{ data_get($userGroup, 'label') }}" maxlength="190">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label" for="group-color">{{ __("Color") }}</label>
                            <select name="group_color" id="group-color" class="form-control form-select form-select-sm" data-placeholder="{{ __("Please select") }}">
                                <option></option>
                                <option value="blue" @if (data_get($userGroup, 'color') == 'blue') selected @endif>{{ __('Blue') }}</option>
                                <option value="dark" @if (data_get($userGroup, 'color') == 'dark') selected @endif>{{ __('Black') }}</option>
                                <option value="gray" @if (data_get($userGroup, 'color') == 'gray') selected @endif>{{ __('Gray') }}</option>
                                <option value="indigo" @if (data_get($userGroup, 'color') == 'indigo') selected @endif>{{ __('Indigo') }}</option>
                                <option value="purple" @if (data_get($userGroup, 'color') == 'purple') selected @endif>{{ __('Purple') }}</option>
                                <option value="pink" @if (data_get($userGroup, 'color') == 'pink') selected @endif>{{ __('Pink') }}</option>
                                <option value="orange" @if (data_get($userGroup, 'color') == 'orange') selected @endif>{{ __('Orange') }}</option>
                                <option value="teal" @if (data_get($userGroup, 'color') == 'teal') selected @endif>{{ __('Teal') }}</option>
                                <option value="success" @if (data_get($userGroup, 'color') == 'success') selected @endif>{{ __('Green') }}</option>
                                <option value="danger" @if (data_get($userGroup, 'color') == 'danger') selected @endif>{{ __('Red') }}</option>
                                <option value="warning" @if (data_get($userGroup, 'color') == 'warning') selected @endif>{{ __('Yellow') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label" for="group-desc">{{ __("Description") }}</label>
                            <div class="form-control-wrap">
                                <textarea name="group_desc" id="group-desc" class="form-control textarea-sm">{{ data_get($userGroup, 'desc') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                            <li>
                                <button type="button" class="btn btn-primary group-update">{{ __("Update") }}</button>
                            </li>
                            <li>
                                <a href="javascript:void(0)" data-dismiss="modal" class="link link-light">{{ __("Cancel") }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('.group-update').on('click', function() {
        let $self = $(this), $form = $self.parents("form"), url = $form.attr("action"), data = $form.serialize();
        if(url) {
            NioApp.Form.toPost(url, data, { btn: $self });
        }
    });
</script>
