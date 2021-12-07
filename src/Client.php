<?php

namespace TaiwanSms\Mitake;

use Carbon\Carbon;
use DomainException;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\MessageFactory;
use Psr\Http\Client\ClientExceptionInterface;

class Client
{
    /**
     * @var string
     */
    public $apiEndpoint = 'http://smexpress.mitake.com.tw:9600/';

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * __construct.
     *
     * @param string $username
     * @param string $password
     * @param HttpClient $httpClient
     * @param MessageFactory $messageFactory
     */
    public function __construct($username, $password, HttpClient $httpClient = null, MessageFactory $messageFactory = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->httpClient = $httpClient ?: HttpClientDiscovery::find();
        $this->messageFactory = $messageFactory ?: MessageFactoryDiscovery::find();
    }

    /**
     * query.
     *
     * @param array $params
     * @param string $type
     * @return array
     * @throws ClientExceptionInterface
     */
    public function query($params, $type = 'query')
    {
        $response = $this->doRequest('SmQueryGet.asp', array_filter(array_merge([
            'username' => $this->username,
            'password' => $this->password,
            'msgid' => null,
        ], $this->remapParams($params))));

        return $this->parseResponse($response, $type);
    }

    /**
     * credit.
     *
     * @return array
     */
    public function credit()
    {
        return $this->query([], 'credit');
    }

    /**
     * send.
     *
     * @param array $params
     * @return array
     * @throws ClientExceptionInterface
     */
    public function send($params)
    {
        $response = $this->doRequest('SmSendGet.asp', array_filter(array_merge([
            'username' => $this->username,
            'password' => $this->password,
            'dstaddr ' => null,
            'encoding' => null,
            'DestName' => null,
            'divtime' => null,
            'vidtime' => null,
            'vldtime' => null,
            'smbody' => null,
            'response' => null,
            'ClientID' => null,
        ], $this->remapParams($params))));

        $response = $this->parseResponse($response, 'send');

        if ($this->isValidResponse($response) === false) {
            throw new DomainException($this->getErrorMessage($response), 500);
        }

        return $response;
    }

    /**
     * isValidResponse.
     *
     * @param array $response
     *
     * @return bool
     */
    private function isValidResponse($response)
    {
        if ((isset($response['statuscode']) === true && $response['statuscode'] === 'p') || isset($response['Error'])) {
            return false;
        }

        return true;
    }

    /**
     * doRequest.
     *
     * @param string $uri
     * @param array $params
     *
     * @return string
     * @throws ClientExceptionInterface
     */
    private function doRequest($uri, $params)
    {
        $request = $this->messageFactory->createRequest(
            'POST',
            rtrim($this->apiEndpoint, '/').'/'.$uri,
            ['Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8'],
            http_build_query($params)
        );
        $response = $this->httpClient->sendRequest($request);

        return $response->getBody()->getContents();
    }

    /**
     * remapParams.
     *
     * @param array $params
     * @return array
     */
    private function remapParams($params)
    {
        if (empty($params['subject']) === false) {
            $params['DestName'] = $params['subject'];
            unset($params['subject']);
        }

        if (empty($params['to']) === false) {
            $params['dstaddr'] = $params['to'];
            unset($params['to']);
        }

        if (empty($params['text']) === false) {
            $params['smbody'] = $params['text'];
            unset($params['text']);
        }

        if (empty($params['sendTime']) === false) {
            $params['divtime'] = Carbon::parse($params['sendTime'])->format('YmdHis');
            unset($params['sendTime']);
        }

        $converEncodingKeys = ['DestName', 'smbody'];

        foreach ($converEncodingKeys as $key) {
            if (empty($params[$key]) === false) {
                $params[$key] = mb_convert_encoding($params[$key], 'big5', 'utf8');
            }
        }

        return $params;
    }

    /**
     * parseResponse.
     *
     * @param string $response
     * @return array
     */
    private function parseResponse($response, $type = 'send')
    {
        $response = mb_convert_encoding($response, 'utf8', 'big5');

        switch ($type) {
            case 'credit':
                list($accountPoint) = sscanf($response, 'AccountPoint=%d');

                return $accountPoint;
            case 'query':
                return array_map(function ($line) {
                    list($to, $credit, $time) = preg_split('/\s+/', $line);

                    return compact('to', 'credit', 'time');
                }, explode("\n", $response));
            default:
                $result = parse_ini_string($response, true);

                return count($result) > 1 ? $result : reset($result);
        }
    }

    /**
     * getErrorMessage.
     *
     * @param array $response
     */
    private function getErrorMessage($response)
    {
        return $response['Error'];
    }
}
