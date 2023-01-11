<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Services\ActivitiesService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateActivityRequest;
use Carbon\Carbon;

class ActivityController extends BaseController
{
    public $activityService;
    public function __construct()
    {
        $this->activityService = new ActivitiesService();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::latest()->paginate(20);
        return $this->sendResponse($users, 'success');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateActivityRequest $request)
    {
        $dayActivities = Activity::where('date', $request->date)->count();
        if ($dayActivities >= 4) {
            return $this->sendError('', 'Maximum 4 activities can be added per day');
        }
        // upload the image
        $directory = 'uploads/images/' . date('Y') . '/' . date('m');
        $original_name = $request->image->getClientOriginalName();
        $request->image->move(public_path($directory), $original_name);
        // prepare data to store for activity creation
        $dataToStore = [
            'title' => $request->title,
            'description' => $request->description,
            'image' => $directory . '/' . $original_name,
            'date' => $request->date,
        ];
        // create a new activity
        if ($request->type === 'global') {
            $activity = $this->activityService->create($dataToStore);
            $userId = User::where('role', 'user')->pluck('id');
            $activity->users()->attach($userId, ['created_at' => now()]);
            $succ = $activity;
        } elseif ($request->type === 'user') {
            $dataToStore['assign'] = $request->userId;
            $succ = $this->activityService->create($dataToStore);
        } else {
            return $this->sendError('', 'Invalid type ' . $request->type);
        }
        return $this->sendResponse($succ, 'success');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function show(Activity $activity)
    {
        return $this->sendResponse($activity, 'success');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function edit(Activity $activity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Activity $activity)
    {
        $file_url = null;
        if ($request->hasFile('image')) {
            $directory = 'uploads/images/' . date('Y') . '/' . date('m');
            $original_name = $request->image->getClientOriginalName();
            $request->image->move(public_path($directory), $original_name);
            $file_url = $directory . '/' . $original_name;
        }

        $dataToStore = [
            'title' => $request->title,
            'description' => $request->description,
            'image' => $file_url ?? $request->image,
            'date' => $request->date,
        ];

        if ($request->type === 'user') {
            $dataToStore['assign'] = $request->userId;
            $updated = $this->activityService->update($activity->id, $dataToStore);
            return $this->sendResponse($updated, 'success');
        } else if ($request->type === 'global') {
            // $activity->users()->updateExistingPivot($activity->id, $dataToStore);
            $updated = $this->activityService->update($activity->id, $dataToStore);
            return $this->sendResponse($updated, 'success');
        } else {
            return $this->sendError('', 'Invalid type ' . $request->type);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Activity $activity, Request $request)
    {
        if ($activity->type === 'global') {
            $activity->users()->deleteExistingPivot($activity->id);
            $deleted = $this->activityService->delete($activity->id);
            return $this->sendResponse($deleted, 'success');
        } else {
            $deleted = $this->activityService->delete($activity->id);
            return $this->sendResponse($deleted, 'success');
        } 
        
    }

    public function overTime(Request $request)
    {
        $data = Activity::select('*')->where('assign', auth()->id())
            ->where('date', '>=', $request->start_date)
            ->where('date', '<=', $request->end_date)
            ->get();
        return $this->sendResponse($data, 'success');
    }
}
