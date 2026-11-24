<?php

namespace App\Http\Controllers;

use App\Application\UseCases\Doctor\GetDashboardSummaryUseCase;
use App\Domain\Entities\SystemRole;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly GetDashboardSummaryUseCase $getDashboardSummaryUseCase
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $branchId = null;

        // Multi-tenant automatic branch scoping for Admin Cabang
        if ($user && $user->role === SystemRole::ADMIN_CABANG) {
            $branchId = $user->branch_id;
        } elseif ($request->filled('branch')) {
            $branchId = (string) $request->query('branch');
        } elseif ($request->filled('scope') && $request->query('scope') !== 'all') {
            $branchId = (string) $request->query('scope');
        }

        $summary = $this->getDashboardSummaryUseCase->execute($branchId);

        return view('dashboard', [
            'summary' => $summary,
            'user' => $user,
        ]);
    }
}
