<?php

namespace App\Http\Controllers\Api\Auth;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponseHelper;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use Carbon\Carbon;
use App\Models\UserGroup;
use App\Models\User;
use App\Mail\WelcomeMail;
use App\Mail\WelcomeEmail;
use App\Mail\ResetPassword;

//use App\Enum\UserAuth;
use App\Http\Resources\UserResource;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /***
     * Generate Identity
     * @param No params
     * @return unique Identity
     */

    private function getID($name)
    {
        // Query the database to find the user by email
        $user = User::where('name', $name)->first();

        // Check if the user exists
        if ($user) {
            // Return the user ID
            return $user->id; //response()->json(['id' => $user->id]);
        } else {
            // Return a not found response
            return ApiResponseHelper::notFound('User not exist', 'USER_NOT_FOUND');
        }
    }


    /***
     * Generate Identity
     * @param No params
     * @return unique Identity
     */
    private function generateIdentity()
    {
        $randomNumber = random_int(10000000000000, 99999999999999);
        if (User::where('identity', '=', $randomNumber)->exists()) {
            return $this->generateIdentity();
        } else {
            return $randomNumber;
        }
    }

    /***
     * Generate Identity
     * @param No params
     * @return unique Identity
     */
    private function generateUser()
    {
        $randomNumber = random_int(100000, 999999);
        if (User::where('name', '=', $randomNumber)->exists()) {
            return $this->generateIdentity();
        } else {
            return $randomNumber;
        }
    }
    private function isValidTimezoneId($usertimezone)
    {
        try {
            new \DateTimeZone($usertimezone);
            return true;
        } catch (Exception $e) {
            return ApiResponseHelper::serverError('Wrong Timezone, please try again', 'INVALID_TIMEZONE', [$e->getMessage()]);
        }
        return true;
    }
    private function getTimeZone($getZome)
    {
        $usertimezone = "Africa/Lagos";

        date_default_timezone_set($usertimezone);

        //new date and time
        $ndate = new datetime();
        //split into date and time seperate
        $nndate = $ndate->format("Y-m-d");
        $nntime = $ndate->format("H:i:S");
        //here you can test it
        echo $nndate . '<br/>';
        echo $nntime;

    }
    /***
     * Create User
     * @param Request $request
     * @return User
     */
    public function createUser(Request $request)
    {
        try {
            //Validated
            $validateUser = Validator::make(
                $request->all(),
                [
                    'first_name' => '',
                    'last_name' => '',
                    'phone' => '',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            //$this->isValidTimezoneId($request->gmt);

            if ($request->manager_id) {
                $user = User::create([
                    'name' => $this->generateUser(),
                    'email' => $request->email,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'phone' => $request->phone,
                    'role_id' => 2,
                    'identity' => $this->generateIdentity(),
                    'status' => 'approved',
                    'password' => Hash::make($request->password)
                ]);
            } else {
                $user = User::create([
                    'name' => $this->generateUser(),
                    'email' => $request->email,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'phone' => $request->phone,
                    'role_id' => 2,
                    'identity' => $this->generateIdentity(),
                    'status' => 'approved',
                    'password' => Hash::make($request->password)
                ]);
            }


            /*$userGroup = UserGroup::create([
                'user_id' => $user->id,
                'group_id' => '1'
            ]);*/

            // Send email to new user
            event(new Registered($user));

            //$accessToken = $user->createToken('access_token', [UserATokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.ac_expiration')));
            //$refreshToken = $user->createToken('refresh_token', [TokenAbility::ISSUE_ACCESS_TOKEN->value], Carbon::now()->addMinutes(config('sanctum.rt_expiration')));

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                //'token' => $user->createToken("API TOKEN")->plainTextToken,
                //'token' => $accessToken->plainTextToken,
                //'refresh_token' => $refreshToken->plainTextToken,
                //'group_id' => $group_id,
                //'gmt' => Auth::user()->gmt
            ], 200);
        } catch (\Throwable $th) {
            return ApiResponseHelper::serverError($th->getMessage(), 'USER_UPDATE_ERROR', [$th->getMessage()]);
        }
    }

    /**
     * Login The User
     * @param Request $request
     * @return User
     */
    public function loginUser(Request $request)
    {
        /*
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');
        $token =  Auth::guard('api')->attempt($credentials); //
        $token2 = Auth::attempt($credentials);
        $refreshToken = JWTAuth::refresh($token2);

        if (!$token) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user()->gmt;
        return response()->json([
            'user' => Auth::user()->created_at,
            'authorization' => [
                'token' => $token,
                'refresh_token' => $refreshToken,
                'type' => 'bearer',
            ]
        ]);
        */
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return ApiResponseHelper::unauthorized('Unauthorized', 'INVALID_CREDENTIALS');

            }
        } catch (JWTException $e) {
            return ApiResponseHelper::serverError('Could not create token', 'TOKEN_CREATION_ERROR', [$e->getMessage()]);
        }

        //$group = UserGroup::where('user_id', Auth::id())->get();

        /*foreach($group as $groups)
        {
            $group_id = $groups->group_id;
        }*/
        return response()->json([
            'token' => $token,
            'refresh_token' => $this->createRefreshToken($token),
            'group_id' => Auth::user()->role_id,
        ]);

    }

    public function refresh()
    {
        try {
            $token = JWTAuth::getToken();
            if (!$token) {
                return ApiResponseHelper::unauthorized('Token not provided', 'TOKEN_NOT_PROVIDED');
            }
            $newToken = JWTAuth::refresh($token);
        } catch (JWTException $e) {
            return ApiResponseHelper::serverError('Could not refresh token', 'TOKEN_REFRESH_ERROR', [$e->getMessage()]);
        }

        return response()->json(['token' => $newToken]);
    }

    private function createRefreshToken($token)
    {
        // Here you can store the refresh token in the database or another secure storage
        // For simplicity, we'll return the same token as the refresh token
        return $token;
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResource
    {
        auth()->user()->tokens()->delete();

        return response()->success([], 'logged out', 200);
    }

    /**
     * @return JsonResponse
     */
    public function signout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Successfully logged out']);
        } catch (JWTException $exception) {
            return ApiResponseHelper::serverError('Failed to logout, please try again.', 'LOGOUT_ERROR', [$exception->getMessage()]);
        }
    }

    /**
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::validationError($validator->errors()->toArray());
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return new JsonResponse([
                'success' => false,
                'message' => "This email does not exist"
            ], 400);
        }

        DB::table('password_resets')->where('email', $request->email)->delete();

        $token = random_int(100000, 999999);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        Mail::to($request->email)->send(new ResetPassword($token));

        return new JsonResponse([
            'success' => true,
            'message' => "Please check your email for a 6 digit pin"
        ], 200);
    }


    /**
     * @param VerifyPin
     * @return JsonResponse
     * @throws ValidationException
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'token' => ['required'],
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::validationError($validator->errors()->toArray());
        }

        $check = DB::table('password_resets')->where([
            ['email', $request->all()['email']],
            ['token', $request->all()['token']],
        ]);

        if ($check->exists()) {
            $difference = Carbon::now()->diffInSeconds($check->first()->created_at);
            if ($difference > 3600) {
                return new JsonResponse(['success' => false, 'message' => "Token Expired"], 400);
            }
            $delete = DB::table('password_resets')->where([
                ['email', $request->all()['email']],
                ['token', $request->all()['token']],
            ])->delete();

            $user = User::where('email', $request->email);
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // $token = $user->first()->createToken('myapptoken')->plainTextToken;

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => "Your password has been reset",
                    // 'token' => $token
                ],
                200
            );

            /*
            return new JsonResponse(
                [
                    'success' => true,
                    'message' => "You can now reset your password"
                ],
                200
            );*/
        } else {
            return new JsonResponse(
                [
                    'success' => false,
                    'message' => "Invalid token"
                ],
                401
            );
        }
    }


    /**
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function listUsers(Request $request)
    {
        $role = $request->query('role');
        $query = User::select([
            'id', 'name', 'first_name', 'last_name', 'email',
            'phone', 'role_id', 'status', 'created_at', 'updated_at'
        ]);

        // Handle role parameter case-insensitively with trimming and decoding
        $normalizedRole = strtolower(trim(urldecode($role)));
        if ($normalizedRole === 'admin') {
            $query->where('role_id', 1);
        } elseif ($normalizedRole === 'user') {
            $query->where('role_id', 2);
        }

        if ($request->has('subscriber_status')) {
            $status = strtolower(trim(urldecode($request->input('subscriber_status'))));
            
            // Validate subscriber_status
            $validStatuses = ['active', 'never_subscribed', 'expired_non_renewed'];
            if (!in_array($status, $validStatuses)) {
                Log::warning('Invalid subscriber_status value: ' . $status);
            } else {
                switch ($status) {
                    case 'active':
                        $query->withActiveSubscription();
                        break;
                    case 'never_subscribed':
                        $query->withNeverSubscribed();
                        break;
                    case 'expired_non_renewed':
                        $query->withExpiredSubscription();
                        break;
                }
            }
        }
        
        // Log query parameters for debugging
        // dump('User list query parameters', [
        //     'role' => $role,
        //     'normalized_role' => $normalizedRole,
        //     'subscriber_status' => $request->input('subscriber_status'),
        //     'normalized_subscriber_status' => $status ?? null
        // ]);
        
        // Handle per_page parameter with validation (min: 1, max: 100, default: 10)
        $perPage = $request->query('per_page', 10);
        $perPage = max(1, min(100, (int) $perPage));
        
        return UserResource::collection($query->paginate($perPage));
    }

    /**
     * Display the user profile.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function profile()
    {
        //$query = User::where('id', Auth::id())->get();
        return new UserResource(User::where('id', Auth::user()->id)->first());
    }

    /**
     * Create User
     * @param Request $request
     * // @return User
     */
    public function adminCreateUser(Request $request)
    {
        try {
            //Validated
            $validateUser = Validator::make(
                $request->all(),
                [
                    'first_Name' => '',
                    'last_Name' => '',
                    'phone' => '',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            // $this->isValidTimezoneId($request->gmt);

            $user = User::create([
                'email' => $request->email,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'role_id' => $request->role_id,
                'password' => Hash::make($request->password),
                'status' => 'approved',
                'name' => $this->generateUser(),
                'identity' => $this->generateIdentity()
            ]);


            /*$userGroup = UserGroup::create([
                'user_id' => $user->id,
                'group_id' => '1'
            ]);*/

            // Send email to new user
            event(new Registered($user));

            //$accessToken = $user->createToken('access_token', [UserATokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.ac_expiration')));
            //$refreshToken = $user->createToken('refresh_token', [TokenAbility::ISSUE_ACCESS_TOKEN->value], Carbon::now()->addMinutes(config('sanctum.rt_expiration')));

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
            ], 200);
        } catch (\Throwable $th) {
            return ApiResponseHelper::serverError($th->getMessage(), 'USER_UPDATE_ERROR', [$th->getMessage()]);
        }
    }

    public function userStatus()
    {
        if (User::where('id', Auth::id())->where('role_id', '1')->exists()) {
            return response()->json([
                'status' => 'admin',
                'message' => 'User is an admin'
            ], 200);
        } else {
            return response()->json([
                'status' => 'user',
                'message' => 'User is unauthorized'
            ], 200);
        }
    }

    /**
     * Update the specified user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id (optional) User ID for admin updates
     * @return \Illuminate\Http\Response
     */
    public function updateUser(Request $request, $id = null)
    {
        try {
            // Validate the request data
            $validateUser = Validator::make($request->all(), [
                'first_name' => 'string|max:255',
                'last_name' => 'string|max:255',
                'phone' => 'string|max:20',
                'email' => 'email|unique:users,email,' . ($id ?? Auth::id()),
                'role_id' => 'integer|exists:roles,id',
                'status' => 'string|in:approved,pending,blocked',
                'password' => 'string|min:6|confirmed',
                'current_password' => 'string|required_with:password',
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 400);
            }

            // Get the authenticated user
            $authUser = Auth::user();
            
            // Determine which user to update
            if ($id) {
                // Admin is updating a specific user
                $user = User::findOrFail($id);
            } else {
                // User is updating their own profile
                $user = $authUser;
            }

            // If password is being updated, verify current password (unless admin)
            if ($request->filled('password')) {
                // Check if the authenticated user is an admin
                $isAdmin = $authUser->hasRole('admin');
                
                // If not admin, verify current password
                if (!$isAdmin) {
                    if (!$request->filled('current_password')) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Current password is required to change password'
                        ], 400);
                    }

                    if (!Hash::check($request->current_password, $user->password)) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Current password is incorrect'
                        ], 400);
                    }
                }
            }

            // Prepare data for update, excluding empty values
            $updateData = array_filter($request->only([
                'first_name', 'last_name', 'phone', 'email', 'role_id', 'status'
            ]), function ($value) {
                return $value !== null && $value !== '';
            });

            // If password is being updated, add it to update data
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            // Update user data
            if (!empty($updateData)) {
                $user->update($updateData);
            }

            return response()->json([
                'status' => true,
                'message' => 'User updated successfully',
                'data' => new UserResource($user)
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error updating user: ' . $th->getMessage()
            ], 500);
        }
    }

}
