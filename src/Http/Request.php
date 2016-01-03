<?php

namespace Appkr\Fractal\Http;

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
    protected function failedAuthorization()
    {
        if (is_api_request()) {
            return app(Response::class)->unauthorizedError();
        }

        return parent::failedAuthorization();
    }
}
