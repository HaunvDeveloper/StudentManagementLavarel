<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Subject
 * 
 * @property int $Id
 * @property string $Code
 * @property string $Name
 * @property int $DefaultCredits
 * @property int $DeptId
 * @property int|null $DefaultLesson
 * 
 * @property Collection|Course[] $courses
 * @property Collection|Courseclass[] $courseclasses
 *
 * @package App\Models
 */
class Subject extends Model
{
	protected $table = 'subject';
	protected $primaryKey = 'Id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'Id' => 'int',
		'DefaultCredits' => 'int',
		'DeptId' => 'int',
		'DefaultLesson' => 'int'
	];

	protected $fillable = [
		'Code',
		'Name',
		'DefaultCredits',
		'DeptId',
		'DefaultLesson'
	];

	public function courses()
	{
		return $this->hasMany(Course::class, 'SubjectId');
	}

	public function courseclasses()
	{
		return $this->hasMany(Courseclass::class, 'SubjectId');
	}
}
