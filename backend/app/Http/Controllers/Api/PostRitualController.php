<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ritual;
use App\Models\RitualFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class PostRitualController extends Controller
{
    /**
 * @OA\Post(
 *     path="/api/rituals",
 *     tags={"Админ панель"},
 *     summary="Добавления ритуальной компании",
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="company_ritual",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="phone_ritual",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="description_ritual",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="address_ritual",
 *                     type="string"
 *                 ),
 *                  @OA\Property(
 *                     property="prices",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="work_time_ritual",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="service_ritual",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="site_ritual",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="social_network_ritual",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="files[]",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         format="binary"
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="string")
 *         )
 *     )
 * )
 */
public function post_ritual(Request $request)
    {
        $ritual = new Ritual([
            'company_ritual' => $request->company_ritual,
            'phone_ritual' => $request->phone_ritual,
            'description_ritual' => $request->description_ritual,
            'address_ritual' => $request->address_ritual,
            'work_time_ritual' => $request->work_time_ritual,
            'service_ritual' => $request->service_ritual,
            'site_ritual' => $request->site_ritual,
            'social_network_ritual' => $request->social_network_ritual,
            'prices' => $request->prices,

        ]);

        if($request->hasFile('files'))
        {
            $files = $request->file('files');
            $paths = [];
            $fileArr = [];

            foreach ($files as $file)
            {
                $path = Storage::disk('ritual')->putFile('file', $file);
                $fullPath = "https://cz19567.tw1.ru/ritual/" . $path;
                $paths[] = $fullPath;
                $fileArr[] = $file->getClientOriginalName();
            }

            $ritual->path = implode(",", $paths);
            $ritual->files = implode(",", $fileArr);
        }

        $ritual->save();



        return response()->json(['success' => 'Ритуальная компания добавлена']);
    }


/**
 * @OA\Get(
 *     path="/api/all/rituals",
 *     tags={"Админ панель"},
 *     summary="Вывод компаний",
 *     description="Вывод компаний",
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id_ritual", type="integer", description="id company"),
 *                 @OA\Property(property="company_ritual", type="string", description="The name of the ritual company"),
 *                 @OA\Property(property="phone_ritual", type="string", description="The phone number of the ritual company"),
 *                 @OA\Property(property="description_ritual", type="string", description="The description of the ritual"),
 *                 @OA\Property(property="address_ritual", type="string", description="The address of the ritual company"),
 *                 @OA\Property(property="work_time_ritual", type="string", description="The work hours of the ritual company"),
 *                 @OA\Property(property="service_ritual", type="string", description="The services offered by the ritual company"),
 *                 @OA\Property(property="site_ritual", type="string", description="The website of the ritual company"),
 *                 @OA\Property(property="social_network_ritual", type="string", description="The social media links of the ritual company"),
 *                 @OA\Property(property="paths", type="string", description="The path to the ritual details page", example="https://cz19567.tw1.ru/ritual/Ритуал.jpg"),
 *                 @OA\Property(property="files", type="array", @OA\Items(type="string"), description="The files associated with the ritual"),
 *                 @OA\Property(property="average_rating", type="number", description="The average rating of the ritual"),
 *                 @OA\Property(property="total_rating_requests", type="integer", description="The total number of rating requests for the ritual"),
 *                 @OA\Property(property="views_count", type="integer", description="сколько было просмотров"),
 *                 @OA\Property(property="name", type="integer", description="имена кто оставил комменты"),
 *                 @OA\Property(property="comment", type="integer", description="комменты пользователей "),
 *                 @OA\Property(property="ratings", type="integer", description="кто какой рейтинг поставил"),
 *                 @OA\Property(property="min_rating", type="integer", description="minimum rating"),
 *                 @OA\Property(property="max_rating", type="integer", description="max rating"),
 *                 @OA\Property(property="sort_by_rating", type="boolean", description="сортировка по среднему рейтингу", example="https://cz19567.tw1.ru/api/all/rituals?sort_by_rating=true" ),
 *                 @OA\Property(property="sort_by_prices", type="boolean", description="сортировка по цене", example="https://cz19567.tw1.ru/api/all/rituals?sort_by_prices=true" ),
 *                 @OA\Property(property="sort_by_prices&sort_by_rating", type="boolean", description="сортировка по цене и рейтингу", example="https://cz19567.tw1.ru/api/all/rituals?sort_by_prices=true&sort_by_rating=true" )
 *              )
 *         )
 *     )
 * )
 */
    public function get_ritual(Request $request)
    {
        $rituals = Ritual::all()->toArray();

        if ($request->boolean('sort_by_rating')) {
            $data = collect($rituals)->sortByDesc('average_rating');
        } elseif ($request->boolean('sort_by_prices')) {
            $data = collect($rituals)->sortByDesc('prices');
        } else {
            $data = collect($rituals);
        }



        $data = $data->map(function ($ritual) {
            $paths = explode(',', $ritual['path']);
            $files = explode(',', $ritual['files']);
            $social = explode(',', $ritual['social_network_ritual']);
            $name = explode(',', $ritual['name']);
            $comment = explode(',', $ritual['comment']);
            $ratings = explode(',', $ritual['ratings']);

            return [
                'id' => $ritual['id'],
                'company_ritual' => $ritual['company_ritual'],
                'phone_ritual' => $ritual['phone_ritual'],
                'description_ritual' => $ritual['description_ritual'],
                'address_ritual' => $ritual['address_ritual'],
                'work_time_ritual' => $ritual['work_time_ritual'],
                'service_ritual' => $ritual['service_ritual'],
                'site_ritual' => $ritual['site_ritual'],
                'social_network_ritual' => $social,
                'paths' => $paths,
                'files' => $files,
                'average_rating' => number_format($ritual['average_rating'], 1, '.', ''),
                'total_rating_requests' => $ritual['total_rating_requests'],
                'min_rating' => $ritual['min_rating'],
                'max_rating' => $ritual['max_rating'],
                'prices' => $ritual['prices'],
                'views_count' => $ritual['views_count'],
                'name'=>$name,
                'comment'=>$comment,
                'ratings'=>$ratings
            ];
        })->values();

        return response()->json($data);
    }

/**
 * @OA\Delete(
 *     path="/api/rituals/delete/{id}",
 *     summary="Удаление компании",
 *     tags={"Админ панель"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="string")
 *         )
 *     )
 * )
 */
    public function delete_ritual($id)
    {
        $ritual = Ritual::findOrFail($id);

        // Удаляем файлы из хранилища
        $paths = explode(',', $ritual->path);
        $files = explode(',', $ritual->files);

        foreach ($paths as $path) {
            Storage::disk('ritual')->delete(str_replace('https://cz19567.tw1.ru/ritual/', '', $path));
        }

        // Удаляем запись из базы данных
        $ritual->delete();

        return response()->json(['success' => 'Ритуальная компания удалена']);
    }

/**
 * @OA\Post(
 *     path="/api/rituals/{id}",
 *     tags={"Админ панель"},
 *     summary="Обновления ритуальной компании",
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="company_ritual",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="phone_ritual",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="description_ritual",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="address_ritual",
 *                     type="string"
 *                 ),
 *                  @OA\Property(
 *                     property="prices",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="work_time_ritual",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="service_ritual",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="site_ritual",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="social_network_ritual",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="files[]",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         format="binary"
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="string")
 *         )
 *     )
 * )
 */

    public function update_ritual(Request $request, $id)
    {
        $ritual = Ritual::find($id);

        if (!$ritual) {
            return response()->json(['error' => 'Ритуальная компания не найдена'], 404);
        }

        // Удаляем старые файлы

        /* $this->delete_ritual_files($ritual->id); */

        if ($request->has('company_ritual')) {
            $ritual->company_ritual = $request->company_ritual;
        }

        if ($request->has('phone_ritual')) {
            $ritual->phone_ritual = $request->phone_ritual;
        }

        if ($request->has('description_ritual')) {
            $ritual->description_ritual = $request->description_ritual;
        }

        if ($request->has('address_ritual')) {
            $ritual->address_ritual = $request->address_ritual;
        }

        if ($request->has('work_time_ritual')) {
            $ritual->work_time_ritual = $request->work_time_ritual;
        }

        if ($request->has('service_ritual')) {
            $ritual->service_ritual = $request->service_ritual;
        }

        if ($request->has('site_ritual')) {
            $ritual->site_ritual = $request->site_ritual;
        }

        if ($request->has('social_network_ritual')) {
            $ritual->social_network_ritual = $request->social_network_ritual;
        }

        if ($request->has('prices')) {
            $ritual->prices = $request->prices;
        }

        $ritual->save();

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $paths = explode(',', $ritual->path);
            $fileArr = explode(',', $ritual->files);

            // Upload new files
            foreach ($files as $file) {
                $path = Storage::disk('ritual')->putFile('file', $file);
                $fullPath = "https://cz19567.tw1.ru/" . $path;
                $paths[] = $fullPath;
                $fileArr[] = $file->getClientOriginalName();
            }

            $ritual->path = implode(",", $paths);
            $ritual->files = implode(",", $fileArr);
            $ritual->save();
        }

        return response()->json(['success' => 'Ритуальная компания обновлена']);
    }

    private function delete_ritual_files($ritual_id)
    {
        $ritual = Ritual::findOrFail($ritual_id);

        // Удаляем файлы из хранилища
        $paths = explode(',', $ritual->path);
        $files = explode(',', $ritual->files);

        foreach ($paths as $path) {
            Storage::disk('ritual')->delete(str_replace('https://cz19567.tw1.ru/', '', $path));
        }

        foreach ($files as $file) {
            Storage::disk('ritual')->delete(str_replace('https://cz19567.tw1.ru/', '', $file));
        }

        // Очищаем значения полей path и files
        $ritual->path = '';
        $ritual->files = '';
        $ritual->save();
    }
}
