<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BaseController extends Controller
{
    protected function success(
        string $message = 'Success',
        mixed $data = null,
        int $status = 200,
        array $meta = []
    ) {
        return ApiResponse::success($message, $data, $status, $meta);
    }

    protected function error(
        string $message = 'Error',
        mixed $errors = null,
        int $status = 400
    ) {
        return ApiResponse::error($message, $errors, $status);
    }

    protected function unauthorized(string $message = 'Unauthorized')
    {
        return ApiResponse::unauthorized($message);
    }

    protected function notFound(string $message = 'Data not found')
    {
        return ApiResponse::notFound($message);
    }

    protected function validation(string $message, $errors)
    {
        return ApiResponse::validation($message, $errors);
    }

    protected function paginated(
        LengthAwarePaginator $collection,
        string $message = 'Success'
    ) {
        return ApiResponse::success($message,
            $collection->items(),
            200,
            [
                'current_page' => $collection->currentPage(),
                'last_page' => $collection->lastPage(),
                'per_page' => $collection->perPage(),
                'total' => $collection->total(),
            ]);
    }
}