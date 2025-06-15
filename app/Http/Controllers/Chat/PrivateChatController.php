<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Chat\ChatMessages;
use App\Models\Chat\Chats;
use App\Models\Chat\ChatUsers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrivateChatController extends Controller {
    public function index(Request $request) {
        return view("chat.private");
    }

    private static $pageElementsCount = 10;
    public function getChatsData(Request $request) {
        $validation = [
            "page" => "integer",
        ];

        $page = intval($validation["page"]);
        if (empty($page) || $page < 1) {
            $page = 1;
        }
        $offset = ($page - 1) * self::$pageElementsCount;
        $limit = self::$pageElementsCount;

        $user = Auth::user();

        $chats = Chats::whereHas("users", function (Builder $query) use ($user) {
            $query->where('user_id', $user->id);
        })->offset($offset)->limit($limit)->get();

        return response(
            [
                // "user" => $user,
                // "request" => $request,
                "page" => $page,
                // "offset" => $offset,
                // "limit" => $limit,
                "chats" => $chats,
            ]
        );
    }

    public function createChat(Request $request) {
        $user = Auth::user();

        $validation = $request->validate([
            "title" => "string|required|unique:chats"
        ]);

        $chat = Chats::create($validation);

        // $chatUsers = ChatUsers::create([
        //     "chat_id" => $chat->id,
        //     "user_id" => $user->id,
        // ]);
        $chatUser = new ChatUsers;
        $chatUser->chat_id = $chat->id;
        $chatUser->user_id = $user->id;
        $chatUser->save();

        return response(
            [
                "chat" => $chat,
            ]
        );
    }

    public function getChatMessages(Request $request) {
        $user = Auth::user();

        $validation = $request->validate([
            "chat_id" => "id|required",
            "page" => "integer",
        ]);

        // check user chat access
        $chatUser = ChatUsers::where([
            "chat_id" => $validation["chat_id"],
            "user_id" => $user->id,
        ]);

        if (empty($chatUser)) {
            return response(
                [
                    "error" => [
                        "message" => "Chat for user not found"
                    ],
                ],
                400,
            );
        }

        $chat = Chats::find($chatUser->chat_id)->with("users");

        // get messages
        $page = $validation["page"];
        if (empty($page) || $page < 1) {
            $page = 1;
        }
        $offset = ($page - 1) * self::$pageElementsCount;
        $limit = self::$pageElementsCount;

        $messages = ChatMessages::where([
            "chat_id" => $chatUser->chat_id,
            "user_id" => $user->id,
        ])
            ->orderBy("created_at", "desc")
            ->offset($offset)
            ->limit($limit)
            ->get();

        $users = $chat->users;

        return response(
            [
                "messages" => $messages,
                "users" => $users,
            ]
        );
    }

    public function test(Request $request) {
        $user = Auth::user();

        $user1_id = 1;
        $user2_id = 2;
    }
}
