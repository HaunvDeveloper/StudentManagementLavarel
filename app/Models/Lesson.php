<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Lesson
 * 
 * @property int $Id
 * @property int $StartLesson
 * @property int $EndLesson
 * @property Carbon|null $Date
 * @property int $CourseClassId
 * @property int|null $RoomId
 * 
 * @property Lessoninfo $lessoninfo
 * @property Courseclass $courseclass
 * @property Room|null $room
 * @property Collection|Device[] $devices
 * @property Collection|Student[] $students
 *
 * @package App\Models
 */
class Lesson extends Model
{
	protected $table = 'lesson';
	protected $primaryKey = 'Id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'Id' => 'int',
		'StartLesson' => 'int',
		'EndLesson' => 'int',
		'Date' => 'datetime',
		'CourseClassId' => 'int',
		'RoomId' => 'int'
	];

	protected $fillable = [
		'StartLesson',
		'EndLesson',
		'Date',
		'CourseClassId',
		'RoomId'
	];

	public function lessoninfo()
	{
		return $this->belongsTo(Lessoninfo::class, 'EndLesson');
	}

	public function courseclass()
	{
		return $this->belongsTo(Courseclass::class, 'CourseClassId');
	}

	public function room()
	{
		return $this->belongsTo(Room::class, 'RoomId');
	}

	public function devices()
	{
		return $this->hasMany(Device::class, 'LessonId');
	}

	public function students()
	{
		return $this->belongsToMany(Student::class, 'studentjoinlesson', 'LessonId', 'StudentId')
					->withPivot('Id', 'JoinTime', 'Status', 'LateLessons', 'Description');
	}
}
