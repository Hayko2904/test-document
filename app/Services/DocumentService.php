<?php


namespace App\Services;


use App\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentService
{
    private $documentModel;

    /**
     * DocumentService constructor.
     * @param Document $document
     */
    public function __construct(Document $document)
    {
        $this->documentModel = $document;
    }

    /**
     * @return string
     */
    public function create(): string
    {
        $document = $this->documentModel::query()->create([
            "status" => "draft",
            "payload" => json_encode((Object)[])
        ]);

        return response()->json([
            "document" => $document
        ], 200);
    }

    /**
     * @param array $data
     * @param int $id
     * @return string
     */
    public function update(array $data, int $id): string
    {
        $document = $this->documentModel->find($id);

        if (!$document) {
            return response()->json([
                "message" => "Dosument not found"
            ], 404);
        }

        if ($document->status === $this->documentModel::PUBLISHED) {
            return response()->json([
                "message" => "Dosument status is PUBLISHED"
            ], 400);
        }
        $document->query()
            ->update([
                "payload" => json_encode($data)
            ]);

        return response()->json([
            "dosument" => $document
        ], 200);
    }

    /**
     * @param int $id
     * @return string
     */
    public function publish(int $id): string
    {
        $document = $this->documentModel->find($id);

        $document->query()->update([
            "status" => $this->documentModel::PUBLISHED
        ]);

        return response()->json([
            "document" => $document
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $page = $request->get("page");
        $perPage = $request->get("perPage");
        $total = $this->documentModel->all()->count();

        $result["document"] = DB::select(
            sprintf("select * from documents limit %u offset %u",
                $perPage,
                ($page - 1) * $perPage
            ));

        $result["pagination"] = [
            "page" => $page,
            "perPage" => $perPage,
            "total" => $total
        ];

        return response()->json([
            "documents" => $result
        ], 200);
    }
}
