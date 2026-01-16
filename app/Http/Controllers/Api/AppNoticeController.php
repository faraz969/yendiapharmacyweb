<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotice;
use Illuminate\Http\Request;

class AppNoticeController extends Controller
{
    /**
     * Get active app notices
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notices = AppNotice::active()->get();

        return response()->json([
            'success' => true,
            'data' => $notices->map(function ($notice) {
                return [
                    'id' => $notice->id,
                    'title' => $notice->title,
                    'content' => $notice->content,
                    'image' => $notice->image_url,
                    'priority' => $notice->priority,
                ];
            }),
        ]);
    }
}
