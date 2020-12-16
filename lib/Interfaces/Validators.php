<?php


namespace Lib\Interfaces;


use Symfony\Component\HttpFoundation\Request;

interface Validators
{
    public function validate(Request $request);

    public function getErrors();
}