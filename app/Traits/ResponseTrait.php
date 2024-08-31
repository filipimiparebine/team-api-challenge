<?php

namespace App\Traits;

use Closure;
use Exception;
use App\Enums\Message;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait ResponseTrait
{
    public function success($data): JsonResponse
    {
        return response()->json($data, Response::HTTP_OK);
    }

    public function failed(string $errorMessage, int $status = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json(['message' => $errorMessage], $status);
    }

    public function sendResponse(Closure $closure)
    {
        try {
            $data = $closure();
            if ($data instanceof Message) {
                return $this->success(['message' => $data]);
            }
            return $this->success($data);
        }  catch (ModelNotFoundException $e) {
            return $this->failed($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return $this->failed($e->getMessage());
        }
    }
}
