<?php

namespace App\Http\Controllers;

use Develpr\AlexaApp\Request\AlexaRequest;
use Illuminate\Support\Str;

abstract class AlexaController extends Controller
{
    protected const DEFAULT_RESPONSE = '{"version":"1.0","response":{"shouldEndSession":true}}';

    public function __invoke(AlexaRequest $request)
    {
        if ($request->getRequestType() === 'LaunchRequest') {
            return $this->launch($request);
        }

        if (Str::of($request->getRequestType())->is('SessionEndedRequest')) {
            return $this->endSession($request);
        }

        if ($request->getRequestType() === 'IntentRequest') {
            $intent = $this->parseIntent($request);
            return $this->callIntent($intent, $request);
        }

        return $this->fallback($request);
    }

    public function fallback(AlexaRequest $request)
    {
        return self::DEFAULT_RESPONSE;
    }

    /**
     * @param AlexaRequest $request
     * @return string|null
     */
    protected function parseIntent(AlexaRequest $request)
    {
        $intent = Str::of($request->getIntent());

        if ($intent->startsWith('AMAZON.')) {
            $intent = $intent->after('AMAZON.')->before('Intent');
        }

        return (string)$intent->camel();
    }

    /**
     * @param string|null $intent
     * @param AlexaRequest $request
     * @return mixed
     */
    protected function callIntent(?string $intent, AlexaRequest $request)
    {
        if (is_callable(array($this, $intent))) {
            return $this->$intent($request);
        } else {
            throw new \InvalidArgumentException(sprintf("%s is not callable", $intent));
        }
    }

    /**
     * @param AlexaRequest $request
     * @return mixed
     */
    public abstract function launch(AlexaRequest $request);

    /**
     * @param AlexaRequest $request
     * @return mixed
     */
    public abstract function endSession(AlexaRequest $request);
}
