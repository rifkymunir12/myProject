<?php

namespace App\Http\Resources;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {   
        return [
            'id'             =>   $this->id,
            'name'           =>   $this->name,
    		'email'          =>   $this->email,
            'image_profile'  =>   url('/').'/storage/'.$this->image_profile,
            'tempat_lahir'   =>   $this->tempat_lahir,   
            'tanggal_lahir'  =>   $this->tanggal_lahir,
            'gender'         =>   $this->gender,
            'lokasi'         =>   $this->lokasi,
            'nomor_telepon'  =>   $this->nomor_telepon,
            'timezone'       =>   $this->timezone,
            'role'           =>   $this->getRoleNames()->first(),
        ];
    }
}
