<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Major
 * 
 * @property int $Id
 * @property string $Code
 * @property string $Name
 * @property int|null $DeptId
 * 
 * @property Department|null $department
 * @property Collection|Curriculum[] $curricula
 * @property Collection|Student[] $students
 *
 * @package App\Models
 */
class Major extends Model
{
	protected $table = 'major';
	protected $primaryKey = 'Id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'Id' => 'int',
		'DeptId' => 'int'
	];

	protected $fillable = [
		'Code',
		'Name',
		'DeptId'
	];

	public function department()
	{
		return $this->belongsTo(Department::class, 'DeptId');
	}

	public function curricula()
	{
		return $this->hasMany(Curriculum::class, 'MajorId');
	}

	public function students()
	{
		return $this->hasMany(Student::class, 'MajorId');
	}
}
