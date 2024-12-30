<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Province
 * 
 * @property int $Code
 * @property string|null $Name
 * 
 * @property Collection|District[] $districts
 * @property Collection|Lecturer[] $lecturers
 * @property Collection|Student[] $students
 *
 * @package App\Models
 */
class Province extends Model
{
	protected $table = 'province';
	protected $primaryKey = 'Code';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'Code' => 'int'
	];

	protected $fillable = [
		'Name'
	];

	public function districts()
	{
		return $this->hasMany(District::class, 'ProvinceCode');
	}

	public function lecturers()
	{
		return $this->hasMany(Lecturer::class, 'ProvinceCode');
	}

	public function students()
	{
		return $this->hasMany(Student::class, 'ProvinceCode');
	}
}
