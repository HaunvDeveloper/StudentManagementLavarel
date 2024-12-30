<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Studentjoinlesson
 * 
 * @property int $Id
 * @property string $StudentId
 * @property int $LessonId
 * @property Carbon $JoinTime
 * @property string $Status
 * @property int|null $LateLessons
 * @property string|null $Description
 * 
 * @property Lesson $lesson
 * @property Student $student
 *
 * @package App\Models
 */
class Studentjoinlesson extends Model
{
	protected $table = 'studentjoinlesson';
	protected $primaryKey = 'Id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'Id' => 'int',
		'LessonId' => 'int',
		'JoinTime' => 'datetime',
		'LateLessons' => 'int'
	];

	protected $fillable = [
		'StudentId',
		'LessonId',
		'JoinTime',
		'Status',
		'LateLessons',
		'Description'
	];

	public function lesson()
	{
		return $this->belongsTo(Lesson::class, 'LessonId');
	}

	public function student()
	{
		return $this->belongsTo(Student::class, 'StudentId');
	}
}
