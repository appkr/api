<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Http status code
    |--------------------------------------------------------------------------
    |
    | While leveraging Appkr\Fractal\Http\Response at YourController,
    | you can use below http status code like
    | return $this->respond()->internalError(trans('http.500'));
    |
    */
    100 => 'Continue',
    101 => 'Switching Protocols',
    102 => 'Processing', // RFC2518
    200 => 'Ok',
    201 => 'Created',
    202 => 'Accepted',
    203 => 'Non Authoritative Information',
    204 => 'No Content',
    205 => 'Reset Content',
    206 => 'Partial Content',
    207 => 'Multi Status', // RFC4918
    208 => 'Already Reported', // RFC5842
    226 => 'Im Used', // RFC3229
    300 => 'Multiple Choices',
    301 => 'Moved Permanently',
    302 => 'Found',
    303 => 'See Other',
    304 => 'Not Modified',
    305 => 'Use Proxy',
    306 => 'Reserved',
    307 => 'Temporary Redirect`',
    308 => 'Permanently Redirect', // RFC7238
    400 => 'Bad Request',
    401 => 'Unthorized',
    402 => 'Payment Required',
    403 => 'Forbidden',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
    406 => 'Not Acceptable',
    407 => 'Proxy Authentication Required',
    408 => 'Request Timeout',
    409 => 'Conflict',
    410 => 'Gone',
    411 => 'Length Required',
    412 => 'Preconditional Failed',
    413 => 'Request Entity Too Large',
    414 => 'Request Uri Too Long',
    415 => 'Unsupported Media Type',
    416 => 'Requested Range Not Satisfiable',
    417 => 'Expectation Failed',
    418 => 'I Am A Teapot', // RFC2324
    422 => 'Unprocessable Entity', // RFC4918
    423 => 'Locked', // RFC4918
    424 => 'Failed Dependency', // RFC4918
    425 => 'Reserved For Webdav Advanced Collections Expired Proposal', // RFC2817
    426 => 'Upgrade Required', // RFC2817
    428 => 'Preconditional Required', // RFC6585
    429 => 'Too Many Requests', // RFC6585
    431 => 'Request Header Fields Too Large', // RFC6585
    500 => 'Internal Server Error',
    501 => 'Not Implemented',
    502 => 'Bad Gateway',
    503 => 'Service Unavailable',
    504 => 'Gateway Timeout',
    505 => 'Version Not Supported',
    506 => 'Variant Also Negotiates Experimental', // RFC2295
    507 => 'Insufficient Storage', // RFC4918
    508 => 'Loop Detected', // RFC5842
    510 => 'Not Extended', // RFC2774
    511 => 'Newtork Authentication Required', // RFC6585

];