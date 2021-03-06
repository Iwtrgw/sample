<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Auth;

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

    //展示用户及关注用户所有的动态
    public function feed()
    {
        $user_ids = Auth::user()->followings->pluck('id')->toArray();
        array_push($user_ids, Auth::user()->id);
        return Status::whereIn('user_id',$user_ids)
                            ->with('user')
                            ->orderBy('created_at','desc');
    }

    //查询用户粉丝列表
    public function followers()
    {
        return $this->belongsToMany(User::class,'followers','user_id','follower_id');
    }

    //查询用户关注列表
    public function followings()
    {
        return $this->belongsToMany(User::class,'followers','follower_id','user_id');
    }

    //关注用户
    public function follow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }

        $this->followings()->sync($user_ids,false);
    }

    //取消关注
    public function unfollow($user_ids)
    {
        if (is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }

        $this->followings()->detach($user_ids);
    }

    //判断是否关注
    public function isFollowing($user_id)
    {
        return $this->followings->contains($user_id);
    }
}
