<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Http\Requests\ConferenceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ConferenceController extends Controller
{
    /**
     * Display a listing of the conferences.
     *
     * @param Request $request
     * @return View|RedirectResponse
     */
    private function validateSortingParameters(Request $request): array
    {
        $sortField = $request->get('sortField', 'date');
        $sortDirection = $request->get('sortDirection', 'asc');

        if (!in_array($sortField, ['date', 'title']) || !in_array($sortDirection, ['asc', 'desc'])) {
            throw new \InvalidArgumentException('Invalid sorting parameters.');
        }

        return [$sortField, $sortDirection];
    }
    private function fetchConferences(string $sortField, string $sortDirection)
    {
        return Conference::orderBy($sortField, $sortDirection)->paginate(10);
    }
    public function index(Request $request): View|RedirectResponse
    {
        Log::info('Fetching all conferences for display.');

        try {
            [$sortField, $sortDirection] = $this->validateSortingParameters($request);
            $conferences = $this->fetchConferences($sortField, $sortDirection);
            $totalConferences = $conferences->total();

            Log::info("Total conferences retrieved: {$totalConferences}");
            return view('index', compact('conferences', 'totalConferences', 'sortField', 'sortDirection'));
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('conferences.index')->withErrors($e->getMessage());
        }
    }

    /**
     * Show the form for creating a new conference.
     *
     * @return View
     */
    public function create(): View
    {
        Log::info('Loading the conference creation form.');

        return view('create', [
            'instructions' => 'Please ensure all required fields are filled correctly.',
        ]);
    }

    /**
     * Store a newly created conference in storage.
     *
     * @param ConferenceRequest $request
     * @return RedirectResponse
     */
    private function saveConference(array $data, ?Conference $conference = null): Conference
    {
        return $conference ? tap($conference)->update($data) : Conference::create($data);
    }

    private function logOperation(string $action, Conference $conference)
    {
        Log::info("Conference {$action} successfully with ID: {$conference->id}");
    }

    public function store(ConferenceRequest $request): RedirectResponse
    {
        try {
            $conference = $this->saveConference($request->validated());
            $this->logOperation('created', $conference);

            return redirect()->route('conferences.index')
                ->with('success', 'Conference created successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating conference:', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors('Failed to create conference.');
        }
    }


    /**
     * Show the form for editing the specified conference.
     *
     * @param Conference $conference
     * @return View
     */
    public function edit(Conference $conference): View
    {
        Log::info("Editing conference with ID: {$conference->id}");

        return view('edit', [
            'conference' => $conference,
            'lastEdited' => now(),
        ]);
    }

    /**
     * Update the specified conference in storage.
     *
     * @param ConferenceRequest $request
     * @param Conference $conference
     * @return RedirectResponse
     */
    public function update(ConferenceRequest $request, Conference $conference): RedirectResponse
    {
        Log::info("Updating conference with ID: {$conference->id}", $request->all());

        try {
            $conference->update($request->validated());
            Log::info("Conference updated successfully with ID: {$conference->id}");

            return redirect()->route('conferences.index')
                ->with('success', 'Conference updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating conference:', ['error' => $e->getMessage()]);

            return redirect()->back()->withErrors('Failed to update conference.');
        }
    }

    /**
     * Remove the specified conference from storage.
     *
     * @param Conference $conference
     * @return RedirectResponse
     */
    public function destroy(Conference $conference): RedirectResponse
    {
        Log::info("Deleting conference with ID: {$conference->id}");

        try {
            $conference->delete();
            Log::info("Conference deleted successfully with ID: {$conference->id}");

            return redirect()->route('conferences.index')
                ->with('success', 'Conference deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting conference:', ['error' => $e->getMessage()]);

            return redirect()->back()->withErrors('Failed to delete conference.');
        }
    }
}
