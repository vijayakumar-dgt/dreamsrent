<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface NewsLetterRepositoryInterface
{
    public function save(Request $request);
    public function list(Request $request);
    public function delete(Request $request);
    public function sendNewsletter(Request $request): array;
}
