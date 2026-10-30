<?php

namespace App\Models;

use App\Models\TabunganMahasiswa;
use App\Models\RiwayatTabungan;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke seluruh data celengan/tabungan milik pengguna ini.
     */
    public function tabunganMahasiswas()
    {
        return $this->hasMany(TabunganMahasiswa::class);
    }

    /**
     * Relasi ke seluruh riwayat transaksi milik pengguna ini.
     */
    public function riwayatTabungans()
    {
        return $this->hasMany(RiwayatTabungan::class);
    }
}
