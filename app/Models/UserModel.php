<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;


class UserModel extends Authenticatable
{
    use HasFactory;
    protected $table = 'm_user'; //Mendefinisikan nama tabel yang digunakan oleh model ini
    protected $primaryKey = 'user_id'; //Mendefiniskan primary key dari tabel yang digunakan
    protected $fillable = ['username', 'password', 'nama', 'alamat', 'nohp', 'level_id', 'ttd', 'image'];
    // protected $hidden = ['password'];
    protected $casts =  ['password' => 'hashed'];

    public function level(): BelongsTo
    {
        return $this->belongsTo(LevelModel::class, 'level_id', 'level_id');
    }
    public function interaksiRealtimes()
    {
        return $this->hasMany(InteraksiRealtime::class, 'user_id', 'user_id');
    }


    public function getRoleName(): string
    {
        return $this->level->level_nama;
    }
    public function hasRole($role): bool
    {
        return $this->level->level_kode == $role;
    }
    public function getRole()
    {
        return $this->level->level_kode;
    }
    public function getImageUrlAttribute(): string
    {
        // Cek jika user punya file gambar dan file itu ada di storage
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }

        // Jika tidak, kembalikan gambar default
        return asset('adminlte/dist/img/default-avatar.png');
    }
}
