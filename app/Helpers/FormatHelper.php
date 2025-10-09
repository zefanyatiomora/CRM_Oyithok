<?php

namespace App\Helpers;

use Carbon\Carbon;

class FormatHelper {
    public static function tanggalIndo($date) {
        return Carbon::parse($date)->format('d-m-Y');
    }
}
