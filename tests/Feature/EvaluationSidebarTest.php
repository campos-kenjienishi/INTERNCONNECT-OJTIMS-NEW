<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class EvaluationSidebarTest extends TestCase
{
    public function test_evaluation_shell_contains_completion_certificate_navigation_for_each_role(): void
    {
        session()->forget('loginId');

        $portals = [
            'student' => ['student.completion-certificate.index', 'Completion Certificate'],
            'professor' => ['professor.completion-certificates.index', 'COC Validation'],
            'coordinator' => ['coordinator.completion-certificates.index', 'COC Validation'],
        ];

        foreach ($portals as $role => [$routeName, $label]) {
            $html = Blade::render('<x-evaluation-shell role="'.$role.'">Evaluation</x-evaluation-shell>');

            $this->assertStringContainsString(route($routeName), $html);
            $this->assertStringContainsString($label, $html);
        }
    }
}
