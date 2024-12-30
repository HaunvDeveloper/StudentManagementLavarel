<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Studentclass
 * 
 * @property int $Id
 * @property string $Code
 * @property string $Name
 * @property int|null $LecturerId
 * 
 * @property Lecturer|null $lecturer
 * @property Collection|Courseclass[] $courseclasses
 * @property Collection|Student[] $students
 *
 * @package App\Models
 */
class Studentclass extends Model
{
	protected $table = 'studentclass';
	protected $primaryKey = 'Id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'Id' => 'int',
		'LecturerId' => 'int'
	];

	protected $fillable = [
		'Code',
		'Name',
		'LecturerId'
	];

	public function lecturer()
	{
		return $this->belongsTo(Lecturer::class, 'LecturerId');
	}

	public function courseclasses()
	{
		return $this->hasMany(Courseclass::class, 'StudentClassId');
	}

	public function students()
	{
		return $this->hasMany(Student::class, 'StudentClassId');
	}
}
