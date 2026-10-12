<link rel="stylesheet" href="{{ vasset('css/components/moa-renewal-history.css') }}">

<div class="modal fade" id="moaRenewalHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title"><i class="fa fa-history me-1"></i> Renewal History</h5>
                    <div id="moaHistoryCompanyName" style="font-size:12px; color:#64748b; margin-top:3px;"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="moaHistorySummary" class="moa-history-summary"></div>
                <div id="moaHistoryTimeline" class="moa-history-timeline"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-close btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    window.moaRenewalHistoryConfig = {
        historyRoute: @json(route('moa.renewals.history', ['company' => '__COMPANY__'])),
        previewRoute: @json(route('moa.renewals.preview', ['renewal' => '__RENEWAL__', 'version' => '__VERSION__'])),
        downloadRoute: @json(route('moa.renewals.download', ['renewal' => '__RENEWAL__', 'version' => '__VERSION__']))
    };
</script>
<script src="{{ vasset('js/components/moa-renewal-history.js') }}" defer></script>
