<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class server_services extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'servicename',
        'serviceid',
        'cost',
        'referenceid',
        'user_id',
        'dhru_provider_id',
        'dhru_catalog_service_id',
        'IMEI',
        'status',
        'code',
        'uuid',
        'Qnt',
    ];

    public function dhruProvider(): BelongsTo
    {
        return $this->belongsTo(DhruProvider::class, 'dhru_provider_id');
    }

    public function dhruCatalogService(): BelongsTo
    {
        return $this->belongsTo(DhruCatalogService::class, 'dhru_catalog_service_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
