<?php

namespace App\Policies\Equipment\FloorModel;

use App\Models\Core\User;
use App\Models\Equipment\FloorModelInventory\FloorModelInventoryNote;

class FloorModelInventoryNotePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('equipment.floor-model-inventory.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return $user->can('equipment.floor-model-inventory.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function store(User $user): bool
    {
        return $user->can('equipment.floor-model-inventory.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FloorModelInventoryNote $floorModelInventoryNote): bool
    {
        if($user->id != $floorModelInventoryNote->user_id) {
            return false;
        };

        return $user->can('equipment.floor-model-inventory.manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FloorModelInventoryNote $floorModelInventoryNote): bool
    {
        if($user->id != $floorModelInventoryNote->user_id) {
            return false;
        };

        return $user->can('equipment.floor-model-inventory.manage');
    }
}
