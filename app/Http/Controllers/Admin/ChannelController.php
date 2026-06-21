<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChannelRequest;
use App\Http\Requests\UpdateChannelRequest;
use App\Models\Channel;

class ChannelController extends Controller
{
    public function index()
    {
        $channels = Channel::orderBy('name')->paginate(15);

        return view('admin.channels.index', compact('channels'));
    }

    public function store(StoreChannelRequest $request)
    {
        Channel::create($request->validated());

        return redirect()->route('admin.channels.index')->with('success', 'Channel created successfully.');
    }

    public function update(UpdateChannelRequest $request, Channel $channel)
    {
        $channel->update($request->validated());

        return redirect()->route('admin.channels.index')->with('success', 'Channel updated successfully.');
    }

    public function destroy(Channel $channel)
    {
        if ($channel->leads()->exists() || $channel->campaigns()->exists()) {
            return back()->with('error', 'Cannot delete a channel that has campaigns or leads linked to it.');
        }

        $channel->delete();

        return redirect()->route('admin.channels.index')->with('success', 'Channel deleted successfully.');
    }
}