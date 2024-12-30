<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Ward
 * 
 * @property int $Code
 * @property string|null $Name
 * @property int $DistrictCode
 * 
 * @property District $district
 * @property Collection|Lecturer[] $lecturers
 * @property Collection|Student[] $students
 *
 * @package App\Models
 */
class Ward extends Model
{
	protected $table = 'ward';
	protected $primaryKey = 'Code';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'Code' => 'int',
		'DistrictCode' => 'int'
	];

	protected $fillable = [
		'Name',
		'DistrictCode'
	];

	public function district()
	{
		return $this->belongsTo(District::class, 'DistrictCode');
	}

	public function lecturers()
	{
		return $this->hasMany(Lecturer::class, 'WardCode');
	}

	public function students()
	{
		return $this->hasMany(Student::class, 'WardCode');
	}
}
