<?php
namespace Src\Controller;
abstract class MainController
{
    protected function unprocessableResponse($msg)
    {
        $response['status_code'] = 'HTTP/1.1 422 Invalid Entity';
        $response['body'] = json_encode([
            'error' => $msg,
        ]);
        header($response['status_code']);
        echo $response['body'];
        exit();
    }

    protected function noDataFound()
    {
        $response['status_code'] = 'HTTP/1.1 200';
        $response['body'] = json_encode([
            'error' => 'No Data found',
        ]);
        return $response;
    }

    abstract protected function validateInput(array $requestData);
    abstract public function processRequests();
}
?>
