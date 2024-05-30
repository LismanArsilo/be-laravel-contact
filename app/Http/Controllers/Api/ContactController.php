<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    private int $page = 1;
    private int $limit = 20;
    private int $offset = 0;
    private string $keyword = "";
    private string $gender = "";

    public function getAllContact(Request $request)
    {
        try {
            Log::debug($request->query());
            $query = [
                "page" => $request->query('page', $this->page),
                "limit" => $request->query('length', $this->limit),
                "offset" => $request->query('start', $this->offset),
                "keyword" => $request->query('keyword', $this->keyword),
                "gender" => $request->query('gender', $this->gender),
            ];

            $contactQuery = Contact::query();

            if ($query['keyword']) {
                $contactQuery->where('name', 'like', '%' . $query['keyword'] . '%');
            }

            $contactQuery->when($query['gender'], function ($q) use ($query) {
                $q->where('gender', $query['gender']);
            });

            $contacts = $contactQuery->orderBy('id', 'desc')->paginate($query['limit']);

            return response()->json(['status' => true, 'message' => 'Get All Contact Successfully', 'contacts' => $contacts], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Server Error : ' . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getOneContact(Request $request, $id)
    {
        try {
            $contact = Contact::where('id', $id)->first();

            if (!$contact) {
                return response()->json(['status' => false, 'message' => 'Contact Not Found'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['status' => true, 'message' => 'Get One Contact Successfully', 'contact' => $contact], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Server Error : ' . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function createContact(Request $request)
    {
        try {
            Log::debug($request->all());
            $validate = Validator::make($request->all(), [
                'name' => 'required',
                'address' => 'required',
                'phone_number' => 'required|integer',
                'gender' => 'required',
            ]);

            if ($validate->fails()) {
                return response()->json(['status' => false, 'message' => $validate->errors()], Response::HTTP_BAD_REQUEST);
            }

            $validated = $validate->validated();

            $data = [
                'name' => $validated['name'],
                'address' => $validated['address'],
                'phone_number' => $validated['phone_number'],
                'gender' => $validated['gender'],
            ];

            $contact = Contact::create($data);

            return response()->json(['status' => true, 'message' => 'Create Contact Successfully', 'contact' => $contact], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Server Error : ' . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateContact(Request $request, $id)
    {
        try {
            $contact = Contact::where('id', $id)->first();

            if (!$contact) {
                return response()->json(['status' => false, 'message' => 'Contact Not Found'], Response::HTTP_NOT_FOUND);
            }

            $validate = Validator::make($request->all(), [
                'name' => 'required',
                'address' => 'required',
                'phone_number' => 'required',
                'gender' => 'required',
            ]);

            if ($validate->fails()) {
                return response()->json(['status' => false, 'message' => $validate->errors()], Response::HTTP_BAD_REQUEST);
            }

            $validated = $validate->validated();

            return response()->json(['status' => true, 'message' => 'Update Contact Successfully', 'contact' => $contact], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Server Error : ' . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteContact(Request $request, $id)
    {
        try {
            $contact = Contact::where('id', $id)->first();

            if (!$contact) {
                return response()->json(['status' => false, 'message' => 'Contact Not Found'], Response::HTTP_NOT_FOUND);
            }

            $contact->delete();

            return response()->json(['status' => true, 'message' => 'Delete Contact Successfully'], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Server Error : ' . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
