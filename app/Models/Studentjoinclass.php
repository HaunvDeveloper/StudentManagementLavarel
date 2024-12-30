<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Studentjoinclass
 * 
 * @property int $Id
 * @property string $StudentId
 * @property int $CourseClassId
 * @property Carbon $DateJoin
 * 
 * @property Courseclass $courseclass
 * @property Student $student
 *
 * @package App\Models
 */
class Studentjoinclass extends Model
{
	protected $table = 'studentjoinclass';
	protected $primaryKey = 'Id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'Id' => 'int',
		'CourseClassId' => 'int',
		'DateJoin' => 'datetime'
	];

	protected $fillable = [
		'StudentId',
		'CourseClassId',
		'DateJoin'
	];

	public function courseclass()
	{
		return $this->belongsTo(Courseclass::class, 'CourseClassId');
	}

	public function student()
	{
		return $this->belongsTo(Student::class, 'StudentId');
	}
}
