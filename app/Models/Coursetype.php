<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Coursetype
 * 
 * @property int $Id
 * @property string $Code
 * @property string $Name
 * @property string|null $Description
 * 
 * @property Collection|Course[] $courses
 *
 * @package App\Models
 */
class Coursetype extends Model
{
	protected $table = 'coursetype';
	protected $primaryKey = 'Id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'Id' => 'int'
	];

	protected $fillable = [
		'Code',
		'Name',
		'Description'
	];

	public function courses()
	{
		return $this->hasMany(Course::class, 'TypeId');
	}
}
