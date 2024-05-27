<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TelegramContact;

class StatisticController extends Controller
{
    /**
 * @OA\Get(
 *     path="/api/clients/count",
 *     summary="Вывод клиентов по каждой компании",
 *     tags={"Админ панель"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="company_ritual",
 *                 type="object",
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="count", type="integer")
 *             )
 *         )
 *     )
 * )
 */
    public function getClientCountByRitualCompany()
    {
        $clientCounts = TelegramContact::selectRaw('ritual_company, COUNT(*) as count')
                        ->groupBy('ritual_company')
                        ->get();

        $clientCountByCompany = [];
        foreach ($clientCounts as $clientCount) {
            $clientCountByCompany[] = [
                'name' => $clientCount->ritual_company,
                'count' => $clientCount->count,
            ];
        }

        return response()->json(['company_ritual' => $clientCountByCompany]);
    }
}
