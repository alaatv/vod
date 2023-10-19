<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ForrestService;
use Illuminate\Http\Request;

class ForrestController extends Controller
{
    public function __construct(private ForrestService $forrestService)
    {
    }

    public function index(Request $request)
    {
        return $this->forrestService->getTree($request->only('type', 'multi-type', 'with-paginate'));
    }

    public function show($grid)
    {
        return $this->forrestService->getTreeByGrid($grid);
    }

    public function store(Request $request)
    {
        return $this->forrestService->storeTree($request->all());
    }

    public function update(Request $request, string $grid)
    {
        return $this->forrestService->updateTree($grid, $request->all());
    }

    public function tags(Request $request)
    {
        return $this->forrestService->getTags($request->get('title'));
    }

}
