<?php

namespace App\Http\Controllers\API;

use App\Enums\ProfilStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfilRequest;
use App\Http\Resources\ProfilResource;
use App\Models\Profil;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;
use Storage;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user",
     *     security={{"sanctum": {}}},
     *     tags={"Administrators"},
     *     summary="Get administrator information",
     *     description="Returns administrator data",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Administrator")
     *     ),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user()->load(['profiles'])
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Retrieve a list of active profiles",
     *     tags={"Profiles"},
     *     security={
     *         {},
     *         {"sanctum": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="A list of active profiles",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Profil")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $profils = Profil::query()
            ->with(['administrator'])
            ->where(['status' => ProfilStatusEnum::ACTIF->value])
            ->get();

        if (!$request->user('api')) {
            $profils->makeHidden(['status', 'administrator']);
        }

        return response()->json(['profils' => $profils]);
    }

    public function indexWithResource(): ResourceCollection
    {
        $profils = Profil::query()
            ->with(['administrator'])
            ->where(['status' => ProfilStatusEnum::ACTIF->value])
            ->get();

        return ProfilResource::collection($profils);
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     security={{"sanctum": {}}},
     *     summary="Create a new profile",
     *     tags={"Profiles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 ref="#/components/schemas/ProfilRequest"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Profile created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Profil")
     *     )
     * )
     */
    public function store(ProfilRequest $request): JsonResponse
    {
        $data = $this->getDatas($request);
        $profil = Profil::query()->create($data);
        $profil = $profil->load(['administrator']);
        return response()->json(['profil' => $profil], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     security={{"sanctum": {}}},
     *     summary="Retrieve a specific profile",
     *     tags={"Profiles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the profile to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Profil")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Profile not found"
     *     )
     * )
     */
    public function show(Profil $user): JsonResponse
    {
        $user = $user->load(['administrator']);
        return response()->json(['profil' => $user]);
    }

    /**
     * @OA\Post(
     *     path="/api/users/{id}",
     *     security={{"sanctum": {}}},
     *     summary="Update an existing profile",
     *     tags={"Profiles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the profile to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Profil data to update profil",
     *         @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"_method"},
     *                  allOf={
     *                      @OA\Schema(ref="#/components/schemas/ProfilRequest"),
     *                      @OA\Schema(
     *                          @OA\Property(
     *                              property="_method",
     *                              type="string",
     *                              enum={"PUT"},
     *                              default="PUT",
     *                              description="HTTP method override for update requests"
     *                          )
     *                      )
     *                  }
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Profil")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Profile not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to update profile"
     *     )
     * )
     */
    public function update(ProfilRequest $request, Profil $user): JsonResponse
    {
        // Can be used to delete profile
        if ($request->boolean('deleted')) {
            return $this->destroy($user);
        }

        try {
            $data = $this->getDatas($request);
            $user->update($data);
            $user = $user->fresh(['administrator']);

            return response()->json(['profil' => $user]);
        } catch (Exception $exc) {
            Log::error(self::class, ['message' => $exc->getMessage(), 'exc' => $exc]);
            return response()->json(['error' => 'Failed to update Profil.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Delete a profile",
     *     security={{"sanctum": {}}},
     *     tags={"Profiles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the profile to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Profil successfully deleted!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Profile not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to delete profile"
     *     )
     * )
     */
    public function destroy(Profil $user): JsonResponse
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
