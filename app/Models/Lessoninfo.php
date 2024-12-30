<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Lessoninfo
 * 
 * @property int $Id
 * @property Carbon $StartTime
 * @property Carbon $EndTime
 * 
 * @property Collection|Lesson[] $lessons
 *
 * @package App\Models
 */
class Lessoninfo extends Model
{
	protected $table = 'lessoninfo';
	protected $primaryKey = 'Id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'Id' => 'int',
		'StartTime' => 'datetime',
		'EndTime' => 'datetime'
	];

	protected $fillable = [
		'StartTime',
		'EndTime'
	];

	public function lessons()
	{
		return $this->hasMany(Lesson::class, 'EndLesson');
	}
}
