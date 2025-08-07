<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FollowupModel extends Model
{
    use HasFactory;

    protected $table = 'followup';

    protected $fillable = [
        'interaksi_id', 'customer_id', 'tahapan', 'pic', 'follow_up', 'close'
    ];

    public function interaksi()
    {
        return $this->belongsTo(InteraksiModel::class, 'interaksi_id');
    }

    public function customer()
    {
        return $this->belongsTo(CustomersModel::class, 'customer_id');
    }

    protected static function booted()
    {
        static::saving(function ($followup) {
            $follow_up = $followup->follow_up;

            if ($follow_up === 'Follow Up 1') {
                $followup->close = 'Follow Up 2';
            } elseif ($follow_up === 'Follow Up 2') {
                $followup->close = 'Broadcast';
            } elseif (in_array($follow_up, ['Closing Survey', 'Closing Pasang', 'Closing Product', 'Closing ALL'])) {
                $followup->close = 'Closing';
            } else {
                $followup->close = 'Follow Up 1';
            }
        });
    }
}
