<?php
namespace App\Services\Contracts;

interface BaseServiceInterface
{
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete($id);
    public function restore($id);
    public function forcesDelete($id);
    public function find($id);
}
