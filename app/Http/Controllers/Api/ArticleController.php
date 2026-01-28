<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Newsletter;
use App\Jobs\SendNewPostEmail;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticleAllResource;
use App\Http\Requests\ArticleRequest;
use App\Http\Controllers\Controller;
use App\Helpers\ApiResponseHelper;
//use App\Jobs\SendNewPostEmail;
//use Illuminate\Support\Facades\DB;
//use App\Models\User;
//use Illuminate\Support\Facades\Mail;

class ArticleController extends Controller
{
    /**
     * Generate Slug.
     */
    private function generateSlug($name)
    {
        return Str::slug($name);
    }

    private function storeFile($file, $directory)
    {
        if (!$file) {
            return null;
        }

        // Get the original file name without the extension
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // Remove spaces from the original file name
        $fileNameWithoutSpaces = str_replace(' ', '_', $originalName);

        $dateStamp = date('Ymd_His');

        $filename = $fileNameWithoutSpaces . '_' . $dateStamp . '.' . $file->getClientOriginalExtension();
        $file->storeAs($directory, $filename, 'public');

        return ['directory' => $directory, 'filename' => $filename];
    }

    private function storeFileNoDirectory($file)
    {
        if (!$file) {
            return null;
        }

        // Get the original file name without the extension
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // Remove spaces from the original file name
        $fileNameWithoutSpaces = str_replace(' ', '_', $originalName);

        $dateStamp = date('Ymd_His');


        // Generate a unique name for the image
        $fileName = $fileNameWithoutSpaces . '_' . $dateStamp . '.' . $file->getClientOriginalExtension();

        // Save the image in the 'images' directory
        $file->storeAs('featured_image', $fileName, 'public');

        return $fileName;
    }

    public function newsletterGenerateSlug()
    {
        $rows = Newsletter::whereNull('slug')->orWhere('slug', '')->get();

        foreach ($rows as $row) {
            $row->slug = Str::slug($row->name); // Generate slug from name
            $row->save(); // Save the updated row
        }

        return response()->json([
            'status' => 'Successful',
            'message' => 'Successfully updated slug',
        ], 200);
    }

    /**
     * Display a latest.
     */
    public function getLatest(Request $request)
    {
        $media = $request->get('m');

        if ($media == "bytes") {
            $latestRow = Newsletter::where('mediaType', 'bytes')->orderBy('news_date', 'desc')->first();

            return new ArticleAllResource($latestRow);
        } elseif ($media == "audio") {
            $latestRow = Newsletter::where('mediaType', 'audio')->orderBy('news_date', 'desc')->first();

            return new ArticleAllResource($latestRow);
        } elseif ($media == "video") {
            $latestRow = Newsletter::where('mediaType', 'video')->orderBy('news_date', 'desc')->first();

            return new ArticleAllResource($latestRow);
        } else {
            $latestRow = Newsletter::where('mediaType', 'text')->orderBy('news_date', 'desc')->first();

            return new ArticleAllResource($latestRow);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter   = $request->get('s');
        $media    = $request->get('m');
        $newsType = $request->get('n');

        if ($newsType && $media) {
            //echo $filter;
            return ArticleResource::collection(
                Newsletter::where('news_type_id', $newsType)
                    ->where('mediaType', $media)
                    ->orderBy('news_date', 'desc')
                    ->paginate(10)
            );
        } elseif ($newsType) {
            //echo $filter;
            return ArticleResource::collection(
                Newsletter::where('news_type_id', $newsType)
                    ->orderBy('news_date', 'desc')
                    ->paginate(10)
            );
        } elseif ($media) {
            //echo $filter;
            return ArticleResource::collection(
                Newsletter::where('mediaType', $media)
                    ->orderBy('news_date', 'desc')
                    ->paginate(10)
            );
        } elseif ($filter) {
            //echo $filter;
            return ArticleResource::collection(
                Newsletter::where('name', 'like', '%' . $filter . '%')
                    ->orWhere('title', 'like', '%' . $filter . '%')
                    ->orderBy('news_date', 'desc')
                    ->paginate(10)
            );
        } else {
            return ArticleResource::collection(Newsletter::orderBy('news_date', 'desc')
                ->paginate(10));
        }
    }
    /**
     * Display sample articles for unsubscribed users.
     * This doesn't require database modifications and returns 8 featured articles.
     */
    public function featuredArticle(Request $request)
    {
        $media = $request->get('m');

        // Start with a base query for approved and featured articles
        $baseQuery = Newsletter::where('status', 'approved')
            ->where('featured', true)
            ->orderBy('news_date', 'desc');

        // Apply media type filter if provided
        if ($media) {
            $baseQuery->where('mediaType', $media);
        } else {
            $baseQuery->where('mediaType', '!=', 'video');
        }

        // Get 6 latest approved featured articles
        $sampleArticles = $baseQuery->take(6)->get();

        if ($sampleArticles->isEmpty()) {
            return ApiResponseHelper::notFound('No sample articles available', 'NO_SAMPLE_ARTICLES');
        }

        // Return the articles as a collection
        return ArticleAllResource::collection($sampleArticles);
    }


    public function indexByMediaType(Request $request)
    {
        $media = $request->get('m');
        if ($media) {
            // Query the latest 10 posts by news_date date
            $posts = Newsletter::where('mediaType', $media)->orderBy('news_date', 'desc')->take(10)->get();

            // Wrap the result with the API resource
            return ArticleAllResource::collection($posts);
        } else {
            // Query the latest 10 posts by news_date date
            $posts = Newsletter::orderBy('news_date', 'desc')->take(10)->get();

            // Wrap the result with the API resource
            return ArticleAllResource::collection($posts);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function indexNoAuth(Request $request)
    {
        $filter   = $request->get('s');
        $media    = $request->get('m');
        $newsType = $request->get('n');

        if ($newsType && $media) {
            //echo $filter;
            return ArticleAllResource::collection(
                Newsletter::where('news_type_id', $newsType)
                    ->where('mediaType', $media)
                    ->orderBy('news_date', 'desc')
                    ->paginate(10)
            );
        } elseif ($newsType) {
            //echo $filter;
            return ArticleAllResource::collection(
                Newsletter::where('news_type_id', $newsType)
                    ->orderBy('news_date', 'desc')
                    ->paginate(10)
            );
        } elseif ($media) {
            //echo $filter;
            return ArticleAllResource::collection(
                Newsletter::where('mediaType', $media)
                    ->orderBy('news_date', 'desc')
                    ->paginate(10)
            );
        } elseif ($filter) {
            //echo $filter;
            return ArticleAllResource::collection(
                Newsletter::where('name', 'like', '%' . $filter . '%')
                    ->orWhere('title', 'like', '%' . $filter . '%')
                    ->orderBy('news_date', 'desc')
                    ->paginate(10)
            );
        } else {
            return ArticleAllResource::collection(Newsletter::orderBy('news_date', 'desc')
                ->paginate(10));
        }
    }

    /**
     * Upload a newly created file in storage.
     */
    public function uploadFile(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'folder' => 'nullable|string',
            'file' => 'nullable|file|mimetypes:image/jpeg,image/png,image/jpg,audio/mpeg,audio/x-wav,audio/mp3,video/avi,video/mpeg,video/quicktime,video/mp4',
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::validationError($validator->errors()->toArray());
        }

        if ($request->hasFile(key: 'file')) {

            $directory = $request->folder
                ? $request->folder
                : 'all';

            $media = $this->storeFile($request->file('file'), $directory);

            return response()->json([
                'status' => 'success',
                'message' => 'Data processed successfully',
                'data' => $media
            ], 200);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Data not processed successfully',
            ], 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'text' => 'nullable|string',
            'news_type_id' => 'required|string',
            'video' => 'nullable|file|mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4',
            'audio' => 'nullable|file|mimetypes:audio/mpeg,audio/x-wav,audio/mp3',
            'image' => 'nullable|file|mimetypes:image/jpeg,image/png,image/jpg',
            'name' => 'required|string',
            'title' => 'required|string',
            'news_date' => 'required|date',
            'bytes' => 'nullable|string',
            'body' => 'nullable|string|min:10|max:100000',
            'featuredImage' => 'nullable|file|mimetypes:image/jpeg,image/png,image/jpg',
            'featured' => 'nullable|boolean',
            'author_id' => 'required|integer',

        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::validationError($validator->errors()->toArray());
        }

        // Initialize an empty data array
        $data = [
            'news_type_id' => $request->input('news_type_id'),
            'name' => $request->input('name'),
            'title' => $request->input('title'),
            'news_date' => $request->input('news_date'),
            'body' => $request->input('body'),
            'status' => 'approved',
            'featured' => $request->input('featured', false),
            'featuredImage' => $this->storeFileNoDirectory($request->file('featuredImage')),
            'author_id' => $request->input('author_id'),
        ];

        // Check for and handle each file type
        if ($request->hasFile('video')) {
            $data['media']     = $this->storeFile($request->file('video'), 'videos');
            $data['mediaType'] = 'video';
        } elseif ($request->hasFile('audio')) {
            $data['media']     = $this->storeFile($request->file('audio'), 'audios');
            $data['mediaType'] = 'audio';
        } elseif ($request->input('text')) {
            $data['mediaType'] = 'text';
        } elseif ($request->input('bytes')) {
            $data['mediaType'] = 'bytes';
        }

        // Handle file uploads
        //$videoPath = $this->storeFile($request->file('video'), 'videos');
        //$audioPath = $this->storeFile($request->file('audio'), 'audios');
        //$imagePath = $this->storeFile($request->file('image'), 'images');

        // Store validated data in the database

        // Store validated data in the database
        $media = Newsletter::create($data);


        // Get unique user emails from the payment table by joining with the users table
        // Dispatch the email job to notify users
        SendNewPostEmail::dispatch($media->title, $media->body, $media->slug, $media->mediaType);


        return response()->json([
            'status' => 'success',
            'message' => 'Data processed successfully',
            'data' => $media
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeArticle(Request $request)
    {

        // Validate the request
        $validator = Validator::make($request->all(), [
            'mediaType' => 'nullable|string',
            'news_type_id' => 'required|string',
            'name' => 'required|string',
            'title' => 'required|string',
            'news_date' => 'required|date',
            'body' => 'nullable|string|min:10|max:100000',
            'featuredImage' => 'required|string',
            'featured' => 'nullable|boolean',
            'media' => 'nullable|string',
            'mediaSrc' => 'nullable|string',
            'status' => 'required|string',
            'tags' => 'nullable|string',
            'author_id' => 'required|integer',

        ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Validation failed',
        //         'errors' => $validator->errors()
        //     ], 422);
        // }

        // Initialize an empty data array
        // Convert tags to proper JSON format if present
        $tags      = $request->input('tags');
        $tagsArray = $tags ? explode(',', $tags) : [];

        $data = [
            'news_type_id' => $request->input('news_type_id'),
            'name' => $request->input('name'),
            'title' => $request->input('title'),
            'news_date' => $request->input('news_date'),
            'body' => $request->input('body'),
            'status' => $request->input('status'),
            'featured' => $request->input('featured', false),
            'tags' => json_encode($tagsArray),
            'mediaType' => $request->input('mediaType'),
            'media' => $request->input('media'),
            'mediaSrc' => $request->input('mediaSrc'),
            'featuredImage' => $request->input('featuredImage'),
            'author_id' => $request->input('author_id'),
        ];


        $media = Newsletter::create($data);

        // Dispatch the email job to notify users
        SendNewPostEmail::dispatch($media->title, $media->body, $media->slug, $media->mediaType);

        return response()->json([
            'status' => 'success',
            'message' => 'Data processed successfully',
            'data' => $media
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Retrieve the item with comments and fail with a 404 error
        $item = Newsletter::with([
            'comments' => function ($query) {
                $query->approved()->with('user');
            }
        ])->withCount('comments')->findOrFail($id);

        // Return the item wrapped in an API resource
        return new ArticleResource($item);
    }

    /**
     * Display the specified article.
     */
    public function showSingleArticle(string $slug)
    {
        // Retrieve the item with comments and fail with a 404 error
        $item = Newsletter::with([
            'comments' => function ($query) {
                $query->approved()->with('user');
            }
        ])->withCount('comments')->where('slug', $slug)->firstOrFail();

        // Return the item wrapped in an API resource
        return new ArticleResource($item);
    }

    /**
     * Search articles by title, content or tags
     */
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (empty($query)) {
            return ApiResponseHelper::error('Search query is required', 'SEARCH_QUERY_REQUIRED', null, 400);
        }

        $limit    = $request->input('limit', 10);
        $articles = Newsletter::where('title', 'like', "%{$query}%")
            ->orWhere('body', 'like', "%{$query}%")
            ->orWhere('tags', 'like', '%"' . $query . '"%')
            ->limit($limit)
            ->get();

        // Return empty array if no results found
        if ($articles->isEmpty()) {
            return response()->json([]);
        }

        return response()->json($articles->map(function ($article) {
            return [
                'id' => $article->id,
                'slug' => $article->slug,
                'title' => $article->title,
                'mediaType' => $article->mediaType
            ];
        }));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug)
    {
        // Find the resource by slug
        $resource = Newsletter::where('slug', $slug)->firstOrFail();

        // Validate the request data
        $validatedData = $request->validate([
            'title' => 'nullable|string',
            'news_date' => 'nullable|date',
            'body' => 'nullable|string|min:10|max:100000',
            'featuredImage' => 'nullable|string',
            'featured' => 'nullable|boolean',
            'media' => 'nullable|string',
            'status' => 'nullable|string',
            'tags' => 'nullable|string',
        ]);




        // Initialize an empty data array
        // Convert tags to proper JSON format if present
        $tags      = $request->input('tags');
        $tagsArray = $tags ? explode(',', $tags) : [];

        $data = [
            'title' => $request->input('title'),
            'news_date' => $request->input('news_date'),
            'body' => $request->input('body'),
            'status' => $request->input('status'),
            'featured' => $request->input('featured'),
            'tags' => json_encode($tagsArray),
            'media' => $request->input('media'),
            'featuredImage' => $request->input('featuredImage'),
        ];

        // Update the resource with validated data
        $resource->update($data);

        // Optionally, you can return a response
        return response()->json([
            'message' => 'Article updated successfully!',
            'data' => $resource
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function updateStatus(Request $request, $slug)
    {
        // Validate the request
        $request->validate([
            'status' => 'required|string|max:255',
        ]);

        // Find the item by ID
        $item = Newsletter::where('slug', $slug)->firstOrFail();;

        // Update the specific column
        $item->update(['status' => $request->status]);

        // Return a success response
        return response()->json(['message' => 'Status updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * List articles with pagination and sorting
     */
    public function listArticles(Request $request)
    {
        // Validate request parameters
        $validated = $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'sort_by' => 'sometimes|in:title,news_date,created_at',
            'sort_order' => 'sometimes|in:asc,desc'
        ]);

        // Set defaults
        $page      = $validated['page'] ?? 1;
        $perPage   = $validated['per_page'] ?? 10;
        $sortBy    = $validated['sort_by'] ?? 'news_date';
        $sortOrder = $validated['sort_order'] ?? 'desc';

        $articles = Newsletter::query()
            ->orderBy($sortBy, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json($articles->map(function ($article) {
            return [
                'id' => $article->id,
                'slug' => $article->slug,
                'title' => $article->title,
                'mediaType' => $article->mediaType,
                'news_date' => $article->news_date,
                'status' => $article->status,
                'featured' => $article->featured
            ];
        }));
    }

    public function listFiles(Request $request)
    {
        // Define the folders you want to list files from
        $folders = ['public/all', 'public/audios', 'public/featured_image', 'public/videos'];

        $allFiles = [];
        $baseUrl  = config('app.url'); // Base URL of your application

        // Loop through each folder and get the files
        foreach ($folders as $folder) {
            $files = Storage::files($folder);

            // Add each file with its media URL
            foreach ($files as $file) {
                $allFiles[] = [
                    'file_name' => str_replace('public/', '', $file),
                    'media_url' => $baseUrl . Storage::url($file)
                ];
            }
        }

        // Set the current page for pagination
        $page    = $request->input('page', 1);
        $perPage = 10; // Number of files per page

        // Slice the files array based on the pagination
        $filesForCurrentPage = array_slice($allFiles, ($page - 1) * $perPage, $perPage);

        // Create a paginator instance
        $paginator = new LengthAwarePaginator($filesForCurrentPage, count($allFiles), $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return response()->json($paginator);
    }

    public function updateImagePaths()
    {
        // Retrieve all rows where image_path is not null
        $posts = Newsletter::whereNotNull('featuredImage')->get();

        // Loop through each post
        foreach ($posts as $post) {
            // Append "featured_image/" to the current image_path
            $newPath = 'featured_image/' . $post->featuredImage;

            // Update the image_path in the database
            Newsletter::where('id', $post->id)
                ->update(['featuredImage' => $newPath]);
        }

        return "Image paths updated successfully!";
    }
}
