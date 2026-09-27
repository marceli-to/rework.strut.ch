<?php

namespace App\Actions\Team;

use App\Models\TeamMember;

class ReorderAction
{
    public function execute(array $items): void
    {
        foreach ($items as $item) {
            TeamMember::where('uuid', $item['uuid'])->update(['sort_order' => $item['sort_order']]);
        }
    }
}
