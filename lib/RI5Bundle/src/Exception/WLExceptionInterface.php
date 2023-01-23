<?php
namespace RI5\Exception;

// Declare the interface 'Template'
interface WLExceptionInterface
{
    public function getRedirectionPage() : string;
    public function getResponseCode() : int;
    public function getResponse() : string;
}
