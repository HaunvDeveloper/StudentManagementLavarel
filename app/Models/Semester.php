<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Semester
 * 
 * @property int $Id
 * @property string $Code
 * @property string $Name
 * @property Carbon $StartDate
 * @property Carbon $EndDate
 * @property int $SchoolYearDetailId
 * 
 * @property Studyyeardetail $studyyeardetail
 * @property Collection|Course[] $courses
 * @property Collection|Courseclass[] $courseclasses
 *
 * @package App\Models
 */
class Semester extends Model
{
	protected $table = 'semester';
	protected $primaryKey = 'Id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'Id' => 'int',
		'StartDate' => 'datetime',
		'EndDate' => 'datetime',
		'SchoolYearDetailId' => 'int'
	];

	protected $fillable = [
		'Code',
		'Name',
		'StartDate',
		'EndDate',
		'SchoolYearDetailId'
	];

	public function studyyeardetail()
	{
		return $this->belongsTo(Studyyeardetail::class, 'SchoolYearDetailId');
	}

	public function courses()
	{
		return $this->hasMany(Course::class, 'SemesterId');
	}

	public function courseclasses()
	{
		return $this->hasMany(Courseclass::class, 'SemesterId');
	}
}
