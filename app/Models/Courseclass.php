<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Courseclass
 * 
 * @property int $Id
 * @property string $Code
 * @property string $Name
 * @property int $SemesterId
 * @property string|null $WeakDays
 * @property Carbon $StartDate
 * @property Carbon $EndDate
 * @property int $MaxQuantity
 * @property int $CurrentQuantity
 * @property int|null $CourseId
 * @property int|null $LecturerId
 * @property int|null $SubjectId
 * @property int|null $StudentClassId
 * @property int|null $DefaultRoomId
 * 
 * @property Semester $semester
 * @property Studentclass|null $studentclass
 * @property Subject|null $subject
 * @property Course|null $course
 * @property Lecturer|null $lecturer
 * @property Room|null $room
 * @property Collection|Device[] $devices
 * @property Collection|Lesson[] $lessons
 * @property Collection|Studentjoinclass[] $studentjoinclasses
 *
 * @package App\Models
 */
class Courseclass extends Model
{
	protected $table = 'courseclass';
	protected $primaryKey = 'Id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'Id' => 'int',
		'SemesterId' => 'int',
		'StartDate' => 'datetime',
		'EndDate' => 'datetime',
		'MaxQuantity' => 'int',
		'CurrentQuantity' => 'int',
		'CourseId' => 'int',
		'LecturerId' => 'int',
		'SubjectId' => 'int',
		'StudentClassId' => 'int',
		'DefaultRoomId' => 'int'
	];

	protected $fillable = [
		'Code',
		'Name',
		'SemesterId',
		'WeakDays',
		'StartDate',
		'EndDate',
		'MaxQuantity',
		'CurrentQuantity',
		'CourseId',
		'LecturerId',
		'SubjectId',
		'StudentClassId',
		'DefaultRoomId'
	];

	public function semester()
	{
		return $this->belongsTo(Semester::class, 'SemesterId');
	}

	public function studentclass()
	{
		return $this->belongsTo(Studentclass::class, 'StudentClassId');
	}

	public function subject()
	{
		return $this->belongsTo(Subject::class, 'SubjectId');
	}

	public function course()
	{
		return $this->belongsTo(Course::class, 'CourseId');
	}

	public function lecturer()
	{
		return $this->belongsTo(Lecturer::class, 'LecturerId');
	}

	public function room()
	{
		return $this->belongsTo(Room::class, 'DefaultRoomId');
	}

	public function devices()
	{
		return $this->hasMany(Device::class, 'CourseClassId');
	}

	public function lessons()
	{
		return $this->hasMany(Lesson::class, 'CourseClassId');
	}

	public function studentjoinclasses()
	{
		return $this->hasMany(Studentjoinclass::class, 'CourseClassId');
	}
}
