<?php

namespace App\Swagger\Schemas\Models;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="Administrator",
 *     description="Administrator model",
 *     schema="Administrator",
 *     type="object",
 *     @OA\Xml(name="Administrator")
 * )
 */
class AdministratorSchema
{
//*     required={"id", "name", "email"},
//*     @OA\Property(property="id", type="integer"),
// *     @OA\Property(property="name", type="string"),
// *     @OA\Property(property="email", type="string"),

    /**
     * @OA\Property(
     *     title="ID",
     *     description="ID of the administrator",
     *     format="int64",
     *     example=1
     * )
     *
     * @var integer
     */
    private int $id;

    /**
     * @OA\Property(
     *     title="Name",
     *     description="Name of the administrator",
     *     example="Jane Doe"
     * )
     *
     * @var string
     */
    public string $name;

    /**
     * @OA\Property(
     *     title="Password",
     *     description="Password of the administrator",
     *     format="password",
     *     example="hashedpassword123"
     * )
     *
     * @var string
     */
    public string $password;

    /**
     * @OA\Property(
     *     title="Email",
     *     description="Email address of the administrator",
     *     example="jane.doe@example.com"
     * )
     *
     * @var string
     */
    public string $email;

    /**
     * @OA\Property(
     *     title="Profiles",
     *     description="List of profiles associated with the administrator",
     *     type="array",
     *     @OA\Items(ref="#/components/schemas/Profil")
     * )
     *
     * @var ProfilSchema[]
     */
    public array $profiles;
}
