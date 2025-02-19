<?php

use App\Enums\ProfilStatusEnum;

if (! function_exists('getProfilEnumStatuses')) {
    function getProfilEnumStatuses(): array
    {
        return [ProfilStatusEnum::INACTIF, ProfilStatusEnum::WAITING, ProfilStatusEnum::ACTIF];
    }
}

if (! function_exists('getProfilStatuses')) {
    function getProfilStatuses(): array
    {
        return array_column(getProfilEnumStatuses(), 'value');
    }
}

if (! function_exists('getProfilStatus')) {
    function getProfilStatus(int $id): string|null
    {
        $statuses = getProfilStatuses();
        if ($id < count($statuses)) {
            return $statuses[$id];
        }

        return null;
    }
}

