<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\VideoCallInitiated;

class VideoCallController extends Controller
{
    public function startCall(Request $request)
    {


        $recipientId = $request->input('recipient_id');

        // Broadcast event to notify the recipient
        broadcast(new VideoCallInitiated($recipientId));

        return response()->json(['status' => 'Call initiated']);
    }
}
