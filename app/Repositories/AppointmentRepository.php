<?php

namespace App\Repositories;
use App\Models\Appointment;

class AppointmentRepository implements RepositoryInterface
{

    public function getAll() {
        return Appointment::get();
    }

    // public function create(array $data);

    // public function update(array $data, $id);

    // public function delete($id);

    // public function show($id);
}
