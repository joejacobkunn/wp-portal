<div class="btn-group mt-n1 mb-3 float-end" role="group" aria-label="Basic example">
    @can('scheduler.schedule.manage')
        @if ($form->schedule->status != 'cancelled' && $form->schedule->status != 'completed')
            <button type="button" wire:key="cancel-toggle-btn" class="btn btn-sm btn-danger"
                wire:click="changeStatus('cancel')" data-bs-toggle="collapse"
                data-bs-target="#cancelCollapse" aria-expanded="false"
                aria-controls="cancelCollapse"><i class="far fa-calendar-times"></i>
                Cancel</button>
        @endif

        @if ($form->schedule->status == 'cancelled')
            <button type="button" class="btn btn-sm btn-warning"
                wire:click="changeStatus('uncacnel')" data-bs-toggle="collapse"
                wire:key="undocancel-toggle-btn" data-bs-target="#undoCancelCollapse"
                aria-expanded="false" aria-controls="undoCancelCollapse"><i class="fas fa-undo"></i>
                Uncancel</button>
        @endif
        @if ($form->schedule->status == 'scheduled' || $form->schedule->status == 'scheduled_linked')
            <button type="button" class="btn btn-sm btn-warning"
                wire:click="changeStatus('reschedule')" data-bs-toggle="collapse"
                data-bs-target="#rescheduleCollapse" aria-expanded="false"
                wire:key="reschedule-toggle-btn" aria-controls="rescheduleCollapse"><i
                    class="fas fa-redo"></i>
                Reschedule</button>
        @endif
        @if ($form->schedule->status == 'scheduled_linked')
            <button type="button" class="btn btn-sm btn-primary"
                wire:click="changeStatus('confirm')" data-bs-toggle="collapse"
                data-bs-target="#confirmCollapse" aria-expanded="false"
                wire:key="confirm-toggle-btn" aria-controls="confirmCollapse"><i
                    class="fas fa-check-double"></i>
                Confirm</button>
            <button type="button" class="btn btn-sm btn-info" wire:click="changeStatus('unlink')"
                data-bs-toggle="collapse" wire:key="unlink-toggle-btn"
                data-bs-target="#unlinkCollapse" aria-expanded="false"
                aria-controls="unlinkCollapse"><i class="fas fa-unlink"></i>
                Unlink SRO</button>
        @endif
        @if ($form->schedule->status == 'confirmed')
            <button type="button" class="btn btn-sm btn-secondary"
                wire:click="changeStatus('unconfirm')" wire:key="unconfirm-toggle-btn"
                data-bs-toggle="collapse" data-bs-target="#unconfirmCollapse" aria-expanded="false"
                aria-controls="unconfirmCollapse"><i class="fas fa-solid fa-xmark"></i>
                Unconfirm</button>
        @endif
    @endcan
    @canany(['scheduler.can-start-event', 'scheduler.schedule.manage'])
        @if ($form->schedule->status == 'confirmed')
            <button type="button" class="btn btn-sm btn-success" wire:click="changeStatus('start')"
                data-bs-toggle="collapse" data-bs-target="#startScheduleCollapse"
                aria-expanded="false" wire:key="start-toggle-btn"
                aria-controls="startScheduleCollapse"><i class="fas fa-check-circle"></i>
                Start</button>
        @endif
    @endcan
    @canany(['scheduler.can-complete-event', 'scheduler.schedule.manage'])
        @if ($form->schedule->status == 'out_for_delivery')
            <button type="button" class="btn btn-sm btn-success" wire:key="complete-toggle-btn"
                wire:click="changeStatus('complete')" data-bs-toggle="collapse"
                data-bs-target="#completeCollapse" aria-expanded="false"
                aria-controls="completeCollapse"><i class="fas fa-check-circle"></i>
                Complete</button>
        @endif
    @endcan

</div>
