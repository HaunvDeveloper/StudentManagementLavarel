<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Curriculum
 * 
 * @property int $Id
 * @property string $Code
 * @property string $Name
 * @property Carbon $CreatedDate
 * @property int $StudyYearId
 * @property int $MajorId
 * @property int|null $TotalCredits
 * 
 * @property Major $major
 * @property Studyyear $studyyear
 * @property Collection|Course[] $courses
 * @property Collection|Student[] $students
 *
 * @package App\Models
 */
class Curriculum extends Model
{
	protected $table = 'curriculum';
	protected $primaryKey = 'Id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'Id' => 'int',
		'CreatedDate' => 'datetime',
		'StudyYearId' => 'int',
		'MajorId' => 'int',
		'TotalCredits' => 'int'
	];

	protected $fillable = [
		'Code',
		'Name',
		'CreatedDate',
		'StudyYearId',
		'MajorId',
		'TotalCredits'
	];

	public function major()
	{
		return $this->belongsTo(Major::class, 'MajorId');
	}

	public function studyyear()
	{
		return $this->belongsTo(Studyyear::class, 'StudyYearId');
	}

	public function courses()
	{
		return $this->hasMany(Course::class, 'CurriculumId');
	}

	public function students()
	{
		return $this->hasMany(Student::class, 'CurriculumId');
	}
}
