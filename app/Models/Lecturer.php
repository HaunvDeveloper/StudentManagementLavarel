<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Lecturer
 * 
 * @property int $Id
 * @property string $FullName
 * @property string|null $Email
 * @property Carbon $DayOfBirth
 * @property Carbon $HiredDate
 * @property int $DeptId
 * @property int|null $UserId
 * @property string $NationId
 * @property string $BirthPlace
 * @property int|null $ProvinceCode
 * @property int|null $DistrictCode
 * @property int|null $WardCode
 * @property string|null $StreetAddress
 * @property string $Sex
 * @property string|null $PhoneNo
 * @property string|null $Nation
 * @property string|null $Religion
 * 
 * @property District|null $district
 * @property Province|null $province
 * @property User|null $user
 * @property Ward|null $ward
 * @property Department $department
 * @property Collection|Courseclass[] $courseclasses
 * @property Collection|Studentclass[] $studentclasses
 *
 * @package App\Models
 */
class Lecturer extends Model
{
	protected $table = 'lecturer';
	protected $primaryKey = 'Id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'Id' => 'int',
		'DayOfBirth' => 'datetime',
		'HiredDate' => 'datetime',
		'DeptId' => 'int',
		'UserId' => 'int',
		'ProvinceCode' => 'int',
		'DistrictCode' => 'int',
		'WardCode' => 'int'
	];

	protected $fillable = [
		'Id',
		'FullName',
		'Email',
		'DayOfBirth',
		'HiredDate',
		'DeptId',
		'UserId',
		'NationId',
		'BirthPlace',
		'ProvinceCode',
		'DistrictCode',
		'WardCode',
		'StreetAddress',
		'Sex',
		'PhoneNo',
		'Nation',
		'Religion'
	];

	public function district()
	{
		return $this->belongsTo(District::class, 'DistrictCode');
	}

	public function province()
	{
		return $this->belongsTo(Province::class, 'ProvinceCode');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'UserId');
	}

	public function ward()
	{
		return $this->belongsTo(Ward::class, 'WardCode');
	}

	public function department()
	{
		return $this->belongsTo(Department::class, 'DeptId');
	}

	public function courseclasses()
	{
		return $this->hasMany(Courseclass::class, 'LecturerId');
	}

	public function studentclasses()
	{
		return $this->hasMany(Studentclass::class, 'LecturerId');
	}
}
