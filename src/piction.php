<?php

namespace Braid\Piction;

use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Request;

class Piction
{
    /**
     * Guzzle client variable
     *
     * @var Client
     */
    protected $client;

    /**
     * Piction SURL variable
     *
     * @var string
     */
    protected $surl;

    public function __construct($surl = null)
    {
        if (!config('piction.host') ||
            !config('piction.user') ||
            !config('piction.pass')) {
            die('Piction environment variables not set.');
        }

        $this->surl = $surl;

        $this->client = new Client([
            'base_uri' => config('piction.host'),
            'timeout'  => config('piction.timeout', 300),
        ]);
    }

    public function recordModel()
    {
        return 'Braid\Piction\Models\\' .
            (config('piction.use_scout', false) ? 'Scout\Record' : 'Record');
    }

    /**
     * Get the set SURL or request one if not set
     *
     * @return string
     */
    public function getSURL()
    {
        if (is_null($this->surl)) {
            $this->requestSurl();
        }
        return $this->surl;
    }

    /**
     * Set SURL for class
     *
     * @param string $surl SURL to use to authenticate requests
     * @return void
     */
    public function setSURL($surl = null)
    {
        $this->surl = $surl;
    }

    /**
     * Retrieve Collections Information
     *
     * @return string
     */
    private function requestSurl()
    {
        $response = $this->client->get(config('piction.endpoint'), [
            'query' => [
                'n' => 'Piction_Login',
                'USERNAME' => config('piction.user'),
                'PASSWORD' => config('piction.pass')
            ]
        ]);

        if ($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody(), true);
            $this->setSURL($body['SURL']);
        } else {
            $this->setSURL(null);
        }

        return $this->surl;
    }

    /**
     * Retrieve Collections Information
     *
     * @return mixed
     */
    public function getCollections()
    {
        $response = $this->client->get(config('piction.endpoint'), [
            'query' => [
                'n' => 'CONTACT_COLLECTIONS',
                'SURL' => $this->getSURL(),
                'ACTION' => 'query',
            ]
        ]);

        if ($response->getStatusCode() == 200) {
            return json_decode(utf8_encode($response->getBody()->getContents()), true);
        }
        return false;
    }

    /**
     * Retrieve Collection Data
     *
     * @param integer $collection_id Specific collection to retrieve, null
     * will retrieve all collections
     * @param integer $page Page to retrieve
     * @param integer $perPage Number of records per page
     * @param string $since Retrieve records that have been added/updated since
     * this date format "Y-m-d", default is null
     * @param array $metaFields Array of meta fields you want to retrieve with the records
     *
     * @return mixed
     */
    public function getRecords(
        $collection_id = null,
        $page = 1,
        $perPage = null,
        $since = null,
        $metaFields = null
    ) {
        $page = $page < 1 ? 1 : $page;
        $perPage = is_numeric($perPage) && $perPage > 0 ?
            $perPage : config('piction.options.perpage', 50);

        $s = is_null($collection_id) ? 'security:4' : 'aid:' . $collection_id;
        if (!is_null($since)) {
            $s .= ' AND metadata_updated_between:' . $since;
        }

        $request = [
            'query' => [
                'n' => 'image_query',
                'WEB_URLS' => 'true',
                'SHOW_SORT_VALUE' => 'true',
                'START' => ($page - 1) * $perPage,
                'MAXROWS' => $perPage,
                'SURL' => $this->getSURL(),
                'SEARCH' => $s,
            ]
        ];

        if (is_array($metaFields)) {
            $request['query']['METATAGS'] = implode(',', $metaFields);
        } elseif (config('piction.options.meta.retrieve_all', true)) {
            $request['query']['ALL_METADATA'] = 'TRUE';
        }
        
        $response = $this->client->get(config('piction.endpoint'), $request);

        if ($response->getStatusCode() == 200) {
            $data = json_decode(utf8_encode($response->getBody()->getContents()), true);
            $max = (int) $data['s']['t'];
            $data['s']['maxpages'] = $max > 0 ? ceil($max / $perPage) : 0;
            return $data;
        } else {
            return false;
        }
    }

    /**
     * Retrieve deleted Piction UMO's for removal. Returns array of UMO's.
     *
     * @return mixed
     */
    public function getDeletedUmos()
    {
        $response = $this->client->get(config('piction.endpoint'), [
            'query' => [
                'n' => 'deleted_umo',
                'SURL' => $this->getSURL(),
            ]
        ]);

        if ($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody(), true);
            return array_pluck($body['deleted'], 'umo');
        }
        return null;
    }

}
