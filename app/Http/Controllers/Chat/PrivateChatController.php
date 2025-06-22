<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Chat\ChatMessages;
use App\Models\Chat\Chats;
use App\Models\Chat\ChatUsers;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

        $chats = Chats::with("users")
            ->whereHas("users", function (Builder $query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->offset($offset)
            ->limit($limit)
            ->get();

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

        $chat->users = [
            User::find($user->id),
        ];

        return response(
            [
                "chat" => $chat,
            ]
        );
    }

    public function getChatMessages(Request $request) {
        $user = Auth::user();

        $validation = $request->validate([
            "chat_id" => "required|exists:App\Models\Chat\Chats,id",
            "page" => "integer",
        ]);

        // get chat with checking user access to chat
        $chat = Chats::with("users")
            ->whereHas("users", function (Builder $query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->find($validation["chat_id"]);

        if (!$chat) {
            return response(
                [
                    "error" => [
                        "message" => "Chat not found",
                    ],
                ],
            );
        }

        // get messages
        $page = $validation["page"];
        if (empty($page) || $page < 1) {
            $page = 1;
        }
        $offset = ($page - 1) * self::$pageElementsCount;
        $limit = self::$pageElementsCount;

        $messages = ChatMessages::where([
            "chat_id" => $validation["chat_id"],
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

    public function addChatUser(Request $request) {
        $user = Auth::user();

        $validation = $request->validate([
            "chat_id" => "required|exists:App\Models\Chat\Chats,id",
            "name" => "required|exists:App\Models\User,name",
        ]);

        // get chat with checking user access to chat
        $chat = Chats::with("users")
            ->whereHas("users", function (Builder $query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->find($validation["chat_id"]);

        if (!$chat) {
            return response(
                [
                    "error" => [
                        "message" => "Chat not found",
                    ],
                ],
            );
        }

        $userToAdd = User::where("name", $validation["name"])->first();
        // check if user already added
        $chatUserToAdd = ChatUsers::where([
            "chat_id" => $chat->id,
            "user_id" => $userToAdd->id,
        ])
            ->first();

        if ($chatUserToAdd) {
            return response(
                [
                    "error" => [
                        "message" => "User already in chat",
                    ],
                ],
            );
        }

        $chatUserToAdd = new ChatUsers();
        $chatUserToAdd->chat_id = $chat->id;
        $chatUserToAdd->user_id = $userToAdd->id;
        $chatUserToAdd->save();

        $chat->users[] = $userToAdd;

        return response(
            [
                "chat" => $chat,
                "user" => $userToAdd,
            ]
        );
    }

    public function deleteChatUser(Request $request) {
        $user = Auth::user();

        $validation = $request->validate([
            "chat_id" => "required|exists:App\Models\Chat\Chats,id",
            "user_id" => "required|exists:App\Models\User,id",
        ]);

        // get chat with checking user access to chat
        $chat = Chats::with("users")
            ->whereHas("users", function (Builder $query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->find($validation["chat_id"]);

        if (!$chat) {
            return response(
                [
                    "error" => [
                        "message" => "Chat not found",
                    ],
                ],
            );
        }

        $userToDelete = User::find($validation["user_id"]);
        // check if user already added
        $chatUserToDelete = ChatUsers::where([
            "chat_id" => $chat->id,
            "user_id" => $userToDelete->id,
        ])
            ->first();

        if (!$chatUserToDelete) {
            return response(
                [
                    "error" => [
                        "message" => "User not present in chat",
                    ],
                ],
            );
        }

        $chatUserToDelete->delete();

        foreach ($chat->users as $key => $_user) {
            if ($_user->id == $chatUserToDelete->id) {
                unset($chat->users[$key]);
                break;
            }
        }

        return response(
            [
                "chat" => $chat,
                "user" => $userToDelete,
            ]
        );
    }

    public function test(Request $request) {
        $user = Auth::user();

        $user1_id = 1;
        $user2_id = 2;
    }
}
