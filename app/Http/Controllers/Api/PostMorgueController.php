<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Morgue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostMorgueController extends Controller
{
     /**
 * @OA\Post(
 *     path="/api/morgue",
 *     tags={"Админ панель"},
 *     summary="Добавления морга в админ панели",
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
public function post_morgue(Request $request){
        $description = explode('.', $request->description);
        $description = array_map(function($item) {
            return trim($item) . '.';
        }, $description);
        $description = implode("\n", $description);

    $morgue = new Morgue([
        'title' => $request -> title,
        'description' =>$description,
        'slug' => $request -> slug,
        'seo_title' => $request -> seo_title,
        'seo_description' => $request -> seo_description,
        'seo_keywords' => $request -> seo_keywords,
        'coordinates' => is_array($request->coordinates) ? implode(',', $request->coordinates) : $request->coordinates
    ]);

    if ($request->hasFile('files')) {
        $files = $request->file('files');
        $paths = Storage::disk('ritual')->putFile('morgue', $files);
        $morgue->files = $files->getClientOriginalName();
        $morgue->paths = $paths;
        $morgue->save();
    }

    $morgue->save();

    return response()->json(['success' => 'Морг добавлен']);
}

 /**
* @OA\Get(
*     path="/api/morgue",
*     tags={"Главная страница"},
*     summary="Вывод моргов на страницу моргов",
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
*                 @OA\Property(property="latitude", type="float"),
*                 @OA\Property(property="longitude", type="float")
*                  )
*              )
*         )
*     )
* )
*/

public function get_morgue(){

    $morgues = Morgue::all();

    $data = [];

    foreach ($morgues as $morgue) {
        $coordinates = explode(',', $morgue->coordinates);
        $description_sentences = preg_split('/[.!?]+/', $morgue->description, -1, PREG_SPLIT_NO_EMPTY);

        $data[] = [
        'id' => $morgue -> id,
        'title' => $morgue -> title,
        'description' => $description_sentences,
        'slug' => $morgue -> slug,
        'files' => $morgue -> files,
        'paths' => 'https://cz19567.tw1.ru/ritual/' .  $morgue -> paths,
        'seo_title' => $morgue -> seo_title,
        'seo_description' => $morgue -> seo_description,
        'seo_keywords' => $morgue -> seo_keywords,
        'coordinates' => [
            'latitude' => trim($coordinates[0]),
            'longitude' => trim($coordinates[1])
            ]
        ];
    }

    return response()->json($data);
}

public function get_morgue_slug($slug)
{
    $morgue = Morgue::where('slug', $slug)->first();

    if (!$morgue) {
        return response()->json(['error' => 'Morgue not found'], 404);
    }

    $coordinates = explode(',', $morgue->coordinates);
    $description_sentences = preg_split('/[.!?]+/', $morgue->description, -1, PREG_SPLIT_NO_EMPTY);

    $data = [
        'id' => $morgue->id,
        'title' => $morgue->title,
        'description' => $description_sentences,
        'slug' => $morgue->slug,
        'files' => $morgue->files,
        'paths' => 'https://cz19567.tw1.ru/ritual/' . $morgue->paths,
        'seo_title' => $morgue->seo_title,
        'seo_description' => $morgue->seo_description,
        'seo_keywords' => $morgue->seo_keywords,
        'coordinates' => [
            'latitude' => trim($coordinates[0]),
            'longitude' => trim($coordinates[1])
        ]
    ];

    return response()->json($data);
}
}
