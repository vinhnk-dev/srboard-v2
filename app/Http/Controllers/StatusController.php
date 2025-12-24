<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use App\Http\Controllers\Controller;
use App\Repositories\StatusRepository;
use Illuminate\Http\Request;
use App\Http\Requests\StatusRequest;
use App\Services\StatusService;

class StatusController extends Controller
{
    protected $statusService;
    public function __construct(StatusRepository $statusRepo, UserRepository $userRepository, StatusService $statusService)
    {
        $this->service = $statusService;
        parent::__construct($statusRepo, $userRepository);
    }

    public function store(StatusRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['is_check_due'] = $request->input("is_check_due") ?? 0;

        $new_status = $this->service->create($validatedData);

        $baseUrl = $this->service->getBaseUrl();        
        return redirect()->route($baseUrl . ".index");
    }

    public function update($id ,StatusRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['is_check_due'] = $request->input("is_check_due") ?? 0;

        $new_status = $this->service->update($id, $validatedData);

        $baseUrl = $this->service->getBaseUrl();        
        return redirect()->route($baseUrl . ".index");
    }

}
