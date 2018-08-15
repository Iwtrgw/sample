<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Auth;

class UsersController extends Controller {

    public function __construct()
    {
        //未登录用户可访问
        $this->middleware('auth',[
            'except' => ['create','store','index']
        ]);


        //只让未登录用户访问注册页面
        $this->middleware('guest',[
            'only' => ['create']
        ]);
    }


    public function index()
    {
        $users = User::paginate(10);
        return view('users.index',compact('users'));
    }

    //注册页面
	public function create() {
		return view('users.create');
	}

    //用户个人中心
	public function show(User $user) {
		return view('users.show', compact('user'));
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

        Auth::login($user);
		session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');

		return redirect()->route('users.show', [$user]);
	}

    //用户编辑页面
    public function edit(User $user)
    {
        //用户权限验证
        $this->authorize('update',$user);

        return view('users.edit',compact('user'));
    }

    //用户更新
    public function update(User $user,Request $request)
    {
        //数据验证过滤
        $this->validate($request,[
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        //权限验证
        $this->authorize('update',$user);

        //更新操作
        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        session()->flash('success','个人资料更新成功！');

        return redirect()->route('users.show',$user->id);
    }

    //删除用户
    public function destroy(User $user)
    {
        $this->authorize('destroy',$user);
        $user->delete();
        session()->flash('success','成功删除用户！');
        return back();
    }
}
