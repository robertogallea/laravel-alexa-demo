<?php

namespace App\Http\Controllers;


use Alexa;
use Develpr\AlexaApp\Request\AlexaRequest;
use Develpr\AlexaApp\Response\AlexaResponse;
use Develpr\AlexaApp\Response\Speech;

class AlexaDemoController extends AlexaController
{
    public function launch(AlexaRequest $request)
    {
        return (new AlexaResponse())
            ->withSpeech(new Speech("Benvenuto a Indovina il numero, per iniziare dì: \"nuova partita\""))
            ->endSession(false);
    }

    public function newGame(AlexaRequest $request)
    {
        $maxNumber = Alexa::slot('max_number');
        $magicNumber = rand(1, $maxNumber);

        Alexa::session('max_number', $maxNumber);
        Alexa::session('magic_number', $magicNumber);
        Alexa::session('guesses_count', 0);

        return (new AlexaResponse())
            ->withSpeech(new Speech("Inizio una nuova partita, ho estratto un numero a caso, fra 1 e " . $maxNumber . ", prova a indovinarlo!"))
            ->endSession(false);
    }

    public function guessNumber(AlexaRequest $request)
    {
        $guess = Alexa::slot('number');
        $maxNumber = Alexa::session('max_number');

        if (in_array($guess, ['?', '', ' '])) {
            return Alexa::say("Non ho capito, per favore riprova.")
                ->endSession(false);
        }

        if ($guess < 0 || $guess > $maxNumber) {
            return Alexa::say("Hai scelto il numero " . $guess . " ma è fuori dall'intervallo possibile, scegli un altro numero")
                ->endSession(false);
        }

        $magicNumber = Alexa::session('magic_number');
        $guessesCount = Alexa::session('guesses_count') + 1;
        Alexa::session('guesses_count', $guessesCount);

        if ($guess == $magicNumber) {
            Alexa::unsetSession('magic_number');
            Alexa::unsetSession('max_number');
            Alexa::unsetSession('guesses_count');

            return Alexa::say("Hai scelto il numero " . $guess . ". Hai indovinato con " . $guessesCount . " tentativi! " .
                "Per iniziare una nuova partita di: ricomincia.")
                ->endSession(false);
        }

        if ($guess > $magicNumber) {
            return Alexa::say("Hai scelto il numero " . $guess . " il numero da indovinare è più piccolo, riprova!")
                ->endSession(false);
        }

        if ($guess < $magicNumber) {
            return Alexa::say("Hai scelto il numero " . $guess . " il numero da indovinare è più grande, riprova!")
                ->endSession(false);
        }
    }

    public function cancel(AlexaRequest $request)
    {
        return Alexa::say('CANCELLA')->endSession(true);
    }

    public function help(AlexaRequest $request)
    {
        return Alexa::say('AIUTO')->endSession(true);
    }

    public function stop(AlexaRequest $request)
    {
        return Alexa::say('STOP')->endSession(true);
    }

    public function navigateHome(AlexaRequest $request)
    {
        return Alexa::say('HOME')->endSession(true);
    }

    public function endSession(AlexaRequest $request)
    {
        return self::DEFAULT_RESPONSE;
    }
}
