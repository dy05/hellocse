<?php

namespace App\Swagger\Schemas\Requests;

use OpenApi\Annotations as OA;
use SplFileInfo;

/**
 * @OA\Schema(
 *     title="ProfilRequest",
 *     description="Request body data for creating or updating a Profil",
 *     schema="ProfilRequest",
 *     type="object",
 *     required={"firstname", "lastname"}
 * )
 */
class ProfilRequestSchema
{
    /**
     * @OA\Property(
     *     title="First Name",
     *     description="The first name of the profile",
     *     example="John"
     * )
     *
     * @var string
     */
    public string $firstname;

    /**
     * @OA\Property(
     *     title="Last Name",
     *     description="The last name of the profile",
     *     example="Doe"
     * )
     *
     * @var string
     */
    public string $lastname;

    /**
     * @OA\Property(
     *     title="Status",
     *     description="The status of the profile",
     *     example="active"
     * )
     *
     * @var string
     */
    public string $status;

    /**
     * @OA\Property(
     *     title="Picture",
     *     description="Profile picture file",
     *     type="string",
     *     format="binary"
     * )
     *
     * @var SplFileInfo
     */
    public SplFileInfo $picture;
}
