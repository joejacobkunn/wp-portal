<?php

namespace App\Models\Core;

use Spatie\LaravelCipherSweet\Contracts\CipherSweetEncrypted;
use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use ParagonIE\CipherSweet\EncryptedRow;
use ParagonIE\CipherSweet\BlindIndex;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroHubConfig extends Model implements CipherSweetEncrypted
{
    use HasFactory,UsesCipherSweet;

    protected $table = 'herohub_configs';

    protected $fillable = [
        'account_id',
        'client_id',
        'client_key',
        'organization_guid'
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
