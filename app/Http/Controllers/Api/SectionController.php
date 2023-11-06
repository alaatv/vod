<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Request;
use App\Models\Section;
use Exception;
use Illuminate\Http\JsonResponse;

class SectionController extends Controller
{
    public function index(): JsonResponse
    {
        $sections = Section::all();
        return response()->json(['sections' => $sections], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $section = Section::create($request->all());
        if ($section) {
            return response()->json(['message' => 'Section successfully created.'], 201);
        } else {
            return response()->json(['error' => 'Error creating section.'], 500);
        }
    }

    public function edit(Section $section): JsonResponse
    {
        return response()->json(['section' => $section], 200);
    }

    public function update(Request $request, Section $section): JsonResponse
    {
        $updateResult = $section->update($request->all());
        if ($updateResult) {
            return response()->json(['message' => 'Section successfully updated.'], 200);
        } else {
            return response()->json(['error' => 'Error updating section.'], 500);
        }
    }

    public function destroy(Section $section): JsonResponse
    {
        try {
            $section->delete();
            return response()->json(['message' => 'Section successfully deleted.'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error deleting section.'], 500);
        }
    }
}