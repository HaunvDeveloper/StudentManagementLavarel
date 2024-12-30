<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Room
 * 
 * @property int $Id
 * @property string $Code
 * @property string $Name
 * @property string $Settlement
 * @property string $Address
 * 
 * @property Collection|Courseclass[] $courseclasses
 * @property Collection|Device[] $devices
 * @property Collection|Lesson[] $lessons
 *
 * @package App\Models
 */
class Room extends Model
{
	protected $table = 'room';
	protected $primaryKey = 'Id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'Id' => 'int'
	];

	protected $fillable = [
		'Code',
		'Name',
		'Settlement',
		'Address'
	];

	public function courseclasses()
	{
		return $this->hasMany(Courseclass::class, 'DefaultRoomId');
	}

	public function devices()
	{
		return $this->hasMany(Device::class, 'RoomId');
	}

	public function lessons()
	{
		return $this->hasMany(Lesson::class, 'RoomId');
	}
}
