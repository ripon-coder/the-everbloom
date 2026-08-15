<?php
namespace App\Repositories\Contracts;

interface SliderRepository
{
    public function all(array $filters = []);
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
