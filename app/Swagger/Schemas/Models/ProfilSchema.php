<?php

namespace App\Swagger\Schemas\Models;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="Profil",
 *     description="Profil model",
 *     schema="Profil",
 *     type="object",
 *     @OA\Xml(
 *         name="Profil"
 *     )
 * )
 */
class ProfilSchema
{
    /**
     * @OA\Property(
     *     title="ID",
     *     description="ID of the profil",
     *     format="int64",
     *     example=1
     * )
     *
     * @var integer
     */
    private $id;

    /**
     * @OA\Property(
     *     title="First Name",
     *     description="First name of the profil",
     *     example="John"
     * )
     *
     * @var string
     */
    public string $firstname;

    /**
     * @OA\Property(
     *     title="Last Name",
     *     description="Last name of the profil",
     *     example="Doe"
     * )
     *
     * @var string
     */
    public string $lastname;

    /**
     * @OA\Property(
     *     title="Picture",
     *     description="URL of the profil picture",
     *     example="http://example.com/images/profile.jpg"
     * )
     *
     * @var string
     */
    public string $picture;

    /**
     * @OA\Property(
     *     title="Status",
     *     description="Status of the profil",
     *     example="active"
     * )
     *
     * @var string
     */
    public string $status;

    /**
     * @OA\Property(
     *     title="User ID",
     *     description="ID of the associated user",
     *     format="int64",
     *     example=10
     * )
     *
     * @var integer
     */
    public int $user_id;

    /**
     * @OA\Property(
     *     title="Administrator",
     *     description="Administrator related to the profil"
     * )
     *
     * @var AdministratorSchema
     */
    public AdministratorSchema $administrator;
}
