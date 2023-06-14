<div wire:init="init" >
    @if($loaded)
    <div class="card border-light shadow-sm mb-4">
        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0" :key="'bew' . time()">
            <button wire:click="openGenerateKeyModal" class="btn btn-success btn-sm float-end"><i class="fa-solid fa-plus"></i> Create API Key</button>
            <h3 class="h5 mb-0"><i class="fas fa-bars me-1"></i> API Keys</h3>
        </div>

        <div class="card-body">

            <div wire:init="loadKeys">
                @if($keysLoaded && count($keys))
                <p>Existing API Keys are listed here</p>
                <table class="table">
                    <thead>
                        <th>Label</th>
                        <th>Key</th>
                        <th>Secret</th>
                        <th>Actions</th>
                    </thead>
                    <tbody>
                        @foreach($keys as $key)
                        <tr class="@if($key->is_revoked) table-danger @endif">
                            <td>{{ $key->label }}</td>
                            <td>{{ $key->client_key }}</td>
                            <td>*****************{{ $key->client_secret_last4 }}</td>
                            <td>
                                @if($key->is_revoked)
                                    Revoked
                                @else
                                    <button wire:click="revokeAccess('{{ $key->client_key }}')" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-xmark"></i> Revoke</button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
    @endif

    <x-modal :toggle="$generateKeyModal">
        <x-slot name="title">
            <div class="pre-genkey-div">Create API Key</div>
            <div class="post-genkey-div d-none">Your API Key</div>
        </x-slot>
        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="pre-genkey-div">
                    <x-forms.input
                        label="Key Label"
                        model="keyLabel"
                        hint="This is to identify your app, eg Mobile App"
                    />
                </div>
                <div class="post-genkey-div d-none">
                    <label class="alert alert-light-warning mb-0">Warning: This key secret will only be displayed once. Please make sure to copy it down or store it securely before proceeding</label>
                    <div class="api-key-field-div mt-3 p-3"></div>
                </div>
            </div>
        </div>

        <x-slot name="footer">
            <button wire:click="generateKey" type="button" class="pre-genkey-div btn btn-success">Generate</button>
            <button wire:click="closeKeyPopup" type="button" class="post-genkey-div d-none btn btn-outline-secondary">Close</button>
        </x-slot>

    </x-modal>

    <script wire:ignore>
        function copyToClipboard() {
            document.getElementById("copy_").select();
            document.execCommand('copy');
        }

        document.addEventListener('account:key-generated', (e) => {
            document.querySelectorAll('.pre-genkey-div').forEach((v) => v.classList.add('d-none'))
            document.querySelectorAll('.post-genkey-div').forEach((v) => v.classList.remove('d-none'))
            document.querySelector('.api-key-field-div').innerHTML = '<div class="mb-3"><strong>Key:</strong><div>'+ e.detail.client_key +'<input type="hidden" value="'+ e.detail.client_key +'" class="copy-input" /> <i class="fa-regular fa-clone copy-btn ms-2" title="Copy"></i></div></div><strong>Secret:</strong><div>'+ e.detail.client_secret +'<input type="hidden" value="'+ e.detail.client_secret +'" class="copy-input" /> <i class="fa-regular fa-clone copy-btn ms-2" title="Copy"></i><div>'

            setTimeout(() => {
                @this.closeKeyPopup()
            }, 180000)
        });

        document.addEventListener('click', function(event) {
            if (event.target.matches('.copy-btn')) {
                event.preventDefault();
                console.log(event.target)
                let copyText = event.target.parentElement.querySelector('.copy-input')
                copyText.select()

                navigator.clipboard.writeText(copyText.value);

                document.dispatchEvent(new CustomEvent('show:toast', {
                    detail: {
                        type: "success",
                        message: "Copied!"
                    }
                }))
            }
        }, false);
    </script>
</div>
