<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>

    <h1>Login</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ url('login') }}" method="POST">
        @csrf
        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="{{ url('register') }}">Register</a></p>

    <div class="login-container">
        <h1>Login with Facebook</h1>
        <div id="login-status"></div>
        <fb:login-button 
            scope="public_profile,email"
            onlogin="checkLoginState();">
        </fb:login-button>
    </div>

    <script>
        // Initialize the Facebook SDK
        window.fbAsyncInit = function() {
            FB.init({
                appId      : '27179975718284365', // Replace with your app ID
                cookie     : true,
                xfbml      : true,
                version    : 'v21.0' // Use the latest version
            });
            FB.AppEvents.logPageView();
        };

        (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

        // Check login state
        function checkLoginState() {
            FB.getLoginStatus(function(response) {
                statusChangeCallback(response);
            });
        }

        function statusChangeCallback(response) {
            if (response.status === 'connected') {
                handleFacebookLogin(response.authResponse.accessToken);
            } else {
                document.getElementById('login-status').innerHTML = 'Please log in using Facebook.';
            }
        }

        function handleFacebookLogin(accessToken) {
            fetch('/login/facebook/callback', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ accessToken })
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      window.location.href = '/'; // Redirect on success
                  } else {
                      console.error('Login failed:', data);
                  }
              });
        }
    </script>
</body>
</html>
