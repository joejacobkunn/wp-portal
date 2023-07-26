<?php

namespace App\Models\Core;

use App\Enums\Account\AccountStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use App\Traits\InteractsWithMedia;
use Illuminate\Support\Str;

class Account extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'subdomain',
        'address',
        'sx_company_number',
        'is_active',
    ];

    const DOCUMENT_COLLECTION = 'documents';

    protected $attributes = [
        'is_active' => 1,
    ];

    protected $casts = [
        'is_active' => AccountStatusEnum::class,
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_user');
    }

    public function sxAccount()
    {
        return $this->belongsTo(SXAccount::class, 'sx_company_number', 'cono');
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'account-modules', 'account_id', 'module_id');
    }

    public function metadata()
    {
        return $this->hasOne(AccountMetadata::class);
    }

    public function herohubConfig()
    {
        return $this->hasOne(HeroHubConfig::class, 'account_id');
    }

    public function scopeBasicSelect($query)
    {
        return $query->select('id', 'name', 'label');
    }

    public function apiKeys()
    {
        return $this->hasMany(AccountApiKey::class);
    }

    public function createKey($label)
    {
        $secretKey = Str::random(40);

        $key = $this->apiKeys()->create([
            'label' => $label,
            'client_key' => bin2hex(random_bytes(15)),
            'client_secret' => $secretKey,
            'client_secret_last4' => substr($secretKey, -4),
        ]);

        return [
            'client_key' => $key->client_key,
            'client_secret' => $secretKey,
        ];
    }

    public function revokeKey($key)
    {
        $key = $this->apiKeys()->where('client_key', $key)->firstOrFail();
        $key->is_revoked = 1;
        $key->save();
    }

    public function hasModule($module_slug)
    {
        return $this->modules()->where('slug', $module_slug)->exists();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::DOCUMENT_COLLECTION);
    }
}
