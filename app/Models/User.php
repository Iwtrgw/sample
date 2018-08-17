<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
	use Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'email', 'password',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];

	protected $table = 'users';

	//监听用户注册，生成激活码
	public static function boot() {
		parent::boot();

		static::creating(function ($user) {
			$user->activation_token = str_random(30);
		});
	}

	public function gravatar($size = '100') {
		$hash = md5(strtolower(trim($this->attributes['email'])));

		return "http://www.gravatar.com/avatar/$hash?s=$size";
	}

	public function sendPasswordResetNotification($token) {
		$this->notify(new ResetPassword($token));
	}

    //关联statuses表
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    //展示用户所有的动态
    public function feed()
    {
        return $this->statuses()
                    ->orderBy('created_at','desc');
    }
}
