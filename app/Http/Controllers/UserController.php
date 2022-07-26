<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    const ROLE_SUPER_ADMIN = 3;

    public function promoteUserToSuperAdmin($userId) {

        try {
            
            $user = User::find($userId);

            if (!$user) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'User not found'
                    ],
                    404
                );
            }

            $user->roles()->attach(self::ROLE_SUPER_ADMIN);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'User '. $user->name .' promoted to super_admin'
                ],
                201
            );

        } catch (Exception $exception) {
            
            Log::error("Error promoting user to super_admin" . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error promoting user to super_admin'
                ],
                500
            );
        }
    }

    public function degradeUserFromSuperAdmin($userId) {

        try {
            
            $user = User::find($userId);

            if (!$user) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'User not found'
                    ],
                    404
                );
            }

            $user->roles()->detach(self::ROLE_SUPER_ADMIN);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'User '. $user->name .' is not super_admin anymore'
                ],
                200
            );

        } catch (Exception $exception) {
            
            Log::error("Error degrading user from super_admin" . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error degrading user from super_admin'
                ],
                500
            );
        }
    }
}
