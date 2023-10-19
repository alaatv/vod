<?php


namespace App\Classes\Lottery;

interface LotteryHandlerInterface
{

    public function scoring();

    public function waitForScoring();

    public function reportScoringError();

    public function holdLottery();

    public function waitForHoldingLottery();

    public function reportHoldingError();

}
