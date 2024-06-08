<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cemetery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostCemeteryController extends Controller
{

    /**
 * @OA\Post(
 *     path="/api/cemetery",
 *     tags={"Админ панель"},
 *     summary="Добавления кладбища в админ панели",
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="title",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="description",
 *                     type="string"
 *                 ),
 *                  @OA\Property(
 *                     property="slug",
 *                     type="string"
 *                 ),
 *                  @OA\Property(
 *                     property="coordinates",
 *                     type="string"
 *                 ),
 *                   @OA\Property(
 *                     property="files",
 *                     type="file"
 *                 ),
 *                 @OA\Property(
 *                     property="seo_title",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="seo_description",
 *                     type="string"
 *                 ),
 *                  @OA\Property(
 *                     property="seo_keywords",
 *                     type="string"
 *                 ),
 *
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
    public function post_cemetery(Request $request){
        $cemetery = new Cemetery([
            'title' => $request -> title,
            'description' => $request -> description,
            'slug' => $request -> slug,
            'seo_title' => $request -> seo_title,
            'seo_description' => $request -> seo_description,
            'seo_keywords' => $request -> seo_keywords,
            'coordinates' => is_array($request->coordinates) ? implode(',', $request->coordinates) : $request->coordinates
        ]);

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $paths = Storage::disk('ritual')->putFile('cemetery', $files);
            $cemetery->files = $files->getClientOriginalName();
            $cemetery->paths = $paths;
            $cemetery->save();
        }

        $cemetery->save();

        return response()->json(['success' => 'Кладбище добавлено']);
    }

     /**
 * @OA\Get(
 *     path="/api/cemetery",
 *     tags={"Главная страница"},
 *     summary="Вывод кладбищ на страницу кладбища",
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", description="id"),
 *                 @OA\Property(property="title", type="string", example="Мы оказываем бесплатную помощь в поиске проверенных ритуальных компаний и агентов"),
 *                 @OA\Property(property="description", type="string",  example="Если у вас возникли проблемы с выбором и не доверием ритуальным компаниям, вы можете обратиться к нам. Мы поможем и знаем, как работают эти компании"),
 *                 @OA\Property(property="slug", type="string",  example="about/search-for-ritual-companies"),
 *                 @OA\Property(property="files", type="string",  example="ritual.jpg"),
 *                 @OA\Property(property="paths", type="string", description="The path to the ritual details page", example="https://cz19567.tw1.ru/ritual/Ритуал.jpg"),
 *                 @OA\Property(property="seo_title", type="string",  example="Ритуальная помощь"),
 *                 @OA\Property(property="seo_description", type="string", example="Бесплатная помощь в организации похорон в Казани"),
 *                 @OA\Property(property="seo_keywords", type="string", example="Ритуальные услуги,Организация похорон в Казани,Похороны,Памятники"),
 *                 @OA\Property(
 *                 property="coordinates",
 *                 type="object",
 *                 @OA\Property(property="latitude", type="string"),
 *                 @OA\Property(property="longitude", type="string")
 *                  )
 *              )
 *         )
 *     )
 * )
 */

    public function get_cemetery(){

        $cemeterys = Cemetery::all();

        $data = [];

        foreach ($cemeterys as $cemetery) {
            $coordinates = explode(',', $cemetery->coordinates);

            $data[] = [
            'id' => $cemetery -> id,
            'title' => $cemetery -> title,
            'description' => $cemetery -> description,
            'slug' => $cemetery -> slug,
            'files' => $cemetery -> files,
            'paths' => 'https://cz19567.tw1.ru/ritual/' .  $cemetery -> paths,
            'seo_title' => $cemetery -> seo_title,
            'seo_description' => $cemetery -> seo_description,
            'seo_keywords' => $cemetery -> seo_keywords,
            'coordinates' => [
                'latitude' => trim($coordinates[0]),
                'longitude' => trim($coordinates[1])
                ]
            ];
        }

        return response()->json($data);
    }
}
