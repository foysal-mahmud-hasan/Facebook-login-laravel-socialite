<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
</head>
<body>
    <h1>Welcome to the Application</h1>
    @if (Auth::check())
        <p>Hello, {{ Auth::user()->name }}!</p>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Logout</button>
        </form>
    @else
        <p>Please <a href="{{ url('login') }}">login</a> or <a href="{{ url('register') }}">register</a>.</p>
    @endif
</body>
</html>
