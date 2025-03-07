<div>
    <div class="card border-light shadow-sm warranty-tab">
        <div class="card-body">
            <div class="alert alert-light-primary color-primary"><i class="fas fa-info-circle"></i> Showing registration
                data for
                the last two years from SX. This list is refreshed hourly<span class="float-end"><strong>Last
                        refreshed</strong>
                    {{ Carbon\Carbon::parse($last_refresh_timestamp)->diffForHumans() }}</span></div>
            @if ($non_registered_count > 0)
                <div class="alert alert-light-warning color-warning"><i class="fas fa-exclamation-triangle"></i>
                    <strong>{{ number_format($non_registered_count) }}</strong> products have missing warranty
                    registration(s)
                </div>
            @endif
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="report-table-wrap">
                        <livewire:equipment.warranty.warranty-import.report-table
                            :key="'report-table-'. $refreshKey">
                    </div>
                </div>
            </div>
        </div>
    </div>
    @script
        <script>
            (function () {
                document.querySelector('.report-table-wrap').addEventListener('click', function(e) {
                    const btnGroup = e.target.closest('.btn-group');
                    const td = e.target.closest('td');
                    const cust_no = btnGroup ? btnGroup.getAttribute('data-cust-no') : null;
                    const data_serial = btnGroup ? btnGroup.getAttribute('data-serial') : null;

                    const btn = e.target.closest('button');
                    if (btn) {
                        const originalWidth = btn.style.width || 'auto';
                    }

                    const showLoader = () => {

                            loaderIcon = document.createElement('i');
                            loaderIcon.className = 'fa fa-spinner fa-spin loader';
                            loaderIcon.style.display = 'none';
                            colSpan = td.querySelector('span');
                            if (colSpan) colSpan.remove();

                            btnGroup.textContent='';
                            btnGroup.appendChild(loaderIcon);
                            if (loaderIcon) {
                                loaderIcon.style.display = 'inline-block';
                            }
                    };

                    const hideLoader = () => {
                        loaderIcon = btnGroup ? btnGroup.querySelector('.loader') : null;
                        if (loaderIcon) {
                            loaderIcon.style.display = 'none';
                        }
                    };

                    const createButton = (className, textContent) => {
                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = className;
                        button.textContent = textContent;
                        return button;
                    };

                    const removeButtons = (...buttons) => buttons.forEach(btn => btn && btn.remove());

                    const addRegisterButtons = () => {
                        btnGroup.appendChild(createButton('btn btn-sm btn-outline-primary warrantyRegister', 'Register'));
                        btnGroup.appendChild(createButton('btn btn-sm btn-outline-secondary ignoreRegistration', 'Ignore'));
                    };

                    if (e.target.matches('.warrantyRegister')) {
                        showLoader()
                        $wire.register(data_serial, cust_no).then((result) => {
                            hideLoader()
                            const dateSpan = document.createElement('span');
                            dateSpan.textContent = result;

                            td.insertBefore(dateSpan, td.firstChild);
                            btnGroup.appendChild(createButton('btn btn-sm btn-outline-danger warrantyUnregister', 'Unregister'));

                            removeButtons(
                                btnGroup.querySelector('.ignoreRegistration'),
                                e.target
                            );
                        });
                    }

                    if (e.target.matches('.warrantyUnregister')) {
                        showLoader()
                        $wire.unregister(data_serial, cust_no).then(() => {
                            hideLoader()
                            const dateSpan = td.querySelector('span');
                            if (dateSpan) dateSpan.remove();
                            addRegisterButtons();
                            removeButtons(e.target);
                        });
                    }

                    if (e.target.matches('.ignoreRegistration')) {
                        showLoader()
                        $wire.ignore(data_serial, cust_no).then(() => {
                            hideLoader()
                            const ignoreSpan = document.createElement('span');
                            ignoreSpan.className = 'badge bg-light-secondary';
                            ignoreSpan.textContent = 'Ignored';

                            td.insertBefore(ignoreSpan, td.firstChild);
                            btnGroup.appendChild(createButton('btn btn-sm btn-outline-warning warrantyUnregister', 'Reset'));
                            removeButtons(
                                btnGroup.querySelector('.warrantyRegister'),
                                e.target
                            );
                        });
                    }
                }, false);
            })();
        </script>
    @endscript


</div>
