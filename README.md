
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
## Documentation
For more information on the available methods and parameters, please refer to the [API Documentation](https://v8.docs.directus.io/api/reference.html)


## Support

For support, email office@nikba.com.


## Contributing

Contributions are always welcome!

If you find a bug or would like to contribute to this project, please open an issue or submit a pull request on GitHub.


## License

[MIT](https://choosealicense.com/licenses/mit/)

