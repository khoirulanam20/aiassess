<?php

namespace App\Http\Controllers\Web;

use App\Services\ShareService;

class SharedResultController extends BaseController
{
    public function __construct(
        private readonly ShareService $shareService
    ) {}

    public function show(string $token)
    {
        $result = $this->shareService->getSharedResult($token);

        if (! $result) {
            abort(404, __('Shared result not found.'));
        }

        return view('shared.result', compact('result'));
    }
}
