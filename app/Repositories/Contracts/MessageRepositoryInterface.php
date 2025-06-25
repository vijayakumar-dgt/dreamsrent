<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface MessageRepositoryInterface
{
    public function getUserData();

    public function sendMessage(Request $request);

    public function fetchMessages(Request $request);
}
