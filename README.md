# Authorization

### Constructor

`$authorization = new Authorization($conn)`

`$conn` shoud be a instance of PDO class.

### Database requeriments

#### Tables

| user | user_session |
| ---- | ------------ |
| id `int` | id `int` |
| username `varchar` | user `int` |
| password `varchar` | token `varchar(20)` |
|| active `boolean` |

### Methods

##### `$authorization->login($username, $password)`  
Verify if there is a user that match `$username` and `$password`. If yes, create a `user_session` with a random `token` string.

| Returns |
| ------ |
| true |
| [ 'error' => 'User not found', ] |

##### `$authorization->verifyToken($token)`  
Verify if `$token` exists if it is active.

| Returns |
| ------ |
| true |
| [ 'error' => 'Token not found', ] |
| [ 'error' => 'Token is not active', ] |

##### `$authorization->logout()`  
Turn the `$token` disactived. (You should call `verifyToken` or `login` methods before calling this method, to know the reference `id` of `user_session`)

| Returns |
| ------ |
| true |
| [ 'error' => 'You should call verifyToken or login first' ] |

### Attributes
After you call `login` or `verifyToken` methods and it returns `true`, you can access these attributes.

##### `$authorization->user`
````
[
	id => Integer,
]
````

##### `$authorization->user_session`
````
[
	id => Integer,
    token => String(20),
    active => Boolean,
]
````


### Examples

#### Login
````
$auth = new Authorization($conn);

$result = $auth->login('mathues', 'test');

if ($result !== true) {
	# if result not true, return error
	echo json_encode($result);
    exit(1);
}

# return token to user store it locally for nexts requests
echo json_encode([
	'token' => $auth->user_session['token'],
]);
````

#### Verify token
````
$auth = new Authorization($conn);

# token sent by user
$token = '....................';
$result = $auth->verifyToken($token);

if ($result !== true) {
	# if result not true, return error
	echo json_encode($result);
    exit(1);
}

# attribute $auth->user is avaiable now, do whatever you want

echo 'Hello ' . $auth->user['id'] . '!';
````

#### Logout
````
$auth = new Authorization($conn);

# token sent by user
$token = '....................';
$result = $auth->verifyToken($token);

if ($result !== true) {
	# if result not true, return error
	echo json_encode($result);
    exit(1);
}

$result = $auth->logout();

if ($result !== true) {
	# if result not true, return error
	echo json_encode($result);
    exit(1);
}

echo 'You have logout!';
````