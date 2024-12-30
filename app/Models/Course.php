<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Course
 * 
 * @property int $Id
 * @property int $Lesson
 * @property int $Credits
 * @property int $CurriculumId
 * @property int $SemesterId
 * @property int $TypeId
 * @property int $SubjectId
 * @property string|null $Infomation
 * 
 * @property Coursetype $coursetype
 * @property Curriculum $curriculum
 * @property Semester $semester
 * @property Subject $subject
 * @property Collection|Courseclass[] $courseclasses
 *
 * @package App\Models
 */
class Course extends Model
{
	protected $table = 'course';
	protected $primaryKey = 'Id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'Id' => 'int',
		'Lesson' => 'int',
		'Credits' => 'int',
		'CurriculumId' => 'int',
		'SemesterId' => 'int',
		'TypeId' => 'int',
		'SubjectId' => 'int'
	];

	protected $fillable = [
		'Lesson',
		'Credits',
		'CurriculumId',
		'SemesterId',
		'TypeId',
		'SubjectId',
		'Infomation'
	];

	public function coursetype()
	{
		return $this->belongsTo(Coursetype::class, 'TypeId');
	}

	public function curriculum()
	{
		return $this->belongsTo(Curriculum::class, 'CurriculumId');
	}

	public function semester()
	{
		return $this->belongsTo(Semester::class, 'SemesterId');
	}

	public function subject()
	{
		return $this->belongsTo(Subject::class, 'SubjectId');
	}

	public function courseclasses()
	{
		return $this->hasMany(Courseclass::class, 'CourseId');
	}
}
