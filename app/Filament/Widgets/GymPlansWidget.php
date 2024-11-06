<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Membership;

class GymPlansWidget extends Widget
{
    protected static string $view = 'filament.widgets.gym-plans-widget';

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        // Fetch all memberships and pass to the view
        $memberships = Membership::all();
        return compact('memberships');
    }
}
