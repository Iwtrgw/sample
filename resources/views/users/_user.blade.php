 <li>
    <img src="{{ $user->gravatar() }}" alt="{{ $user->name }}" class="gravatar"/>
    <a href="{{ route('users.show',$user->id) }}" class="username">{{ $user->name }}</a>

    @can('destroy', $user)
        <form action="{{ route('users.destroy',$user->id) }}" method="POST">
            {{ csrf_field() }}
            {{ method_field('DELETE') }}
            <button class="but btn-sm btn-danger delete-btn" type="submit">删除</button>
        </form>
    @endcan
</li>