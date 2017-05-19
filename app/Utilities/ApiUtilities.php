<?php

namespace App\Utilities;

use Auth;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

define('PAGE_SIZE', 10);
//page offset
define('PAGE_OFFSET', 0);

if (!defined('API_URL')) {
    define('API_URL', 'http://churchapp.dev.local');
}

class ApiUtilities
{

    // const API_URL = 'http://api-test-pricing.netcengroup.com';

    public static function MakeAPIURL($route)
    {
        return url(API_URL . $route);
    }

    public function fetchItem($route, $item_id, $formFunction)
    {

        $response = self::IssueGETRequest(self::MakeAPIURL("{$route}/{$item_id}"));

        if (!empty($response)) {
            $response = json_decode($response, true);
        } else {
            $response = "EMPTY!";
        }

        if (is_array($response)) {
            $formFunction($response);
        }

    }

    public function fetchItemWithRoute($route, $formFunction)
    {

        $response = self::IssueGETRequest(self::MakeAPIURL("{$route}"));

        if (!empty($response)) {
            $response = json_decode($response, true);
        } else {
            $response = "EMPTY!";
        }

        if (is_array($response)) {
            $formFunction($response);
        }

    }

    public static function fetchCollection($route, $dataFunction)
    {
        $iTotal = 0;
        $iFilteredTotal = 0;

        $data = array();

        $route .= (strpos($route, '?') == false) ? "?" : "&";
        $response = self::IssueGETRequest(self::MakeAPIURL("{$route}"));

        if (!empty($response)) {
            $response = json_decode($response, true);
            $data = isset($response['results']) ? $response['results'] : null;
            $iTotal = isset($response['iTotal']) ? $response['iTotal'] : 0;
            $iFilteredTotal = isset($response['iFilteredTotal']) ? $response['iFilteredTotal'] : 0;
        }

        if (is_array($data)) {
            $dataFunction($data);
        }
    }

    public function fetchItemList($route, $dataFunction)
    {

        $iTotal = 0;
        $iFilteredTotal = 0;

        $data = array();

        $route .= (strpos($route, '?') == false) ? "?" : "&";
        $response = self::IssueGETRequest(self::MakeAPIURL("{$route}"));

        if (!empty($response)) {
            $response = json_decode($response, true);
            $data = isset($response['results']) ? $response['results'] : null;
            $iTotal = isset($response['iTotal']) ? $response['iTotal'] : 0;
            $iFilteredTotal = isset($response['iFilteredTotal']) ? $response['iFilteredTotal'] : 0;
        }

        if (is_array($data)) {
            $dataFunction($data);
        }

    }

    public function fetchList($route, $dataFunction)
    {

        $iTotal = 0;
        $iFilteredTotal = 0;
        $results = array();

        if (Auth::check()) {

            $pagesize = PAGE_SIZE;
            $offset = PAGE_OFFSET;
            $search = "";
            $data = array();

            if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
                $pagesize = urlencode($_GET['iDisplayLength']);
                $offset = urlencode($_GET['iDisplayStart'] / (!empty($_GET['iDisplayLength']) ? $_GET['iDisplayLength'] : 0));
            }

            if (isset($_GET['sSearch']) && $_GET['sSearch'] != "") {
                $search = urlencode($_GET['sSearch']);
            }

            $route .= (strpos($route, '?') == false) ? "?" : "&";
            $response = self::IssueGETRequest(self::MakeAPIURL("{$route}q={$search}&page_size={$pagesize}&offset={$offset}"));

            if (!empty($response)) {
                $response = json_decode($response, true);
                $data = isset($response['results']) ? $response['results'] : null;
                $iTotal = isset($response['iTotal']) ? $response['iTotal'] : 0;
                $iFilteredTotal = isset($response['iFilteredTotal']) ? $response['iFilteredTotal'] : 0;
            }

            if (is_array($data)) {
                $dataFunction($data, $results);
            }
        }

        $output = array(
            "sEcho" => intval(isset($_GET['sEcho']) ? $_GET['sEcho'] : 0),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => $results,
        );

        echo json_encode($output);
    }

    public function createItem($route, $content, $formFunction)
    {

        $response = self::IssuePOSTRequest(self::MakeAPIURL($route), $content);

        if (!empty($response)) {
            $response = json_decode($response, true);
        } else {
            $response = "EMPTY!";
        }

        if (is_array($response)) {
            $formFunction($response);
        }

    }

    public function updateItem($route, $item_id, $content, $formFunction)
    {

        $response = self::IssuePUTRequest(self::MakeAPIURL("{$route}/{$item_id}"), $content);

        if (!empty($response)) {
            $response = json_decode($response, true);
        } else {
            $response = "EMPTY!";
        }

        if (is_array($response)) {
            $formFunction($response);
        }

    }

    private static function prepareAuthDetails($uriPath, $username, $password, $company_name)
    {
        $authHeaders = array();

        if (!empty($username) && !empty($password)) {

            $request = sprintf('Basic %s', base64_encode("{$username}:{$password}:{$company_name}"));

            $authHeaders['headers'] = array('Authorization' => $request);
        }

        return $authHeaders;
    }

    public static function IssueGETRequestDebug($url)
    {
        $client = new Client(self::prepareAuthDetails($url, 'Martin Wachira', 'admin', 'Netcen'));
        $response = $client->request('GET', $url);

        if ($response->getStatusCode() === 200) {
            return (string) $response->getBody();
        } else {
            $error = array(
                'error' => 'No Response',
            );
            return json_encode($error);
        }
    }

    public static function IssueGETRequest($url)
    {
//        $user = Auth::user();
        $username = 'username';
        $password = 'apiHash';
        $company_name = 'company_name';
        $client = new Client(self::prepareAuthDetails($url, $username, $password, $company_name));
        $response = $client->request('GET', $url);
        if ($response->getStatusCode() === 200) {
            return (string)$response->getBody();
        } else {
            $error = array(
                'error' => 'No Response',
            );
            return json_encode($error);
        }
//        comment for async method
//        $promise = $client->requestAsync('GET', $url);
//        $response = $promise->wait();
//        if ($response->getStatusCode() === 200) {
//            return (string)$response->getBody();
//        } else {
//            $error = array(
//                'error' => 'No Response',
//            );
//            return json_encode($error);
//        }
    }

    public static function IssuePOSTRequest($url, $content)
    {

        if (empty($content)) {
            $error = array(
                'error' => 'Invalid Request: No data specified.',
            );
            return json_encode($error);
        }

        $user = Auth::user();
        $username = $user->username;
        $password = session('apiHash');
        $company_name = $user->company_name;

        $client = new Client(['headers' => self::prepareAuthDetails($url, $username, $password, $company_name)]);

        try {

            $response = $client->request('POST', $url, [
                'body' => $content,
            ]);

            if ($response->getStatusCode() === 200) {
                return (string)$response->getBody();
            } else {

                $error = array(
                    'error' => 'Server Error: Unsuccessful request.',
                );
                return json_encode($error);
            }

        } catch (RequestException $e) {

            $error = array(
                'error' => 'Server Error: Unsuccessful request.',
            );
            return json_encode($error);

        }

    }

    public static function IssuePUTRequest($url, $content)
    {

        if (empty($content)) {
            $error = array(
                'error' => 'Invalid Request: No data specified.',
            );
            return json_encode($error);
        }

        $user = Auth::user();
        $username = $user->username;
        $password = session('apiHash');
        $company_name = $user->company_name;

        $client = new Client(['headers' => self::prepareAuthDetails($url, $username, $password, $company_name)]);

        try {

            $response = $client->request('PUT', $url, [
                'body' => $content,
            ]);

            if ($response->getStatusCode() === 200) {

                return (string)$response->getBody();

            } else {

                $error = array(
                    'error' => 'Server Error: Unsuccessful request.',
                );

                return json_encode($error);

            }

        } catch (RequestException $e) {

            $error = array(
                'error' => 'Server Error: RequestException.' . $e,
            );

            return $error;

        }

    }

    public static function IssuePUTDataRequest($url, $content)
    {

        if (empty($content)) {
            $error = array(
                'error' => 'Invalid Request: No data specified.',
            );
            return json_encode($error);
        }

        $user = Auth::user();
        $username = $user->username;
        $password = session('apiHash');
        $company_name = $user->company_name;

        $client = new Client(['headers' => self::prepareAuthDetails($url, $username, $password, $company_name)]);

        try {

            $response = $client->request('PUT', $url, [
                'body' => $content,
            ]);

            if ($response->getStatusCode() === 200) {

                return (string)$response->getBody();

            } else {

                $error = array(
                    'error' => 'Server Error: Unsuccessful request.',
                );

                return json_encode($error);

            }

        } catch (RequestException $e) {

            $error = array(
                'error' => 'Server Error: RequestException.' . $e,
            );

            return $error;

        }

    }

    public static function issueNoAuthPUTRequest($url, $content)
    {

        if (empty($content)) {
            $error = array(
                'error' => 'Invalid Request: No data specified.',
            );
            return json_encode($error);
        }

        $client = new Client();

        try {

            $response = $client->request('PUT', $url, [
                'body' => $content,
            ]);

            if ($response->getStatusCode() === 200) {

                return 200;

            } else {

                $error = array(
                    'error' => 'Server Error: Unsuccessful request.',
                );

                return $error;

            }

        } catch (RequestException $e) {

            $error = array(
                'error' => 'Server Error: RequestException.' . $e,
            );

            return $error;

        }

    }

    public static function IssueRedirectRequest($url)
    {
        $user = Auth::user();
        $username = $user->username;
        $password = session('apiHash');
        $company_name = $user->company_name;
        $client = new Client(self::prepareAuthDetails($url, $username, $password, $company_name));
        return redirect($url);
    }

    public function toJSON($result, $output = false, $removeNullField = false, $exceptField = null, $mustRemoveFieldList = null, $setJSONContentType = true, $encoding = 'utf-8')
    {
        $rs = preg_replace(array('/\,\"\_table\"\:\".*\"/U', '/\,\"\_primarykey\"\:\".*\"/U', '/\,\"\_fields\"\:\[\".*\"\]/U'), '', json_encode($result));

        // dd($rs);
        if ($removeNullField) {
            if ($exceptField === null) {
                $rs = preg_replace(array('/\,\"[^\"]+\"\:null/U', '/\{\"[^\"]+\"\:null\,/U'), array('', '{'), $rs);
            } else {
                $funca1 = create_function('$matches', 'if(in_array($matches[1], array(\'' . implode("','", $exceptField) . '\'))===false){
                    return "";
                }
                return $matches[0];');

                $funca2 = create_function('$matches', 'if(in_array($matches[1], array(\'' . implode("','", $exceptField) . '\'))===false){
                    return "{";
                }
                return $matches[0];');

                $rs = preg_replace_callback('/\,\"([^\"]+)\"\:null/U', $funca1, $rs);
                $rs = preg_replace_callback('/\{\"([^\"]+)\"\:null\,/U', $funca2, $rs);
            }
        }

        if ($mustRemoveFieldList !== null) {
            $funcb1 = create_function('$matches', 'if(in_array($matches[1], array(\'' . implode("','", $mustRemoveFieldList) . '\'))){
                return "";
            }
            return $matches[0];');

            $funcb2 = create_function('$matches', 'if(in_array($matches[1], array(\'' . implode("','", $mustRemoveFieldList) . '\'))){
                return "{";
            }
            return $matches[0];');

            $rs = preg_replace_callback(array('/\,\"([^\"]+)\"\:\".*\"/U', '/\,\"([^\"]+)\"\:\{.*\}/U', '/\,\"([^\"]+)\"\:\[.*\]/U', '/\,\"([^\"]+)\"\:([false|true|0-9|\.\-|null]+)/'), $funcb1, $rs);

            $rs = preg_replace_callback(array('/\{\"([^\"]+)\"\:\".*\"\,/U', '/\{\"([^\"]+)\"\:\{.*\}\,/U'), $funcb2, $rs);

            preg_match('/(.*)(\[\{.*)\"(' . implode('|', $mustRemoveFieldList) . ')\"\:\[(.*)/', $rs, $m);

            if ($m) {
                if ($pos = strpos($m[4], '"}],"')) {
                    if ($pos2 = strpos($m[4], '"}]},{')) {
                        $d = substr($m[4], $pos2 + 5);
                        if (substr($m[2], -1) == ',') {
                            $m[2] = substr_replace($m[2], '},', -1);
                        }
                    } else if (strpos($m[4], ']},{') !== false) {
                        $d = substr($m[4], strpos($m[4], ']},{') + 3);
                        if (substr($m[2], -1) == ',') {
                            $m[2] = substr_replace($m[2], '},', -1);
                        }
                    } else if (strpos($m[4], '],"') === 0) {
                        $d = substr($m[4], strpos($m[4], '],"') + 2);
                    } else if (strpos($m[4], '}],"') !== false) {
                        $d = substr($m[4], strpos($m[4], '],"') + 2);
                    } else {
                        $d = substr($m[4], $pos + 4);
                    }
                } else {
                    $rs = preg_replace('/(\[\{.*)\"(' . implode('|', $mustRemoveFieldList) . ')\"\:\[.*\]\}(\,)?/U', '$1}', $rs);
                    $rs = preg_replace('/(\".*\"\:\".*\")\,\}(\,)?/U', '$1}$2', $rs);
                }

                if (isset($d)) {
                    $rs = $m[1] . $m[2] . $d;
                }
            }
        }

        if ($output === true) {
            if ($setJSONContentType === true) {
                $this->setContentType('json', $encoding);
            }

            echo $rs;
        }
        return $rs;
    }

    /**
     * [GetClientIP description]
     */
    public static function GetClientIP()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } else if (getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } else if (getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } else if (getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }

    public function UploadFile($file_path, $file_name, $file_type, $upload_type, $item_id, $doc_name = null, $doc_desc = null, $misc = null)
    {
        //Prepare the file
        $file = $this->PrepareFile($file_path, $file_type, $file_name);

        //API uploads URL
        $url = "http://test-api.dct.netcengroup.com/index.php/uploads";
        //NOTE: The top level key in the array is important, as some apis will insist that it is 'file'.
        $data = array(
            'file' => $file,
            'upload_type' => $upload_type,
            'item_id' => $item_id,
            'document_name' => $doc_name,
            'document_desc' => $doc_desc,
            'miscellaneous' => $misc,
        );
        $headers = array(
            "Content-Type:multipart/form-data",
            "API-Key: SGVyZSBpcyB0aGUgQVBJIEtleSBmb3IgZXZlcnlvbmU=",
            "User-Name: Mike Njonge",
            "User-Password: admin",
            "User-Company: netcen"
        ); // cURL headers for file uploading

        $ch = curl_init();
        $options = array(CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLINFO_HEADER_OUT => true, //Request header
            CURLOPT_HEADER => true, //Return header
            CURLOPT_SSL_VERIFYPEER => false, //Don't veryify server certificate
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $headers
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        $header_info = curl_getinfo($ch, CURLINFO_HEADER_OUT);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($result, 0, $header_size);
        $body = substr($result, $header_size);
        curl_close($ch);
    }

}
