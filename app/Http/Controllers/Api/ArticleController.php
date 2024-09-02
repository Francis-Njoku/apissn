<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticleAllResource;
use App\Models\Newsletter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class ArticleController extends Controller
{
    /**
     * Generate Slug.
     */
    private function generateSlug($name) {
        return Str::slug($name);
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

        if($media == "bytes")
        {
            $latestRow = Newsletter::where('mediaType', 'bytes')->orderBy('created_at', 'desc')->first();
        
            return new ArticleAllResource($latestRow);
        } elseif($media == "audio")
        {
            $latestRow = Newsletter::where('mediaType', 'audio')->orderBy('created_at', 'desc')->first();
        
            return new ArticleAllResource($latestRow);
        } elseif($media == "video")
        {
            $latestRow = Newsletter::where('mediaType', 'video')->orderBy('created_at', 'desc')->first();
        
            return new ArticleAllResource($latestRow);
        } else
        {
            $latestRow = Newsletter::where('mediaType', 'text')->orderBy('created_at', 'desc')->first();
        
            return new ArticleAllResource($latestRow);
        }

        
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = $request->get('s');
        $media = $request->get('m');
        $newsType = $request->get('n');

        if($newsType && $media )
        {
            //echo $filter;
            return ArticleResource::collection(
                Newsletter::where('news_type_id', $newsType)
                ->where('mediaType', $media)
                ->orderBy('created_at','desc')
                ->paginate(10));
        }
        elseif($newsType)
        {
            //echo $filter;
            return ArticleResource::collection(
                Newsletter::where('news_type_id', $newsType)
                ->orderBy('created_at','desc')
                ->paginate(10));
        }
        elseif($media)
        {
            //echo $filter;
            return ArticleResource::collection(
                Newsletter::where('mediaType', $media)
                ->orderBy('created_at','desc')
                ->paginate(10));
        }
        elseif($filter)
        {
            //echo $filter;
            return ArticleResource::collection(
                Newsletter::where('name', 'like', '%'.$filter.'%')
                ->orWhere('title', 'like', '%'.$filter.'%')
                ->orderBy('created_at','desc')
                ->paginate(10));
        }
        else{
            return ArticleResource::collection(Newsletter::orderBy('created_at','desc')
            ->paginate(10));
        }
    }

    public function indexByMediaType(Request $request)
    {
        $media = $request->get('m');
        if($media)
        {
            // Query the latest 10 posts by created_at date
            $posts = Newsletter::where('mediaType',$media)->orderBy('created_at', 'desc')->take(10)->get();

            // Wrap the result with the API resource
            return ArticleAllResource::collection($posts);
        }
        else{
            // Query the latest 10 posts by created_at date
            $posts = Newsletter::orderBy('created_at', 'desc')->take(10)->get();

            // Wrap the result with the API resource
            return ArticleAllResource::collection($posts);
        }
        
    }

    /**
     * Display a listing of the resource.
     */
    public function indexNoAuth(Request $request)
    {
        $filter = $request->get('s');
        $media = $request->get('m');
        $newsType = $request->get('n');

        if($newsType && $media )
        {
            //echo $filter;
            return ArticleAllResource::collection(
                Newsletter::where('news_type_id', $newsType)
                ->where('mediaType', $media)
                ->orderBy('created_at','desc')
                ->paginate(10));
        }
        elseif($newsType)
        {
            //echo $filter;
            return ArticleAllResource::collection(
                Newsletter::where('news_type_id', $newsType)
                ->orderBy('created_at','desc')
                ->paginate(10));
        }
        elseif($media)
        {
            //echo $filter;
            return ArticleAllResource::collection(
                Newsletter::where('mediaType', $media)
                ->orderBy('created_at','desc')
                ->paginate(10));
        }
        elseif($filter)
        {
            //echo $filter;
            return ArticleAllResource::collection(
                Newsletter::where('name', 'like', '%'.$filter.'%')
                ->orWhere('title', 'like', '%'.$filter.'%')
                ->orderBy('created_at','desc')
                ->paginate(10));
        }
        else{
            return ArticleAllResource::collection(Newsletter::orderBy('created_at','desc')
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
            'file' => 'nullable|file|mimetypes:image/jpeg,image/png,image/jpg,audio/mpeg,audio/x-wav,audio/mp3,video/avi,video/mpeg,video/quicktime,video/mp4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->hasFile('file')) {
            $media = $this->storeFile($request->file('file'), 'all');

            return response()->json([
                'status' => 'success',
                'message' => 'Data processed successfully',
            ], 200);
        }
        else
        {
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
            'body' => 'nullable|string|min:10|max:10000',
            'featuredImage' => 'nullable|file|mimetypes:image/jpeg,image/png,image/jpg',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Initialize an empty data array
        $data = [
            'news_type_id' => $request->input('news_type_id'),
            'name' => $request->input('name'),
            'title' => $request->input('title'),
            'news_date' => $request->input('news_date'),
            'body' => $request->input('body'),
            'status' => 'approved',
            'featuredImage' => $this->storeFileNoDirectory($request->file('featuredImage')),
        ];

        // Check for and handle each file type
        if ($request->hasFile('video')) {
            $data['media'] = $this->storeFile($request->file('video'), 'videos');
            $data['mediaType'] = 'video';
        } elseif ($request->hasFile('audio')) {
            $data['media'] = $this->storeFile($request->file('audio'), 'audios');
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
            'body' => 'nullable|string|min:10|max:10000',
            'featuredImage' => 'required|string',
            'media' => 'nullable|string',
            'status' => 'required|string',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Initialize an empty data array
        $data = [
            'news_type_id' => $request->input('news_type_id'),
            'name' => $request->input('name'),
            'title' => $request->input('title'),
            'news_date' => $request->input('news_date'),
            'body' => $request->input('body'),
            'status' => $request->input('status'),
            'mediaType' => $request->input('mediaType'),
            'media' => $request->input('media'),
            'featuredImage' => $request->input('featuredImage'),
        ];

        $media = Newsletter::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data processed successfully',
            'data' => $media
        ], 200);
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
        return $file->storeAs($directory, $filename, 'public');
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Retrieve the item or fail with a 404 error
        $item = Newsletter::findOrFail($id);

        // Return the item wrapped in an API resource
        return new ArticleResource($item);
    }

    /**
     * Display the specified article.
     */
    public function showSingleArticle(string $slug)
    {
        // Retrieve the item or fail with a 404 error
        $item = Newsletter::where('slug', $slug)->firstOrFail();

        // Return the item wrapped in an API resource
        return new ArticleResource($item);
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
            'mediaType' => 'nullable|string',
            'news_type_id' => 'required|string',
            'name' => 'required|string',
            'title' => 'required|string',
            'news_date' => 'required|date',
            'body' => 'nullable|string|min:10|max:10000',
            'featuredImage' => 'required|string',
            'media' => 'nullable|string',
            'status' => 'required|string',
        ]);

        // Update the resource with validated data
        $resource->update($validatedData);

        // Optionally, you can return a response
        return response()->json([
            'message' => 'Article updated successfully!',
            'data' => $resource
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function updateStatus(Request $request, $id)
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

    public function listFiles(Request $request)
    {
        // Define the folders you want to list files from
        $folders = ['public/all','public/audios', 'public/featured_image', 'public/videos'];

        $allFiles = [];
        $baseUrl = config('app.url'); // Base URL of your application

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
        $page = $request->input('page', 1);
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

