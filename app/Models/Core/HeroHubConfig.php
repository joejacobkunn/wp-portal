<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ParagonIE\CipherSweet\EncryptedRow;
use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use Spatie\LaravelCipherSweet\Contracts\CipherSweetEncrypted;

class HeroHubConfig extends Model implements CipherSweetEncrypted
{
    use HasFactory,UsesCipherSweet, SoftDeletes;

    protected $table = 'herohub_configs';

    protected $fillable = [
        'account_id',
        'client_id',
        'client_key',
        'organization_guid',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public static function configureCipherSweet(EncryptedRow $encryptedRow): void
    {
        $encryptedRow
            ->addField('client_key');
    }
}
