<?php

namespace App\Http\Controllers\API;

use App\Enums\ProfilStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfilRequest;
use App\Models\Profil;
use Exception;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $profils = Profil::query()
            ->where(['status' => ProfilStatusEnum::ACTIF->value])
            ->get();

        return response()->json(['profils' => $profils], Response::HTTP_OK, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProfilRequest $request)
    {
        $data = $this->getDatas($request);
        $profil = Profil::query()->create($data);
        return response()->json(['profil' => $profil], Response::HTTP_CREATED, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Display the specified resource.
     */
    public function show(Profil $user)
    {
        return response()->json(['profil' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProfilRequest $request, Profil $user)
    {
        try {
            $data = $this->getDatas($request);
            $user->update($data);
            $user = $user->fresh();

            return response()->json(['profil' => $user], Response::HTTP_OK, [], JSON_UNESCAPED_SLASHES);
        } catch (Exception $exc) {
            Log::error(self::class, ['message' => $exc->getMessage(), 'exc' => $exc]);
            return response()->json(['error' => 'Failed to update Profil.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Profil $user)
    {
        try {
            $user->delete();
            return response()->json(['message' => 'Profil successfully deleted !']);
        } catch (Exception $exc) {
            Log::error(self::class, ['message' => $exc->getMessage(), 'exc' => $exc]);
            return response()->json(['error' => 'Failed to delete Profil.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function getDatas(ProfilRequest $request)
    {
        $data = $request->only([
            'firstname',
            'lastname',
            'status',
        ]);

        if ($request->method() === 'POST') {
            $data['user_id'] = $request->input('user_id');
        }

        if ($request->hasFile('picture')) {
            $directory = storage_path('app/public/users');
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $picturePath = $request->file('picture')->store('users', 'public');
            $data['picture'] = $picturePath;
        }

        return $data;
    }
}
