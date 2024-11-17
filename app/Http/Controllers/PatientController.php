<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientCreateRequest;
use App\Http\Requests\PatientUpdateRequest;
use App\Http\Resources\PatientInfoResource;
use App\Http\Resources\PatientListItem;
use App\Models\Patient;
/**
 * @OA\Info(
 *    title="Patient API",
 *    description="API for Patients",
 *    version="1.0.0",
 * )
 */
class PatientController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/patient/{id}",
     *     summary="Get all patient data",
     *     @OA\Parameter(
     *     name="id",
     *      description="Patient id in system",
     *     in="path",
     *     required=true,
     *          @OA\Schema(type="string"),
     * ),
     *     @OA\Response(
     *          response=200,
     *          description="OK"
     *      )
     * )
     */
    public function getById($patientId)
    {
        return PatientInfoResource::make(Patient::findOrFail($patientId));
    }

    /**
     * @OA\Get(path="/api/patient/",
     *     summary="Get patient list in system",
     *     @OA\Response(
     *          response=200,
     *          description="OK"
     *      )
     * )
     */
    public function getList()
    {
         return PatientListItem::collection(Patient::all());
    }

    /**
     * @OA\Post(
     *     path="/api/patient/{id}",
     *     summary="Update patient in system",
     *     @OA\Parameter(
     *     name="id",
     *       description="Patient id in system",
     *      in="path",
     *      required=true,
     *           @OA\Schema(type="string"),
     *  ),
     *     @OA\Response(
     *          response=200,
     *          description="OK"
     *      )
     * )
     */
    public function update($patientId, PatientUpdateRequest $request)
    {
        Patient::findOrFail($patientId)->update($request->toArray());
    }

    /**
     * @OA\Put(
     *     path="/api/patient/",
     *     summary="Add patient to system",
     *     @OA\Response(
     *          response=200,
     *          description="OK"
     *      )
     * )
     */
    public function add(PatientCreateRequest $request)
    {
        return Patient::create($request->toArray());
    }
}
