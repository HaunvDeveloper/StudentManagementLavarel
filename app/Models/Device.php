<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Device
 * 
 * @property int $Id
 * @property string $Code
 * @property string|null $Name
 * @property int|null $RoomId
 * @property int|null $CourseClassId
 * @property int|null $LessonId
 * @property int|null $UserId
 * @property bool $IsActive
 * 
 * @property Courseclass|null $courseclass
 * @property Lesson|null $lesson
 * @property Room|null $room
 * @property User|null $user
 *
 * @package App\Models
 */
class Device extends Model
{
	protected $table = 'device';
	protected $primaryKey = 'Id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'Id' => 'int',
		'RoomId' => 'int',
		'CourseClassId' => 'int',
		'LessonId' => 'int',
		'UserId' => 'int',
		'IsActive' => 'bool'
	];

	protected $fillable = [
		'Code',
		'Name',
		'RoomId',
		'CourseClassId',
		'LessonId',
		'UserId',
		'IsActive'
	];

	public function courseclass()
	{
		return $this->belongsTo(Courseclass::class, 'CourseClassId');
	}

	public function lesson()
	{
		return $this->belongsTo(Lesson::class, 'LessonId');
	}

	public function room()
	{
		return $this->belongsTo(Room::class, 'RoomId');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'UserId');
	}
}
