<?php

namespace App\Http\Controllers;

use App\Contracts\TranslationRepositoryInterface;
use App\Contracts\TranslationServiceInterface;
use App\Http\Requests\TranslationRequest;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="Translation API", version="1.0")
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class TranslationController extends Controller
{
    private $translationService;

    public function __construct(TranslationServiceInterface $translationService)
    {
        $this->translationService = $translationService;
    }

    /**
     * @OA\Get(
     *     path="/api/translations",
     *     tags={"Translations"},
     *     summary="Get all translations",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of translations"
     *     )
     * )
     */
    public function index()
    {
        return $this->translationService->allTranslations();
    }

    /**
     * @OA\Post(
     *     path="/api/translations",
     *     tags={"Translations"},
     *     summary="Create a new translation",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"key","locale","value","tag"},
     *             @OA\Property(property="key", type="string", example="greeting"),
     *             @OA\Property(property="locale", type="string", example="en"),
     *             @OA\Property(property="value", type="string", example="Hello"),
     *             @OA\Property(property="tag", type="string", example="web")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Translation created"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed or duplicate"
     *     )
     * )
     */
    public function store(TranslationRequest $request)
    {
        try {
            $key = $request->input('key');
            $locale = $request->input('locale');

            if ($this->translationService->findByKeyAndLocale($key, $locale))
                return response()->json(['status' => 1, 'message' => 'Translation with key and locale exits!'], 422);


            $created = $this->translationService->createTranslation([
                'key' => $request->input('key'),
                'locale' => $request->input('locale'),
                'value' => $request->input('tag'),
                'tag' => $request->input('tag')
            ]);

            if ($created)
                return response()->json(['status' => 1, 'message' => 'Translation Created!'], 201);
        } catch (\Exception $e) {

            return response()->json(['status' => 0, 'message' => 'Failed to create Translation!' . $e->getMessage()]);
        }

    }

    /**
     * @OA\Get(
     *     path="/api/translations/{id}",
     *     tags={"Translations"},
     *     summary="Get translation by ID",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Translation ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Single translation"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     )
     * )
     */
    public function show(int $id)
    {
        if ($translation = $this->translationService->findTranslation($id))
            return $translation;

        return response()->json(['status' => 1, 'message' => 'not found!'], 404);
    }

    /**
     * @OA\Put(
     *     path="/api/translations/{id}",
     *     tags={"Translations"},
     *     summary="Update an existing translation",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Translation ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="key", type="string", example="greeting"),
     *             @OA\Property(property="locale", type="string", example="en"),
     *             @OA\Property(property="value", type="string", example="Hi there"),
     *             @OA\Property(property="tag", type="string", example="web")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translation updated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     ),
     *   @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function update(TranslationRequest $request, int $id)
    {
        try {

            $updated = $this->translationService->updateTranslation($id,
                [
                    'key' => $request->input('key'),
                    'locale' => $request->input('locale'),
                    'value' => $request->input('tag'),
                    'tag' => $request->input('tag')
                ]);

            if ($updated)
                return response()->json(['status' => 1, 'message' => 'Translation updated!']);

            return response()->json(['status' => 0, 'message' => 'Not found!'], 404);

        } catch (\Exception $e) {
            return response()->json(
                ['status' => 0, 'message' => 'Failed to update Translation! ' . $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/translations/{id}",
     *     tags={"Translations"},
     *     summary="Delete a translation",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Translation ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     )
     * )
     */
    public function destroy(int $id)
    {
        if ($this->translationService->deleteTranslation($id))
            return response()->json(['status' => 1, 'message' => 'Deleted successfully']);

        return response()->json(['status' => 0, 'message' => 'Not found!'], 404);
    }

    /**
     * @OA\Get(
     *     path="/api/translations/search",
     *     tags={"Translations"},
     *     summary="Search translations by key, value, locale, or tag",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="key", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="value", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="locale", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="tag", in="query", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Matching translations"
     *     )
     * )
     */
    public function search(Request $request)
    {
        return response()->json(
            $this->translationService->searchTranslations($request->only('key', 'value', 'locale', 'tag'))
        );
    }
}
