<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfilRequest;
use App\Models\Profil;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $profils = Profil::all();
        return response()->json(['profils' => $profils], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProfilRequest $request)
    {
        $profil = Profil::query()
            ->create($request->only([
                'name'
            ]));

        return response()->json(['profil' => $profil], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Profil $profil)
    {
        return response()->json(['profil' => $profil], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProfilRequest $request, Profil $profil)
    {
        try {
            $profil->update($request->only([
                'name'
            ]));
            $profil = $profil->fresh();

            return response()->json(['profil' => $profil], Response::HTTP_OK);
        } catch (Exception $exc) {
            Log::error(self::class, ['message' => $exc->getMessage(), 'exc' => $exc]);
            return response()->json(['error' => 'Failed to update Profil.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Profil $profil)
    {
        try {
            $profil->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (Exception $exc) {
            Log::error(self::class, ['message' => $exc->getMessage(), 'exc' => $exc]);
            return response()->json(['error' => 'Failed to delete Profil.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
