<?php

namespace App\Contracts;

use App\Models\ActivityLog;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use JsonSerializable;

abstract class Response
{
    /**
     * Possible response message
     */
    public const MESSAGE_OK = 'OK_SUCCESS';

    public const MESSAGE_CREATED = 'CREATED';

    public const MESSAGE_EXIST = 'EXIST';

    public const MESSAGE_UPDATED = 'UPDATED';

    public const MESSAGE_FOUND = 'FOUND!';

    public const MESSAGE_NO_CONTENT = 'NO_CONTENT';

    public const MESSAGE_NOT_FOUND = 'NOT_FOUND';

    public const MESSAGE_NOT_ACCEPTABLE = 'NOT_ACCEPTABLE';

    public const MESSAGE_UNPROCESSABLE_ENTITY = 'UNPROCESSABLE_ENTITY';

    public const MESSAGE_SERVER_ERROR = 'SERVER_ERROR';

    public const MESSAGE_UNAUTHORIZED = 'UNAUTHORIZED';

    public const MESSAGE_FORBIDDEN = 'FORBIDDEN';

    /**
     * Possible response status codes
     */
    public const STATUS_OK = 200;

    public const STATUS_CREATED = 201;

    public const STATUS_NO_CONTENT = 204;

    public const STATUS_NOT_FOUND = 404;

    public const STATUS_NOT_ACCEPTABLE = 406;

    public const STATUS_UNPROCESSABLE_ENTITY = 422;

    public const STATUS_SERVER_ERROR = 500;

    public const STATUS_UNAUTHORIZED = 401;

    public const STATUS_FORBIDDEN = 403;

    /**
     * @param  \Illuminate\Contracts\Support\Arrayable|\JsonSerializable|array|null  $data
     * @param  string  $message
     * @param  int  $status
     * @return \Illuminate\Http\JsonResponse
     */
    public static function json(
        $data = null,
        string $message = self::MESSAGE_OK,
        int $status = self::STATUS_OK
    ): JsonResponse {
        $content = [
            'message' => $message,
        ];
        if (
            $data instanceof ResourceCollection &&
            $data->resource instanceof LengthAwarePaginator
        ) {
            if ($data->resource->isEmpty())
                return self::noContent();

            $content['data'] = $data->resource->items();
            $content['meta'] = [
                'total'         => $data->resource->total(),
                'perPage'       => $data->resource->perPage(),
                'currentPage'   => $data->resource->currentPage(),
                'lastPage'      => $data->resource->lastPage(),
            ];
        } elseif ($data instanceof JsonSerializable) {
            $content['data'] = $data->jsonSerialize();
        } elseif ($data instanceof Arrayable) {
            $content['data'] = $data->toArray();
        } else {
            $content['data'] = $data;
        }

        return response()->json($content, $status);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public static function okSuccess($data = null): JsonResponse
    {
        return self::json($data, self::MESSAGE_CREATED, self::STATUS_CREATED);
    }



    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public static function okCreated($data = null): JsonResponse
    {
        return self::json($data, self::MESSAGE_CREATED, self::STATUS_CREATED);
    }
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public static function okFound($data = null): JsonResponse
    {
        return self::json(
            $data,
            self::MESSAGE_FOUND,
            self::STATUS_OK
        );
    }
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    // public static function okFoundAndCount($data = null, $count = null): JsonResponse
    // {
    //     return self::json(
    //         $data,
    //         $count,
    //         // self::MESSAGE_FOUND,
    //         self::STATUS_OK
    //     );
    // }


    public static function okFoundAndCount(
        $data = null,
        $count = null,
        string $message = self::MESSAGE_FOUND,
        int $status = self::STATUS_OK
    ): JsonResponse {
        $content = [
            'message' => $message,
            'count' => $count,
        ];
        if (
            $data instanceof ResourceCollection &&
            $data->resource instanceof LengthAwarePaginator
        ) {
            if ($data->resource->isEmpty())
                return self::noContent();

            $content['data'] = $data->resource->items();
            $content['meta'] = [
                'total'         => $data->resource->total(),
                'perPage'       => $data->resource->perPage(),
                'currentPage'   => $data->resource->currentPage(),
                'lastPage'      => $data->resource->lastPage(),
            ];
        } elseif ($data instanceof JsonSerializable) {
            $content['data'] = $data->jsonSerialize();
        } elseif ($data instanceof Arrayable) {
            $content['data'] = $data->toArray();
        } else {
            $content['data'] = $data;
        }

        return response()->json($content, $status);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public static function okUpdated($data = null): JsonResponse
    {
        return self::json($data, self::MESSAGE_UPDATED, self::STATUS_OK);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public static function noContent(): JsonResponse
    {
        $content = ['message' => self::MESSAGE_NO_CONTENT];

        return response()->json($content, self::STATUS_NO_CONTENT);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public static function abortNotFound($message = null): JsonResponse
    {
        return self::json(
            $message,
            self::MESSAGE_NOT_FOUND,
            self::STATUS_NOT_FOUND
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public static function abortFailed($message): JsonResponse
    {
        return self::json(
            $message,
            self::MESSAGE_NOT_ACCEPTABLE,
            self::STATUS_NOT_ACCEPTABLE
        );
    }

    public static function abortExist($message): JsonResponse
    {
        return self::json(
            $message,
            self::MESSAGE_UNPROCESSABLE_ENTITY,
            self::STATUS_UNPROCESSABLE_ENTITY

        );
    }
    public static function abortUnprocess($message): JsonResponse
    {
        return self::json(
            $message,
            self::MESSAGE_UNPROCESSABLE_ENTITY,
            self::STATUS_UNPROCESSABLE_ENTITY
        );
    }
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public static function abortNotAccepted(): JsonResponse
    {
        return self::json(
            null,
            self::MESSAGE_NOT_ACCEPTABLE,
            self::STATUS_NOT_ACCEPTABLE
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public static function abortUnauthorized(): JsonResponse
    {
        return self::json(
            null,
            self::MESSAGE_UNAUTHORIZED,
            self::STATUS_UNAUTHORIZED
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public static function abortForbidden(): JsonResponse
    {
        return self::json(
            null,
            self::MESSAGE_FORBIDDEN,
            self::STATUS_FORBIDDEN
        );
    }

    /**
     * @param  \Illuminate\Validation\ValidationException  $e
     * @return \Illuminate\Http\JsonResponse
     */
    public static function abortFormInvalid(ValidationException $e = null): JsonResponse
    {
        $message = null;
        foreach ($e->errors() as $error) {
            $message = $error[0];
            break;
        }

        return self::json(
            ['message' => $message],
            self::MESSAGE_UNPROCESSABLE_ENTITY,
            self::STATUS_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * @param  \Exception  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public static function abortInternalError(Exception $exception): JsonResponse
    {
        return self::json(
            [
                'exception' => [
                    'class'     => get_class($exception),
                    'message'   => $exception->getMessage(),
                ],
            ],
            self::MESSAGE_SERVER_ERROR,
            self::STATUS_SERVER_ERROR
        );
    }
}
