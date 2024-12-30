<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class District
 * 
 * @property int $Code
 * @property string|null $Name
 * @property int $ProvinceCode
 * 
 * @property Province $province
 * @property Collection|Lecturer[] $lecturers
 * @property Collection|Student[] $students
 * @property Collection|Ward[] $wards
 *
 * @package App\Models
 */
class District extends Model
{
	protected $table = 'district';
	protected $primaryKey = 'Code';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'Code' => 'int',
		'ProvinceCode' => 'int'
	];

	protected $fillable = [
		'Name',
		'ProvinceCode'
	];

	public function province()
	{
		return $this->belongsTo(Province::class, 'ProvinceCode');
	}

	public function lecturers()
	{
		return $this->hasMany(Lecturer::class, 'DistrictCode');
	}

	public function students()
	{
		return $this->hasMany(Student::class, 'DistrictCode');
	}

	public function wards()
	{
		return $this->hasMany(Ward::class, 'DistrictCode');
	}
}
