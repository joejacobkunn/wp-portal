<div class="g-confirmation-popup-div">
    <div class="modal" wire:transition data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Deactivation Modal</h5>
                    <button type="button" onClick="closeConfirmActionModal()" class="btn-close close-modal-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button onClick="closeConfirmActionModal()" type="button" class="btn btn-outline-secondary cancel-btn">Cancel</button>
                    <button type="button" class="btn submit-btn">
                        <span class="wire-loading" wire:loading>
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        </span>
                        <span class="button-text"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-backdrop fade" wire:transition.opacity></div>

    <script data-navigate-once>

        function closeConfirmActionModal (el) {
            document.querySelector('.g-confirmation-popup-div .modal').classList.remove('show');
            document.querySelector('.g-confirmation-popup-div .modal-backdrop').classList.remove('show');
        }

        (function () {
            document.addEventListener('livewire:init', () => {
                Livewire.directive('confirm-action', ({ el, directive, component, cleanup }) => {

                    let onClick = e => {
                        if (e.target.getAttribute('validated')) {
                            return;
                        }

                        e.preventDefault()
                        e.stopPropagation()
                        
                        let el = e.target;
                        let title = el.dataset.confirmTitle ? el.dataset.confirmTitle : 'Confirm';
                        let content = el.dataset.confirmContent ? el.dataset.confirmContent : 'Are you sure?';
                        let buttonContent = el.dataset.confirmButton ? el.dataset.confirmButton : 'Confirm';
                        let cancelButtonContent = el.dataset.cancelButton ? el.dataset.cancelButton : 'Cancel';
                        let buttonClass = el.dataset.confirmType ? el.dataset.confirmType : 'primary';

                        document.querySelector('.g-confirmation-popup-div .modal').classList.add('show');
                        document.querySelector('.g-confirmation-popup-div .modal-backdrop').classList.add('show');
                        document.querySelector('.g-confirmation-popup-div .modal-title').innerHTML = title;
                        document.querySelector('.g-confirmation-popup-div .modal-body').innerHTML = content;
                        document.querySelector('.g-confirmation-popup-div .modal-footer .submit-btn').className = 'submit-btn';
                        document.querySelector('.g-confirmation-popup-div .modal-footer .submit-btn').classList.add('btn', 'btn-' + buttonClass);
                        document.querySelector('.g-confirmation-popup-div .modal-footer .submit-btn .button-text').innerHTML = buttonContent;
                        document.querySelector('.g-confirmation-popup-div .modal-footer .cancel-btn').innerHTML = cancelButtonContent;

                        new Promise(function(resolve, reject) {
                            document.querySelector('.g-confirmation-popup-div .modal-footer .submit-btn').addEventListener('click', function clicked() {
                                this.removeEventListener('click', clicked, false)
                                resolve(true)
                            })
                        }).then((success) => {
                            document.querySelector('.g-confirmation-popup-div .modal-footer .submit-btn').setAttribute('disabled', true);
                            document.querySelector('.g-confirmation-popup-div .modal-footer .wire-loading').removeAttribute('wire:loading')
                            
                            let componentFunctionName = e.target.getAttribute('wire:click')
                            let functionName = componentFunctionName.split('(')[0];
                            let argsString = componentFunctionName.match(/\((.*)\)/)[1];
                            let args = argsString.split(',').map(arg => arg.trim().replace(/^'|'$/g, ''));
                            component.$wire[functionName](...args).then(() => {
                                closeConfirmActionModal();
                                document.querySelector('.g-confirmation-popup-div .modal-footer .submit-btn').removeAttribute('disabled');
                                document.querySelector('.g-confirmation-popup-div .modal-footer .wire-loading').setAttribute('wire:loading')
                            });

                        }, (error) => {
                            document.querySelector('.g-confirmation-popup-div .modal-body').innerHTML += '<div class="alert alert-danger mt-2"><p class="mb-0">Something went wrong, please try again later.</p></div>'
                        });
                    }
                
                    el.addEventListener('click', onClick, { capture: true })
                
                    cleanup(() => {
                        document.querySelector('.g-confirmation-popup-div .modal').classList.remove('show');
                        document.querySelector('.g-confirmation-popup-div .modal-backdrop').classList.remove('show');
                        el.removeEventListener('click', onClick)
                    })
                })

                document.querySelector('.g-confirmation-popup-div .modal-backdrop')
            });
        })()
    </script>
</div>