
# directus-api

This is a PHP wrapper for the Directus v8.8.1 API, which allows you to easily interact with Directus from your PHP applications.

Directus is an open-source headless CMS and API that provides a sleek interface for managing your content, while allowing you to use your favorite frontend tools and frameworks.

With this API wrapper, you can programmatically create, read, update, and delete items and collections in Directus, as well as manage users, permissions, and settings.




## Installation

You can install this package via Composer by running the following command:

```bash
  composer require nikba/directus-api
```
    
## Usage/Examples
To get started with this API wrapper, you'll need to first create an instance of the Nikba\Directus class, passing in your Directus API URL and Project Name:

### Creating an API-instance
```php
use Nikba\Directus\Directus;

/**
* @param string $url
* @param string $project
*/

$directus = new Directus('https://api.nikba.com/', 'projectname');
```

### Authentification
```php
/**
* Temporary (JWT)
* These tokens are generated through the /auth/authenticate endpoint (below) and have a lifespan of 20 minutes.
* @param string $username
* @param string $password
*/
$directus->authenticate('username', '********');

/**
* Using Static token
* Each user can have one static token that will never expire. This is useful for server-to-server communication, but is also less secure than the JWT token. 
* @param string $token
*/
$directus->token('ThIs_Is_ThE_tOkEn');
```

### Error handling
```php
$items = $directus->items($collection)->get();

if($directus->isError($items)) {
    // The request failed.
}
```

Once you have a client instance, you can use it to make API requests.

### Items
Items are individual pieces of data in your database. They can be anything, from articles, to IoT status checks.
```php
/**
* List the Items
* @param string $collection
*/
$items = $directus->items($collection)->get();

/**
* Fields
* Optional
* Control what fields are being returned in the object. 
* @param string $fields
* _fields("fields1,field2,...")
*/
$items = $directus->items($collection)->_fields("field1,field2")->get();

/**
* Limit
* Optional
* A limit on the number of objects that are returned. Default is 200
* @param integer $limit
* _limit(10)
*/
$items = $directus->items($collection)->_limit(10)->get();

/**
* Offset
* Optional
* How many items to skip when fetching data. Default is 0.
* @param integer $offset
* _offset(10)
*/
$items = $directus->items($collection)->_offset(10)->get();

/**
* Sort
* Optional
* How to sort the returned items. 
* @param string $sort
* _sort("-id,sort")
*/
$items = $directus->items($collection)->_sort("-id,sort")->get();

/**
* Single
* Optional
* Return the result as if it was a single item. Useful for selecting a single item based on filters and search queries. Will force limit to be 1.
* _single()
*/
$items = $directus->items($collection)->_single()->get();

/**
* Status
* Optional
* Filter items by the given status.
* @param string $status
* _status("published,under_review,draft")
*/
$items = $directus->items($collection)->_status("published,under_review,draft")->get();

/**
* Filter
* Optional
* Select items in collection by given conditions.
* @param array $filter
* _filter(["page_id" => $id])
*/
$items = $directus->items($collection)->_filter(["page_id" => $id])->get();


/**
* Retrieve an Item
* @param string $collection
* @param integer $id
*/
$item = $directus->item($collection, $id)->get();

/**
* Create an Item
* @param string $collection
* @param array $params
*/
$directus->items($collection)->create([
    'title' => 'The Post Title',
    'status' => 'Published'
]);

/**
* Update an Item
* @param string $collection
* @param integer $id
* @param array $params
*/
$directus->item($collection, $id)->update([
    'title' => 'The New Post Title'
]);

/**
* Delete an Item
* @param string $collection
* @param integer $id
*/
$directus->item($collection, $id)->delete();

/**
* List Item Revisions
* @param string $collection
* @param integer $id
*/
$revisions = $directus->itemRevisions($collection, $id)->get();


/**
* Revert to a Given Revision
* @param string $collection
* @param integer $id
* @param integer $revision
*/
$directus->itemRevert($collection, $id, $revision)->update();
```

### Files
Files can be saved in any given location. Directus has a powerful assets endpoint that can be used to generate thumbnails for images on the fly.
```php
/**
* List the Files
*/
$files = $directus->files()->get();

/**
* Retrieve a File
* @param integer $id
*/
$file = $directus->file($id)->get();

/**
* Create a File
* @param array $data
*/
$directus->files()->create([
    'data' => base64_encode(file_get_contents('./file.pdf'))
]);

/**
* Update a File
* @param array $data
*/
$directus->file(1)->update([
    'data' => base64_encode(file_get_contents('./file.pdf'))
]);

/**
* Delete a File
* @param integer $id
*/
$directus->file(1)->delete();
```

### Assets (Thumbnails)
Image typed files can be dynamically resized and transformed to fit any need.
```php
/**
* Get an asset
* @param string $private_hash
*/
$asset = $directus->asset($private_hash)->get();


/**
* Get an asset
* @param string $private_hash
* @param string $key - The key of the asset size configured in settings.
*
* @param integer $w
* Width of the file in pixels.
* 
* @param integer $h
* Height of the file in pixels.
*
* @param string $f
* Fit. One of crop, contain.
*
* @param integer $q
* Quality of compression. Number between 1 and 100.
*/
$asset = $directus->asset($private_hash)->queries([
    'key' => $key,
    'w' => 100,
    'h' => 100,
    'f' => 'crop',
    'q' => 80
])->get();
```

### Folders
Folders don't do anything yet, but will be used in the (near) future to be able to group files.
```php
/**
* List the Folders
*/
$folders = $directus->folders()->get();

/**
* Retrieve a Folder
* @param integer $id
*/
$folder = $directus->folder($id)->get();
 
/**
* Create a Folder
* @param array $data
*/
$directus->folders()->create([
    'name' => 'Amsterdam'
]);

/**
* Update a Folder
* @param integer $id
* @param array $data
*/
$directus->folder($id)->update([
    'parent_folder' => 3
]);

/**
* Delete a Folder
* @param integer $id
*/
$directus->folder($id)->delete();
```

### Activities
All events that happen within Directus are tracked and stored in the activities collection. This gives you full accountability over everything that happens.
```php
/**
* List Activity Actions
*/
$activities = $directus->activities()->get();

/**
* Retrieve an Activity Action
* @param integer $id
*/
$activity = $directus->activity($id)->get();

/**
* Create a Comment
* @param array $data
*/
$directus->comments()->create([
    'collection' => $collection,
    'id' => $id,
    'comment' => 'Example of comment'
]);

/**
* Update a Comment
* @param array $data
*/
$directus->comment($id)->update([
    'comment' => 'Updated example of comment'
]);

/**
* Delete a Comment
* @param integer $id
*/
$directus->comment($id)->delete();
```

### Permissions
Permissions control who has access to what and when.

```php
/**
* List the Permissions
*/
$permissions = $directus->permissions()->get();

/**
* Retrieve a Permission
* @param integer $id
*/
$permission = $directus->permission($id)->get();

/**
* List the Current User's Permissions
*/
$permissions = $directus->myPermissions()->get();

/**
* List the Current User's Permissions for Given Collection
* @param string @collection
*/
$permission = $directus->myPermission($collection)->get();

/**
* Create a Permission
* @param array @data
*/
$directus->permissions()->create([
    'collection' => 'customers',
    'role' => 3,
    'read' => 'mine',
    'read_field_blacklist' => ['featured_image']
]);

/**
* Update a Permission
* @param integer @id
* @param array @data
*/
$directus->permission($id)->update([
    'read' => 'full'
]);

/**
* Delete a Permission
* @param integer @id
*/
$directus->permission($id)->delete();
```

### Projects
Projects are the individual tenants of the platform. Each project has its own database and data.

```php
/**
* List Available Projects
*/
$projects = $directus->projects()->get();

/**
* Retrieve Project Info
* @param string $project
*/
$project = $directus->project($project)->get();

/**
* Create a Project
* @param array $data
*/
$directus->projects()->create([
    'project' => 'thumper',
    'super_admin_token' => 'very_secret_token',
    'db_name' => 'db',
    'db_user' => 'root',
    'db_password' => 'root',
    'user_email' => 'admin@example.com',
    'user_password' => 'password'
]);

/**
* Delete a Project
* @param array $project
*/
$directus->projects($project)->delete();
```

### Relations
What data is linked to what other data. Allows you to assign authors to articles, products to sales, and whatever other structures you can think of.

```php
/**
* List the Relations
*/
$relations = $directus->relations()->get();

/**
* Retrieve a Relation
* @param integer $id
*/
$relation = $directus->relation($id)->get();

/**
* Create a Relation
* @param array $data
*/
$directus->relations()->create([
    'collection_many' => 'articles',
    'field_many' => 'author',
    'collection_one' => 'authors',
    'field_one' => 'books'
]);

/**
* Update a Relation
* @param integer $id
* @param array $data
*/
$directus->relation($id)->update([
    'field_one' => 'books'
]);

/**
* Delete a Relation
* @param integer $id
*/
$directus->relation($id)->delete();
```

### Revisions
Revisions are individual changes to items made. Directus keeps track of changes made, so you're able to revert to a previous state at will.

```php
/**
* List the Revisions
*/
$revisions = $directus->revisions()->get();

/**
* Retrieve a Revision
* @param integer $id
*/
$revision = $directus->revision($id)->get();
```

### Roles
Roles are groups of users that share permissions.

```php
/**
* List the Roles
*/
$roles = $directus->roles()->get();

/**
* Retrieve a Role
* @param integer $id
*/
$role = $directus->role($id)->get();

/**
* Create a Role
* @param array $data
*/
$directus->roles()->create([
    'name' => 'Interns'
]);

/**
* Update a Role
* @param integer $id
* @param array $data
*/
$directus->role($id)->update([
    'description' => 'Limited access only.'
]);

/**
* Delete a Role
* @param integer $id
*/
$directus->role($id)->delete();
```

### Server
Access to where Directus runs. Allows you to make sure your server has everything needed to run the platform, and check what kind of latency we're dealing with.

```php
/**
* Retrieve Server Info
* Perform a system status check and return the options.
* @param string $token
*/
$info = $directus->info($token)->get();

/**
* Ping the server
* Ping, pong. Ping.. pong. 🏓
*/
$pong = $directus->ping()->get();
```

### Settings
Settings control the way the platform works and acts.

```php
/**
* List the Settings
* The Setting Object
*/
$settings = $directus->settings()->get();

/**
* Retrieve a Setting
* @param integer $id
*/
$setting = $directus->setting($id)->get();

/**
* Create a Setting
* @param array $data
*/
$directus->settings()->create([
    'key' => 'my_custom_setting',
    'value' => 12
]);

/**
* Update a Setting
* @param integer $id
*/
$directus->setting($id)->update([
    'value' => 15
]);

/**
* Delete a Setting
* @param integer $id
*/
$directus->setting($id)->delete();
```

### Users
Users are what gives you access to the data.

```php
/**
* List the users
*/
$users = $directus->users()->get();

/**
* Retrieve a User
* @param integer $id
*/
$user = $directus->user($id)->get();

/**
* Retrieve the Current User
*/
$me = $directus->me()->get();

/**
* Create a User
* Create a new user.
* @param array $data
*/
$directus->users()->create([
    'first_name' => 'John',
    'last_name' => 'Smith',
    'email' => 'user@nikba.com',
    'password' => 'VeryStrongPassword',
    'role' => 1,
    'status' => 'active'
]);

/**
* Update a User
* Update an existing user
* @param integer $id
* @param array $data
*/
$directus->user($id)->update([
    'status' => 'suspended'
]);

/**
* Delete a User
* Delete an existing user
* @param integer $id
*/
$directus->user($id)->delete();

/**
* Invite a New User
* Invites one or more users to this project. It creates a user with an invited status, and then sends an email to the user with instructions on how to activate their account.
* @param integer $email
*/
$directus->invite('user@nikba.com')->create();

/**
* Accept User Invite
* Accepts and enables an invited user using a JWT invitation token.
* @param string $token
*/
$directus->acceptUser($token)->post();

/**
* Track the Last Used Page
* Updates the last used page field of the user. This is used internally to be able to open the Directus admin app from the last page you used.
* @param integer $id
* @param string $url
*/
$directus->trackingPage($id, '/thumper/settings/')->update();

/**
* List User Revisions
* List the revisions made to the given user.
* @param integer $id
*/
$revisions = $directus->userRevisions($id)->get();

/**
* Retrieve a User Revision
* Retrieve a single revision of the user by offset.
* @param integer $id
* @param integer $offset
*/
$revision = $directus->userRevision($id, $offset)->get();
```

### Mail
Send electronic mail through the electronic post.

```php
/**
* Send an Email
* @param array $to
* @param string $subject
* @param string $body
* @param string $type
* HTML or plain text
* @param array $data
*/
$directus->mail()->create([
    'to' => [
        'client@example.com',
        'admin@example.com'
    ],
    'subject' => 'Welcome message',
    'body' => 'Hello <b>{{name}}</b>, welcome to our website: {{website}}.',
    'type' => 'html',
    'data' => [
        'name' => 'John Smith',
        'website' => 'www.nikba.com'
    ]
]);
```
### Utilities
```php
/**
* Create a Hash
* Create a hash for a given string.
* @param string $StringToHash
*/
$hash = $directus->hash('StringToHash')->create();

/**
* Verify a Hashed String
* Check if a hash is valid for a given string.
* @param string $StringToHash
* @param string $hash
*/
$valid = $directus->hashMatch('StringToHash', $hash)->create();

/**
* Generate a Random String
* Returns a random string of given length.
* @param integer $length
*/
$string = $directus->randomString($length)->create();

/**
* Generate a 2FA Secret
* Returns a random string that can be used as a 2FA secret
*/
$secret = $directus->secret()->get();
```
## Documentation
For more information on the available methods and parameters, please refer to the [API Documentation](https://v8.docs.directus.io/api/reference.html)


## Support

For support, email office@nikba.com.


## Contributing

Contributions are always welcome!

If you find a bug or would like to contribute to this project, please open an issue or submit a pull request on GitHub.


## License

[MIT](https://choosealicense.com/licenses/mit/)

