<?php
namespace agoalofalife\bpm\Actions;

use agoalofalife\bpm\Assistants\AuthenticationHelper;
use agoalofalife\bpm\Assistants\ConstructorUrl;
use agoalofalife\bpm\Assistants\QueryBuilder;
use agoalofalife\bpm\Contracts\Action;
use agoalofalife\bpm\Contracts\ActionGet;
use agoalofalife\bpm\Contracts\Handler;
use agoalofalife\bpm\KernelBpm;
use Assert\Assert;


/**
 * Class Read
 * @property KernelBpm $kernel
 * @property string $HTTP_TYPE GET | POST | PUT | DELETE
 * @property string $url
 * @uses     ConstructorUrl trait
 * @package agoalofalife\bpm\Actions
 */
class Read implements Action, ActionGet
{
    use ConstructorUrl, QueryBuilder, AuthenticationHelper;

    protected $kernel;

    /**
     * Type HTTP_TYPE select
     * @var string
     */
    protected $HTTP_TYPE = 'GET';

    protected $url = '?';

    /**
     * @param KernelBpm $bpm
     * @return  void
     */
    public function injectionKernel(KernelBpm $bpm)
    {
        $this->kernel = $bpm;
    }

    /**
     * @return string url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return Handler
     */
    public function processData()
    {
        $this->getData();
        return $this->kernel->getHandler();
    }

    public function getData()
    {
        $this->query();
    }

    /**
     * Request the type of filter
     * Design   filterConstructor allows you to build logical expressions the conditions selecting the desired object .
     * Expressions filterConstructor can be used to reference the properties and literals ,
     * as well as strings, numbers and Boolean expressions (true, false).
     * Expressions $ filter supports arithmetic , logical operations , and operations groups ,
     * strings , date and time of the operation.
     * @documentation
     * @param $strRequest
     * @return $this
     */
    public function filterConstructor($strRequest)
    {
        $ParameterQuery =  '$filter=';
        $ParameterQuery.=  $strRequest;
        $this->concatenationUrlCurl($ParameterQuery);
        return $this;
    }

    /**
     * Service resources can be obtained in the form of sort .
     * asc  ascending
     * desc descending
     * @param string $whatSort
     * @param string $param  asc | desc
     * @return $this
     * @throws \Exception
     */
    public function orderBy($whatSort, $param = 'asc')
    {
        $ParameterQuery = '$orderby=';
        $ParameterQuery.=  ucfirst($whatSort);

        if ( empty($param) === false ) {
            if ($param != 'desc' && $param != 'asc') {
                throw new \Exception('no valid orderby parameters');
            }
            $ParameterQuery.=  " ".$param;
        }
         $this->concatenationUrlCurl($ParameterQuery);

        return $this;
    }

    /**
     * In bpm'online support the use of parameter $ the skip ,
     * which allows you to query the service resources ,
     * skipping the specified number of entries.
     * @param $skip
     * @return $this
     */
    public function skip($skip)
    {
        Assert::that($skip, 'You must specify a numeric parameter for the amount of the method')->integer();
        $ParameterQuery = '$skip='.$skip;
        $this->concatenationUrlCurl($ParameterQuery);

        return $this;
    }

    /**
     * Restrictions in the sample query
     * If you want the request to return more than 40 records at a time, it can be implemented using the parameter $ top
     * @param $amountMax
     * @return $this
     */
    public function amount($amountMax = null)
    {
        Assert::that($amountMax,'You must specify a numeric parameter for the amount of the method')->integer();
        $ParameterQuery = '$top='.$amountMax;
        $this->concatenationUrlCurl($ParameterQuery);
        return $this;
    }

    /**
     * The number of records
     * example SomeCollection/$count or SomeCollection/$count?$filter=...
     * @return $this
     */
    public function count()
    {
        $this->url = '/';
        $ParameterQuery = '$count';
        $this->concatenationUrlCurl($ParameterQuery);
        return $this;
    }

    /**
     * TODO Requires refactoring of this method
     * @return void
     */
    private function query()
    {
        $parameters   = str_replace(' ', '%20', $this->url);
        $url          = $this->kernel->getCollection() . $parameters;
        $urlHome      = config($this->kernel->getPrefixConfig() . '.UrlHome');
        $params = $this->debug()->headers()->getCookie()->httpErrorsFalse()->get();
        $response     =  $this->kernel->getCurl()->request($this->HTTP_TYPE, $urlHome . $url,
                         $params
        );
        $body         = $response->getBody();

        $this->kernel->getHandler()->parse($body->getContents());

        $this->checkResponseUnauthorized($response);
    }
}
