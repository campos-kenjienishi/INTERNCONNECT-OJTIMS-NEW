<?php

namespace Tests\Unit;

use App\Services\ChatbotAiService;
use PHPUnit\Framework\TestCase;

class BudWorkflowGuidanceTest extends TestCase
{
    public function test_completion_guidance_precedes_generic_professor_matching(): void
    {
        foreach (['student', '0', 'professor', 'coordinator'] as $role) {
            $answer = (new ChatbotAiService())->reply('Is professor approval required for my COC?', $role);
            $this->assertStringContainsString('optional', $answer['reply']);
            $this->assertStringContainsString('required final validation', $answer['reply']);
            $this->assertStringContainsString('completion-certificat', $answer['actions'][0]['url']);
        }
    }

    public function test_evaluation_does_not_claim_to_compute_the_official_course_grade(): void
    {
        $answer = (new ChatbotAiService())->reply('Is the supervisor rating my final course grade?', 'student');
        $this->assertStringContainsString('do not automatically become', $answer['reply']);
        $this->assertSame('/student/evaluation', $answer['actions'][0]['url']);
    }

    public function test_personal_deadlines_are_not_inferred_from_unsupplied_records(): void
    {
        $answer = (new ChatbotAiService())->reply('What is my requirement due date?', 'student');
        $this->assertStringContainsString('no verified deadline record', $answer['reply']);
        $this->assertTrue($answer['escalate']);
        $this->assertSame('/student/class', $answer['actions'][0]['url']);
    }

    public function test_targeted_announcements_precede_generic_program_maintenance(): void
    {
        $answer = (new ChatbotAiService())->reply('Announce only to selected programs without email?', 'coordinator');
        $this->assertStringContainsString('Email notifications are optional', $answer['reply']);
        $this->assertStringContainsString('not every faculty account', $answer['reply']);
        $this->assertSame('/dashboard', $answer['actions'][0]['url']);
    }

    public function test_moa_renewal_has_history_guidance(): void
    {
        $answer = (new ChatbotAiService())->reply('Renew an expired agreement and view renewal history', '1');
        $this->assertStringContainsString('Renewal History', $answer['reply']);
        $this->assertStringContainsString('does not automatically renew or delete', $answer['reply']);
        $this->assertSame('/MOA', $answer['actions'][0]['url']);
    }

    public function test_administrative_refusals_remain_ahead_of_workflow_guidance(): void
    {
        foreach (['student', 'professor'] as $role) {
            $answer = (new ChatbotAiService())->reply('Use FLSS faculty sync for COC announcements', $role);
            $this->assertTrue($answer['escalate']);
            $this->assertStringContainsString('cannot answer', $answer['reply']);
            $this->assertNotContains('/dashboard', array_column($answer['actions'], 'url'));
        }
    }
}
