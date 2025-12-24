<?php

namespace App\Services;

use App\Repositories\StatusRepository;

class StatusService extends BaseService
{
    protected StatusRepository $statusRepository;

    public function __construct(StatusRepository $statusRepository)
    {
        parent::__construct($statusRepository);
        $this->statusRepository = $statusRepository;
    }

    public function getBaseUrl()
    {
        return 'admin.status';
    }

}
