<?php
namespace RI5\Services;

interface IMessengerService
{
    public function sendMessage(string $phoneNumber,string $message = null, string $type="sms");
    public function printName() : ?string;
}