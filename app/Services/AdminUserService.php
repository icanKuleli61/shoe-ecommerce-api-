<?php

namespace App\Services;

use App\Models\User;

use App\Exceptions\BaseException;

use App\Enums\ErrorCode;

class AdminUserService
{
    public function index($request)
    {
        $query = User::with([

            'addresses' => function ($query) {

                $query->where(
                    'is_default',
                    true
                )

                    ->with([

                        'city',
                        'district',
                        'neighborhood'

                    ]);
            }

        ]);



        if ($request->search) {

            $query->where(

                function ($q) use ($request) {

                    $q->where(
                        'first_name',
                        'like',
                        '%' . $request->search . '%'
                    )

                        ->orWhere(
                            'last_name',
                            'like',
                            '%' . $request->search . '%'
                        )

                        ->orWhere(
                            'email',
                            'like',
                            '%' . $request->search . '%'
                        );
                }
            );
        }



        if ($request->status === 'active') {

            $query->where(
                'is_active',
                true
            );
        }



        if ($request->status === 'passive') {

            $query->where(
                'is_active',
                false
            );
        }



        return $query
            ->latest()
            ->get();
    }

    public function toggleStatus($id)
    {
        $user = User::find($id);



        if (!$user) {

            throw new BaseException(
                ErrorCode::NOT_FOUND
            );
        }



        if ($user->role === 'admin') {

            throw new BaseException(
                ErrorCode::ADMIN_STATUS_CHANGE_FORBIDDEN
            );
        }



        $user->is_active =
            !$user->is_active;



        $user->save();



        return $user->fresh();
    }
}