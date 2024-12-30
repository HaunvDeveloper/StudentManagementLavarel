<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Department
 * 
 * @property int $Id
 * @property string $Code
 * @property string $Name
 * @property Carbon $DateFound
 * 
 * @property Collection|Lecturer[] $lecturers
 * @property Collection|Major[] $majors
 * @property Collection|Student[] $students
 *
 * @package App\Models
 */
class Department extends Model
{
	protected $table = 'department';
	protected $primaryKey = 'Id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'Id' => 'int',
		'DateFound' => 'datetime'
	];

	protected $fillable = [
		'Code',
		'Name',
		'DateFound'
	];

	public function lecturers()
	{
		return $this->hasMany(Lecturer::class, 'DeptId');
	}

	public function majors()
	{
		return $this->hasMany(Major::class, 'DeptId');
	}

	public function students()
	{
		return $this->hasMany(Student::class, 'DeptId');
	}
}
