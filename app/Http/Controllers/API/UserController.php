<?php

namespace App\Http\Controllers\API;

use App\Enums\ProfilStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfilRequest;
use App\Models\Profil;
use Exception;
use Illuminate\Support\Facades\Log;
use Storage;
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

        return response()->json(['profils' => $profils]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProfilRequest $request)
    {
        $data = $this->getDatas($request);
        $profil = Profil::query()->create($data);
        return response()->json(['profil' => $profil], Response::HTTP_CREATED);
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

            return response()->json(['profil' => $user]);
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
            $picture = $user->getAttributes()['picture'];
            $userPicture = $user->picture;
            $user->delete();

            // Delete picture if it exists in storage
            // Do this after deleted profil, in case exception thrown when deleting profile
            // We can also implement it when changing picture in getDatas method
            if ($userPicture !== $picture && Storage::disk('public')->exists($picture)) {
                Storage::disk('public')->delete($picture);
            }

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
        } else if ($request->input('avatar')) {
            $data['picture'] = $request->input('avatar');
        }

        return $data;
    }
}
