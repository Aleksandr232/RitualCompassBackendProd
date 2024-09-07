<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

/**
*  @OA\Post(
*     path="/api/register",
*     tags={"Авторизация"},
*     summary="Регистрация нового пользователя",
*     operationId="register",
*     @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*             required={"name", "email", "password"},
*             @OA\Property(property="name", type="string", example="Имя пользователя"),
*             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
*             @OA\Property(property="password", type="string", format="password", example="secret"),
*         ),
*     ),
*     @OA\Response(response="200", description="Успешная регистрация", @OA\JsonContent(
*          @OA\Property(property="success", type="string", example="Регистрация прошла успешно"),
*          @OA\Property(property="token", type="string", example="сгенерированный токен"),
*     )),
* )
* @OA\Post(
*     path="/api/login",
*     tags={"Авторизация"},
*     summary="Аутентификация пользователя",
*     operationId="login",
*     @OA\RequestBody(
*         @OA\JsonContent(
*             required={"email", "password"},
*             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
*             @OA\Property(property="password", type="string", format="password", example="secret"),
*         ),
*     ),
*     @OA\Response(response="200", description="Успешный ответ"),
*     @OA\Response(response="401", description="Ошибка аутентификации"),
* )
* @OA\Get(
*     path="/api/handle-auth-callback",
*     tags={"Авторизация"},
*     summary="Обработка колбэка аутентификации Google",
*     operationId="handleAuthCallback",
*     @OA\Response(
*         response="200",
*         description="Успешная аутентификация",
*         @OA\JsonContent(
*             @OA\Property(property="user", type="object", example="Данные пользователя"),
*             @OA\Property(property="token", type="string", example="сгенерированный токен"),
*             @OA\Property(property="token_type", type="string", example="Bearer"),
*         ),
*     ),
*     @OA\Response(response="422", description="Неверные учетные данные", @OA\JsonContent(
*          @OA\Property(property="error", type="string", example="Invalid credentials provided."),
*     )),
* )
* @OA\Get(
*     path="/api/redirect-to-auth",
*     tags={"Авторизация"},
*     summary="Перенаправление на страницу аутентификации с помощью Google",
*     operationId="redirectToAuth",
*     @OA\Response(response="200", description="Успешное перенаправление", @OA\JsonContent(
*          @OA\Property(property="url", type="string", example="URL для аутентификации"),
*     )),
* )
*
*/


class AuthController extends Controller
{
    public function redirectToAuth(): JsonResponse
    {
        return response()->json([
            'url' => Socialite::driver('google')
                            ->stateless()
                            ->redirect()
                            ->getTargetUrl(),
        ]);
    }

    public function handleAuthCallback(): JsonResponse
    {
        try {
            $socialiteUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid credentials provided.'], 422);
        }

        $user = User::firstOrCreate(
            ['email' => $socialiteUser->getEmail()],
            [
                'email_verified_at' => now(),
                'name' => $socialiteUser->getName(),
                'google_id' => $socialiteUser->getId(),
                'avatar' => $socialiteUser->getAvatar(),
            ]
        );

        $userToken = $user->createToken('google-token')->plainTextToken;
        $user->remember_token = $userToken;
        $user->save();

        return response()->json([
            'user' => $user,
            'token' => $userToken,
            'token_type' => 'Bearer',
        ]);
    }

    public function register(Request $request)
    {


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $userToken = $user->createToken('remember_token')->plainTextToken;
        $user->remember_token = $userToken;
        $user->save();

        return response()->json(['success'=>'Регистрация прошла успешно', 'token' => $userToken]);
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $userToken = $user->createToken('auth_token')->plainTextToken;
            $user->remember_token = $userToken;
            $user->save();

            return response()->json(['success' => 'Авторизация прошла успешно', 'token' => $userToken]);
        } else {
            return response()->json(['error' => 'Ошибка аутентификации'], 401);
        }
    }

/**
 * @OA\Get(
 *     path="/api/user",
 *     tags={"Админ панель"},
 *     summary="Получение информации о текущем пользователе",
 *     operationId="getUser",
 *     security={{"bearer_token":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="Иван Иванов"),
 *             @OA\Property(property="email", type="string", example="ivan@example.com"),
 *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-04-01T12:00:00.000000Z"),
 *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-04-01T12:00:00.000000Z")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизованный доступ",
 *         @OA\JsonContent(
 *             @OA\Property(property="error_message", type="string", example="Неавторизованный доступ")
 *         )
 *     )
 * )
 */

    public function user()
    {
        return response()->json(auth()->user());
    }

/**
 * @OA\Post(
 *     path="/api/logout",
 *     tags={"Админ панель"},
 *     summary="Выход пользователя из системы",
 *     operationId="logout",
 *     security={{"bearer_token":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *         @OA\JsonContent(
 *             @OA\Property(property="success_message", type="string", example="Пока, John Doe")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error_message", type="string", example="Пользователь не найден!")
 *         )
 *     )
 * )
 */

    public function logout(Request $request) {
        $user = $request->user();

        if (!empty($user->remember_token)) {
            $user->remember_token = null;
            $user->save();
                return response()->json(['success_message' => 'Пока, ' . $user->name]);
        } else {
            	return response()->json(['error_message' => 'Пользователь не найден!']);
        }

    }

    public function redirectToAuthApple(): JsonResponse
    {
        return response()->json([
            'url' => Socialite::driver('apple')
                ->stateless()
                ->redirect()
                ->getTargetUrl(),
        ]);
    }

    public function handleAuthCallbackApple(): JsonResponse
    {
        try {
            $socialiteUser = Socialite::driver('apple')->stateless()->user();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid credentials provided.'], 422);
        }

        $user = User::firstOrCreate(
            ['email' => $socialiteUser->getEmail()],
            [
                /* 'email_verified_at' => now(), */
                /* 'name' => $socialiteUser->getName(), */
                /* 'apple_id' => $socialiteUser->getId(),
                'avatar' => $socialiteUser->getAvatar(), */
            ]
        );

        $userToken = $user->createToken('apple-token')->plainTextToken;
        $user->remember_token = $userToken;
        $user->save();

        return response()->json([
            'user' => $user,
            'token' => $userToken,
            'token_type' => 'Bearer',
        ]);
    }
}
