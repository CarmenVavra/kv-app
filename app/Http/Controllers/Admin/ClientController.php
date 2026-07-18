<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BehaviorTopic;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientRequest;
use App\Http\Requests\Admin\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $topic = $request->query('thema');

        $clients = Client::query()
            ->when($topic, fn ($query) => $query->where('behavior_topic', $topic))
            ->when($request->query('suche'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('dog_name', 'like', "%{$search}%")
                        ->orWhere('owner_name', 'like', "%{$search}%");
                });
            })
            ->orderBy('dog_name')
            ->get();

        return view('admin.clients.index', [
            'clients' => $clients,
            'topics' => BehaviorTopic::cases(),
            'activeTopic' => $topic,
        ]);
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        Client::create($request->validated() + [
            'color' => collect(Client::AVATAR_PALETTE)->random(),
        ]);

        return redirect()->route('admin.clients.index');
    }

    public function show(Client $client): View
    {
        return view('admin.clients.show', ['client' => $client]);
    }

    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $client->update($request->validated());

        return redirect()->route('admin.clients.show', $client);
    }

    public function updateNotes(Request $request, Client $client): RedirectResponse
    {
        $request->validate(['dog_notes' => ['nullable', 'string']]);

        $client->update(['dog_notes' => $request->input('dog_notes')]);

        return redirect()->route('admin.clients.show', $client);
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return redirect()->route('admin.clients.index');
    }
}
