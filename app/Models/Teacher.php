<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Teacher extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'name',
        'gender',
        'active',
    ];

    public function userable()
    {
        return $this->morphOne(Userable::class,'userable');

    }

    public static function setUserable($id)
    {
        $teacher = self::find($id);

        $user = User::create([
            'name' => $teacher->name,
            'email' => Str::slug($teacher->name).'@teacher.com',
            'password' => Hash::make('password'),
        ]);

        Userable::create([
            'user_id' => $user->id,
            'userable_id' => $teacher->id,
            'userable_type' => 'Teacher',
        ]);

        return self::find($id);
    }

    public function hasUserable()
    {
        // Lakukan pengecekan apakah guru memiliki Userable
        return $this->userable !== null;
    }
}
