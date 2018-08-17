<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Mail;

class UsersController extends Controller {

	public function __construct() {
		//未登录用户可访问
		$this->middleware('auth', [
			'except' => ['create', 'store', 'index', 'confirmEmail'],
		]);

		//只让未登录用户访问注册页面
		$this->middleware('guest', [
			'only' => ['create'],
		]);
	}

	//显示所有用户，每页10条
	public function index() {
		$users = User::paginate(10);
		return view('users.index', compact('users'));
	}

	//注册页面
	public function create() {
		return view('users.create');
	}

	//用户个人中心
	public function show(User $user) {
        //查询用户个人动态按发布时间排序
        $statuses = $user->statuses()
                         ->orderBy('created_at','desc')
                         ->paginate(20);

		return view('users.show', compact('user','statuses'));
	}

	//注册动作
	public function store(Request $request) {
		//提交的数据验证
		$this->validate($request, [
			'name' => 'required|max:50',
			'email' => 'required|email|unique:users|max:255',
			'password' => 'required|confirmed|min:6',
		]);

		//创建用户
		$user = User::create([
			'name' => $request->name,
			'email' => $request->email,
			'password' => bcrypt($request->password),
		]);

		$this->sendEmailConfirmationTo($user);
		session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。 ');
		return redirect('/');
	}

	//用户编辑页面
	public function edit(User $user) {
		//用户权限验证
		$this->authorize('update', $user);

		return view('users.edit', compact('user'));
	}

	//用户更新
	public function update(User $user, Request $request) {
		//数据验证过滤
		$this->validate($request, [
			'name' => 'required|max:50',
			'password' => 'nullable|confirmed|min:6',
		]);

		//权限验证
		$this->authorize('update', $user);

		//更新操作
		$data = [];
		$data['name'] = $request->name;
		if ($request->password) {
			$data['password'] = bcrypt($request->password);
		}
		$user->update($data);

		session()->flash('success', '个人资料更新成功！');

		return redirect()->route('users.show', $user->id);
	}

	//删除用户
	public function destroy(User $user) {
		$this->authorize('destroy', $user);
		$user->delete();
		session()->flash('success', '成功删除用户！');
		return back();
	}

	//邮件发送
	protected function sendEmailConfirmationTo($user) {
		//展示视图
		$view = 'emails.confirm';

		//视图展示的数据
		$data = compact('user');


		//发送邮件的地址
		$to = $user->email;

		//提示信息
		$subject = "感谢注册 Sample 应用！请确认你的邮箱。 ";

		Mail::send($view, $data, function ($message) use ( $to, $subject) {
			$message->to($to)->subject($subject);
		});

	}

	//注册激活码检验，如果成功就登录
	public function confirmEmail($token) {
		$user = User::where('activation_token', $token)->firstOrFail();

		$user->activated = true;
		$user->activation_token = null;
		$user->save();

		Auth::login($user);
		session()->flash('success', '恭喜你，激活成功！');
		return redirect()->route('users.show', [$user]);
	}
}
