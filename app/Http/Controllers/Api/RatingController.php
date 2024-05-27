<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ritual;
use Illuminate\Support\Facades\Session;
use App\Models\UserIp;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
 * @OA\Post(
 *     path="/api/rating/{id}",
 *     summary="Рейтинг для для ритуальных компаний",
 *     description="Добавления рейтинга",
 *     tags={"Главная страница"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="rating",
 *                     type="integer",
 *                     example=5
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Рейтинг успешно сохранен."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Вы можете оставлять рейтинг только раз в месяц."
 *             )
 *         )
 *     )
 * )
 */
public function post_rate(Request $request, $id)
{
    // Получаем ритуал по ID
    $ritual = Ritual::findOrFail($id);
    $ipAddress = $request->getClientIp();

    // Проверяем, чтобы пользователь отправлял форму не чаще раза в месяц
    $lastRatingSubmission = Session::get('last_rating_submission_' . $ipAddress . '_' . $ritual->id);
    if ($lastRatingSubmission && $lastRatingSubmission >= now()->subMonth()->timestamp) {
        return response()->json(['error' => 'Вы можете оставлять рейтинг только раз в месяц.'], 400);
    }

    // Получаем рейтинг, имя и комментарий из запроса
    $rating = $request->input('rating');
    $name = $request->input('name');
    $comment = $request->input('comment');

    $ritual->total_rating_requests++;

    // Получаем существующие имена и комментарии и рейтинг
    $existingRatings = $ritual->ratings ? explode(',', $ritual->ratings) : [];
    $existingNames = explode(',', $ritual->name);
    $existingComments = explode(',', $ritual->comment);

    // Добавляем новое имя и комментарий к существующим
    $existingNames[] = $name;
    $existingComments[] = $comment;
    $existingRatings[] = $rating;

    if (empty($name)) {
        $name = 'Анонимный пользователь';
        $existingNames[] = $name;
        $ritual->name = implode(',', $existingNames);
    }

    // Создаем новую запись в таблице ratings
    $ritual->ratings()->create([
        'rating' => $rating,
        'name' => implode(',', $existingNames),
        'comment' => implode(',', $existingComments)
    ]);

    // Получаем количество минимальных оценок (1 и 2)
    $minRatings = $ritual->ratings()->where('rating', '<', 3)->count();
    // Получаем количество положительных оценок (4 и 5)
    $maxRatings = $ritual->ratings()->where('rating', '>', 3)->count();

    $viewsCount = Session::get('views_count_' . $id, 0);
    $viewsCount++;
    Session::put('views_count_' . $id, $viewsCount);

    // Обновляем средний рейтинг
    $ritual->average_rating = round($ritual->averageRating(), 1);
    $ritual->min_rating = $minRatings;
    $ritual->max_rating = $maxRatings;
    $ritual->views_count = $viewsCount;

    $existingRatings = array_filter($existingRatings);
    $ritual->ratings = implode(',', $existingRatings);

    $existingNames = array_filter($existingNames);
    $ritual->name = implode(',', $existingNames);

    $existingComments = array_filter($existingComments);
    $ritual->comment = implode(',', $existingComments);
    $ritual->save();

    // Сохраняем время последней отправки рейтинга в сессию
    Session::put('last_rating_submission_' . $ipAddress . '_' . $id, now()->timestamp);

    // Увеличиваем счетчик общего количества запросов на создание рейтинга
    $totalRatingRequests = Session::get('total_rating_requests', 0);
    Session::put('total_rating_requests', $totalRatingRequests + 1);

    return response()->json(['message' => 'Рейтинг успешно сохранен.']);
}





}
