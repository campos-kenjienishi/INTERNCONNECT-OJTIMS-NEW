const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

const view = fs.readFileSync('resources/views/professor/evaluation.blade.php', 'utf8');
const source = fs.readFileSync('public/js/professor/evaluation.js', 'utf8');

test('evaluation insight reuses Bud styling and retains its existing controls', () => {
    for (const className of ['bud-ai-insight-card', 'bud-ai-header', 'bud-ai-avatar', 'bud-ai-summary-card', 'bud-pillars-grid', 'bud-pillar-list']) {
        assert.ok(view.includes(className));
    }
    assert.ok(view.includes('Bud AI Intelligence'));
    assert.ok(!view.includes('AI Evaluation Insight</h2>'));
    assert.ok(view.includes('id="generateEvaluationInsightBtn"'));
    assert.ok(view.includes('id="evaluationAiStatus" role="status"'));
});

for (const [provider, label] of [['gemini', 'Gemini AI'], ['openai', 'OpenAI'], ['fallback', 'Internal Insight']]) {
    test(`evaluation insight identifies ${provider} results and safely renders text`, () => {
        const elements = new Map();
        function element(id) {
            if (!elements.has(id)) {
                elements.set(id, { style: {}, hidden: true, textContent: '', innerHTML: '', children: [],
                    addEventListener() {}, appendChild(child) { this.children.push(child); } });
            }
            return elements.get(id);
        }
        const document = {
            getElementById(id) { return id === 'classStatusTable' || id === 'ratingQuestionList' ? null : element(id); },
            createElement() { return { textContent: '' }; }
        };
        const result = { source: provider, summary: '<script>test</script>', key_findings: ['<b>Finding</b>'] };
        const instrumented = source.replace('            if (generateInsightBtn) {', '            renderEvaluationInsight(result);\n            if (generateInsightBtn) {');
        vm.runInNewContext(instrumented, { window: {}, document, result });
        assert.equal(element('evaluationAiSource').textContent, label);
        assert.equal(element('evaluationAiSourceWrap').hidden, false);
        assert.equal(element('evaluationAiSummary').textContent, result.summary);
        assert.equal(element('evaluationAiFindings').children[0].textContent, '<b>Finding</b>');
        assert.equal(element('evaluationAiResult').style.display, 'block');
    });
}
