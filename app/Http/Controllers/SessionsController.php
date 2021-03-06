<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class SessionsController extends Controller {
	public function __construct() {
		//只让未登录用户访问登录页面
		$this->middleware('guest', [
			'only' => ['create'],
		]);
	}

	//登录页面
	public function create() {
		return view('sessions.create');
	}

	//登录操作
	public function store(Request $request) {
		//数据验证过滤
		$credentials = $this->validate($request, [
			'email' => 'required|email|max:255',
			'password' => 'required',
		]);

		if (Auth::attempt($credentials, $request->has('remember'))) {
			//检查账号是否激活
			if (Auth::user()->activated) {
				session()->flash('success', '欢迎回来！');
				return redirect()->intended(route('users.show', [Auth::user()]));
			} else {
				Auth::logout();
				session()->flash('warring', '你的账号未激活，请检查邮箱中的注册邮件进行激活。');
				return redirect('/');
			}
		} else {
			session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
			return redirect()->back()->withInput();
		}
	}

	//退出
	public function destroy() {
		Auth::logout();
		session()->flash('success', '您已的成功退出！');
		return redirect('login');
	}
}
