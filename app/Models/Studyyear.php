<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Studyyear
 * 
 * @property int $Id
 * @property int $Number
 * @property Carbon $StartDate
 * @property Carbon $ExpireDate
 * @property int|null $StartYearId
 * @property int|null $EndYearId
 * 
 * @property Studyyeardetail|null $studyyeardetail
 * @property Collection|Curriculum[] $curricula
 *
 * @package App\Models
 */
class Studyyear extends Model
{
	protected $table = 'studyyear';
	protected $primaryKey = 'Id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'Id' => 'int',
		'Number' => 'int',
		'StartDate' => 'datetime',
		'ExpireDate' => 'datetime',
		'StartYearId' => 'int',
		'EndYearId' => 'int'
	];

	protected $fillable = [
		'Number',
		'StartDate',
		'ExpireDate',
		'StartYearId',
		'EndYearId'
	];

	public function studyyeardetail()
	{
		return $this->belongsTo(Studyyeardetail::class, 'StartYearId');
	}

	public function curricula()
	{
		return $this->hasMany(Curriculum::class, 'StudyYearId');
	}
}
