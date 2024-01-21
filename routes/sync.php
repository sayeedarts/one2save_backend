<?php 
/**
 * Public grouping Routes
 * 
 * @author tanmayap
 * @date 19 nov 2020
 */

 /**
 * Sync Data between HMH and Cloud Server
 */
Route::get('/sync/master/speciality', 'DoctorSync@syncDepartments')->name('sync.speciality');
Route::get('/sync/master/doctors', 'DoctorSync@syncDoctors')->name('sync.doctors');
Route::get('/sync/master/shift/all', 'DoctorSync@shiftList')->name('sync.shift.all');
Route::get('/sync/master/doctors-shift', 'DoctorSync@doctorShift')->name('sync.doctors.shift');
Route::get('/sync/patients/booking-time', 'PatientSync@patientsBookingTime')->name('sync.patient.booking');
Route::get('/sync/patients/report/radiology', 'PatientSync@radiologyDetails')->name('sync.patient.report.radiology');
Route::get('/sync/patients/report/lab', 'PatientSync@labDetails')->name('sync.patient.report.lab');
Route::get('/sync/patients/booking-to-int-server', 'PatientSync@pushBookingOutside')->name('sync.patient.booking.push.intermidiate');
Route::get('/sync/patients/visit/list', 'PatientSync@getPatientVisitDetails')->name('sync.doctors.shift');
Route::get('/sync/patients/sick-leave', 'PatientSync@getPatientSickLeaveReport')->name('sync.patient.sick-leave');
Route::get('/clear-trash', 'MasterDataSync@clearTrash')->name('clear-trash');