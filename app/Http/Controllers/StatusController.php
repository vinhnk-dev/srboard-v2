<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use App\Http\Controllers\Controller;
use App\Repositories\StatusRepository;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function __construct(StatusRepository $statusRepo, UserRepository $userRepository)
    {
        parent::__construct($statusRepo, $userRepository);
    }

    public function store(Request $request)
    {
        $validated_data = $request->validate($this->repo->rules());
        $validated_data['is_check_due'] = $request->input("is_check_due") ?? 0;
        $modal = $this->repo->find($request->input('id'));

        if ($modal) {
            $this->repo->update($request->input('id'), $validated_data);
        } else {
            $this->repo->create($validated_data);
        }

        return redirect()->route($this->repo->getBaseUrl() . ".index");
    }
}
