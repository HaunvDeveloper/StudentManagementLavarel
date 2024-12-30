<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Student
 * 
 * @property string $Id
 * @property string $FullName
 * @property Carbon $DayOfBirth
 * @property string|null $Email
 * @property string $Status
 * @property int|null $StudentClassId
 * @property int $CurriculumId
 * @property int $DeptId
 * @property int $MajorId
 * @property int|null $UserId
 * @property string $NationId
 * @property string $BirthPlace
 * @property int|null $ProvinceCode
 * @property int|null $DistrictCode
 * @property int|null $WardCode
 * @property string|null $StreetAddress
 * @property string $Sex
 * @property string|null $FaceData
 * @property string|null $PhoneNo
 * @property string|null $Nation
 * @property string|null $Religion
 * 
 * @property District|null $district
 * @property Province|null $province
 * @property Studentclass|null $studentclass
 * @property User|null $user
 * @property Ward|null $ward
 * @property Curriculum $curriculum
 * @property Department $department
 * @property Major $major
 * @property Collection|Studentjoinclass[] $studentjoinclasses
 * @property Collection|Lesson[] $lessons
 *
 * @package App\Models
 */
class Student extends Model
{
	protected $table = 'student';
	protected $primaryKey = 'Id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'DayOfBirth' => 'datetime',
		'StudentClassId' => 'int',
		'CurriculumId' => 'int',
		'DeptId' => 'int',
		'MajorId' => 'int',
		'UserId' => 'int',
		'ProvinceCode' => 'int',
		'DistrictCode' => 'int',
		'WardCode' => 'int'
	];

	protected $fillable = [
		'FullName',
		'DayOfBirth',
		'Email',
		'Status',
		'StudentClassId',
		'CurriculumId',
		'DeptId',
		'MajorId',
		'UserId',
		'NationId',
		'BirthPlace',
		'ProvinceCode',
		'DistrictCode',
		'WardCode',
		'StreetAddress',
		'Sex',
		'FaceData',
		'PhoneNo',
		'Nation',
		'Religion'
	];

	public function district()
	{
		return $this->belongsTo(District::class, 'DistrictCode');
	}

	public function province()
	{
		return $this->belongsTo(Province::class, 'ProvinceCode');
	}

	public function studentclass()
	{
		return $this->belongsTo(Studentclass::class, 'StudentClassId');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'UserId');
	}

	public function ward()
	{
		return $this->belongsTo(Ward::class, 'WardCode');
	}

	public function curriculum()
	{
		return $this->belongsTo(Curriculum::class, 'CurriculumId');
	}

	public function department()
	{
		return $this->belongsTo(Department::class, 'DeptId');
	}

	public function major()
	{
		return $this->belongsTo(Major::class, 'MajorId');
	}

	public function studentjoinclasses()
	{
		return $this->hasMany(Studentjoinclass::class, 'StudentId');
	}

	public function lessons()
	{
		return $this->belongsToMany(Lesson::class, 'studentjoinlesson', 'StudentId', 'LessonId')
					->withPivot('Id', 'JoinTime', 'Status', 'LateLessons', 'Description');
	}
}
