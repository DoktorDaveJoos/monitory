<?php

namespace App\Enums;

enum HttpStatusCode: int
{
    case OK = 200;
    case CREATED = 201;
    case ACCEPTED = 202;
    case NO_CONTENT = 204;
    case MOVED_PERMANENTLY = 301;
    case FOUND = 302;
    case SEE_OTHER = 303;
    case NOT_MODIFIED = 304;
    case TEMPORARY_REDIRECT = 307;
    case PERMANENT_REDIRECT = 308;
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case METHOD_NOT_ALLOWED = 405;
    case NOT_ACCEPTABLE = 406;
    case REQUEST_TIMEOUT = 408;
    case CONFLICT = 409;
    case GONE = 410;
    case LENGTH_REQUIRED = 411;
    case PRECONDITION_FAILED = 412;
    case PAYLOAD_TOO_LARGE = 413;
    case UNSUPPORTED_MEDIA_TYPE = 415;
    case TOO_MANY_REQUESTS = 429;
    case INTERNAL_SERVER_ERROR = 500;
    case NOT_IMPLEMENTED = 501;
    case BAD_GATEWAY = 502;
    case SERVICE_UNAVAILABLE = 503;
    case GATEWAY_TIMEOUT = 504;
    case HTTP_VERSION_NOT_SUPPORTED = 505;
    case NETWORK_AUTHENTICATION_REQUIRED = 511;

    public function getLabel(): string
    {
        return match ($this) {
            self::OK => '200 OK',
            self::CREATED => '201 Created',
            self::ACCEPTED => '202 Accepted',
            self::NO_CONTENT => '204 No Content',
            self::MOVED_PERMANENTLY => '301 Moved Permanently',
            self::FOUND => '302 Found',
            self::SEE_OTHER => '303 See Other',
            self::NOT_MODIFIED => '304 Not Modified',
            self::TEMPORARY_REDIRECT => '307 Temporary Redirect',
            self::PERMANENT_REDIRECT => '308 Permanent Redirect',
            self::BAD_REQUEST => '400 Bad Request',
            self::UNAUTHORIZED => '401 Unauthorized',
            self::FORBIDDEN => '403 Forbidden',
            self::NOT_FOUND => '404 Not Found',
            self::METHOD_NOT_ALLOWED => '405 Method Not Allowed',
            self::NOT_ACCEPTABLE => '406 Not Acceptable',
            self::REQUEST_TIMEOUT => '408 Request Timeout',
            self::CONFLICT => '409 Conflict',
            self::GONE => '410 Gone',
            self::LENGTH_REQUIRED => '411 Length Required',
            self::PRECONDITION_FAILED => '412 Precondition Failed',
            self::PAYLOAD_TOO_LARGE => '413 Payload Too Large',
            self::UNSUPPORTED_MEDIA_TYPE => '415 Unsupported Media Type',
            self::TOO_MANY_REQUESTS => '429 Too Many Requests',
            self::INTERNAL_SERVER_ERROR => '500 Internal Server Error',
            self::NOT_IMPLEMENTED => '501 Not Implemented',
            self::BAD_GATEWAY => '502 Bad Gateway',
            self::SERVICE_UNAVAILABLE => '503 Service Unavailable',
            self::GATEWAY_TIMEOUT => '504 Gateway Timeout',
            self::HTTP_VERSION_NOT_SUPPORTED => '505 HTTP Version Not Supported',
            self::NETWORK_AUTHENTICATION_REQUIRED => '511 Network Authentication Required',
        };
    }
}
