<?php 
namespace App\Repositories\Contracts;
interface ProductRepository{

    public function index();
    public function create();
    public function store();
}