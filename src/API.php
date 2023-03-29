<?php

declare(strict_types=1);

namespace Nikba\Directus;

use Nikba\Directus\Request;
use Nikba\Directus\API\Helpers;

/**
 * The main class for all Directus API-Requests.
 *
 * @author Bargan Nicolai <office@nikba.com>
 */

class Directus extends Request
{
    /**
     * Directus project
     */
    protected ?string $project;

    /**
     *  one-time API token
     */
    protected string $token;

    /**
     * Helper class for query manipulation
     */
    public Helpers $helpers;

    /**
     * @param string $baseUrl The base URL of your Directus installation.
     * @param string|null $project The project you're targeting.
     */
    public function __construct(string $baseUrl, ?string $project = null)
    {
        parent::__construct($baseUrl);

        $this->helpers = new Helpers();
        $this->project = $project;
        $this->parameter('project', $project);
    }

    /**
     * Set's the API token.
     *
     * @param string $token one-time-token
     * @return object
     */
    public function token(string $token): self
    {
        $this->token = $token;

        return $this->header('Authorization', 'Bearer ' . $token);
    }

    /**
     * Check if the giben json object contains an error.
     *
     * @param object $json
     * @return boolean
     */
    public function isError(object $json): bool
    {
        return isset($json->error);
    }

    /**
     * Clears the last request.
     *
     * @return object
     */
    public function clear(): self
    {
        parent::clear();
        $this->parameter('project', $this->project);

        if (isset($this->token)) {
            $this->token($this->token);
        }

        return $this;
    }

    /**
     * Retrieve a Temporary Access Token
     *
     * @param string $email Email address of the user you're retrieving the access token for.
     * @param string $password Password of the user.
     * @param string|null $mode Choose between retrieving the token as a string, or setting it as a cookie. One of jwt, cookie. Defaults to jwt.
     * @param string|null $otp If 2FA is enabled, you need to pass the one time password.
     * @return object Returns the token (if jwt mode is used) and the user record for the user you just authenticated as.
     */
    public function authenticate(string $email, string $password, string $mode = null, string $otp = null): object
    {
        $response = $this->endpoint(':project/auth/authenticate')->attributes(compact('email', 'password', 'mode', 'otp'))->post();

        if (!$this->isError($response)) {
            $this->token($response->data->token);
        }

        return $response;
    }

    /**
     * Refresh a Temporary Access Token
     *
     * @return object
     */
    public function tokenRefresh(): object
    {
        $response = $this->endpoint(':project/auth/refresh')->attribute('token', $this->token)->post();

        if (!$this->isError($response)) {
            $this->token($response->data->token);
        }

        return $response;
    }

    /**
     * Endpoint for all collections
     *
     * @return object
     */
    public function custom($endpoint, array $parameters = []): self
    {
        return $this->endpoint('custom/' . ltrim($endpoint, '/'))->parameters($parameters);
    }

    /**
     * Endpoint for all activities
     *
     * @return Request
     */
    public function activities(): Request
    {
        return $this->endpoint(':project/activity');
    }

    /**
     * Endpoint for one activity
     *
     * @param integer $id
     * @return object
     */
    public function activity(int $id): self
    {
        return $this->endpoint(':project/activity/:id')->parameters(compact('id'));
    }

    /**
     * Endpoint for all comments
     *
     * @return object
     */
    public function comments(): self
    {
        return $this->endpoint(':project/activity/comment');
    }
    
    /**
     * Endpoint for all items
     *
     * @param integer $id
     * @return object
     */
    public function comment(int $id): self
    {
        return $this->endpoint(':project/activity/comment/:id')->parameters(compact('id'));
    }

    /**
     * Endpoint for all items
     *
     * @param string $key
     * @return object
     */
    public function asset(string $key): self
    {
        return $this->endpoint(':project/assets/:key')->parameters(compact('key'));
    }

    /**
     * Endpoint for all items
     *
     * @return object
     */
    public function collections(): self
    {
        return $this->endpoint(':project/collections');
    }

    /**
     * Endpoint for one collection
     *
     * @param string $collection
     * @return object
     */
    public function collection(string $collection): self
    {
        return $this->endpoint(':project/collections/:collection')->parameters(compact('collection'));
    }

    /**
     * Endpoint for all items
     *
     * @return object
     */
    public function interfaces(): self
    {
        return $this->endpoint('interfaces');
    }

    /**
     * Endpoint for all items
     *
     * @return object
     */
    public function layouts(): self
    {
        return $this->endpoint('layouts');
    }

    /**
     * Endpoint for all items
     *
     * @return object
     */
    public function modules(): self
    {
        return $this->endpoint('modules');
    }

    /**
     * Endpoint for all items
     *
     * @param string $collection
     * @return object
     */
    public function fields(?string $collection = null): self
    {
        if (is_null($collection)) {
            return $this->endpoint(':project/fields');
        }

        return $this->endpoint(':project/fields/:collection')->parameters(compact('collection'));
    }

    /**
     * @param string $collection
     * @param string $field
     * @return object
     */
    public function field(string $collection, string $field): self
    {
        return $this->endpoint(':project/fields/:collection/:field')->parameters(compact('collection', 'field'));
    }

    /**
     * Endpoint for all items
     *
     * @return object
     */
    public function files(): self
    {
        return $this->endpoint(':project/files');
    }

    /**
     * @param integer $id
     * @return object
     */
    public function file(string $id): self
    {
        return $this->endpoint(':project/files/:id')->parameters(compact('id'));
    }

    /**
     * @param integer $id
     * @return object
     */
    public function fileRevisions(int $id): self
    {
        return $this->endpoint(':project/files/:id/revisions')->parameters(compact('id'));
    }

    /**
     * @param integer $id
     * @param integer $offset
     * @return object
     */
    public function fileRevision(int $id, int $offset): self
    {
        return $this->endpoint(':project/files/:id/revisions/:offset')->parameters(compact('id', 'offset'));
    }

    /**
     * @return object
     */
    public function folders(): self
    {
        return $this->endpoint(':project/folders');
    }

    /**
     * @param integer $id
     * @return object
     */
    public function folder(int $id): self
    {
        return $this->endpoint(':project/folders/:id')->parameters(compact('id'));
    }

    /**
     * @param string $collection
     * @param integer $id
     * @return object
     */
    public function item(string $collection, $id): self
    {
        return $this->endpoint(':project/items/:collection/:id')->parameters(compact('collection', 'id'));
    }

    /**
     * @param string $collection
     * @return object
     */
    public function items(string $collection): self
    {
        return $this->endpoint(':project/items/:collection')->parameters(compact('collection'));
    }

    /**
     * @param string $collection
     * @param integer $id
     * @return object
     */
    public function itemRevisions(string $collection, $id): self
    {
        return $this->endpoint(':project/items/:collection/:id/revisions')->parameters(compact('collection', 'id'));
    }

    /**
     * @param string $collection
     * @param integer $id
     * @param integer $offset
     * @return object
     */
    public function itemRevision(string $collection, $id, int $offset): self
    {
        return $this->endpoint(':project/items/:collection/:id/revisions/:offset')->parameters(compact('collection', 'id', 'offset'));
    }

    /**
     * @param string $collection
     * @param integer $id
     * @param integer $revision
     * @return object
     */
    public function itemRevert(string $collection, $id, int $revision): self
    {
        return $this->endpoint(':project/items/:collection/:id/revert/:revision')->parameters(compact('collection', 'id', 'revision'));
    }

    /**
     * @return object
     */
    public function mail(): self
    {
        return $this->endpoint(':project/mail');
    }

    /**
     * @return object
     */
    public function presets(): self
    {
        return $this->endpoint(':project/collection_presets');
    }

    /**
     * @param integer $id Unique identifier of the item.
     * @return object
     */
    public function preset(int $id): self
    {
        return $this->endpoint(':project/collection_presets/:id')->parameters(compact('id'));
    }

    /**
     * @param string $super_admin_token
     * @return object
     */
    public function info(string $super_admin_token): self
    {
        return $this->endpoint('server/info')->queries(compact('super_admin_token'));
    }

    /**
     * @return object
     */
    public function ping(): self
    {
        return $this->endpoint('server/ping');
    }

    /**
     * @param string $string
     * @return object
     */
    public function hash(string $string): object
    {
        return $this->endpoint(':project/utils/hash')->attributes(compact('string'));
    }

    /**
     * @param string $string
     * @param string $hash
     * @return object
     */
    public function hashMatch(string $string, string $hash): object
    {
        return $this->endpoint(':project/utils/hash/match')->attributes(compact('string', 'hash'));
    }

    /**
     * @param integer $length Default: 32
     * @return object
     */
    public function randomString(int $length = 32): object
    {
        return $this->endpoint(':project/utils/random/string')->attributes(compact('length'));
    }

    /**
     * @return object
     */
    public function secret(): object
    {
        return $this->endpoint(':project/utils/2fa_secret');
    }

    /**
     * @return object
     */
    public function permissions(): object
    {
        return $this->endpoint(':project/permissions');
    }

    /**
     * @param integer $id
     * @return object
     */
    public function permission(int $id): object
    {
        return $this->endpoint(':project/permissions/:id')->parameters(compact('id'));
    }

    /**
     * @return object
     */
    public function myPermissions(): object
    {
        return $this->endpoint(':project/permissions/me');
    }

    /**
     * @param string $collection
     * @return object
     */
    public function myPermission(string $collection): object
    {
        return $this->endpoint(':project/permissions/me/:collection')->parameters(compact('collection'));
    }

    /**
     * @param string $project If null, it will return all projects
     * @return object
     */
    public function projects(?string $project = null): object
    {
        // TODO: this is a bug in the API, it should be server/projects/:project
        if (!is_null($project)) {
            return $this->endpoint('server/projects/:project')->parameters(compact('project'));
        }
        return $this->endpoint('server/projects');
    }

    /**
     * @param string $project
     * @return object
     */
    public function project(string $project): object
    {
        return $this->endpoint(':project/')->parameters(compact('project')); // should be server/projects/:project :-(
    }

    /**
     * @return object
     */
    public function relations(): object
    {
        return $this->endpoint(':project/relations');
    }

    /**
     * @param int $id
     * @return object
     */
    public function relation(int $id): object
    {
        return $this->endpoint(':project/relations/:id')->parameters(compact('id'));
    }

    /**
     * @return object
     */
    public function revisions(): object
    {
        return $this->endpoint(':project/revisions');
    }

    /**
     * @param int $id
     * @return object
     */
    public function revision(int $id): object
    {
        return $this->endpoint(':project/revisions/:id')->parameters(compact('id'));
    }

    /**
     * @return object
     */
    public function roles(): object
    {
        return $this->endpoint(':project/roles');
    }

    /**
     * @param int $id
     * @return object
     */
    public function role(int $id): object
    {
        return $this->endpoint(':project/roles/:id')->parameters(compact('id'));
    }

    /**
     * @return object
     */
    public function scimUsers()
    {
        return $this->endpoint(':project/scim/v2/Users');
    }

    /**
     * @param int $external_id
     * @return object
     */
    public function scimUser(int $external_id)
    {
        return $this->endpoint(':project/scim/v2/Users/:external_id')->parameters(compact('external_id'));
    }

    /**
     * @return object
     */
    public function scimGroups()
    {
        return $this->endpoint(':project/scim/v2/Groups');
    }

    /**
     * @param int $id
     * @return object
     */
    public function scimGroup(int $id)
    {
        return $this->endpoint(':project/scim/v2/Groups/:id')->parameters(compact('id'));
    }

    /**
     * @return object
     */
    public function settings(): object
    {
        return $this->endpoint(':project/settings');
    }

    /**
     * @param string $id
     * @return object
     */
    public function setting(string $id): object
    {
        return $this->endpoint(':project/settings/:id')->parameters(compact('id'));
    }

    /**
     * @return object
     */
    public function users(): object
    {
        return $this->endpoint(':project/users');
    }

    /**
     * @param int $id
     * @return object
     */
    public function user(int $id): object
    {
        return $this->endpoint(':project/users/:id')->parameters(compact('id'));
    }

    /**
     * @return object
     */
    public function me(): object
    {
        return $this->endpoint(':project/users/me');
    }

    /**
     * @param string $email
     * @return object
     */
    public function invite(string $email): object
    {
        return $this->endpoint(':project/users/invite')->attributes(compact('email'));
    }

    /**
     * @param string $token
     * @return object
     */
    public function acceptUser(string $token): object
    {
        return $this->endpoint(':project/users/invite/:token')->parameters(compact('token'));
    }

    /**
     * @param int $id
     * @param string $last_page
     * @return object
     */
    public function trackingPage(int $id, string $last_page): object
    {
        return $this->endpoint(':project/users/:id/tracking/page')->parameters(compact('id'))->attributes(compact('last_page'));
    }

    /**
     * @param int $id
     * @return object
     */
    public function userRevisions(int $id): object
    {
        return $this->endpoint(':project/users/:id/revisions')->parameters(compact('id'));
    }

    /**
     * @param int $id
     * @param int $offset
     * @return object
     */
    public function userRevision(int $id, int $offset): object
    {
        return $this->endpoint(':project/users/:id/revisions/:offset')->parameters(compact('id', 'offset'));
    }

    /**
     * @param string|array $fields
     * @return $this
     * Return the result as if it was a single item. Useful for selecting a single item based on filters and search queries. Will force limit to be 1.
     */
    public function _single($single = true): self
    {
        return $this->query('single', $this->helpers->single($single));
    }

    /**
     * @param string|array $fields
     * @return $this
     * A limit on the number of objects that are returned. Default is 200
     */
    public function _limit($limit): self
    {
        return $this->query('limit', $this->helpers->limit($limit));
    }

    /**
     * @param string|array $fields
     * @return $this
     */
    public function all(): self
    {
        return $this->_limit(-1);
    }

    /**
     * @param string|array $fields
     * @return $this
     * How many items to skip when fetching data. Default is 0.
     */
    public function _offset($offset): self
    {
        return $this->query('offset', $this->helpers->offset($offset));
    }

    /**
     * @param string|array $fields
     * @return $this
     * The page number to return. Default is 1.
     */
    public function _page($page): self
    {
        return $this->query('page', $this->helpers->page($page));
    }

    /**
     * @param string|array $fields
     * @return $this
     * What metadata to return in the response.
     */
    public function _meta($meta = '*'): self
    {
        return $this->query('meta', $this->helpers->meta($meta));
    }

    /**
     * @param string|array $status
     * @return $this
     * Filter items by the given status. 
     */
    public function _status($status = '*'): self
    {
        return $this->query('status', $this->helpers->status($status));
    }

    /**
     * @param string|array $sort
     * @return $this
     * How to sort the returned items.
     */
    public function _sort($sort): self
    {
        return $this->query('sort', $this->helpers->sort($sort));
    }

    /**
     * @param string|array $q
     * @return $this
     * Filter by items that contain the given search query in one of their fields.
     */
    public function _q($q): self
    {
        return $this->query('q', $this->helpers->q($q));
    }

    /**
     * @param string|array $filter
     * @return $this
     * Select items in collection by given conditions. 
     */
    public function _filter($filter): self
    {
        return $this->query('filter', $this->helpers->filter($filter));
    }

    /**
     * @param string|array $fields
     * @return $this
     * Control what fields are being returned in the object.
     */
    public function _fields($fields): self
    {
        return $this->query('fields', $this->helpers->fields($fields));
    }
}