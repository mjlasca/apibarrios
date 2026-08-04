<?php

namespace App\Http\Controllers;

use App\Models\Propuesta;
use App\Services\ProposalService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProposalListController extends Controller
{
    public function __construct(
        private readonly ProposalService $proposals,
    ) {
    }

    public function index(Request $request): View
    {
        $today = now(ProposalService::LOCAL_TIMEZONE)->toDateString();

        $from = $this->validDate((string) $request->input('desde', $today), $today);
        $to = $this->validDate((string) $request->input('hasta', $today), $today);
        $keyword = trim((string) $request->input('q', ''));

        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        [$fromUtc] = $this->proposals->localDayRange($from);
        [, $toUtc] = $this->proposals->localDayRange($to);

        $query = Propuesta::query()
            ->with('cliente')
            ->where('codempresa', auth()->user()->codempresa ?: 'default')
            ->whereBetween('created_at', [$fromUtc, $toUtc]);

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('nombre', 'like', "%{$keyword}%")
                    ->orWhere('documento', 'like', "%{$keyword}%");
            });
        }

        $proposals = $query->orderByDesc('created_at')->get();

        return view('propuesta.lista', [
            'proposals' => $proposals,
            'from' => $from,
            'to' => $to,
            'keyword' => $keyword,
            'today' => $today,
            'todayRange' => $this->proposals->localDayRange($today),
        ]);
    }

    private function validDate(string $date, string $fallback): string
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) ? $date : $fallback;
    }
}
