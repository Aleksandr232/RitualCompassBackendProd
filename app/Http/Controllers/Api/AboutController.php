<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\About;
use Illuminate\Http\Request;

class AboutController extends Controller
{
   /**
 * @OA\Post(
 *     path="/api/about",
 *     tags={"Админ панель"},
 *     summary="Добавления новых разделов в about",
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

   public function post_about(Request $request){

    $about = new About([
        'title' => $request -> title,
        'description' => $request -> description,
        'slug' => $request -> slug,
        'seo_title' => $request -> seo_title,
        'seo_description' => $request -> seo_description,
        'seo_keywords' => $request -> seo_keywords
    ]);

    $about->save();

    return response()->json(['success' => 'Новые описания добавлены']);

   }

     /**
 * @OA\Get(
 *     path="/api/about",
 *     tags={"Главная страница"},
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
 *                 @OA\Property(property="seo_title", type="string",  example="Ритуальная помощь"),
 *                 @OA\Property(property="seo_description", type="string", example="Бесплатная помощь в организации похорон в Казани"),
 *                 @OA\Property(property="seo_keywords", type="string", example="Ритуальные услуги,Организация похорон в Казани,Похороны,Памятники"),
 *
 *              )
 *         )
 *     )
 * )
 */

   public function get_about(){
    $abouts = About::all();
    $data = [];
    foreach ($abouts as $about) {
        $data[] = [
        'id' => $about -> id,
        'title' => $about -> title,
        'description' => $about -> description,
        'slug' => $about -> slug,
        'seo_title' => $about -> seo_title,
        'seo_description' => $about -> seo_description,
        'seo_keywords' => $about -> seo_keywords
        ];
    }
    return response()->json($data);
   }

   public function get_about_slug($slug)
    {
        $about = About::where('slug', $slug)->first();
        
        if ($about) {
            return response()->json([
                'id' => $about->id,
                'title' => $about->title,
                'description' => $about->description,
                'slug' => $about->slug,
                'seo_title' => $about->seo_title,
                'seo_description' => $about->seo_description,
                'seo_keywords' => $about->seo_keywords
            ]);
        } else {
            return response()->json(['error' => 'Информация о том, что не найдено'], 404);
        }
    }
}
