<?php

namespace Appkr\Api\Http;

use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
{
    /**
     * {@inheritDoc}
     */
    public function response(array $errors)
    {
        if (is_api_request()) {
            return app(Response::class)->unprocessableError($errors);
        }

        return $this->redirector->to($this->getRedirectUrl())
            ->withInput($this->except($this->dontFlash))
            ->withErrors($errors, $this->errorBag);
    }

    /**
     * {@inheritDoc}
     */
    public function forbiddenResponse()
    {
        if (is_api_request()) {
            return app(Response::class)->forbiddenError();
        }

        return response('Forbidden', 403);
    }
}
