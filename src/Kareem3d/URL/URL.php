<?php namespace Kareem3d\URL;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Request;
use Kareem3d\Eloquent\Model;

class URL extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'urls';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array();

    /**
     * The attributes that can't be mass assigned
     *
     * @var array
     */
    protected $guarded = array('id');

    /**
     * @var array
     */
    protected static $dontDuplicate = array(array('uri', 'domain'));

    /**
     * @var URL|null
     */
    protected static $currentUrl = null;

    /**
     * @return string
     */
    public static function getCurrentUri()
    {
        return urldecode(Request::path());
    }

    /**
     * @return mixed
     */
    public static function getCurrentDomain()
    {
        return Request::server('SERVER_NAME');
    }

    /**
     * @return URL
     */
    public static function getCurrent()
    {
        if(! static::$currentUrl)

            static::$currentUrl = static::getByUriAndDomain(static::getCurrentUri(), static::getCurrentDomain());

        return static::$currentUrl;
    }

    /**
     * @param $uri
     * @param $domain
     * @return URL
     */
    public static function getByUriAndDomain( $uri, $domain )
    {
        $uri = rtrim($uri, '/');

        return static::where('uri', $uri)->orWhere('uri', '/' . $uri)->where(function(Builder $query) use ($domain)
        {
            $query->where('domain', $domain)
                ->orWhere('domain', NULL);

        })->first();
    }

    /**
     * @param string $uri
     * @return bool
     */
    public static function isActive( $uri )
    {
        return static::getCurrent()->sameUri( $uri );
    }

    /**
     * @param $uri
     * @return bool
     */
    public function sameUri( $uri )
    {
        return trim($this->uri, '/') == trim($uri, '/');
    }

    /**
     * Get full url => $domain.'/'.$uri
     *
     * @return string
     */
    public function getUrl()
    {
        return rtrim($this->getDomain(), '/') . '/' . ltrim($this->getUri(), '/');
    }

    /**
     * @return mixed
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return bool
     */
    public function hasDomain()
    {
        return $this->getDomain() != null;
    }
}