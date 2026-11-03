<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingPageContentTest extends TestCase
{
    public function test_landing_page_describes_current_document_and_completion_workflows(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSeeText('Certificate of Completion (COC) submission and validation')
            ->assertSeeText('Document Submission & Tracking')
            ->assertSeeText('Submit PDF OJT requirements, track approval statuses, and upload your Certificate of Completion for validation.')
            ->assertSeeText('PDF Requirement Submissions')
            ->assertSeeText('Requirement Status Tracking')
            ->assertSeeText('Certificate of Completion Validation')
            ->assertDontSeeText('PDF & Image Submissions');

        $this->assertDoesNotMatchRegularExpression('/automated certificate of completion issuance/i', $response->getContent());
    }

    public function test_landing_page_uses_the_requested_portal_labels_and_coordinator_copy(): void
    {
        $this->get('/')->assertOk()
            ->assertSeeText('Built for Students, Professors, and Coordinator')
            ->assertSeeText('Student Portal')
            ->assertSeeText('Professor Portal')
            ->assertSeeText('Coordinator Portal')
            ->assertSeeText('Oversee OJT operations by managing Memorandum of Agreement (MOA) records, monitoring advisees, and generating comprehensive system reports.')
            ->assertSeeText('Company MOA Tracking')
            ->assertSeeText('Bulk Advisee Auditing')
            ->assertSeeText('System Report Generation')
            ->assertDontSeeText('Professor Hub')
            ->assertDontSeeText('Coordinator Deck')
            ->assertDontSeeText('InternConnect brings OJT workflows into one place');
    }

    public function test_existing_portal_navigation_and_professor_features_are_preserved(): void
    {
        $this->get('/')->assertOk()
            ->assertSee(route('login.gateway'))
            ->assertSeeText('Launch Portal')
            ->assertSeeText('Transparent Evaluation')
            ->assertSeeText('Dynamic Scoring Rubrics')
            ->assertSeeText('Program Management');
    }

    public function test_bud_showcase_uses_the_updated_copy_and_supported_guidance_topics(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('MEET BUD &bull; YOUR AI OJT BUDDY', false)
            ->assertSeeText('A Little Guidance.')
            ->assertSeeText('A Smoother OJT Journey.')
            ->assertSeeText('your always-ready virtual AI buddy inside InternConnect: OJTIMS.')
            ->assertSeeText('Get instant answers on OJT requirements, find your next steps, and navigate your journey with ease.')
            ->assertSeeText('Guidance on requirements, notarized MOAs, certificates of completion, and supervisor evaluations.')
            ->assertSeeText('Guided walk-throughs for profile updates, evaluations, and portal features.')
            ->assertSeeText('Tailored support for Students, OJT Subject Professors, and OJT Coordinator.')
            ->assertSeeText('Available 24/7 in All Portals')
            ->assertDontSeeText('Clear answers on requirements, MOA, OJT hours, and clearance.');
    }

    public function test_bud_showcase_preserves_existing_interactions_and_language_support(): void
    {
        $this->get('/')->assertOk()
            ->assertSeeText('Ask in English, Tagalog, or Taglish. Bud understands.')
            ->assertSeeText('Try Bud in the Portal')
            ->assertSee('data-bud-toggle', false)
            ->assertSee('aria-pressed="false"', false)
            ->assertSee('bud_happy_wave.png')
            ->assertSee('bud_cheering.png')
            ->assertSee('js/pages/landing-page.js');
    }

    public function test_contact_section_uses_the_requested_support_copy_and_office_hours(): void
    {
        $this->get('/')->assertOk()
            ->assertSeeText('Need Help Getting Started?')
            ->assertSeeText('Reach out for assistance with document submissions, account access, or general OJT inquiries. Get quick support via Facebook Messenger or Email.')
            ->assertSeeText('Visit PUP Taguig Campus for in-person consultations, document submissions, and office inquiries.')
            ->assertSeeText('Connect via Facebook Messenger for quick inquiries and real-time guidance.')
            ->assertSeeText('Send an email for formal requests or concerns requiring a written record.')
            ->assertSeeText('Support team is active during official campus office hours on regular school days.')
            ->assertSee('Monday &ndash; Friday | 8:00 AM &ndash; 5:00 PM', false)
            ->assertSeeText('Reach Us Directly')
            ->assertSeeText('Send us a message on Facebook or via Email, and our team will get back to you as soon as we can.')
            ->assertSeeText('InternConnect: An On-the-Job Training (OJT) Information Management System');
    }

    public function test_contact_actions_keep_the_existing_map_messenger_and_email_destinations(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('https://maps.google.com/?q=PUP+Taguig+Campus')
            ->assertSee('https://www.facebook.com/profile.php?id=61593939354633')
            ->assertSee('mailto:internconnect.ojtims@gmail.com')
            ->assertSeeText('View Map')
            ->assertSeeText('Message Us')
            ->assertSeeText('Send Email');
    }
}
