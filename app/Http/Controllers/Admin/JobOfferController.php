<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobOffer;
use Illuminate\Http\Request;

class JobOfferController extends Controller
{
    use Uploads;

    public function index()
    {
        return JobOffer::orderBy('position')->get();
    }

    public function store(Request $request)
    {
        return $this->save($request, new JobOffer(['position' => (int) JobOffer::max('position') + 1]));
    }

    public function update(Request $request, JobOffer $job)
    {
        return $this->save($request, $job);
    }

    public function destroy(JobOffer $job)
    {
        $job->delete();

        return response()->noContent();
    }

    private function save(Request $request, JobOffer $job)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'contract' => ['required', 'string', 'max:60'],
            'location' => ['required', 'string', 'max:120'],
            'experience' => ['required', 'string', 'max:120'],
            'missions' => ['nullable'],
            'is_published' => ['nullable', 'boolean'],
        ]);
        $job->fill(collect($data)->except('missions')->all());
        $job->missions = $this->jsonField($request->input('missions'));
        $job->is_published = $request->boolean('is_published', true);
        $job->save();

        return $job;
    }
}
