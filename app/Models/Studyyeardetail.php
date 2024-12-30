<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Studyyeardetail
 * 
 * @property int $Id
 * @property int $StartYear
 * @property int $EndYear
 * 
 * @property Collection|Semester[] $semesters
 * @property Collection|Studyyear[] $studyyears
 *
 * @package App\Models
 */
class Studyyeardetail extends Model
{
	protected $table = 'studyyeardetail';
	protected $primaryKey = 'Id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'Id' => 'int',
		'StartYear' => 'int',
		'EndYear' => 'int'
	];

	protected $fillable = [
		'StartYear',
		'EndYear'
	];

	public function semesters()
	{
		return $this->hasMany(Semester::class, 'SchoolYearDetailId');
	}

	public function studyyears()
	{
		return $this->hasMany(Studyyear::class, 'StartYearId');
	}
}
