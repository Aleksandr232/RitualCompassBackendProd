<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class PostBlogController extends Controller
{
     /**
 * @OA\Post(
 *     path="/api/blog",
 *     tags={"Админ панель"},
 *     summary="Добавления постов",
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

   public function post_blog(Request $request){
    $description = explode('.', $request->description);
        $description = array_map(function($item) {
            return trim($item) . '.';
        }, $description);
        $description = implode("\n", $description);

    $blog = new Blog([
        'title' => $request -> title,
        'description' =>$description,
        'seo_title' => $request -> seo_title,
        'seo_description' => $request -> seo_description,
        'seo_keywords' => $request -> seo_keywords
    ]);

    $blog->save();

    return response()->json(['success' => 'Новый пост добавлен']);

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
 *                 @OA\Property(property="seo_title", type="string",  example="Ритуальная помощь"),
 *                 @OA\Property(property="seo_description", type="string", example="Бесплатная помощь в организации похорон в Казани"),
 *                 @OA\Property(property="seo_keywords", type="string", example="Ритуальные услуги,Организация похорон в Казани,Похороны,Памятники"),
 *              )
 *         )
 *     )
 * )
 */

   public function get_blog(){
    $blog = Blog::all();
    $data = [];
    foreach ($blog as $about) {
        $description_sentences = preg_split('/[.!?]+/', $about->description, -1, PREG_SPLIT_NO_EMPTY);
        $data[] = [
        'id' => $about -> id,
        'title' => $about -> title,
        'description' => $description_sentences,
        'seo_title' => $about -> seo_title,
        'seo_description' => $about -> seo_description,
        'seo_keywords' => $about -> seo_keywords
        ];
    }
    return response()->json($data);
   }
}
