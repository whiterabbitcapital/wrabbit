<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <a href="javascript:void(0)" class="close" data-dismiss="modal"><em class="icon ni ni-cross-sm"></em></a>
        <div class="modal-body modal-body-md">
            <h6 class="title nk-modal-title">{{ __("Available Groups") }}</h6>
            <div class="divider md"></div>
            <form action="{{ route('admin.users.group.bulk.assign') }}" method="POST" class="form-validate is-alter">
                <div class="row g-2 align-start">
                    <div class="col-md-12">
                        <div class="row g-2">
                            @foreach($userGroups as $group)
                            <div class="col-md-6">
                                <div class="custom-control custom-control-sm custom-checkbox">
                                    <input type="checkbox" id="groups-{{ data_get($group, 'id') }}" class="custom-control-input" name="gid[{{ data_get($group, 'id') }}]">
                                    <label class="custom-control-label" for="groups-{{ data_get($group, 'id') }}">{{ ucfirst(data_get($group, 'label')) }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="divider md"></div>
                <div class="row g-4">
                    <div class="col-12">
                        <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                            <li>
                                <button type="button" class="btn btn-primary group-action">
                                    <span>{{ __("Assign") }}</span>
                                    <span class="spinner-border spinner-border-sm hide" role="status" aria-hidden="true"></span>
                                </button>
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
    $('.group-action').on('click', function() {
        let $self = $(this), $form = $self.parents("form"), url = $form.attr("action"), data = $form.serializeArray();
        data.push({name: 'users', value: '{!! json_encode($users) !!}'});

        if(url) {
            NioApp.Form.toPost(url, data, { btn: $self });
        }
    });
</script>
