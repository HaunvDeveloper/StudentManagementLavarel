<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Authentication
 * 
 * @property int $Id
 * @property string $Code
 * @property string $Name
 * @property string|null $Description
 * 
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class Authentication extends Model
{
	protected $table = 'authentication';
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

	public function users()
	{
		return $this->hasMany(User::class, 'AuthId');
	}
}
