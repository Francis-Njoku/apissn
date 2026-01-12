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
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use Carbon\Carbon;
use App\Models\UserGroup;
use App\Models\User;
use App\Mail\WelcomeMail;
use App\Mail\WelcomeEmail;
use App\Mail\ResetPassword;
use App\Http\Resources\UserResource;

//use App\Enum\UserAuth;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Controllers\Controller;
use App\Helpers\ApiResponseHelper;
use App\Exceptions\AuthException;

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
            // Validate input
            $validateUser = Validator::make(
                $request->all(),
                [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'phone' => 'nullable|string|max:20',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required|string|min:8|confirmed',
                ]
            );

            if ($validateUser->fails()) {
                return ApiResponseHelper::validationError($validateUser->errors()->toArray());
            }

            // Check if email already exists (additional validation)
            if (User::where('email', $request->email)->exists()) {
                throw AuthException::emailAlreadyExists($request->email);
            }

            // Generate user details
            $userData = [
                'name' => $this->generateUser(),
                'email' => $request->email,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'role_id' => $request->manager_id ? 3 : 2, // Different role for manager users
                'identity' => $this->generateIdentity(),
                'status' => 'approved',
                'password' => Hash::make($request->password)
            ];

            // Create user
            $user = User::create($userData);

            // Send email to new user
            event(new Registered($user));

            Log::info('User created successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'phone' => $user->phone,
                    'role_id' => $user->role_id,
                    'status' => $user->status,
                ],
                // 'verification_required' => true,
                // 'verification_message' => 'Please check your email for verification instructions.'
            ], 201);

        } catch (AuthException $e) {
            return ApiResponseHelper::error(
                $e->getMessage(),
                $e->getErrorCode(),
                $e->getErrorDetails(),
                $e->getStatusCode()
            );
        } catch (\Throwable $th) {
            Log::error('User creation error', [
                'error' => $th->getMessage(),
                'email' => $request->email ?? 'unknown',
                'trace' => $th->getTraceAsString()
            ]);

            return ApiResponseHelper::serverError(
                'An unexpected error occurred during user registration',
                'USER_CREATION_ERROR',
                [$th->getMessage()]
            );
        }
    }

    /**
     * Login The User
     * @param Request $request
     * @return JsonResponse
     */
    public function loginUser(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:1',
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::validationError($validator->errors()->toArray());
        }

        $credentials = $request->only('email', 'password');

        try {
            // Check if user exists
            $user = User::where('email', $credentials['email'])->first();

            if (!$user) {
                throw AuthException::userNotFound($credentials['email']);
            }

            // Check if user is verified
            // if (!$user->hasVerifiedEmail()) {
            //     throw AuthException::userNotVerified($credentials['email']);
            // }

            // Check if account is suspended
            // if ($user->status === 'suspended') {
            //     throw AuthException::accountSuspended($credentials['email']);
            // }


            // Attempt authentication
            if (!$token = JWTAuth::attempt($credentials)) {
                // Log failed attempt for security
                Log::warning('Failed login attempt', [
                    'email' => $credentials['email'],
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                throw AuthException::invalidCredentials();
            }

            // Check if user has active subscription (if applicable)
            // You can add additional business logic here

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'token' => $token,
                'refresh_token' => $this->createRefreshToken($token),
                'group_id' => Auth::user()->role_id,
                'user' => [
                    'id' => Auth::user()->id,
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'first_name' => Auth::user()->first_name,
                    'last_name' => Auth::user()->last_name,
                ]
            ], 200);

        } catch (AuthException $e) {
            return ApiResponseHelper::error(
                $e->getMessage(),
                $e->getErrorCode(),
                $e->getErrorDetails(),
                $e->getStatusCode()
            );
        } catch (JWTException $e) {
            return ApiResponseHelper::serverError(
                'Could not create authentication token',
                'TOKEN_CREATION_ERROR',
                [$e->getMessage()]
            );
        } catch (\Throwable $e) {
            Log::error('Login error', [
                'error' => $e->getMessage(),
                'email' => $credentials['email'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return ApiResponseHelper::serverError(
                'An unexpected error occurred during login',
                'LOGIN_ERROR',
                [$e->getMessage()]
            );
        }
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

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                throw AuthException::userNotFound($request->email);
            }

            // Check if user account is active
            // if ($user->status !== 'approved') {
            //     throw AuthException::accountSuspended($request->email);
            // }

            // Delete existing password reset tokens
            DB::table('password_resets')->where('email', $request->email)->delete();

            $token = random_int(100000, 999999);

            // Store password reset token
            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);

            // Send password reset email
            Mail::to($request->email)->send(new ResetPassword($token));

            Log::info('Password reset email sent', [
                'email' => $request->email,
                'ip' => $request->ip(),
            ]);

            return new JsonResponse([
                'success' => true,
                'message' => "Please check your email for a 6 digit pin",
                'expires_in' => 3600, // Token expires in 1 hour
                'instructions' => 'Enter the 6-digit pin in the password reset form.'
            ], 200);

        } catch (AuthException $e) {
            return ApiResponseHelper::error(
                $e->getMessage(),
                $e->getErrorCode(),
                $e->getErrorDetails(),
                $e->getStatusCode()
            );
        } catch (\Throwable $e) {
            Log::error('Password reset request error', [
                'error' => $e->getMessage(),
                'email' => $request->email,
                'trace' => $e->getTraceAsString()
            ]);

            return ApiResponseHelper::serverError(
                'An unexpected error occurred while processing your password reset request',
                'PASSWORD_RESET_ERROR',
                [$e->getMessage()]
            );
        }
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
            'token' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::validationError($validator->errors()->toArray());
        }

        try {
            $check = DB::table('password_resets')->where([
                ['email', $request->email],
                ['token', $request->token],
            ]);

            if (!$check->exists()) {
                throw AuthException::invalidToken($request->token);
            }

            $difference = Carbon::now()->diffInSeconds($check->first()->created_at);
            if ($difference > 3600) {
                throw AuthException::tokenExpired($request->token);
            }

            // Delete the used token
            DB::table('password_resets')->where([
                ['email', $request->email],
                ['token', $request->token],
            ])->delete();

            // Update user password
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                throw AuthException::userNotFound($request->email);
            }

            // Check if user account is active
            // if ($user->status !== 'approved') {
            //     throw AuthException::accountSuspended($request->email);
            // }

            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // Log password reset
            Log::info('Password reset successful', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => "Your password has been reset successfully",
                    'instructions' => 'You can now log in with your new password.'
                ],
                200
            );

        } catch (AuthException $e) {
            return ApiResponseHelper::error(
                $e->getMessage(),
                $e->getErrorCode(),
                $e->getErrorDetails(),
                $e->getStatusCode()
            );
        } catch (\Throwable $e) {
            Log::error('Password reset error', [
                'error' => $e->getMessage(),
                'email' => $request->email,
                'trace' => $e->getTraceAsString()
            ]);

            return ApiResponseHelper::serverError(
                'An unexpected error occurred while resetting your password',
                'PASSWORD_RESET_ERROR',
                [$e->getMessage()]
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
        $role  = $request->query('role');
        $query = User::select([
            'id',
            'name',
            'first_name',
            'last_name',
            'email',
            'phone',
            'role_id',
            'status',
            'created_at',
            'updated_at'
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

        // Handle per_page parameter
        $perPage = $request->query('per_page', 10);
        
        // Check for per_page=all option to return all users without pagination
        if ($perPage === 'all') {
            return UserResource::collection($query->get());
        }
        
        // Validate per_page parameter (min: 1, max: 100, default: 10)
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
                'first_name',
                'last_name',
                'phone',
                'email',
                'role_id',
                'status'
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
