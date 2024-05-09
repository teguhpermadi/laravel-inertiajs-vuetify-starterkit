<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Student extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'nisn',
        'nis',
        'name',
        'gender',
        'active',
        'city_born',
        'birthday',
        'nick_name',
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'nisn' => 'string',
        'nis' => 'string',
        'active' => 'boolean',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    
    protected static function booted(): void
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('active', 1);
        });
        
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('id', 'asc');
        });
    }

    public function userable()
    {
        // return $this->belongsTo(Userable::class,'id', 'userable_id');
        return $this->morphOne(Userable::class, 'userable');
    }

    public static function setUserable($id)
    {
        $student = self::find($id);

        $user = User::create([
            'name' => $student->name,
            'email' => Str::slug($student->name).'@student.com',
            'password' => Hash::make('password'),
        ]);

        Userable::create([
            'user_id' => $user->id,
            'userable_id' => $student->id,
            'userable_type' => 'Student',
        ]);

        return self::find($id);
    }

    public function hasUserable()
    {
        // Lakukan pengecekan apakah siswa memiliki Userable
        return $this->userable !== null;
    }
}
