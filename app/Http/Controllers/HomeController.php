<?php

namespace App\Http\Controllers;

use App\Enums\ConsultationAnnouncementCategories;
use App\Enums\ConsultationAnnouncementCategoriesPtBr;
use App\Models\ConsultationAnnouncement;
use DateInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Inertia\Inertia;
use Inertia\Response;
use Psr\SimpleCache\InvalidArgumentException;

class HomeController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     * @throws InvalidArgumentException
     */
    public function index(Request $request): Response
    {
        $search = $request->string('search');

        if ($search->isNotEmpty()) {
            $enumIndex = ConsultationAnnouncementCategoriesPtBr::from($search)->name;
            /** @var ConsultationAnnouncementCategories $enumUnit */
            $enumUnit = constant(ConsultationAnnouncementCategories::class . '::' . $enumIndex);
            $search = new Stringable($enumUnit->value);
        }
        $cacheKey = 'home' . ($search->isEmpty() ? '' : '.') . $search->snake();

        if (Cache::has($cacheKey)) {
            return Inertia::render('Home', [
                'announces' => Cache::get($cacheKey)
            ]);
        }
        $builder = ConsultationAnnouncement::with(['professional', 'appointmentTimes'])
            ->whereRelation('appointmentTimes', 'status', '=', 'available')
            ->orderBy('id', 'desc');

        if ($search->isNotEmpty()) {
            $builder->where('category', 'like', '%' . $search . '%');
        }

        $announces = $builder
            ->paginate(columns: ['id', 'category', 'professional_id']);

        Cache::tags('home')->set($cacheKey, $announces, DateInterval::createFromDateString('1 hour'));

        return Inertia::render('Home', compact('announces'));
    }

    public function announce(int $id, Request $request)
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|integer'
        ]);

        if (Cache::has('announce/' . $id)) {
            return Inertia::render('Announces/Announce', [
                'announce' => Cache::get('announce')
            ]);
        }

        $announce = ConsultationAnnouncement::with(['professional', 'appointmentTimes'])
            ->whereRelation('appointmentTimes', 'status', '=', 'available')
            ->find($id);

        if (empty($announce)) {
            return Redirect::route('home')->with('error', 'Anúncio não encontrado.');
        }

        return Inertia::render('Announces/Announce', compact('announce'));
    }

    public function search(Request $request)
    {
        $search = preg_quote($request->string('search'));

        $categories = ConsultationAnnouncementCategories::cases();
        $found = [];
        foreach ($categories as $category) {
            $name = $category->getName();
            if (mb_stripos(mb_strtolower($name), mb_strtolower($search)) === false) {
                continue;
            }

            $found[] = $name;
        }

        return Redirect::back()->with('home.search', $found);
    }
}
