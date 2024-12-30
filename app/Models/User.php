<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * 
 * @property int $Id
 * @property string $Username
 * @property string $Password
 * @property string|null $FullName
 * @property string|null $Email
 * @property Carbon|null $DayOfBirth
 * @property string|null $OTP
 * @property Carbon|null $OTPLastestSend
 * @property bool $IsBlock
 * @property int $AuthId
 * 
 * @property Authentication $authentication
 * @property Collection|Device[] $devices
 * @property Collection|Lecturer[] $lecturers
 * @property Collection|Student[] $students
 *
 * @package App\Models
 */
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    // Giữ nguyên các thuộc tính và phương thức hiện tại
    protected $table = 'user';
    protected $primaryKey = 'Id';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'Id' => 'int',
        'DayOfBirth' => 'datetime',
        'OTPLastestSend' => 'datetime',
        'IsBlock' => 'bool',
        'AuthId' => 'int'
    ];

    protected $fillable = [
        'Username',
        'Password',
        'FullName',
        'Email',
        'DayOfBirth',
        'OTP',
        'OTPLastestSend',
        'IsBlock',
        'AuthId',
		'remember_token'
    ];

    public function authentication()
    {
        return $this->belongsTo(Authentication::class, 'AuthId');
    }

    public function devices()
    {
        return $this->hasMany(Device::class, 'UserId');
    }

    public function lecturers()
    {
        return $this->hasMany(Lecturer::class, 'UserId');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'UserId');
    }
}
