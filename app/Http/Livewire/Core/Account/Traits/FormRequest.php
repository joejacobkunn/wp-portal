<?php

namespace App\Http\Livewire\Core\Account\Traits;

use App\Enums\Account\AccountStatusEnum;
use App\Events\User\UserCreated;
use App\Models\Core\Account;
use App\Models\Core\Role;
use App\Models\Core\User;

trait FormRequest
{
    public $adminEmail;

    public $adminUser;

    public $accountMetaData;

    /** Supporting Documents atributes */
    public $documents;

    protected $validationAttributes = [
        'account.name' => 'Name',
        'account.subdomain' => 'Subdomain',
    ];

    public $restricted_subdomains = [
        'adfs',
        'disco01',
        'inforos',
        'mbg',
        'mmicollab',
        'mits01',
        'pas',
        'soap',
        'sx',
        'testinforos',
        'testpas',
        'testsx',
        'sro',
        'webhook',
        'test',
        'internal',
        'api',
        'mits',
        'rest',
    ];

    protected function rules()
    {
        return [
            'account.name' => 'required|min:3',
            'account.subdomain' => 'required|min:3|not_in:'.implode(',', $this->restricted_subdomains).'|unique:accounts,subdomain'.($this->account ? ','.$this->account->id : ''),
            'account.is_active' => 'nullable',
            'account.sx_company_number' => 'required',
            'adminEmail' => 'required',
        ];
    }

    /**
     * Initialize form attributes
     */
    public function formInit()
    {
        if (empty($this->account->id)) {
            $this->account = new Account;
            $this->account->name = null;
            $this->account->subdomain = null;
            $this->account->admin_user = null;
        } else {
            $this->adminUser = $this->account->admin()->first();
            $this->adminEmail = $this->adminUser?->email;
            $this->accountMetaData = $this->account?->metadata()->first();
        }
    }

    /**
     * Form submission action
     */
    public function submit()
    {
        $this->validate();

        if (! empty($this->account->id)) {
            $this->update();
        } else {
            $this->store();
        }
    }

    /**
     * Create new account
     */
    public function store()
    {
        $this->authorize('store', Account::class);

        $this->account->is_active = AccountStatusEnum::Active;
        $this->account->save();

        if ($this->documents !== null) {
            $this->account
                ->syncFromMediaLibraryRequest($this->documents)
                ->toMediaCollection(Account::DOCUMENT_COLLECTION);
            $this->emit("refreshMedia");
        }

        $this->setAdminUser();

        session()->flash('success', 'Account created!');

        return redirect()->route('core.account.show', [
            'account' => $this->account->id,
        ]);
    }

    /**
     * Update existing account
     */
    public function update()
    {
        $this->authorize('update', $this->account);

        $this->account->save();

        if ($this->documents !== null) {
            $this->account
                ->syncFromMediaLibraryRequest($this->documents)
                ->toMediaCollection(Account::DOCUMENT_COLLECTION);
            $this->emit("refreshMedia");
        }

        $this->setAdminUser();

        $this->editRecord = false;
        session()->flash('success', 'Account updated!');
    }

    public function setAdminUser()
    {
        $user = User::where('account_id', $this->account->id)
            ->where('email', $this->adminEmail)
            ->firstOrNew();

        if (empty($user->id)) {
            $user->email = $this->adminEmail;
            $user->name = '';
            $user->is_active = User::ACTIVE;
            $user->account_id = $this->account->id;
            $user->abbreviation = abbreviation($user->email);
            $user->save();

            $user->metadata()->create([
                'invited_by' => auth()->user()->id,
            ]);

            //send notifications
            UserCreated::dispatch($user);
        } elseif ($user->account_id != $this->account->id) {
            $user->account_id = $this->account->id;
            $user->save();
        }

        if ($this->adminUser?->id != $user?->id) {

            if ($this->adminUser) {
                //update existing role
                $this->adminUser->roles()->detach();
                $this->adminUser->assignRole(Role::DEFAULT_USER_ROLE);
            }

            $this->account->admin_user = $user->id;
            $this->account->save();
            $user->roles()->detach();
            $user->assignRole(Role::SUPER_ADMIN_ROLE);
        }
    }

    /**
     * Update Status
     */
    public function updateStatus()
    {
        $isActive = !$this->account->is_active->value;
        $statusEnum = AccountStatusEnum::tryFrom($isActive);

        $this->account->is_active = $statusEnum;
        $this->account->save();
    }

    /**
     * Properties
     */
    public function getStatusAlertClassProperty()
    {
        return $this->account->is_active->class();
    }

    public function getStatusAlertMessageProperty()
    {
        return 'This account is '. $this->account->is_active->label();
    }

    public function getStatusAlertMessageIconProperty()
    {
        return $this->account->is_active->icon();
    }

    public function getStatusAlertHasActionProperty()
    {
        return true;
    }

    public function getStatusAlertActionButtonClassProperty()
    {
        $isActive = !$this->account->is_active->value;
        $statusEnum = AccountStatusEnum::tryFrom($isActive);

        return $statusEnum->class();
    }

    public function getStatusAlertActionButtonNameProperty()
    {
        $isActive = !$this->account->is_active->value;
        $statusEnum = AccountStatusEnum::tryFrom($isActive);

        return $statusEnum->buttonName();
    }

    /**
     * Set media on attribute
     *
     * Note: Emitted from media library component
     */
    public function mediaUpdated($name, $media)
    {
        parent::fieldUpdated($name, $media);
        $this->disableDocumentSubmit = false;
    }

    /**
     * Show document upload form
     */
    public function showEditDocuments()
    {
        $this->editDocuments = true;
    }

    /**
     * Cancel document upload form
     */
    public function cancelDocumentForm()
    {
        $this->editDocuments = false;
    }

}
