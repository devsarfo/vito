<?php

namespace App\Http\Controllers\Settings;

use App\Actions\ServerProvider\CreateServerProvider;
use App\Facades\Toast;
use App\Helpers\HtmxResponse;
use App\Http\Controllers\Controller;
use App\Models\ServerProvider;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ServerProviderController extends Controller
{
    public function index(): View
    {
        return view('settings.server-providers.index', [
            'providers' => auth()->user()->serverProviders,
        ]);
    }

    public function connect(Request $request): HtmxResponse
    {
        app(CreateServerProvider::class)->create(
            $request->user(),
            $request->input()
        );

        Toast::success('Server provider connected.');

        return htmx()->redirect(route('server-providers'));
    }

    /**
     * @TODO Update servers using this provider
     */
    public function delete(int $id): RedirectResponse
    {
        $serverProvider = ServerProvider::query()->findOrFail($id);

        $serverProvider->delete();

        Toast::success('Server provider deleted.');

        return back();
    }
}
